<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberDocument;
use Illuminate\Http\Request;

class MemberDocumentController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $query = MemberDocument::query()->with('member')->latest();

        if ($q !== '') {
            $query->where(fn ($w) => $w->where('title', 'like', "%{$q}%")
                ->orWhereHas('member', fn ($m) => $m->where('name', 'like', "%{$q}%")));
        }

        $documents = $query->paginate(15)->withQueryString();

        return view('member-documents.index', compact('documents', 'q'));
    }

    public function create(Request $request)
    {
        $members = Member::orderBy('name')->get();
        $selectedMember = null;
        if ($request->filled('member_id')) {
            try {
                $selectedMember = did($request->get('member_id'));
            } catch (\Throwable) {
                $selectedMember = null;
            }
        }

        return view('member-documents.create', compact('members', 'selectedMember'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'title' => ['required', 'string', 'max:255'],
            'doc_type' => ['required', 'in:id,contract,photo,proof,other'],
            'file_path' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['uploaded_by'] = auth()->id();
        MemberDocument::create($data);

        return redirect()->route('member-documents.index')->with('status', 'Document recorded.');
    }

    public function destroy(MemberDocument $memberDocument)
    {
        $memberDocument->delete();

        return redirect()->route('member-documents.index')->with('status', 'Document removed.');
    }
}
