<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    // list all active payment gateways
    public function index()
    {
       try {
           $paymentGateways = \App\Models\PaymentGateway::select('id', 'name', 'slug', 'status')->where('status', 5)->get();
           return response(['status' => true, 'data' => $paymentGateways], 200);
       } catch (\Exception $exception) {
           return response(['status' => false, 'message' => $exception->getMessage()], 422);
       }
    }
}
