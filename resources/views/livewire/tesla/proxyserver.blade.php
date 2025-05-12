<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Process;

new class extends Component {
    public string $status = 'unknown';
    public string $buttonText = 'Status prüfen';
    public bool $isRunning = false;

    // Prozess-Name oder Befehl zur Überprüfung
    protected string $processName = 'tesla_http_proxy';

    public function mount(): void
    {
        $this->checkStatus();
    }

    public function checkStatus(): void
    {
        $this->buttonText = 'Prüfe...';
        
        // Befehl zur Überprüfung ob der Prozess läuft (Linux/Unix)
        $result = Process::run("pgrep -f {$this->processName}");
        
        $this->isRunning = $result->successful();
        $this->status = $this->isRunning ? 'running' : 'stopped';
        $this->buttonText = $this->isRunning ? 'Prozess stoppen' : 'Prozess starten';
    }

    public function toggleProcess(): void
    {
        if ($this->isRunning) {
            $this->stopProcess();
        } else {
            $this->startProcess();
        }
    }

    protected function startProcess(): void
    {
        // Hier den Befehl zum Starten des Prozesses einfügen
        // Beispiel: Process::run("tesla_http_proxy --daemonize");
        Process::run("echo 'Starting {$this->processName}'");
        
        // Kurze Pause bevor der Status erneut geprüft wird
        sleep(2);
        $this->checkStatus();
    }

    protected function stopProcess(): void
    {
        // Hier den Befehl zum Stoppen des Prozesses einfügen
        // Beispiel: Process::run("pkill -f {$this->processName}");
        Process::run("echo 'Stopping {$this->processName}'");
        
        // Kurze Pause bevor der Status erneut geprüft wird
        sleep(2);
        $this->checkStatus();
    }

    // Automatische Aktualisierung alle 10 Sekunden
    public function with(): array
    {
        return [
            'statusColor' => match ($this->status) {
                'running' => 'bg-green-500',
                'stopped' => 'bg-red-500',
                default => 'bg-gray-500',
            },
            'statusText' => match ($this->status) {
                'running' => 'Läuft',
                'stopped' => 'Gestoppt',
                default => 'Unbekannt',
            },
        ];
    }
}; ?>

<div class="space-y-4">
    <div class="flex items-center gap-4">
        <!-- Ampel-Anzeige -->
        <div class="flex flex-col items-center">
            <div class="w-8 h-8 rounded-full {{ $statusColor }} shadow-lg"></div>
            <span class="text-sm mt-1">{{ $statusText }}</span>
        </div>
        
        <!-- Status- und Steuerungsbuttons -->
        <div class="space-x-2">
            <button wire:click="checkStatus" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                {{ $buttonText }}
            </button>
            <button wire:click="toggleProcess" class="px-4 py-2 {{ $isRunning ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded">
                {{ $isRunning ? 'Stoppen' : 'Starten' }}
            </button>
        </div>
    </div>
    
    <!-- Automatische Aktualisierung -->
    <div wire:poll.10s="checkStatus" class="text-xs text-gray-500">
        Automatische Aktualisierung alle 10 Sekunden
    </div>
</div>