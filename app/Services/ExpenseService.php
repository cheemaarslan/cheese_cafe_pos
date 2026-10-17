<?php

namespace App\Services;

use App\Http\Requests\ExpenseRequest;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;
use App\Models\Expense;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExpenseService
{
    private const SORTABLE_COLUMNS = [
        'id',
        'title',
        'category',
        'amount',
        'expense_date',
        'created_at',
    ];

    public function list(PaginateRequest $request)
    {
        try {
            $query = Expense::query();

            foreach (['title', 'category'] as $field) {
                if ($request->filled($field)) {
                    $query->where($field, 'like', '%' . $request->input($field) . '%');
                }
            }

            if ($request->filled('from_date')) {
                $query->whereDate('expense_date', '>=', date('Y-m-d', strtotime($request->input('from_date'))));
            }

            if ($request->filled('to_date')) {
                $query->whereDate('expense_date', '<=', date('Y-m-d', strtotime($request->input('to_date'))));
            }

            $orderColumn = in_array($request->input('order_column'), self::SORTABLE_COLUMNS, true)
                ? $request->input('order_column')
                : 'expense_date';
            $orderType = strtolower((string) $request->input('order_type')) === 'asc' ? 'asc' : 'desc';

            $query->orderBy($orderColumn, $orderType)->orderBy('id', $orderType);

            return $request->boolean('paginate')
                ? $query->paginate($request->integer('per_page', 10))
                : $query->get();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function store(ExpenseRequest $request): Expense
    {
        try {
            return Expense::create($request->validated() + ['created_by' => Auth::id()]);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function update(ExpenseRequest $request, Expense $expense): Expense
    {
        try {
            $expense->update($request->validated());

            return $expense->refresh();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    public function destroy(Expense $expense): void
    {
        try {
            $expense->delete();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
