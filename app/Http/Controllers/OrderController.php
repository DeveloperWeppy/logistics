<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
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
            if(($data[$i]['status']==0 && $rol=="Picking") || ($data[$i]['status']==1  && $rol=="Packing") ){
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
            if(($rol=="admin" || $rol=="Delivery") && $data[$i]['status']==2){
               $data[$i]['edit']= $data[$i]['edit'].'<a href="#" class="btm-check" data="'.$data[$i]['id'].'"><i class="mdi  mdi-checkbox-multiple-blank-outline"></i></a>';
            }
            if(($rol=="admin" || $rol=="Delivery") && $data[$i]['status']==2){
                $data[$i]['edit']= $data[$i]['edit'].'<a href="#" class="btm-check" data="'.$data[$i]['id'].'"><i class="mdi  mdi-checkbox-multiple-blank-outline"></i></a>';
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
        $data=$this->apiWc("orders/".$id);
        $data_items=[];
        $order = Order::where('wc_order_id',$id)->first();
        if (!$order) {
            $order = Order::create([
                'wc_order_id' => $data['id'],
                'wc_status' => $data['status'],
                'shipping' => json_encode($data['shipping']),
                'billing' => json_encode($data['billing']),
                'line_items' =>json_encode($data['line_items']),
                'total_amount' =>$data['total'],
                'create_user_id' =>  auth()->user()->id,
                'picking_user_id'=>0,
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
            $data_items[]=['sku'=> $dataP['sku'],'image'=>$image, 'name'=>$dataP['name'],'id'=>$dataP['id'],'quantity'=>$data['line_items'][$i]['quantity'],'scann'=>0];
        }
        return view('orders.form',['title' =>'Agregar Orden','data'=>$data,'data_items'=>$data_items,'id'=> $order->id,'creador'=>$order->creatorUser(),'picking'=>$order->pickingUser(),'status'=>$order->status]);
    }
    public function store(Request $request,$id,$type=0)
    {
        $order = Order::findOrFail($id);
        $currentDateTime = date('Y-m-d H:i:s');
        if($type==1){
            $order->finalized_user_id= auth()->user()->id;
            $order->status=3;
            $order->date_delivery=$currentDateTime;
        }else{
            if($order->status==1){
                $order->status=2;
                $order->date_packing=$currentDateTime;
                $order->packing_user_id=auth()->user()->id;
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
}
