<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Models\Setting;
use App\Helpers\CommonHelper;
use App\Models\OrderStatusList;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessReferralBonusAfterReturnPeriod implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        $order = Order::with('user')->find($this->orderId);
        if (!$order) {
            return;
        }
        $user = $order->user;
        if (!$user || !$user->friends_code) {
            return;
        }

        $referralMinOrderAmount = Setting::get_value('referral_min_order_amount');
        $referralCredit = Setting::get_value('referral_credit_first_order');

        if ($order->final_total < $referralMinOrderAmount) {
            return;
        }

        // Check if this is the user's FIRST delivered order
        $deliveredOrdersCount = Order::where('user_id', $user->id)
            ->where('active_status', OrderStatusList::$delivered)
            ->where('id', '!=', $order->id)
            ->count();

        if ($deliveredOrdersCount > 0) {
            return;
        }

        $referrer = User::where('referral_code', $user->friends_code)->first();

        if ($referrer) {
            $new_balance = floatval($referrer->balance) + floatval($referralCredit);
            CommonHelper::updateUserWalletBalance($new_balance, $referrer->id);
            CommonHelper::addWalletTransaction($order->id, 0, $referrer->id, 'credit', $referralCredit, 'Refer Earn First Order Bonus');
        }
    }
}

