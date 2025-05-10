


<section class="w-full">

        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">
                {{ __('Fleet API Registration') }}
            </h1>

            <div class="flex flex-col gap-6">

                <flux:input wire:model="appkey" :label="__('AppKey')" type="text" readonly />

                <button wire:click="registerWithFleetAPI" class="px-4 py-2 bg-blue-500 text-white rounded">
                    {{ __('Register with Fleet API') }}
                </button>

                @if (session('success'))
                    <div class="text-green-500">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="text-red-500">{{ session('error') }}</div>
                @endif
            </div>
        </div>
</section>
 