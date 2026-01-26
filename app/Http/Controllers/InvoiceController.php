<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService
    ) {}

    /**
     * Generate invoice for orders with given order_number.
     */
    public function generate(Request $request, string $orderNumber)
    {
        $tenantId = auth()->user()->tenant_id;

        $result = $this->invoiceService->generateInvoice($orderNumber, $tenantId);

        if (!$result['status']) {
            if (isset($result['invoice'])) {
                return redirect()->route('invoices.show', $result['invoice'])
                    ->with('info', $result['message']);
            }
            return redirect()->route('payments.index')
                ->with('error', $result['message']);
        }

        return redirect()->route('invoices.show', $result['invoice'])
            ->with('success', $result['message']);
    }

    /**
     * Display invoice in HTML format.
     */
    public function show(Invoice $invoice)
    {
        $tenantId = auth()->user()->tenant_id;

        $result = $this->invoiceService->getInvoiceWithOrders($invoice->id, $tenantId);

        if (!$result['status']) {
            abort(403, $result['message']);
        }

        $settings = $this->invoiceService->getInvoiceSettings();

        return view('invoices.show', [
            'invoice' => $result['invoice'],
            'orders' => $result['orders'],
            'settings' => $settings,
        ]);
    }

    /**
     * Generate and download PDF invoice.
     */
    public function download(Invoice $invoice)
    {
        $tenantId = auth()->user()->tenant_id;

        $result = $this->invoiceService->getInvoiceWithOrders($invoice->id, $tenantId);

        if (!$result['status']) {
            abort(403, $result['message']);
        }

        $settings = $this->invoiceService->getInvoiceSettings();

        // Render the invoice view to HTML
        $html = view('invoices.show', [
            'invoice' => $result['invoice'],
            'orders' => $result['orders'],
            'settings' => $settings,
        ])->render();

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
        $invoices = $this->invoiceService->getByTenant($tenantId);

        return view('invoices.index', compact('invoices'));
    }
}
