<x-filament-panels::page>
    <div class="flex flex-col gap-6">

        {{-- Breadcrumbs --}}
        <nav class="flex flex-wrap items-center gap-1 text-sm">
            <button type="button" wire:click="openFolder(null)" class="fi-link text-primary-600 hover:underline">
                Root
            </button>
            @foreach ($this->breadcrumbs() as $crumb)
                <span class="text-gray-400">/</span>
                <button type="button" wire:click="openFolder({{ $crumb->id }})" class="fi-link text-primary-600 hover:underline">
                    {{ $crumb->name }}
                </button>
            @endforeach
        </nav>

        {{-- Toolbar: create folder / upload / extract zip --}}
        <div class="grid grid-cols-1 gap-4 rounded-xl border border-gray-200 p-4 dark:border-gray-700 md:grid-cols-3">
            <form wire:submit="createFolder" class="flex flex-col gap-2">
                <label class="text-sm font-medium">New Folder</label>
                <input type="text" wire:model="newFolderName" placeholder="Folder name"
                       class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                @error('newFolderName') <span class="text-xs text-danger-600">{{ $message }}</span> @enderror
                <select wire:model="newFolderClassificationId" class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                    <option value="">No classification</option>
                    @foreach ($this->classificationOptions() as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="fi-btn fi-btn-color-primary rounded-lg bg-primary-600 px-3 py-1.5 text-sm text-white">
                    Create Folder
                </button>
            </form>

            <form wire:submit="uploadDocument" class="flex flex-col gap-2">
                <label class="text-sm font-medium">Upload Document</label>
                <input type="file" wire:model="uploadFile" class="text-sm">
                @error('uploadFile') <span class="text-xs text-danger-600">{{ $message }}</span> @enderror
                <button type="submit" wire:loading.attr="disabled" @disabled(! $this->currentFolder())
                        class="fi-btn rounded-lg bg-primary-600 px-3 py-1.5 text-sm text-white disabled:opacity-50">
                    Upload {{ $this->currentFolder() ? '' : '(open a folder first)' }}
                </button>
            </form>

            <form wire:submit="extractZip" class="flex flex-col gap-2">
                <label class="text-sm font-medium">Extract Zip Into This Folder</label>
                <input type="file" wire:model="zipUploadFile" accept=".zip" class="text-sm">
                @error('zipUploadFile') <span class="text-xs text-danger-600">{{ $message }}</span> @enderror
                <p class="text-xs text-gray-500">Every entry is checked for entry-count, total size, and compression-ratio limits before anything is extracted — protects against zip-bomb uploads.</p>
                <button type="submit" wire:loading.attr="disabled" @disabled(! $this->currentFolder())
                        class="fi-btn rounded-lg bg-gray-600 px-3 py-1.5 text-sm text-white disabled:opacity-50">
                    Extract
                </button>
            </form>
        </div>

        {{-- Folder contents --}}
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Type</th>
                        <th class="px-4 py-2 text-left">Classification</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->folders() as $folder)
                        <tr class="border-t border-gray-100 dark:border-gray-800">
                            <td class="px-4 py-2">
                                <button type="button" wire:click="openFolder({{ $folder->id }})" class="font-medium text-primary-600 hover:underline">
                                    📁 {{ $folder->name }}
                                </button>
                                @if ($folder->is_system)
                                    <span class="ms-1 rounded bg-gray-200 px-1.5 py-0.5 text-xs dark:bg-gray-700">system</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-gray-500">Folder</td>
                            <td class="px-4 py-2 text-gray-500">{{ $folder->classification?->name }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                @unless ($folder->is_system)
                                    <button type="button" wire:click="startRenameFolder({{ $folder->id }})" class="text-xs text-gray-600 hover:underline">Rename</button>
                                    <button type="button" wire:click="startMove('folder', {{ $folder->id }})" class="text-xs text-gray-600 hover:underline">Move</button>
                                @endunless
                                <button type="button" wire:click="startCopy('folder', {{ $folder->id }})" class="text-xs text-gray-600 hover:underline">Copy</button>
                                <a href="{{ route('dms.folders.zip', $folder) }}" class="text-xs text-gray-600 hover:underline">Download Zip</a>
                                @unless ($folder->is_system)
                                    <button type="button" wire:click="deleteFolder({{ $folder->id }})" wire:confirm="Delete this folder?" class="text-xs text-danger-600 hover:underline">Delete</button>
                                @endunless
                            </td>
                        </tr>
                        @if ($renamingFolderId === $folder->id)
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <td colspan="4" class="px-4 py-2">
                                    <form wire:submit="confirmRename" class="flex items-center gap-2">
                                        <input type="text" wire:model="renameValue" class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                        <button type="submit" class="fi-btn rounded bg-primary-600 px-2 py-1 text-xs text-white">Save</button>
                                        <button type="button" wire:click="cancelRename" class="text-xs text-gray-500">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                        @if ($movingType === 'folder' && $movingId === $folder->id)
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <td colspan="4" class="px-4 py-2">
                                    <form wire:submit="confirmMove" class="flex items-center gap-2">
                                        <select wire:model="moveTargetFolderId" class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                            <option value="">Move to…</option>
                                            @foreach ($this->folderOptions() as $id => $label)
                                                <option value="{{ $id }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="fi-btn rounded bg-primary-600 px-2 py-1 text-xs text-white">Move</button>
                                        <button type="button" wire:click="cancelMove" class="text-xs text-gray-500">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                        @if ($copyingType === 'folder' && $copyingId === $folder->id)
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <td colspan="4" class="px-4 py-2">
                                    <form wire:submit="confirmCopy" class="flex items-center gap-2">
                                        <select wire:model="copyTargetFolderId" class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                            <option value="">Copy to…</option>
                                            @foreach ($this->folderOptions() as $id => $label)
                                                <option value="{{ $id }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="fi-btn rounded bg-primary-600 px-2 py-1 text-xs text-white">Copy</button>
                                        <button type="button" wire:click="cancelCopy" class="text-xs text-gray-500">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @empty
                    @endforelse

                    @forelse ($this->documents() as $document)
                        <tr class="border-t border-gray-100 dark:border-gray-800">
                            <td class="px-4 py-2">
                                📄 {{ $document->name }}
                                @if ($document->is_locked)
                                    <span class="ms-1 rounded bg-warning-100 px-1.5 py-0.5 text-xs text-warning-700">locked by {{ $document->lockedBy?->name }}</span>
                                @endif
                                <span class="ms-1 text-xs text-gray-400">v{{ $document->current_version }}</span>
                            </td>
                            <td class="px-4 py-2 text-gray-500">Document</td>
                            <td class="px-4 py-2 text-gray-500">{{ $document->classification?->name }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <a href="{{ route('dms.documents.download', $document) }}" class="text-xs text-gray-600 hover:underline">Download</a>
                                @if ($this->isOfficeEditable($document))
                                    <a href="{{ route('onlyoffice.editor', $document) }}" target="_blank" class="text-xs text-gray-600 hover:underline">Edit</a>
                                @endif
                                <button type="button" wire:click="startRenameDocument({{ $document->id }})" class="text-xs text-gray-600 hover:underline">Rename</button>
                                <button type="button" wire:click="startMove('document', {{ $document->id }})" class="text-xs text-gray-600 hover:underline">Move</button>
                                <button type="button" wire:click="startCopy('document', {{ $document->id }})" class="text-xs text-gray-600 hover:underline">Copy</button>
                                <button type="button" wire:click="startShare({{ $document->id }})" class="text-xs text-gray-600 hover:underline">Share</button>
                                @if ($document->is_locked)
                                    <button type="button" wire:click="unlockDocument({{ $document->id }})" class="text-xs text-gray-600 hover:underline">Unlock</button>
                                @else
                                    <button type="button" wire:click="lockDocument({{ $document->id }})" class="text-xs text-gray-600 hover:underline">Lock</button>
                                @endif
                                <button type="button" wire:click="deleteDocument({{ $document->id }})" wire:confirm="Delete this document?" class="text-xs text-danger-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                        @if ($renamingDocumentId === $document->id)
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <td colspan="4" class="px-4 py-2">
                                    <form wire:submit="confirmRename" class="flex items-center gap-2">
                                        <input type="text" wire:model="renameValue" class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                        <button type="submit" class="fi-btn rounded bg-primary-600 px-2 py-1 text-xs text-white">Save</button>
                                        <button type="button" wire:click="cancelRename" class="text-xs text-gray-500">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                        @if ($movingType === 'document' && $movingId === $document->id)
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <td colspan="4" class="px-4 py-2">
                                    <form wire:submit="confirmMove" class="flex items-center gap-2">
                                        <select wire:model="moveTargetFolderId" class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                            <option value="">Move to…</option>
                                            @foreach ($this->folderOptions() as $id => $label)
                                                <option value="{{ $id }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="fi-btn rounded bg-primary-600 px-2 py-1 text-xs text-white">Move</button>
                                        <button type="button" wire:click="cancelMove" class="text-xs text-gray-500">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                        @if ($copyingType === 'document' && $copyingId === $document->id)
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <td colspan="4" class="px-4 py-2">
                                    <form wire:submit="confirmCopy" class="flex items-center gap-2">
                                        <select wire:model="copyTargetFolderId" class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                            <option value="">Copy to…</option>
                                            @foreach ($this->folderOptions() as $id => $label)
                                                <option value="{{ $id }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="fi-btn rounded bg-primary-600 px-2 py-1 text-xs text-white">Copy</button>
                                        <button type="button" wire:click="cancelCopy" class="text-xs text-gray-500">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                        @if ($sharingDocumentId === $document->id)
                            <tr class="bg-gray-50 dark:bg-gray-800">
                                <td colspan="4" class="px-4 py-2">
                                    <form wire:submit="confirmShare" class="flex items-center gap-2">
                                        <input type="email" wire:model="shareEmail" placeholder="recipient@example.com"
                                               class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                        <select wire:model="shareAbility" class="fi-input rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                            <option value="view">View</option>
                                            <option value="edit">Edit</option>
                                        </select>
                                        <button type="submit" class="fi-btn rounded bg-primary-600 px-2 py-1 text-xs text-white">Send</button>
                                        <button type="button" wire:click="cancelShare" class="text-xs text-gray-500">Cancel</button>
                                    </form>
                                    @error('shareEmail') <span class="text-xs text-danger-600">{{ $message }}</span> @enderror
                                </td>
                            </tr>
                        @endif
                    @empty
                        @if ($this->folders()->isEmpty())
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400">This folder is empty.</td>
                            </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
