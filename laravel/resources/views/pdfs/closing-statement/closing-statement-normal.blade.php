<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Closing Statement - {{ $closing['ct_number'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px; 
            line-height: 1.4; 
            color: #333;
            margin: 20mm;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        .header h1 { 
            font-size: 24px; 
            margin-bottom: 10px; 
            text-transform: uppercase;
            font-weight: bold;
            color: #2c3e50;
        }
        .header h2 { 
            font-size: 18px; 
            margin-bottom: 5px; 
            color: #34495e;
        }
        .header p { 
            font-size: 12px; 
            color: #666; 
        }
        .info-grid { 
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border: 1px solid #ddd;
        }
        .info-row {
            display: table-row;
        }
        .info-item { 
            display: table-cell;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .info-item:first-child {
            border-right: 1px solid #eee;
            width: 50%;
        }
        .section { 
            margin-bottom: 30px; 
            break-inside: avoid;
        }
        .section-title { 
            font-weight: bold; 
            font-size: 16px; 
            margin-bottom: 15px; 
            padding: 10px 15px; 
            background: #f8f9fa; 
            text-align: center;
            border: 2px solid #dee2e6;
            color: #495057;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
            font-size: 11px;
        }
        th, td { 
            border: 1px solid #dee2e6; 
            padding: 8px 12px; 
            text-align: left; 
            vertical-align: top;
        }
        th { 
            background-color: #f8f9fa; 
            font-weight: bold; 
            text-align: center;
            color: #495057;
        }
        .amount { 
            text-align: right !important; 
            font-weight: bold;
            font-family: monospace;
        }
        .total-row { 
            font-weight: bold; 
            background-color: #d4edda; 
            border-top: 2px solid #28a745;
        }
        .total-row td {
            font-size: 12px;
            font-weight: bold;
        }
        .summary {
            margin-top: 30px;
            border-top: 3px solid #333;
            padding-top: 20px;
        }
        .summary-grid {
            display: table;
            width: 100%;
            border: 2px solid #333;
        }
        .summary-row {
            display: table-row;
        }
        .summary-label {
            display: table-cell;
            padding: 10px 15px;
            font-weight: bold;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
        }
        .summary-value {
            display: table-cell;
            padding: 10px 15px;
            text-align: right;
            font-weight: bold;
            font-family: monospace;
            border-bottom: 1px solid #dee2e6;
        }
        .footer { 
            margin-top: 40px; 
            text-align: center; 
            font-size: 11px; 
            color: #666;
            border-top: 2px solid #333;
            padding-top: 20px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Hospital Closing Statement</h1>
    <h2>{{ $closing['ct_number'] }}</h2>
    <p>{{ $generated_at->format('d/m/Y H:i:s') }}</p>
</div>

<div class="info-grid">
    <div class="info-row">
        <div class="info-item">
            <strong>Reception:</strong> {{ $reception['name'] }}
        </div>
        <div class="info-item">
            <strong>Receptionist:</strong> {{ $receptionist['name'] }}
        </div>
    </div>
    <div class="info-row">
        <div class="info-item">
            <strong>Status:</strong> {{ $closing['status'] }}
        </div>
        <div class="info-item">
            <strong>Opening Amount:</strong> Rs. {{ number_format($closing['opening_amount'], 2) }}
        </div>
    </div>
    <div class="info-row">
        <div class="info-item">
            <strong>Closing Amount:</strong> Rs. {{ number_format($closing['closing_amount'], 2) }}
        </div>
        <div class="info-item">
            <strong>Cash Received:</strong> {{ $closing['cash_receiving_time'] ? \Carbon\Carbon::parse($closing['cash_receiving_time'])->format('d/m/Y H:i') : 'N/A' }}
        </div>
    </div>
</div>

{{-- Income Transactions --}}
@if(count($transactions['income']) > 0)
<div class="section">
    <div class="section-title">Income Transactions ({{ $summary['income_count'] }})</div>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Patient</th>
                <th>Service</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions['income'] as $transaction)
                @foreach($transaction['elements'] as $element)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($element['created_at'])->format('H:i') }}</td>
                    <td>
                        @if($element['patient_name'])
                            {{ $element['patient_name'] }}<br>
                            <small>{{ $element['patient_ps_number'] }}</small>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $element['service_name'] }}</td>
                    <td class="amount">Rs. {{ number_format($element['amount'], 2) }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3"><strong>Total Income</strong></td>
                <td class="amount"><strong>Rs. {{ number_format($totals['total_income'], 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

{{-- Expense Transactions --}}
@if(count($transactions['expense']) > 0)
<div class="section">
    <div class="section-title">Expense Transactions ({{ $summary['expense_count'] }})</div>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions['expense'] as $transaction)
                @foreach($transaction['elements'] as $element)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($element['created_at'])->format('H:i') }}</td>
                    <td>
                        {{ $element['type'] }}
                        @if($element['patient_name'])
                            - {{ $element['patient_name'] }}
                        @endif
                    </td>
                    <td class="amount">Rs. {{ number_format($element['amount'], 2) }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2"><strong>Total Expenses</strong></td>
                <td class="amount"><strong>Rs. {{ number_format($totals['total_expense'], 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

{{-- Summary --}}
<div class="section">
    <div class="section-title">Summary</div>
    <table>
        <tr>
            <td><strong>Total Income</strong></td>
            <td class="amount">Rs. {{ number_format($totals['total_income'], 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total Expenses</strong></td>
            <td class="amount">Rs. {{ number_format($totals['total_expense'], 2) }}</td>
        </tr>
        <tr class="total-row">
            <td><strong>Net Amount</strong></td>
            <td class="amount"><strong>Rs. {{ number_format($totals['net_amount'], 2) }}</strong></td>
        </tr>
        <tr>
            <td><strong>Cash Amount</strong></td>
            <td class="amount">Rs. {{ number_format($closing['closing_amount_cash'], 2) }}</td>
        </tr>
        <tr>
            <td><strong>Card Amount</strong></td>
            <td class="amount">Rs. {{ number_format($closing['closing_amount_card'], 2) }}</td>
        </tr>
        <tr>
            <td><strong>Cheque Amount</strong></td>
            <td class="amount">Rs. {{ number_format($closing['closing_amount_cheque'], 2) }}</td>
        </tr>
    </table>
</div>

<div class="footer">
    <p>Generated on {{ $generated_at->format('d/m/Y H:i:s') }}</p>
    <p>Closing Statement: {{ $closing['ct_number'] }}</p>
</div>

</body>
</html>