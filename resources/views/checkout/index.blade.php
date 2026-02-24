<x-app-layout>
    @section('title', 'Checkout')
    @section('meta_description', 'Complete your order securely.')

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="text-center mb-10" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Secure Checkout</p>
                <h1 class="text-fluid-3xl font-display font-bold mb-3" style="color: var(--on-surface);">Checkout</h1>
                <p class="text-lg" style="color: var(--on-surface-muted);">Complete your order</p>
            </div>

            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Left Column: Checkout Form --}}
                    <div class="lg:col-span-2">
                        {{-- Error Summary --}}
                        @if($errors->any())
                            <div class="rounded-xl p-4 mb-6 bg-red-500/10" style="border: 1px solid rgba(239, 68, 68, 0.2);" data-animate="fade-up">
                                <p class="font-bold text-red-500 mb-2">Please correct the following errors:</p>
                                <ul class="list-disc list-inside text-red-500/80">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Customer Information --}}
                        <div class="card-glass rounded-2xl p-6 md:p-8 mb-6" data-animate="fade-up">
                            <h2 class="text-xl font-display font-bold mb-6" style="color: var(--on-surface);">Customer Information</h2>

                            <div class="mb-5">
                                <label for="name" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Full Name *</label>
                                <input type="text" name="name" id="name" required
                                       value="{{ old('name', $customer->name ?? Auth::user()->name ?? '') }}"
                                       class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <label for="email" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Email Address *</label>
                                <input type="email" name="email" id="email" required
                                       value="{{ old('email', $customer->email ?? Auth::user()->email ?? '') }}"
                                       class="glass-input w-full rounded-xl" style="color: var(--on-surface);"
                                       {{ Auth::check() ? 'readonly' : '' }}>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Phone Number</label>
                                <input type="tel" name="phone" id="phone"
                                       value="{{ old('phone', $customer->phone ?? '') }}"
                                       placeholder="(123) 456-7890"
                                       class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Shipping Address --}}
                        <div class="card-glass rounded-2xl p-6 md:p-8 mb-6" data-animate="fade-up"
                             x-data="savedAddressHandler()">
                            <h2 class="text-xl font-display font-bold mb-6" style="color: var(--on-surface);">Shipping Address</h2>

                            {{-- Saved Address Selector (authenticated users only) --}}
                            @auth
                            <div class="mb-5" x-show="addresses.length > 0">
                                <label for="saved_address" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">
                                    <i class="fas fa-bookmark mr-1 text-earth-primary"></i>Select Saved Address
                                </label>
                                <select id="saved_address" @change="fillAddress($event.target.value)"
                                        class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                    <option value="">-- Enter address manually --</option>
                                    <template x-for="addr in addresses" :key="addr.id">
                                        <option :value="addr.id" x-text="addr.label + ' - ' + addr.street + ', ' + addr.city + ', ' + addr.state + ' ' + addr.zip"></option>
                                    </template>
                                </select>
                                <p class="text-xs mt-1" style="color: var(--on-surface-muted);">
                                    <a href="{{ route('addresses.index') }}" class="text-earth-primary hover:underline">Manage saved addresses</a>
                                </p>
                            </div>
                            @endauth

                            <div class="mb-5">
                                <label for="shipping_street" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Street Address *</label>
                                <input type="text" name="shipping_street" id="shipping_street" required
                                       value="{{ old('shipping_street', $customer->shipping_street ?? '') }}"
                                       class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                @error('shipping_street')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                                <div>
                                    <label for="shipping_city" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">City *</label>
                                    <input type="text" name="shipping_city" id="shipping_city" required
                                           value="{{ old('shipping_city', $customer->shipping_city ?? '') }}"
                                           class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                    @error('shipping_city')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="shipping_state" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">State *</label>
                                    <select name="shipping_state" id="shipping_state" required
                                            class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                        <option value="">Select State</option>
                                        @foreach(['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'] as $code => $state)
                                            <option value="{{ $code }}" {{ old('shipping_state', $customer->shipping_state ?? '') == $code ? 'selected' : '' }}>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                    @error('shipping_state')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="shipping_zip" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">ZIP Code *</label>
                                    <input type="text" name="shipping_zip" id="shipping_zip" required
                                           value="{{ old('shipping_zip', $customer->shipping_zip ?? '') }}"
                                           class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                    @error('shipping_zip')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" name="shipping_country" value="US">
                        </div>

                        {{-- Billing Address --}}
                        <div class="card-glass rounded-2xl p-6 md:p-8 mb-6" data-animate="fade-up">
                            <h2 class="text-xl font-display font-bold mb-6" style="color: var(--on-surface);">Billing Address</h2>

                            <div class="mb-5">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="same_as_shipping" id="same_as_shipping" value="1"
                                           {{ old('same_as_shipping', true) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-earth-primary shadow-sm focus:ring-earth-primary"
                                           onchange="toggleBillingAddress()">
                                    <span class="ms-2 text-sm" style="color: var(--on-surface-muted);">Same as shipping address</span>
                                </label>
                            </div>

                            <div id="billing_address_fields" style="display: none;">
                                <div class="mb-5">
                                    <label for="billing_street" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Street Address *</label>
                                    <input type="text" name="billing_street" id="billing_street"
                                           value="{{ old('billing_street', $customer->billing_street ?? '') }}"
                                           class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                    @error('billing_street')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
                                    <div>
                                        <label for="billing_city" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">City *</label>
                                        <input type="text" name="billing_city" id="billing_city"
                                               value="{{ old('billing_city', $customer->billing_city ?? '') }}"
                                               class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                        @error('billing_city')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="billing_state" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">State *</label>
                                        <select name="billing_state" id="billing_state"
                                                class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                            <option value="">Select State</option>
                                            @foreach(['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'] as $code => $state)
                                                <option value="{{ $code }}" {{ old('billing_state', $customer->billing_state ?? '') == $code ? 'selected' : '' }}>{{ $state }}</option>
                                            @endforeach
                                        </select>
                                        @error('billing_state')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="billing_zip" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">ZIP Code *</label>
                                        <input type="text" name="billing_zip" id="billing_zip"
                                               value="{{ old('billing_zip', $customer->billing_zip ?? '') }}"
                                               class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                        @error('billing_zip')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <input type="hidden" name="billing_country" value="US">
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div class="card-glass rounded-2xl p-6 md:p-8 mb-6" data-animate="fade-up">
                            <h2 class="text-xl font-display font-bold mb-6" style="color: var(--on-surface);">Payment Method</h2>

                            <div class="space-y-3">
                                @if(config('business.payments.enabled_methods.stripe'))
                                <label class="flex items-center p-4 rounded-xl cursor-pointer transition-all duration-200 hover:bg-earth-primary/5 {{ old('payment_method', 'stripe') === 'stripe' ? 'bg-earth-primary/10' : '' }}" style="border: 2px solid {{ old('payment_method', 'stripe') === 'stripe' ? 'var(--earth-primary, #FF3366)' : 'var(--glass-border)' }};">
                                    <input type="radio" name="payment_method" value="stripe"
                                           {{ old('payment_method', 'stripe') === 'stripe' ? 'checked' : '' }}
                                           class="text-earth-primary focus:ring-earth-primary">
                                    <div class="ml-3 flex-1">
                                        <div class="font-medium" style="color: var(--on-surface);">Credit/Debit Card</div>
                                        <div class="text-sm" style="color: var(--on-surface-muted);">Secure payment via Stripe</div>
                                    </div>
                                    <i class="fab fa-cc-stripe text-2xl text-earth-green"></i>
                                </label>
                                @endif

                                @if(config('business.payments.enabled_methods.paypal'))
                                <label class="flex items-center p-4 rounded-xl {{ isset($paypalAvailable) && !$paypalAvailable ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:bg-earth-primary/5' }} transition-all duration-200 {{ old('payment_method') === 'paypal' ? 'bg-earth-primary/10' : '' }}" style="border: 2px solid {{ old('payment_method') === 'paypal' ? 'var(--earth-primary, #FF3366)' : 'var(--glass-border)' }};">
                                    <input type="radio" name="payment_method" value="paypal"
                                           {{ old('payment_method') === 'paypal' ? 'checked' : '' }}
                                           {{ isset($paypalAvailable) && !$paypalAvailable ? 'disabled' : '' }}
                                           class="text-earth-primary focus:ring-earth-primary">
                                    <div class="ml-3 flex-1">
                                        <div class="font-medium" style="color: var(--on-surface);">
                                            PayPal
                                            @if(isset($paypalAvailable) && !$paypalAvailable)
                                                <span class="text-sm ml-2" style="color: var(--on-surface-muted);">(Coming Soon)</span>
                                            @endif
                                        </div>
                                        <div class="text-sm" style="color: var(--on-surface-muted);">Pay with your PayPal account</div>
                                    </div>
                                    <i class="fab fa-paypal text-2xl text-earth-green"></i>
                                </label>
                                @endif

                                @if(config('business.payments.enabled_methods.cash'))
                                <label class="flex items-center p-4 rounded-xl cursor-pointer transition-all duration-200 hover:bg-earth-primary/5 {{ old('payment_method') === 'cash' ? 'bg-earth-primary/10' : '' }}" style="border: 2px solid {{ old('payment_method') === 'cash' ? 'var(--earth-primary, #FF3366)' : 'var(--glass-border)' }};">
                                    <input type="radio" name="payment_method" value="cash"
                                           {{ old('payment_method') === 'cash' ? 'checked' : '' }}
                                           class="text-earth-primary focus:ring-earth-primary">
                                    <div class="ml-3 flex-1">
                                        <div class="font-medium" style="color: var(--on-surface);">Cash</div>
                                        <div class="text-sm" style="color: var(--on-surface-muted);">Pay in person</div>
                                    </div>
                                    <i class="fas fa-money-bill-wave text-2xl text-earth-success"></i>
                                </label>
                                @endif

                                @if(config('business.payments.enabled_methods.check'))
                                <label class="flex items-center p-4 rounded-xl cursor-pointer transition-all duration-200 hover:bg-earth-primary/5 {{ old('payment_method') === 'check' ? 'bg-earth-primary/10' : '' }}" style="border: 2px solid {{ old('payment_method') === 'check' ? 'var(--earth-primary, #FF3366)' : 'var(--glass-border)' }};">
                                    <input type="radio" name="payment_method" value="check"
                                           {{ old('payment_method') === 'check' ? 'checked' : '' }}
                                           class="text-earth-primary focus:ring-earth-primary">
                                    <div class="ml-3 flex-1">
                                        <div class="font-medium" style="color: var(--on-surface);">Check</div>
                                        <div class="text-sm" style="color: var(--on-surface-muted);">Pay by check</div>
                                    </div>
                                    <i class="fas fa-money-check text-2xl" style="color: var(--on-surface-muted);"></i>
                                </label>
                                @endif
                            </div>

                            @error('payment_method')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Shipping Method --}}
                        <div class="card-glass rounded-2xl p-6 md:p-8 mb-6" data-animate="fade-up"
                             x-data="shippingHandler()">
                            <h2 class="text-xl font-display font-bold mb-6" style="color: var(--on-surface);">Shipping Method</h2>

                            <div class="space-y-3">
                                @foreach($shippingMethods as $method)
                                <label class="flex items-center p-4 rounded-xl cursor-pointer transition-all duration-200 hover:bg-earth-primary/5"
                                       :class="selectedMethod === '{{ $method['id'] }}' ? 'bg-earth-primary/10' : ''"
                                       :style="selectedMethod === '{{ $method['id'] }}' ? 'border: 2px solid var(--earth-primary, #FF3366)' : 'border: 2px solid var(--glass-border)'">
                                    <input type="radio" name="shipping_method" value="{{ $method['id'] }}"
                                           {{ old('shipping_method', $shippingMethods[0]['id'] ?? '') === $method['id'] ? 'checked' : '' }}
                                           @change="selectMethod('{{ $method['id'] }}', {{ $method['cost'] }})"
                                           class="text-earth-primary focus:ring-earth-primary">
                                    <div class="ml-3 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="font-medium" style="color: var(--on-surface);">{{ $method['name'] }}</div>
                                            <div class="font-bold {{ $method['cost'] == 0 ? 'text-earth-success' : '' }}" style="{{ $method['cost'] > 0 ? 'color: var(--on-surface);' : '' }}">
                                                {{ $method['cost'] == 0 ? 'FREE' : '$' . number_format($method['cost'], 2) }}
                                            </div>
                                        </div>
                                        <div class="text-sm" style="color: var(--on-surface-muted);">{{ $method['description'] }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>

                            @error('shipping_method')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Additional Information --}}
                        <div class="card-glass rounded-2xl p-6 md:p-8 mb-6" data-animate="fade-up">
                            <h2 class="text-xl font-display font-bold mb-6" style="color: var(--on-surface);">Additional Information</h2>

                            <div class="mb-5">
                                <label for="notes" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Order Notes (Optional)</label>
                                <textarea name="notes" id="notes" rows="4"
                                          class="glass-input w-full rounded-xl" style="color: var(--on-surface);"
                                          placeholder="Special instructions or delivery notes">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="newsletter_opt_in" value="1"
                                           {{ old('newsletter_opt_in') ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-earth-primary shadow-sm focus:ring-earth-primary">
                                    <span class="ms-2 text-sm" style="color: var(--on-surface-muted);">Subscribe to our newsletter for tips and exclusive offers</span>
                                </label>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="card-glass rounded-2xl p-6 md:p-8" data-animate="fade-up">
                            <button type="submit" class="btn-gradient btn-lg w-full">
                                <i class="fas fa-lock mr-2"></i>Place Order
                            </button>
                            <p class="text-sm text-center mt-4" style="color: var(--on-surface-muted);">
                                Your payment information will be collected on the next page
                            </p>
                        </div>
                    </div>

                    {{-- Right Column: Order Summary --}}
                    <div class="lg:col-span-1" data-animate="fade-up">
                        <div class="card-glass rounded-2xl p-6 sticky top-24">
                            <h2 class="text-xl font-display font-bold mb-4" style="color: var(--on-surface);">Order Summary</h2>

                            {{-- Cart Items --}}
                            <div class="space-y-4 mb-6">
                                @foreach($cartItems as $item)
                                    @php
                                        $itemPrice = $item->variant
                                            ? (float) $item->variant->retail_price
                                            : ($item->item->current_price ?? $item->item->base_price ?? 0);
                                    @endphp
                                    <div class="flex items-start gap-4 pb-4" style="border-bottom: 1px solid var(--glass-border);">
                                        <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden">
                                            @if($item->variant && $item->item && $item->item->isPrintful && $item->item->mockups && $item->item->mockups->count() > 0)
                                                <img src="{{ $item->item->mockups->where('is_primary', true)->first()?->mockup_url ?? $item->item->mockups->first()->mockup_url }}" alt="{{ $item->item->name }}" class="w-full h-full object-cover">
                                            @elseif($item->item && $item->item->images && count($item->item->images) > 0)
                                                <img src="{{ asset('storage/' . $item->item->images[0]) }}" alt="{{ $item->item->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-earth-primary/20 to-earth-green/20">
                                                    <i class="fas fa-box text-earth-primary/40"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1">
                                            <h3 class="text-sm font-semibold" style="color: var(--on-surface);">{{ $item->item->name }}</h3>
                                            @if($item->variant)
                                                <p class="text-xs" style="color: var(--on-surface-muted);">{{ $item->variant->display_name }} &middot; Qty: {{ $item->quantity }}</p>
                                            @else
                                                <p class="text-xs" style="color: var(--on-surface-muted);">Qty: {{ $item->quantity }}</p>
                                            @endif
                                            <p class="text-sm font-bold text-gradient mt-1">
                                                ${{ number_format($itemPrice * $item->quantity, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Coupon Code --}}
                            <div x-data="couponHandler()" class="mb-4 pb-4" style="border-bottom: 1px solid var(--glass-border);">
                                <label class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Coupon Code</label>
                                <div class="flex gap-2">
                                    <input type="text" x-model="code" x-ref="codeInput"
                                           placeholder="Enter code"
                                           :disabled="applied"
                                           class="glass-input flex-1 rounded-xl text-sm" style="color: var(--on-surface); text-transform: uppercase;">
                                    <button type="button" @click="applied ? removeCoupon() : applyCoupon()"
                                            :class="applied ? 'bg-red-500 hover:bg-red-600' : 'bg-earth-primary hover:bg-earth-primary/90'"
                                            class="px-4 py-2 text-white rounded-xl text-sm font-medium transition-colors"
                                            :disabled="loading">
                                        <span x-show="loading"><i class="fas fa-spinner fa-spin"></i></span>
                                        <span x-show="!loading && !applied">Apply</span>
                                        <span x-show="!loading && applied"><i class="fas fa-times"></i></span>
                                    </button>
                                </div>
                                <input type="hidden" name="coupon_code" :value="applied ? code : ''">
                                <p x-show="message" x-text="message" class="text-sm mt-2"
                                   :class="applied ? 'text-earth-success' : 'text-red-500'"></p>
                            </div>

                            {{-- Loyalty Points Redemption --}}
                            @auth
                            @if(($loyaltyBalance ?? 0) > 0)
                            <div x-data="loyaltyHandler()" class="mb-4 pb-4" style="border-bottom: 1px solid var(--glass-border);">
                                <label class="block text-sm font-medium mb-2" style="color: var(--on-surface);">
                                    <i class="fas fa-coins text-earth-amber mr-1"></i>Loyalty Points
                                </label>
                                <p class="text-xs mb-2" style="color: var(--on-surface-muted);">
                                    You have <span class="font-bold text-earth-amber">{{ number_format($loyaltyBalance) }}</span> points available (max {{ number_format($maxRedeemable) }} redeemable)
                                </p>
                                <div class="flex gap-2 items-center">
                                    <input type="number" x-model.number="loyaltyPoints"
                                           min="0" max="{{ $maxRedeemable }}"
                                           placeholder="0"
                                           @input="loyaltyPoints = Math.min(Math.max(0, loyaltyPoints || 0), {{ $maxRedeemable }})"
                                           class="glass-input flex-1 rounded-xl text-sm" style="color: var(--on-surface);">
                                    <button type="button" @click="loyaltyPoints = {{ $maxRedeemable }}"
                                            class="px-3 py-2 bg-earth-amber/10 text-earth-amber rounded-xl text-xs font-medium hover:bg-earth-amber/20 transition-colors whitespace-nowrap">
                                        Use Max
                                    </button>
                                </div>
                                <input type="hidden" name="redeem_points" :value="loyaltyPoints">
                                <p x-show="loyaltyPoints > 0" class="text-sm mt-2 text-earth-success">
                                    = $<span x-text="loyaltyDiscount"></span> discount
                                </p>
                            </div>
                            @endif
                            @endauth

                            {{-- Pricing Summary --}}
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between">
                                    <span style="color: var(--on-surface-muted);">Subtotal</span>
                                    <span class="font-semibold" style="color: var(--on-surface);">${{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span style="color: var(--on-surface-muted);">Estimated Tax</span>
                                    <span class="font-semibold" style="color: var(--on-surface);">${{ number_format($tax, 2) }}</span>
                                </div>
                                <div class="flex justify-between" x-data>
                                    <span style="color: var(--on-surface-muted);">Shipping</span>
                                    <span class="font-semibold" :class="$store.shipping.cost == 0 ? 'text-earth-success' : ''" :style="$store.shipping.cost > 0 ? 'color: var(--on-surface);' : ''" x-text="$store.shipping.cost == 0 ? 'FREE' : '$' + $store.shipping.cost.toFixed(2)">
                                        FREE
                                    </span>
                                </div>
                                <div x-data x-show="$store.coupon && $store.coupon.discount > 0" class="flex justify-between" style="display: none;">
                                    <span class="text-earth-success">Discount</span>
                                    <span class="font-semibold text-earth-success" x-text="$store.coupon ? $store.coupon.formatted : ''"></span>
                                </div>
                                <div x-data x-show="$store.loyalty && $store.loyalty.discount > 0" class="flex justify-between" style="display: none;">
                                    <span class="text-earth-amber">Loyalty Points</span>
                                    <span class="font-semibold text-earth-amber" x-text="$store.loyalty ? '-$' + $store.loyalty.discount.toFixed(2) : ''"></span>
                                </div>
                                <div class="pt-3 flex justify-between text-lg" style="border-top: 1px solid var(--glass-border);">
                                    <span class="font-bold" style="color: var(--on-surface);">Total</span>
                                    <span class="font-bold text-gradient" x-data x-text="calculateTotal()">
                                        ${{ number_format($total, 2) }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ route('cart.index') }}" class="block w-full text-center text-earth-primary hover:opacity-80 text-sm mt-4 transition-opacity">
                                <i class="fas fa-edit mr-1"></i>Edit Cart
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    @push('scripts')
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('coupon', { discount: 0, formatted: '', newTotal: {{ $total }} });
        Alpine.store('shipping', { cost: {{ $shippingMethods[0]['cost'] ?? 0 }} });
        Alpine.store('loyalty', { discount: 0, points: 0 });
    });

    function calculateTotal() {
        const baseTotal = {{ $total }};
        const shippingCost = Alpine.store('shipping') ? Alpine.store('shipping').cost : 0;
        const discount = (Alpine.store('coupon') && Alpine.store('coupon').discount > 0) ? Alpine.store('coupon').discount : 0;
        const loyaltyDiscount = (Alpine.store('loyalty') && Alpine.store('loyalty').discount > 0) ? Alpine.store('loyalty').discount : 0;
        return '$' + (baseTotal + shippingCost - discount - loyaltyDiscount).toFixed(2);
    }

    function loyaltyHandler() {
        return {
            loyaltyPoints: 0,
            loyaltyBalance: {{ $loyaltyBalance ?? 0 }},
            maxRedeemable: {{ $maxRedeemable ?? 0 }},
            pointsPerDollar: {{ $pointsPerDollar ?? 100 }},
            get loyaltyDiscount() {
                const pts = Math.min(Math.max(0, this.loyaltyPoints || 0), this.maxRedeemable);
                const disc = (pts / this.pointsPerDollar).toFixed(2);
                Alpine.store('loyalty', { discount: parseFloat(disc), points: pts });
                return disc;
            }
        };
    }

    function shippingHandler() {
        return {
            selectedMethod: '{{ old("shipping_method", $shippingMethods[0]["id"] ?? "standard") }}',
            selectMethod(method, cost) {
                this.selectedMethod = method;
                Alpine.store('shipping', { cost: cost });
            }
        };
    }

    function couponHandler() {
        return {
            code: '{{ old("coupon_code", $promoCode ?? "") }}',
            applied: false,
            loading: false,
            message: '',
            init() {
                @if(!empty($promoResult) && $promoResult['valid'])
                    this.applied = true;
                    this.message = 'Coupon applied: {{ $promoResult["formatted"] }} off';
                    Alpine.store('coupon', {
                        discount: {{ $promoResult['discount'] }},
                        formatted: '{{ $promoResult["formatted"] }}',
                        newTotal: {{ $total - $promoResult['discount'] }}
                    });
                @elseif(!empty($promoCode) && !empty($promoResult) && !$promoResult['valid'])
                    this.message = '{{ $promoResult["error"] }}';
                @endif
            },
            applyCoupon() {
                if (!this.code.trim()) return;
                this.loading = true;
                this.message = '';

                fetch('{{ route("api.coupon.validate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: this.code.trim(), subtotal: {{ $subtotal }} })
                })
                .then(r => r.json())
                .then(data => {
                    this.loading = false;
                    if (data.valid) {
                        this.applied = true;
                        this.message = data.message;
                        Alpine.store('coupon', {
                            discount: data.discount,
                            formatted: data.formatted,
                            newTotal: {{ $total }} + (Alpine.store('shipping') ? Alpine.store('shipping').cost : 0) - data.discount
                        });
                    } else {
                        this.message = data.error;
                    }
                })
                .catch(() => {
                    this.loading = false;
                    this.message = 'Unable to validate coupon. Please try again.';
                });
            },
            removeCoupon() {
                this.applied = false;
                this.message = '';
                this.code = '';
                Alpine.store('coupon', { discount: 0, formatted: '', newTotal: {{ $total }} });
            }
        };
    }

    function savedAddressHandler() {
        return {
            addresses: [],
            init() {
                @auth
                fetch('{{ route("addresses.json") }}', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => { this.addresses = data; })
                .catch(() => {});
                @endauth
            },
            fillAddress(addressId) {
                if (!addressId) return;
                const addr = this.addresses.find(a => a.id == addressId);
                if (!addr) return;

                document.getElementById('shipping_street').value = addr.street;
                document.getElementById('shipping_city').value = addr.city;
                document.getElementById('shipping_state').value = addr.state;
                document.getElementById('shipping_zip').value = addr.zip;

                if (addr.phone && document.getElementById('phone')) {
                    document.getElementById('phone').value = addr.phone;
                }
            }
        };
    }

    function toggleBillingAddress() {
        const checkbox = document.getElementById('same_as_shipping');
        const billingFields = document.getElementById('billing_address_fields');
        const billingInputs = billingFields.querySelectorAll('input:not([type="hidden"]), select');

        if (checkbox.checked) {
            billingFields.style.display = 'none';
            billingInputs.forEach(input => {
                input.removeAttribute('required');
            });
        } else {
            billingFields.style.display = 'block';
            billingInputs.forEach(input => {
                input.setAttribute('required', 'required');
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleBillingAddress();

        {{-- GA4: begin_checkout --}}
        @if(config('services.google.analytics_id'))
        gtag('event', 'begin_checkout', {
            currency: 'USD',
            value: {{ $cartTotal ?? 0 }},
            items: [
                @foreach($cartItems as $cartItem)
                {
                    item_id: '{{ $cartItem->item->sku ?? $cartItem->item->id }}',
                    item_name: @json($cartItem->item->name),
                    price: {{ $cartItem->item->current_price ?? $cartItem->item->base_price ?? 0 }},
                    quantity: {{ $cartItem->quantity }},
                },
                @endforeach
            ]
        });
        @endif

        {{-- Meta Pixel: InitiateCheckout --}}
        @if(config('services.meta.pixel_id'))
        fbq('track', 'InitiateCheckout', {
            content_ids: [@foreach($cartItems as $cartItem)'{{ $cartItem->item->sku ?? $cartItem->item->id }}',@endforeach],
            content_type: 'product',
            value: {{ $cartTotal ?? 0 }},
            currency: 'USD',
            num_items: {{ $cartItems->sum('quantity') }}
        });
        @endif
    });
    </script>
    @endpush
</x-app-layout>
