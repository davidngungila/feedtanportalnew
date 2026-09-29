<?php

namespace App\Http\Controllers;

use App\Models\MemberGroup;
use Illuminate\Http\Request;

class MemberGroupController extends Controller
{
    public function index()
    {
        $groups = MemberGroup::withCount('members')->latest()->paginate(15);

        return view('member-groups.index', compact('groups'));
    }

    public function create()
    {
        return view('member-groups.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:member_groups,name'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        MemberGroup::create($data);

        return redirect()->route('member-groups.index')->with('status', 'Member group created.');
    }

    public function show(MemberGroup $memberGroup)
    {
        $memberGroup->load('members');

        return view('member-groups.show', ['group' => $memberGroup]);
    }

    public function edit(MemberGroup $memberGroup)
    {
        return view('member-groups.edit', ['group' => $memberGroup]);
    }

    public function update(Request $request, MemberGroup $memberGroup)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:member_groups,name,'.$memberGroup->id],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $memberGroup->update($data);

        return redirect()->route('member-groups.index')->with('status', 'Member group updated.');
    }

    public function destroy(MemberGroup $memberGroup)
    {
        $memberGroup->delete();

        return redirect()->route('member-groups.index')->with('status', 'Member group removed.');
    }
}
