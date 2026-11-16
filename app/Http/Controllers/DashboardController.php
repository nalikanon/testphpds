<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = SalesOrder::count();
        $totalCustomers = Customer::count();
        $totalItems = Item::count();
        $totalUsers = User::count();

        $draftOrders = SalesOrder::where('status', 'draft')->count();
        $confirmedOrders = SalesOrder::where('status', 'confirmed')->count();
        $cancelledOrders = SalesOrder::where('status', 'cancelled')->count();

        $confirmedSalesAmount = SalesOrder::where('status', 'confirmed')->sum('grand_total');

        $recentOrders = SalesOrder::with(['customer', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'totalOrders',
            'totalCustomers',
            'totalItems',
            'totalUsers',
            'draftOrders',
            'confirmedOrders',
            'cancelledOrders',
            'confirmedSalesAmount',
            'recentOrders'
        ));
    }
}

