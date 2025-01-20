<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Bill;
use App\Models\Room;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'counts' => [
                'rooms' => Room::where('status', 'available')->count(),
                'tenants' => Room::where('status', 'occupied')->count(),
                'bills' => Bill::where('status', 'pending')->count()
            ]
        ]);
    }
}