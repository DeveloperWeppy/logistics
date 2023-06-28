<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\User;
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
        $order_complet = DB::table('orders')
        ->where('status', 3)
        ->count();
        $count = Order::count();
        $picking_g = $this->estadistica(1);
        $packing_g = $this->estadistica(2);
        $delivery_g = $this->estadistica(3);

        $picking_user= $this->estadistica(1,1);
        $packing_user= $this->estadistica(2,2);
        $delivery_user= $this->estadistica(3,3);
        return view('dashboard', ['total_sales'=>$count,'total_sales_today'=>$total_sales_today,'picking_user'=> $picking_user,'packing_user'=> $packing_user,'delivery_user'=> $delivery_user,'order_complet'=>$order_complet, 'order_picking'=>$order_picking, 'order_packing'=>$order_packing,'picking_g'=>$picking_g,'packing_g'=>$packing_g,'delivery_g'=>$delivery_g]);
    }
    public function estadistica($status = 0, $rol = 0)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $startOfMonth = Carbon::create($currentYear, $currentMonth)->subMonths(6)->startOfMonth();
        $endOfMonth = Carbon::create($currentYear, $currentMonth)->endOfMonth();
        
        if ($rol == 0) {
            $query = Order::select('id', 'create_user_id', 'date_picking', 'picking_user_id', 'date_packing', 'packing_user_id', 'finalized_user_id', 'created_at', 'date_delivery', 'status')
                ->where('status', $status)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $query = Order::select('id', 'create_user_id', 'date_picking', 'picking_user_id', 'date_packing', 'packing_user_id', 'finalized_user_id', 'created_at', 'date_delivery', 'status')
                ->where('status', $status)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderBy('created_at', 'asc')
                ->get();
        }
        $promedioPorMes = [];
        $arrayUser = [];
        $arrayUserMes = [];
        foreach ($query as $index => $order) {
            $user = null;
            
            if ($rol == 1) {
                $user = User::find($order->picking_user_id);
            } elseif ($rol == 2) {
                $user = User::find($order->packing_user_id);
            } elseif ($rol == 3) {
                $user = User::find($order->finalized_user_id);
            }
    
            $month = Carbon::parse($order->created_at)->format('M');

            $createdDate = null;
            $deliveryDate = null;

            if ($status == 1) {
                $createdDate = Carbon::parse($order->created_at);
                $deliveryDate = Carbon::parse($order->date_picking);
            } elseif ($status == 2) {
                $createdDate = Carbon::parse($order->date_picking);
                $deliveryDate = Carbon::parse($order->date_packing);
            } elseif ($status == 3) {
                $createdDate = Carbon::parse($order->created_at);
                $deliveryDate = Carbon::parse($order->date_delivery);
            }
            if ($createdDate !== null && $deliveryDate !== null) {
                $differenceInSeconds = $createdDate->diffInSeconds($deliveryDate);
                $differenceInMinutes = $differenceInSeconds / 60;
                if ($user !== null){
                    $userName = $user->name . " " . $user->last_name;
                    if (!in_array($userName, $arrayUser)) {
                        array_push($arrayUser, $userName);
                        $arrayUserMes[$userName]=[];
                    }
                    if (!isset($arrayUserMes[$userName][$month])) {
                        $arrayUserMes[$userName][$month] = [];
                    }
                    $arrayUserMes[$userName][$month][] = $differenceInMinutes;
                }
                if($rol==0){
                    if (!isset($promedioPorMes[$month])) {
                        $promedioPorMes[$month] = [];
                    }
                    $promedioPorMes[$month][] = $differenceInMinutes;
                }
            }
        }
        $promedioFinal = [];
        
        $allMonths = Carbon::now()->subMonths(6)->monthsUntil(Carbon::now()->endOfMonth());
       
        foreach ($arrayUser as $usern) {
            $data2=[];
            foreach ($allMonths as $month) {
                $monthLabel = $month->format('M');
                if (isset($arrayUserMes[$usern][$monthLabel])) {
                    $promedio = round(array_sum($arrayUserMes[$usern][$monthLabel]) / count($arrayUserMes[$usern][$monthLabel]));
                } else {
                    $promedio = 0;
                }
                array_push($data2,$promedio);
            }
            array_push($promedioFinal, ['name'=>$usern,'data'=>$data2]);
        }
        $data=[];
        $cateogories=[];
        if($rol==0){
            foreach ($allMonths as $month) {
                $monthLabel = $month->format('M');
                if (isset($promedioPorMes[$monthLabel])) {
                    $promedio = round(array_sum($promedioPorMes[$monthLabel]) / count($promedioPorMes[$monthLabel]));
                } else {
                    $promedio = 0;
                }
                $promedioFinal[$monthLabel] = $promedio;
            }

            foreach ($promedioFinal as $month => $promedio) {
                array_push($data, $promedio);
            }
        }else{
            foreach ($allMonths as $month) {
                $monthLabel = $month->format('M');
                array_push($cateogories, $monthLabel);
            }
        }
        if ($rol == 0) {
            return ['data'=>$data,'categories'=>$cateogories];
        }else{
            return ['data'=>$promedioFinal,'categories'=>$cateogories,'user'=>$arrayUser];
        }
       
    }
}
