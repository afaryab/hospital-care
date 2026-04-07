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
        .flex {
            display: flex;
            justify-content: space-between;
        }

        .flex-col {
            display: flex;
            flex-direction: column;
        }
        .flex-row {
            display: flex;
            flex-direction: row;
        }
        .footer {
            text-align: center;
            font-size: 8px;
            margin-top: 3mm;
        }


    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>{{ $hospital_info['name'] }}</h1>
            @if(!empty($hospital_info['address']))
                <p>{{ $hospital_info['address'] }}</p>
            @endif
            @if(!empty($hospital_info['ntn']) || !empty($hospital_info['strn']))
                <p>
                    @if(!empty($hospital_info['ntn'])) NTN: {{ $hospital_info['ntn'] }} @endif
                    @if(!empty($hospital_info['strn'])) | STRN: {{ $hospital_info['strn'] }} @endif
                </p>
            @endif
            <h2>Transaction Receipt: {{ $transaction->tr_number }}</h2>
        </div>
        <div class="header">
            <p>{{ $transaction->closing->ct_number }} @ {{ $generated_at->format('d/m/Y H:i') }}</p>
            <p>By {{ $transaction->receptionist->name ?? 'N/A' }}</p>
            <div class="divider"></div>
            @if($transaction->patient)
                <table width="100%" style="margin-bottom: 2mm;">
                    <tr style="border-bottom: 1px dotted #0c0c0c;">
                        <td class="label" width="40%">Patient:</td>
                        <td class="value">{{ $transaction->patient->name }}</td>
                    </tr>
                    <tr style="border-bottom: 1px dotted #0c0c0c;">
                        <td class="label">MR Number:</td>
                        <td class="value">{{ $transaction->patient->ps_number }}</td>
                    </tr>
                    <tr style="border-bottom: 1px dotted #0c0c0c;">
                        <td class="label">Age:</td>
                        <td class="value">{{ $transaction->patient->age }}</td>
                    </tr>
                    <tr style="border-bottom: 1px dotted #0c0c0c;">
                        <td class="label">Gender:</td>
                        <td class="value">
                            @if($transaction->patient->gender === 'm') Male
                            @elseif($transaction->patient->gender === 'f') Female
                            @elseif($transaction->patient->gender === 't') Transgender
                            @else N/A
                            @endif
                        </td>
                    </tr>
                    @if($transaction->patient->contact)
                        <tr style="border-bottom: 1px dotted #0c0c0c;">
                            <td class="label">Contact:</td>
                            <td class="value">{{ $transaction->patient->contact }}</td>
                        </tr>
                    @endif
                </table>
                
            @endif
            @if($transaction->receaveable_id)
            <div class="info-line">
                <span class="label">Receaveable ID:</span>
                <span class="value">{{ $transaction->receaveable_id }}</span>
            </div>
            @endif
        </div>
        <div class="divider-2"></div>
        <!-- Items -->
        @if($transaction->income_or_expense === 'INCOME')
        <div class="items">
            <div class="items-header">
                <span class="col-product">Services</span>
                <span class="col-total">Charges</span>
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
        @elseif($transaction->income_or_expense === 'EXPENSE')
            <div class="items">
                <div class="items-header">
                    <span class="col-product">Category</span>
                    <span class="col-total">Amount</span>
                </div>
            </div>
            @foreach($transaction->elements as $item)
                <div class="item-line">
                    <span class="col-product">
                        {{ $item?->expenseCategory?->name }}
                        <p style="font-size: 8px;">{{ $item->notes }}</p>
                    </span>
                    
                    <span class="col-total">{{ $item->amount }}</span>
                </div>
            @endforeach
        @endif

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Totals -->
        <div class="totals">
            <div class="total-line">
                <span class="total-label">Total:</span>
                <span class="total-value">{{ $transaction->amount }}/- only</span>
            </div>
            <div class="total-line">
                <span class="total-label">Payment Method:</span>
                <span class="total-value">{{ $transaction->income_or_expense === 'INCOME' ? ucfirst($transaction->type) : 'CASH' }}</span>
            </div>
        </div>
        <!-- Receaveables -->
        @if($patient?->receaveables?->count() > 0)

        <!-- Divider -->
        <div class="divider"></div>
        <div class="totals">
            <div class="total-line bold">
                <span class="total-label">Outstanding Receivables:</span>
                <span class="total-value">
                    {{
                        number_format(
                            $patient?->receaveables?->sum('amount'),
                            2
                        )
                    }}/- only
                </span>
            </div>
        </div>
        @endif
        <div class="divider"></div>
        <div class="footer">
            <p>Thank you for choosing {{ $hospital_info['name'] }}</p>    
            <p>Phone: {{ $hospital_info['phone'] }} | Email: {{ $hospital_info['email'] }}</p>
            @if(!empty($hospital_info['ntn']) || !empty($hospital_info['strn']))
                <p>
                    @if(!empty($hospital_info['ntn'])) NTN: {{ $hospital_info['ntn'] }} @endif
                    @if(!empty($hospital_info['strn'])) | STRN: {{ $hospital_info['strn'] }} @endif
                </p>
            @endif
            <p>Generated on {{ $generated_at->format('d/m/Y H:i:s') }}</p>
            <p>This is a computer-generated receipt and does not require a signature</p>
        </div>
    </div>
</body>
</html>