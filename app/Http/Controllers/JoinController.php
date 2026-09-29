<?php

namespace App\Http\Controllers;

use App\Models\MemberApplication;
use App\Models\MemberGroup;
use App\Models\MemberType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JoinController extends Controller
{
    public const STEPS = [
        1 => 'Personal details',
        2 => 'Contact & work',
        3 => 'Bank & payments',
        4 => 'Membership',
        5 => 'Beneficiaries',
        6 => 'Savings goal',
        7 => 'Review & submit',
    ];

    public const MAX_STEP = 7;

    protected function ownApplication(): ?MemberApplication
    {
        $user = auth()->user();

        return MemberApplication::where('user_id', $user->id)
            ->orWhere(fn ($q) => $q->whereNull('user_id')->where('email', $user->email))
            ->latest('id')->first();
    }

    protected function draft(): MemberApplication
    {
        $user = auth()->user();
        $app = $this->ownApplication();

        if (! $app || in_array($app->status, ['approved'], true)) {
            [$first, $middle, $last] = self::splitName($user->name);
            $app = MemberApplication::create([
                'user_id' => $user->id,
                'current_step' => 1,
                'name' => $user->name,
                'first_name' => $first,
                'middle_name' => $middle,
                'surname' => $last,
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

    /** Split a full name into [first, middle, surname]. */
    public static function splitName(?string $name): array
    {
        $parts = preg_split('/\s+/', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);
        if (! $parts) {
            return [null, null, null];
        }
        if (count($parts) === 1) {
            return [$parts[0], null, null];
        }

        return [$parts[0], implode(' ', array_slice($parts, 1, -1)) ?: null, end($parts)];
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

    protected function storeUpload(?object $file, string $folder): ?string
    {
        if (! $file) {
            return null;
        }

        return $file->store($folder, 'public');
    }

    /**
     * Store a photo normalized to JPEG with the longest side at 500px.
     * Smaller images are kept as-is (never upscaled).
     */
    protected function storePhoto(?object $file, string $folder, int $maxSide = 500): ?string
    {
        if (! $file) {
            return null;
        }
        $path = $file->store($folder, 'public');
        $absolute = Storage::disk('public')->path($path);

        try {
            $info = @getimagesize($absolute);
            if (! $info || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                return $path;
            }
            [$width, $height] = [$info[0], $info[1]];
            $longest = max($width, $height);
            if ($longest <= $maxSide) {
                return $path;
            }
            $src = $info[2] === IMAGETYPE_PNG ? @imagecreatefrompng($absolute) : @imagecreatefromjpeg($absolute);
            if (! $src) {
                return $path;
            }
            $scale = $maxSide / $longest;
            $dst = imagecreatetruecolor((int) round($width * $scale), (int) round($height * $scale));
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
            imagecopyresampled($dst, $src, 0, 0, 0, 0, imagesx($dst), imagesy($dst), $width, $height);
            $target = preg_replace('/\.[^.]+$/', '.jpg', $path);
            imagejpeg($dst, Storage::disk('public')->path($target), 85);
            imagedestroy($src);
            imagedestroy($dst);
            if ($target !== $path) {
                Storage::disk('public')->delete($path);
            }

            return $target;
        } catch (\Throwable) {
            return $path;
        }
    }

    public function saveStep(Request $request, string $n)
    {
        $n = $this->resolveStep($n);
        $app = $this->draft();
        abort_unless($app->status === 'draft', 403);

        $data = match ($n) {
            1 => $request->validate([
                'first_name' => ['required', 'string', 'max:120'],
                'middle_name' => ['nullable', 'string', 'max:120'],
                'surname' => ['required', 'string', 'max:120'],
                'sex' => ['nullable', 'in:male,female'],
                'dob' => ['nullable', 'date', 'before:today'],
                'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],
                'phone' => ['required', 'string', 'max:30'],
                'national_id' => ['nullable', 'string', 'max:60'],
                'passport_picture' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            ]),
            2 => $request->validate([
                'email' => ['nullable', 'email'],
                'address' => ['nullable', 'string', 'max:255'],
                'job' => ['nullable', 'string', 'max:255'],
                'employer' => ['nullable', 'string', 'max:255'],
            ]),
            3 => $request->validate([
                'bank_name' => ['nullable', 'string', 'max:255'],
                'bank_account' => ['nullable', 'string', 'max:100'],
            ]),
            4 => $request->validate([
                'member_type_id' => ['nullable', 'exists:member_types,id'],
                'referrer' => ['nullable', 'string', 'max:255'],
            ]),
            5 => $request->validate([
                'beneficiaries' => ['nullable', 'array', 'max:6'],
                'beneficiaries.*.name' => ['required_with:beneficiaries', 'string', 'max:255'],
                'beneficiaries.*.relationship' => ['nullable', 'string', 'max:100'],
                'beneficiaries.*.allocation' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'beneficiaries.*.bank' => ['nullable', 'string', 'max:255'],
                'beneficiaries.*.contact' => ['nullable', 'string', 'max:255'],
            ]),
            default => $request->validate([
                'savings_goal' => ['nullable', 'string', 'max:255'],
                'goal_amount' => ['nullable', 'numeric', 'min:0'],
                'goal_months' => ['nullable', 'integer', 'min:1', 'max:600'],
                'goal_start' => ['nullable', 'date'],
            ]),
        };

        $update = collect($data)->except([
            'passport_picture', 'application_letter',
            'beneficiaries',
        ])->all();

        // Beneficiaries must allocate 100% when given.
        if ($n === 5 && ! empty($data['beneficiaries'])) {
            $rows = array_values(array_filter($data['beneficiaries'], fn ($b) => trim($b['name'] ?? '') !== ''));
            $total = round(collect($rows)->sum(fn ($b) => (float) ($b['allocation'] ?? 0)), 2);
            if ($total > 0 && abs($total - 100) > 0.01) {
                return back()->withErrors(['beneficiaries' => 'Beneficiary allocations must add up to 100% (now '.$total.'%).'])->withInput();
            }
            $update['beneficiaries'] = $rows;
        }

        // Attachments merge with previously uploaded files.
        $attachments = $app->attachments ?? [];
        foreach (['passport_picture' => 'passport'] as $field => $key) {
            if ($request->hasFile($field)) {
                if (! empty($attachments[$key]) && is_string($attachments[$key])) {
                    Storage::disk('public')->delete($attachments[$key]);
                }
                $attachments[$key] = $this->storePhoto($request->file($field), 'applications');
            }
        }
        if ($attachments) {
            $update['attachments'] = $attachments;
        }

        // Keep the full name in sync with its parts.
        if ($n === 1) {
            $update['name'] = trim(implode(' ', array_filter([$update['first_name'] ?? null, $update['middle_name'] ?? null, $update['surname'] ?? null])));
        }

        $update['current_step'] = max($app->current_step, min($n + 1, self::MAX_STEP));
        $app->update($update);

        return redirect()->route('join.step', eid(min($n + 1, self::MAX_STEP)))->with('status', 'Step '.$n.' saved.');
    }

    public function submit(Request $request)
    {
        $app = $this->draft();
        abort_unless($app->status === 'draft', 403);

        if (! $app->first_name || ! $app->surname || ! $app->phone) {
            return redirect()->route('join.step', eid(1))->withErrors(['name' => 'Please complete your name and phone in step 1 first.']);
        }

        $app->update([
            'name' => trim(implode(' ', array_filter([$app->first_name, $app->middle_name, $app->surname]))),
            'status' => 'pending',
            'current_step' => self::MAX_STEP,
        ]);

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
