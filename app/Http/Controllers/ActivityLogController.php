<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Yajra\DataTables\Facades\DataTables;

class ActivityLogController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:admin')];
    }

    public function index(Request $request): View|JsonResponse
    {
        $logs = ActivityLog::query()
            ->leftJoin('users', 'users.id', '=', 'activity_logs.user_id')
            ->select([
                'activity_logs.id',
                'activity_logs.user_id',
                'activity_logs.action',
                'activity_logs.description',
                'activity_logs.created_at',
                'users.name as user_name',
            ])
            ->orderByDesc('activity_logs.created_at')
            ->orderByDesc('activity_logs.id');

        if ($request->ajax()) {
            return DataTables::eloquent($logs)
                ->addIndexColumn()
                ->editColumn('created_at', fn (ActivityLog $log): string => Carbon::parse($log->created_at)->timezone('Asia/Jakarta')->format('d/m/Y H:i:s'))
                ->editColumn('user_name', fn (ActivityLog $log): string => $log->user_name ?: 'System')
                ->filterColumn('user_name', function (Builder $query, string $keyword): void {
                    $query->where('users.name', 'like', "%{$keyword}%");
                })
                ->orderColumn('user_name', function (Builder $query, string $direction): void {
                    $query->orderBy('users.name', $direction === 'desc' ? 'desc' : 'asc');
                })
                ->addColumn('action_display', function (ActivityLog $log): string {
                    return view('logs.datatables.action', ['log' => $log])->render();
                })
                ->addColumn('details_action', function (ActivityLog $log): string {
                    return view('logs.datatables.details', ['log' => $log])->render();
                })
                ->rawColumns(['action_display', 'details_action'])
                ->toJson();
        }

        return view('logs.index');
    }

    public function show(Request $request, ActivityLog $activityLog): View
    {
        $activityLog->load('user');

        if ($request->ajax()) {
            return view('logs.partials.detail-content', compact('activityLog'));
        }

        return view('logs.show', compact('activityLog'));
    }
}
