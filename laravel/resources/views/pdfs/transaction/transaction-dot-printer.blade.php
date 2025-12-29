<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dot Matrix Receipt - {{ $transaction->tr_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 8mm;
            size: A4 portrait;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            max-width: 180mm;
            margin: 0 auto;
        }

        .receipt {
            padding: 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
        }

        .hospital-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .hospital-info {
            font-size: 9px;
        }

        .section {
            margin-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
            margin-bottom: 4px;
            padding-bottom: 2px;
        }

        .info-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 9px;
        }

        .info-label {
            font-weight: bold;
        }

        .items-section {
            border-top: 1px dashed #333;
            border-bottom: 1px dashed #333;
            padding: 5px 0;
        }

        .item {
            margin-bottom: 3px;
            font-size: 8px;
        }

        .item-name {
            font-weight: bold;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 7px;
        }

        .totals {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px dashed #333;
        }

        .total-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .grand-total {
            font-weight: bold;
            font-size: 11px;
            border-top: 1px solid #333;
            padding-top: 3px;
            margin-top: 3px;
        }

        .payment-section {
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px dashed #333;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 7px;
            border-top: 1px dashed #333;
            padding-top: 5px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .dashed-line {
            border-bottom: 1px dashed #333;
            margin: 5px 0;
        }

        @media print {
            body {
                font-size: 9px;
            }
            
            .hospital-name {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="hospital-name">{{ $hospital_info['name'] }}</div>
            <div class="hospital-info">{{ $hospital_info['address'] }}</div>
            <div class="hospital-info">{{ $hospital_info['phone'] }}</div>
        </div>

        <!-- Transaction Info -->
        <div class="section">
            <div class="info-line">
                <span class="info-label">Receipt:</span>
                <span>{{ $transaction->tr_number }}</span>
            </div>
            <div class="info-line">
                <span class="info-label">Date:</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-line">
                <span class="info-label">Counter:</span>
                <span>{{ $transaction->counter->ct_number ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Patient Info -->
        <div class="section">
            <div class="section-title">PATIENT INFO</div>
            <div class="info-line">
                <span class="info-label">Name:</span>
                <span>{{ $patient->name }}</span>
            </div>
            <div class="info-line">
                <span class="info-label">MR#:</span>
                <span>{{ $patient->ps_number }}</span>
            </div>
            @if($patient->age)
            <div class="info-line">
                <span class="info-label">Age:</span>
                <span>{{ $patient->age }}</span>
            </div>
            @endif
        </div>

        <!-- Items -->
        <div class="items-section">
            <div class="section-title">SERVICES</div>
            @foreach($items as $item)
            <div class="item">
                <div class="item-name">{{ $item->service_name }}</div>
                <div class="item-details">
                    <span>{{ $item->quantity }} x {{ number_format($item->unit_price, 2) }}</span>
                    <span>{{ number_format($item->total, 2) }}</span>
                </div>
                @if($item->provider)
                <div style="font-size: 7px; color: #666;">{{ $item->provider->name }}</div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-line">
                <span>Subtotal:</span>
                <span>{{ number_format($transaction->total_amount, 2) }}</span>
            </div>
            <div class="total-line grand-total">
                <span>TOTAL:</span>
                <span>{{ number_format($transaction->total_amount, 2) }}/-</span>
            </div>
        </div>

        <!-- Payment -->
        <div class="payment-section">
            <div class="section-title">PAYMENT</div>
            <div class="info-line">
                <span class="info-label">Method:</span>
                <span>{{ ucfirst($transaction->payment_method) }}</span>
            </div>
            <div class="info-line">
                <span class="info-label">Paid:</span>
                <span>{{ number_format($transaction->amount_paid, 2) }}/-</span>
            </div>
            @if($transaction->change_amount > 0)
            <div class="info-line">
                <span class="info-label">Change:</span>
                <span>{{ number_format($transaction->change_amount, 2) }}/-</span>
            </div>
            @endif
        </div>

        <div class="dashed-line"></div>

        <!-- Receivables -->
         @if($patient->receaveables->count() > 0)
        <div class="section">
            <div class="section-title">OUTSTANDING RECEIVABLES</div>
            @foreach($patient->receaveables as $receaveable)
            <div class="info-line">
                <span class="info-label">Amount Due:</span>
                <span>{{ number_format($receaveable->amount, 2) }}/-</span>
            </div>
            <div class="info-line">
                <span class="info-label">Due Date:</span>
                <span>{{ $receaveable->due_date ? $receaveable->due_date->format('d/m/Y') : 'N/A' }}</span>
            </div>
            <div class="dashed-line"></div>
            @endforeach
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="bold">Thank You!</div>
            <div>{{ $generated_at->format('d/m/Y H:i') }}</div>
            <div>Computer Generated Receipt</div>
        </div>
    </div>
</body>
</html>