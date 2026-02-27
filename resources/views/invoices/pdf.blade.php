<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; color: #1e293b; background: #fff; padding: 30px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
        .brand-name { font-size: 18px; font-weight: bold; color: #6366f1; }
        .brand-sub { font-size: 10px; color: #64748b; }
        .invoice-title { font-size: 22px; font-weight: bold; color: #6366f1; text-align: right; }
        .invoice-num { font-size: 10px; color: #64748b; text-align: right; }
        hr { border: none; border-top: 1px solid #e2e8f0; margin: 16px 0; }
        .bill-section { display: flex; justify-content: space-between; margin-bottom: 24px; }
        .bill-to h4 { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .bill-to p { margin: 2px 0; }
        .job-card { background: #f8fafc; border-radius: 6px; padding: 10px 14px; margin-bottom: 20px; }
        .job-card strong { font-size: 13px; }
        .job-card span { font-size: 10px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f1f5f9; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px; text-align: left; color: #64748b; }
        td { padding: 7px 8px; border-bottom: 1px solid #f1f5f9; }
        .totals-table { float: right; min-width: 220px; }
        .totals-table td { padding: 4px 8px; }
        .totals-table .total-row td { font-size: 14px; font-weight: bold; color: #6366f1; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .notes { margin-top: 30px; font-size: 10px; color: #64748b; }
        .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 12px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="brand-name">🐛 FieldServicePro</div>
            <div class="brand-sub">Professional Pest Control Services</div>
        </div>
        <div>
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-num">{{ $invoice->invoice_number }}</div>
            <div class="invoice-num" style="margin-top:4px">
                <span class="badge">{{ ucfirst($invoice->status) }}</span>
            </div>
        </div>
    </div>
    <hr>

    <div class="bill-section">
        <div class="bill-to">
            <h4>Bill To</h4>
            <p><strong>{{ $invoice->job->client->name }}</strong></p>
            @if($invoice->job->client->company)<p>{{ $invoice->job->client->company }}</p>@endif
            @if($invoice->job->client->email)<p>{{ $invoice->job->client->email }}</p>@endif
            @if($invoice->job->client->phone)<p>{{ $invoice->job->client->phone }}</p>@endif
            @if($invoice->job->client->address)<p>{{ $invoice->job->client->address }}</p>@endif
        </div>
        <div style="text-align:right">
            <p><strong>Issued:</strong> {{ $invoice->issued_at?->format('d M Y') }}</p>
            @if($invoice->due_date)<p><strong>Due:</strong> {{ $invoice->due_date->format('d M Y') }}</p>@endif
            <p><strong>Job #:</strong> {{ $invoice->job->job_number }}</p>
            @if($invoice->job->technician)<p><strong>Technician:</strong> {{ $invoice->job->technician->user->name }}</p>@endif
        </div>
    </div>

    <div class="job-card">
        <strong>{{ $invoice->job->title }}</strong><br>
        <span>{{ $invoice->job->service_type }} &bull; Scheduled: {{ $invoice->job->scheduled_at?->format('d M Y') ?? 'N/A' }}</span>
        @if($invoice->job->description)<br><span style="margin-top:4px;display:block">{{ $invoice->job->description }}</span>@endif
    </div>

    @if($invoice->job->chemicals->isNotEmpty())
    <table>
        <thead>
            <tr><th>Type</th><th>Item/Description</th><th>Qty</th><th>Unit</th><th style="text-align:right">Cost</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->job->chemicals as $item)
            <tr>
                <td style="font-size: 8px; color: #64748b;">{{ ucfirst(str_replace('_', ' ', $item->type)) }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->quantity ?? '—' }}</td>
                <td>{{ $item->unit ?? '—' }}</td>
                <td style="text-align:right">₹{{ number_format($item->cost, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table class="totals-table">
        <tr><td>Subtotal</td><td style="text-align:right">₹{{ number_format($invoice->subtotal, 2) }}</td></tr>
        <tr><td>Tax ({{ $invoice->tax_percent }}%)</td><td style="text-align:right">₹{{ number_format($invoice->tax_amount, 2) }}</td></tr>
        @if($invoice->discount > 0)
        <tr><td>Discount</td><td style="text-align:right; color:#dc2626">-₹{{ number_format($invoice->discount, 2) }}</td></tr>
        @endif
        <tr class="total-row">
            <td><strong>TOTAL</strong></td>
            <td style="text-align:right"><strong>₹{{ number_format($invoice->total_amount, 2) }}</strong></td>
        </tr>
    </table>

    <div style="clear:both"></div>

    @if($invoice->notes)
    <div class="notes"><strong>Notes:</strong> {{ $invoice->notes }}</div>
    @endif

    <div class="footer">
        Thank you for choosing FieldServicePro &bull; Pest Control Services<br>
        This invoice was generated on {{ now()->format('d M Y') }}
    </div>
</body>
</html>
