{{-- Shared report footer partial --}}
    {{-- Footer --}}
    <div class="report-footer">
        <div class="report-footer-left">
            {{ config('app.name') }} &middot; {{ $report_title }}
        </div>
        <div class="report-footer-right">
            Generated: @hdate($generated_at, 'd M Y, H:i:s')
        </div>
    </div>
</div> {{-- End .page --}}
