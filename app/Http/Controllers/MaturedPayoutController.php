<?php

namespace App\Http\Controllers;

use App\Imports\PayoutSheetImport;
use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\Investment;
use App\Models\InvestmentPayout;
use App\Models\InvestmentReturn;
use App\Models\LoanRepayment;
use App\Models\Member;
use App\Models\SwfEntry;
use App\Services\FinancePosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MaturedPayoutController extends Controller
{
    public const HEADERS = ['Name', 'Amount Earned', 'Loan installment', 'SWF deduction', 'Fines deduction', 'T-shirt deduction', 'Capital FeedTan CMG', 'Net cash'];

    public function importForm()
    {
        $payouts = InvestmentPayout::with('member')->where('kind', 'matured')->latest()->limit(50)->get();
        $pendingSms = InvestmentPayout::where('kind', 'matured')->where('status', 'pending')->whereNull('sms_sent_at')->count();

        return view('investments.matured-import', compact('payouts', 'pendingSms'));
    }

    public function template()
    {
        $headers = self::HEADERS;
        array_splice($headers, 1, 0, ['Phone']);
        $rows = [$headers, ['Amina Juma', '0712345678', '500000', '100000', '10000', '5000', '15000', '20000', '350000']];

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        }, 'matured-payout-template.csv', ['Content-Type' => 'text/csv']);
    }

    protected static function normKey(string $header): string
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower($header));
    }

    protected static function normPhone(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone);
    }

    protected static function num(mixed $value): float
    {
        $s = trim((string) $value);
        if ($s === '') {
            return 0;
        }
        // Accepts "350000", "350,000", "350,000.00", "TZS 350,000", "350,000/=".
        if (preg_match('/-?[\d,]*\.?\d+/', str_replace(' ', '', $s), $m)) {
            return (float) str_replace(',', '', $m[0]);
        }

        return 0;
    }

    protected static function readRows(string $path, string $ext): array
    {
        if (in_array($ext, ['csv', 'txt'], true)) {
            $rows = [];
            $handle = fopen($path, 'r');
            while (($line = fgetcsv($handle)) !== false) {
                $rows[] = $line;
            }
            fclose($handle);

            return $rows;
        }

        $import = new PayoutSheetImport;
        // getRealPath() (e.g. /tmp/phpXXXX) has no extension, so pass an
        // explicit reader type — otherwise FileTypeDetector throws
        // NoTypeDetectedException on xlsx/xls uploads.
        $readerType = match ($ext) {
            'xls' => \Maatwebsite\Excel\Excel::XLS,
            default => \Maatwebsite\Excel\Excel::XLSX,
        };
        Excel::import($import, $path, null, $readerType);

        return $import->rows;
    }

    public function store(Request $request)
    {
        $request->validate([
            'sheet' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:5120'],
        ]);

        $file = $request->file('sheet');
        $ext = strtolower($file->getClientOriginalExtension());
        $raw = self::readRows($file->getRealPath(), $ext);

        if (! $raw || count($raw) < 2) {
            return back()->withErrors(['sheet' => 'The sheet is empty.']);
        }

        $headers = array_map(fn ($h) => self::normKey((string) $h), $raw[0]);
        $need = ['name', 'amountearned', 'netcash'];
        foreach ($need as $col) {
            if (! in_array($col, $headers, true)) {
                return back()->withErrors(['sheet' => 'Columns must be: '.implode(', ', self::HEADERS).'. Phone is optional (needed for SMS).']);
            }
        }

        $imported = 0;
        $createdMembers = 0;
        $totalNet = 0.0;
        $failed = [];

        foreach (array_slice($raw, 1) as $i => $line) {
            $lineNo = $i + 2;
            if (! is_array($line) || count(array_filter($line, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }
            $row = [];
            foreach ($headers as $idx => $key) {
                $row[$key] = trim((string) ($line[$idx] ?? ''));
            }

            try {
                DB::transaction(function () use ($row, &$imported, &$createdMembers, &$totalNet) {
                    $this->importRow($row, $imported, $createdMembers, $totalNet);
                });
                $imported++;
            } catch (\Throwable $e) {
                $failed[] = 'Row '.$lineNo.': '.($e instanceof \Illuminate\Validation\ValidationException
                    ? implode(' ', $e->errors()['row'] ?? ['Invalid data.'])
                    : 'Could not import this row.');
            }
        }

        return redirect()->route('investments.matured.import')
            ->with('status', "Imported {$imported} payout(s), total net ".money($totalNet).". New members: {$createdMembers}. Failed: ".count($failed).'.')
            ->with('import_failed', $failed);
    }

    protected function importRow(array $row, int &$imported, int &$createdMembers, float &$totalNet): void
    {
        $fail = function (string $msg) {
            throw \Illuminate\Validation\ValidationException::withMessages(['row' => $msg]);
        };

        $phone = self::normPhone($row['phone'] ?? '');
        $name = trim((string) ($row['name'] ?? ''));
        if ($name === '') {
            $fail('Name is required.');
        }
        $amount = self::num($row['amountearned'] ?? '');
        $netRaw = trim((string) ($row['netcash'] ?? ''));
        if ($amount < 0) {
            $fail('Amount Earned must be zero or more.');
        }

        $get = fn (string $k) => max(0, self::num($row[$k] ?? ''));
        $loanInst = $get('loaninstallment');
        $swf = $get('swfdeduction');
        $fines = $get('finesdeduction');
        $tshirt = $get('tshirtdeduction');
        $capital = $get('capitalfeedtancmg');

        $expected = $amount - ($loanInst + $swf + $fines + $tshirt + $capital);
        if ($netRaw === '') {
            // Blank Net cash cell: compute it instead of importing zero.
            $net = max(0, $expected);
            $notes = 'Net cash was blank — computed as Amount Earned minus deductions.';
        } else {
            $net = self::num($netRaw);
            $notes = abs($expected - $net) > 0.5
                ? 'Sheet net cash differs from Amount minus deductions by '.money($net - $expected).'.'
                : null;
        }

        $member = null;
        if ($phone !== '') {
            $member = Member::where('phone', $phone)->first();
        }
        if (! $member) {
            $member = Member::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
        }
        if (! $member) {
            $member = Member::create([
                'member_no' => 'M-'.now()->format('Ymd').'-'.str_pad((string) (Member::max('id') + 1), 4, '0', STR_PAD_LEFT),
                'name' => $name,
                'phone' => $phone !== '' ? $phone : 'NO-PHONE-'.strtoupper(substr(md5($name.microtime()), 0, 8)),
                'join_date' => now()->toDateString(),
                'status' => 'active',
                'notes' => 'Created from matured payout import.'.($phone === '' ? ' No phone on sheet — SMS unavailable.' : ''),
                'created_by' => auth()->id(),
            ]);
            $createdMembers++;
        }

        $investment = Investment::where('member_id', $member->id)
            ->where('status', 'active')->latest()->first();
        if (! $investment) {
            $investment = Investment::create([
                'member_id' => $member->id,
                'investment_no' => 'INV-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'amount' => $amount,
                'expected_return_rate' => 0,
                'expected_return' => 0,
                'start_date' => now()->toDateString(),
                'maturity_date' => now()->toDateString(),
                'status' => 'matured',
                'plan' => 'Payout import',
                'notes' => 'Created from matured payout import.',
                'created_by' => auth()->id(),
            ]);
        }

        InvestmentPayout::create([
            'investment_id' => $investment->id,
            'kind' => 'matured',
            'member_id' => $member->id,
            'phone' => $phone,
            'amount' => $amount,
            'loan_installment' => $loanInst,
            'swf_deduction' => $swf,
            'fines_deduction' => $fines,
            'tshirt_deduction' => $tshirt,
            'capital_cmg' => $capital,
            'net_cash' => $net,
            'verify_code' => InvestmentPayout::makeCode(),
            'status' => 'pending',
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
        $totalNet += $net;
    }

    public function show(InvestmentPayout $payout)
    {
        $payout->load(['member', 'investment']);
        $code = $payout->verify_code;

        $postings = [
            'repayments' => LoanRepayment::with('loan')->where('notes', 'like', "%{$code}%")->get(),
            'swf' => SwfEntry::where('reason', 'like', "%{$code}%")->get(),
            'finance' => \App\Models\FinanceTransaction::with('account')->where('description', 'like', "%{$code}%")->get(),
            'returns' => InvestmentReturn::where('notes', 'like', "%{$code}%")->get(),
            'sms' => \App\Models\SmsLog::where('investment_payout_id', $payout->id)->latest()->get(),
        ];

        return view('investments.payout-show', compact('payout', 'postings'));
    }

    public function pay(InvestmentPayout $payout)
    {
        if ($payout->status !== 'verified') {
            return back()->withErrors(['payout' => 'Member must verify first (status is '.$payout->status.').']);
        }

        DB::transaction(function () use ($payout) {
            $member = $payout->member;

            if ($payout->loan_installment > 0) {
                $this->applyLoan($member, (float) $payout->loan_installment, 'Matured payout deduction '.$payout->verify_code);
            }

            if ($payout->swf_deduction > 0) {
                SwfEntry::create([
                    'member_id' => $member->id,
                    'receipt_no' => 'SWF-'.now()->format('YmdHis').'-'.random_int(100, 999),
                    'type' => 'contribution',
                    'amount' => $payout->swf_deduction,
                    'method' => 'bank',
                    'transacted_at' => now()->toDateString(),
                    'reason' => 'Matured payout deduction '.$payout->verify_code,
                    'received_by' => auth()->id(),
                ]);
            }

            $cash = FinanceAccount::where('code', '1000')->first();
            foreach ([[$payout->fines_deduction, 'fee', 'Fines deduction '.$payout->verify_code], [$payout->tshirt_deduction, 'other', 'T-shirt deduction '.$payout->verify_code], [$payout->capital_cmg, 'other', 'Capital FeedTan CMG '.$payout->verify_code]] as [$amt, $cat, $desc]) {
                if ($amt > 0 && $cash) {
                    $tx = \App\Models\FinanceTransaction::create([
                        'reference' => FinancePosting::reference('FT'),
                        'type' => 'income',
                        'category' => $cat,
                        'finance_account_id' => $cash->id,
                        'member_id' => $member->id,
                        'amount' => $amt,
                        'transacted_at' => now()->toDateString(),
                        'description' => $desc,
                        'created_by' => auth()->id(),
                    ]);
                    FinancePosting::postTransaction($tx->fresh());
                }
            }

            // Member-chosen allocation of the remaining net cash.
            foreach ($this->resolveAllocation($payout) as $kind => $item) {
                $this->applyAllocation($payout, $member, $kind, $item);
            }

            $payout->update(['status' => 'paid', 'paid_at' => now()]);
        });

        return back()->with('status', 'Payout '.$payout->verify_code.' paid and deductions applied.');
    }

    protected function resolveAllocation(InvestmentPayout $payout): array
    {
        if (is_array($payout->allocation) && $payout->allocation) {
            return $payout->allocation;
        }

        // Backwards compatibility for rows verified before allocation existed.
        $net = (float) $payout->net_cash;

        return match ($payout->decision) {
            'reinvest' => ['reinvest' => $net, 'reinvest_term' => '2'],
            'keep_savings' => ['savings' => $net, 'savings_type' => 'flex'],
            default => ['cash' => $net],
        };
    }

    protected function applyLoan(Member $member, float $amount, string $notes): void
    {
        if ($amount <= 0) {
            return;
        }
        $loan = $member->loans()->whereIn('status', ['active', 'overdue'])->orderBy('due_date')->first();
        if (! $loan || $loan->outstanding() <= 0) {
            return;
        }
        LoanRepayment::create([
            'loan_id' => $loan->id,
            'receipt_no' => 'LR-'.now()->format('YmdHis').'-'.random_int(100, 999),
            'amount' => min($amount, $loan->outstanding()),
            'paid_at' => now()->toDateString(),
            'method' => 'bank',
            'notes' => $notes,
            'received_by' => auth()->id(),
        ]);
        if ($loan->fresh()->outstanding() <= 0) {
            $loan->update(['status' => 'paid']);
        }
    }

    protected function applyAllocation(InvestmentPayout $payout, Member $member, string $kind, mixed $item): void
    {
        $cash = FinanceAccount::where('code', '1000')->first();

        switch ($kind) {
            case 'cash':
                if ((float) $item > 0 && $payout->investment_id) {
                    $via = $payout->allocation['cash_method'] ?? null;
                    $acct = $payout->allocation['cash_account'] ?? null;
                    InvestmentReturn::create([
                        'investment_id' => $payout->investment_id,
                        'amount' => (float) $item,
                        'paid_at' => now()->toDateString(),
                        'notes' => 'Matured payout cash '.$payout->verify_code.($via ? ' via '.$via.($acct ? ' '.$acct : '') : ''),
                        'paid_by' => auth()->id(),
                    ]);
                }
                break;
            case 'swf':
                if ((float) $item > 0) {
                    SwfEntry::create([
                        'member_id' => $member->id,
                        'receipt_no' => 'SWF-'.now()->format('YmdHis').'-'.random_int(100, 999),
                        'type' => 'contribution',
                        'amount' => (float) $item,
                        'method' => 'bank',
                        'transacted_at' => now()->toDateString(),
                        'reason' => 'Member allocation '.$payout->verify_code,
                        'received_by' => auth()->id(),
                    ]);
                }
                break;
            case 'loan':
                $this->applyLoan($member, (float) $item, 'Member allocation (rejesho) '.$payout->verify_code);
                break;
            case 'shares':
                if ((float) $item > 0 && $cash) {
                    $tx = \App\Models\FinanceTransaction::create([
                        'reference' => FinancePosting::reference('FT'),
                        'type' => 'income',
                        'category' => 'other',
                        'finance_account_id' => $cash->id,
                        'member_id' => $member->id,
                        'amount' => (float) $item,
                        'transacted_at' => now()->toDateString(),
                        'description' => 'Hisa za duka '.$payout->verify_code,
                        'created_by' => auth()->id(),
                    ]);
                    FinancePosting::postTransaction($tx->fresh());
                }
                break;
            case 'reinvest':
                if ((float) $item > 0) {
                    $years = in_array($payout->allocation['reinvest_term'] ?? null, ['2', '4', '6'], true)
                        ? (int) $payout->allocation['reinvest_term'] : 2;
                    $rate = (float) (\App\Models\Setting::get('default_investment_return', '0'));
                    Investment::create([
                        'member_id' => $member->id,
                        'investment_no' => 'INV-'.now()->format('YmdHis').'-'.random_int(100, 999),
                        'amount' => (float) $item,
                        'expected_return_rate' => $rate,
                        'expected_return' => round((float) $item * $rate / 100, 2),
                        'start_date' => now()->toDateString(),
                        'maturity_date' => now()->addYears($years)->toDateString(),
                        'status' => 'active',
                        'plan' => "Reinvest {$years}-year FIA",
                        'notes' => 'From matured payout '.$payout->verify_code,
                        'created_by' => auth()->id(),
                    ]);
                }
                break;
            case 'savings':
                if ((float) $item > 0) {
                    \App\Models\Deposit::create([
                        'member_id' => $member->id,
                        'receipt_no' => 'DP-'.now()->format('YmdHis').'-'.random_int(100, 999),
                        'type' => 'deposit',
                        'amount' => (float) $item,
                        'method' => 'bank',
                        'transacted_at' => now()->toDateString(),
                        'notes' => 'Akiba '.strtoupper($payout->allocation['savings_type'] ?? '').' from payout '.$payout->verify_code,
                        'received_by' => auth()->id(),
                    ]);
                }
                break;
        }
    }

    protected function sendOne(InvestmentPayout $payout, ?string $template = null): bool
    {
        [$ok, $resp] = \App\Services\SmsService::send(
            $payout->intlPhone(),
            \App\Services\SmsService::buildMessage($payout->load('member'), $template)
        );

        \App\Models\SmsLog::create([
            'investment_payout_id' => $payout->id,
            'member_id' => $payout->member_id,
            'phone' => $payout->phone,
            'message' => \App\Services\SmsService::buildMessage($payout, $template),
            'status' => $ok ? 'sent' : 'failed',
            'provider_response' => $resp,
            'created_by' => auth()->id(),
        ]);

        if ($ok) {
            $payout->update(['sms_sent_at' => now()]);
        }

        return $ok;
    }

    public function sendSms(InvestmentPayout $payout)
    {
        $ok = $this->sendOne($payout);

        return back()->with($ok ? 'status' : 'import_failed', $ok
            ? 'SMS sent to '.$payout->phone.'.'
            : ['SMS to '.$payout->phone.' failed — check Communication Settings.']);
    }
    public function sendBulk(Request $request)
    {
        $data = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
            'resend' => ['nullable'],
        ]);
        $template = $data['message'] ?? null;

        $query = InvestmentPayout::with('member')->where('kind', 'matured')->where('status', 'pending')->where('phone', '!=', '');
        if (! $request->boolean('resend')) {
            $query->whereNull('sms_sent_at');
        }
        $payouts = $query->get();

        if ($payouts->isEmpty()) {
            return back()->with('status', 'No pending payouts waiting for SMS.');
        }

        // One NextSMS v2 multi request when possible, else per-recipient singles.
        [$bulkOk, $bulkResp] = \App\Services\SmsService::sendBulk(
            $payouts->map(fn ($p) => ['to' => $p->intlPhone(), 'text' => \App\Services\SmsService::buildMessage($p, $template)])->all()
        );

        $sent = 0;
        $failed = 0;
        if ($bulkOk) {
            foreach ($payouts as $p) {
                \App\Models\SmsLog::create([
                    'investment_payout_id' => $p->id,
                    'member_id' => $p->member_id,
                    'phone' => $p->phone,
                    'message' => \App\Services\SmsService::buildMessage($p, $template),
                    'status' => 'sent',
                    'provider_response' => 'bulk: '.$bulkResp,
                    'created_by' => auth()->id(),
                ]);
                $p->update(['sms_sent_at' => now()]);
                $sent++;
            }

            return back()->with('status', "Bulk SMS done in one request: {$sent} sent.");
        }

        foreach ($payouts as $payout) {
            $this->sendOne($payout, $template) ? $sent++ : $failed++;
        }

        return back()->with('status', "Bulk SMS done: {$sent} sent, {$failed} failed.");
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'string'],
        ]);

        $deleted = 0;
        $skipped = [];

        foreach ($data['ids'] as $hash) {
            try {
                $payout = InvestmentPayout::findOrFail(did($hash));
            } catch (\Throwable) {
                continue;
            }

            if ($payout->status === 'paid') {
                $skipped[] = $payout->verify_code;
                continue;
            }

            DB::transaction(function () use ($payout) {
                $investment = $payout->investment;
                $payout->delete();

                // Clean up investments auto-created by the import when nothing else uses them.
                if ($investment && $investment->plan === 'Payout import'
                    && $investment->payouts()->count() === 0
                    && $investment->returns()->count() === 0) {
                    $investment->delete();
                }
            });
            $deleted++;
        }

        $msg = "Bulk delete: {$deleted} removed.";
        if ($skipped) {
            $msg .= ' Skipped paid: '.implode(', ', $skipped).'.';
        }

        return back()->with('status', $msg);
    }
}
