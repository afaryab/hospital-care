<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Closing Statement - {{ $closing['ct_number'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.4;
            color: #111;
            margin: 4mm;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .header {
            text-align: center;
            margin-bottom: 6px;
            border-bottom: 2px solid #111;
            padding-bottom: 4px;
        }
        .header h1 { font-size: 13px; text-transform: uppercase; font-weight: bold; }
        .header h2 { font-size: 11px; margin-top: 2px; }
        .header p  { font-size: 8px; color: #555; margin-top: 2px; }
        .kv { display: table; width: 100%; margin-bottom: 2px; font-size: 9px; }
        .kv-label { display: table-cell; }
        .kv-value { display: table-cell; text-align: right; font-weight: bold; }
        hr { border: none; border-top: 1px dashed #999; margin: 5px 0; }
        hr.solid { border-top: 1px solid #111; }
        .section-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #555;
            margin: 5px 0 2px;
            font-weight: bold;
        }
        .amount-row { display: table; width: 100%; margin-bottom: 1px; font-size: 9px; }
        .amount-label { display: table-cell; }
        .amount-value { display: table-cell; text-align: right; font-weight: bold; }
        .highlight { background: #f0f0f0; padding: 1px 2px; }
        .net-row { font-size: 11px; font-weight: bold; margin: 3px 0; display: table; width: 100%; }
        .net-label { display: table-cell; }
        .net-value { display: table-cell; text-align: right; }
        .footer { margin-top: 8px; text-align: center; font-size: 8px; color: #666; border-top: 1px solid #ccc; padding-top: 4px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Closing Statement</h1>
    <h2>{{ $closing['ct_number'] }}</h2>
    <p>@hdate($generated_at, 'd/m/Y H:i')</p>
</div>

{{-- Counter Info --}}
<div class="kv"><span class="kv-label">Reception</span><span class="kv-value">{{ $reception['name'] }}</span></div>
<div class="kv"><span class="kv-label">Receptionist</span><span class="kv-value">{{ $receptionist['name'] }}</span></div>
<div class="kv"><span class="kv-label">Status</span><span class="kv-value">{{ strtoupper($closing['status']) }}</span></div>

<hr>

{{-- Transaction Counts --}}
<div class="section-label">Transactions</div>
<div class="kv"><span class="kv-label">Count</span><span class="kv-value">{{ $totals['transactions_count'] }}</span></div>
@if($totals['refund_count'] > 0)
<div class="kv"><span class="kv-label">Refunded</span><span class="kv-value">{{ $totals['refund_count'] }}</span></div>
@endif
@if($totals['edited_count'] > 0)
<div class="kv"><span class="kv-label">Edited</span><span class="kv-value">{{ $totals['edited_count'] }}</span></div>
@endif
@if($totals['receaveables_count'] > 0)
<div class="kv"><span class="kv-label">Receivables</span><span class="kv-value">{{ $totals['receaveables_count'] }}</span></div>
@endif

<hr>

{{-- Income by Payment Method --}}
<div class="section-label">Income by Method</div>
@php
    $cashIncome = $totals['income_by_payment_method']['CASH'] ?? 0;
    $chequeIncome = $totals['income_by_payment_method']['CHEQUE'] ?? 0;
    $bankTransferIncome = $totals['income_by_payment_method']['BANK_TRANSFER'] ?? 0;
    $cardIncome = $totals['income_by_payment_method']['CARD'] ?? 0;
    $panelIncome = $totals['income_by_payment_method']['PANEL'] ?? 0;
    $netCash = $cashIncome - $totals['total_expense'];
@endphp
<div class="kv"><span class="kv-label">Cash</span><span class="kv-value">{{ number_format($cashIncome, 0) }}</span></div>
@if($chequeIncome > 0)
<div class="kv"><span class="kv-label">Cheque</span><span class="kv-value">{{ number_format($chequeIncome, 0) }}</span></div>
@endif
@if($bankTransferIncome > 0)
<div class="kv"><span class="kv-label">Bank Transfer</span><span class="kv-value">{{ number_format($bankTransferIncome, 0) }}</span></div>
@endif
@if($cardIncome > 0)
<div class="kv"><span class="kv-label">Card</span><span class="kv-value">{{ number_format($cardIncome, 0) }}</span></div>
@endif
@if($panelIncome > 0)
<div class="kv"><span class="kv-label">Panel</span><span class="kv-value">{{ number_format($panelIncome, 0) }}</span></div>
@endif
<div class="kv bold highlight"><span class="kv-label">Total Income</span><span class="kv-value">{{ number_format($totals['total_income'], 0) }}</span></div>

<hr>

{{-- Expenses --}}
<div class="section-label">Expenses</div>
<div class="kv"><span class="kv-label">Expense Paid</span><span class="kv-value">{{ number_format($totals['total_expense'], 0) }}</span></div>

<hr class="solid">

{{-- Net Totals --}}
<div class="net-row"><span class="net-label">Net Total</span><span class="net-value">{{ number_format($totals['net_amount'], 0) }}</span></div>
<div class="kv highlight"><span class="kv-label">Net Cash <span style="font-size:8px;font-weight:normal">(Cash − Exp)</span></span><span class="kv-value">{{ number_format($netCash, 0) }}</span></div>

<div class="footer">
    <p>@hdate($generated_at, 'd/m/Y H:i:s')</p>
</div>

</body>
</html>