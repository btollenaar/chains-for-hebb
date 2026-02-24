<footer class="relative mt-12" style="background: linear-gradient(to bottom, var(--footer-bg-start), var(--footer-bg-end));">
    {{-- Gradient accent strip --}}
    <div class="h-[2px]" style="background: var(--gradient-primary);"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Main Footer Content --}}
        <div class="py-12 md:py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
                {{-- Navigation --}}
                <div>
                    <h3 class="font-display font-bold text-white text-sm uppercase tracking-wider mb-4">Navigation</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-white transition-colors text-sm">Home</a></li>
                        @if($featureSettings['products'] ?? true)
                        <li><a href="{{ route('products.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm">Shop</a></li>
                        @endif
                        @if($featureSettings['donations'] ?? false)
                        <li><a href="{{ route('donate.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm">Donate</a></li>
                        @endif
                        @if($featureSettings['events'] ?? false)
                        <li><a href="{{ route('events.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm">Events</a></li>
                        @endif
                        @if($featureSettings['gallery'] ?? false)
                        <li><a href="{{ route('gallery.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm">Gallery</a></li>
                        @endif
                        @if($featureSettings['fundraising_tracker'] ?? false)
                        <li><a href="{{ route('progress.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm">Progress</a></li>
                        @endif
                        @if($featureSettings['sponsors'] ?? false)
                        <li><a href="{{ route('sponsors.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm">Sponsors</a></li>
                        @endif
                        @if($featureSettings['blog'] ?? true)
                        <li><a href="{{ route('blog.index') }}" class="text-slate-400 hover:text-white transition-colors text-sm">Blog</a></li>
                        @endif
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="font-display font-bold text-white text-sm uppercase tracking-wider mb-4">Contact</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="mailto:{{ $contactSettings['email'] }}" class="text-slate-400 hover:text-white transition-colors text-sm flex items-center gap-2.5">
                                <i class="far fa-envelope w-4 text-center text-earth-primary/70"></i>
                                <span>{{ $contactSettings['email'] }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="tel:{{ $contactSettings['phone'] }}" class="text-slate-400 hover:text-white transition-colors text-sm flex items-center gap-2.5">
                                <i class="fas fa-phone-alt w-4 text-center text-earth-primary/70"></i>
                                <span>{{ $contactSettings['phone'] }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Social --}}
                <div>
                    <h3 class="font-display font-bold text-white text-sm uppercase tracking-wider mb-4">Follow Us</h3>
                    <div class="flex gap-3">
                        @if($socialSettings['facebook_url'])
                        <a href="{{ $socialSettings['facebook_url'] }}" target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg bg-white/5 hover:bg-[#1877F2]/20 flex items-center justify-center transition-colors group">
                            <i class="fab fa-facebook-f text-slate-400 group-hover:text-[#1877F2] transition-colors"></i>
                            <span class="sr-only">Facebook</span>
                        </a>
                        @endif
                        @if($socialSettings['instagram_url'])
                        <a href="{{ $socialSettings['instagram_url'] }}" target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg bg-white/5 hover:bg-[#E4405F]/20 flex items-center justify-center transition-colors group">
                            <i class="fab fa-instagram text-slate-400 group-hover:text-[#E4405F] transition-colors"></i>
                            <span class="sr-only">Instagram</span>
                        </a>
                        @endif
                        @if($socialSettings['twitter_url'] ?? false)
                        <a href="{{ $socialSettings['twitter_url'] }}" target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/10 flex items-center justify-center transition-colors group">
                            <i class="fab fa-x-twitter text-slate-400 group-hover:text-white transition-colors"></i>
                            <span class="sr-only">X / Twitter</span>
                        </a>
                        @endif
                        @if($socialSettings['linkedin_url'] ?? false)
                        <a href="{{ $socialSettings['linkedin_url'] }}" target="_blank" rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg bg-white/5 hover:bg-[#0A66C2]/20 flex items-center justify-center transition-colors group">
                            <i class="fab fa-linkedin-in text-slate-400 group-hover:text-[#0A66C2] transition-colors"></i>
                            <span class="sr-only">LinkedIn</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Legal Links --}}
        <div class="border-t border-white/10 py-4 flex flex-wrap justify-center gap-4 md:gap-6">
            <a href="{{ route('legal.privacy-policy') }}" class="text-slate-500 hover:text-white transition-colors text-xs">Privacy Policy</a>
            <a href="{{ route('legal.terms-of-service') }}" class="text-slate-500 hover:text-white transition-colors text-xs">Terms of Service</a>
            <a href="{{ route('legal.return-policy') }}" class="text-slate-500 hover:text-white transition-colors text-xs">Return Policy</a>
            <a href="{{ route('legal.shipping-policy') }}" class="text-slate-500 hover:text-white transition-colors text-xs">Shipping Policy</a>
        </div>

        {{-- Bottom Footer --}}
        <div class="border-t border-white/10 py-6 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} {{ $profileSettings['name'] }}. All rights reserved.</p>
            <div class="flex items-center gap-1 text-slate-600 text-xs">
                <span>Built with</span>
                <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
    </div>
</footer>

{{-- Back to Top --}}
<x-back-to-top />
