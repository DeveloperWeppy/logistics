<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function apiWc($url, $data = [])
    {
        try {
            $authorization = base64_encode(env('API_WOOCOMMERCE_USER') . ':' . env('API_WOOCOMMERC_PASSWORD'));
    
            $headers = [
                'Authorization' => 'Basic ' . $authorization,
                'Cookie' => 'database_validation=1; mailpoet_page_view=%7B%22timestamp%22%3A1686054310%7D',
            ];
            if(count($data)>0){
                $response = Http::withHeaders($headers)->post('https://natylondon.com/wp-json/wc/v3/' . $url,$data);
            }else{
                $response = Http::withHeaders($headers)->get('https://natylondon.com/wp-json/wc/v3/' . $url);
            }
            
    
            if ($response->failed()) {
                return false;
            }
    
            $response = $response->throw()->json();
    
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }
  
    public function apiSiigo($url, $data = [],$method="")
    {
        try {
            $headers = [
                'Content-Type' => 'application/json', 
            ];
            if($url=="auth"){
               $url='https://api.siigo.com/auth';
               $data=['username'=>env('API_SIIGO_USERNAME'),'access_key'=>env('API_SIIGO_ACCESS_KEY')];
            }else{
                $url='https://api.siigo.com/v1/'.$url;
                $authorization = session('siigo_token');
                $headers['Authorization']='Bearer ' . $authorization; 
            }
            if(count($data)>0){
                if($method==""){
                    $response = Http::withHeaders($headers)->post( $url, $data); 
                }else{
                    $response = Http::withHeaders($headers)->put($url, $data);
                }
               
            }else{
                $response = Http::withHeaders($headers)->get( $url);
            }
            if ($response->failed()) {
                return false;
            }
            $response = $response->throw()->json();
            if($url=="https://api.siigo.com/auth"){
                if(isset($response['access_token'])){
                   session(['siigo_token' =>$response['access_token'],'siigo_data' => date('Y-m-d H:i:s') ]);
                }
            }
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function apiWompi($id_transaction)
    {
        try {
            $publicKey = env('API_PUBLIC_KEY_WOMPI');
            $privateKey = env('API_PRIVATE_KEY_WOMPI');
            $baseUrl = 'https://production.wompi.co/v1/';

            $response = Http::withBasicAuth($publicKey, $privateKey)
                ->get($baseUrl . 'transactions/' . $id_transaction);

            //dd($response);
            return $response->json();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function apiAddi($id_transaction)
    {
        try {
            $publicKey = env('API_PUBLIC_KEY_WOMPI');
            $privateKey = env('API_PRIVATE_KEY_WOMPI');
            $baseUrl = 'https://production.wompi.co/v1/';

        $response = Http::withBasicAuth($publicKey, $privateKey)
            ->get($baseUrl . 'transactions/' . $id_transaction);

        return $response->json();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    
}
