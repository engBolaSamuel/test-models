<div class="h-full flex flex-col bg-gray-900 text-gray-200">
    <div class="p-4 border-b border-gray-700 flex items-center justify-between shrink-0">
        <h3 class="font-semibold">ER Diagram</h3>
        <button wire:click="generateDiagram" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded transition-colors duration-150">
            View DB as ERD
        </button>
    </div>
    
    <div
        x-data="mermaidPreview"
        x-effect="renderDiagram()"
        class="p-4 overflow-auto flex-1 relative"
    >
        <div wire:ignore x-ref="mermaidContainer" id="mermaid-preview-container" class="w-full h-full flex items-center justify-center"></div>
        
        {{-- Empty state before button is clicked --}}
        <template x-if="!hasDiagram">
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <p class="text-gray-500 text-sm text-center">
                    Click "View DB as ERD" to generate the diagram
                </p>
            </div>
        </template>
    </div>
</div>
