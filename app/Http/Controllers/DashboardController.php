<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesOrder;
use App\Models\SalesLine;
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

    /**
     * Live Database Inspector to view raw data from MSSQL
     */
    public function dbViewer()
    {
        $serverInfo = DB::select("SELECT @@SERVERNAME as server_name, DB_NAME() as db_name, @@VERSION as version")[0];

        $users = User::orderBy('user_id')->get();
        $customers = Customer::orderBy('customer_id')->get();
        $items = Item::orderBy('item_id')->get();
        $orders = SalesOrder::with(['customer', 'creator', 'lines'])->orderBy('order_id')->get();
        $lines = SalesLine::with(['order', 'item'])->orderBy('line_id')->get();

        return view('db_viewer', compact('serverInfo', 'users', 'customers', 'items', 'orders', 'lines'));
    }
}
