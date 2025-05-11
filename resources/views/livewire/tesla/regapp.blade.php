    @if($message)
        <div class="{{ $messageType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' }} px-4 py-3 rounded relative mb-4">
            {{ $message }}
        </div>
    @endif

<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">
        {{ __('Fleet API Registration') }}
    </h1>

    <div class="flex flex-col gap-6">

        <flux:input wire:model="appkey" :label="__('AppKey')" type="text" readonly />

        <flux:button variant="danger" type="submit" class="w-full" wire:click="registerWithFleetAPI"
            >
            {{ __('Register with Fleet API') }}
        </flux:button>

        @if (session('success'))
            <div class="text-green-500">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="text-red-500">{{ session('error') }}</div>
        @endif
    </div>
</div>
