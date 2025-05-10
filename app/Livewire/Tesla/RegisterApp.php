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
    public string $tesla_client_id = '';
    public string $tesla_client_secret = '';
    public string $tesla_access_token = '';

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
        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post('https://fleet-auth.prd.vn.cloud.tesla.com/oauth2/v3/token', [
            'grant_type' => 'client_credentials',
            'client_id' => $this->tesla_client_id,
            'client_secret' => $this->tesla_client_secret,
            'scope' => 'openid vehicle_device_data vehicle_cmds vehicle_charging_cmds',
            'audience' => 'https://fleet-api.prd.vn.cloud.tesla.com',
        ]);

        if ($response->successful()) {
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
        } catch (\Exception $e) {
            Log::error('Fleet API registration error: ' . $e->getMessage());
            session()->flash('error', 'Fleet API registration failed: ' . $e->getMessage());
        }
    }
};
?>
