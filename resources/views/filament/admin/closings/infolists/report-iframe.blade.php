@props(['src', 'title', 'showVariant' => false])

<div class="flex flex-col gap-4 py-2" style="height: calc(100vh - 220px);" x-data="{{ $showVariant ? "{ variant: 'normal', get iframeSrc() { return '{$src}' + '?variant=' + this.variant } }" : '' }}">
    @if($showVariant)
        <div class="flex items-center gap-3 shrink-0">
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
            class="w-full rounded border flex-1 min-h-0"
            style="height: 100%;"
        ></iframe>
    @else
        <iframe
            src="{{ $src }}"
            title="{{ $title }}"
            class="w-full rounded border flex-1 min-h-0"
            style="height: 100%;"
        ></iframe>
    @endif
</div>
