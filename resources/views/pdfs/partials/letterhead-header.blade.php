{{--
    Shared hospital branding letterhead — repeats on every page of the PDF
    via dompdf's `position: fixed` + `@page` margin convention (fixed-
    position elements are anchored to the page's content box, so a negative
    `top` equal to the reserved @page margin-top pulls this into that
    margin band and repeats it on every page). The including template's
    `@page` rule MUST reserve a top margin equal to this element's `height`
    (72px) — that's what keeps it flush against whatever comes right after
    it in normal flow, with zero gap.
--}}
@php
    $letterheadName = \App\Models\HospitalSetting::name();
    $letterheadLogo = \App\Models\HospitalSetting::logoDataUri();
    $letterheadContactLine = collect([
        \App\Models\HospitalSetting::get('hospital_address'),
        \App\Models\HospitalSetting::get('hospital_phone'),
        \App\Models\HospitalSetting::get('hospital_email'),
    ])->filter()->implode('   |   ');
@endphp
<style>
    #letterhead-header {
        position: fixed;
        top: -72px;
        left: 0;
        right: 0;
        height: 72px;
        box-sizing: border-box;
        display: table;
        width: 100%;
        border-bottom: 2px solid #111827;
        padding-bottom: 6px;
    }
    #letterhead-header .letterhead-logo-cell {
        display: table-cell;
        width: 56px;
        vertical-align: middle;
    }
    #letterhead-header .letterhead-logo-cell img {
        max-height: 56px;
        max-width: 56px;
    }
    #letterhead-header .letterhead-text-cell {
        display: table-cell;
        vertical-align: middle;
        padding-left: 10px;
    }
    #letterhead-header .letterhead-name {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        letter-spacing: 0.3px;
    }
    #letterhead-header .letterhead-contact {
        font-size: 9px;
        color: #4b5563;
        margin-top: 2px;
    }
</style>
<div id="letterhead-header">
    @if ($letterheadLogo)
        <div class="letterhead-logo-cell">
            <img src="{{ $letterheadLogo }}" alt="{{ $letterheadName }} logo">
        </div>
    @endif
    <div class="letterhead-text-cell">
        <div class="letterhead-name">{{ strtoupper($letterheadName) }}</div>
        @if ($letterheadContactLine)
            <div class="letterhead-contact">{{ $letterheadContactLine }}</div>
        @endif
    </div>
</div>
