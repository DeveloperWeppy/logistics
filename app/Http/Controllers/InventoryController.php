<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class InventoryController extends Controller
{
    public function index()
    {
        return view('inventory.index');
    }

    public function auth_siigo()
    {
        $username = env('SIIGO_API_USERNAME');
        $accessKey = env('SIIGO_API_ACCESS_KEY');
        try {
            $response = Http::post('https://api.siigo.com/auth', [
                'username' => $username,
                'access_key' => $accessKey,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $accessToken = $responseData['access_token'];

                Session::put('accessToken', $accessToken);
                return $accessToken;
            } else {
                // Maneja la respuesta con código de estado no exitoso
                // Puedes lanzar una excepción, retornar un mensaje de error, etc.
                return null;
            }
        } catch (Exception $e) {
            // Maneja cualquier excepción que pueda ocurrir durante la petición
            // Puedes lanzar una excepción, retornar un mensaje de error, etc.
            return null;
        }
    }

    public function search($id)
    {
        $user_warehouse = Auth::user()->warehouse;
        $user_role = Auth::user()->roles->first()->name;
        $product = '';
        $data = null;
        $token_siigo = '';

        if ($user_warehouse == 'FERIAS') {
            $product = Product::where('sku', $id)->first();
        }
        if (Session::has('accessToken') && !empty(Session::get('accessToken'))) {
            $token_siigo = Session::get('accessToken');
        } else {
            $token_siigo = $this->auth_siigo();
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token_siigo,
                'Content-Type' => 'application/json',
            ])->get('https://api.siigo.com/v1/products', [
                'code' => $id,
            ]);
            if ($response->successful()) {
                $data = json_decode($response, true);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        //dd($data); 

        return response()->json(['data' => $data, 'user_warehouse' => $user_warehouse, 'product' => $product, 'user_role'=>$user_role]);
    }

    public function updateStock(Request $request)
    {
        $productId = $request->input('productId');
        $newStockValue = $request->input('newStockValue');
        $stockActual = $request->input('stockActual');

        // Calcula el nuevo stock
        $new_stock = $stockActual - $newStockValue;

        // Actualiza el producto en la base de datos
        $product = Product::find($productId);
        $product->stock = $new_stock;
        $product->save();

        // Crea una respuesta JSON con el mensaje de éxito
        $response = [
            'success' => true,
            'message' => 'Stock actualizado correctamente',
            'newStock' => $new_stock // Opcional: puedes incluir datos adicionales en la respuesta
        ];

        // Devuelve la respuesta JSON al cliente
        return response()->json($response);
    }

}
