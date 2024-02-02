<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Dompdf\Dompdf;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode;
use App\Models\LastSyncInvoices;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
//use TCPDF;

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

    public function pruebaOrders()
    {
        $consumer_key = env('API_WOOCOMMERCE_USER');
        $consumer_secret = env('API_WOOCOMMERC_PASSWORD');
        $authorization = base64_encode(env('API_WOOCOMMERCE_USER') . ':' . env('API_WOOCOMMERC_PASSWORD'));
        $headers = [
            'Authorization' => 'Basic ' . $authorization,
            'Cookie' => 'database_validation=1; mailpoet_page_view=%7B%22timestamp%22%3A1686054310%7D',
        ];
        //$url = 'https://natylondon.com/wp-json/wc/v3/orders/';
        $url = 'https://natylondon.com/wp-json/wc/v3/orders?consumer_key='.$consumer_key.'&consumer_secret='.$consumer_secret.'&status=processing&per_page=50';
        $params = [
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret,
            'status' => 'processing', // Comma-separated list of statuses
            'per_page' => 100,
        ];
                $response = Http::withHeaders($headers)->get($url,$params);
        dd(json_decode($response->body(), true));
        if ($response->status() == 200) {
            $orders = json_decode($response->body(), true);

            // estados a filtrar
            $desiredStatuses = ["processing", "addi-approved"];

            // Filtra las órdenes por estado
            $filteredOrders = array_filter($orders, function ($order) use ($desiredStatuses) {
                return in_array($order["status"], $desiredStatuses);
            });

            
        }else if($response->status() == 403){
            dd($response);
        }
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
        $type="";
        if(isset($_GET['type'])){
            $type=$_GET['type'];              
        }
        return view('orders.indexnew',['type'=>$type]);

    }
    public function get_orders_completed()
    {
        $type="";
        if(isset($_GET['type'])){
            $type=$_GET['type'];              
        }
        return view('orders.indexnewcomplete',['type'=>$type]);

    }

    public function get_orders_datatable(Request $request)
    {
       $rutaReferente = $request->headers->get('referer');

       // Obtiene la ruta relativa
       $rutaRelativa = parse_url($rutaReferente, PHP_URL_PATH);

       //URL base
       $urlBase = '/orders/web';

       $arrayStatus=['En cola','Picking Realizado','Packing Realizado','Completado','Embalado','Etiquetado','Enviado',''];
    
        $query = Order::leftJoin('users', 'users.id', '=', 'orders.create_user_id')
        ->select('orders.id', 'orders.wc_order_id', 'orders.create_user_id', 'orders.billing','orders.payment_method', 'orders.wc_status', 'orders.total_amount', 'orders.status', 'orders.date_paid', 'users.name as name_user');
        
        // Se aplica la búsqueda global a wc_order_id, city
        $query->where(function ($query) use ($request) {
            $searchValue = $request->input('search')['value'];
        
            $query->where('orders.wc_order_id', 'like', '%' . $searchValue . '%')
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(orders.billing, '$.city')) LIKE '%$searchValue%'");
        });
        // Compara la ruta relativa con la URL base
       if ($rutaRelativa == $urlBase) {
            $data = $query->where('orders.status', '<>', 3)->get();
        } else {
            $data = $query->where('orders.status', 3)->get();
        }
        
        //$data= $query->get();
        $datos = array();
        $rol=auth()->user()->getRoleNames()->first();
        for ($i=0;$i<count($data);$i++){
            $datos[$i]['edit'] = '';
            if(($data[$i]['status']==0 && ($rol=="Picking" || $rol=="Admin" )) || ($data[$i]['status']==1  && ($rol=="Packing"  ||  $rol=="Admin"  )) ){
                $datos[$i]['edit'] = '<a href="'.route('orders.create', $data[$i]['wc_order_id']).'"><i class="mdi mdi-tooltip-edit"></i></a>';
            }
            if(($data[$i]['status']==1 && $rol=="Picking") || ($data[$i]['status']==2  && $rol=="Packing") ){
                $datos[$i]['edit'] .='<a href="#" class="btn-no-check"><i class="mdi mdi-checkbox-marked-outline"></i></a>';
            }
            
            if($rol=="Despachador"){
                $datos[$i]['edit'] .="";
            }
            // if(!isset($data[$i]['edit'])){
            //     $datos[$i]['edit']="";
            // }
            if(($rol=="Admin" || $rol=="Delivery") && $data[$i]['status']==2){
               $datos[$i]['edit'] .= '<a href="#" class="btm-check" data="'.$data[$i]['id'].'"><i class="mdi mdi-checkbox-blank-outline"></i></a>';
            }
             if($data[$i]['status']==3){
               // $data[$i]['edit']= $data[$i]['edit'].'<a  href="'.route('orders.detail', $data[$i]['wc_order_id']).'" class="btm-check" data="'.$data[$i]['id'].'"><i class="mdi  mdi-checkbox-multiple-blank-outline"></i></a>';
               $datos[$i]['edit']='<a href="#" class="btn-no-check"><i class="mdi mdi-checkbox-marked-outline"></i></a>';
            }
            $datos[$i]['status_name']=$arrayStatus[ $data[$i]['status']];
            
            $customer=json_decode($data[$i]['billing'],true);
            if(isset($customer['first_name'],$customer['last_name'])){
                $datos[$i]['customer']=$customer['first_name']." ".$customer['last_name'];
            }
            if(stripos($customer['city'], 'cali') !== false){
                $datos[$i]['edit'] .= '<a class="" target="_blank" href="'.route('orders.pdf', ['idOrder' => $data[$i]['wc_order_id']]).'"> <i class="mdi mdi-file-pdf"></i></a>';

            }

            $fecha_hora = date('d/m/Y h:i A', strtotime($data[$i]['date_paid']));
            $payment_methid = $data[$i]['payment_method'] == 'Paga a cuotas' ? 'Addi' : $data[$i]['payment_method'];
            //$qr = '<td style="display:flex;justify-content:center;"><a class="" href="'.get_site_url().'/wp-json/picking-weppy/order/qr?id='.$pedido->get_id().'"><i class="mdi mdi-qrcode"></i></a></td>';
            $qr = '<td style="display:flex;justify-content:center;"><a class="" target="_blank" href="'.route('orders.qr', ['id' => $data[$i]['wc_order_id']]).'"> <i class="mdi mdi-qrcode"></i></a></td>';
            $invoiceSiigo = '<td style="display:flex;justify-content:center;"><a class="" target="_blank" href="'.route('orders.invoicesiigo', ['id_order' => $data[$i]['id']]).'"> <i class="mdi mdi-clipboard-text"></i></a></td>';

            $datos[$i]['phone']= $customer['phone'];
            $datos[$i]['city']= $customer['city'];
            $datos[$i]['payment_method']= $payment_methid;
            $datos[$i]['total_amount']= number_format($data[$i]['total_amount'], 2, '.', ',');
            $datos[$i]['city']= $customer['city'];
            $datos[$i]['date']= $fecha_hora;
            $datos[$i]['wc_order_id']= $data[$i]['wc_order_id'];
            $datos[$i]['siigo_invoice']= $invoiceSiigo;

    
            // Almacena la ruta al código QR en tus datos
            $datos[$i]['qr'] = $qr;
        }
        $totalRecords = count($datos); // Obtener la cantidad total de registros

        $filteredRecords = $totalRecords; // En este ejemplo, asumimos que no se realiza filtrado

        $start = $request->input('start');
        $length = $request->input('length');

        // Paginación
        $datos = array_slice($datos, $start, $length);
        return response()->json(['data'=>$datos, 'recordsTotal' => $totalRecords,'recordsFiltered' => $filteredRecords]);
    }

    public function sync_invoices()
    {
        $error = false;
        $mensaje = '';
        try {
            // Obtén la fecha y hora de la última sincronización exitosa
            $lastSync = LastSyncInvoices::first(); // Suponiendo que LastSync es el modelo para tu tabla auxiliar
            //$day = date("Y-m-d");
            $authorization = base64_encode(env('API_WOOCOMMERCE_USER') . ':' . env('API_WOOCOMMERC_PASSWORD'));
            $consumer_key = env('API_WOOCOMMERCE_USER');
            $consumer_secret = env('API_WOOCOMMERC_PASSWORD');
            $headers = [
                'Authorization' => 'Basic ' . $authorization,
                'Cookie' => 'database_validation=1; mailpoet_page_view=%7B%22timestamp%22%3A1686054310%7D',
            ];
            
            $url = 'https://natylondon.com/wp-json/wc/v3/orders?consumer_key='.$consumer_key.'&consumer_secret='.$consumer_secret.'&status=processing&per_page=100';
            $params = [
                'consumer_key' => $consumer_key,
                'consumer_secret' => $consumer_secret,
                'status' => 'processing', // 
                'per_page' => 100,
            ];
            $response = Http::withHeaders($headers)->get($url, $params);
            //$response = Http::get('https://natylondon.com/wp-json/wc/v3/orders?consumer_key='.$consumer_key.'&consumer_secret='.$consumer_secret);
            
            if ($response->status() == 200) {
                $orders = json_decode($response->body(), true);

                // estados a filtrar
                //$desiredStatuses = ["processing", "addi-approved"];

                // Filtra las órdenes por estado
                // $filteredOrders = array_filter($orders, function ($order) use ($desiredStatuses) {
                //     return in_array($order["status"], $desiredStatuses);
                // });
                $totalInvoicesresults= 0;
                // Ahora, $filteredOrders contiene solo las órdenes con estado "processing" o "addi-approved"
                // Puedes trabajar con este array filtrado según tus necesidades.
                foreach ($orders as $key => $invoice) {
                    
                    $createdTimestamp = strtotime($invoice['date_created']);
                    $modifiedTimestamp = strtotime($invoice['date_paid']);

                    //if (!$lastSync || $createdTimestamp > Carbon::parse($lastSync->last_register, 'America/Bogota')->timestamp) {
                        if((!$lastSync || $createdTimestamp > Carbon::parse($lastSync->last_register, 'America/Bogota')->timestamp)
                            && ($createdTimestamp > Carbon::parse($lastSync->last_register, 'America/Bogota')->timestamp || $modifiedTimestamp > Carbon::parse($lastSync->last_register, 'America/Bogota')->timestamp)){
                            // Verifica si ya existe un pedido con el mismo wc_order_id
                            $existingOrder = Order::where('wc_order_id', $invoice['id'])->first();

                            if (!$existingOrder) {
                                $totalInvoicesresults++;
                                $siigo_invoice_id="";
                                $cedula = ""; 
                                foreach ($invoice['meta_data'] as $meta_data) {
                                    if($meta_data['key']=='_siigo_invoice_id'){
                                    $siigo_invoice_id=$meta_data['value'];
                                    }
                                    if ($meta_data['key'] == 'cedula') {
                                        $cedula = $meta_data['value'];
                                    }
                                }
                                 $customer_note = $invoice['customer_note'] ? $invoice['customer_note'] : 'Sin nota';
                                // Convertir la cadena de fecha a un objeto DateTime
                                $timestamp = Carbon::parse($invoice['date_paid'], 'America/Bogota');
                                // Agregar "cedula" al arreglo "billing"
                                $invoice['billing']['document_number'] = $cedula;
                                $invoice['billing']['customer_note'] = $customer_note;
                                $order = Order::create([
                                    'wc_order_id' => $invoice['id'],
                                    'payment_method' => $invoice['payment_method_title'], 
                                    'id_transaction_payment' => $invoice['transaction_id'],
                                    'wc_status' => $invoice['status'],
                                    'shipping' => json_encode($invoice['shipping']),
                                    'billing' => json_encode($invoice['billing']),
                                    'line_items' =>json_encode($invoice['line_items']),
                                    'total_amount' =>$invoice['total'],
                                    'create_user_id' =>  auth()->user()->id,
                                    'picking_user_id'=>0,
                                    'siigo_invoice'=>$siigo_invoice_id,
                                    'status' => 0,
                                    'date_paid'  => $timestamp
                                ]);
                            }else{
                                Log::info('Pedido existente con wc_order_id: ' . $invoice['id']);
                            }
                        
                        
                    }
                }
                // Ejecutar la segunda función
                try {
                    Log::info('Antes de processAdditionalOrders');
                    $this->processAdditionalOrders($consumer_key, $consumer_secret, $lastSync->last_register);
                    Log::info('Después de processAdditionalOrders');
                } catch (\Throwable $th) {
                    Log::error('Excepción en processAdditionalOrders: ' . $th->getMessage());
                }
                                
                // Actualiza la fecha y hora de la última sincronización exitosa
                if (!$lastSync) {
                    $lastSync = new LastSyncInvoices();
                }

                $lastSync->last_register = now()->setTimezone('America/Bogota');
                $lastSync->save();

                Log::info('Cantidad de facturas processing: ' . $totalInvoicesresults);
                $error = false;
                $mensaje = 'Exitoso';
            }else{
                $error = true;
                $mensaje = 'Error al procesar facturas  '.$response->throw()->json();
                Log::error( 'false api'. $response->throw()->json());
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error en la sincronización de facturas: ' . $th->getMessage());
        }
        return response()->json(['error' => $error, 'mensaje' => $mensaje]);
    }

    private function processAdditionalOrders($consumer_key, $consumer_secret, $last_register)
    {
        try {
            $authorization = base64_encode($consumer_key . ':' . $consumer_secret);
            $headers = [
                'Authorization' => 'Basic ' . $authorization,
                'Cookie' => 'database_validation=1; mailpoet_page_view=%7B%22timestamp%22%3A1686054310%7D',
            ];
    
            $page = 1;
            do {
                $url = 'https://natylondon.com/wp-json/wc/v3/orders?consumer_key=' . $consumer_key . '&consumer_secret=' . $consumer_secret . '&page=' . $page;
                $response = Http::withHeaders($headers)->get($url);
    
                if ($response->status() == 200) {
                    $additionalOrders = json_decode($response->body(), true);
    
                    // Verifica si la respuesta es un array antes de aplicar array_filter
                    if (is_array($additionalOrders)) {
                        $desiredStatuses = ["addi-approved"];
    
                        $filteredOrders = array_filter($additionalOrders, function ($order) use ($desiredStatuses) {
                            return in_array($order["status"], $desiredStatuses);
                        });
    
                        $totalInvoicesresults = 0;
    
                        foreach ($filteredOrders as $key => $invoice) {
                            $createdTimestamp = strtotime($invoice['date_created']);
                            $modifiedTimestamp = strtotime($invoice['date_paid']);
    
                            if ((!$last_register || $createdTimestamp > Carbon::parse($last_register, 'America/Bogota')->timestamp)
                                && ($createdTimestamp > Carbon::parse($last_register, 'America/Bogota')->timestamp || $modifiedTimestamp > Carbon::parse($last_register, 'America/Bogota')->timestamp)) {
                           
                                // Verifica si ya existe un pedido con el mismo wc_order_id
                                $existingOrder = Order::where('wc_order_id', $invoice['id'])->first();

                                if (!$existingOrder) {
                                    $totalInvoicesresults++;
                                    $siigo_invoice_id = "";
                                    $cedula = ""; 
        
                                    foreach ($invoice['meta_data'] as $meta_data) {
                                        if ($meta_data['key'] == '_siigo_invoice_id') {
                                            $siigo_invoice_id = $meta_data['value'];
                                        }
                                        if ($meta_data['key'] == 'cedula') {
                                            $cedula = $meta_data['value'];
                                        }
                                    }
                                    
                                    $customer_note = $invoice['customer_note'] ? $invoice['customer_note'] : 'Sin nota';
                                    $timestamp = Carbon::parse($invoice['date_paid'], 'America/Bogota');
                                    $invoice['billing']['document_number'] = $cedula;
                                    $invoice['billing']['customer_note'] = $customer_note;
        
                                    $order = Order::create([
                                        'wc_order_id' => $invoice['id'],
                                        'payment_method' => $invoice['payment_method_title'], 
                                        'id_transaction_payment' => $invoice['transaction_id'],
                                        'wc_status' => $invoice['status'],
                                        'shipping' => json_encode($invoice['shipping']),
                                        'billing' => json_encode($invoice['billing']),
                                        'line_items' =>json_encode($invoice['line_items']),
                                        'total_amount' =>$invoice['total'],
                                        'create_user_id' =>  auth()->user()->id,
                                        'picking_user_id'=>0,
                                        'siigo_invoice'=>$siigo_invoice_id,
                                        'status' => 0,
                                        'date_paid'  => $timestamp,
                                    ]);
                                }else{
                                    Log::info('Pedido existente con wc_order_id: ' . $invoice['id']);
                                }
                                
                            }
                        }
    
                        $page++; // Incrementa el número de página para obtener la siguiente página de resultados
    
                    } else {
                        Log::error('La respuesta no es un array válido.');
                        break; // Sale del bucle si la respuesta no es válida
                    }
                } else {
                    Log::error('Error al obtener pedidos adicionales: ' . $response->throw()->json());
                    break; // Sale del bucle si hay un error en la solicitud
                }
            } while (!empty($additionalOrders)); // Continúa el bucle mientras haya más resultados
    
            Log::info('Cantidad total de facturas Addi: ' . $totalInvoicesresults);
    
        } catch (\Throwable $th) {
            Log::error('Error al procesar pedidos adicionales: ' . $th->getMessage());
        }
    }

    // public function qr($params=[]) {
    //     $options = new QROptions(
    //         [
    //           'eccLevel' => QRCode::ECC_L,
    //           'outputType' => QRCode::OUTPUT_MARKUP_SVG,
    //           'version' => 5,
    //         ]
    //       );
    //     if(!isset($params['id'])){
    //        return ["status"=>false]; 
    //     }
    //     $repetir=1;
    //     if(isset($params['repetir'])){
    //         $repetir=$params['repetir'];
    //      }
    //    $order_ids = explode(",", urldecode(rawurldecode($params['id'])));
    //    //print_r($order_ids);
		
    //     $html="";
	// 	$dd="";
	// 	$logo=base64_encode(file_get_contents("https://natylondon.com/wp-content/uploads/2022/06/LOGO-NATY-LONDON-sin-fondo-1.jpg"));
    //     $logo ='data:image/jpeg;base64,'.$logo;
    //     for ($i = 0; $i <count($order_ids); $i++) {
    //         $order = wc_get_order(trim($order_ids[$i]));
    //         $qrcode = (new QRCode($options))->render(get_site_url()."/".$order->get_id());
    //         $first_name = $order->get_billing_first_name()." ".$order->get_billing_last_name();
    //         $first_name=strlen($first_name) > 10? substr($first_name, 0, 10) : $first_name;
    //         $customer_id = $customer->ID;
    //         $identification= get_post_meta( $order->get_id(), '_billing_document_number', true ) ? get_post_meta( $order->get_id(), '_billing_document_number', true ) : (get_post_meta( $order->get_id(), 'cedula', true )?get_post_meta( $order->get_id(), 'cedula', true ):0);
    //         //$chtml=$this->views("qr",['image'=>$qrcode,'logo'=>$logo,'id'=>$order->get_id()]);
	// 	     $html.='<table style="width: 100%; height:90%;">
	// 		  <tr style="height: 100%;width: 100%;padding:0px;margin:0px;">
	// 			<td style="width:50%;height:87%;padding:0px;margin:0px;position: absolute;z-index:105"> 
	// 				<img class="qr-image" src="'.$qrcode.'" style="width:98.3%;heigth:auto;margin-top:-2.5px;z-index:-15">
	// 			 </td>
	// 			<td style="width:49%;height:50%;padding:0px;margin:0px;">
	// 				<img class="qr-image" src="'.$logo.'" style="width:0%;heigth:auto;margin-top:-11px;margin-left:-12px;">
                    
	// 				<div style="font-size:6px;position: absolute;margin-top:1px;right:24px;font-weight: bold;">ID:'.$order->get_id().'</div>
    //                 <div style="font-size:6px;margin-top:-5px;font-weight: bold;">D:'.$identification.'</div>
    //                 <div style="font-size:6px;font-weight: bold;">'.$first_name.'</div>
    //                 <div style="font-size:6px;font-weight: bold;">'.$order->get_billing_phone().'</div>
    //                 <div style="font-size:6px;font-weight: bold;">$'.number_format($order->get_total(),2).'</div>
    //                 <div style="font-size:6px;font-weight: bold;">'.$order->get_payment_method().'</div>

                   
	// 			</td>
	// 		  </tr>
	// 		</table>';
    //     }
		
    //     getPdf($html);
    //     exit();
        
    // }

    public function getinvoiceSiigo($idOrder)
    {
        $order = Order::find($idOrder);
        $invoice_siigo = $order->siigo_invoice;
        $token_siigo = '';
        //Order::truncate();

        if (Session::has('accessToken') && !empty(Session::get('accessToken'))) {
            $token_siigo = Session::get('accessToken');
        } else {
            $token_siigo = $this->auth_siigo();
        }
        
        try {
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token_siigo,
                'Content-Type' => 'application/json',
                'Partner-Id' => 'IntegrationWeppy',
            ])->get('https://api.siigo.com/v1/invoices/'.$invoice_siigo.'/pdf');
            
            $data = $response->json();
            //dd($data);
            // Asumiendo que $data['base64'] contiene el contenido en base64 del PDF
            $pdfContent = base64_decode($data['base64']);

            // HTML para mostrar en la página (puedes personalizarlo según tus necesidades)
            //$html = '<div style="text-align: center;"><embed src="data:application/pdf;base64,'.base64_encode($pdfContent).'" width="100%" height="auto" /></div>';

            // Utiliza tu función getPdf para mostrar el PDF
            //$this->generatePdf($html);
             // Mostrar el PDF
            header('Content-Type: application/pdf');
            echo $pdfContent;
        } catch (\Throwable $th) {
            Log::error('Error al obtener invoice de Siigo: ' . $th->getMessage() . PHP_EOL . $th->getTraceAsString());

        }
    }
    public function getQrCode($id)
    {
        $options = new QROptions([
            'eccLevel' => QRCode::ECC_L,
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'version' => 5,
        ]);
    
        //$orderIds = $request->input('order_ids');
        $html="";
        //$pdf = new TCPDF();
    
        // foreach ($orderIds as $orderId) {
            $order = Order::where('wc_order_id',$id)->first();
    
            if (!$order) {
                return ["status" => false];
            }
    
            $logo=base64_encode(file_get_contents( asset('assets/images/LOGO-NATY-LONDON-sin-fondo-1.jpg') ));
            $logo ='data:image/jpeg;base64,'.$logo;
            $customer=json_decode($order['billing'],true);

            $payment_methid = $order->payment_method == 'Paga a cuotas' ? 'Addi' : $order->payment_method;
            //$qrcode = (new QRCode($options))->render(env('APP_URL').$order->wc_order_id);
            $qrcode = (new QRCode($options))->render($order->wc_order_id);
            $first_name = $customer['first_name'] . ' ' . $customer['last_name'];
            $first_name = strlen($first_name) > 10 ? substr($first_name, 0, 10) : $first_name;
            $identification = $customer['document_number'] ? $customer['document_number'] : 0;
            $phone = $customer['phone'] ? $customer['phone'] : 0;
            $html = '<table style="width: 100%; height:90%;">';
            $html .= '<tr style="height: 100%;width: 100%;padding:0px;margin:0px;">';
            $html .= '<td style="width:50%;height:87%;padding:0px;margin:0px;position: absolute;z-index:105">';
            $html .= '<img class="qr-image" src="' . $qrcode . '" style="width:98.3%;heigth:auto;margin-top:-2.5px;z-index:-15">';
            $html .= '</td>';
            $html .= '<td style="width:49%;height:50%;padding:0px;margin:0px;">';
            $html .= '<img class="qr-image" src="' . $logo . '" style="width:0%;heigth:auto;margin-top:-11px;margin-left:-12px;">';
            $html .= '<div style="font-size:6px;position: absolute;margin-top:1px;right:24px;font-weight: bold;">ID:' . $order->wc_order_id . '</div>';
            $html .= '<div style="font-size:6px;margin-top:3px;font-weight: bold;">D:' . $identification . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">' . $first_name . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">' . $phone . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">$' . number_format($order->total_amount, 2) . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">' . $payment_methid . '</div>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';
    
            // $pdf->AddPage();
            // $pdf->writeHTML($html, true, false, true, false, '');
        // }

        //$pdf->Output();
        $this->getPdf($html);
        exit();
    }

    function getPdf($html,$conf="") {
        $tabla="<style>
                @font-face {
                font-family:'Monea Alegante';           
                src: url('https://ceramik.com.co/wp-content/uploads/2022/11/Monea-Alegante.otf') format('truetype');
                font-weight: normal;
                font-style: normal;
                }
            body{
                font-family: 'Monea Alegante', sans-serif;
                
            }
                  td {
                  border:0px;
                  padding:0;
                  border-collapse: collapse;
                  height:50%;
                }
                html{margin:0;padding:0}
                table{margin:0;padding:0;height:100%;width:100%;
                }
                .title-qr-content{
                    width:100%;
                    text-align: center;
                    margin-top:-15px;
                    font-weight: 500;
                    padding-left:-2px;
                    font-family: 'Monea Alegante', sans-serif;
                    }
                </style>";
                $tabla.=$html;
                $dompdf = new Dompdf();
                $dompdf->loadHtml($tabla);
                $dompdf->set_paper(array(0,0,147,71));
                $dompdf->set_option('dpi', 52);
                $dompdf->render();
                
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=documento.pdf");
                echo $dompdf->output();
    }

function getPdfCodeMasivos($htmlData, $conf = "")
    {
        
        // Deserializar la cadena JSON en un array
        $data = json_decode($htmlData, true);
        
        // Inicializar el contenido HTML del PDF
        $pdfContent = "<style>
                            @font-face {
                                font-family: 'Monea Alegante';
                                src: url('https://ceramik.com.co/wp-content/uploads/2022/11/Monea-Alegante.otf') format('truetype');
                                font-weight: normal;
                                font-style: normal;
                            }
                            body {
                                font-family: 'Monea Alegante', sans-serif;
                            }
                            td {
                                border: 0px;
                                padding: 0;
                                border-collapse: collapse;
                                height: 50%;
                            }
                            html {
                                margin: 0;
                                padding: 0;
                            }
                            table {
                                margin: 0;
                                padding: 0;
                                height: 100%;
                                width: 100%;
                            }
                            .title-qr-content {
                                width: 100%;
                                text-align: center;
                                margin-top: -15px;
                                font-weight: 500;
                                padding-left: -2px;
                                font-family: 'Monea Alegante', sans-serif;
                            }
                        </style>";

        // Agregar el contenido HTML de cada QR al contenido total del PDF
        foreach ($data as $html) {
            $pdfContent .= $html;
        }

        // Crear el objeto Dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($pdfContent);

        // Configuración del tamaño del papel y DPI
        $dompdf->set_paper(array(0,0,147,71));
        $dompdf->set_option('dpi', 52);

        // Renderizar el PDF
        $dompdf->render();

        // Configurar las cabeceras para la respuesta PDF
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=documento.pdf");

        // Enviar el contenido del PDF al navegador
        echo $dompdf->output();
    }

    public function redirectToDetail($order_id)
    {
        // Verificar si el pedido existe en la base de datos
        $order = Order::where('wc_order_id', $order_id)->first();

        if ($order) {
            $responsePayment = '';
            $status_order = $order->status;
            $payment_method = $order->payment_method;
            $id_transaction_payment = $order->id_transaction_payment;
            if ($status_order == 0) {
                if ($payment_method == "Wompi") {
                    $responsePayment = $this->apiWompi($id_transaction_payment);
                }
            }
            // Si existe, devolver una respuesta JSON válida
            return response()->json(['valid' => true, 'order_status' => $status_order, 'responseStatusPayment' => $responsePayment, 'payment_method'=>$payment_method]);
        } else {
            // Si no existe, devolver una respuesta JSON no válida
            return response()->json(['valid' => false]);
        }
    }

    public function getPdfOrder($idOrder)
    {
        $order = Order::where('wc_order_id',$idOrder)->first();
        $logo=base64_encode(file_get_contents( asset('assets/images/LOGO-NATY-LONDON-sin-fondo-1.jpg') ));
        $logo ='data:image/jpeg;base64,'.$logo;

        $customer=json_decode($order['billing'],true);
        $first_name = $customer['first_name'] . ' ' . $customer['last_name'];
        $identification = $customer['document_number'] ? $customer['document_number'] : 0;
        $phone = $customer['phone'] ? $customer['phone'] : 0;
        $city = $customer['city'] ? $customer['city'] : 'Sin ciudad';
        $note = isset($customer['customer_note'])  ? $customer['customer_note'] : 'Sin nota';

        
        $company = 'Naty London';
        $addres_company = $customer['address_1'] ? $customer['address_1'] : 'Sin Dirección';
        $addres_company2 = $customer['address_2'] ? $customer['address_2'] : 'Sin Dirección';

        
        // Obtener la fecha del pedido
        $date_order = $order->created_at;
        $formatted_date_order  = date('d/m/Y H:i:s', strtotime($date_order));
        $num_order = $idOrder;
        $payment_method= $order->payment_method;

        $lineItems = json_decode($order->line_items, true);
        $tabla="<style>
                @font-face {
                font-family:'Monea Alegante';           
                src: url('https://ceramik.com.co/wp-content/uploads/2022/11/Monea-Alegante.otf') format('truetype');
                font-weight: normal;
                font-style: normal;
                }
            body{
                font-family: 'Monea Alegante', sans-serif;
                
            }
                  td {
                  border:0px;
                  padding:0;
                  border-collapse: collapse;
                }
                html{margin:0;padding:0}
                .text-title{margin-top: -6px !important}
                .title-qr-content{
                    width:100%;
                    text-align: center;
                    margin-top:-15px;
                    font-weight: 500;
                    padding-left:-2px;
                    font-family: 'Monea Alegante', sans-serif;
                    }
                </style>";

                $html = '<table style="width: 100%; margin-top: 0; padding-top: 0;">';
                $html .= '<tr>';
                $html .= '<td style="width: 33%; vertical-align: top; position: relative; text-align: center;">';
                $html .= '<h3 style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); margin: 0; padding: 5px; color: #000; font-weight: bold;">FACTURA</h3>';
                $html .= '<img src="' . $logo . '" style="width: 100%; margin-top: 10px;">';
                $html .= '</td>';
                $html .= '<td style="width: 33%; vertical-align: top;">';
                $html .= '<p style="margin-top: 3px !important"><strong>Cliente:</strong> ' . $first_name . '</p>';
                $html .= '<p class="text-title"><strong>Identificación:</strong> ' . $identification . '</p>';
                $html .= '<p class="text-title"><strong>Teléfono:</strong> ' . $phone . '</p>';
                $html .= '<p class="text-title"><strong>Ciudad:</strong> ' . $city . '</p>';
                $html .= '<p class="text-title"><strong>Nota del Cliente:</strong> ' . $note. '</p>';
                $html .= '</td>';
                $html .= '<td style="width: 33%; vertical-align: top;">';
                $html .= '<p style="margin-top: 3px !important"><strong>' . $company . '</strong> </p>';
                $html .= '<p class="text-title"><strong>Dirección:</strong> ' . $addres_company . '</p>';
                $html .= '<p class="text-title"><strong>Dirección 2:</strong> ' . $addres_company2 . '</p>';
                $html .= '<p class="text-title"><strong>Fecha del pedido:</strong> ' . $formatted_date_order . '</p>';
                $html .= '<p class="text-title"><strong>Número de orden:</strong> ' . $num_order . '</p>';
                $html .= '<p class="text-title"><strong>Método de pago:</strong> ' . $payment_method . '</p>';
                $html .= '</td>';
                $html .= '</tr>';
                $html .= '</table>';

                // Añadir tabla de productos
                $html .= '<table style="width: 100%; border-collapse: collapse;">';
                $html .= '<thead style="background-color: #000; color: #fff;">';
                $html .= '<tr>';
                $html .= '<th style="padding: 8px; font-weight: bold;">Producto</th>';
                $html .= '<th style="padding: 8px; font-weight: bold;">Cantidad</th>';
                $html .= '<th style="padding: 8px; font-weight: bold;">Total</th>';
                $html .= '</tr>';
                $html .= '</thead>';

                foreach ($lineItems as $item) {
                    $productName = $item['name'];
                    $quantity = $item['quantity'];
                    $total = $item['total'];
                    $sku = $item['sku'];

                    $html .= '<tr>';
                    $html .= '<td style="border: 1px dotted #ccc; padding: 8px;">';
                    $html .= $productName . '<br><strong>SKU:</strong> ' . $sku;
                    $html .= '</td>';
                    $html .= '<td style="border: 1px dotted #ccc; padding: 8px;">' . $quantity . '</td>';
                    $html .= '<td style="border: 1px dotted #ccc; padding: 8px;">' . number_format($total, 2) . '</td>';
                    $html .= '</tr>';
                }

                $html .= '</table>';
                
        $tabla.=$html;
        $dompdf = new Dompdf();
        $dompdf->loadHtml($tabla);
        //$dompdf->set_paper(array(0,0,147,71));
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->set_option('dpi', 52);
        $dompdf->render();
        
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=documento.pdf");
        echo $dompdf->output();
    }

    public function generateQrSelected(Request $request)
    {
        $selectedOrders = $request->input('orders');

        $generatedHtml = [];
        // Lógica para generar QR de los pedidos seleccionados
        foreach ($selectedOrders as $orderId) {
            // Puedes ajustar tu lógica actual para manejar múltiples pedidos a la vez
            $options = new QROptions([
                'eccLevel' => QRCode::ECC_L,
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'version' => 5,
            ]);

            $order = Order::where('wc_order_id', $orderId)->first();

            if (!$order) {
                return ["status" => false];
            }

            $logo = base64_encode(file_get_contents(asset('assets/images/LOGO-NATY-LONDON-sin-fondo-1.jpg')));
            $logo = 'data:image/jpeg;base64,' . $logo;
            $customer = json_decode($order['billing'], true);

            $payment_method = $order->payment_method == 'Paga a cuotas' ? 'Addi' : $order->payment_method;
            $qrcode = (new QRCode($options))->render($order->wc_order_id);
            $first_name = $customer['first_name'] . ' ' . $customer['last_name'];
            $first_name = strlen($first_name) > 10 ? substr($first_name, 0, 10) : $first_name;
            $identification = $customer['document_number'] ? $customer['document_number'] : 0;
            $phone = $customer['phone'] ? $customer['phone'] : 0;

            $html = '<table style="width: 100%; height:90%;">';
            $html .= '<tr style="height: 100%;width: 100%;padding:0px;margin:0px;">';
            $html .= '<td style="width:50%;height:87%;padding:0px;margin:0px;position: absolute;z-index:105">';
            $html .= '<img class="qr-image" src="' . $qrcode . '" style="width:98.3%;heigth:auto;margin-top:-2.5px;z-index:-15">';
            $html .= '</td>';
            $html .= '<td style="width:49%;height:50%;padding:0px;margin:0px;">';
            $html .= '<img class="qr-image" src="' . $logo . '" style="width:0%;heigth:auto;margin-top:-11px;margin-left:-12px;">';
            $html .= '<div style="font-size:6px;position: absolute;margin-top:1px;right:24px;font-weight: bold;">ID:' . $order->wc_order_id . '</div>';
            $html .= '<div style="font-size:6px;margin-top:3px;font-weight: bold;">D:' . $identification . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">' . $first_name . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">' . $phone . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">$' . number_format($order->total_amount, 2) . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">' . $payment_method . '</div>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';

            // Lógica para guardar o mostrar el HTML generado
            // $this->getPdf($html);
            // almacenar el HTML en un array para usarlo más tarde
            $generatedHtml[] = $html;
        }
        $generatedHtml = array_map('utf8_encode', $generatedHtml);
        //dd($generatedHtml);
        return response()->json(['success' => true, 'message' => 'QR generados correctamente', 'html' => $generatedHtml]);
    }

    public function generatePdfMultiple(Request $request)
    {
        $htmlData = $request->input('htmlData');

        // Lógica para generar el PDF con múltiples QR a partir de los datos HTML
        $this->getPdfCodeMasivos($htmlData);

        exit();
    }
    
    public function apiWompi($id_transaction)
    {
        try {
            $publicKey = env('API_PUBLIC_KEY_WOMPI');
            $privateKey = env('API_PRIVATE_KEY_WOMPI');
            $token = base64_encode($publicKey . ':' . $privateKey);
            $baseUrl = 'https://production.wompi.co/v1/';

                $response = Http::withHeaders([
                    'Bearer' => $token,
                ])->get($baseUrl . 'transactions/' . $id_transaction);
            $response = $response->json();
            $status = $response['data']['status'];

            return $status;
        } catch (\Throwable $th) {
            Log::error('Error en la sincronización de facturas: ' . $th->getMessage());
        }
    }

}
