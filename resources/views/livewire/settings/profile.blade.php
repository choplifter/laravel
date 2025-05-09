<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $appkey = '';
    public string $profile_picture = '';
    public string|NULL $tesla_client_id = '';
    public string|NULL $tesla_client_secret = '';
    public string|NULL $tesla_access_token = '';
    public string|NULL $tesla_token_expires_at = '';
    public string|NULL $tesla_refresh_token = '';


    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->appkey = Auth::user()->appkey;
        $this->profile_picture = Auth::user()->profile_picture;
        $this->tesla_client_id = Auth::user()->tesla_client_id;
        $this->tesla_client_secret = Auth::user()->tesla_client_secret;
        $this->tesla_access_token = Auth::user()->tesla_access_token;       
        $this->tesla_token_expires_at = Auth::user()->tesla_token_expires_at;
        $this->tesla_refresh_token = Auth::user()->tesla_refresh_token; 

    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
 
            'profile_picture' => ['required', 'string', 'max:2550'],
            'tesla_client_id' => ['required', 'string', 'max:2550'],     
            'tesla_client_secret' => ['required', 'string', 'max:2550'],  
            'tesla_access_token' => ['required', 'string', 'max:2550'],  
            'tesla_token_expires_at' => ['required', 'string', 'max:2550'],  
            'tesla_refresh_token' => ['required', 'string', 'max:2550'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your profile')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

                <div>
                    <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
                </div>
                <div>
                    <flux:input wire:model="appkey" :label="__('AppKey')" type="email" required  readonly />
                </div>
                <div>
                    <flux:input wire:model="tesla_client_id" :label="__('Client_id')" type="tesla_client_id" required   />
                </div>
                <div>
                    <flux:input wire:model="tesla_client_secret" :label="__('Client_secret')" type="tesla_client_secret" required   />
                </div>
                <div>
                    <flux:input wire:model="tesla_access_token" :label="__('Access_token')" type="tesla_access_token" required  readonly />
                </div>
                <div>
                    <flux:input wire:model="tesla_token_expires_at" :label="__('Token Expires')" type="tesla_access_token" required  readonly />
                </div>                <div>
                    <flux:input wire:model="tesla_refresh_token" :label="__('Refresh_token')" type="tesla_refresh_token" required  readonly />
                </div>
                <div>
                    <flux:input wire:model="profile_picture" :label="__('Picture')" type="profile_picture" required autocomplete="profile_picture" readonly />
                </div>

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
