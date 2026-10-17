<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ExpenseRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Services\ExpenseService;
use Exception;

class ExpenseController extends AdminController
{
    public function __construct(private ExpenseService $expenseService)
    {
        parent::__construct();
        $this->middleware(['permission:expenses'])->only('index', 'show');
        $this->middleware(['permission:expenses_create'])->only('store');
        $this->middleware(['permission:expenses_edit'])->only('update');
        $this->middleware(['permission:expenses_delete'])->only('destroy');
    }

    public function index(PaginateRequest $request)
    {
        try {
            return ExpenseResource::collection($this->expenseService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ExpenseRequest $request)
    {
        try {
            return new ExpenseResource($this->expenseService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Expense $expense)
    {
        return new ExpenseResource($expense);
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        try {
            return new ExpenseResource($this->expenseService->update($request, $expense));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Expense $expense)
    {
        try {
            $this->expenseService->destroy($expense);

            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
