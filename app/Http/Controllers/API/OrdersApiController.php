<?php

namespace App\Http\Controllers\API;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Jobs\ProcessReferralBonusAfterReturnPeriod;
use App\Jobs\SendEmailJob;
use App\Models\Admin;
use App\Models\DeliveryBoy;
use App\Models\FundTransfer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\OrderStatusList;
use App\Models\Role;
use App\Models\Seller;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\DeliveryBoyTransaction;
use App\Models\ProductVariant;
use App\Notifications\OrderNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrdersApiController extends Controller
{
    public function getOrders(Request $request)
    {
        $limit = $request->input('per_page', 10);
        $offset = (($request->input('page', 0)) - 1) * $limit;
        $search = $request->input('search', '');

        $sellers = Seller::select('id', 'name')
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray();

        $ordersQuery = Order::select(
            'orders.id',
            'orders.mobile',
            'orders.total',
            'orders.delivery_charge',
            'orders.wallet_balance',
            'orders.final_total',
            'orders.remaining_final',
            'orders.payment_method',
            'orders.delivery_time',
            'orders.additional_charges',
            'orders.active_status',
            'users.name as user_name',
            DB::raw('(SELECT s.name FROM sellers s INNER JOIN order_items oi ON s.id = oi.seller_id WHERE oi.order_id = orders.id LIMIT 1) as seller_name')
        )
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.order_type', 'doorstep');

        // Only parse dates when actually provided
        if (isset($request->startDate) && $request->startDate != "" && isset($request->endDate) && $request->endDate != "") {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();

            // Use whereExists for better performance than join
            $ordersQuery = $ordersQuery->whereExists(function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                    ->from('order_items')
                    ->whereColumn('order_items.order_id', 'orders.id')
                    ->whereBetween('order_items.created_at', [$startDate, $endDate]);
            });
        }

        if (isset($request->startDeliveryDate) && $request->startDeliveryDate != "" && isset($request->endDeliveryDate) && $request->endDeliveryDate != "") {
            // Convert start and end dates from request to Y-m-d format
            $startDeliveryDate = date('Y-m-d', strtotime($request->startDeliveryDate));
            $endDeliveryDate = date('Y-m-d', strtotime($request->endDeliveryDate));

            // Use whereRaw for delivery_time filtering
            $ordersQuery = $ordersQuery->where(function ($query) use ($startDeliveryDate, $endDeliveryDate) {
                $query->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(orders.delivery_time, ' ', 1), '%d-%m-%Y') BETWEEN ? AND ?", [$startDeliveryDate, $endDeliveryDate]);
            });
        }

        if (isset($request->seller) && $request->seller != "") {
            // Use whereExists for seller filter to avoid join issues
            $ordersQuery = $ordersQuery->whereExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('order_items')
                    ->whereColumn('order_items.order_id', 'orders.id')
                    ->where('order_items.seller_id', $request->seller);
            });
        }

        if (isset($request->status) && $request->status != "") {
            $ordersQuery = $ordersQuery->where('orders.active_status', $request->status);
        }

        if ($search) {
            // Optimize search with better query structure
            $ordersQuery = $ordersQuery->where(function ($query) use ($search) {
                $query->where('orders.payment_method', 'like', "%{$search}%")
                    ->orWhere('orders.id', 'like', "%{$search}%")
                    ->orWhere('orders.delivery_charge', 'like', "%{$search}%")
                    ->orWhere('orders.wallet_balance', 'like', "%{$search}%")
                    ->orWhere('orders.remaining_final', 'like', "%{$search}%")
                    ->orWhere('orders.total', 'like', "%{$search}%")
                    ->orWhere('orders.delivery_time', 'like', "%{$search}%")
                    ->orWhereExists(function ($subQuery) use ($search) {
                        $subQuery->select(DB::raw(1))
                            ->from('users')
                            ->whereColumn('users.id', 'orders.user_id')
                            ->where('users.name', 'like', "%{$search}%");
                    })
                    ->orWhereExists(function ($subQuery) use ($search) {
                        $subQuery->select(DB::raw(1))
                            ->from('order_items')
                            ->leftJoin('sellers', 'order_items.seller_id', '=', 'sellers.id')
                            ->whereColumn('order_items.order_id', 'orders.id')
                            ->where('sellers.name', 'like', "%{$search}%");
                    })
                    ->orWhereExists(function ($subQuery) use ($search) {
                        $subQuery->select(DB::raw(1))
                            ->from('order_items')
                            ->whereColumn('order_items.order_id', 'orders.id')
                            ->where('order_items.active_status', 'like', "%{$search}%");
                    });
            });
        }

        // Get total count using a separate optimized query (without groupBy)
        $orders_total = (clone $ordersQuery)->count();

        // Apply ordering and pagination
        $orders = $ordersQuery->orderBy('orders.id', 'DESC')
            ->skip($offset)
            ->take($limit)
            ->get();

        // Process additional_charges more efficiently
        $orders->transform(function ($order) {
            if (!empty($order->additional_charges)) {
                if (is_string($order->additional_charges)) {
                    $decoded = json_decode($order->additional_charges, true);
                    $order->additional_charges = (is_array($decoded)) ? $decoded : [];
                } else {
                    $order->additional_charges = [];
                }
            } else {
                $order->additional_charges = [];
            }
            return $order;
        });

        $item_limit = $request->input('item_per_page', 10);
        $item_offset = (($request->input('item_page', 0)) - 1) * $item_limit;
        $data = array(
            "sellers" => $sellers,
            "orders" => $orders,
            "orders_total" => $orders_total
        );
        return CommonHelper::responseWithData($data);
    }

    public function getSelfPickupOrders(Request $request)
    {
        $limit = $request->input('per_page', 10);
        $offset = (($request->input('page', 0)) - 1) * $limit;
        $search = $request->input('search', '');

        $sellers = Seller::select('id', 'name')
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray();

        $ordersQuery = Order::select(
            'orders.id',
            'orders.mobile',
            'orders.total',
            'orders.delivery_charge',
            'orders.wallet_balance',
            'orders.final_total',
            'orders.remaining_final',
            'orders.payment_method',
            'orders.delivery_time',
            'orders.additional_charges',
            'orders.active_status',
            'users.name as user_name',
            DB::raw('(SELECT s.name FROM sellers s INNER JOIN order_items oi ON s.id = oi.seller_id WHERE oi.order_id = orders.id LIMIT 1) as seller_name')
        )
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.order_type', 'selfpickup');

        // Only parse dates when actually provided
        if (isset($request->startDate) && $request->startDate != "" && isset($request->endDate) && $request->endDate != "") {
            $startDate = Carbon::parse($request->startDate)->startOfDay();
            $endDate = Carbon::parse($request->endDate)->endOfDay();

            // Use whereExists for better performance than join
            $ordersQuery = $ordersQuery->whereExists(function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                    ->from('order_items')
                    ->whereColumn('order_items.order_id', 'orders.id')
                    ->whereBetween('order_items.created_at', [$startDate, $endDate]);
            });
        }

        if (isset($request->startDeliveryDate) && $request->startDeliveryDate != "" && isset($request->endDeliveryDate) && $request->endDeliveryDate != "") {
            // Convert start and end dates from request to Y-m-d format
            $startDeliveryDate = date('Y-m-d', strtotime($request->startDeliveryDate));
            $endDeliveryDate = date('Y-m-d', strtotime($request->endDeliveryDate));

            // Use whereRaw for delivery_time filtering
            $ordersQuery = $ordersQuery->where(function ($query) use ($startDeliveryDate, $endDeliveryDate) {
                $query->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(orders.delivery_time, ' ', 1), '%d-%m-%Y') BETWEEN ? AND ?", [$startDeliveryDate, $endDeliveryDate]);
            });
        }

        if (isset($request->seller) && $request->seller != "") {
            // Use whereExists for seller filter to avoid join issues
            $ordersQuery = $ordersQuery->whereExists(function ($query) use ($request) {
                $query->select(DB::raw(1))
                    ->from('order_items')
                    ->whereColumn('order_items.order_id', 'orders.id')
                    ->where('order_items.seller_id', $request->seller);
            });
        }

        if (isset($request->status) && $request->status != "") {
            $ordersQuery = $ordersQuery->where('orders.active_status', $request->status);
        }

        if ($search) {
            // Optimize search with better query structure
            $ordersQuery = $ordersQuery->where(function ($query) use ($search) {
                $query->where('orders.payment_method', 'like', "%{$search}%")
                    ->orWhere('orders.id', 'like', "%{$search}%")
                    ->orWhere('orders.delivery_charge', 'like', "%{$search}%")
                    ->orWhere('orders.wallet_balance', 'like', "%{$search}%")
                    ->orWhere('orders.remaining_final', 'like', "%{$search}%")
                    ->orWhere('orders.total', 'like', "%{$search}%")
                    ->orWhere('orders.delivery_time', 'like', "%{$search}%")
                    ->orWhereExists(function ($subQuery) use ($search) {
                        $subQuery->select(DB::raw(1))
                            ->from('users')
                            ->whereColumn('users.id', 'orders.user_id')
                            ->where('users.name', 'like', "%{$search}%");
                    })
                    ->orWhereExists(function ($subQuery) use ($search) {
                        $subQuery->select(DB::raw(1))
                            ->from('order_items')
                            ->leftJoin('sellers', 'order_items.seller_id', '=', 'sellers.id')
                            ->whereColumn('order_items.order_id', 'orders.id')
                            ->where('sellers.name', 'like', "%{$search}%");
                    })
                    ->orWhereExists(function ($subQuery) use ($search) {
                        $subQuery->select(DB::raw(1))
                            ->from('order_items')
                            ->whereColumn('order_items.order_id', 'orders.id')
                            ->where('order_items.active_status', 'like', "%{$search}%");
                    });
            });
        }

        // Get total count using a separate optimized query (without groupBy)
        $orders_total = (clone $ordersQuery)->count();

        // Apply ordering and pagination
        $orders = $ordersQuery->orderBy('orders.id', 'DESC')
            ->skip($offset)
            ->take($limit)
            ->get();

        // Process additional_charges more efficiently
        $orders->transform(function ($order) {
            if (!empty($order->additional_charges)) {
                if (is_string($order->additional_charges)) {
                    $decoded = json_decode($order->additional_charges, true);
                    $order->additional_charges = (is_array($decoded)) ? $decoded : [];
                } else {
                    $order->additional_charges = [];
                }
            } else {
                $order->additional_charges = [];
            }
            return $order;
        });

        $item_limit = $request->input('item_per_page', 10);
        $item_offset = (($request->input('item_page', 0)) - 1) * $item_limit;
        $data = array(
            "sellers" => $sellers,
            "orders" => $orders,
            "orders_total" => $orders_total
        );
        return CommonHelper::responseWithData($data);
    }

    public function view($id)
    {
        $data = CommonHelper::getOrderDetails($id);
        if (!$data["order"]) {
            return CommonHelper::responseError("Order Not found!");
        }

        if ($data["order"]->order_type == 'selfpickup') {
            $pickupStatus = OrderStatus::where('order_id', $id)
                ->where('status', OrderStatusList::$selfPickupPicked)
                ->orderBy('created_at', 'desc')
                ->first();

            $data["pickup_date"] = $pickupStatus ? $pickupStatus->created_at : null;
        }

        $deliveryBoys = DeliveryBoy::select('id', 'name')->where('city_id', $data["order"]->city_id)->where('status', 1)->get();

        $data["deliveryBoys"] = $deliveryBoys;
        return CommonHelper::responseWithData($data);
    }


    public function generateOrderInvoice(Request $request)
    {
        $data = CommonHelper::getOrderDetails($request->order_id, true);
        if (!$data["order"]) {
            return CommonHelper::responseError("Order Not found!");
        }
        CommonHelper::AdditionalChargesArray($data['order']);
        $invoice = CommonHelper::generateOrderInvoice($data);
        return CommonHelper::responseWithData($invoice);
    }
    public function downloadOrderInvoice(Request $request)
    {
        $data = CommonHelper::getOrderDetails($request->order_id, true);
        if (!$data["order"]) {
            return CommonHelper::responseError("Order Not found!");
        }
        CommonHelper::AdditionalChargesArray($data['order']);
        return CommonHelper::downloadOrderInvoice($request->order_id);
    }

    public function delete(Request $request)
    {
        if (isset($request->id)) {
            $order = Order::find($request->id);
            if ($order) {
                $order->delete();
                return CommonHelper::responseSuccess("Order Deleted Successfully!");
            } else {
                return CommonHelper::responseSuccess("Order Already Deleted!");
            }
        }
    }

    public function deleteItem(Request $request)
    {
        if (isset($request->id)) {
            $orderItem = OrderItem::find($request->id);
            if ($orderItem) {
                $orderItem->delete();
                return CommonHelper::responseSuccess("Order Item Deleted Successfully!");
            } else {
                return CommonHelper::responseSuccess("Order Item Already Deleted!");
            }
        }
    }

    public function getWeeklySales()
    {
        $year = date("Y");
        $curdate = date('Y-m-d');
        $orders = Order::select(DB::raw('ROUND(SUM(remaining_final), 2) AS total_sale'), DB::raw('DATE(created_at) AS order_date'))
            ->where(DB::raw('YEAR(created_at)'), '=', $year)
            ->where(DB::raw('DATE(created_at)'), '<=', $curdate);

        $orders = $orders->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'), 'DESC')
            ->limit(7)->get();
        return CommonHelper::responseWithData($orders);
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'status_id' => 'required',
        ], [
            'order_id.required' => 'The Order id field is required.',
            'status_id.required' => 'The status field is required.',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }
        $order = Order::find($request->order_id);
        if (empty($order)) {
            return CommonHelper::responseError("Order Not found!");
        }
        $selectedStatus = OrderStatusList::where('id', $request->status_id)->value('status');

        if ($order->active_status == $request->status_id) {
            return CommonHelper::responseError("This Order is already " . $selectedStatus . "!");
        }

        if ($order->active_status == 6 && $request->status_id < 6) {
            return CommonHelper::responseError("This Order is Delivered");
        }

        if ($order->active_status == OrderStatusList::$paymentPending) {
            return CommonHelper::responseError("Payment is pending. Without payment order can not receive");
        }

        if ($order->active_status == OrderStatusList::$returned || $order->active_status == OrderStatusList::$cancelled) {
            return CommonHelper::responseError("Order is Cancelled OR Returned.");
        }

        if (auth()->user()->role_id != Role::$roleSuperAdmin) {
            if ($order->active_status > $request->status_id) {
                return CommonHelper::responseError("You can not update this order status to " . $selectedStatus . "!");
            }
        }

        DB::beginTransaction();
        try {

            if ($order->active_status != $request->status_id) {

                if (isset($request->delivery_boy_id) && $request->delivery_boy_id != "" && $request->delivery_boy_id != 0) {

                    // Delivery Boy cash collection add and cash_received update with update balance of delivery boy start
                    if ($request->status_id == OrderStatusList::$delivered) {

                        $deliveryBoy = DeliveryBoy::find($request->delivery_boy_id);

                        $deliveryBoy->balance = floatval($deliveryBoy->balance) + floatval($order->delivery_boy_bonus_amount);

                        CommonHelper::addFundTransfers($deliveryBoy->id, $order->delivery_boy_bonus_amount, FundTransfer::$typeCredit);

                        if ($order->payment_method == DeliveryBoyTransaction::$paymentTypeCod) {

                            $transactionData = [
                                'user_id'           => $order->user_id,
                                'order_id'          => $order->id,
                                'delivery_boy_id'   => $deliveryBoy->id,
                                'type'              => $order->payment_method,
                                'amount'            => $order->remaining_final,
                                'status'            => Transaction::$statusSuccess,
                                'message'           => "Delivery boy " . OrderStatusList::$orderDelivered . " this order. Order payment method was " . Transaction::$paymentTypeCod,
                                'transaction_date'  => now(), // Cleaner than date('Y-m-d H:i:s')
                            ];

                            $transaction = DeliveryBoyTransaction::create($transactionData);

                            if (!$transaction) {
                                \Log::error("Failed to save delivery boy transaction", $transactionData);
                            }

                            $order->transaction_id = $transaction->id ?? 0;

                            $deliveryBoy->cash_received = floatval($deliveryBoy->cash_received) + floatval($order->remaining_final);
                        }

                        $deliveryBoy->save();
                    }

                    $order->delivery_boy_id = $request->delivery_boy_id;
                }
                //refer earn bonus amount update start

                $order->active_status = $request->status_id;
                $order->save();

                if ($request->status_id == OrderStatusList::$delivered) {
                    $order = Order::with('user', 'items.productVariant.product')->find($request->order_id);
                    $user = $order->user;

                    $referralMinOrderAmount = Setting::get_value('referral_min_order_amount');
                    $referralCredit = Setting::get_value('referral_credit_first_order');

                    if ($user && $user->friends_code && $order->final_total >= $referralMinOrderAmount) {

                        // Check if this is the user's FIRST delivered order
                        $deliveredOrdersCount = Order::where('user_id', $user->id)
                            ->where('active_status', OrderStatusList::$delivered)
                            ->where('id', '!=', $order->id)  // Exclude current order
                            ->count();

                        if ($deliveredOrdersCount === 0) {  // This means it's the first order

                            $now = Carbon::now();

                            $canCreditReferral = true;

                            foreach ($order->items as $item) {
                                $product = $item->productVariant->product ?? null;
                                if ($product && $product->return_status == 1 && $product->return_days > 0) {
                                    $canCreditReferral = false;
                                }
                            }

                            if ($canCreditReferral) {
                                // Credit referral bonus immediately
                                $referrer = User::where('referral_code', $user->friends_code)->first();

                                if ($referrer) {
                                    $new_balance = floatval($referrer->balance) + floatval($referralCredit);
                                    CommonHelper::updateUserWalletBalance($new_balance, $referrer->id);
                                    CommonHelper::addWalletTransaction($order->id, 0, $referrer->id, 'credit', $referralCredit, 'Refer Earn First Order Bonus');
                                }
                            } else {
                                // Queue a job to check again after max return days
                                $maxReturnDays = $order->items->filter(function ($item) {
                                    $product = $item->productVariant->product ?? null;
                                    return $product && $product->return_status == 1 && $product->return_days > 0;
                                })->max(function ($item) {
                                    return $item->productVariant->product->return_days;
                                }) ?? 0;

                                $deliveredStatus = OrderStatus::where('order_id', $order->id)
                                    ->where('status', OrderStatusList::$delivered)
                                    ->orderBy('created_at', 'desc')
                                    ->first();

                                $deliveredAt = $deliveredStatus ? Carbon::parse($deliveredStatus->created_at) : Carbon::parse($order->updated_at);
                                $now = Carbon::now();

                                if ($maxReturnDays > 0) {

                                    $returnPeriodEnd = $deliveredAt->copy()->addDays($maxReturnDays);
                                    $delay = $now->diffInSeconds($returnPeriodEnd, false); // false: signed diff


                                    if ($delay > 0) {
                                        ProcessReferralBonusAfterReturnPeriod::dispatch($order->id)->delay(Carbon::now()->addSeconds($delay));
                                    }
                                }
                            }
                        }
                    }
                }

                $excludedStatuses = [OrderStatusList::$cancelled, OrderStatusList::$returned];

                // Update the order items
                $query = OrderItem::where("order_id", $request->order_id)
                    ->whereNotIn("active_status", $excludedStatuses)
                    ->update(['active_status' => $request->status_id]);

                $orderStatus = array();
                $orderStatus["order_id"] = $request->order_id;
                $orderStatus['order_item_id'] = 0;
                $orderStatus["status"] = $request->status_id;
                $orderStatus["created_by"] = auth()->user()->id;
                $orderStatus["user_type"] = auth()->user()->role_id;
                CommonHelper::setOrderStatus($orderStatus);
            } else {
                $status = OrderStatusList::find($request->status_id);
                return CommonHelper::responseError("Status is already " . $status->status);
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::info("Error : " . $e->getMessage());
            DB::rollBack();
            throw $e;
            return CommonHelper::responseError("Something Went Wrong!");
        }

        $order = Order::with('items')->where("id", $request->order_id)->first();

        if (!empty($order)) {
            log::info("order", [$order]);

            try {
                CommonHelper::sendNotificationOrderStatus($order);
                $admins = Admin::get();

                foreach ($admins as $admin) {
                    $admin->notify(new OrderNotification($order->id, (string)$request->status_id));
                }
            } catch (\Exception $e) {
            }

            try {
                dispatch(new SendEmailJob($order))->afterResponse();
            } catch (\Exception $e) {
                Log::error("Update order status by delivery boy Send mail error :", [$e->getMessage()]);
            }

            try {
                CommonHelper::sendSmsOrderStatus($order, $order->active_status);
            } catch (\Exception $e) {
                Log::error("Update order status by delivery boy Send SMS error :", [$e->getMessage()]);
            }
        }


        return CommonHelper::responseSuccess("Order Updated Successfully!");
    }

    public function assignDeliveryBoy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'delivery_boy_id' => 'required',
        ], [
            'order_id.required' => 'The Order id field is required.',
            'delivery_boy_id.required' => 'The delivery boy field is required.',
        ]);

        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        $deliveryBoy = DeliveryBoy::find($request->delivery_boy_id);
        if (empty($deliveryBoy)) {
            return CommonHelper::responseSuccess("Delivery Boy Not Found!");
        }
        $order = Order::find($request->order_id);

        if ($order) {
            if ($order->delivery_boy_id == $request->delivery_boy_id) {
                return CommonHelper::responseError("This delivery boy already assign!");
            }
            if ($order->active_status == OrderStatusList::$paymentPending) {
                return CommonHelper::responseError("Payment is pending. Without payment order can not receive");
            }

            $final_total = floatval($order->total);

            $bonus_type = $deliveryBoy->bonus_type;
            $bonus_details['final_total'] = $final_total;
            $bonus_details['bonus_type'] = $bonus_type;
            $bonus_amount = 0;
            if ($bonus_type == DeliveryBoy::$bonusCommission) {

                $bonus_percentage = floatval($deliveryBoy->bonus_percentage);
                $bonus_min_amount = floatval($deliveryBoy->bonus_min_amount);
                $bonus_max_amount = floatval($deliveryBoy->bonus_max_amount);

                $bonus_amount = floatval(($final_total *  $bonus_percentage) / 100);

                if ($bonus_amount < $bonus_min_amount && $bonus_min_amount != 0) {
                    $bonus_amount = $bonus_min_amount;
                }

                if ($bonus_amount > $bonus_max_amount && $bonus_max_amount != 0) {
                    $bonus_amount = $bonus_max_amount;
                }

                $bonus_details['bonus_type_name'] = DeliveryBoy::$commission;
                $bonus_details['bonus_percentage'] = $bonus_percentage;
                $bonus_details['bonus_min_amount'] = $bonus_min_amount;
                $bonus_details['bonus_max_amount'] = $bonus_max_amount;
                $bonus_details['bonus_amount'] = $bonus_amount;
            } else {
                $bonus_details['bonus_type_name'] = DeliveryBoy::$fixed;
            }
            $bonus_details['bonus_amount'] = $bonus_amount;

            $order->delivery_boy_bonus_details = $bonus_details;
            $order->delivery_boy_bonus_amount = $bonus_amount;

            $order->delivery_boy_id = $request->delivery_boy_id;
            $order->save();

            try {
                CommonHelper::sendMailOrderStatus($order, true);
                CommonHelper::sendNotificationOrderAssignDeliveryBoy($order);
            } catch (\Exception $e) {
                Log::error("Delivery boy assigned on order Send mail error :", [$e->getMessage()]);
            }

            return CommonHelper::responseSuccess("Delivery boy assigned Successfully for this order!");
        } else {
            return CommonHelper::responseError("Order Not found!");
        }
    }

    public function updateItemsStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required',
            'status_id' => 'required',
        ], [
            'ids.required' => 'The Item id field is required.',
            'status_id.required' => 'The status field is required.',
        ]);
        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }
        $ids = explode(",", $request->ids);
        foreach ($ids as $key => $id) {
            $orderItem = OrderItem::find($id);
            $orderItem->active_status = $request->status_id;
            $orderItem->save();

            $orderStatus = array();
            $orderStatus["order_id"] = $orderItem->order_id;
            $orderStatus["order_item_id"] = $id;
            $orderStatus["status"] = $request->status_id;
            $orderStatus["created_by"] = auth()->user()->id;
            $orderStatus["user_type"] = auth()->user()->role_id;
            CommonHelper::setOrderStatus($orderStatus);
        }
        return CommonHelper::responseSuccess("Order Updated Successfully!");
    }

    public function updateSelfPickupOrderStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'status_id' => 'required',
            'order_item_id' => 'nullable|integer',
        ], [
            'order_id.required' => 'The Order id field is required.',
            'status_id.required' => 'The status field is required.',
        ]);

        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        $order = Order::find($request->order_id);
        if (empty($order)) {
            return CommonHelper::responseError("Order Not found!");
        }

        if ($order->order_type !== 'selfpickup') {
            return CommonHelper::responseError("This is not a self pickup order!");
        }

        if (isset($request->order_item_id) && $request->order_item_id != "") {
            return $this->handleItemReturn($request, $order);
        }

        $selectedStatus = OrderStatusList::where('id', $request->status_id)->value('status');

        if ($order->active_status == $request->status_id) {
            return CommonHelper::responseError("This Order is already " . $selectedStatus . "!");
        }

        $selfPickupStatuses = [
            OrderStatusList::$selfPickupPending,
            OrderStatusList::$selfPickupReady,
            OrderStatusList::$selfPickupPicked,
        ];

        if (!in_array($request->status_id, $selfPickupStatuses)) {
            return CommonHelper::responseError("Invalid status for self pickup order!");
        }

        if ($order->active_status == OrderStatusList::$selfPickupPicked) {
            return CommonHelper::responseError("Order is already picked up!");
        }
        if ($order->active_status == OrderStatusList::$returned) {
            return CommonHelper::responseError("Order is already returned!");
        }
        if ($order->active_status == OrderStatusList::$cancelled) {
            return CommonHelper::responseError("Order is already cancelled!");
        }

        if ($order->active_status > $request->status_id) {
            return CommonHelper::responseError("You cannot update this order status to " . $selectedStatus . "!");
        }

        if ($order->active_status == OrderStatusList::$paymentPending) {
            return CommonHelper::responseError("Payment is pending. Without payment order can not be processed");
        }

        DB::beginTransaction();
        try {
            if ($order->active_status != $request->status_id) {
                $order->active_status = $request->status_id;
                $order->save();

                $excludedStatuses = [OrderStatusList::$cancelled];
                $query = OrderItem::where("order_id", $request->order_id)
                    ->whereNotIn("active_status", $excludedStatuses)
                    ->update(['active_status' => $request->status_id]);

                $orderStatus = array();
                $orderStatus["order_id"] = $request->order_id;
                $orderStatus['order_item_id'] = 0;
                $orderStatus["status"] = $request->status_id;
                $orderStatus["created_by"] = auth()->user()->id;
                $orderStatus["user_type"] = auth()->user()->role_id;
                CommonHelper::setOrderStatus($orderStatus);
            } else {
                $status = OrderStatusList::find($request->status_id);
                return CommonHelper::responseError("Status is already " . $status->status);
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::info("Error : " . $e->getMessage());
            DB::rollBack();
            throw $e;
            return CommonHelper::responseError("Something Went Wrong!");
        }

        $order = Order::with('items')->where("id", $request->order_id)->first();

        if (!empty($order)) {
            try {
                CommonHelper::sendNotificationOrderStatus($order);
                $admins = Admin::get();

                foreach ($admins as $admin) {
                    $admin->notify(new OrderNotification($order->id, (string)$request->status_id));
                }
            } catch (\Exception $e) {
                Log::error("Self pickup order status notification error: " . $e->getMessage());
            }

            try {
                dispatch(new SendEmailJob($order))->afterResponse();
            } catch (\Exception $e) {
                Log::error("Self pickup order status email error: " . $e->getMessage());
            }

            try {
                CommonHelper::sendSmsOrderStatus($order, $order->active_status);
            } catch (\Exception $e) {
                Log::error("Self pickup order status SMS error: " . $e->getMessage());
            }
        }

        return CommonHelper::responseSuccess("Self Pickup Order Updated Successfully!");
    }

    private function handleItemReturn($request, $order)
    {
        $orderItem = OrderItem::find($request->order_item_id);
        if (empty($orderItem)) {
            return CommonHelper::responseError("Order item not found!");
        }

        if ($request->status_id != OrderStatusList::$returned) {
            return CommonHelper::responseError("Invalid status for product return!");
        }

        if ($orderItem->active_status == OrderStatusList::$returned || $orderItem->active_status == OrderStatusList::$cancelled) {
            return CommonHelper::responseError("This product is already returned or cancelled!");
        }

        if ($order->active_status != OrderStatusList::$selfPickupPicked) {
            return CommonHelper::responseError("Order must be picked up before returning products!");
        }

        DB::beginTransaction();
        try {
            $orderItem->active_status = $request->status_id;
            $orderItem->cancellation_reason = $request->reason ?? 'Product returned by customer';
            $orderItem->save();

            $remainingActiveItems = OrderItem::where("order_id", $order->id)
                ->where('id', '!=', $orderItem->id)
                ->where('active_status', '!=', OrderStatusList::$cancelled)
                ->where('active_status', '!=', OrderStatusList::$returned)
                ->count();

            if ($remainingActiveItems == 0) {
                $additional_charges = json_decode($order->additional_charges, true) ?? [];
                $additional_charges_total = array_sum(array_column($additional_charges, 'amount'));

                $order->active_status = OrderStatusList::$returned;
                $order->remaining_total = 0;
                $order->final_total = $additional_charges_total;
                $order->remaining_final = $additional_charges_total;
                $order->save();
            } else {
                $additional_charges = json_decode($order->additional_charges, true) ?? [];
                $additional_charges_total = array_sum(array_column($additional_charges, 'amount'));

                $order->remaining_total = floatval($order->remaining_total) - floatval($orderItem->sub_total);
                $order->remaining_final = floatval($order->remaining_total) + $additional_charges_total;

                $order->final_total = $order->remaining_final;

                $order->save();
            }

            $product_variant_id = $orderItem->product_variant_id;
            $product_variant = ProductVariant::where('id', $product_variant_id)->first();

            if ($product_variant) {
                $new_stock_value = $product_variant->stock + $orderItem->quantity;
                $product_variant->stock = $new_stock_value;
                $product_variant->save();
            }

            try {
                CommonHelper::sendSmsOrderStatus($orderItem, 9);
                CommonHelper::sendOrderItemStatusMailNotification($orderItem, 'order_item_status_update');
            } catch (\Exception $e) {
                Log::error("Error sending notifications for item return: " . $e->getMessage());
            }

            DB::commit();
        } catch (\Exception $e) {
            Log::info("Error returning product: " . $e->getMessage());
            DB::rollBack();
            return CommonHelper::responseError("Something went wrong while returning the product!");
        }

        return CommonHelper::responseSuccess("Product returned successfully");
    }
}
