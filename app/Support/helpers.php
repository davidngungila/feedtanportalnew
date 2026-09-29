<?php

use App\Models\Member;

if (! function_exists('money')) {
    function money(float|int|string|null $amount): string
    {
        return 'TZS '.number_format((float) $amount, (float) $amount === round((float) $amount) ? 0 : 2, '.', ',');
    }
}

if (! function_exists('status_badge')) {
    function status_badge(string $status): string
    {
        return match (strtolower($status)) {
            'completed', 'active', 'paid', 'matured', 'approved', 'disbursed', 'success' => 'tag-green',
            'pending', 'open', 'due', 'overdue' => 'tag-gold',
            'failed', 'defaulted', 'suspended', 'overdue_soon' => 'tag-red',
            'inactive', 'reversed', 'withdrawn', 'rejected', 'closed', 'claim', 'deduction' => 'tag-grey',
            'contribution' => 'tag-green',
            default => 'tag-terracotta',
        };
    }
}

if (! function_exists('member_balance')) {
    function member_balance(Member $member): array
    {
        $deposits = (float) $member->deposits()->where('type', 'deposit')->sum('amount');
        $withdrawals = (float) $member->deposits()->where('type', 'withdrawal')->sum('amount');
        $loanDisbursed = (float) $member->loans()->whereIn('status', ['active', 'paid', 'overdue'])->sum('total_payable');
        $loanRepaid = (float) $member->loans()->withSum('repayments as repaid', 'amount')->get()->sum('repaid');
        $invested = (float) $member->investments()->whereIn('status', ['active', 'matured'])->sum('amount');
        $swfIn = (float) $member->swfEntries()->where('type', 'contribution')->sum('amount');
        $swfOut = (float) $member->swfEntries()->whereIn('type', ['payout', 'claim', 'deduction'])->sum('amount');

        return [
            'savings' => $deposits - $withdrawals,
            'loan_outstanding' => max(0, $loanDisbursed - $loanRepaid),
            'invested' => $invested,
            'swf' => $swfIn - $swfOut,
        ];
    }
}

if (! function_exists('eid')) {
    /**
     * Encrypt an ID for use in URLs so raw incrementing IDs are never exposed.
     */
    function eid(int|string $id): string
    {
        return rtrim(strtr(encrypt((string) $id), '+/', '-_'), '=');
    }
}

if (! function_exists('did')) {
    /**
     * Decrypt an ID from a URL. Aborts with 404 when tampered.
     * Plain numeric IDs are accepted for backwards compatibility.
     */
    function did(?string $hash): int
    {
        if ($hash !== null && ctype_digit($hash)) {
            return (int) $hash;
        }

        $b64 = strtr((string) $hash, '-_', '+/');
        $pad = strlen($b64) % 4;
        if ($pad) {
            $b64 .= str_repeat('=', 4 - $pad);
        }

        try {
            return (int) decrypt($b64);
        } catch (\Throwable) {
            abort(404);
        }
    }
}

if (! function_exists('is_role')) {
    function is_role(string ...$roles): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole(...$roles);
        }

        return in_array($user->role ?? 'cashier', $roles, true);
    }
}

if (! function_exists('is_admin')) {
    function is_admin(): bool
    {
        return is_role('administrator', 'admin');
    }
}

if (! function_exists('log_activity')) {
    function log_activity(string $action, ?string $subject = null): void
    {
        try {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'subject' => $subject,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 500),
            ]);
        } catch (\Throwable) {
        }
    }
}

if (! function_exists('role_names')) {
    function role_names(): array
    {
        return [
            'administrator' => 'Administrator',
            'chairperson' => 'Chairperson',
            'secretary' => 'Secretary',
            'accountant' => 'Accountant',
            'swf_officer' => 'SWF Officer',
            'deposit_officer' => 'Deposit Officer',
            'investment_officer' => 'Investment Officer',
            'loan_officer' => 'Loan Officer',
            'member' => 'Member',
        ];
    }
}
