<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($this->shouldLog($request)) {
            $this->activityLogService->logRequest(Auth::user(), $request);
        }
    }

    private function shouldLog(Request $request): bool
    {
        if (! Auth::check()) {
            return false;
        }

        // Exclusions via config
        $excludedRoutes = config('activitylog.exclude_route_names', []);
        $excludedPaths = config('activitylog.exclude_paths', []);
        $excludedPrefixes = config('activitylog.exclude_prefixes', []);

        $routeName = optional($request->route())->getName();

        if ($routeName && in_array($routeName, $excludedRoutes, true)) {
            return false;
        }

        // Always define $path to prevent undefined variable errors
        $path = '/'.ltrim($request->path() ?? '', '/');

        // Check excluded paths only if there's a path to check
        if ($request->path()) {
            foreach ($excludedPaths as $excluded) {
                if (trim($excluded) !== '' && strcasecmp($path, $excluded) === 0) {
                    return false;
                }
            }
        }

        // Always check excluded prefixes (even for empty paths)
        foreach ($excludedPrefixes as $prefix) {
            if ($prefix !== '' && str_starts_with($path, $prefix)) {
                return false;
            }
        }

        return true;
    }
}

