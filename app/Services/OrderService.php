<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {
    }

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method

        $merchant = Merchant::where('domain', $data['merchant_domain'])->first();
        /*
            $affiliate = Affiliate::with([
                'user' =>
                function ($query, $data) {
                    $query->where('email', $data->email);
                }
            ])->first();
        */
        $affiliate = Affiliate::where('merchant_id', $merchant->id)->first();

        $commission_owed = $data['subtotal_price'] * (isset($affiliate) ? $affiliate->commission_rate : 1);
        Log::info($commission_owed);

        Log::info("here is it");
        Log::info(Order::all());
        Log::info('Order id: ' . $data['order_id']);
        $order = Order::find($data['order_id']);
        if (!$order)

            Order::updateOrCreate([
                'subtotal' => $data['subtotal_price'],
                'affiliate_id' => isset($affiliate) ? $affiliate->id : 0,
                'merchant_id' => $merchant->id,
                'commission_owed' => $commission_owed,
                'order_id' => $data['order_id']
            ]);
        Log::info(Order::all());
        $affiliate = $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], $merchant->default_commission_rate);
    }
}
