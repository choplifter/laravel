<div class="container mx-auto px-4 py-8">
    <!-- Container with borders -->

    <!-- Heading -->
    <h1 class="text-2xl font-bold mb-6 text-center text-gray-800" session('user')</h1>

        <div class="grid grid-cols-2  gap-4 rounded-xl">


            <div class="container mx-auto px-4 py-8  col-span-2">
                <div class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 ">

                    <x-placeholder-pattern
                        class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />

                    <x-auth-header :title="__('Welcome to chargerproxy.com!')" :description="__('Follow the steps')" />



                </div>
            </div>

            <div
                class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                <div class="p-4">
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ 'Code' }}</label>
                    <flux:input id="code" name="code" value="{{ request('code') }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300" />
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
