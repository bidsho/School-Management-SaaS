
<x-layouts.super title="Edit School">

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Edit School</h3>
                <p class="text-muted mb-0">
                    Manage {{ $school->name }}.
                </p>
            </div>

            <a href="{{ route('platform.schools.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Schools
            </a>
        </div>

        <livewire:platform.schools.school-edit :school="$school" />
    </div>

</x-layouts.super>
