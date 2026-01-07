<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected SearchService $searchService
    ) {
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $tenantId = auth()->user()->tenant_id;

        $results = $this->searchService->search($query, $tenantId);

        return response()->json($results);
    }
}

