<?php

namespace App\Http\Controllers;

use App\Models\MemberType;
use Illuminate\Http\Request;

class MemberTypeController extends Controller
{
    public function index()
    {
        $types = MemberType::withCount('members')->latest()->paginate(15);

        return view('member-types.index', compact('types'));
    }

    public function create()
    {
        return view('member-types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:member_types,name'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $type = MemberType::create($data);

        return redirect()->route('member-types.index')->with('status', 'Member type created.');
    }

    public function edit(MemberType $memberType)
    {
        return view('member-types.edit', ['type' => $memberType]);
    }

    public function update(Request $request, MemberType $memberType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:member_types,name,'.$memberType->id],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $memberType->update($data);

        return redirect()->route('member-types.index')->with('status', 'Member type updated.');
    }

    public function destroy(MemberType $memberType)
    {
        $memberType->delete();

        return redirect()->route('member-types.index')->with('status', 'Member type removed.');
    }
}
