
<x-layouts.super title="School Management">
    <div class="container-fluid px-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="h4 fw-bold mb-1">School Management</h2>
                <p class="text-secondary mb-0">
                    Manage schools registered on your platform.
                </p>
            </div>

            <a href="{{ route('platform.schools.create') }}"
               class="btn btn-primary rounded-3 px-4">
                <i class="bi bi-plus-lg me-2"></i>
                Add New School
            </a>
        </div>

        <livewire:platform.schools.school-index />
    </div>
</x-layouts.super>
