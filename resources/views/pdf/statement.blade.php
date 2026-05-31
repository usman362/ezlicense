<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statement — {{ $statement['reference'] }}</title>
    <style>
        @page { margin: 30px 35px; }
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 11.5px; line-height: 1.55; margin: 0; padding: 0; }
        h1, h2, h3 { color: #111827; margin: 0; padding: 0; font-weight: 700; }

        .header { padding-bottom: 12px; border-bottom: 3px solid #f59e0b; margin-bottom: 22px; }
        .brand { font-size: 22px; font-weight: 800; color: #111827; }
        .brand .l-badge { background: #ffd500; color: #121110; padding: 2px 8px; border-radius: 4px; margin: 0 1px; }

        .section { margin-bottom: 16px; }
        .section-title { font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin-bottom: 6px; font-weight: 600; }

        .two-col { width: 100%; }
        .two-col td { vertical-align: top; padding: 0; }
        .two-col td:first-child { width: 50%; }

        .pill { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .pill-paid       { background: #dcfce7; color: #166534; }
        .pill-pending    { background: #fef3c7; color: #92400e; }
        .pill-failed     { background: #fee2e2; color: #991b1b; }
        .pill-current    { background: #dbeafe; color: #1e40af; }
        .pill-none       { background: #f3f4f6; color: #6b7280; }

        /* Summary KPI strip */
        table.summary { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        table.summary td {
            border: 1px solid #e5e7eb; padding: 12px 14px; vertical-align: top; width: 25%;
        }
        table.summary .kpi-label { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; }
        table.summary .kpi-value { font-size: 18px; font-weight: 800; color: #111827; margin-top: 4px; }

        /* Items table */
        table.items { width: 100%; border-collapse: collapse; }
        table.items th { background: #f9fafb; color: #374151; font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.04em; padding: 8px 9px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        table.items td { padding: 8px 9px; border-bottom: 1px solid #f3f4f6; }
        table.items td.r, table.items th.r { text-align: right; }
        table.items tr.totals td { border-top: 2px solid #111827; border-bottom: none; padding-top: 10px; font-weight: 700; background: #fafafa; }
        table.items tr.total-final td { font-size: 13px; color: #111827; }

        .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 9.5px; line-height: 1.6; }
        .muted { color: #6b7280; }
        .right { text-align: right; }
        .strong { font-weight: 700; color: #111827; }
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
                <div class="muted mt-2">Instructor Statement · Australia</div>
            </td>
            <td class="right">
                <div style="font-size:17px; font-weight:800; color:#111827;">
                    {{ ucfirst(str_replace('_', ' ', $statement['frequency'])) }} Statement
                </div>
                <div class="muted mt-2">{{ $statement['reference'] }}</div>
                <div class="muted">Issued: {{ $statement['issued_at']->format('j M Y, H:i') }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ── Period + Status banner ── --}}
<table class="two-col section">
    <tr>
        <td>
            <div class="section-title">Statement Period</div>
            <div class="strong" style="font-size:14px;">{{ $statement['period_label'] }}</div>
            <div class="muted mt-2">{{ $statement['period_start']->format('D, j M Y') }} → {{ $statement['period_end']->format('D, j M Y') }}</div>
        </td>
        <td>
            <div class="section-title">Payout Status</div>
            @php
                $cls = match (true) {
                    $statement['is_current']                            => 'current',
                    $statement['payout']['status'] === 'paid'           => 'paid',
                    $statement['payout']['status'] === 'failed'         => 'failed',
                    $statement['payout']['status'] === 'no_bookings'    => 'none',
                    default                                             => 'pending',
                };
            @endphp
            <span class="pill pill-{{ $cls }}">{{ $statement['payout']['status_label'] }}</span>
            @if($statement['payout']['paid_at'])
                <div class="muted mt-2">Paid: {{ $statement['payout']['paid_at']->format('j M Y, H:i') }}</div>
            @endif
            @if($statement['payout']['payment_ref'])
                <div class="muted">Reference: {{ $statement['payout']['payment_ref'] }}</div>
            @endif
            @if($statement['payout']['failure_reason'])
                <div class="muted">Issue: {{ $statement['payout']['failure_reason'] }}</div>
            @endif
        </td>
    </tr>
</table>

{{-- ── Instructor block ── --}}
<div class="section">
    <div class="section-title">Issued To</div>
    <div class="strong">{{ $statement['instructor']['business_name'] ?: $statement['instructor']['name'] }}</div>
    @if($statement['instructor']['business_name'])
        <div class="muted">{{ $statement['instructor']['name'] }}</div>
    @endif
    @if($statement['instructor']['abn'])
        <div class="muted">ABN: {{ $statement['instructor']['abn'] }}{{ $statement['instructor']['gst_registered'] ? ' · GST registered' : '' }}</div>
    @endif
    @if($statement['instructor']['billing_address'])
        <div class="muted">{{ $statement['instructor']['billing_address'] }}</div>
    @endif
    @if($statement['instructor']['bank_account_masked'])
        <div class="muted mt-2">Payout to BSB {{ $statement['instructor']['bank_bsb'] }} acct {{ $statement['instructor']['bank_account_masked'] }}</div>
    @endif
</div>

{{-- ── Summary KPI strip ── --}}
<table class="summary">
    <tr>
        <td>
            <div class="kpi-label">Lessons</div>
            <div class="kpi-value">{{ $statement['totals']['bookings'] }}</div>
        </td>
        <td>
            <div class="kpi-label">Lesson Hours</div>
            <div class="kpi-value">{{ $statement['totals']['lesson_hrs'] }}</div>
        </td>
        <td>
            <div class="kpi-label">Gross Earned</div>
            <div class="kpi-value">${{ number_format($statement['totals']['gross'], 2) }}</div>
        </td>
        <td>
            <div class="kpi-label">Net Payout</div>
            <div class="kpi-value">${{ number_format($statement['totals']['net'], 2) }}</div>
        </td>
    </tr>
</table>

{{-- ── Lessons table ── --}}
<div class="section">
    <div class="section-title">Lessons Delivered in this Period</div>
    @if(count($statement['items']) === 0)
        <div style="background:#f9fafb; padding:18px; text-align:center; border-radius:8px; color:#6b7280;">
            No completed lessons in this period.
        </div>
    @else
        <table class="items">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Learner</th>
                    <th>Type</th>
                    <th class="r">Mins</th>
                    <th class="r">Gross</th>
                    <th class="r">Fees</th>
                    <th class="r">Net</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statement['items'] as $row)
                    <tr>
                        <td>{{ $row['scheduled_at']->format('D, j M') }}<br><span class="muted">{{ $row['scheduled_at']->format('H:i') }}</span></td>
                        <td>{{ $row['learner_name'] }}</td>
                        <td>{{ $row['type'] }}</td>
                        <td class="r">{{ $row['duration_mins'] }}</td>
                        <td class="r">${{ number_format($row['gross'], 2) }}</td>
                        <td class="r">−${{ number_format($row['fees'], 2) }}</td>
                        <td class="r">${{ number_format($row['net'], 2) }}</td>
                    </tr>
                @endforeach
                <tr class="totals">
                    <td colspan="3" class="r">Totals</td>
                    <td class="r">{{ array_sum(array_column($statement['items'], 'duration_mins')) }}</td>
                    <td class="r">${{ number_format($statement['totals']['gross'], 2) }}</td>
                    <td class="r">−${{ number_format($statement['totals']['fees'], 2) }}</td>
                    <td class="r">${{ number_format($statement['totals']['net'], 2) }}</td>
                </tr>
                <tr class="totals total-final">
                    <td colspan="6" class="r">Net payable to instructor</td>
                    <td class="r">${{ number_format($statement['totals']['net'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    @if($statement['cancelled_count'] > 0)
        <div class="muted mt-2" style="margin-top:8px; font-size:10px;">
            Note: {{ $statement['cancelled_count'] }} cancelled booking{{ $statement['cancelled_count'] > 1 ? 's' : '' }} in this period — these don't contribute to payout.
        </div>
    @endif
</div>

{{-- ── Fees breakdown ── --}}
<div class="section">
    <div class="section-title">Fees Applied</div>
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td class="muted" style="padding:3px 0;">Platform service fee</td>
            <td class="r" style="padding:3px 0;">${{ number_format($statement['fee_breakdown']['service_fee_per_booking'], 2) }} per booking</td>
        </tr>
        <tr>
            <td class="muted" style="padding:3px 0;">Payment processing fee</td>
            <td class="r" style="padding:3px 0;">${{ number_format($statement['fee_breakdown']['processing_fee_per_booking'], 2) }} per booking</td>
        </tr>
        <tr>
            <td class="strong" style="padding:6px 0; border-top:1px solid #e5e7eb;">Total fees retained this period</td>
            <td class="r strong" style="padding:6px 0; border-top:1px solid #e5e7eb;">${{ number_format($statement['fee_breakdown']['total_fees'], 2) }}</td>
        </tr>
    </table>
</div>

{{-- ── Footer ── --}}
<div class="footer">
    <strong>Secure Licence Pty Ltd</strong> · {{ $statement['support_email'] }} · {{ url('/') }}<br>
    Payouts are processed after each statement period ends. Funds typically clear into your bank account within 1–3 business days.
    Disputes or adjustments? Contact us within 14 days of statement issue.
</div>

</body>
</html>
