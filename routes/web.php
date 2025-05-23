<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::get('forward', function (\Illuminate\Http\Request $request) {

        $code = $request->query('code');
        if ($code) {
            # Authorization code token request
            info('Code received: ' . $code);
            // Replace these with your actual values
            $clientId = env('TESLA_CLIENT_ID');
            $clientSecret = env('TESLA_CLIENT_SECRET');
            $audience = env('TESLA_AUDIENCE');
            $scope = env('TESLA_SCOPE');
            $redirectUri = env('TESLA_CALLBACK');
            $tokenUrl ='https://fleet-auth.prd.vn.cloud.tesla.com/oauth2/v3/token';

            $data = [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'scope' => $scope,
                'audience' => $audience,
                'redirect_uri' => $redirectUri,
                
            ];
            info('Posting to Auth: ' . json_encode($data));
            $response = Http::asForm()->post($tokenUrl, $data);
            info('Auth Response: ' . $response->body());

            if ($response->successful()) {
                $accessToken = $response->json('access_token');
                $refreshToken = $response->json('refresh_token');
                $expiresIn = $response->json('expires_in');
                // Use $accessToken and $refreshToken as needed
            
                $userResponse = Http::withToken($accessToken)
                    ->get('https://fleet-api.prd.eu.vn.cloud.tesla.com/api/1/users/me');
                
                info('User Response: ' . $userResponse->body());

                if ($userResponse->successful()) {
                    $userData = $userResponse;
                    // Process user data as needed
                    // For example, you can create or update a user in your database
                    
                       $user = \App\Models\User::updateOrCreate(
                       
                        
                            ['email' => $userData['response']['email']],     
                            ['name' => $userData['response']['full_name'],
                                     'profile_picture' => $userData['response']['profile_image_url'],
                                     'appkey' => emailToHashedLetters($userData['response']['email']),
                                     'password' => Hash::make(env('TESLA_PWD')),
                                     'tesla_refresh_token' => $refreshToken,
                                     'tesla_access_token' => $accessToken,
                                     'tesla_token_expires_at' => now()->addSeconds($expiresIn),
                                        
                            ],
                            //'appkey' => $accessToken,
                            //'password' => bcrypt($refreshToken),
                        
                    );
                    
                  

                    // Log the user in
                    Auth::login($user);
            
                    return redirect()->route('dashboard');
                } else {
                    // Handle error in user data retrieval
                    return redirect()->route('home')->with('error', 'Failed to retrieve user data from Tesla. Please try again.');
                }
            } else {
                // Handle error in token request
                $error = $response->json('error');
                $errorDescription = $response->json('error_description');

                return redirect()->route('home')->with('error', "Token request failed: $error - $errorDescription");

                // Log or display the error as needed
            }
        } else {
            return redirect()->route('home')->with('error', 'Authorization code not found. Please try again.');
        }

        
    });

    function emailToHashedLetters($email) {
        $hash = md5(strtolower($email)); // Generate hash
        $letters = preg_replace('/[^a-z]/', '', $hash); // Extract letters
        return 'app-'.substr(str_pad($letters, 10, 'a'), 0, 10); // Ensure 10 chars
    }
  

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
