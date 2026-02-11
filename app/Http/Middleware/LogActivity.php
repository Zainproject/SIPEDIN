<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // skip route auth biar tidak spam
        $skipNames = [
            'login',
            'logout',
            'register',
            'password.request',
            'password.email',
            'password.reset',
            'password.store',
            'password.update',
            'verification.notice',
            'verification.verify',
            'verification.send',
        ];

        $routeName = optional($request->route())->getName();
        if ($routeName && in_array($routeName, $skipNames, true)) {
            return $response;
        }

        // catat hanya ketika sudah login
        if (!Auth::check()) {
            return $response;
        }

        $method = strtoupper($request->method());

        $action = match ($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'visit',
        };

        $payload = [
            'query' => $request->query(),
            'input' => $request->except(['password', 'password_confirmation']),
        ];

        if ($request->files->count() > 0) {
            $payload['files'] = 'has_files';
        }

        Activity::create([
            'user_id' => Auth::id(), // ✅ jangan auth()->id() biar intelephense aman
            'action' => $action,
            'method' => $method,
            'url' => $request->fullUrl(),
            'route' => $routeName,
            'payload' => $payload,
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return $response;
    }
}
