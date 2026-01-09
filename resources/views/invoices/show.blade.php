<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background: #fff;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-logo {
            max-width: 150px;
            max-height: 80px;
            margin-bottom: 15px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .company-details {
            color: #6b7280;
            font-size: 11px;
            line-height: 1.8;
        }
        
        .invoice-title {
            text-align: right;
            flex: 1;
        }
        
        .invoice-title h1 {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .invoice-meta {
            color: #6b7280;
            font-size: 11px;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .bill-to {
            flex: 1;
        }
        
        .bill-to h3 {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .bill-to p {
            color: #6b7280;
            font-size: 11px;
            margin: 3px 0;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info p {
            color: #6b7280;
            font-size: 11px;
            margin: 3px 0;
        }
        
        .invoice-info strong {
            color: #1f2937;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table thead {
            background: #f3f4f6;
        }
        
        .items-table th {
            padding: 12px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            color: #1f2937;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .items-table td {
            padding: 12px;
            font-size: 11px;
            color: #4b5563;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .items-table tbody tr:hover {
            background: #f9fafb;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-section {
            margin-left: auto;
            width: 300px;
            margin-bottom: 30px;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 11px;
        }
        
        .totals-row.subtotal {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            margin-top: 12px;
        }
        
        .totals-row.total {
            border-top: 2px solid #1f2937;
            padding-top: 12px;
            margin-top: 12px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .totals-label {
            color: #6b7280;
        }
        
        .totals-value {
            color: #1f2937;
            font-weight: 600;
        }
        
        .payment-info {
            margin-bottom: 30px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .payment-info h3 {
            font-size: 13px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .payment-info p {
            font-size: 11px;
            color: #6b7280;
            margin: 3px 0;
        }
        
        .payment-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .payment-status.paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .payment-status.partial {
            background: #fef3c7;
            color: #92400e;
        }
        
        .payment-status.pending {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }
        
        .invoice-footer p {
            margin: 5px 0;
        }
        
        .terms-section {
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }
        
        .terms-section h4 {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .terms-section p {
            font-size: 10px;
            color: #6b7280;
            line-height: 1.6;
        }
        
        @media print {
            body {
                background: #fff;
            }
            
            .invoice-container {
                padding: 20px;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .action-buttons {
            margin-bottom: 20px;
            text-align: right;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-left: 10px;
            background: #3b82f6;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .btn:hover {
            background: #2563eb;
        }
        
        .btn-secondary {
            background: #6b7280;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        @if(request()->routeIs('invoices.show'))
        <div class="action-buttons no-print">
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Back to Payments</a>
            <a href="{{ route('invoices.download', $invoice) }}" class="btn">Download PDF</a>
        </div>
        @endif
        
        @if(session('success'))
        <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 12px;">
            {{ session('success') }}
        </div>
        @endif
        
        @if(session('info'))
        <div style="background: #dbeafe; color: #1e40af; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 12px;">
            {{ session('info') }}
        </div>
        @endif
        
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                @if($settings['invoice_logo'] ?? null)
                    <img src="{{ $settings['invoice_logo'] }}" alt="Company Logo" class="company-logo">
                @endif
                <div class="company-name">{{ $invoice->tenant->name ?? 'Company Name' }}</div>
                <div class="company-details">
                    @if($invoice->tenant->email)
                        <div>{{ $invoice->tenant->email }}</div>
                    @endif
                    @if($invoice->tenant->phone)
                        <div>{{ $invoice->tenant->phone }}</div>
                    @endif
                    @if($invoice->tenant->address)
                        <div>{{ $invoice->tenant->address }}</div>
                    @endif
                </div>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="invoice-meta">
                    <div><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</div>
                    <div><strong>Date:</strong> {{ $invoice->created_at->format('F d, Y') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="bill-to">
                <h3>Bill To:</h3>
                @php
                    $customer = $invoice->order->customer;
                @endphp
                <p><strong>{{ $customer->name ?? 'N/A' }}</strong></p>
                @if($customer->email)
                    <p>{{ $customer->email }}</p>
                @endif
                @if($customer->mobile)
                    <p>{{ $customer->mobile }}</p>
                @endif
                @if($customer->address)
                    <p>{{ $customer->address }}</p>
                @endif
            </div>
            <div class="invoice-info">
                <p><strong>Order Number:</strong> {{ $invoice->order->order_number }}</p>
                <p><strong>Invoice Status:</strong> {{ ucfirst($invoice->status) }}</p>
                <p><strong>Payment Status:</strong> 
                    <span class="payment-status {{ $invoice->order->payment_status }}">
                        {{ ucfirst($invoice->order->payment_status) }}
                    </span>
                </p>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="datatable-simple items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Event Date</th>
                    <th>Event Time</th>
                    <th>Menu</th>
                    <th>Guest Count</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $index => $order)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $order->event_date ? $order->event_date->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->event_time ?? 'N/A')) }}</td>
                    <td>{{ $order->event_menu ?? 'N/A' }}</td>
                    <td>{{ $order->guest_count ?? 'N/A' }}</td>
                    <td class="text-right">₹{{ number_format($order->estimated_cost ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-row subtotal">
                <span class="totals-label">Subtotal:</span>
                <span class="totals-value">₹{{ number_format($invoice->total_amount, 2) }}</span>
            </div>
            @if($invoice->tax > 0)
            <div class="totals-row">
                <span class="totals-label">Tax:</span>
                <span class="totals-value">₹{{ number_format($invoice->tax, 2) }}</span>
            </div>
            @endif
            @if($invoice->discount > 0)
            <div class="totals-row">
                <span class="totals-label">Discount:</span>
                <span class="totals-value">-₹{{ number_format($invoice->discount, 2) }}</span>
            </div>
            @endif
            <div class="totals-row total">
                <span class="totals-label">Total Amount:</span>
                <span class="totals-value">₹{{ number_format($invoice->final_amount, 2) }}</span>
            </div>
        </div>
        
        <!-- Payment Information -->
        @if($invoice->payments->count() > 0)
        <div class="payment-info">
            <h3>Payment History</h3>
            @foreach($invoice->payments as $payment)
            <p>
                <strong>₹{{ number_format($payment->amount, 2) }}</strong> 
                via {{ ucfirst(str_replace('_', ' ', $payment->payment_mode)) }} 
                on {{ $payment->payment_date->format('M d, Y') }}
                @if($payment->reference_number)
                    (Ref: {{ $payment->reference_number }})
                @endif
            </p>
            @endforeach
        </div>
        @endif
        
        <!-- Terms & Conditions -->
        @if($settings['invoice_terms'] ?? null)
        <div class="terms-section">
            <h4>Terms & Conditions</h4>
            <p>{{ $settings['invoice_terms'] }}</p>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="invoice-footer">
            @if($settings['invoice_footer_text'] ?? null)
                <p>{{ $settings['invoice_footer_text'] }}</p>
            @endif
            <p>Thank you for your business!</p>
        </div>
    </div>
</body>
</html>

