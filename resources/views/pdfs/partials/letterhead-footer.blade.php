{{--
    Shared repeating footer — hospital name and page number, using the same
    `position: fixed` + `@page` margin-bottom technique as
    letterhead-header.blade.php (negative `bottom` pulls it into the
    reserved bottom margin band). `counter(page)`/`counter(pages)` is
    dompdf's built-in page-numbering support, valid inside a fixed-position
    running element — no PHP callback needed. The including template's
    `@page` rule must reserve a bottom margin equal to this element's
    `height` (34px).
--}}
@php
    $letterheadFooterName = \App\Models\HospitalSetting::name();
@endphp
<style>
    #letterhead-footer {
        position: fixed;
        bottom: -34px;
        left: 0;
        right: 0;
        height: 34px;
        box-sizing: border-box;
        display: table;
        width: 100%;
        border-top: 1px solid #d1d5db;
        padding-top: 6px;
        font-size: 9px;
        color: #6b7280;
    }
    #letterhead-footer .letterhead-footer-left {
        display: table-cell;
        text-align: left;
    }
    #letterhead-footer .letterhead-footer-right {
        display: table-cell;
        text-align: right;
    }
    #letterhead-footer .letterhead-page-num:after {
        content: counter(page) " / " counter(pages);
    }
</style>
<div id="letterhead-footer">
    <div class="letterhead-footer-left">{{ $letterheadFooterName }}</div>
    <div class="letterhead-footer-right">Page <span class="letterhead-page-num"></span></div>
</div>
