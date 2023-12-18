<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SyncSiigo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:siigo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize Siigo data';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->sync_invoices();
            $this->info('Sincronización Siigo ejecutada con éxito CRON.');
        } catch (\Throwable $th) {
            $this->error('Error en la sincronización Siigo CRON: ' . $th->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
    public function sync_invoices()
    {
        set_time_limit(0);
        $error = false;
        $mensaje = '';
        try {
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

                $totalInvoicesresults= 0;
                $fechaActualMenosUnDia = Carbon::now('America/Bogota')->subDay()->timestamp;
                // Ahora, $filteredOrders contiene solo las órdenes con estado "processing" o "addi-approved"
                // Puedes trabajar con este array filtrado según tus necesidades.
                foreach ($orders as $key => $invoice) {
                    
                    $createdTimestamp = strtotime($invoice['date_created']);
                    $modifiedTimestamp = strtotime($invoice['date_paid']);

                    
                        if (
                            ($createdTimestamp < $fechaActualMenosUnDia)
                            && ($createdTimestamp < $fechaActualMenosUnDia || $modifiedTimestamp < $fechaActualMenosUnDia)
                        ) {
                            // Verifica si ya existe un pedido con el mismo wc_order_id
                            $existingOrder = Order::where('wc_order_id', $invoice['id'])->first();

                            if (!$existingOrder  && $existingOrder === null) {
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
                                    'create_user_id' =>  29,
                                    'picking_user_id'=>0,
                                    'siigo_invoice'=>$siigo_invoice_id,
                                    'status' => 0,
                                    'date_paid'  => $timestamp
                                ]);
                            }else{
                                Log::error('Ya existe el pedido con wc_order_id: ' . $invoice['id']);
                            }
                        
                        
                    }
                }
                // Ejecutar la segunda función
                try {
                    Log::info('Antes de processAdditionalOrders');
                    $this->processAdditionalOrders($consumer_key, $consumer_secret);
                    Log::info('Después de processAdditionalOrders');
                } catch (\Throwable $th) {
                    Log::error('Excepción en processAdditionalOrders: ' . $th->getMessage());
                }

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
            Log::error('StackTrace: ' . $th->getTraceAsString());
        }
        return response()->json(['error' => $error, 'mensaje' => $mensaje]);
    }

    private function processAdditionalOrders($consumer_key, $consumer_secret)
    {
        set_time_limit(0);
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
                    $fechaActualMenosUnDia = Carbon::now('America/Bogota')->subDay()->timestamp;
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
    
                            if (
                                ($createdTimestamp < $fechaActualMenosUnDia)
                                && ($createdTimestamp < $fechaActualMenosUnDia || $modifiedTimestamp < $fechaActualMenosUnDia)
                            ) {
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
                                        'create_user_id' =>  29,
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
}
