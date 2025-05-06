<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->appkey = Auth::user()->appkey;
        $this->profile_picture = Auth::user()->profile_picture;
    }
    
};

    ?>
    
    <div class="container mx-auto px-4 py-8">
    <!-- Container with borders -->

    <!-- Heading -->
    <h1 class="text-2xl font-bold mb-6 text-center text-gray-800" >

        <div class="grid grid-cols-2  gap-4 rounded-xl">


            <div class="container mx-auto  py-8  col-span-2">
                <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 ">


                    <x-auth-header :title="__('Welcome to chargerproxy.com!')" :description="__('Follow the steps')" />



                </div>
            </div>

            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <div class="p-4 my-6 w-full space-y-6"">
 



 
                    
                                    <div>
                                        <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
                                    </div>
                                    
                                    <div>
                                        <flux:input wire:model="appkey" :label="__('AppKey')" type="email" required  readonly />
                                    </div>
                    
                                    <div>
                                        <flux:input wire:model="profile_picture" :label="__('Picture')" type="profile_picture" required autocomplete="profile_picture" readonly />
                                    </div>
                    




                    

                </div>
            </div>

            @for ($i = 0; $i < 4; $i++)
                <div
                    class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                    <x-placeholder-pattern
                        class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                </div>
            @endfor

        </div>

</div>
