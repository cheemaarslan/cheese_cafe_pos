<?php

namespace App\Services;

use Exception;
use App\Models\LoyaltyPoint;
use App\Http\Requests\LoyaltyPointRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PaginateRequest;
use App\Libraries\QueryExceptionLibrary;

class LoyaltyPointService
{
    protected array $loyaltyPointFilter = [
        'percentage',
        'status'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            return \Smartisan\Settings\Facades\Settings::group('loyalty_point')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(LoyaltyPointRequest $request)
    {
        try {
            \Smartisan\Settings\Facades\Settings::group('loyalty_point')->set($request->validated());
            return \Smartisan\Settings\Facades\Settings::group('loyalty_point')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(LoyaltyPointRequest $request, LoyaltyPoint $loyaltyPoint)
    {
        try {
            \Smartisan\Settings\Facades\Settings::group('loyalty_point')->set($request->validated());
            return \Smartisan\Settings\Facades\Settings::group('loyalty_point')->all();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(LoyaltyPoint $loyaltyPoint): void
    {
        try {
            \Smartisan\Settings\Facades\Settings::group('loyalty_point')->forget(['percentage', 'status']);
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(LoyaltyPoint $loyaltyPoint): LoyaltyPoint
    {
        try {
            $settings = \Smartisan\Settings\Facades\Settings::group('loyalty_point')->all();
            $loyaltyPoint->fill($settings);
            return $loyaltyPoint;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception(QueryExceptionLibrary::message($exception), 422);
        }
    }
}
