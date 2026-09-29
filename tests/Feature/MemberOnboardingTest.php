<?php

namespace Tests\Feature;

use App\Models\FinanceAccount;
use App\Models\JournalEntry;
use App\Models\Member;
use App\Models\MemberApplication;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\FinanceChartSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RoleSeeder::class, FinanceChartSeeder::class]);
    }

    protected function makeAdmin(): User
    {
        return User::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'secret123', 'role' => 'admin']);
    }

    public function test_guest_can_register_and_starts_stepped_join(): void
    {
        $this->post(route('register'), [
            'name' => 'New Person', 'phone' => '0711000001', 'email' => 'new@test.local',
            'password' => 'secret123', 'password_confirmation' => 'secret123',
        ])->assertRedirect(route('join.index'));

        $user = User::where('email', 'new@test.local')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('applicant'));
        $this->assertTrue(is_applicant_incomplete($user->fresh()));

        $this->actingAs($user)->get(route('join.index'))->assertOk()->assertSee('How it works');
        $this->assertNotNull(MemberApplication::where('user_id', $user->id)->first());
    }

    public function test_applicant_walks_steps_and_submits(): void
    {
        $user = User::create(['name' => 'A', 'email' => 'a@test.local', 'password' => 'secret123', 'role' => 'applicant']);
        $user->roles()->sync(Role::where('slug', 'applicant')->pluck('id'));

        $this->assertEquals(2, did(basename($this->actingAs($user)->post(route('join.save', eid(1)), ['name' => 'A Person', 'phone' => '0711000002'])->assertRedirect()->headers->get('Location'))));
        $this->assertEquals(3, did(basename($this->actingAs($user)->post(route('join.save', eid(2)), ['address' => 'Mwanza'])->assertRedirect()->headers->get('Location'))));
        $this->assertEquals(4, did(basename($this->actingAs($user)->post(route('join.save', eid(3)), [])->assertRedirect()->headers->get('Location'))));
        $this->actingAs($user)->get(route('join.step', eid(4)))->assertOk();
        // Tampered step keys are rejected.
        $this->actingAs($user)->get(route('join.step', 'NOTASTEP'))->assertNotFound();
        $this->actingAs($user)->post(route('join.submit'), ['name' => 'A Person', 'phone' => '0711000002'])->assertRedirect(route('join.status'));

        $app = MemberApplication::where('user_id', $user->id)->first();
        $this->assertEquals('pending', $app->status);
    }

    public function test_applicant_is_kept_out_of_staff_areas(): void
    {
        $user = User::create(['name' => 'A', 'email' => 'a@test.local', 'password' => 'secret123', 'role' => 'applicant']);
        $user->roles()->sync(Role::where('slug', 'applicant')->pluck('id'));

        $this->actingAs($user)->get(route('dashboard'))->assertRedirect(route('join.index'));
        $this->actingAs($user)->get(route('members.index'))->assertRedirect(route('join.index'));
        $this->actingAs($user)->get(route('portal.home'))->assertRedirect(route('join.index'));
    }

    public function test_approval_creates_member_and_unlocks_services(): void
    {
        $admin = $this->makeAdmin();
        $user = User::create(['name' => 'A Person', 'phone' => '0711000003', 'email' => 'a@test.local', 'password' => 'secret123', 'role' => 'applicant']);
        $user->roles()->sync(Role::where('slug', 'applicant')->pluck('id'));
        $app = MemberApplication::create([
            'user_id' => $user->id, 'current_step' => 4, 'name' => 'A Person',
            'phone' => '0711000003', 'email' => 'a@test.local', 'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('member-applications.approve', $app))->assertRedirect();

        $user->refresh();
        $member = Member::where('email', 'a@test.local')->first();
        $this->assertNotNull($member);
        $this->assertEquals($member->id, $user->member_id);
        $this->assertTrue($user->hasRole('member'));
        $this->assertFalse($user->hasRole('applicant'));
        $this->assertFalse(is_applicant_incomplete($user));

        $this->actingAs($user)->get('/')->assertRedirect(route('portal.home'));
    }

    public function test_member_self_service_posts_to_ledger(): void
    {
        $admin = $this->makeAdmin();
        $member = Member::create([
            'member_no' => 'M-T-1', 'name' => 'M', 'phone' => '0711000004',
            'join_date' => now()->toDateString(), 'status' => 'active', 'created_by' => $admin->id,
        ]);
        $user = User::create(['name' => 'M', 'email' => 'm@test.local', 'password' => 'secret123', 'role' => 'member', 'member_id' => $member->id]);
        $user->roles()->sync(Role::where('slug', 'member')->pluck('id'));

        // Deposit.
        $this->actingAs($user)->post(route('portal.deposits.store'), [
            'type' => 'deposit', 'amount' => 40000, 'method' => 'mobile', 'transacted_at' => now()->toDateString(),
        ])->assertRedirect(route('portal.deposits'));
        $deposit = \App\Models\Deposit::first();
        $this->assertTrue(JournalEntry::where('source_type', \App\Models\Deposit::class)->where('source_id', $deposit->id)->exists());

        // Withdrawal beyond balance is refused.
        $this->actingAs($user)->post(route('portal.deposits.store'), [
            'type' => 'withdrawal', 'amount' => 999999, 'method' => 'cash', 'transacted_at' => now()->toDateString(),
        ])->assertSessionHasErrors('amount');

        // Investment.
        $product = \App\Models\InvestmentProduct::create(['name' => 'FIA', 'return_rate' => 10, 'min_amount' => 1000, 'duration_months' => 12, 'status' => 'active']);
        $this->actingAs($user)->post(route('portal.investments.store'), [
            'investment_product_id' => $product->id, 'amount' => 50000, 'start_date' => now()->toDateString(),
        ])->assertRedirect(route('portal.investments'));
        $this->assertEqualsWithDelta(50000, FinanceAccount::where('code', '2300')->first()->balance(), 0.01);

        // SWF contribution.
        $this->actingAs($user)->post(route('portal.swf.store'), [
            'type' => 'contribution', 'amount' => 8000, 'method' => 'cash', 'transacted_at' => now()->toDateString(),
        ])->assertRedirect(route('portal.swf'));

        // Loan + self repayment.
        $loan = \App\Models\Loan::create([
            'member_id' => $member->id, 'loan_no' => 'LN-T-1', 'principal' => 60000,
            'interest_rate' => 10, 'interest_amount' => 6000, 'total_payable' => 66000,
            'disbursed_at' => now()->toDateString(), 'status' => 'active', 'created_by' => $admin->id,
        ]);
        $this->actingAs($user)->post(route('portal.loans.repay', $loan), [
            'amount' => 10000, 'paid_at' => now()->toDateString(), 'method' => 'mobile',
        ])->assertRedirect();
        $this->assertEqualsWithDelta(56000, $loan->fresh()->outstanding(), 0.01);

        // Ledger stays balanced across member operations.
        $this->assertEqualsWithDelta(
            (float) \App\Models\JournalLine::sum('debit'),
            (float) \App\Models\JournalLine::sum('credit'),
            0.01
        );
    }
}
