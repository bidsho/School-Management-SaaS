
<div class="row g-4 mb-4">

    @php
        $cards = [
            [
                'label' => 'Total Schools',
                'value' => $stats['totalSchools'],
                'icon' => 'bi-buildings',
                'color' => 'primary',
                'description' => 'All registered schools',
            ],
            [
                'label' => 'Active Schools',
                'value' => $stats['activeSchools'],
                'icon' => 'bi-check-circle',
                'color' => 'success',
                'description' => 'Schools currently active',
            ],
            [
                'label' => 'Total Users',
                'value' => $stats['totalUsers'],
                'icon' => 'bi-people',
                'color' => 'info',
                'description' => 'All registered accounts',
            ],
            [
                'label' => 'Total Students',
                'value' => $stats['totalStudents'],
                'icon' => 'bi-mortarboard',
                'color' => 'warning',
                'description' => 'Students across the platform',
            ],
            [
                'label' => 'Active Subscriptions',
                'value' => $stats['activeSubscriptions'],
                'icon' => 'bi-credit-card',
                'color' => 'success',
                'description' => 'Subscriptions marked active',
            ],
        ];
    @endphp

    @foreach ($cards as $card)
        <div class="col-12 col-sm-6 col-xl-4 col-xxl">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <span class="text-secondary small fw-semibold">
                            {{ $card['label'] }}
                        </span>

                        <div
                            class="bg-{{ $card['color'] }} bg-opacity-10
                                   text-{{ $card['color'] }} rounded-3
                                   d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; flex-shrink: 0;"
                        >
                            <i class="bi {{ $card['icon'] }} fs-4"></i>
                        </div>
                    </div>

                    <h3 class="fw-bold mb-2">
                        {{ number_format($card['value']) }}
                    </h3>

                    <p class="text-secondary small mb-0">
                        {{ $card['description'] }}
                    </p>

                </div>
            </div>
        </div>
    @endforeach

</div>
