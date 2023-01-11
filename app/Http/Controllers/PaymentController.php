<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\UserCard;

use App\Helpers\GerenciaNetPIXHelper;

class PaymentController extends Controller
{
    public function test() {
        GerenciaNetPIXHelper::test();
    }
    
    public function generateCharge($card_code) {
        $response = ['status' => null];

        $user_card = UserCard::where('code', $card_code)->first();
        if($user_card) {
            $charge = GerenciaNetPIXHelper::imediateChargeTxid($user_card->card->price, $user_card->code);

            $payment = Payment::create([
                'user_card_id' => $user_card->id,
                'txid' => $charge->txid,
                'location_id' => $charge->loc->id,
                'price' => $user_card->card->price,
                'pix_key' => $charge->chave,
                'paid' => false,
            ]);

            if($payment) {
                $response['qrcode'] = GerenciaNetPIXHelper::locationQRcode($payment->location_id);
                if($response['qrcode']) {
                    return response()->json($response, 200);
                }
            }
    
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }
    
    }

    public function paymentConfirm(Request $request) {
        $method = $request->method();
        $params = explode('/', $request->url());
        $body = json_decode(file_get_contents('php://input'), true);
        $body = implode('|', $body);
        switch($method) {
            case 'POST':
                $payment = Payment::create([
		    'body' => $body
		]);
 		
		if($payment) {
		    return response()->json($body, 200);
		} else {
		    return response()->body($body, 300);
		}  
	    break;
	    case 'GET':
		$response['status'] = 200;
                $response['mensagem'] = 'Requisição realizada com sucesso!';
                $response['dados'] = $body;

                return response()->json($response, 200);
	    break;
	} 
    }                  
}
