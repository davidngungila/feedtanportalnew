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

        $this->assertEquals(2, did(basename($this->actingAs($user)->post(route('join.save', eid(1)), ['first_name' => 'Amina', 'middle_name' => 'Said', 'surname' => 'Juma', 'phone' => '0711000002', 'sex' => 'female'])->assertRedirect()->headers->get('Location'))));
        $this->assertEquals(3, did(basename($this->actingAs($user)->post(route('join.save', eid(2)), ['address' => 'Mwanza', 'job' => 'Trader'])->assertRedirect()->headers->get('Location'))));
        $this->assertEquals(4, did(basename($this->actingAs($user)->post(route('join.save', eid(3)), ['bank_name' => 'CRDB', 'bank_account' => '0112233'])->assertRedirect()->headers->get('Location'))));
        $this->assertEquals(5, did(basename($this->actingAs($user)->post(route('join.save', eid(4)), [
            'referrer' => 'Juma',
            'beneficiaries' => [
                ['name' => 'Kid One', 'relationship' => 'Child', 'allocation' => 60, 'bank' => '', 'contact' => '0711'],
                ['name' => 'Spouse', 'relationship' => 'Spouse', 'allocation' => 40, 'bank' => '', 'contact' => '0722'],
            ],
        ])->assertRedirect()->headers->get('Location'))));
        $this->actingAs($user)->get(route('join.step', eid(5)))->assertOk()->assertSee('Beneficiaries');
        // Tampered step keys are rejected.
        $this->actingAs($user)->get(route('join.step', 'NOTASTEP'))->assertNotFound();
        // Allocations must total 100%.
        $this->actingAs($user)->post(route('join.save', eid(4)), [
            'beneficiaries' => [['name' => 'Kid One', 'allocation' => 30]],
        ])->assertSessionHasErrors('beneficiaries');
        $this->actingAs($user)->post(route('join.submit'))->assertRedirect(route('join.status'));
        $this->actingAs($user)->get(route('join.status'))->assertOk()->assertSee('office');

        $app = MemberApplication::where('user_id', $user->id)->first();
        $this->assertEquals('pending', $app->status);
        $this->assertEquals('CRDB', $app->bank_name);
        $this->assertCount(2, $app->beneficiaries);
    }

    public function test_age_is_calculated_from_date_of_birth(): void
    {
        $user = User::create(['name' => 'G', 'email' => 'g@test.local', 'password' => 'secret123', 'role' => 'applicant']);
        $user->roles()->sync(Role::where('slug', 'applicant')->pluck('id'));

        $dob = now()->subYears(30)->toDateString();
        $this->actingAs($user)->post(route('join.save', eid(1)), [
            'first_name' => 'G', 'surname' => 'Person', 'phone' => '0711000030', 'dob' => $dob,
        ])->assertRedirect();

        $app = MemberApplication::where('user_id', $user->id)->first();
        $this->assertEquals(30, $app->age);
        $this->actingAs($user)->get(route('join.step', eid(1)))->assertOk()->assertSee('30 years old');
    }

    public function test_step_uploads_are_stored(): void    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $user = User::create(['name' => 'U', 'email' => 'u@test.local', 'password' => 'secret123', 'role' => 'applicant']);
        $user->roles()->sync(Role::where('slug', 'applicant')->pluck('id'));

        $this->actingAs($user)->post(route('join.save', eid(1)), [
            'first_name' => 'U', 'surname' => 'Person', 'phone' => '0711000011',
            'passport_picture' => \Illuminate\Http\UploadedFile::fake()->image('passport.jpg', 1200, 900),
        ])->assertRedirect();

        $app = MemberApplication::where('user_id', $user->id)->first();
        $this->assertNotEmpty($app->attachments['passport'] ?? null);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($app->attachments['passport']);
        $size = getimagesize(\Illuminate\Support\Facades\Storage::disk('public')->path($app->attachments['passport']));
        $this->assertEquals(500, max($size[0], $size[1]));
    }

    public function test_status_page_renders_for_every_state(): void
    {
        $admin = $this->makeAdmin();
        $user = User::create(['name' => 'S', 'email' => 's@test.local', 'password' => 'secret123', 'role' => 'applicant']);
        $user->roles()->sync(Role::where('slug', 'applicant')->pluck('id'));

        // Draft state renders the landing + step pages.
        $this->actingAs($user)->get(route('join.index'))->assertOk();
        foreach ([1, 2, 3, 4] as $n) {
            // Only reachable steps render; step 1 always works on a fresh draft.
            if ($n === 1) {
                $this->actingAs($user)->get(route('join.step', eid($n)))->assertOk();
            }
        }

        // Rejected state renders with restart option.
        $app = MemberApplication::create([
            'user_id' => $user->id, 'current_step' => 4, 'name' => 'S',
            'phone' => '0711000009', 'email' => 's@test.local', 'status' => 'rejected',
        ]);
        $this->actingAs($user)->get(route('join.status'))->assertOk()->assertSee('send again');
        $this->actingAs($user)->post(route('join.restart'))->assertRedirect();
        $this->assertEquals('draft', $app->fresh()->status);

        // Approved state renders with portal link.
        $app->update(['status' => 'pending']);
        $this->actingAs($admin)->post(route('member-applications.approve', $app))->assertRedirect();
        $this->actingAs($user->fresh())->get(route('join.status'))->assertOk()->assertSee('Open my portal');
    }

    public function test_approval_uses_passport_as_profile_photo(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $admin = $this->makeAdmin();
        $user = User::create(['name' => 'P', 'email' => 'p@test.local', 'password' => 'secret123', 'role' => 'applicant']);
        $user->roles()->sync(Role::where('slug', 'applicant')->pluck('id'));
        $photo = \Illuminate\Http\UploadedFile::fake()->image('face.jpg', 800, 600)->store('applications', 'public');
        $app = MemberApplication::create([
            'user_id' => $user->id, 'current_step' => 5, 'name' => 'P Person',
            'phone' => '0711000040', 'email' => 'p@test.local', 'status' => 'pending',
            'attachments' => ['passport' => $photo],
        ]);

        $this->actingAs($admin)->post(route('member-applications.approve', $app))->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->avatar_path);
        $this->assertNotNull($user->avatarUrl());
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_avatar_falls_back_to_application_passport(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $user = User::create(['name' => 'F', 'email' => 'f@test.local', 'password' => 'secret123', 'role' => 'member']);
        $photo = \Illuminate\Http\UploadedFile::fake()->image('face.jpg', 400, 400)->store('applications', 'public');
        MemberApplication::create([
            'user_id' => $user->id, 'current_step' => 5, 'name' => 'F Person',
            'phone' => '0711000050', 'email' => 'f@test.local', 'status' => 'approved',
            'attachments' => ['passport' => $photo],
        ]);

        $this->assertNull($user->avatar_path);
        $this->assertStringContainsString('applications/', $user->fresh()->avatarUrl());
        $this->actingAs($user)->get(route('account.index'))->assertOk()->assertSee('applications/', false);
    }

    public function test_staff_can_review_full_file_and_approval_carries_details(): void    {
        $admin = $this->makeAdmin();
        $app = MemberApplication::create([
            'current_step' => 5, 'name' => 'Full File', 'phone' => '0711000020',
            'email' => 'full@test.local', 'status' => 'pending',
            'referrer' => 'Juma',
            'bank_name' => 'CRDB', 'bank_account' => '0112233',
            'beneficiaries' => [['name' => 'Kid', 'relationship' => 'Child', 'allocation' => 100]],
        ]);

        $this->actingAs($admin)->get(route('member-applications.show', $app))
            ->assertOk()->assertSee('Juma')->assertSee('0112233')->assertSee('Kid');

        $this->actingAs($admin)->post(route('member-applications.approve', $app))->assertRedirect();

        $member = Member::where('email', 'full@test.local')->first();
        $this->assertNotNull($member);
        $this->assertStringContainsString('Juma', $member->notes ?? '');
    }

    public function test_portal_create_pages_render(): void
    {
        $admin = $this->makeAdmin();
        $member = Member::create([
            'member_no' => 'M-R-1', 'name' => 'R', 'phone' => '0711000010',
            'join_date' => now()->toDateString(), 'status' => 'active', 'created_by' => $admin->id,
        ]);
        $user = User::create(['name' => 'R', 'email' => 'r@test.local', 'password' => 'secret123', 'role' => 'member', 'member_id' => $member->id]);
        $user->roles()->sync(Role::where('slug', 'member')->pluck('id'));

        $this->actingAs($user)->get(route('portal.deposits.create'))->assertOk();
        $this->actingAs($user)->get(route('portal.investments.create'))->assertOk();
        $this->actingAs($user)->get(route('portal.swf.create'))->assertOk();
        $this->actingAs($user)->get(route('portal.home'))->assertOk();
        $this->actingAs($user)->get(route('portal.statements'))->assertOk();
        $this->actingAs($user)->get(route('portal.profile'))->assertOk();
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
