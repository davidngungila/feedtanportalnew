<?php

namespace App\Http\Controllers;

use App\Models\MemberApplication;
use App\Models\MemberGroup;
use App\Models\MemberType;
use Illuminate\Http\Request;

class JoinController extends Controller
{
    public const STEPS = [
        1 => 'Personal details',
        2 => 'Contact',
        3 => 'Membership',
        4 => 'Review & submit',
    ];

    protected function ownApplication(): ?MemberApplication
    {
        $user = auth()->user();

        return MemberApplication::where('user_id', $user->id)
            ->orWhere(fn ($q) => $q->whereNull('user_id')->where('email', $user->email))
            ->latest()->first();
    }

    protected function draft(): MemberApplication
    {
        $user = auth()->user();
        $app = $this->ownApplication();

        if (! $app || in_array($app->status, ['approved'], true)) {
            $app = MemberApplication::create([
                'user_id' => $user->id,
                'current_step' => 1,
                'name' => $user->name,
                'phone' => $user->phone ?? '',
                'email' => $user->email,
                'status' => 'draft',
            ]);
        }
        if (! $app->user_id) {
            $app->update(['user_id' => $user->id]);
        }

        return $app->fresh();
    }

    public function index()
    {
        $app = $this->ownApplication();

        if ($app && in_array($app->status, ['pending', 'approved', 'rejected'], true)) {
            return redirect()->route('join.status');
        }

        $app = $this->draft();

        return view('join.start', ['app' => $app, 'steps' => self::STEPS]);
    }

    /** Step keys travel encrypted (plain numbers still accepted for old links). */
    protected function resolveStep(mixed $n): int
    {
        try {
            $n = did((string) $n);
        } catch (\Throwable) {
            abort(404);
        }
        abort_unless(isset(self::STEPS[$n]), 404);

        return $n;
    }

    public function step(string $n)
    {
        $n = $this->resolveStep($n);
        $app = $this->draft();

        if ($app->status !== 'draft') {
            return redirect()->route('join.status');
        }
        if ($n > $app->current_step) {
            return redirect()->route('join.step', eid($app->current_step));
        }

        $types = MemberType::where('status', 'active')->orderBy('name')->get();
        $groups = MemberGroup::where('status', 'active')->orderBy('name')->get();

        return view('join.step', ['app' => $app, 'step' => $n, 'steps' => self::STEPS, 'types' => $types, 'groups' => $groups]);
    }

    public function saveStep(Request $request, string $n)
    {
        $n = $this->resolveStep($n);
        $app = $this->draft();
        abort_unless($app->status === 'draft', 403);

        $data = match ($n) {
            1 => $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:30'],
                'national_id' => ['nullable', 'string', 'max:60'],
            ]),
            2 => $request->validate([
                'email' => ['nullable', 'email'],
                'address' => ['nullable', 'string', 'max:255'],
            ]),
            default => $request->validate([
                'member_type_id' => ['nullable', 'exists:member_types,id'],
                'member_group_id' => ['nullable', 'exists:member_groups,id'],
                'notes' => ['nullable', 'string'],
            ]),
        };

        $app->update([...$data, 'current_step' => max($app->current_step, min($n + 1, 4))]);

        return redirect()->route('join.step', eid(min($n + 1, 4)))->with('status', 'Step '.$n.' saved.');
    }

    public function submit(Request $request)
    {
        $app = $this->draft();
        abort_unless($app->status === 'draft', 403);

        $valid = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $app->update([...$valid, 'status' => 'pending', 'current_step' => 4]);

        return redirect()->route('join.status')->with('status', 'Application sent — we will notify you once reviewed.');
    }

    public function status()
    {
        $app = $this->ownApplication();
        abort_unless($app, 404);
        $member = linked_member(auth()->user());

        return view('join.status', compact('app', 'member'));
    }

    public function restart()
    {
        $app = $this->ownApplication();
        abort_unless($app && $app->status === 'rejected', 403);

        $app->update(['status' => 'draft', 'current_step' => 1]);

        return redirect()->route('join.step', eid(1))->with('status', 'Let’s update your details and send again.');
    }
}
