<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Receipt - {{ $transaction->tr_number }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm 100mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10px;
            line-height: 1.1;
            color: #000;
            background: white;
            width: 80mm;
            margin: 0;
            padding: 2mm;
        }
        
        .receipt {
            width: 76mm;
            background: white;
        }
        
        .header { 
            text-align: center;
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
        
        .info-line {
            width: 100%;
            font-size: 9px;
            margin-bottom: 1mm;
            text-align: left;
        }
        
        .info-line::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .label {
            color: #666;
            width: 40%;
            text-align: left;
        }
        
        .value {
            color: #000;
            width: 60%;
            text-align: left;
            font-weight: normal;
        }
        
        .items {
            margin: 2mm 0;
        }
        
        .items-header {
            border-bottom: 1px dotted #CFCFCF;
            padding: 1mm 0;
            margin: 1mm 0;
            font-size: 9px;
            font-weight: bold;
        }
        
        .items-header::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .col-product {
            float: left;
            width: 60%;
        }
        
        .col-qty {
            float: left;
            width: 15%;
            text-align: center;
        }
        
        .col-total {
            float: right;
            width: 25%;
            text-align: right;
        }
        
        .item-line {
            font-size: 9px;
            margin-bottom: 0.5mm;
        }
        
        .item-line::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .order-line {
            background: #f0f0f0;
            font-size: 8px;
            color: #666;
            padding: 0.5mm;
            margin-bottom: 1mm;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 2mm 0 2mm 0;
        }
        .divider-2 {
            border-top: 1px dotted #c3c3c3;
            margin: 2mm 0 2mm 0;
        }
        
        .totals {
            font-size: 9px;
        }
        
        .total-line {
            margin-bottom: 0.5mm;
        }
        
        .total-line::after {
            content: "";
            display: table;
            clear: both;
        }
        
        .total-line.bold {
            font-weight: bold;
        }
        
        .total-label {
            float: left;
            width: 55%;
        }
        
        .total-value {
            float: right;
            width: 45%;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>{{ $hospital_info['name'] }}</h1>
            <h2>Receipt</h2>
            <h2>{{ $transaction->tr_number }}</h2>
            <p>{{ $generated_at->format('d/m/Y H:i') }}</p>
            <div class="divider"></div>
            @if($transaction->patient)
            <div class="info-line">
                <span class="label">Patient:</span>
                <span class="value">{{ $transaction->patient->name }}</span>
            </div>
            <div class="info-line">
                <span class="label">MR Number:</span>
                <span class="value">{{ $transaction->patient->ps_number }}</span>
            </div>
            @endif
        </div>
        <div class="divider-2"></div>
        <!-- Items -->
        <div class="items">
            <div class="items-header">
                <span class="col-product">Services</span>
                <span class="col-total">Total</span>
            </div>
            @foreach($transaction->elements as $item)
                <div class="item-line">
                    <span class="col-product">
                        {{ $item->income_or_expense === 'INCOME' ? (
                            $item->service_id ? $item->service->name : ($item->service_recestation_id ? $item->serviceRecestation->name : '')
                        ) : (
                            $item->type === 'EXP' ? ($item->expense->description ?? 'Expense') : 'Voucher Payment'
                        ) }}
                    </span>
                    <span class="col-total">{{ $item->amount }}</span>
                </div>
                @if($item->serviceOrder)
                <div class="order-line">
                    MRI: {{ $item->serviceOrder->so_number }} {{ $item?->doctor?->name}}
                </div>
                @endif
            @endforeach
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-line">
                <span class="total-label">Total:</span>
                <span class="total-value">{{ $transaction->amount }}</span>
            </div>
            <div class="total-line">
                <span class="total-label">Payment Method:</span>
                <span class="total-value">{{ strtoupper($transaction->payment_method) }}</span>
            </div>
            <div class="total-line">
                <span class="total-label">Amount Paid:</span>
                <span class="total-value">{{ $transaction->customer_payed ?? $transaction->amount }}</span>
            </div>
            <div class="total-line bold">
                <span class="total-label">Change:</span>
                <span class="total-value">{{ $transaction->change ?? '0.00' }}</span>
            </div>
        </div>
    </div>
</body>
</html>