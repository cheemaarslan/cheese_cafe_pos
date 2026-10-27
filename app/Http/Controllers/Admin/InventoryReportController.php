<?php

namespace App\Http\Controllers\Admin;

use App\Models\InventoryItem;
use App\Services\InventoryReportService;
use Exception;
use Illuminate\Http\Request;

class InventoryReportController extends AdminController
{
    public function __construct(private InventoryReportService $inventoryReportService)
    {
        parent::__construct();
        $this->middleware(['permission:inventory-report'])->only('index', 'daily');
    }

    public function index(Request $request)
    {
        try {
            return response(['data' => $this->inventoryReportService->report($request)]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function daily(Request $request, InventoryItem $inventoryItem)
    {
        try {
            return response(['data' => $this->inventoryReportService->daily($request, $inventoryItem)]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
