@extends('layouts.mail')

@section('content')
@php
    $app_name = \App\Models\Setting::get_value('app_name');
    $currency = \App\Models\Setting::get_value('currency') ?? '$';
    $support_email = \App\Models\Setting::get_value('support_email');
    $support_number = \App\Models\Setting::get_value('support_number');
@endphp

@if(isset($customer) && isset($productRequest))
    <p style="line-height: 24px; margin-bottom:15px;">
        Dear {{ $customer->name }},
    </p>

    <p style="line-height: 24px; margin-bottom: 15px;">
        Your product request #{{ $productRequest->id }} has been <strong>{{ ucfirst($productRequest->status) }}</strong>.
    </p>

    @if($productRequest->status === 'accepted' && $productRequest->product)
        <p style="line-height: 24px; margin-bottom: 15px;">
            🎉 Great news! Your product request has been accepted and the product is now available in our store.
        </p>
        <p style="line-height: 24px; margin-bottom: 15px;">
            <strong>Product Name:</strong> {{ $productRequest->product->name }}<br>
            @if($productRequest->product->seller)
                <strong>Added by:</strong> {{ $productRequest->product->seller->name }}
            @endif
        </p>
        <p style="line-height: 24px; margin-bottom: 15px;">
            You can now find this product in our store and place your order!
        </p>
    @elseif($productRequest->status === 'rejected')
        <p style="line-height: 24px; margin-bottom: 15px;">
            We regret to inform you that your product request has been rejected.
        </p>
        @if($productRequest->admin_notes)
            <p style="line-height: 24px; margin-bottom: 15px;">
                <strong>Reason:</strong> {{ $productRequest->admin_notes }}
            </p>
        @endif
        <p style="line-height: 24px; margin-bottom: 15px;">
            Please feel free to submit another request or contact our support team for more information.
        </p>
    @endif

    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
        <h3 style="color: #333; margin-bottom: 15px;">Product Request Details</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Request ID:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">#{{ $productRequest->id }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Status:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ ucfirst($productRequest->status) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Submitted:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $productRequest->created_at->format('M d, Y H:i') }}</td>
            </tr>
            @if($productRequest->product)
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Product Name:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $productRequest->product->name }}</td>
            </tr>
            @endif
            @if($productRequest->admin_notes)
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Admin Notes:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $productRequest->admin_notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($productRequest->description)
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="color: #333; margin-bottom: 10px;">Your Description:</h3>
            <p style="line-height: 24px; margin-bottom: 0; font-style: italic;">
                "{{ $productRequest->description }}"
            </p>
        </div>
    @endif

    <p style="line-height: 24px; margin-bottom: 15px;">
        Thank you for your request and for helping us improve our product selection!
    </p>

@else
    <p style="line-height: 24px; margin-bottom:15px;">
        There was an error processing your product request notification.
    </p>
@endif
@endsection
