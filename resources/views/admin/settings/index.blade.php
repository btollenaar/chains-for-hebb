@extends('layouts.admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
        <p class="text-gray-600 mt-1">Manage your business settings and configuration</p>
    </div>

    <div class="pb-12" x-data="{ activeTab: 'profile' }">
        <div class="max-w-7xl mx-auto">
            <!-- Tab Navigation -->
            <div class="bg-white shadow-sm rounded-t-lg">
                <!-- Mobile: Vertical Stack -->
                <nav class="flex flex-col md:hidden border-b border-gray-200" aria-label="Settings tabs">
                    <button @click="activeTab = 'profile'"
                            :class="activeTab === 'profile' ? 'bg-gray-50 text-abs-primary border-l-4 border-abs-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-l-4 border-transparent'"
                            class="w-full text-left py-3 px-4 font-medium text-sm transition-colors"
                            aria-label="Profile settings tab">
                        <i class="fas fa-building mr-2 w-5"></i> Profile
                    </button>
                    <button @click="activeTab = 'contact'"
                            :class="activeTab === 'contact' ? 'bg-gray-50 text-abs-primary border-l-4 border-abs-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-l-4 border-transparent'"
                            class="w-full text-left py-3 px-4 font-medium text-sm transition-colors"
                            aria-label="Contact settings tab">
                        <i class="fas fa-address-card mr-2 w-5"></i> Contact
                    </button>
                    <button @click="activeTab = 'social'"
                            :class="activeTab === 'social' ? 'bg-gray-50 text-abs-primary border-l-4 border-abs-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-l-4 border-transparent'"
                            class="w-full text-left py-3 px-4 font-medium text-sm transition-colors"
                            aria-label="Social media settings tab">
                        <i class="fas fa-share-alt mr-2 w-5"></i> Social Media
                    </button>
                    <button @click="activeTab = 'branding'"
                            :class="activeTab === 'branding' ? 'bg-gray-50 text-abs-primary border-l-4 border-abs-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-l-4 border-transparent'"
                            class="w-full text-left py-3 px-4 font-medium text-sm transition-colors"
                            aria-label="Branding settings tab">
                        <i class="fas fa-palette mr-2 w-5"></i> Branding
                    </button>
                    <button @click="activeTab = 'features'"
                            :class="activeTab === 'features' ? 'bg-gray-50 text-abs-primary border-l-4 border-abs-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-l-4 border-transparent'"
                            class="w-full text-left py-3 px-4 font-medium text-sm transition-colors"
                            aria-label="Features settings tab">
                        <i class="fas fa-toggle-on mr-2 w-5"></i> Features
                    </button>
                    <button @click="activeTab = 'theme'"
                            :class="activeTab === 'theme' ? 'bg-gray-50 text-abs-primary border-l-4 border-abs-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-l-4 border-transparent'"
                            class="w-full text-left py-3 px-4 font-medium text-sm transition-colors"
                            aria-label="Theme settings tab">
                        <i class="fas fa-paint-brush mr-2 w-5"></i> Theme
                    </button>
                    <button @click="activeTab = 'homepage'"
                            :class="activeTab === 'homepage' ? 'bg-gray-50 text-abs-primary border-l-4 border-abs-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-l-4 border-transparent'"
                            class="w-full text-left py-3 px-4 font-medium text-sm transition-colors"
                            aria-label="Homepage settings tab">
                        <i class="fas fa-home mr-2 w-5"></i> Homepage
                    </button>
                    <button @click="activeTab = 'navigation'"
                            :class="activeTab === 'navigation' ? 'bg-gray-50 text-abs-primary border-l-4 border-abs-primary' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700 border-l-4 border-transparent'"
                            class="w-full text-left py-3 px-4 font-medium text-sm transition-colors"
                            aria-label="Navigation settings tab">
                        <i class="fas fa-bars mr-2 w-5"></i> Navigation
                    </button>
                </nav>

                <!-- Desktop: Horizontal Tabs -->
                <nav class="hidden md:flex border-b border-gray-200 overflow-x-auto" aria-label="Settings tabs">
                    <button @click="activeTab = 'profile'"
                            :class="activeTab === 'profile' ? 'border-abs-primary text-abs-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                            aria-label="Profile settings tab">
                        <i class="fas fa-building mr-2"></i> Profile
                    </button>
                    <button @click="activeTab = 'contact'"
                            :class="activeTab === 'contact' ? 'border-abs-primary text-abs-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                            aria-label="Contact settings tab">
                        <i class="fas fa-address-card mr-2"></i> Contact
                    </button>
                    <button @click="activeTab = 'social'"
                            :class="activeTab === 'social' ? 'border-abs-primary text-abs-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                            aria-label="Social media settings tab">
                        <i class="fas fa-share-alt mr-2"></i> Social Media
                    </button>
                    <button @click="activeTab = 'branding'"
                            :class="activeTab === 'branding' ? 'border-abs-primary text-abs-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                            aria-label="Branding settings tab">
                        <i class="fas fa-palette mr-2"></i> Branding
                    </button>
                    <button @click="activeTab = 'features'"
                            :class="activeTab === 'features' ? 'border-abs-primary text-abs-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                            aria-label="Features settings tab">
                        <i class="fas fa-toggle-on mr-2"></i> Features
                    </button>
                    <button @click="activeTab = 'theme'"
                            :class="activeTab === 'theme' ? 'border-abs-primary text-abs-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                            aria-label="Theme settings tab">
                        <i class="fas fa-paint-brush mr-2"></i> Theme
                    </button>
                    <button @click="activeTab = 'homepage'"
                            :class="activeTab === 'homepage' ? 'border-abs-primary text-abs-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                            aria-label="Homepage settings tab">
                        <i class="fas fa-home mr-2"></i> Homepage
                    </button>
                    <button @click="activeTab = 'navigation'"
                            :class="activeTab === 'navigation' ? 'border-abs-primary text-abs-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition-colors"
                            aria-label="Navigation settings tab">
                        <i class="fas fa-bars mr-2"></i> Navigation
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="bg-white shadow-sm rounded-b-lg p-6">
                <!-- Profile Tab -->
                <div x-show="activeTab === 'profile'" x-cloak>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Business Profile</h2>
                    <p class="text-gray-600 mb-6">Basic information about your business</p>

                    <form action="{{ route('admin.settings.update.profile') }}" method="POST">
                        @csrf
                        @method('PUT')

                        @foreach($profileSettings as $setting)
                            <div class="mb-6">
                                <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                </label>
                                <input type="text"
                                       name="{{ $setting->key }}"
                                       id="{{ $setting->key }}"
                                       value="{{ old($setting->key, $setting->value) }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                       {{ $setting->key === 'business_name' ? 'required' : '' }}>
                                @if($setting->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ $setting->description }}</p>
                                @endif
                                @error($setting->key)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Save Profile Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Contact Tab -->
                <div x-show="activeTab === 'contact'" x-cloak>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Information</h2>
                    <p class="text-gray-600 mb-6">How customers can reach you</p>

                    <form action="{{ route('admin.settings.update.contact') }}" method="POST">
                        @csrf
                        @method('PUT')

                        @foreach($contactSettings as $setting)
                            <div class="mb-6">
                                <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                </label>
                                <input type="{{ $setting->key === 'email' ? 'email' : 'text' }}"
                                       name="{{ $setting->key }}"
                                       id="{{ $setting->key }}"
                                       value="{{ old($setting->key, $setting->value) }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                       required>
                                @if($setting->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ $setting->description }}</p>
                                @endif
                                @error($setting->key)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Save Contact Information
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Social Media Tab -->
                <div x-show="activeTab === 'social'" x-cloak>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Social Media Links</h2>
                    <p class="text-gray-600 mb-6">Your social media profiles (leave empty to hide icons)</p>

                    <form action="{{ route('admin.settings.update.social') }}" method="POST">
                        @csrf
                        @method('PUT')

                        @foreach($socialSettings as $setting)
                            <div class="mb-6">
                                <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-{{ str_replace(['_url', '_'], ['', '-'], $setting->key) }} mr-2"></i>
                                    {{ ucwords(str_replace(['_url', '_'], ['', ' '], $setting->key)) }}
                                </label>
                                <input type="url"
                                       name="{{ $setting->key }}"
                                       id="{{ $setting->key }}"
                                       value="{{ old($setting->key, $setting->value) }}"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                       placeholder="https://">
                                @if($setting->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ $setting->description }}</p>
                                @endif
                                @error($setting->key)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Save Social Media Links
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Branding Tab -->
                <div x-show="activeTab === 'branding'" x-cloak>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Branding Assets</h2>
                    <p class="text-gray-600 mb-6">Logo, favicon, and other brand assets</p>

                    <form action="{{ route('admin.settings.update.branding') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Logo Upload -->
                        <x-admin.image-upload
                            name="logo"
                            label="Business Logo"
                            :currentPath="\App\Models\Setting::get('branding.logo_path')"
                            description="Recommended: 300x100px, transparent background"
                            :maxSize="5120"
                            previewWidth="w-48"
                            previewHeight="h-16"
                            objectFit="object-contain"
                            :showBackground="true" />

                        <!-- Logo Alt Text -->
                        <div class="mb-6">
                            <label for="logo_alt" class="block text-sm font-medium text-gray-700 mb-2">
                                Logo Alt Text
                            </label>
                            <input type="text"
                                   name="logo_alt"
                                   id="logo_alt"
                                   value="{{ old('logo_alt', $brandingSettings->firstWhere('key', 'logo_alt')?->value) }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                   placeholder="{{ config('business.profile.name', 'Your Business Name') }}">
                            <p class="text-sm text-gray-500 mt-1">Descriptive text for accessibility and SEO</p>
                            @error('logo_alt')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Favicon Upload -->
                        <x-admin.image-upload
                            name="favicon"
                            label="Favicon"
                            :currentPath="\App\Models\Setting::get('branding.favicon_path')"
                            description="Recommended: 32x32px or 16x16px, .ico or .png format"
                            accept="image/x-icon,image/png"
                            :maxSize="1024"
                            previewWidth="w-16"
                            previewHeight="h-16" />

                        <!-- Hero Video URL -->
                        <div class="mb-6">
                            <label for="hero_video_url" class="block text-sm font-medium text-gray-700 mb-2">
                                Hero Video URL
                            </label>
                            <input type="url"
                                   name="hero_video_url"
                                   id="hero_video_url"
                                   value="{{ old('hero_video_url', $brandingSettings->firstWhere('key', 'hero_video_url')?->value) }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                   placeholder="https://www.youtube.com/embed/...">
                            <p class="text-sm text-gray-500 mt-1">YouTube or Vimeo embed URL for homepage hero section</p>
                            @error('hero_video_url')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <p class="text-sm text-green-800">
                                <i class="fas fa-check-circle mr-2"></i>
                                <strong>New:</strong> Upload images directly using the file inputs above. Images are stored securely and managed automatically.
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Save Branding Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Features Tab -->
                <div x-show="activeTab === 'features'" x-cloak>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Feature Toggles</h2>
                    <p class="text-gray-600 mb-6">Enable or disable specific features for your business</p>

                    <form action="{{ route('admin.settings.update.features') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4 mb-6">
                            @foreach($featureSettings as $setting)
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox"
                                               name="{{ $setting->key }}"
                                               id="{{ $setting->key }}"
                                               value="1"
                                               {{ old($setting->key, $setting->value) == '1' ? 'checked' : '' }}
                                               class="h-4 w-4 text-abs-primary focus:ring-abs-primary border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3">
                                        <label for="{{ $setting->key }}" class="font-medium text-gray-700">
                                            {{ ucwords(str_replace('_enabled', '', str_replace('_', ' ', $setting->key))) }}
                                        </label>
                                        @if($setting->description)
                                            <p class="text-sm text-gray-500">{{ $setting->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Warning:</strong> Disabling features will hide them from customers but won't delete existing data.
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Save Feature Settings
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Theme Tab -->
                <div x-show="activeTab === 'theme'" x-cloak>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Theme & Colors</h2>
                    <p class="text-gray-600 mb-6">Customize your brand colors and visual theme</p>

                    <form action="{{ route('admin.settings.update.theme') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6 mb-6">
                            @foreach($themeSettings as $setting)
                                <x-admin.color-picker
                                    name="{{ $setting->key }}"
                                    label="{{ $setting->description }}"
                                    :currentValue="$setting->value"
                                    :required="true"
                                />
                            @endforeach
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Note about theme changes</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>After saving theme colors, refresh the page to see the changes applied throughout the site.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Save Theme Colors
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Homepage Tab -->
                <div x-show="activeTab === 'homepage'" x-cloak>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Homepage Hero Section</h2>
                    <p class="text-gray-600 mb-6">Customize the main hero section and call-to-action buttons on your homepage</p>

                    <form action="{{ route('admin.settings.update.homepage') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Hero Content -->
                        @foreach($homepageSettings as $setting)
                            @if(!in_array($setting->key, ['hero_products_button']))
                            <div class="mb-6">
                                <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ ucwords(str_replace('_', ' ', str_replace('hero_', '', $setting->key))) }}
                                </label>
                                @if($setting->type === 'text')
                                    <textarea
                                        name="{{ $setting->key }}"
                                        id="{{ $setting->key }}"
                                        rows="3"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                        required>{{ old($setting->key, $setting->value) }}</textarea>
                                @else
                                    <input type="text"
                                           name="{{ $setting->key }}"
                                           id="{{ $setting->key }}"
                                           value="{{ old($setting->key, $setting->value) }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                           required>
                                @endif
                                @if($setting->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ $setting->description }}</p>
                                @endif
                                @error($setting->key)
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif
                        @endforeach

                        <hr class="my-8 border-gray-200">

                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Call-to-Action Buttons</h3>
                        <p class="text-gray-600 mb-6">Configure the two main buttons displayed below the hero text</p>

                        <!-- Products Button -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-base font-semibold text-gray-900">Products Button</h4>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           name="nav_products_enabled"
                                           id="nav_products_enabled"
                                           value="1"
                                           {{ old('nav_products_enabled', $settings->where('key', 'nav_products_enabled')->first()->value ?? '1') == '1' ? 'checked' : '' }}
                                           class="h-4 w-4 text-abs-primary focus:ring-abs-primary border-gray-300 rounded mr-2">
                                    <label for="nav_products_enabled" class="text-sm font-medium text-gray-700">
                                        Show Button
                                    </label>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="hero_products_button" class="block text-sm font-medium text-gray-700 mb-2">
                                        Button Text
                                    </label>
                                    <input type="text"
                                           name="hero_products_button"
                                           id="hero_products_button"
                                           value="{{ old('hero_products_button', $homepageSettings->where('key', 'hero_products_button')->first()->value ?? 'Shop Products') }}"
                                           placeholder="Shop Products"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                           required>
                                    <p class="text-sm text-gray-500 mt-1">The text displayed on the button</p>
                                </div>

                                <div>
                                    <label for="nav_products_url" class="block text-sm font-medium text-gray-700 mb-2">
                                        Button Link
                                    </label>
                                    <input type="text"
                                           name="nav_products_url"
                                           id="nav_products_url"
                                           value="{{ old('nav_products_url', $settings->where('key', 'nav_products_url')->first()->value ?? '/products') }}"
                                           placeholder="/products"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                           required>
                                    <p class="text-sm text-gray-500 mt-1">Where the button links to</p>
                                </div>

                                <div>
                                    <label for="hero_products_button_theme" class="block text-sm font-medium text-gray-700 mb-2">
                                        Button Color
                                    </label>
                                    <select name="hero_products_button_theme"
                                            id="hero_products_button_theme"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                            required>
                                        @foreach($themeSettings as $themeSetting)
                                            <option value="{{ $themeSetting->key }}"
                                                {{ old('hero_products_button_theme', $homepageSettings->where('key', 'hero_products_button_theme')->first()->value ?? 'accent_color') == $themeSetting->key ? 'selected' : '' }}>
                                                {{ str_replace(' (', ' - ', rtrim($themeSetting->description, ')')) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-sm text-gray-500 mt-1">Theme color from Theme tab</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Save Homepage Settings
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Navigation Tab --}}
                <div x-show="activeTab === 'navigation'" x-cloak>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Navigation Settings</h2>
                    <p class="text-gray-600 mb-6">Control which navigation items appear in the header and customize their URLs.</p>

                    <form action="{{ route('admin.settings.update.navigation') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Products Navigation --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                <i class="fas fa-box mr-2 text-gray-500"></i>Products Navigation
                            </h3>

                            <div class="mb-4">
                                <label class="flex items-center gap-3">
                                    <input type="checkbox"
                                           name="nav_products_enabled"
                                           value="1"
                                           {{ \App\Models\Setting::get('navigation.nav_products_enabled', '1') == '1' ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-abs-primary focus:ring-abs-primary">
                                    <span class="text-sm font-medium text-gray-700">Show Products in navigation</span>
                                </label>
                                <p class="text-sm text-gray-500 mt-1 ml-8">When unchecked, Products will be hidden from the header navigation.</p>
                            </div>

                            <div>
                                <label for="nav_products_url_field" class="block text-sm font-medium text-gray-700 mb-2">
                                    Products URL
                                </label>
                                <input type="text"
                                       name="nav_products_url"
                                       id="nav_products_url_field"
                                       value="{{ old('nav_products_url', \App\Models\Setting::get('navigation.nav_products_url', '/products')) }}"
                                       placeholder="/products"
                                       class="block w-full max-w-md rounded-md border-gray-300 shadow-sm focus:border-abs-primary focus:ring-abs-primary"
                                       required>
                                <p class="text-sm text-gray-500 mt-1">Customize the URL (e.g., /products, /shop, /store)</p>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                <p class="text-sm text-blue-700">Changes take effect immediately on the header navigation for both desktop and mobile views.</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="btn-admin-primary">
                                <i class="fas fa-save mr-2"></i>Save Navigation Settings
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection
