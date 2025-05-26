<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium">Create {{ $entity->name }}</h2>
        </div>

        <div class="bg-white rounded-lg shadow">
            <form wire:submit="create">
                {{ $this->form }}

                <div class="p-4 flex justify-end">
                    <x-filament::button type="submit">
                        Create
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page> 