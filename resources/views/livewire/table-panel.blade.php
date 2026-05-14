<div class="flex flex-col h-full">
    {{-- Header --}}
    <div class="p-4 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Tables</h3>

        {{-- Add Table Form --}}
        <form wire:submit="createTable" class="mt-3 flex gap-2">
            <x-text-input
                wire:model="form.name"
                type="text"
                class="flex-1 text-sm"
                placeholder="New table name..."
                id="new-table-name"
            />
            <x-primary-button class="text-xs !px-3 !py-1.5" id="add-table-button">
                {{ __('Add') }}
            </x-primary-button>
        </form>
        <x-input-error :messages="$errors->get('form.name')" class="mt-1" />
    </div>

    {{-- Table List --}}
    <div class="flex-1 overflow-y-auto">
        @forelse ($tables as $table)
            <div
                wire:key="table-{{ $table->id }}"
                class="group flex items-center gap-2 px-4 py-2.5 border-b border-gray-100 cursor-pointer transition-colors duration-150
                    {{ $selectedTableId === $table->id ? 'bg-indigo-50 border-l-2 border-l-indigo-500' : 'hover:bg-gray-50 border-l-2 border-l-transparent' }}"
            >
                @if ($editingTableId === $table->id)
                    {{-- Inline Edit Mode --}}
                    <form wire:submit="saveEdit" class="flex-1 flex gap-2">
                        <x-text-input
                            wire:model="editingName"
                            type="text"
                            class="flex-1 text-sm !py-1"
                            id="edit-table-name-{{ $table->id }}"
                            autofocus
                        />
                        <button type="submit" class="text-green-600 hover:text-green-800" title="Save">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </button>
                        <button type="button" wire:click="cancelEdit" class="text-gray-400 hover:text-gray-600" title="Cancel">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                @else
                    {{-- Normal Display --}}
                    <div wire:click="selectTable({{ $table->id }})" class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 truncate block">{{ $table->name }}</span>
                        <span class="text-xs text-gray-400">{{ $table->columns_count }} cols</span>
                    </div>

                    {{-- Actions (visible on hover) --}}
                    <div class="hidden group-hover:flex items-center gap-1">
                        <button
                            wire:click.stop="startEditing({{ $table->id }})"
                            class="text-gray-400 hover:text-indigo-500 p-0.5"
                            title="Rename"
                            id="rename-table-{{ $table->id }}"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                        <button
                            wire:click.stop="deleteTable({{ $table->id }})"
                            wire:confirm="Delete table '{{ $table->name }}' and all its columns?"
                            class="text-gray-400 hover:text-red-500 p-0.5"
                            title="Delete"
                            id="delete-table-{{ $table->id }}"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-8 text-center text-sm text-gray-400" id="no-tables-message">
                <p>No tables yet.</p>
                <p class="mt-1">Add one above to get started.</p>
            </div>
        @endforelse
    </div>
</div>
