<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\GerenciaNetPIXHelper;

class PaymentController extends Controller
{
    public function test() {
        GerenciaNetPIXHelper::test();
    }

    public function paymentConfirm() {
        
    }
}
