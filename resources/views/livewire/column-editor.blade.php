<div class="flex flex-col h-full">
    @if ($selectedTable)
        {{-- Header --}}
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Columns</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $selectedTable->name }}</p>
            </div>
            @if (! $showAddForm && ! $editingColumnId)
                <x-primary-button wire:click="showAdd" class="text-xs !px-3 !py-1.5" id="add-column-button">
                    {{ __('+ Add Column') }}
                </x-primary-button>
            @endif
        </div>

        {{-- Add/Edit Form --}}
        @if ($showAddForm || $editingColumnId)
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <form wire:submit="{{ $editingColumnId ? 'updateColumn' : 'createColumn' }}" id="column-form">
                    <div class="grid grid-cols-2 gap-3">
                        {{-- Name --}}
                        <div>
                            <x-input-label for="col-name" :value="__('Name')" class="text-xs" />
                            <x-text-input wire:model="form.name" id="col-name" type="text" class="mt-1 block w-full text-sm" placeholder="column_name" />
                            <x-input-error :messages="$errors->get('form.name')" class="mt-1" />
                        </div>

                        {{-- Type --}}
                        <div>
                            <x-input-label for="col-type" :value="__('Type')" class="text-xs" />
                            <select wire:model="form.type" id="col-type" class="mt-1 block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($columnTypes as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('form.type')" class="mt-1" />
                        </div>

                        {{-- Length --}}
                        <div>
                            <x-input-label for="col-length" :value="__('Length')" class="text-xs" />
                            <x-text-input wire:model="form.length" id="col-length" type="number" class="mt-1 block w-full text-sm" placeholder="e.g. 255" />
                        </div>

                        {{-- Index Type --}}
                        <div>
                            <x-input-label for="col-index" :value="__('Index')" class="text-xs" />
                            <select wire:model="form.index_type" id="col-index" class="mt-1 block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">None</option>
                                @foreach ($indexTypes as $idx)
                                    <option value="{{ $idx->value }}">{{ $idx->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Default Value --}}
                        <div>
                            <x-input-label for="col-default" :value="__('Default')" class="text-xs" />
                            <x-text-input wire:model="form.default_value" id="col-default" type="text" class="mt-1 block w-full text-sm" placeholder="null" />
                        </div>

                        {{-- FK Table --}}
                        <div>
                            <x-input-label for="col-fk-table" :value="__('FK Table')" class="text-xs" />
                            <x-text-input wire:model="form.fk_table" id="col-fk-table" type="text" class="mt-1 block w-full text-sm" placeholder="e.g. users" />
                        </div>

                        {{-- FK Column --}}
                        <div>
                            <x-input-label for="col-fk-column" :value="__('FK Column')" class="text-xs" />
                            <x-text-input wire:model="form.fk_column" id="col-fk-column" type="text" class="mt-1 block w-full text-sm" placeholder="e.g. id" />
                        </div>

                        {{-- Checkboxes --}}
                        <div class="flex items-end gap-4">
                            <label class="flex items-center gap-1.5 text-sm">
                                <input wire:model="form.is_nullable" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" id="col-nullable">
                                Nullable
                            </label>
                            <label class="flex items-center gap-1.5 text-sm">
                                <input wire:model="form.is_unsigned" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" id="col-unsigned">
                                Unsigned
                            </label>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <x-secondary-button wire:click="cancelForm" type="button" class="text-xs" id="cancel-column-form">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button class="text-xs" id="save-column-button">
                            {{ $editingColumnId ? __('Update') : __('Add Column') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Column List --}}
        <div class="flex-1 overflow-y-auto relative transition-opacity duration-200" wire:loading.class="opacity-50 pointer-events-none" wire:sortable="reorder">
            @forelse ($columns as $column)
                <div
                    wire:sortable.item="{{ $column->id }}"
                    wire:key="column-{{ $column->id }}"
                    class="group flex items-center gap-3 px-4 py-2.5 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150
                        {{ $editingColumnId === $column->id ? 'bg-indigo-50' : '' }}"
                >
                    {{-- Drag Handle --}}
                    <div wire:sortable.handle class="cursor-grab text-gray-300 hover:text-gray-500 py-1 pr-1">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6.75h16.5" />
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-800">{{ $column->name }}</span>
                            @if ($column->is_primary)
                                <span class="text-xs bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded font-medium">PK</span>
                            @endif
                            @if ($column->fk_table)
                                <span class="text-xs bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded font-medium">FK</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-gray-500">{{ $column->type->label() }}</span>
                            @if ($column->length)
                                <span class="text-xs text-gray-400">({{ $column->length }})</span>
                            @endif
                            @if ($column->is_nullable)
                                <span class="text-xs text-gray-400">nullable</span>
                            @endif
                            @if ($column->is_unsigned)
                                <span class="text-xs text-gray-400">unsigned</span>
                            @endif
                            @if ($column->index_type)
                                <span class="text-xs text-gray-400">{{ $column->index_type->label() }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="hidden group-hover:flex items-center gap-1">
                        <button
                            wire:click="editColumn({{ $column->id }})"
                            class="text-gray-400 hover:text-indigo-500 p-0.5"
                            title="Edit"
                            id="edit-column-{{ $column->id }}"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                        <button
                            wire:click="deleteColumn({{ $column->id }})"
                            wire:confirm="Delete column '{{ $column->name }}'?"
                            class="text-gray-400 hover:text-red-500 p-0.5"
                            title="Delete"
                            id="delete-column-{{ $column->id }}"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-gray-400" id="no-columns-message">
                    <p>No columns yet.</p>
                    <p class="mt-1">Click "+ Add Column" to define your schema.</p>
                </div>
            @endforelse
        </div>
    @else
        {{-- No table selected --}}
        <div class="flex-1 flex items-center justify-center p-8" id="no-table-selected">
            <div class="text-center text-gray-400">
                <svg class="mx-auto h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z" />
                </svg>
                <p class="mt-3 text-sm">Select a table from the left panel to edit its columns.</p>
            </div>
        </div>
    @endif
</div>
