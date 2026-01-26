<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {
    }

    public function index()
    {
        $stats = $this->dashboardService->getStats(auth()->user());

        return view('dashboard', compact('stats'));
    }
}
