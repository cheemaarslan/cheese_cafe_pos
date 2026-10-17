<?php

namespace App\Http\Controllers\Admin;

use App\Models\InventoryPurchase;
use App\Services\ProfitLossService;
use Exception;
use Illuminate\Http\Request;

class ProfitLossController extends AdminController
{
    public function __construct(private ProfitLossService $profitLossService)
    {
        parent::__construct();
        $this->middleware(['permission:profit-loss'])->only('index');
        $this->middleware(['permission:inventory_delete'])->only('destroyPurchase');
    }

    public function index(Request $request)
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
        ]);

        try {
            return response(['data' => $this->profitLossService->report($request)]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroyPurchase(InventoryPurchase $inventoryPurchase)
    {
        try {
            $this->profitLossService->destroyPurchase($inventoryPurchase);

            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
