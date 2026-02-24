<x-app-layout>
    @section('title', 'My Loyalty Points')
    @section('meta_description', 'View your loyalty points balance, earn rewards, and redeem discounts.')

    <section class="py-12 md:py-16" style="background-color: var(--surface);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="text-center mb-10" data-animate="fade-up">
                <p class="text-sm font-semibold uppercase tracking-wider text-gradient mb-3">Rewards Program</p>
                <h1 class="text-fluid-3xl font-display font-bold mb-3" style="color: var(--on-surface);">Your Loyalty Points</h1>
                <p class="text-lg" style="color: var(--on-surface-muted);">Earn points with every purchase, redeem for discounts</p>
            </div>

            {{-- Balance Card --}}
            <div class="card-glass rounded-2xl p-8 text-center mb-8" data-animate="fade-up">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-earth-amber/10 mb-4">
                    <i class="fas fa-coins text-earth-amber text-2xl"></i>
                </div>
                <p class="text-fluid-hero font-display font-bold text-gradient">{{ number_format($customer->loyalty_points_balance) }}</p>
                <p class="text-lg font-medium mt-1" style="color: var(--on-surface-muted);">points available</p>
                @if($customer->loyalty_points_balance >= 100)
                    <p class="text-sm mt-2 text-earth-success">
                        <i class="fas fa-tag mr-1"></i>Worth ${{ number_format($customer->loyalty_points_balance / 100, 2) }} in discounts
                    </p>
                @endif
            </div>

            {{-- Stats Row --}}
            <div class="grid grid-cols-2 gap-4 mb-8" data-animate="stagger">
                <div class="card-glass rounded-2xl p-6 text-center">
                    <p class="text-sm font-medium mb-1" style="color: var(--on-surface-muted);">Total Earned</p>
                    <p class="text-2xl font-display font-bold text-earth-success">{{ number_format($totalEarned) }}</p>
                    <p class="text-xs" style="color: var(--on-surface-muted);">points</p>
                </div>
                <div class="card-glass rounded-2xl p-6 text-center">
                    <p class="text-sm font-medium mb-1" style="color: var(--on-surface-muted);">Total Redeemed</p>
                    <p class="text-2xl font-display font-bold" style="color: var(--on-surface);">{{ number_format($totalRedeemed) }}</p>
                    <p class="text-xs" style="color: var(--on-surface-muted);">points</p>
                </div>
            </div>

            {{-- How It Works --}}
            <div class="card-glass rounded-2xl p-6 md:p-8 mb-8" data-animate="fade-up">
                <h2 class="text-xl font-display font-bold mb-4" style="color: var(--on-surface);">
                    <i class="fas fa-info-circle text-earth-green mr-2"></i>How It Works
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-earth-primary/10 mb-3">
                            <i class="fas fa-shopping-bag text-earth-primary"></i>
                        </div>
                        <h3 class="font-semibold mb-1" style="color: var(--on-surface);">Earn Points</h3>
                        <p class="text-sm" style="color: var(--on-surface-muted);">Earn 1 point for every $1 spent on orders</p>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-earth-success/10 mb-3">
                            <i class="fas fa-piggy-bank text-earth-success"></i>
                        </div>
                        <h3 class="font-semibold mb-1" style="color: var(--on-surface);">Collect Rewards</h3>
                        <p class="text-sm" style="color: var(--on-surface-muted);">100 points = $1.00 discount on your next order</p>
                    </div>
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-earth-amber/10 mb-3">
                            <i class="fas fa-tag text-earth-amber"></i>
                        </div>
                        <h3 class="font-semibold mb-1" style="color: var(--on-surface);">Redeem at Checkout</h3>
                        <p class="text-sm" style="color: var(--on-surface-muted);">Apply points at checkout for instant savings (up to 50% off)</p>
                    </div>
                </div>
            </div>

            {{-- Transaction History --}}
            <div class="card-glass rounded-2xl p-6 md:p-8" data-animate="fade-up">
                <h2 class="text-xl font-display font-bold mb-6" style="color: var(--on-surface);">Transaction History</h2>

                @if($transactions->count() > 0)
                    <div class="space-y-4">
                        @foreach($transactions as $transaction)
                            <div class="flex items-center justify-between py-3" style="border-bottom: 1px solid var(--glass-border);">
                                <div class="flex-1">
                                    <p class="font-medium text-sm" style="color: var(--on-surface);">{{ $transaction->description }}</p>
                                    <p class="text-xs mt-1" style="color: var(--on-surface-muted);">
                                        {{ $transaction->created_at->format('M d, Y g:i A') }}
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs ml-2
                                            {{ $transaction->type === 'earned' ? 'bg-earth-success/10 text-earth-success' : '' }}
                                            {{ $transaction->type === 'redeemed' ? 'bg-earth-primary/10 text-earth-primary' : '' }}
                                            {{ $transaction->type === 'adjusted' ? 'bg-earth-green/10 text-earth-green' : '' }}
                                            {{ $transaction->type === 'expired' ? 'bg-red-500/10 text-red-500' : '' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="font-bold text-sm {{ $transaction->points > 0 ? 'text-earth-success' : 'text-red-500' }}">
                                        {{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points) }}
                                    </p>
                                    <p class="text-xs" style="color: var(--on-surface-muted);">
                                        Bal: {{ number_format($transaction->balance_after) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-earth-amber/10 mb-4">
                            <i class="fas fa-coins text-earth-amber/40 text-2xl"></i>
                        </div>
                        <p class="font-medium mb-2" style="color: var(--on-surface);">No transactions yet</p>
                        <p class="text-sm mb-4" style="color: var(--on-surface-muted);">Start earning points by placing your first order!</p>
                        <a href="{{ route('products.index') }}" class="btn-gradient">Start Shopping</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
