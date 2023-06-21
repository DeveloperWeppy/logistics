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
    
            $response = Http::withHeaders($headers)->get('https://weppydev.com.co/prueba/wp-json/wc/v3/' . $url);
    
            if ($response->failed()) {
                return false;
            }
    
            $response = $response->throw()->json();
    
            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }
    
}
