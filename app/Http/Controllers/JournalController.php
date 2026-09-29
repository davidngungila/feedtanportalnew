<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\FinanceAccount;
use App\Services\FinancePosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = JournalEntry::query()->withCount('lines')->latest();

        if (in_array($status, ['draft', 'posted'], true)) {
            $query->where('status', $status);
        }

        $entries = $query->paginate(15)->withQueryString();

        return view('finance.journals', compact('entries', 'status'));
    }

    public function ledger(Request $request)
    {
        $accounts = FinanceAccount::orderBy('code')->get();
        $accountId = $request->get('account_id', $accounts->first()?->id);
        $from = $request->get('from', '');
        $to = $request->get('to', '');

        $account = $accountId ? FinanceAccount::find($accountId) : null;
        $lines = collect();
        $opening = 0;
        $closing = 0;

        if ($account) {
            // Opening = balance before $from (opening + prior posted lines).
            $priorDebit = (float) $account->lines()->whereHas('entry', function ($e) use ($from) {
                $e->where('status', 'posted');
                if ($from !== '') {
                    $e->whereDate('entry_date', '<', $from);
                }
            })->sum('debit');
            $priorCredit = (float) $account->lines()->whereHas('entry', function ($e) use ($from) {
                $e->where('status', 'posted');
                if ($from !== '') {
                    $e->whereDate('entry_date', '<', $from);
                }
            })->sum('credit');

            $opening = in_array($account->type, ['asset', 'expense'], true)
                ? (float) $account->opening_balance + $priorDebit - $priorCredit
                : (float) $account->opening_balance + $priorCredit - $priorDebit;

            $lines = $account->lines()->with('entry')->whereHas('entry', function ($e) use ($from, $to) {
                $e->where('status', 'posted');
                if ($from !== '') {
                    $e->whereDate('entry_date', '>=', $from);
                }
                if ($to !== '') {
                    $e->whereDate('entry_date', '<=', $to);
                }
            })->orderBy('id')->paginate(25)->withQueryString();

            $closing = $account->balance(null, $to !== '' ? $to : null);
        }

        return view('finance.ledger', compact('accounts', 'account', 'accountId', 'from', 'to', 'lines', 'opening', 'closing'));
    }

    public function create()
    {
        $accounts = FinanceAccount::where('status', 'active')->orderBy('code')->get();

        return view('finance.journal-create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'entry_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.finance_account_id' => ['required', 'exists:finance_accounts,id'],
            'lines.*.debit' => ['required', 'numeric', 'min:0'],
            'lines.*.credit' => ['required', 'numeric', 'min:0'],
            'lines.*.narration' => ['nullable', 'string', 'max:255'],
        ]);

        FinancePosting::assertOpenPeriod($data['entry_date']);

        $debit = collect($data['lines'])->sum('debit');
        $credit = collect($data['lines'])->sum('credit');

        if ($debit <= 0 || abs($debit - $credit) > 0.005) {
            return back()->withErrors(['lines' => 'Journal must balance: total debit must equal total credit.'])->withInput();
        }

        $entry = DB::transaction(function () use ($data) {
            $entry = JournalEntry::create([
                'reference' => FinancePosting::reference('JE'),
                'entry_date' => $data['entry_date'],
                'description' => $data['description'],
                'status' => 'posted',
                'created_by' => auth()->id(),
            ]);

            foreach ($data['lines'] as $line) {
                if ((float) $line['debit'] == 0 && (float) $line['credit'] == 0) {
                    continue;
                }
                $entry->lines()->create($line);
            }

            return $entry;
        });

        return redirect()->route('finance.journals.show', $entry)->with('status', 'Journal posted.');
    }

    public function show(JournalEntry $journal)
    {
        $journal->load(['lines.account']);

        return view('finance.journal-show', compact('journal'));
    }

    public function destroy(JournalEntry $journal)
    {
        if ($journal->source_type) {
            return back()->withErrors(['journal' => 'Auto-posted entries cannot be deleted here — remove the source record instead.']);
        }
        $journal->lines()->delete();
        $journal->delete();

        return redirect()->route('finance.journals')->with('status', 'Journal removed.');
    }
}
