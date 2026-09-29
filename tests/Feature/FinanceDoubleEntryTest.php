<?php

namespace Tests\Feature;

use App\Models\FinanceAccount;
use App\Models\JournalEntry;
use App\Models\Member;
use App\Models\User;
use Database\Seeders\FinanceChartSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceDoubleEntryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Member $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, FinanceChartSeeder::class]);
        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'secret123', 'role' => 'admin']);
        $this->member = Member::create([
            'member_no' => 'M-TEST-001', 'name' => 'Test Member', 'phone' => '0700000001',
            'join_date' => now()->toDateString(), 'status' => 'active', 'created_by' => $this->admin->id,
        ]);
    }

    protected function bal(string $code): float
    {
        return FinanceAccount::where('code', $code)->firstOrFail()->balance();
    }

    public function test_loan_disbursement_posts_balanced_journal(): void
    {
        $this->actingAs($this->admin)->post(route('loans.store'), [
            'member_id' => $this->member->id,
            'principal' => 100000,
            'interest_rate' => 10,
            'status' => 'active',
        ])->assertRedirect();

        $loan = \App\Models\Loan::first();
        $this->assertNotNull($loan);

        $entry = JournalEntry::where('source_type', \App\Models\Loan::class)->where('source_id', $loan->id)->first();
        $this->assertNotNull($entry);
        $this->assertTrue($entry->isBalanced());
        $this->assertEquals(110000, $entry->totalDebit());
        $this->assertEqualsWithDelta(110000, $this->bal('1200'), 0.01);
        $this->assertEqualsWithDelta(10000, $this->bal('4300'), 0.01);
        $this->assertEqualsWithDelta(-100000, $this->bal('1000'), 0.01);
    }

    public function test_loan_repayment_reduces_receivable_and_reverses_on_delete(): void
    {
        $this->actingAs($this->admin)->post(route('loans.store'), [
            'member_id' => $this->member->id, 'principal' => 100000, 'interest_rate' => 10, 'status' => 'active',
        ]);
        $loan = \App\Models\Loan::first();

        $this->actingAs($this->admin)->post(route('loans.repay', $loan), [
            'amount' => 30000, 'paid_at' => now()->toDateString(), 'method' => 'cash',
        ])->assertRedirect();

        $this->assertEqualsWithDelta(80000, $this->bal('1200'), 0.01);
        $this->assertEqualsWithDelta(80000, $loan->fresh()->outstanding(), 0.01);

        $repayment = \App\Models\LoanRepayment::first();
        $this->actingAs($this->admin)->delete(route('repayments.destroy', $repayment))->assertRedirect();
        $this->assertEqualsWithDelta(110000, $this->bal('1200'), 0.01);
        $this->assertFalse(JournalEntry::where('source_type', \App\Models\LoanRepayment::class)->exists());
    }

    public function test_deposit_and_withdrawal_hit_savings_liability(): void
    {
        $this->actingAs($this->admin)->post(route('deposits.store'), [
            'member_id' => $this->member->id, 'type' => 'deposit', 'amount' => 50000,
            'method' => 'mobile', 'transacted_at' => now()->toDateString(),
        ])->assertRedirect();

        $this->assertEqualsWithDelta(50000, $this->bal('2000'), 0.01);
        $this->assertEqualsWithDelta(50000, $this->bal('1110'), 0.01);

        $this->actingAs($this->admin)->post(route('deposits.store'), [
            'member_id' => $this->member->id, 'type' => 'withdrawal', 'amount' => 20000,
            'method' => 'cash', 'transacted_at' => now()->toDateString(),
        ])->assertRedirect();

        $this->assertEqualsWithDelta(30000, $this->bal('2000'), 0.01);
    }

    public function test_swf_and_investment_post_to_own_ledgers(): void
    {
        $this->actingAs($this->admin)->post(route('swf.store'), [
            'member_id' => $this->member->id, 'type' => 'contribution', 'amount' => 15000,
            'method' => 'cash', 'transacted_at' => now()->toDateString(),
        ])->assertRedirect();
        $this->assertEqualsWithDelta(15000, $this->bal('2100'), 0.01);

        $this->actingAs($this->admin)->post(route('investments.store'), [
            'member_id' => $this->member->id, 'amount' => 200000, 'expected_return_rate' => 12,
            'start_date' => now()->toDateString(), 'status' => 'active',
        ])->assertRedirect();
        $this->assertEqualsWithDelta(200000, $this->bal('2300'), 0.01);

        $inv = \App\Models\Investment::first();
        $this->actingAs($this->admin)->post(route('investment-returns.store'), [
            'investment_id' => $inv->id, 'amount' => 5000, 'paid_at' => now()->toDateString(),
        ])->assertRedirect();
        $this->assertEqualsWithDelta(5000, $this->bal('5300'), 0.01);
    }

    public function test_receivable_accrual_and_collection(): void
    {
        $this->actingAs($this->admin)->post(route('finance.receivables.store'), [
            'kind' => 'receivable', 'party_name' => 'Test Debtor', 'amount' => 40000,
        ])->assertRedirect();
        $this->assertEqualsWithDelta(40000, $this->bal('1300'), 0.01);

        $row = \App\Models\Receivable::first();
        $this->actingAs($this->admin)->post(route('finance.receivables.collect', $row), [
            'amount' => 15000, 'method' => 'bank',
        ])->assertRedirect();
        $this->assertEqualsWithDelta(25000, $this->bal('1300'), 0.01);
        $this->assertEqualsWithDelta(15000, $this->bal('1100'), 0.01);
    }

    public function test_closed_period_blocks_posting_and_rolls_back(): void
    {
        \App\Models\FinancialPeriod::create([
            'name' => 'Closed', 'starts_at' => now()->subMonth()->toDateString(),
            'ends_at' => now()->addMonth()->toDateString(), 'status' => 'closed',
        ]);

        $this->actingAs($this->admin)->post(route('deposits.store'), [
            'member_id' => $this->member->id, 'type' => 'deposit', 'amount' => 50000,
            'method' => 'cash', 'transacted_at' => now()->toDateString(),
        ])->assertSessionHasErrors();

        $this->assertEquals(0, \App\Models\Deposit::count());
        $this->assertEquals(0, JournalEntry::count());
    }

    public function test_trial_balance_always_balances(): void
    {
        $this->actingAs($this->admin)->post(route('loans.store'), [
            'member_id' => $this->member->id, 'principal' => 100000, 'interest_rate' => 10, 'status' => 'active',
        ]);
        $this->actingAs($this->admin)->post(route('deposits.store'), [
            'member_id' => $this->member->id, 'type' => 'deposit', 'amount' => 50000,
            'method' => 'cash', 'transacted_at' => now()->toDateString(),
        ]);
        $this->actingAs($this->admin)->post(route('finance.transfers.store'), [
            'from_account_id' => FinanceAccount::where('code', '1000')->first()->id,
            'to_account_id' => FinanceAccount::where('code', '1100')->first()->id,
            'amount' => 10000, 'transferred_at' => now()->toDateString(),
        ]);

        $this->assertEqualsWithDelta(
            (float) \App\Models\JournalLine::sum('debit'),
            (float) \App\Models\JournalLine::sum('credit'),
            0.01
        );

        $this->actingAs($this->admin)->get(route('finance.trial-balance'))->assertOk();
    }
}
