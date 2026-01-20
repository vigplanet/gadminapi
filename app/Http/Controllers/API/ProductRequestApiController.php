<?php

namespace App\Http\Controllers\API;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\UserProductRequest;
use App\Models\Setting;
use App\Models\UserToken;
use App\Models\Product;
use App\Models\Seller;
use App\Jobs\SendProductRequestNotificationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProductRequestApiController extends Controller
{
    /**
     * Create a new product request
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'nullable|string|max:250',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        if (empty($request->description) && !$request->hasFile('image')) {
            return CommonHelper::responseError('Either description or image must be provided.');
        }

        try {
            $productRequest = new UserProductRequest();
            $productRequest->customer_id = auth()->user()->id;
            $productRequest->description = $request->description;
            $productRequest->status = UserProductRequest::STATUS_PENDING;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = time() . '_' . rand(1111, 99999) . '.' . $file->getClientOriginalExtension();
                $imagePath = Storage::disk('public')->putFileAs('user_product_requests', $file, $fileName);
                $productRequest->image = $imagePath;
            }

            $productRequest->save();

            return CommonHelper::responseSuccessWithData('Product request submitted successfully!', $productRequest);

        } catch (\Exception $e) {
            Log::error('Error creating product request: ' . $e->getMessage());
            return CommonHelper::responseError('Something went wrong while creating the request.');
        }
    }

    /**
     * Get authenticated user's product requests
     */
    public function getAuthUserRequests(Request $request)
    {
        try {
            $customerId = auth()->user()->id;
            $status = $request->get('status');
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);

            $query = UserProductRequest::with(['product.seller'])
                ->byCustomer($customerId)
                ->orderBy('created_at', 'desc');

            if ($status && in_array($status, ['pending', 'accepted', 'rejected'])) {
                $query->where('status', $status);
            }

            $requests = $query->skip($offset)->take($limit)->get();
            
            // Transform the data to include only product name and seller name
            $transformedRequests = $requests->map(function ($request) {
                $data = $request->toArray();
                
                if ($request->product) {
                    $data['product'] = [
                        'id' => $request->product->id,
                        'name' => $request->product->name,
                        'seller_name' => $request->product->seller ? $request->product->seller->name : 'null'
                    ];
                } else {
                    $data['product'] = null;
                }
                
                return $data;
            });
            
            $total = UserProductRequest::where('customer_id', $customerId)->count();

            return CommonHelper::responseWithData($transformedRequests, $total);

        } catch (\Exception $e) {
            Log::error('Error fetching user requests: ' . $e->getMessage());
            return CommonHelper::responseError('Something went wrong while fetching requests.');
        }
    }

    /**
     * Get all product requests for admin
     */
    public function getAllRequests(Request $request)
    {
        try {
            $status = $request->get('status');

            $query = UserProductRequest::with(['customer', 'product'])
                ->orderBy('created_at', 'desc');

            if ($status && in_array($status, ['pending', 'accepted', 'rejected'])) {
                $query->where('status', $status);
            }

            $requests = $query->paginate(15);

            return CommonHelper::responseWithData($requests);

        } catch (\Exception $e) {
            Log::error('Error fetching all requests: ' . $e->getMessage());
            return CommonHelper::responseError('Something went wrong while fetching requests.');
        }
    }

    /**
     * Update product request status (Admin only)
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:user_product_requests,id',
            'status' => 'required|in:accepted,rejected',
            'product_id' => 'required_if:status,accepted|exists:products,id',
            'admin_notes' => 'required_if:status,rejected|string|max:1000',
        ]);

        if ($validator->fails()) {
            return CommonHelper::responseError($validator->errors()->first());
        }

        try {
            $productRequest = UserProductRequest::findOrFail($request->id);

           
            if ($request->status === 'rejected') {
                $productRequest->admin_notes = $request->admin_notes;
            }

            $productRequest->status = $request->status;

            if ($request->status === 'accepted' && $request->product_id) {
                $productRequest->product_id = $request->product_id;
            }

            $productRequest->save();

            try {
                dispatch(new SendProductRequestNotificationJob($productRequest, 'status_update'))->afterResponse();
            } catch (\Exception $e) {
                Log::error("Product request notification error: " . $e->getMessage());
            }

            return CommonHelper::responseSuccess('Request status updated successfully!', $productRequest->load(['customer', 'product']));

        } catch (\Exception $e) {
            Log::error('Error updating request status: ' . $e->getMessage());
            return CommonHelper::responseError('Something went wrong while updating the request.');
        }
    }


    /**
     * Get request details by ID
     */
    public function getRequestDetails($id)
    {
        try {
            $request = UserProductRequest::with(['customer', 'product'])
                ->findOrFail($id);

            return CommonHelper::responseWithData($request);

        } catch (\Exception $e) {
            Log::error('Error fetching request details: ' . $e->getMessage());
            return CommonHelper::responseError('Request not found.');
        }
    }
}