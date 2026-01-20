<?php

namespace App\Jobs;

use App\Helpers\CommonHelper;
use App\Models\UserProductRequest;
use App\Models\UserToken;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendProductRequestNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productRequest;
    protected $type;

    public function __construct(UserProductRequest $productRequest, $type = 'status_update')
    {
        $this->productRequest = $productRequest;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->sendPushNotification();
            $this->sendEmailNotification();

        } catch (\Exception $e) {
            Log::error('Error in SendProductRequestNotificationJob: ' . $e->getMessage());
        }
    }

    /**
     * Send push notification to customer
     */
    private function sendPushNotification()
    {
        try {
            $customer = $this->productRequest->customer;
            $status = $this->productRequest->status;
            $productName = $this->productRequest->product ? $this->productRequest->product->name : 'N/A';

            $title = "Product Request " . ucfirst($status);
            $message = "";
            
            if ($status === 'accepted') {
                $message = "Great news! Your product request has been accepted. Product: {$productName}.";
            } else {
                $message = "We regret to inform you that your product request has been rejected.";
            }

            $userTokens = UserToken::where('user_id', $customer->id)
                ->where('type', 'customer')
                ->get()
                ->pluck('fcm_token', 'platform')
                ->toArray();

            if (!empty($userTokens)) {
                CommonHelper::sendNotification($userTokens, $title, $message, 'product_request_status', $this->productRequest->id);
            }

        } catch (\Exception $e) {
            Log::error('Error sending push notification: ' . $e->getMessage());
        }
    }

    /**
     * Send email notification to customer
     */
    private function sendEmailNotification()
    {
        try {
            $customer = $this->productRequest->customer;
            $status = $this->productRequest->status;
            $appName = Setting::get_value('app_name');

            $subject = 'Product Request ' . ucfirst($status);
            
            $data = [
                'customer' => $customer,
                'productRequest' => $this->productRequest,
                'status' => $status,
                'app_name' => $appName,
                'subject' => $subject,
                'type' => 'product_request_status',
                'name' => $customer->name
            ];

            CommonHelper::sendMail($customer->email, $subject, $data);

        } catch (\Exception $e) {
            Log::error('Error sending email notification: ' . $e->getMessage());
        }
    }
}
