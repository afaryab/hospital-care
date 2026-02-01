<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Closing Statement - {{ $closing['ct_number'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
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