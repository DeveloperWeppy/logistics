<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\LastSyncInvoices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{   
    public function index()
    {
        $type="";
        if(isset($_GET['type'])){
            $type=$_GET['type'];              
        }
        return view('orders.index',['type'=>$type]);
    }
    public function get(Request $request,$type="")
    {
        $arrayStatus=['Procesando','Picking Realizado','Packing Realizado','Completado','Embalado','Etiquetado','Enviado',''];
        $search = $request->input('search.value');
        $query = Order::leftJoin('users', 'users.id', '=', 'orders.create_user_id')
        ->select('orders.id', 'orders.wc_order_id', 'orders.create_user_id', 'orders.billing', 'orders.wc_status', 'orders.status', 'orders.created_at', 'users.name as name_user');
        $type_s='<';
        if($type=="completed"){
            $type_s='=';
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('orders.id', 'like', '%'.$search.'%')
                    ->orWhere('orders.wc_status', 'like', '%'.$search.'%')
                    ->orWhere('orders.status', $type_s, 3);
            });
        } else {
            $query->where('orders.status', $type_s, 3);
        }
        //$l=$request->input('start') / $request->input('length') + 1;
        $l=1;
        $users = $query->paginate($request->input('length'), ['*'], 'page',1 );
        $count = $users->total();
        $data= $users->items();
        $rol=auth()->user()->getRoleNames()->first();
        for ($i=0;$i<count($data);$i++){
            if(($data[$i]['status']==0 && ($rol=="Picking" || $rol=="Admin" )) || ($data[$i]['status']==1  && ($rol=="Packing"  ||  $rol=="Admin"  )) ){
                $data[$i]['edit']='<a href="'.route('orders.create', $data[$i]['wc_order_id']).'"><i class="mdi mdi-checkbox-blank-outline"></i></a>';
            }
            if(($data[$i]['status']==1 && $rol=="Picking") || ($data[$i]['status']==2  && $rol=="Packing") ){
                $data[$i]['edit']='<a href="#" class="btn-no-check"><i class="mdi mdi-checkbox-marked-outline"></i></a>';
            }
            
            if($rol=="Despachador"){
                $data[$i]['edit']="";
            }
            if(!isset($data[$i]['edit'])){
                $data[$i]['edit']="";
            }
            if(($rol=="Admin" || $rol=="Delivery") && $data[$i]['status']==2){
               $data[$i]['edit']= $data[$i]['edit'].'<a href="#" class="btm-check" data="'.$data[$i]['id'].'"><i class="mdi mdi-checkbox-blank-outline"></i></a>';
            }
             if($type=="completed"){
               // $data[$i]['edit']= $data[$i]['edit'].'<a  href="'.route('orders.detail', $data[$i]['wc_order_id']).'" class="btm-check" data="'.$data[$i]['id'].'"><i class="mdi  mdi-checkbox-multiple-blank-outline"></i></a>';
               $data[$i]['edit']='<a href="#" class="btn-no-check"><i class="mdi mdi-checkbox-marked-outline"></i></a>';
            }
            $data[$i]['status_name']=$arrayStatus[ $data[$i]['status']];
            $customer=json_decode($data[$i]['billing'],true);
            if(isset($customer['first_name'],$customer['last_name'])){
                $data[$i]['customer']=$customer['first_name']." ".$customer['last_name'];
            }
           
        }
        
        return response()->json(['data'=>$data,'recordsTotal' => $count,'recordsFiltered' => $count]);
    }
    public function create($id)
    {
        $arrayStatus=['Procesando','Picking Realizado','Packing Realizado','Completado','Embalado','Etiquetado','Enviado',''];
        $data=$this->apiWc("orders/".$id);
        $data_items=[];
        $order = Order::where('wc_order_id',$id)->first();
        if (!$order) {
            $siigo_invoice_id="";
            foreach ($data['meta_data'] as $meta_data) {
                if($meta_data['key']=='_siigo_invoice_id'){
                   $siigo_invoice_id=$meta_data['value'];
                }
            }
            $order = Order::create([
                'wc_order_id' => $data['id'],
                'wc_status' => $data['status'],
                'shipping' => json_encode($data['shipping']),
                'billing' => json_encode($data['billing']),
                'line_items' =>json_encode($data['line_items']),
                'total_amount' =>$data['total'],
                'create_user_id' =>  auth()->user()->id,
                'picking_user_id'=>0,
                'siigo_invoice'=>$siigo_invoice_id,
                'status' => 0,
            ]);
        }
     
  
        for($i = 0; $i < count($data['line_items']); $i++) {
            $dataP=$this->apiWc("products/".$data['line_items'][$i]['product_id']);
            $images= $dataP['images'];
            $image=asset('assets/images/logo/logo-icon.png');
            if(count($images)>0){
                $image=$images[0]['src'];
            }
            $data_items[]=['sku'=> $data['line_items'][$i]['sku'],'image'=>$image, 'name'=>$dataP['name'],'id'=>$dataP['id'],'quantity'=>$data['line_items'][$i]['quantity'],'scann'=>0];
        }
        return view('orders.form',['title' =>'Agregar Orden','data'=>$data,'order'=>$order,'data_items'=>$data_items,'id'=> $order->id,'creador'=>$order->creatorUser(),'picking'=>$order->pickingUser(),'delivery'=>$order->deliveryUser,'status'=>$order->status,'status_name'=>$arrayStatus[$order->status]]);
    }
    public function store(Request $request,$id,$type=0)
    {
        $order = Order::findOrFail($id);
        $currentDateTime = date('Y-m-d H:i:s');
        if($type==1){
            $order->finalized_user_id= auth()->user()->id;
            $order->status=3;
            $order->date_delivery=$currentDateTime;
            $order->tracking_code= $request->input('cod');
            $siigo_invoice_id=$order->siigo_invoice;
            if($siigo_invoice_id==""){
                $data=$this->apiWc("orders/".$order->wc_order_id);
                foreach ($data['meta_data'] as $meta_data) {
                    if($meta_data['key']=='_siigo_invoice_id'){
                       $siigo_invoice_id=$meta_data['value'];
                    }
                }
            }
            $this->siigoEnviar($siigo_invoice_id);
            $data=$this->apiWc("orders/".$order->wc_order_id,["status"=>"completed"]);

        }else{
            if($order->status==1){
                //paquin
                //$order->status=2;
                $order->finalized_user_id= auth()->user()->id;
                $order->status=3;
                $order->date_packing=$currentDateTime;
                $order->date_delivery=$currentDateTime;
                $order->packing_user_id=auth()->user()->id;
                $siigo_invoice_id=$order->siigo_invoice;
                if($siigo_invoice_id==""){
                    $data=$this->apiWc("orders/".$order->wc_order_id);
                    foreach ($data['meta_data'] as $meta_data) {
                        if($meta_data['key']=='_siigo_invoice_id'){
                           $siigo_invoice_id=$meta_data['value'];
                        }
                    }
                }
                $this->siigoEnviar($siigo_invoice_id);
                $data=$this->apiWc("orders/".$order->wc_order_id,["status"=>"completed"]);
            }else{
                $order->status=1;
                $order->date_picking=$currentDateTime;
                $order->picking_user_id=auth()->user()->id;
            }
        }
        $order->save();
        return response()->json(['status' => true]); 
    }
    public function detail($id)
    { 
        $order = Order::findOrFail($id);
        $data=$order;
        $data->shipping= json_decode($order->shipping);
        $data->billing=json_decode($order->billing);
        $data->line_items=json_decode($order->line_items);
        for($i = 0; $i < count($data->line_items); $i++) {
            $dataP=$this->apiWc("products/".$data['line_items'][$i]['product_id']);
            $images= $dataP['images'];
            $image=asset('assets/images/logo/logo-icon.png');
            if(count($images)>0){
                $image=$images[0]['src'];
            }
            $data_items[]=['sku'=> $dataP['sku'],'image'=>$image, 'name'=>$dataP['name'],'id'=>$dataP['id'],'quantity'=>$data['line_items'][$i]['quantity'],'scann'=>0];
        }
        return view('orders.detail',['order'=> $data]);
    }
    public function estadistica()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $startOfMonth = Carbon::create($currentYear, $currentMonth)->subMonths(6)->startOfMonth();
        $endOfMonth = Carbon::create($currentYear, $currentMonth)->endOfMonth();
    
        $query = Order::select('id', 'created_at', 'date_delivery', 'status')
            ->where('status', 3)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('created_at', 'asc')
            ->get();
        
        $promedioPorMes = [];
        
        foreach ($query as $index => $order) {
            $month = Carbon::parse($order->created_at)->format('M');
            $createdDate = Carbon::parse($order->created_at);
            $deliveryDate = Carbon::parse($order->date_delivery);
            $differenceInSeconds = $createdDate->diffInSeconds($deliveryDate);
            $differenceInMinutes = $differenceInSeconds / 60;
            if (!isset($promedioPorMes[$month])) {
                $promedioPorMes[$month] = [];
            }
            $promedioPorMes[$month][] = $differenceInMinutes;
        }
        
        $promedioFinal = [];
        
        $allMonths = Carbon::now()->subMonths(6)->monthsUntil(Carbon::now()->endOfMonth());
        
        foreach ($allMonths as $month) {
            $monthLabel = $month->format('M');
            if (isset($promedioPorMes[$monthLabel])) {
                $promedio = round(array_sum($promedioPorMes[$monthLabel]) / count($promedioPorMes[$monthLabel]));
            } else {
                $promedio = 0;
            }
            $promedioFinal[$monthLabel] = $promedio;
        }
        $data=[];
        $cateogories=[];
        foreach ($promedioFinal as $month => $promedio) {
            array_push($cateogories, $month);
            array_push($data, $promedio);
        }
        return response()->json( ['data'=>$data,'cateogories'=>$cateogories]);
    }
    public function prueba()
    {
        echo "<br>-----";
        $id=256276;
        $order = Order::where('wc_order_id',$id)->first();
        $siigo_invoice_id=$order->siigo_invoice;
        if($siigo_invoice_id==""){
            $data=$this->apiWc("orders/".$id);
            foreach ($data['meta_data'] as $meta_data) {
                if($meta_data['key']=='_siigo_invoice_id'){
                   $siigo_invoice_id=$meta_data['value'];
                }
            }
        }
      
        $this->siigoEnviar($siigo_invoice_id);
    }
    public function siigoEnviar($siigo_invoice_id)
    {
        $value = session('siigo_data');
        //Order::truncate();
        if ($value) {
            $expirationTime = Carbon::parse($value)->addHours(12);
            $currentTime = Carbon::now();
            if ($expirationTime > $currentTime) {
    
            } else {
                $auth = $this->apiSiigo("auth");
            }
        } else {
            $auth = $this->apiSiigo("auth");
        }
        $invoice=$this->apiSiigo("invoices/".$siigo_invoice_id);
        if(isset($invoice['stamp']['status'])){
              if($invoice['stamp']['status']=="Draft"){
                    $data=[
                      "document"=>$invoice['document'],
                      "date"=>$invoice['date'],
                      "customer"=>$invoice['customer'],
                      "items"=>$invoice['items'],
                      "payments"=>$invoice['payments'],
                      "seller"=>$invoice['seller'],
                      "stamp"=>[ "send"=> true ],
                      "mail"=>["send"=> true],     
                    ];
                    $invoice=$this->apiSiigo("invoices/".$siigo_invoice_id,$data,"put");
              }
        }
        return true;
    }

    public function get_orders()
    {
        return view('orders.indexnew');

    }

    public function get_orders_datatable()
    {
        try {
            $authorization = base64_encode(env('API_WOOCOMMERCE_USER') . ':' . env('API_WOOCOMMERC_PASSWORD'));
    
            $headers = [
                'Authorization' => 'Basic ' . $authorization,
                'Cookie' => 'database_validation=1; mailpoet_page_view=%7B%22timestamp%22%3A1686054310%7D',
            ];
            $response = Http::withHeaders($headers)->get('https://natylondon.com/wp-json/wc/v3/orders');

            if ($response->successful()) {
                $orders = json_decode($response->body(), true);

                // estados a filtrar
                $desiredStatuses = ["processing", "addi-approved"];

                // Filtra las órdenes por estado
                $filteredOrders = array_filter($orders, function ($order) use ($desiredStatuses) {
                    return in_array($order["status"], $desiredStatuses);
                });

                // Ahora, $filteredOrders contiene solo las órdenes con estado "processing" o "addi-approved"
                // Puedes trabajar con este array filtrado según tus necesidades.
                dd($filteredOrders);
            }else{
                dd( 'false api'. $response) ;
            }
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
        // $data=$this->apiWc("orders");

        // dd($data);

    }

    public function sync_invoices()
    {
        try {
            // Obtén la fecha y hora de la última sincronización exitosa
            $lastSync = LastSyncInvoices::first(); // Suponiendo que LastSync es el modelo para tu tabla auxiliar
            $day = date("Y-m-d");
            $authorization = base64_encode(env('API_WOOCOMMERCE_USER') . ':' . env('API_WOOCOMMERC_PASSWORD'));
    
            $headers = [
                'Authorization' => 'Basic ' . $authorization,
                'Cookie' => 'database_validation=1; mailpoet_page_view=%7B%22timestamp%22%3A1686054310%7D',
            ];
            $response = Http::withHeaders($headers)->get('https://natylondon.com/wp-json/wc/v3/orders');

            if ($response->successful()) {
                $orders = json_decode($response->body(), true);

                // estados a filtrar
                $desiredStatuses = ["processing", "addi-approved"];

                // Filtra las órdenes por estado
                $filteredOrders = array_filter($orders, function ($order) use ($desiredStatuses) {
                    return in_array($order["status"], $desiredStatuses);
                });
                $totalInvoicesresults= 0;
                // Ahora, $filteredOrders contiene solo las órdenes con estado "processing" o "addi-approved"
                // Puedes trabajar con este array filtrado según tus necesidades.
                foreach ($filteredOrders as $key => $invoice) {
                    $totalInvoicesresults++;
                    $createdTimestamp = strtotime($invoice['date_created']);
                    if (!$lastSync || $createdTimestamp > Carbon::parse($lastSync->last_register)->timestamp) {
                        $siigo_invoice_id="";
                        foreach ($invoice['meta_data'] as $meta_data) {
                            if($meta_data['key']=='_siigo_invoice_id'){
                            $siigo_invoice_id=$meta_data['value'];
                            }
                        }
                        $order = Order::create([
                            'wc_order_id' => $invoice['id'],
                            'wc_status' => $invoice['status'],
                            'shipping' => json_encode($invoice['shipping']),
                            'billing' => json_encode($invoice['billing']),
                            'line_items' =>json_encode($invoice['line_items']),
                            'total_amount' =>$invoice['total'],
                            'create_user_id' =>  auth()->user()->id,
                            'picking_user_id'=>0,
                            'siigo_invoice'=>$siigo_invoice_id,
                            'status' => 0,
                        ]);
                    }
                }
                // Actualiza la fecha y hora de la última sincronización exitosa
                if (!$lastSync) {
                    $lastSync = new LastSyncInvoices();
                }

                $lastSync->last_register = now();
                $lastSync->save();
                Log::info('Cantidad de facturas: ' . $totalInvoicesresults);
                $error = false;
                $mensaje = 'Exitoso';
            }else{
                dd( 'false api'. $response);
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error en la sincronización de facturas: ' . $th->getMessage());
        }
        return response()->json(['error' => $error, 'mensaje' => $mensaje]);
    }
}
