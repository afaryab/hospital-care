@php
    $hospitalLogoUrl = \App\Models\HospitalSetting::logoUrl();
    $hospitalName = \App\Models\HospitalSetting::name();
@endphp

<div class="flex items-center gap-2">
    @if ($hospitalLogoUrl)
        {{-- Uploaded hospital logos can have transparent or dark backgrounds
             that vanish or clash against the panel's own chrome — a white
             backing keeps them legible in both light and dark mode. --}}
        <span class="inline-flex items-center justify-center rounded-md bg-white p-1">
            <img src="{{ $hospitalLogoUrl }}" alt="{{ $hospitalName }}" class="h-6 w-auto" style="max-height: 24px;">
        </span>
    @else
        <img src="{{ asset('logo.png') }}" alt="{{ $hospitalName }}" class="h-8 w-auto" style="max-height: 24px; display:inline-block; margin-left:4px;">
    @endif

    <span class="text-xl font-light">
        {{ $hospitalName }}
    </span>
</div>
