<x-app-layout>
    @section('title', 'Thank You!')

    <section class="py-20 px-4" style="background-color: var(--surface);">
        <div class="max-w-lg mx-auto text-center">
            <div class="text-6xl mb-6">🎉</div>
            <h1 class="page-heading text-gradient-nature mb-4">Thank You!</h1>
            <p class="text-fluid-lg mb-2" style="color: var(--on-surface);">
                Your donation of <strong>${{ number_format($donation->amount, 2) }}</strong> has been received.
            </p>
            <p class="mb-8" style="color: var(--on-surface-muted);">
                A confirmation email with your tax receipt has been sent to {{ $donation->donor_email }}.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('progress.index') }}" class="btn-gradient">See Our Progress</a>
                <a href="{{ route('donate.wall') }}" class="btn-glass">View Donor Wall</a>
            </div>
        </div>
    </section>
</x-app-layout>
