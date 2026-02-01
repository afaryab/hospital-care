<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>General Ledger Report</title>
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
        <div class="report-title">General Ledger Report</div>
        <div style="font-size: 12px; color: #666;">Generated on {{ $generatedAt }}</div>
    </div>

    <div class="report-info">
        <div class="info-row">
            <span class="info-label">Account:</span>
            <span>{{ $account ? $account->code . ' - ' . $account->name : 'N/A' }}</span>
        </div>
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
            <span class="info-label">Total Entries:</span>
            <span>{{ $entries->count() }}</span>
        </div>
    </div>

    @if($entries->isNotEmpty())
        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width: 17%;">Date</th>
                    <th style="width: 25%;">Account</th>
                    <th style="width: 20%; text-align: right;">Debit</th>
                    <th style="width: 20%; text-align: right;">Credit</th>
                    <th style="width: 18%; text-align: right;">Running Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $entry)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($entry->date)->format('M d, Y') }}</td>
                        <td class="text-left">{{ $entry->account->code.' '.$entry->account->name ?? 'N/A' }}</td>
                        <td class="text-right">
                            {{ $entry->debit > 0 ? number_format($entry->debit, 2) : '' }}
                        </td>
                        <td class="text-right">
                            {{ $entry->credit > 0 ? number_format($entry->credit, 2) : '' }}
                        </td>
                        <td class="text-right font-bold">
                            {{ number_format($entry->running_balance, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="font-bold">Total Debit:</td>
                    <td class="text-right font-bold">{{ number_format($totalDebit, 2) }}</td>
                </tr>
                <tr>
                    <td class="font-bold">Total Credit:</td>
                    <td class="text-right font-bold">{{ number_format($totalCredit, 2) }}</td>
                </tr>
                <tr style="border-top: 2px solid #333;">
                    <td class="font-bold">Net Balance:</td>
                    <td class="text-right font-bold">{{ number_format($totalDebit - $totalCredit, 2) }}</td>
                </tr>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <h3>No entries found for the selected criteria</h3>
        </div>
    @endif

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
