<?php

namespace App\Providers;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        Event::listen(Login::class, function (Login $event) {
            if ($event->user) {
                $event->user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => request()->ip(),
                ]);
                AuditLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'login',
                    'auditable_type' => get_class($event->user),
                    'auditable_id' => $event->user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'description' => 'User logged in successfully.',
                ]);
            }
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                AuditLog::create([
                    'user_id' => $event->user->id,
                    'action' => 'logout',
                    'auditable_type' => get_class($event->user),
                    'auditable_id' => $event->user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'description' => 'User logged out.',
                ]);
            }
        });
    }
}
