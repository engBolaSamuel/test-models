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
    </div>
</x-slot>

<div class="flex-1 overflow-hidden">
    <div class="h-[calc(100vh-8rem)] grid grid-cols-1 lg:grid-cols-12 gap-0">
        {{-- Left Panel: Tables --}}
        <div class="lg:col-span-2 border-r border-gray-200 bg-white overflow-y-auto" id="panel-tables">
            <livewire:table-panel :project="$project" />
        </div>

        {{-- Center Panel: Column Editor --}}
        <div class="lg:col-span-4 bg-gray-50 overflow-y-auto" id="panel-columns">
            <livewire:column-editor :project="$project" />
        </div>

        {{-- Center-right Panel: Pivot Manager --}}
        <div class="lg:col-span-3 border-l border-gray-200 bg-white overflow-y-auto" id="panel-pivots">
            <livewire:pivot-manager :project="$project" />
        </div>

        {{-- Right Panel: Mermaid Preview --}}
        <div class="lg:col-span-3 border-l border-gray-200 bg-gray-900 overflow-y-auto" id="panel-mermaid">
            <livewire:mermaid-preview :project="$project" />
        </div>
    </div>
</div>
