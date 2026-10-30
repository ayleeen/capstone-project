<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->limit(1000)
            ->get()
            ->map(function ($log) {
                $name = $log->user_name ?: 'System';

                return [
                    'id'        => 'LOG-' . str_pad($log->id, 4, '0', STR_PAD_LEFT),
                    'timestamp' => $log->created_at->toIso8601String(),
                    'user'      => [
                        'name'   => $name,
                        'role'   => $log->user
                            ? ($log->user->is_admin ? 'Administrator' : 'Customer')
                            : 'System',
                        'avatar' => 'https://ui-avatars.com/api/?size=64&background=429646&color=fff&name='
                            . urlencode($name),
                    ],
                    'action'   => $log->action,
                    'table'    => $log->table_name,
                    'recordId' => $log->record_id ? '#' . $log->record_id : '—',
                    'details'  => $log->details,
                    'ip'       => $log->ip_address ?: '—',
                    'changes'  => $this->buildChanges($log),
                ];
            })
            ->values();

        return view('admin.audit', compact('logs'));
    }

    private function buildChanges(AuditLog $log): array
    {
        $old = $log->old_values ?? [];
        $new = $log->new_values ?? [];

        $fields = array_unique(array_merge(array_keys($old), array_keys($new)));

        return collect($fields)
            ->reject(fn ($f) => in_array($f, ['created_at', 'updated_at', 'deleted_at']))
            ->map(fn ($f) => [
                'field' => $f,
                'old'   => $this->stringify($old[$f] ?? null),
                'new'   => $this->stringify($new[$f] ?? null),
            ])
            ->values()
            ->all();
    }

    private function stringify($value): string
    {
        if ($value === null) {
            return '';
        }

        return is_scalar($value) ? (string) $value : json_encode($value);
    }
}