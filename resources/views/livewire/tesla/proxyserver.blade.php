<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Process;

new class extends Component {
    public string $status = 'unknown';
    public bool $isRunning = false;
    public bool $isLoading = false;

    // Process configuration
    protected string $processName = 'tesla_http_proxy';
    protected string $processCommand = '/usr/local/bin/tesla_http_proxy'; // Adjust path as needed
    protected string $configPath = '/etc/tesla_http_proxy.conf'; // Adjust config path as needed

    public function mount(): void
    {
        $this->checkStatus();
    }

    public function checkStatus(): void
    {
        $this->isLoading = true;
        
        // Check if process is running
        $result = Process::run("pgrep -f '{$this->processName}'");
        
        $this->isRunning = $result->successful();
        $this->status = $this->isRunning ? 'running' : 'stopped';
        $this->isLoading = false;
    }

    public function toggleProcess(): void
    {
        $this->isLoading = true;
        
        if ($this->isRunning) {
            $this->stopProcess();
        } else {
            $this->startProcess();
        }
    }

    protected function startProcess(): void
    {
        // Start as daemon with config file
        Process::run("{$this->processCommand} -c {$this->configPath} -d");
        
        // Wait a moment then check status
        sleep(2);
        $this->checkStatus();
    }

    protected function stopProcess(): void
    {
        // Graceful shutdown
        Process::run("pkill -f '{$this->processName}'");
        
        // Force kill if needed after 1 second
        sleep(1);
        if (Process::run("pgrep -f '{$this->processName}'")->successful()) {
            Process::run("pkill -9 -f '{$this->processName}'");
        }
        
        sleep(1);
        $this->checkStatus();
    }

    public function with(): array
    {
        return [
            'statusData' => match ($this->status) {
                'running' => [
                    'color' => 'bg-emerald-500',
                    'text' => 'Aktiv',
                    'icon' => '✓',
                ],
                'stopped' => [
                    'color' => 'bg-red-500',
                    'text' => 'Inaktiv',
                    'icon' => '✗',
                ],
                default => [
                    'color' => 'bg-gray-500',
                    'text' => 'Unbekannt',
                    'icon' => '?',
                ],
            },
        ];
    }
}; ?>

<div class="max-w-md mx-auto p-6 bg-white rounded-xl shadow-md">
    <div class="flex items-center justify-between">
        <!-- Status-Anzeige -->
        <div class="flex items-center space-x-4">
            <div class="relative">
                <!-- Ampel-Kreis -->
                <div class="w-12 h-12 rounded-full {{ $statusData['color'] }} flex items-center justify-center text-white text-xl font-bold shadow-lg">
                    {{ $statusData['icon'] }}
                </div>
                <!-- Pulsierende Animation wenn aktiv -->
                @if($status === 'running')
                <div class="absolute inset-0 rounded-full {{ $statusData['color'] }} opacity-75 animate-ping"></div>
                @endif
            </div>
            
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Tesla HTTP Proxy</h3>
                <p class="text-gray-600">{{ $statusData['text'] }}</p>
            </div>
        </div>
        
        <!-- Steuerungs-Button -->
        <button 
            wire:click="toggleProcess" 
            @class([
                'px-4 py-2 rounded-lg font-medium transition-all',
                'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' => !$isRunning,
                'bg-red-100 text-red-800 hover:bg-red-200' => $isRunning,
                'opacity-75 cursor-not-allowed' => $isLoading,
            ])
            wire:loading.attr="disabled"
        >
            <span wire:loading.remove>
                {{ $isRunning ? 'Stoppen' : 'Starten' }}
            </span>
            <span wire:loading>
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-800 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ $isRunning ? 'Stoppt...' : 'Startet...' }}
            </span>
        </button>
    </div>
    
    <!-- Zusatzinformationen -->
    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
        <button 
            wire:click="checkStatus" 
            class="text-sm text-blue-600 hover:text-blue-800 hover:underline flex items-center"
            wire:loading.attr="disabled"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Status aktualisieren
        </button>
        
        <div class="text-xs text-gray-500">
            <span wire:poll.10s="checkStatus" class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Auto-Update aktiv
            </span>
        </div>
    </div>
</div>