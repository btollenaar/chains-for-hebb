<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Phone -->
        <div>
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full"
                          :value="old('phone', $user->phone)"
                          placeholder="(555) 555-5555"
                          autocomplete="tel" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <!-- Billing Address -->
        <div class="pt-4 border-t border-gray-200">
            <h3 class="text-md font-medium text-gray-900 mb-4">Billing Address</h3>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <x-input-label for="billing_street" :value="__('Street Address')" />
                    <x-text-input id="billing_street" name="billing_street" type="text"
                                  class="mt-1 block w-full"
                                  :value="old('billing_street', $user->billing_street)"
                                  autocomplete="billing street-address" />
                    <x-input-error class="mt-2" :messages="$errors->get('billing_street')" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="billing_city" :value="__('City')" />
                        <x-text-input id="billing_city" name="billing_city" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('billing_city', $user->billing_city)"
                                      autocomplete="billing address-level2" />
                        <x-input-error class="mt-2" :messages="$errors->get('billing_city')" />
                    </div>

                    <div>
                        <x-input-label for="billing_state" :value="__('State')" />
                        <x-text-input id="billing_state" name="billing_state" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('billing_state', $user->billing_state)"
                                      autocomplete="billing address-level1" />
                        <x-input-error class="mt-2" :messages="$errors->get('billing_state')" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="billing_zip" :value="__('ZIP Code')" />
                        <x-text-input id="billing_zip" name="billing_zip" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('billing_zip', $user->billing_zip)"
                                      autocomplete="billing postal-code" />
                        <x-input-error class="mt-2" :messages="$errors->get('billing_zip')" />
                    </div>

                    <div>
                        <x-input-label for="billing_country" :value="__('Country')" />
                        <x-text-input id="billing_country" name="billing_country" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('billing_country', $user->billing_country ?? 'United States')"
                                      autocomplete="billing country" />
                        <x-input-error class="mt-2" :messages="$errors->get('billing_country')" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="pt-4 border-t border-gray-200" x-data="{
            sameAsBilling: {{ old('same_as_billing', $user->shipping_street === $user->billing_street ? 'true' : 'false') }}
        }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-md font-medium text-gray-900">Shipping Address</h3>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" x-model="sameAsBilling" class="rounded border-gray-300 text-abs-primary">
                    <span class="ml-2 text-sm text-gray-600">Same as billing</span>
                </label>
            </div>

            <div x-show="!sameAsBilling" class="grid grid-cols-1 gap-4">
                <div>
                    <x-input-label for="shipping_street" :value="__('Street Address')" />
                    <x-text-input id="shipping_street" name="shipping_street" type="text"
                                  class="mt-1 block w-full"
                                  :value="old('shipping_street', $user->shipping_street)"
                                  autocomplete="shipping street-address" />
                    <x-input-error class="mt-2" :messages="$errors->get('shipping_street')" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="shipping_city" :value="__('City')" />
                        <x-text-input id="shipping_city" name="shipping_city" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('shipping_city', $user->shipping_city)"
                                      autocomplete="shipping address-level2" />
                        <x-input-error class="mt-2" :messages="$errors->get('shipping_city')" />
                    </div>

                    <div>
                        <x-input-label for="shipping_state" :value="__('State')" />
                        <x-text-input id="shipping_state" name="shipping_state" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('shipping_state', $user->shipping_state)"
                                      autocomplete="shipping address-level1" />
                        <x-input-error class="mt-2" :messages="$errors->get('shipping_state')" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="shipping_zip" :value="__('ZIP Code')" />
                        <x-text-input id="shipping_zip" name="shipping_zip" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('shipping_zip', $user->shipping_zip)"
                                      autocomplete="shipping postal-code" />
                        <x-input-error class="mt-2" :messages="$errors->get('shipping_zip')" />
                    </div>

                    <div>
                        <x-input-label for="shipping_country" :value="__('Country')" />
                        <x-text-input id="shipping_country" name="shipping_country" type="text"
                                      class="mt-1 block w-full"
                                      :value="old('shipping_country', $user->shipping_country ?? 'United States')"
                                      autocomplete="shipping country" />
                        <x-input-error class="mt-2" :messages="$errors->get('shipping_country')" />
                    </div>
                </div>
            </div>

            <!-- Hidden inputs when same as billing -->
            <template x-if="sameAsBilling">
                <div>
                    <input type="hidden" name="shipping_street" :value="document.getElementById('billing_street').value">
                    <input type="hidden" name="shipping_city" :value="document.getElementById('billing_city').value">
                    <input type="hidden" name="shipping_state" :value="document.getElementById('billing_state').value">
                    <input type="hidden" name="shipping_zip" :value="document.getElementById('billing_zip').value">
                    <input type="hidden" name="shipping_country" :value="document.getElementById('billing_country').value">
                </div>
            </template>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
