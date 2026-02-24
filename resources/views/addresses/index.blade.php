<x-app-layout>
    @section('title', 'My Addresses')
    @section('meta_description', 'Manage your saved addresses for faster checkout.')

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="text-center mb-10" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Address Book</p>
                <h1 class="text-fluid-3xl font-display font-bold mb-3" style="color: var(--on-surface);">My Addresses</h1>
                <p class="text-lg" style="color: var(--on-surface-muted);">Manage your saved addresses for faster checkout</p>
            </div>

            <div x-data="addressBook()" x-cloak>
                {{-- Add New Address Button --}}
                <div class="flex justify-end mb-6" data-animate="fade-up">
                    <button @click="openAddForm()" class="btn-gradient py-2.5 px-6">
                        <i class="fas fa-plus mr-2"></i>Add New Address
                    </button>
                </div>

                {{-- Address Form Modal --}}
                <div x-show="showForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);">
                    <div @click.outside="showForm = false" class="card-glass rounded-2xl p-6 md:p-8 w-full max-w-lg max-h-[90vh] overflow-y-auto" style="background: var(--surface);">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-display font-bold" style="color: var(--on-surface);" x-text="editingId ? 'Edit Address' : 'Add New Address'"></h2>
                            <button @click="showForm = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" style="color: var(--on-surface-muted);">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <form :action="editingId ? '{{ url('addresses') }}/' + editingId : '{{ route('addresses.store') }}'" method="POST">
                            @csrf
                            <template x-if="editingId">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div class="mb-4">
                                <label for="label" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Label *</label>
                                <input type="text" name="label" id="label" required x-model="form.label"
                                       placeholder="e.g., Home, Work, Office"
                                       class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                            </div>

                            <div class="mb-4">
                                <label for="type" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Address Type *</label>
                                <select name="type" id="type" required x-model="form.type"
                                        class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                    <option value="both">Shipping & Billing</option>
                                    <option value="shipping">Shipping Only</option>
                                    <option value="billing">Billing Only</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="street" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Street Address *</label>
                                <input type="text" name="street" id="street" required x-model="form.street"
                                       class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label for="city" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">City *</label>
                                    <input type="text" name="city" id="city" required x-model="form.city"
                                           class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                </div>

                                <div>
                                    <label for="state" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">State *</label>
                                    <select name="state" id="state" required x-model="form.state"
                                            class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                        <option value="">Select</option>
                                        @foreach(['AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut','DE'=>'Delaware','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois','IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland','MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana','NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York','NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania','RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah','VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming'] as $code => $state)
                                            <option value="{{ $code }}">{{ $state }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="zip" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">ZIP Code *</label>
                                    <input type="text" name="zip" id="zip" required x-model="form.zip"
                                           placeholder="12345"
                                           class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="phone" class="block text-sm font-medium mb-2" style="color: var(--on-surface);">Phone (Optional)</label>
                                <input type="tel" name="phone" id="phone" x-model="form.phone"
                                       placeholder="(123) 456-7890"
                                       class="glass-input w-full rounded-xl" style="color: var(--on-surface);">
                            </div>

                            <input type="hidden" name="country" value="US">

                            <div class="mb-6">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_default" value="1" x-model="form.is_default"
                                           class="rounded border-gray-300 text-earth-primary shadow-sm focus:ring-earth-primary">
                                    <span class="ms-2 text-sm" style="color: var(--on-surface-muted);">Set as default address</span>
                                </label>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="btn-gradient flex-1 py-2.5">
                                    <i class="fas fa-save mr-2"></i><span x-text="editingId ? 'Update Address' : 'Save Address'"></span>
                                </button>
                                <button type="button" @click="showForm = false" class="btn-glass py-2.5 px-6" style="color: var(--on-surface);">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Address List --}}
                @if($addresses->isEmpty())
                    <div class="card-glass rounded-2xl p-12 text-center" data-animate="fade-up">
                        <i class="fas fa-map-marker-alt text-5xl mb-4" style="color: var(--on-surface-muted);"></i>
                        <h2 class="font-display text-xl font-semibold mb-2" style="color: var(--on-surface);">No saved addresses</h2>
                        <p class="mb-6" style="color: var(--on-surface-muted);">Add an address to speed up your checkout process.</p>
                        <button @click="openAddForm()" class="btn-gradient py-2.5 px-6">
                            <i class="fas fa-plus mr-2"></i>Add Your First Address
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" data-animate="stagger">
                        @foreach($addresses as $address)
                            <div class="card-glass rounded-2xl p-6 relative">
                                {{-- Default Badge --}}
                                @if($address->is_default)
                                    <span class="absolute top-4 right-4 text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-lg bg-earth-success/15 text-earth-success">
                                        Default
                                    </span>
                                @endif

                                {{-- Label & Type --}}
                                <div class="mb-4">
                                    <h3 class="font-display font-bold text-lg" style="color: var(--on-surface);">
                                        <i class="fas fa-{{ $address->label === 'Work' || $address->label === 'Office' ? 'building' : 'home' }} mr-2 text-earth-primary"></i>
                                        {{ $address->label }}
                                    </h3>
                                    <span class="text-xs font-medium uppercase tracking-wider px-2 py-0.5 rounded-md mt-1 inline-block"
                                          style="background: var(--surface-raised); color: var(--on-surface-muted);">
                                        {{ $address->type === 'both' ? 'Shipping & Billing' : ucfirst($address->type) }}
                                    </span>
                                </div>

                                {{-- Address Details --}}
                                <div class="mb-5" style="color: var(--on-surface-muted);">
                                    <p>{{ $address->street }}</p>
                                    <p>{{ $address->city }}, {{ $address->state }} {{ $address->zip }}</p>
                                    @if($address->phone)
                                        <p class="mt-1"><i class="fas fa-phone text-xs mr-1"></i>{{ $address->phone }}</p>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2 flex-wrap">
                                    <button @click="openEditForm({{ $address->toJson() }})"
                                            class="btn-glass text-sm py-1.5 px-4" style="color: var(--on-surface);">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>

                                    @if(!$address->is_default)
                                        <form action="{{ route('addresses.set-default', $address) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn-glass text-sm py-1.5 px-4" style="color: var(--on-surface);">
                                                <i class="fas fa-star mr-1"></i>Set Default
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete this address?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm py-1.5 px-4 rounded-lg text-red-500 hover:bg-red-500/10 transition-colors">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Back to Dashboard --}}
                <div class="mt-8 text-center" data-animate="fade-up">
                    <a href="{{ route('dashboard') }}" class="text-earth-primary hover:opacity-80 transition-opacity">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
    function addressBook() {
        return {
            showForm: false,
            editingId: null,
            form: {
                label: 'Home',
                type: 'both',
                street: '',
                city: '',
                state: '',
                zip: '',
                phone: '',
                is_default: false,
            },
            openAddForm() {
                this.editingId = null;
                this.form = {
                    label: 'Home',
                    type: 'both',
                    street: '',
                    city: '',
                    state: '',
                    zip: '',
                    phone: '',
                    is_default: false,
                };
                this.showForm = true;
            },
            openEditForm(address) {
                this.editingId = address.id;
                this.form = {
                    label: address.label,
                    type: address.type,
                    street: address.street,
                    city: address.city,
                    state: address.state,
                    zip: address.zip,
                    phone: address.phone || '',
                    is_default: address.is_default,
                };
                this.showForm = true;
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
