<x-slot name="header">
    <div class="flex items-center justify-between" x-data>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Projects') }}
        </h2>

        <x-primary-button x-on:click="$dispatch('open-modal', 'create-project')" id="open-create-project-modal">
            {{ __('+ New Project') }}
        </x-primary-button>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div class="mb-6 rounded-md bg-green-50 p-4" id="flash-message">
                <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
            </div>
        @endif

        {{-- Project Grid --}}
        @if ($projects->isEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" id="empty-state">
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ __('No projects yet') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Get started by creating your first database schema project.') }}</p>
                    <div class="mt-6">
                        <x-primary-button x-on:click="$dispatch('open-modal', 'create-project')" id="empty-state-create-button">
                            {{ __('+ New Project') }}
                        </x-primary-button>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="projects-grid">
                @foreach ($projects as $project)
                    <div
                        wire:key="project-{{ $project->id }}"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                                        <a href="{{ route('projects.show', $project) }}" class="hover:text-indigo-600 transition-colors duration-150">
                                            {{ $project->name }}
                                        </a>
                                    </h3>
                                    @if ($project->description)
                                        <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                                            {{ $project->description }}
                                        </p>
                                    @endif
                                </div>

                                <button
                                    wire:click="deleteProject({{ $project->id }})"
                                    wire:confirm="Are you sure you want to delete this project?"
                                    class="ml-2 shrink-0 text-gray-400 hover:text-red-500 transition-colors duration-150"
                                    title="{{ __('Delete project') }}"
                                    id="delete-project-{{ $project->id }}"
                                >
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.125c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 0v1.5c0 .621-.504 1.125-1.125 1.125M3.375 15.75h7.5" />
                                    </svg>
                                    {{ $project->tables_count }} {{ Str::plural('table', $project->tables_count) }}
                                </span>

                                <span class="text-xs text-gray-400">
                                    {{ $project->updated_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Create Project Modal --}}
    <x-modal name="create-project" maxWidth="md">
        <form wire:submit="createProject" class="p-6" id="create-project-form">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create New Project') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Define a name and optional description for your new database schema project.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="project-name" :value="__('Project Name')" />
                <x-text-input
                    wire:model="name"
                    id="project-name"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g. E-Commerce Schema"
                    autofocus
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="project-description" :value="__('Description')" />
                <textarea
                    wire:model="description"
                    id="project-description"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    rows="3"
                    placeholder="Optional — describe the purpose of this schema"
                ></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close-modal', 'create-project')" id="cancel-create-project">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button id="submit-create-project">
                    {{ __('Create Project') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>
</div>
