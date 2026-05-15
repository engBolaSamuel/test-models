<x-slot name="header">
    <div class="flex items-center gap-4">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition-colors duration-150">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $project->name }}
        </h2>
        
        <div class="ml-auto flex items-center space-x-3">
            @if($historyCount > 0)
                <button wire:click="undo" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                    <svg wire:loading.remove wire:target="undo" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                    <svg wire:loading wire:target="undo" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Undo
                </button>
            @endif

            <button wire:click="exportMermaid" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <svg wire:loading.remove wire:target="exportMermaid" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <svg wire:loading wire:target="exportMermaid" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Export .mmd
            </button>

            <button wire:click="exportMigration" wire:loading.attr="disabled" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                <svg wire:loading.remove wire:target="exportMigration" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <svg wire:loading wire:target="exportMigration" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Export Migration
            </button>
        </div>
    </div>
</x-slot>

<div class="flex-1 overflow-auto lg:overflow-hidden">
    <div class="min-h-[calc(100vh-8rem)] lg:h-[calc(100vh-8rem)] flex flex-col lg:flex-row gap-4 lg:gap-0 p-4 lg:p-0">
        
        {{-- Left Section --}}
        <div class="w-full lg:w-1/2 flex flex-col gap-4 lg:gap-0 lg:border-r lg:border-gray-200">
            
            {{-- Top Section: Tables and Columns --}}
            <div class="flex-1 flex flex-col lg:flex-row gap-4 lg:gap-0 min-h-0 lg:border-b lg:border-gray-200">
                {{-- Left Panel: Tables --}}
                <div class="w-full lg:w-1/3 border border-gray-200 lg:border-none lg:border-r lg:border-gray-200 rounded-lg lg:rounded-none bg-white overflow-y-auto h-96 lg:h-auto" id="panel-tables">
                    <livewire:table-panel :project="$project" />
                </div>

                {{-- Center Panel: Column Editor --}}
                <div class="w-full lg:w-2/3 border border-gray-200 lg:border-none rounded-lg lg:rounded-none bg-gray-50 overflow-y-auto h-96 lg:h-auto" id="panel-columns">
                    <livewire:column-editor :project="$project" />
                </div>
            </div>

            {{-- Bottom Section: Pivot Manager --}}
            <div class="h-96 lg:h-[40%] border border-gray-200 lg:border-none rounded-lg lg:rounded-none bg-white overflow-y-auto shrink-0" id="panel-pivots">
                <livewire:pivot-manager :project="$project" />
            </div>
        </div>

        {{-- Right Section: Mermaid Preview or Editor --}}
        <div class="w-full lg:w-1/2 border border-gray-200 lg:border-none rounded-lg lg:rounded-none bg-gray-900 overflow-hidden flex flex-col h-96 lg:h-auto" id="panel-mermaid">
            <div class="flex items-center justify-center p-2 bg-gray-950 border-b border-gray-800 shrink-0">
                <div class="bg-gray-900 p-1 rounded-lg flex space-x-1">
                    <button 
                        wire:click="$set('rightPanelMode', 'preview')"
                        @class([
                            'px-4 py-1.5 text-xs font-medium rounded-md transition-colors',
                            'bg-gray-700 text-white shadow' => $rightPanelMode === 'preview',
                            'text-gray-400 hover:text-gray-200 hover:bg-gray-800' => $rightPanelMode !== 'preview',
                        ])
                    >
                        Visual Diagram
                    </button>
                    <button 
                        wire:click="$set('rightPanelMode', 'editor')"
                        @class([
                            'px-4 py-1.5 text-xs font-medium rounded-md transition-colors',
                            'bg-gray-700 text-white shadow' => $rightPanelMode === 'editor',
                            'text-gray-400 hover:text-gray-200 hover:bg-gray-800' => $rightPanelMode !== 'editor',
                        ])
                    >
                        Code Editor
                    </button>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto relative min-h-0">
                @if($rightPanelMode === 'preview')
                    <livewire:mermaid-preview :project="$project" />
                @else
                    <livewire:mermaid-editor :project="$project" />
                @endif
            </div>
        </div>
    </div>
</div>
