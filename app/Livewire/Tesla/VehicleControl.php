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
    
    //protected $baseUrl = 'https://fleet-api.prd.eu.vn.cloud.tesla.com/api/1';
    protected $baseUrl = 'https://localhost:4443/api/1';

    protected $proxyUrl = "localhost:4443";
    
    public function mount()
    {
        $this->checkAndRefreshToken();
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
        foreach ($this->vehicles as &$vehicle) {
            $vehicleTag = $vehicle['vin'] ?? null;
            if ($vehicleTag) {
                $vehicleDataResponse = Http::withToken($token)
                    ->get("{$this->baseUrl}/vehicles/{$vehicleTag}/vehicle_data");
                
                if ($vehicleDataResponse->successful()) {
                    $vehicle['data'] = $vehicleDataResponse->json('response', []);
                } else {
                    $vehicle['data'] = ['error' => $vehicleDataResponse->json('error', 'Unknown error')];
                }
            }
        }
    }
    
    public function sendCommand($vehicleId, $command)
    {
        $token = Auth::user()->tesla_access_token;
        $response = Http::withToken($token)
                      ->post("{$this->baseUrl}/vehicles/{$vehicleId}/{$command}");
                      
        if ($response->successful()) {
            $this->showSuccess("Command '{$command}' sent successfully!");
            // Refresh vehicle data after command
            $this->fetchVehicles();
        } else {
            $this->showError("Error sending command: " . $response->json('reason', 'Unknown error'));
        }
    }
    private function checkAndRefreshToken()
    {
        $token = Auth::user()->tesla_access_token;
        $response = Http::withToken($token)->get("{$this->baseUrl}/vehicles");

        if ($response->status() === 401) { // Token is invalid or expired
            $refreshToken = Auth::user()->tesla_refresh_token;
            $clientId = Auth::user()->tesla_client_id;
            $refreshResponse = Http::post("https://fleet-auth.prd.vn.cloud.tesla.com/oauth2/v3/token", [
                'grant_type' => 'refresh_token',
                'client_id' => $clientId,
                'refresh_token' => $refreshToken,
            ]);

            if ($refreshResponse->successful()) {
                $newToken = $refreshResponse->json('access_token');
                $newRefreshToken = $refreshResponse->json('refresh_token');

                // Update the user's tokens
                $user = Auth::user();
                $user->tesla_access_token = $newToken;
                $user->tesla_refresh_token = $newRefreshToken;
                $user->tesla_token_expires_at = now()->addSeconds($refreshResponse->json('expires_in'));
                $user->save();
            } else {
                $this->showError("Failed to refresh token: " . $refreshResponse->json('error', 'Unknown error'));
            }
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
