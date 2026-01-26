<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class ActivityLogService extends BaseService
{
    public function __construct(private readonly ActivityLogRepository $repository)
    {
        parent::__construct($repository);
    }

    public function logRequest(?Authenticatable $user, Request $request): void
    {
        try {
            $this->repository->create([
                'user_id' => $user?->getAuthIdentifier(),
                'route_name' => optional($request->route())->getName(),
                'url' => $request->fullUrl(),
                'http_method' => $request->getMethod(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1024),
                'visited_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Avoid breaking the request cycle. Consider logging to a channel.
            report($e);
        }
    }

    public function logLogin(?Authenticatable $user, Request $request): void
    {
        try {
            $this->repository->create([
                'user_id' => $user?->getAuthIdentifier(),
                'route_name' => 'login',
                'url' => $request->fullUrl(),
                'http_method' => $request->getMethod(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1024),
                'visited_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function logLogout(?Authenticatable $user, Request $request): void
    {
        try {
            $this->repository->create([
                'user_id' => $user?->getAuthIdentifier(),
                'route_name' => 'logout',
                'url' => $request->fullUrl(),
                'http_method' => $request->getMethod(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 1024),
                'visited_at' => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}

