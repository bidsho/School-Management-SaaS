
<div class="container-fluid py-4">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Fee Assignment</h3>
        <p class="text-muted mb-0">
            Select a class and academic period to generate student invoices.
        </p>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Invoice Generation</h5>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label fw-semibold">Academic Session</label>
                    <select class="form-select" wire:model.live="sessionId">
                        <option value="">Choose session</option>
                        @foreach ($sessions as $session)
                            <option value="{{ $session->id }}">
                                {{ $session->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('sessionId')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label fw-semibold">Term</label>
                    <select class="form-select" wire:model.live="termId">
                        <option value="">Choose term</option>
                        @foreach ($terms as $term)
                            <option value="{{ $term->id }}">
                                {{ $term->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('termId')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label fw-semibold">Class</label>
                    <select class="form-select" wire:model.live="classRoomId">
                        <option value="">All classes</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('classRoomId')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <label class="form-label fw-semibold">Eligible Students</label>
                    <div class="form-control bg-light">
                        {{ number_format($studentCount) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Applicable Fee Structures</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Fee</th>
                            <th>Description</th>
                            <th>Class</th>
                            <th>Currency</th>
                            <th class="text-end pe-3">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fees as $fee)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $fee->name }}</td>
                                <td>{{ $fee->description ?: '—' }}</td>
                                <td>{{ $fee->classRoom?->name ?? 'All classes' }}</td>
                                <td>{{ $fee->currency }}</td>
                                <td class="text-end pe-3">
                                    {{ number_format((float) $fee->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Choose a session and term to preview the fees.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($fees->isNotEmpty())
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total per student</th>
                                <th class="text-end pe-3">
                                    {{ $fees->first()->currency }}
                                    {{ number_format($feeTotal, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="bi bi-info-circle me-1"></i>
        Existing invoices will be skipped. Review the session, term, class, and fee total before generating invoices.
    </div>

    <button
        type="button"
        class="btn btn-primary px-4"
        wire:click="generateInvoices"
        wire:confirm="Generate invoices for eligible enrolled students? Existing invoices will not be changed."
        wire:loading.attr="disabled"
        @disabled($fees->isEmpty() || $studentCount === 0)
    >
        <span wire:loading.remove wire:target="generateInvoices">
            <i class="bi bi-file-earmark-plus me-1"></i>
            Generate Invoices
        </span>
        <span wire:loading wire:target="generateInvoices">
            Generating...
        </span>
    </button>
</div>
