<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Closing Statement - {{ $closing['ct_number'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px; 
            line-height: 1.2; 
            color: #333;
            margin: 5mm;
        }
        .header { 
            text-align: center; 
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        .header h1 { 
            font-size: 14px; 
            margin-bottom: 3px; 
            text-transform: uppercase;
            font-weight: bold;
        }
        .header h2 { 
            font-size: 12px; 
            margin-bottom: 2px; 
        }
        .header .date { 
            font-size: 9px; 
            color: #666; 
        }
        .info { 
            margin-bottom: 8px; 
            font-size: 9px;
        }
        .info > div { 
            margin-bottom: 2px; 
        }
        .info hr {
            margin: 5px 0;
            border: 0;
            border-top: 1px solid #ccc;
        }
        .section { 
            margin-bottom: 10px; 
        }
        .section-title { 
            font-weight: bold; 
            font-size: 11px; 
            margin-bottom: 0px; 
            padding: 3px; 
            background: #f0f0f0;
            border: 1px solid #ccc;
            border-bottom: none;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 8px; 
            font-size: 8px;
        }
        th, td { 
            border: 1px solid #ccc; 
            padding: 2px; 
            text-align: left; 
            vertical-align: top;
        }
        th { 
            background-color: #f5f5f5; 
            font-weight: bold; 
            text-align: center;
        }
        .amount { 
            text-align: right !important; 
            font-weight: bold;
        }
        .total-row { 
            font-weight: bold; 
            background-color: #e8f5e8; 
        }
        .total-row td {
            font-size: 9px;
            font-weight: bold;
        }
        .summary {
            margin-top: 10px;
            border-top: 2px solid #333;
            padding-top: 8px;
        }
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
            font-size: 10px;
        }
        .summary-label {
            display: table-cell;
            font-weight: bold;
        }
        .summary-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
        }
        .footer { 
            margin-top: 10px; 
            text-align: center; 
            font-size: 8px; 
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Closing Statement</h1>
    <h2>{{ $closing['ct_number'] }}</h2>
    <p>{{ $generated_at->format('d/m/Y H:i') }}</p>
</div>

<div class="section">
    <div class="section-title">Net Summary</div>
    <table>
        <tr>
            <td class="section-title">Reception:</td>
            <td class="">{{ $reception['name'] }}</td>
        </tr>
        <tr>
            <td class="section-title">Receptionist:</td>
            <td class="">{{ $receptionist['name'] }}</td>
        </tr>
        <tr>
            <td class="section-title">Status:</td>
            <td class="">{{ $closing['status'] }}</td>
        </tr>
        <tr>
            <td class="section-title">Transactions Count:</td>
            <td class="">{{ $totals['transactions_count'] }}</td>
        </tr>
        <tr>
            <td class="section-title">Edited Count:</td>
            <td class="">{{ $totals['edited_count'] }}</td>
        </tr>
        <tr>
            <td class="section-title">Refunded Count:</td>
            <td class="">{{ $totals['refund_count'] }}</td>
        </tr>
        <tr>
            <td class="section-title">Receivables Count:</td>
            <td class="">{{ $totals['receaveables_count'] }}</td>
        </tr>
    </table>
</div>


{{-- Net Summary --}}
<div class="section">
    <div class="section-title">Net Summary</div>
    <table>
        <tr>
            <td>Total Transactions</td>
            <td class="amount">{{ $totals['transactions_count']}}</td>
        </tr>
        <tr>
            <td>Income</td>
            <td class="amount">{{ number_format($totals['total_income'], 0) }}</td>
        </tr>
        <tr>
            <td>Expenses</td>
            <td class="amount">{{ number_format($totals['total_expense'], 0) }}</td>
        </tr>
        <tr class="total-row">
            <td>NET (Income - Expenses)</td>
            <td class="amount">{{ number_format($totals['net_amount'], 0) }}</td>
        </tr>
    </table>
</div>

{{-- Payment Methods --}}
<div class="section">
    <div class="section-title">Payment Methods</div>
    <table>
        @foreach($totals['by_type'] as $type => $amount)
        <tr>
            <td>{{ ucfirst($type) }}</td>
            <td class="amount">{{ number_format($amount, 0) }}</td>
        </tr>
        @endforeach
    </table>
</div>

{{-- Panel --}}
<div class="section">
    <div class="section-title">PANEL</div>
    <table>
        @foreach($totals['by_type'] as $type => $amount)
        <tr>
            <td>{{ ucfirst($type) }}</td>
            <td class="amount">{{ number_format($amount, 0) }}</td>
        </tr>
        @endforeach
    </table>
</div>


<div class="footer">
    <hr>
    <p>{{ $generated_at->format('d/m/Y H:i:s') }}</p>
    <p>Thank you!</p>
</div>

</body>
</html>