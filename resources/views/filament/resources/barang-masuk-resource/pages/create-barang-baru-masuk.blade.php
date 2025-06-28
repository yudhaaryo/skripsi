<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}
        <div class="mt-4 mb-8 py-8" >
            <x-filament::button type="submit">
                Simpan Barang Baru & Masuk
            </x-filament::button>
        </div>
    </form>
</x-filament::page>
