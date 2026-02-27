<?php

namespace App\Http\Controllers;

use App\Models\ServiceJob;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class InvoiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'auth',
            'role:admin',
        ];
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['job.client', 'job.technician.user', 'job.chemicals']);
        return view('invoices.show', compact('invoice'));
    }

    public function generate(Request $request, ServiceJob $job)
    {
        $request->validate([
            'subtotal'    => 'required|numeric|min:0',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'discount'    => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string',
            'due_date'    => 'nullable|date',
            'status'      => 'required|in:draft,sent,paid,overdue',
        ]);

        $subtotal   = $request->subtotal;
        $taxPct     = $request->tax_percent ?? 0;
        $taxAmount  = round($subtotal * $taxPct / 100, 2);
        $discount   = $request->discount ?? 0;
        $total      = $subtotal + $taxAmount - $discount;

        $invoice = $job->invoice ?? new Invoice(['service_job_id' => $job->id]);

        $invoice->fill([
            'subtotal'     => $subtotal,
            'tax_percent'  => $taxPct,
            'tax_amount'   => $taxAmount,
            'discount'     => $discount,
            'total_amount' => $total,
            'status'       => $request->status,
            'notes'        => $request->notes,
            'due_date'     => $request->due_date,
        ]);
        $invoice->save();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice generated!');
    }

    public function download(Invoice $invoice)
    {
        $invoice->load(['job.client', 'job.technician.user', 'job.chemicals']);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }
}
