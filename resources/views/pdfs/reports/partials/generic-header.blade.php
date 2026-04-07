{{-- Generic report header partial (not per-closing) --}}
{{-- Variables: $report_title, $report_color, $from, $until, $generated_at, $filters (optional) --}}
@php
    $hospitalName = \App\Models\HospitalSetting::get('hospital_name', config('app.name'));
    $hospitalAddress = \App\Models\HospitalSetting::get('hospital_address');
    $hospitalNtn = \App\Models\HospitalSetting::get('hospital_ntn');
    $hospitalStrn = \App\Models\HospitalSetting::get('hospital_strn');
@endphp
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { color: #1f2937; font-family: Helvetica, Arial, sans-serif; font-size: 11px; line-height: 1.5; }
    .page { padding: 20px 24px; max-width: 900px; margin: 0 auto; }

    .report-header { display: table; width: 100%; margin-bottom: 16px; border-bottom: 3px solid {{ $report_color ?? '#1f2937' }}; padding-bottom: 12px; }
    .report-header-left { display: table-cell; vertical-align: top; width: 55%; }
    .report-header-right { display: table-cell; vertical-align: top; text-align: right; width: 45%; }
    .company-name { font-size: 16px; font-weight: 700; color: #111827; letter-spacing: 0.5px; }
    .company-sub { font-size: 10px; color: #6b7280; margin-top: 2px; }
    .report-title { font-size: 16px; font-weight: 700; color: {{ $report_color ?? '#1f2937' }}; text-transform: uppercase; letter-spacing: 1px; }
    .report-period { font-size: 11px; color: #374151; margin-top: 4px; font-weight: 600; }

    .info-grid { display: table; width: 100%; margin-bottom: 14px; border: 1px solid #e5e7eb; }
    .info-grid-row { display: table-row; }
    .info-cell { display: table-cell; padding: 5px 10px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
    .info-cell:first-child { width: 50%; }
    .info-label { color: #6b7280; text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px; font-weight: 600; }
    .info-value { color: #111827; font-weight: 500; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 11px; }
    th, td { border: 1px solid #d1d5db; padding: 5px 8px; vertical-align: top; text-align: left; }
    thead th { background: #f9fafb; color: #374151; font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 700; }
    .amount { text-align: right; white-space: nowrap; font-family: 'Courier New', monospace; }
    .total-row td { background: #f3f4f6; font-weight: 700; border-top: 2px solid #9ca3af; }
    .subtotal-row td { background: #f9fafb; font-weight: 600; }
    .section-title { background: {{ $report_color ?? '#1f2937' }}; color: #ffffff; font-weight: 700; font-size: 11px; padding: 6px 10px; margin: 14px 0 4px; text-transform: uppercase; letter-spacing: 0.5px; }
    .sub-section { background: #f3f4f6; font-weight: 600; font-size: 11px; padding: 5px 10px; margin: 8px 0 2px; border-left: 3px solid {{ $report_color ?? '#1f2937' }}; }
    .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 9px; font-weight: 700; letter-spacing: 0.3px; }
    .badge-green { background: #d1fae5; color: #065f46; }
    .badge-red { background: #fee2e2; color: #991b1b; }
    .badge-orange { background: #ffedd5; color: #9a3412; }
    .badge-blue { background: #dbeafe; color: #1e40af; }
    .badge-purple { background: #f3e8ff; color: #6b21a8; }
    .badge-amber { background: #fef3c7; color: #92400e; }
    .text-right { text-align: right; }
    .text-muted { color: #6b7280; }
    .text-bold { font-weight: 700; }
    .mono { font-family: 'Courier New', monospace; font-size: 10px; }
    .empty-state { text-align: center; color: #9ca3af; padding: 30px 0; font-size: 12px; font-style: italic; }

    .report-footer { margin-top: 20px; padding-top: 8px; border-top: 2px solid #e5e7eb; display: table; width: 100%; font-size: 9px; color: #9ca3af; }
    .report-footer-left { display: table-cell; vertical-align: bottom; }
    .report-footer-right { display: table-cell; text-align: right; vertical-align: bottom; }
</style>

<div class="page">
    <div class="report-header">
        <div class="report-header-left">
            <div class="company-name">{{ $hospitalName }}</div>
            <div class="company-sub">{{ $hospitalAddress ?: 'Hospital Management System' }}</div>
        </div>
        <div class="report-header-right">
            <div class="report-title">{{ $report_title }}</div>
            <div class="report-period">{{ $from->format('d M Y') }} &mdash; {{ $until->format('d M Y') }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-grid-row">
            <div class="info-cell">
                <span class="info-label">Period</span><br>
                <span class="info-value">{{ $from->format('d M Y') }} to {{ $until->format('d M Y') }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">Generated</span><br>
                <span class="info-value">{{ $generated_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
        <div class="info-grid-row">
            <div class="info-cell">
                <span class="info-label">NTN</span><br>
                <span class="info-value">{{ $hospitalNtn ?: '-' }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">STRN</span><br>
                <span class="info-value">{{ $hospitalStrn ?: '-' }}</span>
            </div>
        </div>
    </div>
