<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {
    }

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method

        $is_email_merchant = $merchant->user()->where('email', $email)->first();
        if ($is_email_merchant) {
        } else {

            $merchant->update([
                'display_name' => $name,
                'default_commission_rate' => $commissionRate
            ]);

            $user = $merchant->user()->create([
                'name' => $name,
                'email' => $email,
                'type' => User::TYPE_AFFILIATE
            ]);
        }

        $affiliate =  Affiliate::create([
            'user_id' => isset($user) ? $user->id : $merchant->user_id,
            'merchant_id' => $merchant->id,
            'commission_rate' => $commissionRate,
            'discount_code' => $this->apiService->createDiscountCode($merchant)['code']
        ]);
        Mail::to($email)->send(new AffiliateCreated($affiliate));
        //$merchant->user()->first()->update(['email' => $email, 'name' => $name, 'type' => User::TYPE_MERCHANT]);
        return $affiliate;
    }
}
