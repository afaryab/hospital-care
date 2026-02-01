<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profit & Loss Statement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .report-info {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #333;
        }
        
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .ledger-table th {
            background-color: #f0f0f0;
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .ledger-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .section-total {
            background-color: #f8f8f8;
            font-weight: bold;
            border-top: 2px solid #333;
        }
        
        .totals-section {
            border-top: 2px solid #333;
            padding-top: 15px;
            margin-top: 20px;
        }
        
        .totals-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
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
    <div class="header">
        <div class="company-name">Your Company Name</div>
        <div class="report-title">Profit & Loss Statement</div>
        <div style="font-size: 12px; color: #666;">Generated on {{ $generatedAt }}</div>
    </div>

    <div class="report-info">
        @if($year)
        <div class="info-row">
            <span class="info-label">Year:</span>
            <span>{{ $year->start_date->format('Y') }} - {{ $year->end_date->format('Y') }}</span>
        </div>
        @endif
        @if($startDate)
        <div class="info-row">
            <span class="info-label">From Date:</span>
            <span>{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</span>
        </div>
        @endif
        @if($endDate)
        <div class="info-row">
            <span class="info-label">To Date:</span>
            <span>{{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Income Items:</span>
            <span>{{ $incomeRows->count() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Expense Items:</span>
            <span>{{ $expenseRows->count() }}</span>
        </div>
    </div>

    <!-- Income Section -->
    <div class="section">
        <div class="section-title">Income</div>
        @if($incomeRows->isNotEmpty())
            <table class="ledger-table">
                <thead>
                    <tr>
                        <th style="width: 70%;">Account Name</th>
                        <th style="width: 30%; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($incomeRows as $row)
                        <tr>
                            <td>{{ $row['code'] .' '.$row['name'] }}</td>
                            <td class="text-right">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="section-total">
                        <td class="text-right" style="padding: 8px;">Total Income:</td>
                        <td class="text-right" style="padding: 8px;">{{ number_format($totalIncome, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div style="text-align: center; padding: 20px; color: #666;">
                <em>No income accounts found for the selected criteria</em>
            </div>
        @endif
    </div>

    <!-- Expense Section -->
    <div class="section">
        <div class="section-title">Expenses</div>
        @if($expenseRows->isNotEmpty())
            <table class="ledger-table">
                <thead>
                    <tr>
                        <th style="width: 70%;">Account Name</th>
                        <th style="width: 30%; text-align: right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenseRows as $row)
                        <tr>
                            <td>{{ $row['code'] .' '.$row['name'] }}</td>
                            <td class="text-right">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="section-total">
                        <td class="text-right" style="padding: 8px;">Total Expenses:</td>
                        <td class="text-right" style="padding: 8px;">{{ number_format($totalExpense, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div style="text-align: center; padding: 20px; color: #666;">
                <em>No expense accounts found for the selected criteria</em>
            </div>
        @endif
    </div>

    <!-- Summary Section -->
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td class="font-bold">Total Income:</td>
                <td class="text-right font-bold">{{ number_format($totalIncome, 2) }}</td>
            </tr>
            <tr>
                <td class="font-bold">Total Expenses:</td>
                <td class="text-right font-bold">{{ number_format($totalExpense, 2) }}</td>
            </tr>
            <tr style="border-top: 2px solid #333;">
                <td class="font-bold">Net {{ $totalIncome - $totalExpense >= 0 ? 'Profit' : 'Loss' }}:</td>
                <td class="text-right font-bold {{ $totalIncome - $totalExpense >= 0 ? '' : 'color: red;' }}">
                    {{ number_format($totalIncome - $totalExpense, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text1 = "page {PAGE_NUM} / {PAGE_COUNT}";
            $text2 = "Report generated by: {{ auth()->user()->name ?? 'System' }}";
            $text3 = "Generated by Processton Abacus";
            $size = 6;
            $font = $fontMetrics->getFont("Verdana");
            $width1 = $fontMetrics->get_text_width($text1, $font, $size) / 2;
            $width2 = $fontMetrics->get_text_width($text2, $font, $size) / 2;
            $width3 = $fontMetrics->get_text_width($text3, $font, $size) / 2;
            $footerY = $pdf->get_height() - 12;

            $x = (($pdf->get_width() - $width3) / 2) - 32;


            $x2 = ($pdf->get_width() - $width2) - 78;


            $pdf->page_text(25, $footerY, $text1, $font, $size);
            $pdf->page_text($x2, $footerY, $text2, $font, $size);
            $pdf->page_text($x, $footerY, $text3, $font, $size);
        }
    </script>
</body>
</html>
