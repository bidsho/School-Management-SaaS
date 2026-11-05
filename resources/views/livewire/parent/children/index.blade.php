
<div>
    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-2">My Children</h4>
            <p class="text-secondary mb-0">
                View your children's information and school records.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2">
                <i class="bi bi-people me-1"></i>
                {{ $students->total() }} {{ $students->total() === 1 ? 'Child' : 'Children' }}
            </span>
        </div>
    </div>

    {{-- Missing parent profile --}}
    @if (!$parentProfileExists)
        <div class="alert alert-warning rounded-4 border-0 shadow-sm p-4">
            <div class="d-flex gap-3 align-items-start">
                <i class="bi bi-exclamation-triangle fs-4"></i>
                <div>
                    <h6 class="fw-bold mb-1">Parent profile not found</h6>
                    <p class="mb-0">
                        Your login account does not have a parent profile linked to
                        your school yet. Please contact your school administrator.
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- Search --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-8">
                        <label for="childSearch" class="form-label fw-semibold">
                            Search children
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-secondary"></i>
                            </span>

                            <input
                                id="childSearch"
                                type="search"
                                class="form-control border-start-0 ps-1"
                                placeholder="Search by name or admission number..."
                                wire:model.live.debounce.300ms="search"
                            >
                        </div>
                    </div>

                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <button
                            type="button"
                            class="btn btn-light border w-100"
                            wire:click="$set('search', '')"
                        >
                            <i class="bi bi-arrow-counterclockwise me-1"></i>
                            Clear search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Children cards --}}
        <div wire:loading.class="opacity-50" wire:target="search, gotoPage, nextPage, previousPage">
            @if ($students->count())
                <div class="row g-4">
                    @foreach ($students as $student)
                        <div class="col-12 col-md-6 col-xl-4" wire:key="parent-child-{{ $student->id }}">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <div style="height: 5px; background: linear-gradient(90deg, #4f46e5, #818cf8);"></div>

                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <div
                                            class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                            style="width: 58px; height: 58px; background: #eeeeff; color: #4338ca; font-size: 21px; font-weight: 800;"
                                        >
                                            {{ strtoupper(substr($student->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr($student->last_name ?? '', 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <h5 class="fw-bold mb-1 text-break">
                                                {{ trim(($student->first_name ?? '') . ' ' . ($student->middle_name ?? '') . ' ' . ($student->last_name ?? '')) }}
                                            </h5>

                                            <div class="text-secondary small">
                                                {{ $student->admission_number ?: 'Admission number not assigned' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
                                            <span class="text-secondary">
                                                <i class="bi bi-person-vcard me-2"></i>Relationship
                                            </span>

                                            <span class="fw-semibold text-capitalize">
                                                {{ $student->pivot->relationship ?: 'Parent' }}
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
                                            <span class="text-secondary">
                                                <i class="bi bi-patch-check me-2"></i>Status
                                            </span>

                                            @php
                                                $studentStatus = strtolower((string) ($student->status ?? 'unknown'));

                                                $statusClass = match ($studentStatus) {
                                                    'active' => 'bg-success-subtle text-success',
                                                    'inactive', 'suspended' => 'bg-danger-subtle text-danger',
                                                    default => 'bg-secondary-subtle text-secondary',
                                                };
                                            @endphp

                                            <span class="badge rounded-pill {{ $statusClass }} text-capitalize">
                                                {{ $student->status ?: 'Not specified' }}
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <span class="text-secondary">
                                                <i class="bi bi-star me-2"></i>Primary contact
                                            </span>

                                            <span class="small fw-semibold">
                                                {{ $student->pivot->is_primary ? 'Yes' : 'No' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <div class="d-grid">
                                            
<a
    href="{{ route('parent.children.show', ['studentId' => $student->id]) }}"
    class="btn btn-outline-primary rounded-3"
>
    <i class="bi bi-eye me-1"></i>
    View Student Profile
</a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
                    <div class="text-secondary small">
                        Showing {{ $students->firstItem() }}–{{ $students->lastItem() }}
                        of {{ $students->total() }} children
                    </div>

                    <div>
                        {{ $students->links() }}
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-5 px-3">
                        <div
                            class="d-flex align-items-center justify-content-center rounded-circle mx-auto mb-3"
                            style="width: 80px; height: 80px; background: #eeeeff; color: #4f46e5;"
                        >
                            <i class="bi bi-people fs-1"></i>
                        </div>

                        @if (trim($search) !== '')
                            <h5 class="fw-bold">No matching children</h5>
                            <p class="text-secondary mb-3">
                                Try another name or admission number.
                            </p>

                            <button
                                type="button"
                                class="btn btn-primary rounded-3"
                                wire:click="$set('search', '')"
                            >
                                Clear search
                            </button>
                        @else
                            <h5 class="fw-bold">No children linked yet</h5>
                            <p class="text-secondary mb-0 mx-auto" style="max-width: 440px;">
                                Your children have not been linked to this parent
                                account. Contact your school administrator to link
                                their student records.
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
