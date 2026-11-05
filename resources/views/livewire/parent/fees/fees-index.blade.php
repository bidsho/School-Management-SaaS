
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1">School Fees</h2>
            <p class="text-muted mb-0">
                View your children's invoices and outstanding school fees.
            </p>
        </div>

        <a href="{{ route('parent.children.index') }}"
           class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-arrow-left me-1"></i>
            My Children
        </a>
    </div>

    @if ($children->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-people fs-1 text-muted"></i>
                <h5 class="mt-3">No linked students</h5>
                <p class="text-muted mb-0">
                    Contact your school administrator to link your child to your account.
                </p>
            </div>
        </div>
    @else

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <label for="studentId" class="form-label fw-semibold">
                    Filter by Child
                </label>

                <select id="studentId"
                        class="form-select rounded-3"
                        wire:model.live="studentId">
                    <option value="all">All my children</option>

                    @foreach ($children as $child)
                        <option value="{{ $child->id }}">
                            {{ $child->first_name }}
                            {{ $child->middle_name }}
                            {{ $child->last_name }}
                            — {{ $child->admission_number }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mb-4">

            @foreach ([
                ['label' => 'Invoices', 'value' => $summary['count'], 'icon' => 'receipt', 'color' => 'primary', 'money' => false],
                ['label' => 'Total Fees', 'value' => $summary['amount_due'], 'icon' => 'wallet2', 'color' => 'info', 'money' => true],
                ['label' => 'Amount Paid', 'value' => $summary['amount_paid'], 'icon' => 'check-circle', 'color' => 'success', 'money' => true],
                ['label' => 'Outstanding Balance', 'value' => $summary['balance'], 'icon' => 'exclamation-circle', 'color' => 'danger', 'money' => true],
            ] as $card)
                <div class="col-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted small">{{ $card['label'] }}</span>
                                <i class="bi bi-{{ $card['icon'] }} fs-5 text-{{ $card['color'] }}"></i>
                            </div>

                            <h4 class="fw-bold mb-0">
                                @if ($card['money'])
                                    {{ number_format((float) $card['value'], 2) }}
                                @else
                                    {{ $card['value'] }}
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">

                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-1">Invoice History</h5>
                    <p class="text-muted small mb-0">
                        Your school's recorded invoices and balances.
                    </p>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Invoice</th>
                                <th class="py-3">Student</th>
                                <th class="py-3">Term</th>
                                <th class="py-3">Issued</th>
                                <th class="py-3">Due Date</th>
                                <th class="py-3">Amount Due</th>
                                <th class="py-3">Paid</th>
                                <th class="py-3">Balance</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">View Details</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($invoices as $invoice)
                                @php
                                    $status = strtolower((string) $invoice->status);

                                    $statusClass = match ($status) {
                                        'paid' => 'success',
                                        'pending', 'partial', 'partially_paid' => 'warning',
                                        'overdue', 'unpaid' => 'danger',
                                        'cancelled', 'canceled' => 'secondary',
                                        default => 'info',
                                    };
                                @endphp

                                <tr wire:key="invoice-{{ $invoice->id }}">
                                    <td class="px-4 py-3 fw-semibold">
                                        {{ $invoice->invoice_number }}
                                    </td>

                                    <td>
                                        {{ $invoice->student?->first_name }}
                                        {{ $invoice->student?->last_name }}
                                    </td>

                                    <td>
                                        {{ $invoice->term?->name ?? '—' }}
                                        @if ($invoice->academicSession?->name)
                                            <div class="text-muted small">
                                                {{ $invoice->academicSession->name }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $invoice->issued_at?->format('d M Y') ?? '—' }}
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $invoice->due_at?->format('d M Y') ?? '—' }}
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $invoice->currency }}
                                        {{ number_format((float) $invoice->amount_due, 2) }}
                                    </td>

                                    <td class="text-nowrap text-success">
                                        {{ $invoice->currency }}
                                        {{ number_format((float) $invoice->amount_paid, 2) }}
                                    </td>

                                    <td class="text-nowrap fw-semibold">
                                        {{ $invoice->currency }}
                                        {{ number_format((float) $invoice->balance, 2) }}
                                    </td>

                                    <td>
                                        <span class="badge rounded-pill text-bg-{{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $status ?: 'unknown')) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                    <a href="{{ route('parent.fees.show', $invoice->id) }}"
                                    class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye me-1"></i>
                                        View Details / Pay
                                    </a>
                                </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="bi bi-receipt fs-1 text-muted"></i>
                                        <h6 class="mt-3">No invoices found</h6>
                                        <p class="text-muted small mb-0">
                                            Invoices will appear here when your school issues them.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($invoices->hasPages())
                    <div class="p-3">
                        {{ $invoices->links() }}
                    </div>
                @endif

            </div>
        </div>

        <div class="alert alert-light border rounded-3 small mt-4">
            <i class="bi bi-shield-check me-1"></i>
            Invoice balances are shown from your school's records. Contact the school
            accountant if you notice an incorrect balance.
        </div>

    @endif
</div>
