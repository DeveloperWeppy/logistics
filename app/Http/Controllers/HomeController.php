<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
   
    public function index()
    {
        $total_sales = Order::sum('total_amount');
        $total_sales_today = DB::table('orders')
        ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
        ->sum('total_amount');

        $order_picking = DB::table('orders')
        ->where('status', 1)
        ->count();

        $order_packing = DB::table('orders')
        ->where('status', 2)
        ->count();
        return view('dashboard', ['total_sales'=>$total_sales, 'total_sales_today'=>$total_sales_today, 'order_picking'=>$order_picking, 'order_packing'=>$order_packing]);
    }
}
