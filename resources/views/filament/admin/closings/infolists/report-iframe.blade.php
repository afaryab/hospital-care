@props(['src', 'title', 'showVariant' => false])

<div class="flex flex-col gap-4 py-2" x-data="{{ $showVariant ? "{ variant: 'normal', get iframeSrc() { return '{$src}' + '?variant=' + this.variant } }" : '' }}">
    @if($showVariant)
        <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Print Version</label>
            <select
                x-model="variant"
                class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
            >
                <option value="normal">Full page</option>
                <option value="mini">Mini page</option>
            </select>
        </div>
        <iframe
            :src="iframeSrc"
            title="{{ $title }}"
            class="h-[700px] w-full rounded border"
        ></iframe>
    @else
        <iframe
            src="{{ $src }}"
            title="{{ $title }}"
            class="h-[700px] w-full rounded border"
        ></iframe>
    @endif
</div>
