
<div class="container-fluid py-4">

    <style>
        .timetable-hero {
            background: linear-gradient(135deg, #172554, #2563eb);
            color: white;
            border-radius: 18px;
            padding: 28px;
        }

        .timetable-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
        }

        .timetable-day {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            padding: 15px 20px;
            font-weight: 700;
            color: #1e293b;
        }

        .lesson-card {
            border: 1px solid #dbeafe;
            background: #eff6ff;
            border-radius: 12px;
            padding: 15px;
            height: 100%;
        }

        .lesson-time {
            font-size: 12px;
            font-weight: 700;
            color: #1d4ed8;
            margin-bottom: 8px;
        }

        .lesson-subject {
            color: #172554;
            font-weight: 700;
            font-size: 15px;
        }

        .lesson-detail {
            font-size: 12px;
            color: #64748b;
            margin-top: 7px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm;
            }

            body * {
                visibility: hidden;
            }

            #print-timetable,
            #print-timetable * {
                visibility: visible;
            }

            #print-timetable {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .no-print,
            nav,
            aside,
            header,
            footer {
                display: none !important;
            }

            .timetable-hero {
                background: white !important;
                color: #111827 !important;
                border: 1px solid #d1d5db;
            }

            .timetable-hero h2,
            .timetable-hero p {
                color: #111827 !important;
            }

            .lesson-card {
                background: white !important;
                border: 1px solid #d1d5db !important;
                break-inside: avoid;
            }

            .lesson-subject,
            .lesson-time,
            .lesson-detail {
                color: #111827 !important;
            }

            .timetable-day {
                background: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 no-print">
        <div>
            <h3 class="fw-bold mb-1">My Class Timetable</h3>
            <p class="text-secondary mb-0">
                Your weekly lessons, subjects and classroom schedule.
            </p>
        </div>

        <button type="button" onclick="window.print()" class="btn btn-primary px-4">
            <i class="bi bi-printer me-2"></i> Print Timetable
        </button>
    </div>

    <div id="print-timetable">

        <div class="timetable-hero mb-4">
            <h2 class="fw-bold mb-2">Weekly Class Timetable</h2>
            <p class="mb-0">
                {{ $enrollment?->classRoom?->name ?? 'Class not assigned' }}
                @if($enrollment?->section)
                    · {{ $enrollment->section->name }}
                @endif
            </p>

            <div class="mt-3 small">
                Academic Session:
                <strong>{{ $enrollment?->academicSession?->name ?? 'N/A' }}</strong>
                <span class="mx-2">|</span>
                Term:
                <strong>{{ $enrollment?->term?->name ?? 'N/A' }}</strong>
            </div>

            <div class="small mt-2">
                Student:
                <strong>{{ $student->user?->name ?? 'Student' }}</strong>
                <span class="mx-2">|</span>
                Admission No:
                <strong>{{ $student->admission_number ?? 'N/A' }}</strong>
            </div>
        </div>

        @if(!$enrollment)
            <div class="alert alert-warning">
                <i class="bi bi-info-circle me-2"></i>
                No active class enrollment was found. Please contact your school administrator.
            </div>
        @elseif($timetable->isEmpty())
            <div class="timetable-card p-5 text-center">
                <i class="bi bi-calendar-x text-secondary" style="font-size:48px;"></i>
                <h5 class="fw-bold mt-3">Timetable not available yet</h5>
                <p class="text-secondary mb-0">
                    Your school has not added timetable entries for your current class, session and term.
                </p>
            </div>
        @else
            @foreach($days as $day)
                @php
                    $lessons = $timetable->get($day, collect());

                    // Also support numeric weekdays if the database stores them.
                    if ($lessons->isEmpty()) {
                        $dayNumber = array_search($day, $days) + 1;
                        $lessons = $timetable->get($dayNumber, collect());
                    }
                @endphp

                @if($lessons->isNotEmpty())
                    <div class="timetable-card mb-4">
                        <div class="timetable-day">
                            <i class="bi bi-calendar-week me-2"></i>{{ $day }}
                        </div>

                        <div class="p-3">
                            <div class="row g-3">
                                @foreach($lessons as $lesson)
                                    <div class="col-12 col-md-6 col-xl-4">
                                        <div class="lesson-card">
                                            <div class="lesson-time">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ \Illuminate\Support\Carbon::parse($lesson->starts_at)->format('g:i A') }}
                                                –
                                                {{ \Illuminate\Support\Carbon::parse($lesson->ends_at)->format('g:i A') }}
                                            </div>

                                            <div class="lesson-subject">
                                                {{ $lesson->subject?->name ?? 'Subject not specified' }}
                                            </div>

                                            <div class="lesson-detail">
                                                <i class="bi bi-person me-1"></i>
                                                Teacher:
                                                {{ $lesson->teacher?->user?->name
                                                    ?? $lesson->teacher?->name
                                                    ?? 'Not assigned' }}
                                            </div>

                                            <div class="lesson-detail">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                Room: {{ $lesson->room ?: 'Not specified' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

        <div class="text-center text-secondary small mt-4">
            Timetable generated by the School Management System.
        </div>
    </div>
</div>
