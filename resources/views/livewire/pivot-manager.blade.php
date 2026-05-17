<div class="flex flex-col h-full">
    {{-- Header --}}
    <div class="p-4 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white z-10">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Relationships</h3>
        @if (! $showAddForm && ! $editingPivotId)
            <x-primary-button wire:click="showAdd" class="text-xs !px-3 !py-1.5" id="add-pivot-button">
                {{ __('+ Add M2M') }}
            </x-primary-button>
        @endif
    </div>

    {{-- Add/Edit Form --}}
    @if ($showAddForm || $editingPivotId)
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <form wire:submit="{{ $editingPivotId ? 'updatePivot' : 'createPivot' }}" id="pivot-form">
                <div class="space-y-3">
                    {{-- Table One --}}
                    <div>
                        <x-input-label for="pivot-table-one" :value="__('Table One')" class="text-xs" />
                        <select wire:model="form.table_one_id" id="pivot-table-one" class="mt-1 block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select a table...</option>
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}">{{ $table->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.table_one_id')" class="mt-1" />
                    </div>

                    {{-- Table Two --}}
                    <div>
                        <x-input-label for="pivot-table-two" :value="__('Table Two')" class="text-xs" />
                        <select wire:model="form.table_two_id" id="pivot-table-two" class="mt-1 block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select a table...</option>
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}">{{ $table->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('form.table_two_id')" class="mt-1" />
                    </div>

                    {{-- Pivot Table Name --}}
                    <div>
                        <x-input-label for="pivot-name" :value="__('Pivot Table Name')" class="text-xs" />
                        <x-text-input wire:model="form.pivot_table_name" id="pivot-name" type="text" class="mt-1 block w-full text-sm" placeholder="Auto-generated if empty" />
                        <x-input-error :messages="$errors->get('form.pivot_table_name')" class="mt-1" />
                    </div>

                    {{-- With Timestamps --}}
                    <div>
                        <label class="flex items-center gap-1.5 text-sm">
                            <input wire:model="form.with_timestamps" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" id="pivot-timestamps">
                            Include timestamps
                        </label>
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <x-secondary-button wire:click="cancelForm" type="button" class="text-xs" id="cancel-pivot-form">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-primary-button class="text-xs" id="save-pivot-button">
                        {{ $editingPivotId ? __('Update') : __('Create') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    @endif

    {{-- Pivot List --}}
    <div class="flex-1 overflow-y-auto">
        @forelse ($pivots as $pivot)
            <div
                wire:key="pivot-{{ $pivot->id }}"
                class="group px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150
                    {{ $editingPivotId === $pivot->id ? 'bg-indigo-50' : '' }}"
            >
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 text-sm">
                            <span class="font-medium text-gray-800">{{ $pivot->tableOne->name }}</span>
                            <svg class="h-3.5 w-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                            </svg>
                            <span class="font-medium text-gray-800">{{ $pivot->tableTwo->name }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-gray-400">via {{ $pivot->pivot_table_name }}</span>
                            @if ($pivot->with_timestamps)
                                <span class="text-xs text-gray-400">• timestamps</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="hidden group-hover:flex items-center gap-1">
                        <button
                            wire:click="editPivot({{ $pivot->id }})"
                            class="text-gray-400 hover:text-indigo-500 p-0.5"
                            title="Edit"
                            id="edit-pivot-{{ $pivot->id }}"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                        <button
                            wire:click="deletePivot({{ $pivot->id }})"
                            wire:confirm="Delete this relationship?"
                            class="text-gray-400 hover:text-red-500 p-0.5"
                            title="Delete"
                            id="delete-pivot-{{ $pivot->id }}"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-sm text-gray-400" id="no-pivots-message">
                <p>No many-to-many relationships yet.</p>
                <p class="mt-1">Click "+ Add M2M" to define one.</p>
            </div>
        @endforelse
    </div>
</div>
