<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt - {{ $transaction->tr_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: left;
            padding-left: 15px;
            padding-bottom: 15px;
            border-bottom: 2px dotted #333;
        }

        .hospital-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .receipt-info {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 2px;
        }

        .hospital-info {
            font-size: 10px;
            color: #666;
        }

        .transaction-header {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 15px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .transaction-header h2 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 3px 10px 3px 0;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #555;
        }

        .patient-info {
            background-color: #e8f4fd;
            padding: 15px;
            margin: 15px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }

        .patient-info h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .items-table {
            width: calc(100% - 30px);
            border-collapse: collapse;
            margin: 15px;
            margin-bottom: 20px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
            font-size: 13px;
        }

        .items-table td {
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            margin: 15px;
            margin-bottom: 20px;
        }

        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .total-label,
        .total-value {
            display: table-cell;
            padding: 3px;
        }

        .total-label {
            text-align: right;
            font-weight: bold;
            padding-right: 15px;
        }

        .total-value {
            text-align: right;
            min-width: 100px;
        }

        .grand-total {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }

        .signature-section {
            margin-top: 40px;
            display: table;
            width: 100%;
        }

        .signature {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px 0;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 30px;
        }

        .payment-info {
            background-color: #fff3cd;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
        }

        .payment-info h3 {
            color: #856404;
            margin-bottom: 10px;
            font-size: 14px;
        }

        @media print {
            body {
                font-size: 11px;
            }
            
            .hospital-name {
                font-size: 20px;
            }
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="hospital-name">
            <h1>{{ $hospital_info['name'] }}</h1>
        </div>
        @if(!empty($hospital_info['address']))
            <div class="hospital-info">{{ $hospital_info['address'] }}</div>
        @endif
        @if(!empty($hospital_info['ntn']) || !empty($hospital_info['strn']))
            <div class="hospital-info">
                @if(!empty($hospital_info['ntn'])) NTN: {{ $hospital_info['ntn'] }} @endif
                @if(!empty($hospital_info['strn'])) | STRN: {{ $hospital_info['strn'] }} @endif
            </div>
        @endif
        <div class="receipt-info">
            <h4>Transaction Receipt: {{ $transaction->tr_number }}</h4>
        </div>
    </div>
    <table style="width:100%">
        <tr>
            <td style="width:50%">
                <!-- Transaction Header -->
                <div class="transaction-header">
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-cell info-label">Date:</div>
                            <div class="info-cell">@hdate($transaction->created_at, 'd/m/Y H:i:s')</div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell info-label">Counter:</div>
                            <div class="info-cell">{{ $transaction->closing->ct_number ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell info-label">Type:</div>
                            <div class="info-cell">{{ ucfirst($transaction->income_or_expense) }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-cell info-label">Receptionist:</div>
                            <div class="info-cell">{{ $transaction->receptionist->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </td>
            <td style="width:50%">
                @if($patient)
                    <!-- Patient Information -->
                    <div class="patient-info">
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-cell info-label">Patient Name:</div>
                                <div class="info-cell">{{ $patient->name }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-cell info-label">MR Number:</div>
                                <div class="info-cell">{{ $patient->ps_number }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-cell info-label">Age:</div>
                                <div class="info-cell">{{ $patient->age ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-cell info-label">Gender:</div>
                                <div class="info-cell">
                                    @if($patient->gender === 'm') Male
                                    @elseif($patient->gender === 'f') Female
                                    @elseif($patient->gender === 't') Transgender
                                    @else N/A
                                    @endif
                                </div>
                            </div>
                            @if($patient->contact)
                            <div class="info-row">
                                <div class="info-cell info-label">Contact:</div>
                                <div class="info-cell">{{ $patient->contact }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif
            </td>
        </tr>
    </table>

    
    
    @if($transaction->income_or_expense === 'INCOME')
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">S.No</th>
                    <th width="35%">Service</th>
                    <th width="20%">Provider</th>
                    <th width="15%">Charges</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                    @if($item->income_or_expense === 'INCOME')
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div>{{ $item->service_id ? $item->service->name : ($item->service_recestation_id ? $item->serviceRecestation->name : '') }}</div>
                                @if($item->serviceOrder)
                                    <div>MRI: {{ $item->serviceOrder->so_number }}@if($item->serviceOrder->token_short) &middot; Token #{{ $item->serviceOrder->token_short }}@endif</div>
                                @endif
                            </td>
                            <td>{{ $item->doctor->name ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            <tfoot class="grand-total">
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right">Total Payment:</td>
                    <td class="text-right">
                        {{
                            number_format(
                                $transaction->amount,
                                2
                            )
                        }}/- only
                    </td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right">Payment Method:</td>
                    <td class="text-right">
                        {{ ucfirst($transaction->type) ?? 'N/A' }}
                    </td>
                </tr>
            </tfoot>
            </tbody>
        </table>
    @elseif($transaction->income_or_expense === 'EXPENSE')
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">S.No</th>
                    <th width="55%">Description</th>
                    <th width="25%">Category</th>
                    <th width="15%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                    @if($item->income_or_expense === 'EXPENSE')
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->expense->notes ?? '' }}</td>
                            <td>{{ $item->expense->category->name ?? 'N/A' }}</td>
                            <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot class="grand-total">
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right">Total Expense:</td>
                    <td class="text-right">
                        {{
                            number_format(
                                $transaction->amount,
                                2
                            )
                        }}/- only
                    </td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="text-right">Payment Method:</td>
                    <td class="text-right">
                        {{ $transaction->income_or_expense === 'INCOME' ? ucfirst($transaction->type) : 'CASH' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if($patient?->receaveables?->count() > 0)
    <table style="width:100%">
        <tr>
            <td style="width:40%"></td>
            <td>
                <div class="totals-section text-right">
                    <div class="total-row inline-block">
                        <div class="total-label" style="padding:0px;">Outstanding Receivables</div>
                    </div>
                    <div class="total-row text-right">
                        <table style="width:100%;">
                            <thead>
                                <tr>
                                    <th width="30%" class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patient->receaveables as $index => $receaveable)
                                <tr>
                                    <td class="text-right">{{ number_format($receaveable->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="grand-total">
                                <tr>
                                    <td class="text-right">
                                        {{
                                            number_format(
                                                $patient->receaveables?->sum('amount'),
                                                2
                                            )
                                        }}/- only
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for choosing {{ $hospital_info['name'] }}</p>
        <p>Phone: {{ $hospital_info['phone'] }} | Email: {{ $hospital_info['email'] }}</p>
        @if(!empty($hospital_info['ntn']) || !empty($hospital_info['strn']))
            <p>
                @if(!empty($hospital_info['ntn'])) NTN: {{ $hospital_info['ntn'] }} @endif
                @if(!empty($hospital_info['strn'])) | STRN: {{ $hospital_info['strn'] }} @endif
            </p>
        @endif
        <p>Generated on @hdate($generated_at, 'd/m/Y H:i:s')</p>
        <p>This is a computer-generated receipt and does not require a signature</p>
    </div>
</body>
</html>