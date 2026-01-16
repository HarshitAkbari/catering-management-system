<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Services\InvoiceNumberService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Generate invoice for orders with given order_number.
     */
    public function generate(Request $request, string $orderNumber)
    {
        $tenantId = auth()->user()->tenant_id;

        // Get all orders with this order_number
        $orders = Order::where('tenant_id', $tenantId)
            ->where('order_number', $orderNumber)
            ->with('customer')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('payments.index')
                ->with('error', 'Orders not found.');
        }

        // Check if invoice already exists for any of these orders
        $existingInvoice = Invoice::where('tenant_id', $tenantId)
            ->whereIn('order_id', $orders->pluck('id'))
            ->first();

        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice)
                ->with('info', 'Invoice already exists for this order.');
        }

        // Get the first order (we'll use it as the primary order for the invoice)
        $primaryOrder = $orders->first();

        // Calculate totals
        $totalAmount = $orders->sum('estimated_cost');
        $tax = 0; // Can be configured later
        $discount = 0; // Can be configured later
        $finalAmount = $totalAmount + $tax - $discount;

        // Generate invoice number
        $invoiceNumber = InvoiceNumberService::generate($tenantId);

        // Create invoice
        $invoice = Invoice::create([
            'tenant_id' => $tenantId,
            'order_id' => $primaryOrder->id,
            'invoice_number' => $invoiceNumber,
            'total_amount' => $totalAmount,
            'tax' => $tax,
            'discount' => $discount,
            'final_amount' => $finalAmount,
            'status' => 'sent',
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice generated successfully.');
    }

    /**
     * Display invoice in HTML format.
     */
    public function show(Invoice $invoice)
    {
        // Ensure user can only access invoices from their tenant
        if ($invoice->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Load relationships
        $invoice->load(['order.customer', 'tenant', 'payments']);

        // Get all orders with the same order_number
        $orders = Order::where('tenant_id', $invoice->tenant_id)
            ->where('order_number', $invoice->order->order_number)
            ->with('customer')
            ->orderBy('event_date')
            ->get();

        // Invoice branding settings (no longer configurable)
        $settings = [
            'invoice_logo' => null,
            'invoice_footer_text' => null,
            'invoice_terms' => null,
        ];

        return view('invoices.show', compact('invoice', 'orders', 'settings'));
    }

    /**
     * Generate and download PDF invoice.
     */
    public function download(Invoice $invoice)
    {
        // Ensure user can only access invoices from their tenant
        if ($invoice->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        // Load relationships
        $invoice->load(['order.customer', 'tenant', 'payments']);

        // Get all orders with the same order_number
        $orders = Order::where('tenant_id', $invoice->tenant_id)
            ->where('order_number', $invoice->order->order_number)
            ->with('customer')
            ->orderBy('event_date')
            ->get();

        // Invoice branding settings (no longer configurable)
        $settings = [
            'invoice_logo' => null,
            'invoice_footer_text' => null,
            'invoice_terms' => null,
        ];

        // Render the invoice view to HTML
        $html = view('invoices.show', compact('invoice', 'orders', 'settings'))->render();

        // Configure dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Generate filename
        $filename = 'invoice-' . $invoice->invoice_number . '.pdf';

        // Return PDF download
        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * List all invoices (optional, for future use).
     */
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $invoices = Invoice::where('tenant_id', $tenantId)
            ->with(['order.customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }
}

