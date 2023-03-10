<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Payment;
use App\Models\UserCard;
use App\Models\Sale;

use App\Helpers\GerenciaNetPIXHelper;

class PaymentController extends Controller
{
    public function test() {
        GerenciaNetPIXHelper::test();
    }
    
    public function generateCharge($card_code, Request $request) {
        $response = ['status' => null];

        $user_card = UserCard::where('code', $card_code)->first();
        if($user_card) {
            if(!$user_card->validated) {
                $charge = GerenciaNetPIXHelper::imediateChargeTxid($user_card->card->price, $user_card->code);

                $payment = Payment::create([
                    'user_card_id' => $user_card->id,
                    'entry_id' => $request->entry ?? null,
                    'txid' => $charge->txid,
                    'location_id' => $charge->loc->id,
                    'price' => $user_card->card->price,
                    'pix_key' => $charge->chave,
                    'paid' => false,
                ]);

                if($payment) {
                    $response['qrcode'] = GerenciaNetPIXHelper::locationQRcode($payment->location_id);
                    if($response['qrcode']) {
                        $response['status'] = 'success';
                        return response()->json($response, 200);
                    }
                }

            } else {
                $response['status'] = 'error';
                $response['error'] = 'A cartela inserida já foi paga.';

                return response()->json($response, 422);
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
      
        switch($method) {
            case 'POST':
                $payment = WebhookTest::create([
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

    public function pixPaymentConfirm(Request $request) {
        $response = ['status' => null];
        $pix = json_decode(json_encode($request->pix[0]));
        $payment = Payment::where('txid', $pix->txid)->first();
 
        if($payment) {
            $payment->userCard->validated = true;
            $payment->entry->sale = true;
            $payment->paid = true;
                
            if($payment->userCard->save() && $payment->entry->save() && $payment->save()) {
                $sale = Sale::create([
                    'attendant_id' => $payment->entry->attendant_id,
                    'user_card_id' => $payment->userCard->id,
                    'name' => $payment->userCard->name,
                    'code' => $payment->userCard->code,
                    'phone' => $payment->userCard->phone,
                    'value' => $payment->price,
                ]);
                
                if($sale) {
                    $response['status'] = 'success';
                    return response()->json($response, 200);

                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Error during process save register.';

                    return response()->json($response, 300);
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Error during process save register.';
                    
                return response()->json($response, 300);      
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error during process save register.';

            return response()->json($response, 300);
        }
    }                  
}
