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
            margin-bottom: 5px; 
            padding: 3px; 
            background: #f0f0f0; 
            text-align: center;
            border: 1px solid #ccc;
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

<div class="info">
    <div><strong>Reception:</strong> {{ $reception['name'] }}</div>
    <div><strong>Staff:</strong> {{ $receptionist['name'] }}</div>
    <div><strong>Status:</strong> {{ $closing['status'] }}</div>
    <hr>
</div>

{{-- Income Summary --}}
@if(count($transactions['income']) > 0)
<div class="section">
    <div class="section-title">INCOME ({{ $summary['income_count'] }})</div>
    <table>
        @foreach($transactions['income'] as $transaction)
            @foreach($transaction['elements'] as $element)
            <tr>
                <td>{{ \Carbon\Carbon::parse($element['created_at'])->format('H:i') }}</td>
                <td>
                    @if($element['patient_ps_number'])
                        {{ $element['patient_ps_number'] }}
                    @else
                        {{ $element['service_name'] }}
                    @endif
                </td>
                <td class="amount">{{ number_format($element['amount'], 0) }}</td>
            </tr>
            @endforeach
        @endforeach
        <tr class="total-row">
            <td colspan="2">TOTAL INCOME</td>
            <td class="amount">{{ number_format($totals['total_income'], 0) }}</td>
        </tr>
    </table>
</div>
@endif

{{-- Expense Summary --}}
@if(count($transactions['expense']) > 0)
<div class="section">
    <div class="section-title">EXPENSES ({{ $summary['expense_count'] }})</div>
    <table>
        @foreach($transactions['expense'] as $transaction)
            @foreach($transaction['elements'] as $element)
            <tr>
                <td>{{ \Carbon\Carbon::parse($element['created_at'])->format('H:i') }}</td>
                <td>{{ $element['type'] }}</td>
                <td class="amount">{{ number_format($element['amount'], 0) }}</td>
            </tr>
            @endforeach
        @endforeach
        <tr class="total-row">
            <td colspan="2">TOTAL EXPENSES</td>
            <td class="amount">{{ number_format($totals['total_expense'], 0) }}</td>
        </tr>
    </table>
</div>
@endif

{{-- Final Summary --}}
<div class="section">
    <div class="section-title">SUMMARY</div>
    <table>
        <tr>
            <td>Income</td>
            <td class="amount">{{ number_format($totals['total_income'], 0) }}</td>
        </tr>
        <tr>
            <td>Expenses</td>
            <td class="amount">{{ number_format($totals['total_expense'], 0) }}</td>
        </tr>
        <tr class="total-row">
            <td>NET</td>
            <td class="amount">{{ number_format($totals['net_amount'], 0) }}</td>
        </tr>
        <hr>
        <tr>
            <td>Cash</td>
            <td class="amount">{{ number_format($closing['closing_amount_cash'], 0) }}</td>
        </tr>
        <tr>
            <td>Card</td>
            <td class="amount">{{ number_format($closing['closing_amount_card'], 0) }}</td>
        </tr>
        <tr>
            <td>Cheque</td>
            <td class="amount">{{ number_format($closing['closing_amount_cheque'], 0) }}</td>
        </tr>
    </table>
</div>

<div class="footer">
    <hr>
    <p>{{ $generated_at->format('d/m/Y H:i:s') }}</p>
    <p>Thank you!</p>
</div>

</body>
</html>