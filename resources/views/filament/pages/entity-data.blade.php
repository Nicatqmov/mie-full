<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold tracking-tight">
                {{ $entity->name }}
            </h2>
        </div>

        <div class="bg-white rounded-xl shadow">
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page> 