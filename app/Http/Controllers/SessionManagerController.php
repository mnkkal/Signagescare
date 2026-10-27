<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SessionManagerController extends Controller
{
    /**
     * Display a listing of active browser sessions.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $currentSessionId = $request->session()->getId();

        // If admin, can optionally view all active user sessions across platform
        $allSessions = null;
        if ($user->can('view audit logs')) {
            $allSessions = Session::with('user')
                ->orderBy('last_activity', 'desc')
                ->take(50)
                ->get();
        }

        $userSessions = Session::where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === $currentSessionId,
                    'last_active' => $session->last_active,
                    'user_agent' => $session->user_agent,
                    'browser' => $this->parseBrowser($session->user_agent),
                    'platform' => $this->parsePlatform($session->user_agent),
                ];
            });

        $recentAuditLogs = AuditLog::where('user_id', $user->id)
            ->orWhere(function ($query) use ($user) {
                if ($user->can('view audit logs')) {
                    $query->whereNotNull('id');
                }
            })
            ->latest()
            ->take(15)
            ->get();

        return view('sessions.index', [
            'sessions' => $userSessions,
            'allSessions' => $allSessions,
            'recentLogs' => $recentAuditLogs,
            'currentSessionId' => $currentSessionId,
        ]);
    }

    /**
     * Log out from other browser sessions.
     */
    public function destroyOtherSessions(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $currentSessionId = $request->session()->getId();
        $user = $request->user();

        // Invalidate other sessions in database
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'logout_other_devices',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => 'User logged out all other browser sessions.',
        ]);

        return back()->with('status', 'All other browser sessions have been logged out.');
    }

    /**
     * Terminate a specific session.
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $session = DB::table('sessions')->where('id', $id)->first();

        if (!$session) {
            return back()->with('error', 'Session not found or already expired.');
        }

        // Only allow user to kill their own session, or Super Admin to kill any session
        if ($session->user_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'Unauthorized to terminate this session.');
        }

        DB::table('sessions')->where('id', $id)->delete();

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'terminate_session',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Terminated session ID: {$id}",
        ]);

        return back()->with('status', 'Target session was terminated successfully.');
    }

    private function parseBrowser(?string $userAgent): string
    {
        if (!$userAgent) return 'Unknown Browser';
        if (str_contains($userAgent, 'Edg')) return 'Microsoft Edge';
        if (str_contains($userAgent, 'Chrome')) return 'Google Chrome';
        if (str_contains($userAgent, 'Firefox')) return 'Mozilla Firefox';
        if (str_contains($userAgent, 'Safari')) return 'Apple Safari';
        if (str_contains($userAgent, 'Opera') || str_contains($userAgent, 'OPR')) return 'Opera';
        return 'Web Browser';
    }

    private function parsePlatform(?string $userAgent): string
    {
        if (!$userAgent) return 'Unknown Device';
        if (str_contains($userAgent, 'Windows')) return 'Windows PC';
        if (str_contains($userAgent, 'Macintosh')) return 'macOS';
        if (str_contains($userAgent, 'iPhone')) return 'Apple iPhone';
        if (str_contains($userAgent, 'iPad')) return 'Apple iPad';
        if (str_contains($userAgent, 'Android')) return 'Android Mobile';
        if (str_contains($userAgent, 'Linux')) return 'Linux';
        return 'Desktop / Mobile';
    }
}
