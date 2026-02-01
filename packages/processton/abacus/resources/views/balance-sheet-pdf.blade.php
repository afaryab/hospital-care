<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Balance Sheet</title>
    <style>
        @page {
            size: A4;
            margin: 0.3in;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
            line-height: 1.1;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .report-date {
            font-size: 10px;
            margin-bottom: 2px;
        }
        
        .balance-sheet-container {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .left-side, .right-side {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 8px;
        }
        
        .left-side {
            border-right: 2px solid #000;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
            padding: 3px 0;
            border-bottom: 1px solid #000;
            text-transform: uppercase;
        }
        
        .account-group {
            margin-bottom: 10px;
        }
        
        .group-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 3px;
            text-decoration: underline;
        }
        
        .account-line {
            display: table;
            width: 100%;
            padding: 1px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .account-name {
            display: table-cell;
            width: 70%;
            padding: 2px 0px 0px 10px;
            font-weight: normal;

        }
        
        .account-amount {
            display: table-cell;
            width: 30%;
            text-align: right;
            padding: 2px 0px 0px 0px;
            font-family: 'Courier New', monospace;
        }
        
        .group-total {
            display: table;
            width: 100%;
            padding: 2px 0;
            margin-top: 3px;
            border-top: 1px solid #000;
            font-weight: bold;
        }
        
        .group-total-name {
            display: table-cell;
            width: 70%;
            padding-left: 3px;
        }
        
        .group-total-amount {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .section-total {
            display: table;
            width: 100%;
            padding: 3px 0;
            margin: 8px 0;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-weight: bold;
            font-size: 10px;
        }
        
        .section-total-name {
            display: table-cell;
            width: 70%;
            text-align: center;
        }
        
        .section-total-amount {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        
        .balance-verification {
            margin-top: 15px;
            padding: 8px;
            border: 2px solid #000;
            text-align: center;
        }
        
        .balance-check {
            display: table;
            width: 100%;
            margin: 3px 0;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #000;
            font-size: 8px;
            text-align: center;
        }
        
        .empty-section {
            text-align: center;
            padding: 15px;
            font-style: italic;
            color: #666;
        }
        
        .text-green {
            color: green;
        }
        
        .text-red {
            color: red;
        }
        .last-account{
            border-bottom: none !important;
        }
        
        @media print {
            body { margin: 0; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">YOUR COMPANY NAME</div>
        <div class="report-title">BALANCE SHEET</div>
        @if($year)
            <div class="report-date">As at {{ $year->end_date->format('F d, Y') }}</div>
            <div class="report-date">For the Year Ended {{ $year->end_date->format('F d, Y') }}</div>
        @else
            <div class="report-date">As at {{ now()->format('F d, Y') }}</div>
        @endif
    </div>

    <div class="balance-sheet-container">
        <!-- LEFT SIDE - ASSETS -->
        <div class="left-side">
            <div class="section-title">Assets</div>
            
            @if($assets->isNotEmpty())
                @foreach($assets as $assetGroup)
                    <div class="account-group">
                        <div class="group-title">{{ $assetGroup['name'] }}</div>
                        @if($assetGroup['children']->isNotEmpty())
                            @foreach($assetGroup['children'] as $index => $account)
                                <div class="account-line {{ count($assetGroup['children']) == $index + 1 ? 'last-account' : '' }}">
                                    <div class="account-name">{{ $account['name'] }}</div>
                                    <div class="account-amount">{{ number_format($account['amount'], 2) }}</div>
                                </div>
                            @endforeach
                            <div class="group-total">
                                <div class="group-total-name">Total {{ $assetGroup['name'] }}</div>
                                <div class="group-total-amount">{{ number_format($assetGroup['children']->sum('amount'), 2) }}</div>
                            </div>
                        @else
                            <div class="account-line">
                                <div class="account-name" style="font-style: italic; color: #666;">No accounts in this group</div>
                                <div class="account-amount">0.00</div>
                            </div>
                            <div class="group-total">
                                <div class="group-total-name">Total {{ $assetGroup['name'] }}</div>
                                <div class="group-total-amount">0.00</div>
                            </div>
                        @endif
                    </div>
                @endforeach
                
                <div class="section-total">
                    <div class="section-total-name">TOTAL ASSETS</div>
                    <div class="section-total-amount">{{ number_format($totalAssets, 2) }}</div>
                </div>
            @else
                <div class="empty-section">
                    No asset accounts found for the selected year
                </div>
            @endif
        </div>

        <!-- RIGHT SIDE - LIABILITIES & EQUITY -->
        <div class="right-side">
            <div class="section-title">Liabilities & Equity</div>
            
            <!-- Liabilities -->
            @if($liabilities->isNotEmpty())
                @foreach($liabilities as $liabilityGroup)
                    <div class="account-group">
                        <div class="group-title">{{ $liabilityGroup['name'] }}</div>
                        @if($liabilityGroup['children']->isNotEmpty())
                            @foreach($liabilityGroup['children'] as $index => $account)
                                <div class="account-line {{ count($liabilityGroup['children']) == $index + 1 ? 'last-account' : '' }}">
                                    <div class="account-name">{{ $account['name'] }}</div>
                                    <div class="account-amount">{{ number_format($account['amount'], 2) }}</div>
                                </div>
                            @endforeach
                            <div class="group-total">
                                <div class="group-total-name">Total {{ $liabilityGroup['name'] }}</div>
                                <div class="group-total-amount">{{ number_format($liabilityGroup['children']->sum('amount'), 2) }}</div>
                            </div>
                        @else
                            <div class="account-line">
                                <div class="account-name" style="font-style: italic; color: #666;">No accounts in this group</div>
                                <div class="account-amount">0.00</div>
                            </div>
                            <div class="group-total">
                                <div class="group-total-name">Total {{ $liabilityGroup['name'] }}</div>
                                <div class="group-total-amount">0.00</div>
                            </div>
                        @endif
                    </div>
                @endforeach
                
                <div class="section-total">
                    <div class="section-total-name">TOTAL LIABILITIES</div>
                    <div class="section-total-amount">{{ number_format($totalLiabilities, 2) }}</div>
                </div>
            @else
                <div class="empty-section">
                    No liability accounts found
                </div>
            @endif
            
            <!-- Equity -->
            @if($equity->isNotEmpty())
                @foreach($equity as $equityGroup)
                    <div class="account-group">
                        <div class="group-title">{{ $equityGroup['name'] }}</div>
                        @if($equityGroup['children']->isNotEmpty())
                            @foreach($equityGroup['children'] as $index => $account)
                                <div class="account-line {{ count($equityGroup['children']) == $index + 1 ? 'last-account' : '' }}">
                                    <div class="account-name">{{ $account['name'] }}</div>
                                    <div class="account-amount">{{ number_format($account['amount'], 2) }}</div>
                                </div>
                            @endforeach
                            <div class="group-total">
                                <div class="group-total-name">Total {{ $equityGroup['name'] }}</div>
                                <div class="group-total-amount">{{ number_format($equityGroup['children']->sum('amount'), 2) }}</div>
                            </div>
                        @else
                            <div class="account-line">
                                <div class="account-name" style="font-style: italic; color: #666;">No accounts in this group</div>
                                <div class="account-amount">0.00</div>
                            </div>
                            <div class="group-total">
                                <div class="group-total-name">Total {{ $equityGroup['name'] }}</div>
                                <div class="group-total-amount">0.00</div>
                            </div>
                        @endif
                    </div>
                @endforeach
                
                <div class="section-total">
                    <div class="section-total-name">TOTAL EQUITY</div>
                    <div class="section-total-amount">{{ number_format($totalEquity, 2) }}</div>
                </div>
            @else
                <div class="empty-section">
                    No equity accounts found
                </div>
            @endif
            
            <!-- Total Liabilities + Equity -->
            <div class="section-total" style="margin-top: 15px;">
                <div class="section-total-name">TOTAL LIABILITIES & EQUITY</div>
                <div class="section-total-amount">{{ number_format($totalLiabilities + $totalEquity, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Balance Verification -->
    <div class="balance-verification">
        <div class="balance-check">
            <span style="display: table-cell; width: 70%;">Total Assets:</span>
            <span style="display: table-cell; width: 30%; text-align: right;">{{ number_format($totalAssets, 2) }}</span>
        </div>
        <div class="balance-check">
            <span style="display: table-cell; width: 70%;">Total Liabilities & Equity:</span>
            <span style="display: table-cell; width: 30%; text-align: right;">{{ number_format($totalLiabilities + $totalEquity, 2) }}</span>
        </div>
        <div class="balance-check" style="border-top: 1px solid #000; padding-top: 5px; margin-top: 5px;">
            <span style="display: table-cell; width: 70%;">Balance Check (Should be 0.00):</span>
            <span style="display: table-cell; width: 30%; text-align: right;" class="{{ abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01 ? 'text-green' : 'text-red' }}">
                {{ number_format($totalAssets - ($totalLiabilities + $totalEquity), 2) }}
            </span>
        </div>
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
