<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $appkey = '';
    public string $profile_picture = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->appkey = $user->appkey;
        $this->tesla_access_token = $user->tesla_access_token;
    }

    /**
     * Generate a partner authentication token.
     */
    private function generatePartnerToken(): string
    {
        $user = Auth::user();
        $response = Http::post('https://fleet-auth.prd.vn.cloud.tesla.com/oauth2/v3/token', [
            'content_type' => 'application/x-www-form-urlencoded',
            'grant_type' => 'client_credentials',
            'client_id' => $user->tesla_client_id,
            'client_secret' => $user->tesla_client_secret,
            'scope' => 'openid vehicle_device_data vehicle_cmds vehicle_charging_cmds',      
            'audience' => 'https://fleet-api.prd.vn.cloud.tesla.com',
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        Log::error('Failed to generate partner token: ' . $response->body());
        throw new \Exception('Failed to generate partner token.');
    }

    /**
     * Call the Fleet API register endpoint.
     */
    public function registerWithFleetAPI(): void
    {
        try {
            $token = $this->generatePartnerToken();

            $response = Http::withToken($token)->post('https://fleet-api.prd.na.vn.cloud.tesla.com/api/1/partner_accounts', [
                'content_type' => 'application/json',
                'domain' => 'kindlbacher.de',
            ]);

            if (!$response->successful()) {
                Log::error('Fleet API registration failed: ' . $response->body());
                throw new \Exception('Failed to register with Fleet API.');
            }

            Log::info('Successfully registered with Fleet API: ' . $response->body());
            session()->flash('success', 'Successfully registered with Fleet API.');
        } catch (\Exception $e) {
            session()->flash('error', 'Fleet API registration failed. Please try again later.');
        }
    }

    /**
     * Render the component.
     */
    public function render(): mixed
    {
        return <<<'blade'
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
        blade;
    }
};