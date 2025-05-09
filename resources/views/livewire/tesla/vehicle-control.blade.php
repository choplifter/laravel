<div>
    @if($message)
        <div class="{{ $messageType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' }} px-4 py-3 rounded relative mb-4">
            {{ $message }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        @if(count($vehicles) > 0)
            @foreach($vehicles as $vehicle)
                <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
                    <h2 class="font-semibold">{{ $vehicle['vin'] }}</h2>
                    <p>Status: <span>{{ $vehicle['state'] }}</span></p>
                    <p>Battery Level: {{ $vehicle['battery_level'] ?? 'N/A' }}%</p>
                    
                    <div class="flex flex-wrap gap-2 mt-4">
                        <button wire:click="sendCommand('{{ $vehicle['vin'] }}', 'wake_up')" 
                                class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Wake Up
                        </button>
                        
                        <button wire:click="sendCommand('{{ $vehicle['vin'] }}', 'door_unlock')" 
                                class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                            Unlock
                        </button>
                        
                        <button wire:click="sendCommand('{{ $vehicle['vin'] }}', 'door_lock')" 
                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                            Lock
                        </button>
                        
                        <button wire:click="sendCommand('{{ $vehicle['vin'] }}', 'honk_horn')" 
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                            Honk
                        </button>
                        
                        <button wire:click="sendCommand('{{ $vehicle['vin'] }}', 'flash_lights')" 
                                class="px-3 py-1 bg-purple-500 text-white rounded hover:bg-purple-600">
                            Flash Lights
                        </button>
                    </div>
                </div>
            @endforeach
        @else
            <p>No vehicle data available.</p>
        @endif
    </div>
    
    <div class="mt-4">
        <button wire:click="fetchVehicles" 
                class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            Refresh Vehicles
        </button>
    </div>
</div>
