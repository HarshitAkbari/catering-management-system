<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Package;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $categories = Category::where('tenant_id', auth()->user()->tenant_id)
            ->with('menuItems')
            ->orderBy('display_order')
            ->get();
        
        $packages = Package::where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'active')
            ->get();

        return view('menu.index', compact('categories', 'packages'));
    }
}
