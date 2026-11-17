<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesLine;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $ordersQuery = SalesOrder::query()->visibleTo($user);

        $totalOrders = (clone $ordersQuery)->count();
        $totalCustomers = Customer::count();
        $totalItems = Item::count();

        $draftOrders = (clone $ordersQuery)->where('status', 'draft')->count();
        $confirmedOrders = (clone $ordersQuery)->where('status', 'confirmed')->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', 'cancelled')->count();

        $confirmedSalesAmount = (clone $ordersQuery)->where('status', 'confirmed')->sum('grand_total');

        $recentOrders = (clone $ordersQuery)->with(['customer', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'totalOrders',
            'totalCustomers',
            'totalItems',
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
        $serverInfo = DB::select('SELECT @@SERVERNAME as server_name, DB_NAME() as db_name, @@VERSION as version')[0];

        $users = User::orderBy('user_id')->get();
        $customers = Customer::orderBy('customer_id')->get();
        $items = Item::orderBy('item_id')->get();
        $orders = SalesOrder::with(['customer', 'creator', 'lines'])->orderBy('order_id')->get();
        $lines = SalesLine::with(['order', 'item'])->orderBy('line_id')->get();

        return view('db_viewer', compact('serverInfo', 'users', 'customers', 'items', 'orders', 'lines'));
    }
}
