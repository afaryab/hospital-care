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
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }

        .hospital-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .hospital-info {
            font-size: 10px;
            color: #666;
        }

        .transaction-header {
            background-color: #f8f9fa;
            padding: 15px;
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
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }

        .patient-info h3 {
            color: #007bff;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
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
            font-size: 11px;
        }

        .items-table td {
            font-size: 10px;
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
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="hospital-name">{{ $hospital_info['name'] }}</div>
        <div class="hospital-info">
            {{ $hospital_info['address'] }}<br>
            Phone: {{ $hospital_info['phone'] }} | Email: {{ $hospital_info['email'] }}
        </div>
    </div>

    <!-- Transaction Header -->
    <div class="transaction-header">
        <h2>Transaction Receipt</h2>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Receipt No:</div>
                <div class="info-cell">{{ $transaction->tr_number }}</div>
                <div class="info-cell info-label">Date:</div>
                <div class="info-cell">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Counter:</div>
                <div class="info-cell">{{ $transaction->counter->ct_number ?? 'N/A' }}</div>
                <div class="info-cell info-label">Type:</div>
                <div class="info-cell">{{ ucfirst($transaction->income_or_expense) }}</div>
            </div>
        </div>
    </div>

    <!-- Patient Information -->
    <div class="patient-info">
        <h3>Patient Information</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Patient Name:</div>
                <div class="info-cell">{{ $patient->name }}</div>
                <div class="info-cell info-label">MR Number:</div>
                <div class="info-cell">{{ $patient->ps_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Age:</div>
                <div class="info-cell">{{ $patient->age ?? 'N/A' }}</div>
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
                <div class="info-cell info-label">CNIC:</div>
                <div class="info-cell">{{ $patient->cnic ?? 'N/A' }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">S.No</th>
                <th width="35%">Service</th>
                <th width="20%">Provider</th>
                <th width="10%">Qty</th>
                <th width="15%">Unit Price</th>
                <th width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->service_name }}</td>
                <td>{{ $item->provider->name ?? 'N/A' }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Payment Information -->
    <div class="payment-info">
        <h3>Payment Details</h3>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">Payment Method:</div>
                <div class="info-cell">{{ ucfirst($transaction->payment_method) }}</div>
                <div class="info-cell info-label">Amount Paid:</div>
                <div class="info-cell">{{ number_format($transaction->amount_paid, 2) }}/- only</div>
            </div>
            @if($transaction->change_amount > 0)
            <div class="info-row">
                <div class="info-cell info-label">Change Returned:</div>
                <div class="info-cell">{{ number_format($transaction->change_amount, 2) }}/- only</div>
                <div class="info-cell"></div>
                <div class="info-cell"></div>
            </div>
            @endif
        </div>
    </div>

    <!-- Totals Section -->
    <div class="totals-section">
        <div class="total-row">
            <div class="total-label">Subtotal:</div>
            <div class="total-value">{{ number_format($transaction->total_amount, 2) }}/-</div>
        </div>
        <div class="total-row grand-total">
            <div class="total-label">Grand Total:</div>
            <div class="total-value">{{ number_format($transaction->total_amount, 2) }}/- only</div>
        </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature">
            <div class="signature-line"></div>
            <div>Cashier Signature</div>
        </div>
        <div class="signature">
            <div class="signature-line"></div>
            <div>Patient Signature</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for choosing {{ $hospital_info['name'] }}</p>
        <p>Generated on {{ $generated_at->format('d/m/Y H:i:s') }}</p>
        <p>This is a computer-generated receipt and does not require a signature</p>
    </div>
</body>
</html>