<?php

namespace App\Http\Controllers;

use App\Imports\PayoutSheetImport;
use App\Models\FinanceAccount;
use App\Models\InvestmentPayout;
use App\Models\LoanRepayment;
use App\Models\Member;
use App\Models\SwfEntry;
use App\Services\FinancePosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CouponPaymentController extends Controller
{
    public const HEADERS = ['Name', 'Amount Earned', 'Loan installment', 'SWF deduction', 'Fines deduction', 'T-shirt deduction', 'Capital FeedTan CMG', 'Net cash', 'Phone of their payment'];

    public function index()
    {
        $payouts = InvestmentPayout::with('member')->where('kind', 'coupon')->latest()->limit(50)->get();
        $pendingSms = InvestmentPayout::where('kind', 'coupon')->where('status', 'pending')->whereNull('sms_sent_at')->count();

        return view('coupon.import', compact('payouts', 'pendingSms'));
    }

    public function template()
    {
        $rows = [self::HEADERS, ['Amina Juma', '500000', '100000', '10000', '5000', '15000', '20000', '350000', '0712345678']];

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        }, 'coupon-payment-template.csv', ['Content-Type' => 'text/csv']);
    }

    public function export()
    {
        return Excel::download(
            new \App\Exports\CouponPaymentsExport,
            'coupon-payments-'.now()->format('Ymd-His').'.xlsx'
        );
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
        // Phone header may be "Phone", "Phone of their payment", etc. -> normalized keys: phone, phoneoftheirpayment.
        $hasPhone = in_array('phone', $headers, true) || in_array('phoneoftheirpayment', $headers, true);
        $need = ['name', 'amountearned', 'netcash'];
        foreach ($need as $col) {
            if (! in_array($col, $headers, true)) {
                return back()->withErrors(['sheet' => 'Columns must be: '.implode(', ', self::HEADERS).'.']);
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
            // Alias long phone header to "phone".
            if (! isset($row['phone']) || $row['phone'] === '') {
                $row['phone'] = $row['phoneoftheirpayment'] ?? '';
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

        return redirect()->route('coupon.index')
            ->with('status', "Imported {$imported} coupon payment(s), total net ".money($totalNet).". New members: {$createdMembers}. Failed: ".count($failed).'.')
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
        if ($phone === '') {
            $fail('Phone of their payment is required (needed for SMS verification).');
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

        $member = Member::where('phone', $phone)->first();
        if (! $member) {
            $member = Member::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();
        }
        if (! $member) {
            $member = Member::create([
                'member_no' => 'M-'.now()->format('Ymd').'-'.str_pad((string) (Member::max('id') + 1), 4, '0', STR_PAD_LEFT),
                'name' => $name,
                'phone' => $phone,
                'join_date' => now()->toDateString(),
                'status' => 'active',
                'notes' => 'Created from coupon payment import.',
                'created_by' => auth()->id(),
            ]);
            $createdMembers++;
        }

        InvestmentPayout::create([
            'investment_id' => null,
            'kind' => 'coupon',
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
        abort_unless($payout->kind === 'coupon', 404);
        $payout->load(['member']);
        $code = $payout->verify_code;

        $postings = [
            'repayments' => LoanRepayment::with('loan')->where('notes', 'like', "%{$code}%")->get(),
            'swf' => SwfEntry::where('reason', 'like', "%{$code}%")->get(),
            'finance' => \App\Models\FinanceTransaction::with('account')->where('description', 'like', "%{$code}%")->get(),
            'settlement' => \App\Models\JournalEntry::with('lines.account')->where('source_type', \App\Models\InvestmentPayout::class)->where('source_id', $payout->id)->where('source_tag', 'settlement')->first(),
            'sms' => \App\Models\SmsLog::where('investment_payout_id', $payout->id)->latest()->get(),
        ];

        return view('coupon.show', compact('payout', 'postings'));
    }

    public function pay(InvestmentPayout $payout)
    {
        abort_unless($payout->kind === 'coupon', 404);
        if ($payout->status !== 'verified') {
            return back()->withErrors(['payout' => 'Member must verify first (status is '.$payout->status.').']);
        }

        DB::transaction(function () use ($payout) {
            $member = $payout->member;
            $parts = [
                'gross' => (float) $payout->amount,
                'loan' => 0,
                'swf' => 0,
                'fee' => (float) $payout->fines_deduction,
                'other' => (float) $payout->tshirt_deduction + (float) $payout->capital_cmg,
                'shares' => 0,
                'savings' => 0,
                'reinvest' => 0,
                'cash' => (float) $payout->net_cash,
            ];

            // Operational records post nothing here — one compound journal settles all legs.
            FinancePosting::withoutPosting(function () use ($payout, $member, &$parts) {
                if ($payout->loan_installment > 0) {
                    $parts['loan'] += $this->applyLoan($member, (float) $payout->loan_installment, 'Coupon payout deduction '.$payout->verify_code);
                }

                if ($payout->swf_deduction > 0) {
                    SwfEntry::create([
                        'member_id' => $member->id,
                        'receipt_no' => 'SWF-'.now()->format('YmdHis').'-'.random_int(100, 999),
                        'type' => 'contribution',
                        'amount' => $payout->swf_deduction,
                        'method' => 'bank',
                        'transacted_at' => now()->toDateString(),
                        'reason' => 'Coupon payout deduction '.$payout->verify_code,
                        'received_by' => auth()->id(),
                    ]);
                    $parts['swf'] += (float) $payout->swf_deduction;
                }
            });

            FinancePosting::postPayoutSettlement($payout, $parts, $payout->allocation['cash_method'] ?? null);

            $payout->update(['status' => 'paid', 'paid_at' => now()]);
        });

        return back()->with('status', 'Coupon '.$payout->verify_code.' paid and posted to the ledger.');
    }

    protected function applyLoan(Member $member, float $amount, string $notes): float
    {
        if ($amount <= 0) {
            return 0;
        }
        $loan = $member->loans()->whereIn('status', ['active', 'overdue'])->orderBy('due_date')->first();
        if (! $loan || $loan->outstanding() <= 0) {
            return 0;
        }
        $applied = min($amount, $loan->outstanding());
        LoanRepayment::create([
            'loan_id' => $loan->id,
            'receipt_no' => 'LR-'.now()->format('YmdHis').'-'.random_int(100, 999),
            'amount' => $applied,
            'paid_at' => now()->toDateString(),
            'method' => 'bank',
            'notes' => $notes,
            'received_by' => auth()->id(),
        ]);
        if ($loan->fresh()->outstanding() <= 0) {
            $loan->update(['status' => 'paid']);
        }

        return $applied;
    }

    protected function sendOne(InvestmentPayout $payout, ?string $template = null): bool
    {
        [$ok, $resp] = \App\Services\SmsService::send(
            $payout->intlPhone(),
            \App\Services\SmsService::buildCouponMessage($payout->load('member'), $template)
        );

        \App\Models\SmsLog::create([
            'investment_payout_id' => $payout->id,
            'member_id' => $payout->member_id,
            'phone' => $payout->phone,
            'message' => \App\Services\SmsService::buildCouponMessage($payout, $template),
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
        abort_unless($payout->kind === 'coupon', 404);
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

        $query = InvestmentPayout::with('member')->where('kind', 'coupon')->where('status', 'pending')->where('phone', '!=', '');
        if (! $request->boolean('resend')) {
            $query->whereNull('sms_sent_at');
        }
        $payouts = $query->get();

        if ($payouts->isEmpty()) {
            return back()->with('status', 'No pending coupon payments waiting for SMS.');
        }

        // One NextSMS v2 multi request when possible, else per-recipient singles.
        [$bulkOk, $bulkResp] = \App\Services\SmsService::sendBulk(
            $payouts->map(fn ($p) => ['to' => $p->intlPhone(), 'text' => \App\Services\SmsService::buildCouponMessage($p, $template)])->all()
        );

        $sent = 0;
        $failed = 0;
        if ($bulkOk) {
            foreach ($payouts as $p) {
                \App\Models\SmsLog::create([
                    'investment_payout_id' => $p->id,
                    'member_id' => $p->member_id,
                    'phone' => $p->phone,
                    'message' => \App\Services\SmsService::buildCouponMessage($p, $template),
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

            if ($payout->kind !== 'coupon') {
                continue;
            }

            if ($payout->status === 'paid') {
                $skipped[] = $payout->verify_code;
                continue;
            }

            $payout->delete();
            $deleted++;
        }

        $msg = "Bulk delete: {$deleted} removed.";
        if ($skipped) {
            $msg .= ' Skipped paid: '.implode(', ', $skipped).'.';
        }

        return back()->with('status', $msg);
    }
}
