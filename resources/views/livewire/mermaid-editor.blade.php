<div class="h-full flex flex-col bg-gray-900 border-l border-gray-800 text-gray-300 font-mono text-sm">
    <div class="flex items-center justify-between p-4 border-b border-gray-800 bg-gray-950">
        <div class="flex items-center space-x-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <h2 class="text-sm font-semibold text-gray-100">Mermaid DSL Editor</h2>
            @if($isDirty)
                <span class="text-xs bg-yellow-500/20 text-yellow-300 px-2 py-0.5 rounded-full">Unsaved Changes</span>
            @endif
        </div>
        <button 
            wire:click="apply"
            class="px-3 py-1.5 text-xs font-medium rounded-md bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm transition-colors flex items-center space-x-2"
        >
            <svg wire:loading.class="animate-spin" wire:target="apply" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg>
            <span>Apply to DB</span>
        </button>
    </div>

    @if($errorMessage)
        <div class="p-3 bg-red-900/30 border-b border-red-800/50">
            <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 mt-0.5 mr-2 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <div class="text-red-300 text-xs whitespace-pre-wrap font-sans">{{ $errorMessage }}</div>
            </div>
        </div>
    @endif

    <div class="flex-1 relative">
        <textarea 
            wire:model.live.debounce.500ms="dsl"
            class="absolute inset-0 w-full h-full p-4 bg-transparent text-gray-300 border-none resize-none focus:ring-0 leading-relaxed"
            placeholder="Write your Mermaid ER Diagram DSL here..."
            spellcheck="false"
        ></textarea>
    </div>
</div>
