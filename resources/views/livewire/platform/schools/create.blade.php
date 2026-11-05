
<x-layouts.super title="Create School">
    <div class="container-fluid px-0">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('platform.schools.index') }}"
               class="btn btn-light border rounded-3">
                <i class="bi bi-arrow-left"></i>
            </a>

            <div>
                <h2 class="h4 fw-bold mb-1">Register a School</h2>
                <p class="text-secondary mb-0">
                    Create a school and its first administrator.
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-xl-10">
                <livewire:platform.schools.school-create />
            </div>
        </div>

    </div>
</x-layouts.super>
