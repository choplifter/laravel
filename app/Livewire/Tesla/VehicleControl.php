<?php

namespace App\Livewire\Tesla;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class VehicleControl extends Component
{
    public $vehicles = [];
    public $message = '';
    public $messageType = 'success';
    
    protected $baseUrl = 'https://fleet-api.prd.eu.vn.cloud.tesla.com/api/1';
    
    public function mount()
    {
        $this->fetchVehicles();
    }
    
    public function fetchVehicles()
    {
        $token = Auth::user()->tesla_access_token;
        $response = Http::withToken($token)->get("{$this->baseUrl}/vehicles");
        
        if ($response->successful()) {
            $this->vehicles = $response->json('response', []);
        } else {
            $this->showError("Failed to fetch vehicles: " . $response->json('error', 'Unknown error'));
        }
    }
    
    public function sendCommand($vehicleId, $command)
    {
        $token = Auth::user()->tesla_access_token;
        $response = Http::withToken($token)
                      ->post("{$this->baseUrl}/vehicles/{$vehicleId}/command/{$command}");
                      
        if ($response->successful()) {
            $this->showSuccess("Command '{$command}' sent successfully!");
            // Refresh vehicle data after command
            $this->fetchVehicles();
        } else {
            $this->showError("Error sending command: " . $response->json('reason', 'Unknown error'));
        }
    }
    
    private function showSuccess($message)
    {
        $this->message = $message;
        $this->messageType = 'success';
    }
    
    private function showError($message)
    {
        $this->message = $message;
        $this->messageType = 'error';
    }
    
    public function render()
    {
        return view('livewire.tesla.vehicle-control');
    }
}
