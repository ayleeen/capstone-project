<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'user_name', 'action', 'table_name', 'record_id',
        'details', 'old_values', 'new_values', 'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        string $action,
        Model $model,
        ?array $old = null,
        ?array $new = null,
        ?string $details = null
    ): void {
        $user = Auth::user();

        static::create([
            'user_id'    => $user?->id,
            'user_name'  => $user?->name ?? 'System',
            'action'     => $action,
            'table_name' => $model->getTable(),
            'record_id'  => $model->getKey(),
            'details'    => $details,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
        ]);
    }
}