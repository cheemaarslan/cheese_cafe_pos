<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Enums\Source;
use App\Exports\ProfitLossExport;
use App\Models\InventoryPurchase;
use App\Services\CompanyService;
use App\Services\ProfitLossService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Smartisan\Settings\Facades\Settings;

class ProfitLossController extends AdminController
{
    public function __construct(private ProfitLossService $profitLossService, private CompanyService $companyService)
    {
        parent::__construct();
        $this->middleware(['permission:profit-loss'])->only('index', 'export', 'pdf');
        $this->middleware(['permission:inventory_delete'])->only('destroyPurchase');
    }

    public function index(Request $request)
    {
        $this->validateFilters($request);

        try {
            return response(['data' => $this->profitLossService->report($request)]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(Request $request)
    {
        $this->validateFilters($request);

        try {
            return Excel::download(new ProfitLossExport($this->profitLossService->report($request)), 'Profit-Loss.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function pdf(Request $request)
    {
        $this->validateFilters($request);

        try {
            $pdf = Pdf::loadView('pdf.profit_loss', [
                'report' => $this->profitLossService->report($request),
                'company' => $this->companyService->list(),
                'copyright' => Settings::group('site')->get('site_copyright'),
            ])->setPaper('a4');

            return response()->stream(fn () => print($pdf->output()), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="profit_loss.pdf"',
            ]);
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

    private function validateFilters(Request $request): void
    {
        $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'group_by' => ['nullable', Rule::in(['day', 'month'])],
            'order_type' => ['nullable', Rule::in([OrderType::DELIVERY, OrderType::TAKEAWAY, OrderType::POS, OrderType::DINING_TABLE])],
            'source' => ['nullable', Rule::in([Source::WEB, Source::APP, Source::POS])],
            'payment_status' => ['nullable', Rule::in([PaymentStatus::PAID, PaymentStatus::UNPAID])],
        ]);
    }
}
