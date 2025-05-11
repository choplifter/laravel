<?php

use Livewire\Volt\Component;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class RegApp extends Component {
    
    public string $appkey = '';
    public string $profile_picture = '';
    public string $tesla_client_id = '';
    public string $tesla_client_secret = '';
    public string $tesla_access_token = '';
    public $message = '';
    public $messageType = 'success';
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->appkey = $user->appkey ?? '';
        $this->tesla_access_token = $user->tesla_access_token ?? '';
        $this->tesla_client_id = $user->tesla_client_id ?? '';
        $this->tesla_client_secret = $user->tesla_client_secret ?? '';
    }

    /**
     * Generate a partner authentication token.
     */
    private function generatePartnerToken(): string
    {
        $response = Http::post('https://fleet-auth.prd.vn.cloud.tesla.com/oauth2/v3/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->tesla_client_id,
            'client_secret' => $this->tesla_client_secret,
            'scope' => 'openid vehicle_device_data vehicle_cmds vehicle_charging_cmds',
            'audience' => 'https://fleet-api.prd.eu.vn.cloud.tesla.com',
        ]);

        if ($response->successful()) {
            
            $this->showSuccess("Partner token received successfully!");
            return $response->json()['access_token'];
        }

        Log::error('Failed to generate partner token: ' . $response->body());
        throw new \Exception('Failed to generate partner token: ' . $response->body());
    }

    /**
     * Call the Fleet API register endpoint.
     */
    public function registerWithFleetAPI(): void
    {
        
        $this->showSuccess('Requesting Fleet API registration...'); $this->render();

        try {
            $token = $this->generatePartnerToken(); 

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ])->post('https://fleet-api.prd.na.vn.cloud.tesla.com/api/1/partner_accounts', [
                'domain' => request()->getHost(), // Dynamische Domain basierend auf der aktuellen URL
            ]);

            if (!$response->successful()) {
                Log::error('Fleet API registration failed: ' . $response->body());
                throw new \Exception('Failed to register with Fleet API: ' . $response->body());
            }

            Log::info('Successfully registered with Fleet API: ' . $response->body());
            session()->flash('success', 'Successfully registered with Fleet API.');
            $this->showSuccess("Successfully registered with Fleet API.");
        } catch (\Exception $e) {
            $this->showError("Error registring API: " . $e->getMessage());
            Log::error('Fleet API registration error: ' . $e->getMessage());
            session()->flash('error', 'Fleet API registration failed: ' . $e->getMessage());
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
   
}; ?>

<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">
            {{ __('Fleet API Registration') }}
        </h1>

        <div class="flex flex-col gap-6">

            <flux:input wire:model="appkey" :label="__('AppKey')" type="text" readonly />

            <flux:button variant="danger" type="submit" class="w-full" wire:click="registerWithFleetAPI">
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
    @if ($message)
        <div
            class="{{ $messageType === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' }} px-4 py-3 rounded relative mb-4">
            {{ $message }}
        </div>
    @endif
</div>

