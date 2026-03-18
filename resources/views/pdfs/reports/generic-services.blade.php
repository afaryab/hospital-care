<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Services Report</title>
    @php $report_title = 'Services Report'; $report_color = '#4338ca'; @endphp
</head>
<body>
    @include('pdfs.reports.partials.generic-header')

    {{-- Provider Summary Table --}}
    @if(count($service_groups) > 0)
    @php
        $providerSummary = [];
        foreach ($service_groups as $group) {
            foreach ($group['providers'] as $provider) {
                $key = $provider['doctor_id'] ?: $provider['doctor_name'];
                if (!isset($providerSummary[$key])) {
                    $providerSummary[$key] = ['name' => $provider['doctor_name'], 'total_income' => 0, 'total_expense' => 0];
                }
                $providerSummary[$key]['total_income'] += $provider['total_income'];
                $providerSummary[$key]['total_expense'] += $provider['total_expense'];
            }
        }
        $providerSummary = array_values($providerSummary);
    @endphp

    <div class="section-title">Service Providers Summary</div>
    <table>
        <thead>
            <tr>
                <th style="width: 28px;">#</th>
                <th>Service Provider</th>
                <th class="amount" style="width: 90px;">Income</th>
                <th class="amount" style="width: 90px;">Expense Paid</th>
                <th class="amount" style="width: 90px;">Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($providerSummary as $idx => $ps)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="text-bold">{{ $ps['name'] }}</td>
                <td class="amount" style="color: #059669;">{{ number_format($ps['total_income'], 2) }}</td>
                <td class="amount" style="color: #dc2626;">{{ number_format($ps['total_expense'], 2) }}</td>
                <td class="amount text-bold">{{ number_format($ps['total_income'] - $ps['total_expense'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right">Totals</td>
                <td class="amount" style="color: #059669;">{{ number_format($total_service_income, 2) }}</td>
                <td class="amount" style="color: #dc2626;">{{ number_format($total_expense_paid, 2) }}</td>
                <td class="amount">{{ number_format($total_service_income - $total_expense_paid, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Service → Provider detail --}}
    @if(count($service_groups) > 0)
        @foreach($service_groups as $group)
            <div class="section-title" style="margin-top: 18px;">
                {{ $group['service_name'] }}
                <span style="float: right; font-size: 10px; font-weight: 400; letter-spacing: 0;">
                    Service Total: {{ number_format($group['total_income'], 2) }}
                </span>
            </div>

            @foreach($group['providers'] as $provider)
                <div class="sub-section">
                    {{ $provider['doctor_name'] }}
                    <span style="float: right; font-size: 10px; font-weight: 400;">
                        <span class="badge badge-green">Income: {{ number_format($provider['total_income'], 2) }}</span>
                        @if($provider['total_expense'] > 0)
                            <span class="badge badge-red">Paid: {{ number_format($provider['total_expense'], 2) }}</span>
                        @endif
                    </span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 25px;">#</th>
                            <th>Transaction</th>
                            <th>Patient</th>
                            <th style="width: 50px;">Method</th>
                            <th style="width: 60px;">Date</th>
                            <th class="amount" style="width: 75px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($provider['items'] as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td><span class="mono">{{ $item['transaction_number'] }}</span></td>
                            <td>{{ $item['patient_name'] }}</td>
                            <td>
                                @php
                                    $badgeClass = match($item['type']) {
                                        'CASH' => 'badge-green', 'CARD' => 'badge-blue',
                                        'CHEQUE' => 'badge-amber', 'PANEL' => 'badge-purple',
                                        default => 'badge-green',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $item['type'] }}</span>
                            </td>
                            <td>{{ $item['created_at']->format('d/m H:i') }}</td>
                            <td class="amount">{{ number_format($item['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="subtotal-row">
                            <td colspan="5" class="text-right">Subtotal &mdash; {{ $provider['doctor_name'] }}</td>
                            <td class="amount">{{ number_format($provider['total_income'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @endforeach
        @endforeach
    @else
        <div class="empty-state">No service transactions found for this period.</div>
    @endif

    {{-- Expenses paid to providers --}}
    @if(count($expenses_by_doctor) > 0)
        <div class="section-title" style="background: #dc2626; margin-top: 24px;">
            Expenses Paid to Service Providers
        </div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">#</th>
                    <th>Paid To</th>
                    <th>Category</th>
                    <th>Voucher</th>
                    <th style="width: 60px;">Date</th>
                    <th class="amount" style="width: 75px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $expIdx = 0; @endphp
                @foreach($expenses_by_doctor as $doc)
                    @foreach($doc['items'] as $exp)
                        @php $expIdx++; @endphp
                        <tr>
                            <td>{{ $expIdx }}</td>
                            <td class="text-bold">{{ $doc['doctor_name'] }}</td>
                            <td>{{ $exp['category'] }}</td>
                            <td><span class="mono">{{ $exp['voucher'] ?? '-' }}</span></td>
                            <td>{{ $exp['created_at']->format('d/m H:i') }}</td>
                            <td class="amount" style="color: #dc2626;">{{ number_format($exp['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-right">Total Expenses Paid</td>
                    <td class="amount">{{ number_format($total_expense_paid, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Grand Summary --}}
    <div class="section-title" style="background: #1f2937; margin-top: 24px;">Grand Summary</div>
    <table>
        <tr>
            <td><span class="badge badge-green">INCOME</span> Total Service Income</td>
            <td class="amount" style="width: 120px; color: #059669;">{{ number_format($total_service_income, 2) }}</td>
        </tr>
        <tr>
            <td><span class="badge badge-red">EXPENSE</span> Total Expense Paid to Providers</td>
            <td class="amount" style="color: #dc2626;">{{ number_format($total_expense_paid, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Net Amount (Income &minus; Expense)</td>
            <td class="amount">{{ number_format($total_service_income - $total_expense_paid, 2) }}</td>
        </tr>
    </table>

    @include('pdfs.reports.partials.generic-footer')
</body>
</html>
