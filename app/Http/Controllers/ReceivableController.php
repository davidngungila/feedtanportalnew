<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Receivable;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    private function list(string $kind, string $title)
    {
        $query = Receivable::query()->with('member')->where('kind', $kind)->latest();
        $rows = $query->paginate(15);
        $outstanding = (float) Receivable::where('kind', $kind)->whereIn('status', ['open', 'partial', 'overdue'])->get()->sum->outstanding();

        return view('finance.receivables', compact('rows', 'title', 'kind', 'outstanding'));
    }

    public function receivables()
    {
        return $this->list('receivable', 'Receivables');
    }

    public function payables()
    {
        return $this->list('payable', 'Payables');
    }

    public function create(Request $request)
    {
        $members = Member::orderBy('name')->get();
        $kind = $request->get('kind', 'receivable');

        return view('finance.receivable-create', compact('members', 'kind'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kind' => ['required', 'in:receivable,payable'],
            'party_name' => ['required', 'string', 'max:255'],
            'member_id' => ['nullable', 'exists:members,id'],
            'amount' => ['required', 'numeric', 'min:100'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        Receivable::create([
            ...$data,
            'reference' => \App\Services\FinancePosting::reference($data['kind'] === 'receivable' ? 'RCV' : 'PAY'),
            'paid_amount' => 0,
            'status' => 'open',
            'created_by' => auth()->id(),
        ]);

        $route = $data['kind'] === 'receivable' ? 'finance.receivables' : 'finance.payables';

        return redirect()->route($route)->with('status', 'Recorded.');
    }

    public function collect(Request $request, Receivable $receivable)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:'.$receivable->outstanding()],
        ]);

        $receivable->increment('paid_amount', $data['amount']);
        $receivable->update(['status' => $receivable->fresh()->outstanding() <= 0 ? 'paid' : 'partial']);

        return back()->with('status', 'Payment recorded.');
    }

    public function destroy(Receivable $receivable)
    {
        $receivable->delete();

        return back()->with('status', 'Record removed.');
    }
}
