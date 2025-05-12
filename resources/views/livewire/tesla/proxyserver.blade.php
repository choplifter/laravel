<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    public bool $isRunning = false;
    public string $lastChecked = '';
    
    /**
     * Mount the component and check the status initially
     */
    public function mount(): void
    {
        $this->checkProxyStatus();
    }
    
    /**
     * Poll for updates every 30 seconds
     */
    public function getListeners(): array
    {
        return [
            '$refresh',
            'pollStatus' => '$refresh'
        ];
    }
    
    /**
     * Check if tesla_http_proxy process is running
     */
    public function checkProxyStatus(): void
    {
        // Get the appropriate command for the current OS
        if (PHP_OS_FAMILY === 'Windows') {
            $result = shell_exec('tasklist /FI "IMAGENAME eq tesla_http_proxy*" 2>&1');
            $this->isRunning = (str_contains($result, 'tesla_http_proxy') && !str_contains($result, 'INFO:'));
        } else {
            // For Linux/Mac
            $result = shell_exec('ps -ef | grep "tesla_http_proxy" | grep -v grep 2>&1');
            $this->isRunning = !empty($result);
        }
        
        $this->lastChecked = now()->format('H:i:s');
        
        // Schedule next check in 30 seconds
        $this->dispatch('pollStatus')
             ->later(30);
    }
    
    /**
     * Start the proxy process
     */
    public function startProxy(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            // Windows command to start process
            shell_exec('start /B tesla_http_proxy.exe > NUL 2>&1');
        } else {
            // Linux/Mac command to start process
            shell_exec('nohup tesla_http_proxy > /dev/null 2>&1 &');
        }
        
        // Check status after a brief delay
        sleep(1);
        $this->checkProxyStatus();
    }
}; ?>

<div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                <!-- Traffic light indicator -->
                <div class="h-4 w-4 rounded-full {{ $isRunning ? 'bg-green-500' : 'bg-red-500' }}"></div>
            </div>
            
            <div>
                <h3 class="text-sm font-medium">Tesla HTTP Proxy</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Status: {{ $isRunning ? 'Running' : 'Stopped' }}
                </p>
                <p class="text-xs text-gray-400 dark:text-gray-500">
                    Last checked: {{ $lastChecked }}
                </p>
            </div>
        </div>
        
        <div class="flex space-x-2">
            @if (!$isRunning)
                <flux:button 
                    wire:click="startProxy" 
                    wire:loading.attr="disabled"
                    size="xs"
                    variant="primary">
                    Start Proxy
                </flux:button>
            @endif
            
            <flux:button 
                wire:click="checkProxyStatus" 
                wire:loading.attr="disabled"
                size="xs"
                variant="secondary">
                Refresh
            </flux:button>
        </div>
    </div>
</div>