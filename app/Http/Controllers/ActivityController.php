<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display the activity logs page.
     */
    public function index()
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $currentUserId = $user->id;

        // Get activities
        $activities = $this->getActivities($tenantId, $currentUserId);

        return view('activity', compact('activities'));
    }

    /**
     * Get activities grouped by date
     */
    private function getActivities(int $tenantId, int $currentUserId, int $limit = 10): array
    {
        // Get activities from last 3 days
        $startDate = now()->subDays(2)->startOfDay();

        // Following tab: Activities from other users in the same tenant
        $followingActivities = ActivityLog::where('tenant_id', $tenantId)
            ->where('user_id', '!=', $currentUserId)
            ->whereNotNull('user_id')
            ->where('visited_at', '>=', $startDate)
            ->with('user')
            ->orderBy('visited_at', 'desc')
            ->limit($limit)
            ->get();

        // You tab: Activities from current user
        $yourActivities = ActivityLog::where('tenant_id', $tenantId)
            ->where('user_id', $currentUserId)
            ->where('visited_at', '>=', $startDate)
            ->with('user')
            ->orderBy('visited_at', 'desc')
            ->limit($limit)
            ->get();

        return [
            'following' => $this->groupActivitiesByDate($followingActivities),
            'you' => $this->groupActivitiesByDate($yourActivities),
        ];
    }

    /**
     * Load more activities via AJAX
     */
    public function loadMoreActivities(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $currentUserId = $user->id;

        $request->validate([
            'tab' => 'required|in:following,you',
            'date_filter' => 'required|in:today,yesterday',
            'offset' => 'required|integer|min:0',
        ]);

        $tab = $request->input('tab');
        $dateFilter = $request->input('date_filter');
        $offset = (int) $request->input('offset');
        $limit = 10;

        // Get activities from last 3 days
        $startDate = now()->subDays(2)->startOfDay();
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        // Build query based on tab
        $query = ActivityLog::where('tenant_id', $tenantId)
            ->where('visited_at', '>=', $startDate)
            ->with('user')
            ->orderBy('visited_at', 'desc');

        if ($tab === 'following') {
            $query->where('user_id', '!=', $currentUserId)
                  ->whereNotNull('user_id');
        } else {
            $query->where('user_id', $currentUserId);
        }

        // Apply date filter
        if ($dateFilter === 'today') {
            $query->where('visited_at', '>=', $today);
        } else {
            $query->where('visited_at', '>=', $yesterday)
                  ->where('visited_at', '<', $today);
        }

        // Get total count before pagination
        $totalCount = $query->count();

        // Apply offset and limit
        $activities = $query->skip($offset)
            ->take($limit)
            ->get();

        // Format activities
        $formattedActivities = $activities->map(function ($activity) {
            return $this->formatActivity($activity);
        })->toArray();

        return response()->json([
            'success' => true,
            'activities' => $formattedActivities,
            'has_more' => ($offset + $limit) < $totalCount,
            'total' => $totalCount,
        ]);
    }

    /**
     * Group activities by date (Today, Yesterday)
     */
    private function groupActivitiesByDate($activities): array
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        $grouped = [
            'today' => [],
            'yesterday' => [],
        ];

        foreach ($activities as $activity) {
            $visitedAt = $activity->visited_at;
            
            if ($visitedAt >= $today) {
                $grouped['today'][] = $this->formatActivity($activity);
            } elseif ($visitedAt >= $yesterday) {
                $grouped['yesterday'][] = $this->formatActivity($activity);
            }
        }

        return $grouped;
    }

    /**
     * Format activity for display
     */
    private function formatActivity(ActivityLog $activity): array
    {
        $user = $activity->user;
        $userName = $user ? $user->name : 'Unknown User';
        $initials = $this->getUserInitials($userName);
        
        // Get badge color based on user_id hash for consistency
        $badgeColors = ['badge-primary', 'badge-warning', 'badge-secondary', 'badge-info', 'badge-success', 'badge-danger'];
        $colorIndex = $user ? ($user->id % count($badgeColors)) : 0;
        $badgeColor = $badgeColors[$colorIndex];

        return [
            'id' => $activity->id,
            'user_name' => $userName,
            'user_initials' => $initials,
            'badge_color' => $badgeColor,
            'description' => $activity->description ?? 'performed an action',
            'time' => $activity->visited_at->format('g:i A'),
            'visited_at' => $activity->visited_at,
        ];
    }

    /**
     * Get user initials from name
     */
    private function getUserInitials(?string $name): string
    {
        if (!$name) {
            return 'U';
        }

        $name = trim($name);
        $parts = explode(' ', $name);
        
        if (count($parts) >= 2) {
            // First letter of first name + first letter of last name
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[count($parts) - 1], 0, 1));
        } else {
            // First two letters of single name
            return strtoupper(substr($name, 0, 2));
        }
    }
}

