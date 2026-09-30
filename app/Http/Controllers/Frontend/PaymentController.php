<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\Activity;
use App\Enums\PaymentStatus;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Libraries\AppLibrary;
use App\Models\Currency;
use App\Models\CustomerLoyaltyPoint;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\ThemeSetting;
use App\Services\AdminNotificationService;
use App\Services\PaymentManagerService;
use Illuminate\Http\Request;
use Smartisan\Settings\Facades\Settings;

class PaymentController extends Controller
{
    private PaymentManagerService $paymentManagerService;

    private AdminNotificationService $adminNotificationService;

    public function __construct(PaymentManagerService $paymentManagerService, AdminNotificationService $adminNotificationService)
    {
        $this->paymentManagerService = $paymentManagerService;
        $this->adminNotificationService = $adminNotificationService;
    }

    public function index(
        Order $order
    ): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse {
        $credit = false;
        $paymentGateways = PaymentGateway::with('gatewayOptions')->whereNotIn('id', [1])->where(['status' => Activity::ENABLE])->get();
        $company = Settings::group('company')->all();
        $logo = ThemeSetting::where(['key' => 'theme_logo'])->first();
        $faviconLogo = ThemeSetting::where(['key' => 'theme_favicon_logo'])->first();
        $currency = Currency::findOrFail(Settings::group('site')->get('site_default_currency'));
        if ($order?->user?->balance >= $order->total) {
            $credit = true;
        }

        if (blank($order->transaction) && $order->payment_status === PaymentStatus::UNPAID) {
            return view('payment', [
                'company' => $company,
                'logo' => $logo,
                'currency' => $currency,
                'faviconLogo' => $faviconLogo,
                'paymentGateways' => $paymentGateways,
                'order' => $order,
                'creditAmount' => AppLibrary::currencyAmountFormat($order?->user?->balance),
                'credit' => $credit,
            ]);
        }

        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }

    public function payment(Order $order, PaymentRequest $request)
    {
        if ($this->paymentManagerService->gateway($request->paymentMethod)->status()) {
            $className = 'App\\Http\\PaymentGateways\\PaymentRequests\\'.ucfirst($request->paymentMethod);
            $gateway = new $className;
            $request->validate($gateway->rules());

            return $this->paymentManagerService->gateway($request->paymentMethod)->payment($order, $request);
        } else {
            return redirect()->route('payment.index', ['order' => $order])->with(
                'error',
                trans('all.message.payment_gateway_disable')
            );
        }
    }

    public function success(PaymentGateway $paymentGateway, Order $order, Request $request)
    {

        $this->adminNotificationService->createForAllAdmins('new_order', [
            'message' => 'New order received: #'.$order->order_serial_no,
            'url' => '/admin/online-orders/show/'.$order->id,
            'order_id' => $order->id,
            'order_serial_no' => $order->order_serial_no,
            'order_type' => 'online',
        ]);

        return $this->paymentManagerService->gateway($paymentGateway->slug)->success($order, $request);
    }

    public function fail(PaymentGateway $paymentGateway, Order $order, Request $request)
    {
        return $this->paymentManagerService->gateway($paymentGateway->slug)->fail($order, $request);
    }

    public function cancel(PaymentGateway $paymentGateway, Order $order, Request $request)
    {
        return $this->paymentManagerService->gateway($paymentGateway->slug)->cancel($order, $request);
    }

    public function successful(
        Order $order
    ): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse {
        $company = Settings::group('company')->all();
        $logo = ThemeSetting::where(['key' => 'theme_logo'])->first();
        $faviconLogo = ThemeSetting::where(['key' => 'theme_favicon_logo'])->first();

        $this->UpdateLoyaltyPoints($order);

        if (! blank($order->transaction)) {
            return view('paymentSuccess', [
                'company' => $company,
                'logo' => $logo,
                'faviconLogo' => $faviconLogo,
                'order' => $order,
            ]);
        }

        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }

    private function UpdateLoyaltyPoints(Order $order): void
    {
        // 1. Get Loyalty Points based on order
        $points = CustomerLoyaltyPoint::where('order_id', $order->id)->first();

        if ($points) {
            $points->status = Status::ACTIVE ?? 5;
            $points->save();
        }
    }
}
