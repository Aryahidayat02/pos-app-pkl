<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ActivityLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin')];
    }

    public function index()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(15);
        return view('logs.index', compact('logs'));
    }
}