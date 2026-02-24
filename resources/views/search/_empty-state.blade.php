<div class="text-center py-16" data-animate="fade-up">
    <div class="w-20 h-20 rounded-2xl bg-earth-primary/10 flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-search text-3xl text-earth-primary/50"></i>
    </div>
    <p class="text-lg font-display font-semibold mb-2" style="color: var(--on-surface);">No results found</p>
    <p class="mb-6 max-w-md mx-auto" style="color: var(--on-surface-muted);">
        We couldn't find anything matching "<strong>{{ $query }}</strong>". Try a different search term or browse our catalog.
    </p>
    <div class="flex flex-wrap gap-3 justify-center">
        <a href="{{ route('products.index') }}" class="btn-gradient">
            <i class="fas fa-box mr-2"></i>Browse Products
        </a>
    </div>
</div>
