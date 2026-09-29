<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function activity(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $query = ActivityLog::query()->with('user')->latest();

        if ($q !== '') {
            $query->where(fn ($w) => $w->where('action', 'like', "%{$q}%")
                ->orWhere('subject', 'like', "%{$q}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")));
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('activity.index', compact('logs', 'q'));
    }

    public function access(Request $request)
    {
        $event = $request->get('event', 'all');
        $query = AccessLog::query()->with('user')->latest();

        if (in_array($event, ['login', 'logout', 'failed'], true)) {
            $query->where('event', $event);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('activity.access', compact('logs', 'event'));
    }
}
