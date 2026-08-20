<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 16px; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td, th { padding: 6px 8px; border: 1px solid #ccc; text-align: left; }
        .label { font-weight: bold; width: 30%; background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $hospital_info['name'] }}</h1>
        <div>Expense Voucher</div>
    </div>

    <table>
        <tr><td class="label">Voucher Number</td><td>{{ $voucher->vc_number }}</td></tr>
        <tr><td class="label">Paid To</td><td>{{ $voucher->payedTo?->name ?? $voucher->payed_to_name }}</td></tr>
        <tr><td class="label">Category</td><td>{{ $voucher->expCategory?->name }}</td></tr>
        <tr><td class="label">Amount</td><td>{{ number_format((float) $voucher->amount, 2) }}</td></tr>
        <tr><td class="label">Notes</td><td>{{ $voucher->notes }}</td></tr>
        <tr><td class="label">Date</td><td>{{ $voucher->created_at?->format('d M Y, H:i') }}</td></tr>
    </table>
</body>
</html>
