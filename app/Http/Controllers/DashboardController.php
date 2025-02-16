<?php

namespace App\Http\Controllers;

use App\Models\Equipement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $equipmentCount = Equipement::count();
        $latestEquipments = Equipement::latest()->take(5)->get();

        return Inertia::render('Dashboard', [
            'equipmentCount' => $equipmentCount,
            'latestEquipments' => $latestEquipments
        ]);
    }
}
