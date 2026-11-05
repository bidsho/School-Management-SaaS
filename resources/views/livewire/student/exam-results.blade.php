<div class="container-fluid py-4 exam-results-page">

    <style>
        .result-heading {
            background: linear-gradient(135deg, #172554, #1d4ed8);
            color: #fff;
            border-radius: 18px;
            padding: 28px;
        }

        .result-stat {
            border: 1px solid #e5e7eb;
            border-radius: 15px;
            background: #fff;
            padding: 20px;
            height: 100%;
        }

        .result-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 21px;
        }

        .result-stat-label {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .result-stat-value {
            font-size: 25px;
            font-weight: 700;
            color: #0f172a;
        }

        .result-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
        }

        .result-panel-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .result-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .4px;
            white-space: nowrap;
            padding: 15px;
        }

        .result-table td {
            padding: 15px;
            vertical-align: middle;
        }

        .grade-pill {
            display: inline-block;
            min-width: 38px;
            text-align: center;
            border-radius: 8px;
            padding: 5px 9px;
            font-weight: 700;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .student-report-header {
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 20px;
            margin-bottom: 22px;
        }

        .school-report-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            background: #fff;
            border-radius: 12px;
            padding: 5px;
        }

        /*
        |--------------------------------------------------------------------------
        | Print Watermark
        |--------------------------------------------------------------------------
        */

        .result-watermark {
            display: none;
        }

        @media print {

            @page {
                size: A4 portrait;
                margin: 12mm;
            }

            body {
                background: #fff !important;
                color: #111827 !important;
            }

            body * {
                visibility: hidden;
            }

            #student-result-print,
            #student-result-print * {
                visibility: visible;
            }

            #student-result-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                background: #fff !important;
                overflow: visible !important;
            }

            /*
             * School logo watermark.
             * Fixed positioning helps keep it centered on printed pages.
             */

            .result-watermark {
                display: block !important;
                position: fixed !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                width: 420px !important;
                height: 420px !important;
                object-fit: contain !important;
                opacity: .075 !important;
                z-index: 9999 !important;
                pointer-events: none !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print,
            .no-print *,
            nav,
            aside,
            header,
            footer {
                display: none !important;
            }

            .result-table th,
            .result-table td {
                padding: 8px !important;
                font-size: 10px !important;
                border: 1px solid #d1d5db !important;
            }

            .result-table th {
                background: #e5e7eb !important;
                color: #111827 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .result-stat,
            .result-panel {
                border: 1px solid #d1d5db !important;
                break-inside: avoid;
            }

            .result-heading {
                background: #fff !important;
                color: #111827 !important;
                border: 1px solid #d1d5db !important;
            }

            .result-heading h2,
            .result-heading p {
                color: #111827 !important;
            }

            .pagination {
                display: none !important;
            }

            a {
                color: inherit !important;
                text-decoration: none !important;
            }
        }
    </style>


    {{-- Page Heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 no-print">

        <div>

            <h3 class="fw-bold mb-1">
                My Exam Results
            </h3>

            <p class="text-secondary mb-0">
                View your academic performance and print your results.
            </p>

        </div>

        <button type="button"
                onclick="window.print()"
                class="btn btn-primary px-4 py-2">

            <i class="bi bi-printer me-2"></i>
            Print Results

        </button>

    </div>


    {{-- Printable Report --}}
    <div id="student-result-print">

        {{-- School Logo Watermark --}}
        @if ($schoolLogoExists)

            <img
                src="{{ $schoolLogoUrl }}"
                alt="School watermark"
                class="result-watermark"
            >

        @endif


        {{-- School Heading --}}
        <div class="result-heading mb-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div>

                    <div class="small mb-2">
                        {{ $school?->name ?? 'School Management System' }}
                    </div>

                    <h2 class="fw-bold mb-2">
                        Academic Report Card
                    </h2>

                    <p class="mb-0">
                        Student examination performance
                    </p>

                </div>


                {{-- School Logo --}}
                @if ($schoolLogoExists)

                    <img
                        src="{{ $schoolLogoUrl }}"
                        alt="{{ $school?->name ?? 'School' }} Logo"
                        class="school-report-logo"
                    >

                @else

                    <div class="text-center">
                        <i class="bi bi-mortarboard" style="font-size:48px;"></i>
                    </div>

                @endif

            </div>

        </div>


        {{-- Result Panel --}}
        <div class="result-panel mb-4">

            <div class="p-4">

                {{-- Student Information --}}
                <div class="student-report-header">

                    <h5 class="fw-bold mb-3">
                        Student Information
                    </h5>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <small class="text-secondary d-block">
                                Full Name
                            </small>

                            <strong>
                                {{ $student->user?->name ?? 'Student' }}
                            </strong>

                        </div>


                        <div class="col-md-6">

                            <small class="text-secondary d-block">
                                Admission Number
                            </small>

                            <strong>
                                {{ $student->admission_number ?? 'N/A' }}
                            </strong>

                        </div>


                        <div class="col-md-6">

                            <small class="text-secondary d-block">
                                Academic Session
                            </small>

                            <strong>

                                @if ($filterSession !== '')

                                    {{ $sessions->firstWhere('id', (int) $filterSession)?->name ?? 'Selected session' }}

                                @else

                                    All available sessions

                                @endif

                            </strong>

                        </div>


                        <div class="col-md-6">

                            <small class="text-secondary d-block">
                                Term
                            </small>

                            <strong>

                                @if ($filterTerm !== '')

                                    {{ $terms->firstWhere('id', (int) $filterTerm)?->name ?? 'Selected term' }}

                                @else

                                    All available terms

                                @endif

                            </strong>

                        </div>

                    </div>

                </div>


                {{-- Result Statistics --}}
                <div class="row g-3 mb-4">

                    <div class="col-6 col-lg-4">

                        <div class="result-stat">

                            <div class="result-stat-icon mb-3">
                                <i class="bi bi-journal-check"></i>
                            </div>

                            <div class="result-stat-label">
                                Subjects / Results
                            </div>

                            <div class="result-stat-value">
                                {{ $resultCount }}
                            </div>

                        </div>

                    </div>


                    <div class="col-6 col-lg-4">

                        <div class="result-stat">

                            <div class="result-stat-icon mb-3">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>

                            <div class="result-stat-label">
                                Average Total Score
                            </div>

                            <div class="result-stat-value">
                                {{ number_format($averageScore, 2) }}
                            </div>

                        </div>

                    </div>


                    <div class="col-12 col-lg-4">

                        <div class="result-stat">

                            <div class="result-stat-icon mb-3">
                                <i class="bi bi-trophy"></i>
                            </div>

                            <div class="result-stat-label">
                                Highest Total Score
                            </div>

                            <div class="result-stat-value">
                                {{ number_format($highestScore, 2) }}
                            </div>

                        </div>

                    </div>

                </div>


                {{-- Filters --}}
                <div class="no-print row g-3 align-items-end mb-4">

                    <div class="col-md-5">

                        <label class="form-label fw-semibold">
                            Academic Session
                        </label>

                        <select class="form-select"
                                wire:model.live="filterSession">

                            <option value="">
                                All sessions
                            </option>

                            @foreach ($sessions as $session)

                                <option value="{{ $session->id }}">
                                    {{ $session->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-5">

                        <label class="form-label fw-semibold">
                            Term
                        </label>

                        <select class="form-select"
                                wire:model.live="filterTerm">

                            <option value="">
                                All terms
                            </option>

                            @foreach ($terms as $term)

                                <option value="{{ $term->id }}">
                                    {{ $term->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="col-md-2">

                        <button type="button"
                                class="btn btn-outline-secondary w-100"
                                wire:click="$set('filterSession', '')"
                                wire:loading.attr="disabled">

                            Reset

                        </button>

                    </div>

                </div>


                {{-- Subject Results --}}
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="fw-bold mb-0">
                        Subject Results
                    </h5>

                    <span class="text-secondary small">
                        {{ $resultCount }} result(s)
                    </span>

                </div>


                {{-- Results Table --}}
                <div class="table-responsive">

                    <table class="table result-table align-middle mb-0">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Examination</th>
                                <th>Session / Term</th>
                                <th>CA</th>
                                <th>Exam</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Remark</th>
                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($results as $result)

                                <tr>

                                    <td>
                                        {{ $results->firstItem() + $loop->index }}
                                    </td>


                                    <td class="fw-semibold">

                                        {{ $result->subject?->name
                                            ?? $result->exam?->subject?->name
                                            ?? 'N/A' }}

                                    </td>


                                    <td>

                                        <div class="fw-semibold">
                                            {{ $result->exam?->name ?? 'Examination' }}
                                        </div>

                                        <small class="text-secondary">

                                            {{ $result->exam?->exam_date?->format('d M Y') ?? '—' }}

                                        </small>

                                    </td>


                                    <td>

                                        <div>
                                            {{ $result->exam?->academicSession?->name ?? '—' }}
                                        </div>

                                        <small class="text-secondary">
                                            {{ $result->exam?->term?->name ?? '—' }}
                                        </small>

                                    </td>


                                    <td>
                                        {{ number_format((float) $result->ca_score, 2) }}
                                    </td>


                                    <td>
                                        {{ number_format((float) $result->exam_score, 2) }}
                                    </td>


                                    <td class="fw-bold">
                                        {{ number_format((float) $result->total_score, 2) }}
                                    </td>


                                    <td>

                                        <span class="grade-pill">
                                            {{ $result->grade ?: '—' }}
                                        </span>

                                    </td>


                                    <td>
                                        {{ $result->remark ?: '—' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="9" class="text-center py-5">

                                        <i class="bi bi-journal-x text-secondary"
                                           style="font-size:36px;"></i>

                                        <h6 class="fw-bold mt-3">
                                            No exam results available
                                        </h6>

                                        <p class="text-secondary mb-0">
                                            Your results will appear here when they have been entered.
                                        </p>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                <div class="no-print mt-4">
                    {{ $results->links() }}
                </div>


                {{-- Signatures --}}
                <div class="mt-5 pt-3 border-top">

                    <div class="row g-4">

                        <div class="col-6">

                            <div style="border-top:1px solid #9ca3af;padding-top:8px;width:80%;">

                                Class Teacher's Signature

                            </div>

                        </div>


                        <div class="col-6 text-end">

                            <div style="border-top:1px solid #9ca3af;padding-top:8px;width:80%;margin-left:auto;">

                                Principal's Signature

                            </div>

                        </div>

                    </div>


                    <p class="text-center text-secondary small mt-4 mb-0">

                        Generated from the school management system.

                    </p>

                </div>

            </div>

        </div>

    </div>

</div>