<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\LastSyncInvoices;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use TCPDF;

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
        $type="";
        if(isset($_GET['type'])){
            $type=$_GET['type'];              
        }
        return view('orders.indexnew',['type'=>$type]);

    }

    public function get_orders_datatable(Request $request)
    {

       $arrayStatus=['Procesando','Picking Realizado','Packing Realizado','Completado','Embalado','Etiquetado','Enviado',''];
    
        $query = Order::leftJoin('users', 'users.id', '=', 'orders.create_user_id')
        ->select('orders.id', 'orders.wc_order_id', 'orders.create_user_id', 'orders.billing','orders.payment_method', 'orders.wc_status', 'orders.total_amount', 'orders.status', 'orders.created_at', 'users.name as name_user');
        
    
        //$l=$request->input('start') / $request->input('length') + 1;
        //$users = $query->paginate($request->input('length'), ['*'], 'page',1 );
        //$count = count($users);
        $data= $query->get();
        $datos = array();
        $rol=auth()->user()->getRoleNames()->first();
        for ($i=0;$i<count($data);$i++){
            if(($data[$i]['status']==0 && ($rol=="Picking" || $rol=="Admin" )) || ($data[$i]['status']==1  && ($rol=="Packing"  ||  $rol=="Admin"  )) ){
                $datos[$i]['edit']='<a href="'.route('orders.create', $data[$i]['wc_order_id']).'"><i class="mdi mdi-checkbox-blank-outline"></i></a>';
            }
            if(($data[$i]['status']==1 && $rol=="Picking") || ($data[$i]['status']==2  && $rol=="Packing") ){
                $datos[$i]['edit']='<a href="#" class="btn-no-check"><i class="mdi mdi-checkbox-marked-outline"></i></a>';
            }
            
            if($rol=="Despachador"){
                $datos[$i]['edit']="";
            }
            if(!isset($data[$i]['edit'])){
                $datos[$i]['edit']="";
            }
            if(($rol=="Admin" || $rol=="Delivery") && $data[$i]['status']==2){
               $datos[$i]['edit']= '<a href="#" class="btm-check" data="'.$data[$i]['id'].'"><i class="mdi mdi-checkbox-blank-outline"></i></a>';
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
            $fecha_hora = date('d/m/Y h:i A', strtotime($data[$i]['created_at']));
            //$qr = '<td style="display:flex;justify-content:center;"><a class="" href="'.get_site_url().'/wp-json/picking-weppy/order/qr?id='.$pedido->get_id().'"><i class="mdi mdi-qrcode"></i></a></td>';
            $qr = '<td style="display:flex;justify-content:center;"><a class="" target="_blank" href="'.route('orders.qr', ['id' => $data[$i]['wc_order_id']]).'"> <i class="mdi mdi-qrcode"></i></a></td>';
            $datos[$i]['phone']= $customer['phone'];
            $datos[$i]['city']= $customer['city'];
            $datos[$i]['payment_method']= $data[$i]['payment_method'];
            $datos[$i]['total_amount']= number_format($data[$i]['total_amount'], 2, '.', ',');
            $datos[$i]['city']= $customer['city'];
            $datos[$i]['date']= $fecha_hora;
            $datos[$i]['wc_order_id']= $data[$i]['wc_order_id'];
            
            // $qrCode = QrCode::size(150)->generate(route('orders.qr', ['id' => $data[$i]['wc_order_id']]));
            // $qrCodePath = public_path("qrcodes/{$data[$i]['wc_order_id']}.png");
            // $qrCode->format('png')->generate($qrCodePath);
    
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
            $day = date("Y-m-d");
            $authorization = base64_encode(env('API_WOOCOMMERCE_USER') . ':' . env('API_WOOCOMMERC_PASSWORD'));
            $consumer_key = env('API_WOOCOMMERCE_USER');
            $consumer_secret = env('API_WOOCOMMERC_PASSWORD');
            $headers = [
                //'Authorization' => 'Basic ' . $authorization,
                'Cookie' => 'database_validation=1; mailpoet_page_view=%7B%22timestamp%22%3A1686054310%7D',
            ];
            $response = Http::get('https://natylondon.com/wp-json/wc/v3/orders?consumer_key='.$consumer_key.'&consumer_secret='.$consumer_secret);

            if ($response->status() == 200) {
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
                    
                    $createdTimestamp = strtotime($invoice['date_created']);
                    if (!$lastSync || $createdTimestamp > Carbon::parse($lastSync->last_register)->timestamp) {
                        $totalInvoicesresults++;
                        $siigo_invoice_id="";
                        foreach ($invoice['meta_data'] as $meta_data) {
                            if($meta_data['key']=='_siigo_invoice_id'){
                            $siigo_invoice_id=$meta_data['value'];
                            }
                        }
                        $order = Order::create([
                            'wc_order_id' => $invoice['id'],
                            'payment_method' => $invoice['payment_method_title'], 
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

                $lastSync->last_register = now()->setTimezone('America/Bogota');
                $lastSync->save();
                Log::info('Cantidad de facturas: ' . $totalInvoicesresults);
                $error = false;
                $mensaje = 'Exitoso';
            }else{
                dd( 'false api'. $response->throw()->json());
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error en la sincronización de facturas: ' . $th->getMessage());
        }
        return response()->json(['error' => $error, 'mensaje' => $mensaje]);
    }

    public function qr($params=[]) {
        $options = new QROptions(
            [
              'eccLevel' => QRCode::ECC_L,
              'outputType' => QRCode::OUTPUT_MARKUP_SVG,
              'version' => 5,
            ]
          );
        if(!isset($params['id'])){
           return ["status"=>false]; 
        }
        $repetir=1;
        if(isset($params['repetir'])){
            $repetir=$params['repetir'];
         }
       $order_ids = explode(",", urldecode(rawurldecode($params['id'])));
       //print_r($order_ids);
		
        $html="";
		$dd="";
		$logo=base64_encode(file_get_contents("https://natylondon.com/wp-content/uploads/2022/06/LOGO-NATY-LONDON-sin-fondo-1.jpg"));
        $logo ='data:image/jpeg;base64,'.$logo;
        for ($i = 0; $i <count($order_ids); $i++) {
            $order = wc_get_order(trim($order_ids[$i]));
            $qrcode = (new QRCode($options))->render(get_site_url()."/".$order->get_id());
            $first_name = $order->get_billing_first_name()." ".$order->get_billing_last_name();
            $first_name=strlen($first_name) > 10? substr($first_name, 0, 10) : $first_name;
            $customer_id = $customer->ID;
            $identification= get_post_meta( $order->get_id(), '_billing_document_number', true ) ? get_post_meta( $order->get_id(), '_billing_document_number', true ) : (get_post_meta( $order->get_id(), 'cedula', true )?get_post_meta( $order->get_id(), 'cedula', true ):0);
            //$chtml=$this->views("qr",['image'=>$qrcode,'logo'=>$logo,'id'=>$order->get_id()]);
		     $html.='<table style="width: 100%; height:90%;">
			  <tr style="height: 100%;width: 100%;padding:0px;margin:0px;">
				<td style="width:50%;height:87%;padding:0px;margin:0px;position: absolute;z-index:105"> 
					<img class="qr-image" src="'.$qrcode.'" style="width:98.3%;heigth:auto;margin-top:-2.5px;z-index:-15">
				 </td>
				<td style="width:49%;height:50%;padding:0px;margin:0px;">
					<img class="qr-image" src="'.$logo.'" style="width:0%;heigth:auto;margin-top:-11px;margin-left:-12px;">
                    
					<div style="font-size:6px;position: absolute;margin-top:1px;right:24px;font-weight: bold;">ID:'.$order->get_id().'</div>
                    <div style="font-size:6px;margin-top:-5px;font-weight: bold;">D:'.$identification.'</div>
                    <div style="font-size:6px;font-weight: bold;">'.$first_name.'</div>
                    <div style="font-size:6px;font-weight: bold;">'.$order->get_billing_phone().'</div>
                    <div style="font-size:6px;font-weight: bold;">$'.number_format($order->get_total(),2).'</div>
                    <div style="font-size:6px;font-weight: bold;">'.$order->get_payment_method().'</div>

                   
				</td>
			  </tr>
			</table>';
        }
		
        getPdf($html);
        exit();
        
    }

    public function getQrCode($id)
    {
        $options = new QROptions([
            'eccLevel' => QRCode::ECC_L,
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'version' => 5,
        ]);
    
        //$orderIds = $request->input('order_ids');
    
        $pdf = new TCPDF();
    
        // foreach ($orderIds as $orderId) {
            $order = Order::find($$id);
    
            if (!$order) {
                return ["status" => false];
            }
    
            $logo=base64_encode(file_get_contents("https://natylondon.com/wp-content/uploads/2022/06/LOGO-NATY-LONDON-sin-fondo-1.jpg"));
            $logo ='data:image/jpeg;base64,'.$logo;

            $qrcode = (new QRCode($options))->render(env('APP_URL')."/".$order->id());
            $first_name = $order->billing_first_name . ' ' . $order->billing_last_name;
            $first_name = strlen($first_name) > 10 ? substr($first_name, 0, 10) : $first_name;
            $identification = $order->billing_document_number ? $order->billing_document_number : 0;
            $html = '<table style="width: 100%; height:90%;">';
            $html .= '<tr style="height: 100%;width: 100%;padding:0px;margin:0px;">';
            $html .= '<td style="width:50%;height:87%;padding:0px;margin:0px;position: absolute;z-index:105">';
            $html .= '<img class="qr-image" src="data:image/svg+xml;base64,' . base64_encode($qrcode) . '" style="width:98.3%;heigth:auto;margin-top:-2.5px;z-index:-15">';
            $html .= '</td>';
            $html .= '<td style="width:49%;height:50%;padding:0px;margin:0px;">';
            $html .= '<img class="qr-image" src="' . $logo . '" style="width:0%;heigth:auto;margin-top:-11px;margin-left:-12px;">';
            $html .= '<div style="font-size:6px;position: absolute;margin-top:1px;right:24px;font-weight: bold;">ID:' . $order->id . '</div>';
            $html .= '<div style="font-size:6px;margin-top:-5px;font-weight: bold;">D:' . $identification . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">' . $first_name . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">' . $order->billing_phone . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">$' . number_format($order->total, 2) . '</div>';
            $html .= '<div style="font-size:6px;font-weight: bold;">' . $order->payment_method . '</div>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '</table>';
    
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');
        // }
    
        $pdf->Output();
    }


}
