<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Closing Statement - {{ $closing['ct_number'] }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #111827;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .px-2 { padding-left: 8px; padding-right: 8px; }
        .py-8 { padding-top: 32px; padding-bottom: 32px; }
        .max-w-xl { max-width: 900px; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .flex { display: table; width: 100%; }
        .items-center { vertical-align: middle; }
        .justify-between > div { display: table-cell; vertical-align: top; }
        .justify-between > div:last-child { text-align: right; }
        .mb-8 { margin-bottom: 24px; }
        .mb-2 { margin-bottom: 8px; }
        .text-gray-700 { color: #374151; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .text-lg { font-size: 18px; }
        .text-xl { font-size: 20px; }
        .text-sm { font-size: 12px; }
        .uppercase { text-transform: uppercase; }

        .info-grid { margin: 0 8px 16px; }
        .info-row { margin-bottom: 6px; }
        .info-item { display: inline-block; width: 49%; vertical-align: top; }

        .section { margin: 0 8px 14px; }
        .section-title {
            font-weight: 700;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            margin-bottom: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px;
            vertical-align: top;
            text-align: left;
        }
        thead th { background: #f9fafb; }
        .amount { text-align: right; white-space: nowrap; }
        .total-row td { background: #f3f4f6; }
        .footer {
            margin: 18px 8px 0;
            padding-top: 8px;
            border-top: 1px solid #d1d5db;
            color: #6b7280;
            font-size: 11px;
        }
    </style>
</head>
<body>

<div class="px-2 py-8 max-w-xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center">
            <div class="text-gray-700 font-semibold text-lg">{{ config('app.name') }}</div>
        </div>
        <div class="text-gray-700">
            <div class="font-bold text-xl mb-2 uppercase">Closing Statement</div>
            <div class="text-sm">Date: {{ $generated_at->format('d/m/Y') }}</div>
            <div class="text-sm">Closing #: {{ $closing['ct_number'] }}</div>
        </div>
    </div>
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