
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Receipts</h2>
            <p class="text-muted mb-0">
                View receipts issued for payments on your children's invoices.
            </p>
        </div>

        <a href="{{ route('parent.fees.index') }}"
           class="btn btn-outline-primary">
            <i class="bi bi-receipt me-1"></i> View Invoices
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Issued Receipts</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Receipt Number</th>
                        <th>Child</th>
                        <th>Invoice</th>
                        <th>Payment Reference</th>
                        <th>Amount</th>
                        <th>Issued Date</th>
                        <th>Receipt File</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($receipts as $receipt)
                        <tr wire:key="receipt-{{ $receipt->id }}">
                            <td class="fw-semibold">
                                {{ $receipt->receipt_number }}
                            </td>

                            <td>
                                {{ $receipt->invoice?->student?->first_name }}
                                {{ $receipt->invoice?->student?->last_name }}
                            </td>

                            <td>
                                {{ $receipt->invoice?->invoice_number ?? '—' }}
                            </td>

                            <td>
                                {{ $receipt->payment?->reference ?? '—' }}
                            </td>

                            <td>
                                @if ($receipt->payment)
                                    {{ $receipt->payment->currency }}
                                    {{ number_format((float) $receipt->payment->amount, 2) }}
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                {{ $receipt->issued_at?->format('d M Y') ?? '—' }}
                            </td>

                            <td>
                                @if ($receipt->file_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($receipt->file_path) }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download me-1"></i> View receipt
                                    </a>
                                @else
                                    <span class="text-muted small">No file available</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-receipt fs-1 text-muted"></i>
                                <h6 class="fw-bold mt-3">No receipts available</h6>
                                <p class="text-muted mb-0">
                                    Issued receipts will appear here.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($receipts->hasPages())
            <div class="card-footer bg-white">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>
</div>
