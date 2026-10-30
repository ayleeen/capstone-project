<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Auth\Events\Login;
use App\Models\AuditLog;
use App\Models\BusinessProfile;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Login Audit Log
        Event::listen(Login::class, function ($e) {
            $u = $e->user;

            $name = $u->name
                ?? trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''))
                ?: ($u->email ?? 'User #' . $u->id);

            AuditLog::create([
                'user_id'    => $u->id,
                'user_name'  => $name,
                'action'     => 'login',
                'table_name' => 'users',
                'record_id'  => $u->id,
                'details'    => 'Logged in',
                'ip_address' => request()->ip(),
            ]);
        });

        // Share Business Profile with all admin views
        View::composer('admin.*', function ($view) {
            $view->with(
                'businessProfile',
                BusinessProfile::current()
            );
        });
    }
}
