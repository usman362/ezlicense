<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt — {{ $receipt['number'] }}</title>
    <style>
        /* DomPDF only supports a subset of CSS. Keep styles inline-ish + simple. */
        @page { margin: 30px 35px; }
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; line-height: 1.55; margin: 0; padding: 0; }
        h1, h2, h3 { color: #111827; margin: 0; padding: 0; font-weight: 700; }

        /* ── Header bar (brand) ── */
        .header { padding-bottom: 12px; border-bottom: 3px solid #f59e0b; margin-bottom: 22px; }
        .brand { font-size: 22px; font-weight: 800; color: #111827; }
        .brand .l-badge { background: #ffd500; color: #121110; padding: 2px 8px; border-radius: 4px; margin: 0 1px; }
        .doc-type { float: right; text-align: right; font-size: 11px; color: #6b7280; }
        .doc-type .doc-title { font-size: 18px; font-weight: 800; color: #111827; letter-spacing: -0.02em; }

        /* ── Section blocks ── */
        .section { margin-bottom: 18px; }
        .section-title { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 6px; font-weight: 600; }
        .two-col { width: 100%; }
        .two-col td { vertical-align: top; padding: 0; }
        .two-col td:first-child { width: 50%; }

        /* ── Status pill ── */
        .pill { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .pill-paid       { background: #dcfce7; color: #166534; }
        .pill-pending    { background: #fef3c7; color: #92400e; }
        .pill-cancelled  { background: #fee2e2; color: #991b1b; }
        .pill-refunded   { background: #e0e7ff; color: #3730a3; }
        .pill-completed  { background: #dcfce7; color: #166534; }

        /* ── Itemised table ── */
        table.items { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.items th { background: #f9fafb; color: #374151; font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; padding: 8px 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        table.items td { padding: 10px; border-bottom: 1px solid #f3f4f6; }
        table.items td.r { text-align: right; }
        table.items tr.totals td { border-top: 2px solid #111827; border-bottom: none; padding-top: 10px; font-weight: 700; }
        table.items tr.total-final td { font-size: 14px; color: #111827; }
        table.items tr.refund-line td { color: #166534; }

        /* ── Footer ── */
        .footer { margin-top: 30px; padding-top: 14px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 10px; line-height: 1.6; }
        .footer .small-print { margin-top: 6px; font-size: 9px; }

        .muted { color: #6b7280; }
        .right { text-align: right; }
        .strong { font-weight: 700; color: #111827; }
        .mt-6 { margin-top: 12px; }
        .mt-2 { margin-top: 4px; }
    </style>
</head>
<body>

{{-- ── Header ── --}}
<div class="header">
    <table style="width:100%; border-collapse: collapse;">
        <tr>
            <td>
                <div class="brand">Secure<span class="l-badge">L</span>icence</div>
                <div class="muted mt-2">Australia's verified driving instructor marketplace</div>
            </td>
            <td class="right">
                <div class="doc-title">{{ $receipt['doc_title'] ?? 'Tax Invoice / Receipt' }}</div>
                <div class="muted mt-2">{{ $receipt['number'] }}</div>
                <div class="muted">Issued: {{ $receipt['issued_at']->format('j M Y, H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── Billed To + Status ── --}}
<table class="two-col section">
    <tr>
        <td>
            <div class="section-title">Billed To</div>
            <div class="strong">{{ $receipt['learner']['name'] }}</div>
            @if(!empty($receipt['learner']['email']))
                <div class="muted">{{ $receipt['learner']['email'] }}</div>
            @endif
            @if(!empty($receipt['learner']['phone']))
                <div class="muted">{{ $receipt['learner']['phone'] }}</div>
            @endif
        </td>
        <td>
            <div class="section-title">Status</div>
            <span class="pill pill-{{ $receipt['status_class'] }}">{{ $receipt['status_label'] }}</span>
            @if(!empty($receipt['paid_at']))
                <div class="muted mt-2">Paid: {{ $receipt['paid_at']->format('j M Y, H:i') }}</div>
            @endif
            @if(!empty($receipt['cancelled_at']))
                <div class="muted mt-2">Cancelled: {{ $receipt['cancelled_at']->format('j M Y, H:i') }}</div>
            @endif
            @if(!empty($receipt['refunded_at']))
                <div class="muted mt-2">Refunded: {{ $receipt['refunded_at']->format('j M Y, H:i') }}</div>
            @endif
        </td>
    </tr>
</table>

{{-- ── Booking + Instructor info ── --}}
<table class="two-col section">
    <tr>
        <td>
            <div class="section-title">Booking Details</div>
            <div><span class="muted">Booking ref:</span> <span class="strong">#{{ $receipt['booking_id'] }}</span></div>
            <div><span class="muted">Service:</span> {{ $receipt['service_label'] }}</div>
            <div><span class="muted">Scheduled:</span> {{ $receipt['scheduled_at']->format('l, j M Y') }} at {{ $receipt['scheduled_at']->format('H:i') }}</div>
            <div><span class="muted">Duration:</span> {{ $receipt['duration_minutes'] }} mins</div>
            <div><span class="muted">Transmission:</span> {{ ucfirst($receipt['transmission']) }}</div>
            @if(!empty($receipt['location']))
                <div><span class="muted">Pick-up:</span> {{ $receipt['location'] }}</div>
            @endif
        </td>
        <td>
            <div class="section-title">Instructor</div>
            <div class="strong">{{ $receipt['instructor']['name'] ?? '—' }}</div>
            @if(!empty($receipt['instructor']['phone']))
                <div class="muted">{{ $receipt['instructor']['phone'] }}</div>
            @endif
            @if(!empty($receipt['instructor']['email']))
                <div class="muted">{{ $receipt['instructor']['email'] }}</div>
            @endif
        </td>
    </tr>
</table>

{{-- ── Itemised charges ── --}}
<div class="section">
    <div class="section-title">Charges</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width:55%">Item</th>
                <th class="r" style="width:15%">Qty</th>
                <th class="r" style="width:15%">Unit</th>
                <th class="r" style="width:15%">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="strong">{{ $receipt['service_label'] }}</div>
                    <div class="muted" style="font-size:11px;">with {{ $receipt['instructor']['name'] ?? 'instructor' }} on {{ $receipt['scheduled_at']->format('j M Y') }}</div>
                </td>
                <td class="r">1</td>
                <td class="r">${{ number_format($receipt['amount'], 2) }}</td>
                <td class="r">${{ number_format($receipt['amount'], 2) }}</td>
            </tr>

            @if(!empty($receipt['coupon_discount']))
                <tr>
                    <td>
                        <div>Discount{{ !empty($receipt['coupon_code']) ? ' — ' . $receipt['coupon_code'] : '' }}</div>
                    </td>
                    <td class="r">—</td>
                    <td class="r">—</td>
                    <td class="r">−${{ number_format($receipt['coupon_discount'], 2) }}</td>
                </tr>
            @endif

            <tr class="totals">
                <td colspan="3" class="r">Subtotal</td>
                <td class="r">${{ number_format($receipt['subtotal'], 2) }}</td>
            </tr>
            @if(!empty($receipt['gst_amount']))
                <tr>
                    <td colspan="3" class="r muted">GST (included)</td>
                    <td class="r muted">${{ number_format($receipt['gst_amount'], 2) }}</td>
                </tr>
            @endif
            <tr class="totals total-final">
                <td colspan="3" class="r">Total {{ $receipt['payment_method_label'] ? 'paid (' . $receipt['payment_method_label'] . ')' : 'paid' }}</td>
                <td class="r">${{ number_format($receipt['total_paid'], 2) }}</td>
            </tr>

            @if(!empty($receipt['refund_amount']))
                <tr class="refund-line">
                    <td colspan="3" class="r">Refund issued ({{ $receipt['refund_method_label'] ?? 'manual' }})</td>
                    <td class="r">−${{ number_format($receipt['refund_amount'], 2) }}</td>
                </tr>
                @if($receipt['cancellation_fee_retained'] > 0)
                    <tr>
                        <td colspan="3" class="r muted">Cancellation fee retained</td>
                        <td class="r muted">${{ number_format($receipt['cancellation_fee_retained'], 2) }}</td>
                    </tr>
                @endif
                <tr class="totals">
                    <td colspan="3" class="r">Net to learner</td>
                    <td class="r">${{ number_format($receipt['net_to_learner'], 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if(!empty($receipt['cancellation_reason']) || !empty($receipt['refund_reason']))
    <div class="section">
        <div class="section-title">Notes</div>
        @if(!empty($receipt['cancellation_reason']))
            <div><span class="muted">Cancellation reason:</span> {{ $receipt['cancellation_reason'] }}</div>
        @endif
        @if(!empty($receipt['cancellation_message']))
            <div><span class="muted">Message:</span> {{ $receipt['cancellation_message'] }}</div>
        @endif
        @if(!empty($receipt['refund_reason']))
            <div><span class="muted">Refund reason:</span> {{ $receipt['refund_reason'] }}</div>
        @endif
        @if(!empty($receipt['refund_reference']))
            <div><span class="muted">Refund reference:</span> {{ $receipt['refund_reference'] }}</div>
        @endif
    </div>
@endif

{{-- ── Footer ── --}}
<div class="footer">
    <strong>Secure Licence Pty Ltd</strong> · {{ $receipt['support_email'] }} · {{ url('/') }}<br>
    Thank you for choosing Secure Licence. Keep this receipt for your records.
    <div class="small-print">
        Receipt issued automatically. Amounts shown in AUD. For disputes or refund queries, contact our support team within 14 days of the lesson date.
    </div>
</div>

</body>
</html>
