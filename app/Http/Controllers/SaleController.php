<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Sale;

class SaleController extends Controller
{
    public function sales(Request $request) {
        $response = ['status' => null];
        
        $filter = date('Y-m-d H:i:s', strtotime("-$request->filter days"));

        $sales = Auth::user()->level === 'admin' || Auth::user()->level === 'sub-admin' ? 
            Sale::where('created_at', '>', $filter)->orderBy('created_at', 'desc')->paginate(25) : 
            Sale::where('attendant_id', Auth::id())->get();

        if($sales) {
            foreach($sales as $sale) {
                $sale->attendant_name = $sale->attendant->name ?? null;
                $sale->name = $sale->userCard->name;
                $sale->phone = $sale->userCard->phone;
                $sale->code = $sale->userCard->code;

                unset($sale->attendant);
                unset($sale->userCard);
            }

            $response['sales'] =  $sales;
            $response['status'] = 'success';
            
            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Erro interno ao consultar registro.';

            return response()->json($response, 500);
        }
    }

    public function attendantSales($id, Request $request) {
        $response = ['status' => null];
            
        $filter = date('Y-m-d H:i:s', strtotime("-$request->filter days"));
        
        $sales = Sale::where('created_at', '>', $filter)
            ->where('attendant_id', $id)
            ->orderBy('created_at', 'desc')
        ->paginate(25);
        
        if($sales) {
            foreach($sales as $sale) {
                $sale->attendant_name = $sale->attendant->name ?? null;
                $sale->name = $sale->userCard->name;
                $sale->phone = $sale->userCard->phone;
                $sale->code = $sale->userCard->code;

                unset($sale->attendant);
                unset($sale->userCard);
            }

            $response['sales'] =  $sales;
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Erro interno ao consultar registro.';
            
            return response()->json($response, 500);
        }

    }

    public function search(Request $request) {
        $response = ['status' => null];
        
        $results = Sale::where('name', 'like', "%$request->q%")->orWhere('code', $request->q)->get();
        
        foreach($results as $sale) {
            $sale->attendant_name = $sale->attendant->name ?? null;
            $sale->name = $sale->userCard->name;
            $sale->phone = $sale->userCard->phone;
            $sale->code = $sale->userCard->code;

            unset($sale->attendant);
            unset($sale->userCard);
        }

        $response['sales'] =  $results;
        $response['status'] = 'success';

        return response()->json($response, 200);

    } 
}
