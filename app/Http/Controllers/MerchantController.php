<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MerchantController extends Controller
{
    public function __construct(
        protected MerchantService $merchantService
    ) {
    }

    /**
     * Useful order statistics for the merchant API.
     * 
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form 
     * {count: total number of orders in range, 
     * commission_owed: amount of unpaid commissions for orders with an affiliate,
     *  revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {


        //$merchant = $this->merchantService->register(array('domain' => 'example.com', 'name' => 'Abdullah', 'email' => 'ima322s@gm.com', 'api_key' => '7263126371623'));
        //$merchant = $this->merchantService->updateMerchant(User::find(4), array('domain' => 'newdomain.com', 'name' => 'hello Abdullah new', 'email' => 'this@gm.com', 'api_key' => '18371'));
        //$merchant = $this->merchantService->findMerchantByEmail('this@gm.com');

        // TODO: Complete this method

        $orders = Order::whereBetween('created_at', [$request->get('from'), $request->get('to')]);

        $revenue = $orders->sum('subtotal');
        $count = $orders->get()->count();

        return response()->json(array(
            'count' => $count,
            'commissions_owed' => $orders->whereNotNull('affiliate_id')->where('payout_status', Order::STATUS_UNPAID)->sum('commission_owed'),
            'revenue' => $revenue
        ));
    }
}
