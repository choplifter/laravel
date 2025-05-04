<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Volt::route('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


    Route::get('forward', function (\Illuminate\Http\Request $request) {

        Debugbar::info($request->all());

        $code = $request->query('code');
        if ($code) {
            # Authorization code token request

            // Replace these with your actual values
            $clientId = env('TESLA_CLIENT_ID');
            $clientSecret = env('TESLA_CLIENT_SECRET');
            $audience = env('TESLA_AUDIENCE');
            $redirectUri = env('TESLA_CALLBACK');
            $tokenUrl = 'https://fleet-auth.prd.vn.cloud.tesla.com/oauth2/v3/token';

            $response = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'audience' => $audience,
                'redirect_uri' => $redirectUri,
            ]);

            if ($response->successful()) {
                $accessToken = $response->json('access_token');
                $refreshToken = $response->json('refresh_token');
                // Use $accessToken and $refreshToken as needed
            
                $userResponse = Http::withToken($accessToken)
                    ->get('https://fleet-api.prd.eu.vn.cloud.tesla.com/api/1/users/me');
            
                if ($userResponse->successful()) {
                    $userData = $userResponse->json();
                    // Process user data as needed
                    // For example, you can create or update a user in your database
                    $user = \App\Models\User::updateOrCreate(
                        ['email' => $userData['email']],
                        [
                            'name' => $userData['name'],
                            'appkey' => $accessToken,
                            'password' => bcrypt($refreshToken),
                        ]
                    );
            
                    // Log the user in
                    \Illuminate\Support\Facades\Auth::login($user);
            
                    return redirect()->route('dashboard');
                } else {
                    // Handle error in user data retrieval
                }
            } else {
                // Handle error in token request
                $error = $response->json('error');
                $errorDescription = $response->json('error_description');
                // Log or display the error as needed
            }
        } else {
            // Handle the case where the code is not present in the request
            // You can redirect to an error page or show a message
            // For example, redirect to the home page
            // return redirect()->route('home');
            // Or you can handle the login code here
            // Example: Check if the user is already logged in
            // if (auth()->check()) {
            //     return redirect()->route('dashboard');
            // }
            // If the user is not logged in, you can redirect them to the login page
            // return redirect()->route('login');
            // Or you can handle the login code here
            // Example: Check if the user is already logged in                                  



            // Example: Find user by code and log them in
            //$user = \App\Models\User::where('login_code', $code)->first();
            //if ($user) {
            //    \Illuminate\Support\Facades\Auth::login($user);
            //    return redirect()->route('dashboard');
            //}
        }

        return redirect()->route('home');
    });

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
