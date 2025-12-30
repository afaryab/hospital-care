<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Income Cash Flow Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px; 
            line-height: 1.4; 
            color: #333;
            margin: 10mm;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
        }
        .header h1 { 
            font-size: 22px; 
            margin-bottom: 8px; 
            text-transform: uppercase;
            font-weight: bold;
            color: #1e40af;
        }
        .header .subtitle { 
            font-size: 14px; 
            color: #64748b;
            font-weight: 600;
        }
        
        .filters-section {
            background: #f1f5f9;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
        }
        .filters-grid {
            display: table;
            width: 100%;
        }
        .filter-row {
            display: table-row;
        }
        .filter-item {
            display: table-cell;
            padding: 5px 10px;
            font-size: 9px;
        }
        .filter-label {
            font-weight: bold;
            color: #475569;
        }
        .filter-value {
            color: #1e293b;
        }

        .group-header {
            background: #dbeafe;
            padding: 10px 15px;
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 12px;
            color: #1e40af;
            border-left: 4px solid #2563eb;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
            font-size: 9px;
        }
        th, td { 
            border: 1px solid #cbd5e1; 
            padding: 6px 8px; 
            text-align: left; 
            vertical-align: top;
        }
        th { 
            background-color: #e0e7ff; 
            font-weight: bold; 
            text-align: center;
            color: #3730a3;
            font-size: 9px;
        }
        .amount { 
            text-align: right !important; 
            font-weight: 600;
            font-family: monospace;
        }
        .subtotal-row { 
            font-weight: bold; 
            background-color: #fef3c7;
            border-top: 2px solid #f59e0b;
        }
        .subtotal-row td {
            font-size: 10px;
            padding: 8px;
        }
        .total-row { 
            font-weight: bold; 
            background-color: #bbf7d0; 
            border-top: 3px solid #22c55e;
        }
        .total-row td {
            font-size: 11px;
            font-weight: bold;
            padding: 10px 8px;
        }
        
        .summary-box {
            background: #f0fdf4;
            border: 2px solid #22c55e;
            padding: 15px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-row {
            display: table-row;
        }
        .summary-item {
            display: table-cell;
            padding: 5px 10px;
            border-bottom: 1px dashed #bbf7d0;
        }
        .summary-label {
            font-weight: bold;
            color: #166534;
            width: 60%;
        }
        .summary-value {
            text-align: right;
            font-weight: bold;
            font-family: monospace;
            color: #15803d;
            font-size: 11px;
        }

        .edited-indicator {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .strikethrough {
            text-decoration: line-through;
            color: #dc2626;
            font-size: 8px;
        }
        
        .edited-amount {
            color: #15803d;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #cbd5e1;
            text-align: center;
            font-size: 8px;
            color: #64748b;
        }

        .page-break {
            page-break-after: always;
        }

        @media print {
            body { margin: 0; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Income Cash Flow Report</h1>
        <div class="subtitle">Financial Analysis & Revenue Summary</div>
        @if(isset($total_records_in_db) && isset($records_shown) && $total_records_in_db > $records_shown)
            <div style="background: #fef3c7; color: #92400e; padding: 8px; margin-top: 10px; border-radius: 4px; font-size: 9px;">
                ⚠️ Note: Showing {{ number_format($records_shown) }} of {{ number_format($total_records_in_db) }} total records. 
                Please apply filters to narrow down results for complete data.
            </div>
        @endif
    </div>

    {{-- Balance Statement - Moved to Top --}}
    @if($totals['count'] > 0)
        <div class="summary-box" style="margin-bottom: 20px;">
            <div class="summary-grid">
                <div class="summary-row">
                    <div class="summary-item summary-label">Total Transactions:</div>
                    <div class="summary-item summary-value">{{ number_format($totals['count']) }}</div>
                </div>
                <div class="summary-row">
                    <div class="summary-item summary-label">Total Income Amount:</div>
                    <div class="summary-item summary-value">{{ number_format($totals['total_amount'], 2) }}</div>
                </div>
                @if($totals['edited_count'] > 0)
                <div class="summary-row">
                    <div class="summary-item summary-label">Edited Transactions:</div>
                    <div class="summary-item summary-value">{{ number_format($totals['edited_count']) }} transactions</div>
                </div>
                <div class="summary-row">
                    <div class="summary-item summary-label">Total Edited Amount:</div>
                    <div class="summary-item summary-value">{{ number_format($totals['total_edited'], 2) }}</div>
                </div>
                @endif
                <div class="summary-row" style="border-top: 2px solid #22c55e; margin-top: 5px; padding-top: 5px;">
                    <div class="summary-item summary-label" style="font-size: 12px;">Net Balance:</div>
                    <div class="summary-item summary-value" style="font-size: 13px;">{{ number_format($totals['balance'], 2) }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Filters Section --}}
    <div class="filters-section">
        <div class="filters-grid">
            <div class="filter-row">
                <div class="filter-item">
                    <span class="filter-label">Date Range:</span>
                    <span class="filter-value">
                        {{ \Carbon\Carbon::parse($filters['date_from'])->format('M d, Y') }} - 
                        {{ \Carbon\Carbon::parse($filters['date_to'])->format('M d, Y') }}
                    </span>
                </div>
                <div class="filter-item">
                    <span class="filter-label">Generated:</span>
                    <span class="filter-value">{{ $generated_at }}</span>
                </div>
            </div>
            @if($filters['closing'])
            <div class="filter-row">
                <div class="filter-item">
                    <span class="filter-label">Counter:</span>
                    <span class="filter-value">{{ $filters['closing']['ct_number'] ?? 'N/A' }}</span>
                </div>
            </div>
            @endif
            @if($filters['service'])
            <div class="filter-row">
                <div class="filter-item">
                    <span class="filter-label">Service:</span>
                    <span class="filter-value">{{ $filters['service']['name'] ?? 'N/A' }}</span>
                </div>
            </div>
            @endif
            @if($filters['doctor'])
            <div class="filter-row">
                <div class="filter-item">
                    <span class="filter-label">Provider:</span>
                    <span class="filter-value">{{ $filters['doctor']['name'] ?? 'N/A' }}</span>
                </div>
            </div>
            @endif
            @if($filters['patient'])
            <div class="filter-row">
                <div class="filter-item">
                    <span class="filter-label">Patient:</span>
                    <span class="filter-value">{{ $filters['patient']['ps_number'] ?? 'N/A' }}</span>
                </div>
            </div>
            @endif
            @if($group_by !== 'none')
            <div class="filter-row">
                <div class="filter-item">
                    <span class="filter-label">Grouped By:</span>
                    <span class="filter-value">{{ ucfirst($group_by) }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Data Table --}}
    @if($group_by !== 'none' && count($groupedData) > 0)
        {{-- Grouped Data Display --}}
        
        {{-- Group Summary Table --}}
        <div style="margin-bottom: 20px;">
            <h3 style="color: #1e40af; font-size: 14px; margin-bottom: 10px;">Group Summary ({{ count($groupedData) }} groups)</h3>
            <table style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Group Name</th>
                        <th style="width: 120px; text-align: right;">Transactions</th>
                        <th style="width: 150px; text-align: right;">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $groupIndex = 1; @endphp
                    @foreach($groupedData as $key => $group)
                    <tr>
                        <td style="text-align: center;">{{ $groupIndex++ }}</td>
                        <td style="font-weight: bold;">{{ $group['label'] }}</td>
                        <td style="text-align: right;">{{ number_format($group['count']) }}</td>
                        <td class="amount">{{ number_format($group['subtotal'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="2" style="text-align: right; font-weight: bold;">Grand Total:</td>
                        <td style="text-align: right; font-weight: bold;">{{ number_format($totals['count']) }}</td>
                        <td class="amount" style="font-weight: bold;">{{ number_format($totals['balance'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        {{-- Detailed Group Data --}}
        @foreach($groupedData as $key => $group)
            <div class="group-header">
                {{ $group['label'] }} - {{ number_format($group['count']) }} transaction(s) - Total: {{ number_format($group['subtotal'], 2) }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 25px;">#</th>
                        @if($columns['show_date'])
                            <th style="width: 70px;">Date</th>
                        @endif
                        @if($columns['show_transaction_number'])
                            <th style="width: 100px;">Transaction #</th>
                        @endif
                        <th>Patient</th>
                        <th>Service</th>
                        @if($columns['show_service_order'])
                            <th style="width: 100px;">Service Order</th>
                        @endif
                        @if($columns['show_provider_name'])
                            <th>Provider</th>
                        @endif
                        @if($columns['show_original_amount'])
                            <th style="width: 70px;">Original</th>
                        @endif
                        <th style="width: 90px;">Amount</th>
                        @if($columns['show_balance'])
                            <th style="width: 70px;">Balance</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['items'] as $index => $element)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            @if($columns['show_date'])
                                <td>{{ \Carbon\Carbon::parse($element->created_at)->format('M d, Y') }}</td>
                            @endif
                            @if($columns['show_transaction_number'])
                                <td>{{ $element->tr_number ?? 'N/A' }}</td>
                            @endif
                            <td>
                                {{ $element->ps_name ?? 'N/A' }}
                                @if($element->ps_number)
                                    <br><small style="color: #64748b;">{{ $element->ps_number }}</small>
                                @endif
                            </td>
                            <td>{{ $element->service_name ?? 'N/A' }}</td>
                            @if($columns['show_service_order'])
                                <td>{{ $element->so_number ?? 'N/A' }}</td>
                            @endif
                            @if($columns['show_provider_name'])
                                <td>{{ $element->doctor_name ?? 'N/A' }}</td>
                            @endif
                            @if($columns['show_original_amount'])
                                <td class="amount">{{ number_format($element->orignal_amount ?? 0, 2) }}</td>
                            @endif
                            <td class="amount">
                                @if($element->edited_amount !== null)
                                    <span class="strikethrough">{{ number_format($element->amount ?? 0, 2) }}</span><br>
                                    <span class="edited-amount">{{ number_format($element->edited_amount, 2) }}</span>
                                @else
                                    {{ number_format($element->amount ?? 0, 2) }}
                                @endif
                            </td>
                            @if($columns['show_balance'])
                                <td class="amount">{{ number_format($element->edited_amount ?? $element->amount ?? 0, 2) }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="subtotal-row">
                        <td colspan="{{ $columns['show_date'] ? 2 : 1 }}"></td>
                        @if($columns['show_transaction_number'])
                            <td></td>
                        @endif
                        @if($columns['show_patient_name'])
                            <td></td>
                        @endif
                        @if($columns['show_service_name'])
                            <td></td>
                        @endif
                        @if($columns['show_service_order'])
                            <td></td>
                        @endif
                        @if($columns['show_provider_name'])
                            <td></td>
                        @endif
                        @if($columns['show_original_amount'])
                            <td></td>
                        @endif
                        <td class="amount" style="font-weight: bold;">
                            {{ number_format($group['subtotal'], 2) }}
                        </td>
                        @if($columns['show_edited_amount'])
                            <td></td>
                        @endif
                        @if($columns['show_customer_payed'])
                            <td></td>
                        @endif
                        @if($columns['show_change'])
                            <td></td>
                        @endif
                        @if($columns['show_balance'])
                            <td class="amount" style="font-weight: bold;">
                                {{ number_format($group['subtotal'], 2) }}
                            </td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        @endforeach
    @else
        {{-- Non-grouped Data Display --}}
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">#</th>
                    @if($columns['show_date'])
                        <th style="width: 70px;">Date</th>
                    @endif
                    @if($columns['show_transaction_number'])
                        <th style="width: 100px;">Transaction #</th>
                    @endif
                    <th>Patient</th>
                    <th>Service</th>
                    @if($columns['show_service_order'])
                        <th style="width: 100px;">Service Order</th>
                    @endif
                    @if($columns['show_provider_name'])
                        <th>Provider</th>
                    @endif
                    @if($columns['show_original_amount'])
                        <th style="width: 70px;">Original</th>
                    @endif
                    <th style="width: 90px;">Amount</th>
                    @if($columns['show_balance'])
                        <th style="width: 70px;">Balance</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($elements as $index => $element)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        @if($columns['show_date'])
                            <td>{{ \Carbon\Carbon::parse($element->created_at)->format('M d, Y') }}</td>
                        @endif
                        @if($columns['show_transaction_number'])
                            <td>{{ $element->tr_number ?? 'N/A' }}</td>
                        @endif
                        <td>
                            {{ $element->ps_name ?? 'N/A' }}
                            @if($element->ps_number)
                                <br><small style="color: #64748b;">{{ $element->ps_number }}</small>
                            @endif
                        </td>
                        <td>{{ $element->service_name ?? 'N/A' }}</td>
                        @if($columns['show_service_order'])
                            <td>{{ $element->so_number ?? 'N/A' }}</td>
                        @endif
                        @if($columns['show_provider_name'])
                            <td>{{ $element->doctor_name ?? 'N/A' }}</td>
                        @endif
                        @if($columns['show_original_amount'])
                            <td class="amount">{{ number_format($element->orignal_amount ?? 0, 2) }}</td>
                        @endif
                        <td class="amount">
                            @if($element->edited_amount !== null)
                                <span class="strikethrough">{{ number_format($element->amount ?? 0, 2) }}</span><br>
                                <span class="edited-amount">{{ number_format($element->edited_amount, 2) }}</span>
                            @else
                                {{ number_format($element->amount ?? 0, 2) }}
                            @endif
                        </td>
                        @if($columns['show_balance'])
                            <td class="amount">{{ number_format($element->edited_amount ?? $element->amount ?? 0, 2) }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="20" style="text-align: center; padding: 30px; color: #64748b;">
                            No income transactions found for the selected filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>This is a computer-generated report. No signature is required.</p>
        <p>Generated on {{ $generated_at }} | Hospital Care Management System</p>
    </div>
</body>
</html>
