<div class="container mx-auto px-4 py-8">
    <!-- Container with borders -->

    <!-- Heading -->
    <h1 class="text-2xl font-bold mb-6 text-center text-gray-800" session('user')</h1>

        <div class="grid grid-cols-2  gap-4 rounded-xl">

            <div class="md:col-span-1 col-span-2  relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 ">
                
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />


                <div
                    class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">

                    <div class="container mx-auto px-4 py-8  ">


                        <x-placeholder-pattern
                            class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                        <div class="flex flex-col gap-6  ">
                            <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

                            <!-- Session Status -->
                            <x-auth-session-status class="text-center" :status="session('status')" />

                            <form wire:submit="login" class="flex flex-col gap-6">
                                <!-- Email Address -->
                                <flux:input wire:model="email" :label="__('Email address')" type="email" required
                                    autofocus autocomplete="email" placeholder="email@example.com" />

                                <!-- Password -->
                                <div class="relative">
                                    <flux:input wire:model="password" :label="__('Password')" type="password" required
                                        autocomplete="current-password" :placeholder="__('Password')" />

                                    @if (Route::has('password.request'))
                                        <flux:link class="absolute end-0 top-0 text-sm"
                                            :href="route('password.request')" wire:navigate>
                                            {{ __('Forgot your password?') }}
                                        </flux:link>
                                    @endif
                                </div>

                                <!-- Remember Me -->
                                <flux:checkbox wire:model="remember" :label="__('Remember me')" />

                                <div class="flex items-center justify-end">
                                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}
                                    </flux:button>
                                </div>
                            </form>

                            @if (Route::has('register'))
                                <div
                                    class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ __('Don\'t have an account?') }}
                                    <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
  

                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                </div>
            
    </div>
</div>