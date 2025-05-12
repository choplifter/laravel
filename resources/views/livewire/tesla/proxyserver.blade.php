<?php

use Livewire\Volt\Component;

new class extends Component {
    public bool $isRunning = false;

    public function checkStatus(): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows: check for process in tasklist
            $result = shell_exec('tasklist /FI "IMAGENAME eq https_proxy_server.exe" 2>&1');
            $this->isRunning = str_contains($result, 'https_proxy_server.exe');
        } else {
            // Linux/macOS: check for process in ps
            $result = shell_exec('ps aux | grep https_proxy_server | grep -v grep 2>&1');
            $this->isRunning = !empty(trim($result));
        }
    }
}; ?>

<div wire:poll.10s="checkStatus" class="flex items-center space-x-3 rounded-xl border border-neutral-200 dark:border-neutral-700 p-4">
    <span class="inline-block w-4 h-4 rounded-full {{ $isRunning ? 'bg-green-500' : 'bg-red-500' }}"></span>
    <span class="font-medium">
        https_proxy_server
    </span>
    <span class="text-xs text-gray-500">
        Status: <span class="font-semibold">{{ $isRunning ? 'Running' : 'Not running' }}</span>
    </span>
</div>