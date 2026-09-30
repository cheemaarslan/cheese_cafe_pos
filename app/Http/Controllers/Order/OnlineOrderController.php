<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OnlineOrderController extends Controller
{
    public function index()
    {
        return view('online-order.index');
    }
}
