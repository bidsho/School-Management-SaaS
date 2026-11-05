<x-layouts.super title="Platform Dashboard">
    <div class="mb-4">
        <h2 class="h4 fw-bold mb-2">Welcome to your platform</h2>
        <p class="text-secondary mb-0">
            Manage schools, subscriptions and platform activity from one place.
        </p>
    </div>

    {{-- Platform dashboard components --}}
    <livewire:platform.dashboard.overview-stats />
    <livewire:platform.dashboard.subscription-stats />
    <livewire:platform.dashboard.recent-schools />
    <livewire:platform.dashboard.recent-activity />
</x-layouts.super>