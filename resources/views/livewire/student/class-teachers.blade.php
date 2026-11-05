
<div class="container-fluid py-4">

    <style>
        .class-hero {
            background: linear-gradient(135deg, #172554, #2563eb);
            color: #fff;
            padding: 28px;
            border-radius: 18px;
        }

        .class-info-card,
        .teacher-card,
        .subject-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            height: 100%;
        }

        .class-info-card {
            padding: 20px;
        }

        .class-icon {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 22px;
            flex-shrink: 0;
        }

        .teacher-card {
            padding: 22px;
            transition: box-shadow .2s ease, transform .2s ease;
        }

        .teacher-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(15, 23, 42, .07);
        }

        .teacher-avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 22px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .subject-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .subject-row:last-child {
            border-bottom: 0;
        }

        .subject-code {
            background: #f1f5f9;
            color: #475569;
            border-radius: 7px;
            padding: 5px 9px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        @media print {
            @page {
                size: A4;
                margin: 12mm;
            }

            body * {
                visibility: hidden;
            }

            #class-teachers-print,
            #class-teachers-print * {
                visibility: visible;
            }

            #class-teachers-print {
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

            .class-hero {
                background: #fff !important;
                color: #111827 !important;
                border: 1px solid #d1d5db;
            }

            .class-hero h2,
            .class-hero p,
            .class-hero strong,
            .class-hero .class-hero-text {
                color: #111827 !important;
            }

            .teacher-card,
            .class-info-card,
            .subject-panel {
                border: 1px solid #d1d5db !important;
                box-shadow: none !important;
                break-inside: avoid;
            }

            .teacher-card:hover {
                transform: none;
            }

            .row {
                break-inside: auto;
            }

            .teacher-card {
                margin-bottom: 10px;
            }
        }
    </style>

    {{-- Page heading --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 no-print">
        <div>
            <h3 class="fw-bold mb-1">My Class &amp; Teachers</h3>
            <p class="text-secondary mb-0">
                Meet your teachers and view your current class information.
            </p>
        </div>

        <button
            type="button"
            onclick="window.print()"
            class="btn btn-primary px-4"
        >
            <i class="bi bi-printer me-2"></i>
            Print Information
        </button>
    </div>

    <div id="class-teachers-print">

        {{-- Class banner --}}
        <div class="class-hero mb-4">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <i class="bi bi-mortarboard" style="font-size:46px;"></i>
                </div>

                <div>
                    <p class="mb-1 class-hero-text">
                        {{ $enrollment?->classRoom?->name ?? 'Student Class Portal' }}
                    </p>

                    <h2 class="fw-bold mb-1 class-hero-text">
                        My Class &amp; Teachers
                    </h2>

                    <p class="mb-0 class-hero-text">
                        {{ $enrollment?->academicSession?->name ?? 'Academic session not assigned' }}

                        @if ($enrollment?->term)
                            · {{ $enrollment->term->name }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        @if (!$enrollment)

            <div class="alert alert-warning">
                <i class="bi bi-info-circle me-2"></i>
                No active class enrollment was found. Please contact your school administrator.
            </div>

        @else

            {{-- Class information cards --}}
            <div class="row g-3 mb-4">

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="class-info-card">
                        <div class="class-icon mb-3">
                            <i class="bi bi-building"></i>
                        </div>

                        <div class="text-secondary small mb-1">My Class</div>

                        <h5 class="fw-bold mb-0">
                            {{ $enrollment->classRoom?->name ?? 'Not assigned' }}
                        </h5>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="class-info-card">
                        <div class="class-icon mb-3">
                            <i class="bi bi-person-vcard"></i>
                        </div>

                        <div class="text-secondary small mb-1">Admission Number</div>

                        <h5 class="fw-bold mb-0">
                            {{ $student->admission_number ?? 'N/A' }}
                        </h5>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="class-info-card">
                        <div class="class-icon mb-3">
                            <i class="bi bi-people"></i>
                        </div>

                        <div class="text-secondary small mb-1">Assigned Teachers</div>

                        <h5 class="fw-bold mb-0">
                            {{ $teachers->count() }}
                        </h5>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="class-info-card">
                        <div class="class-icon mb-3">
                            <i class="bi bi-journal-bookmark"></i>
                        </div>

                        <div class="text-secondary small mb-1">Assigned Subjects</div>

                        <h5 class="fw-bold mb-0">
                            {{ $subjects->count() }}
                        </h5>
                    </div>
                </div>

            </div>

            {{-- Teachers section --}}
            <div class="mb-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">My Teachers</h5>
                        <p class="text-secondary small mb-0">
                            Teachers assigned to your class and subjects.
                        </p>
                    </div>
                </div>

                @if ($teachers->isEmpty())

                    <div class="teacher-card text-center py-5">
                        <i
                            class="bi bi-person-exclamation text-secondary"
                            style="font-size:42px;"
                        ></i>

                        <h6 class="fw-bold mt-3">
                            No teacher assignments found
                        </h6>

                        <p class="text-secondary mb-0">
                            Your school has not assigned teachers to your class for this term yet.
                        </p>
                    </div>

                @else

                    <div class="row g-3">

                        @foreach ($teachers as $teacherAssignment)

                            @php
                                $teacher = $teacherAssignment->teacher;

                                // Use the actual name columns in the teachers table.
                                $teacherName = trim(implode(' ', array_filter([
                                    $teacher?->first_name,
                                    $teacher?->middle_name,
                                    $teacher?->last_name,
                                ])));

                                // Fall back to the linked user account.
                                if ($teacherName === '') {
                                    $teacherName = $teacher?->user?->name
                                        ?? 'Name unavailable';
                                }

                                $initials = collect(explode(' ', $teacherName))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                    ->implode('');

                                // Show every subject taught by this teacher in this class.
                                $teacherSubjects = $assignments
                                    ->where('teacher_id', $teacherAssignment->teacher_id)
                                    ->filter(fn ($item) => $item->subject !== null)
                                    ->unique('subject_id')
                                    ->pluck('subject.name')
                                    ->filter()
                                    ->values();
                            @endphp

                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="teacher-card">

                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="teacher-avatar">
                                            {{ $initials ?: 'T' }}
                                        </div>

                                        <div class="min-w-0">
                                            <h6 class="fw-bold mb-1">
                                                {{ $teacherName }}
                                            </h6>

                                            <span class="badge text-bg-primary">
                                                Subject Teacher
                                            </span>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3">

                                        <div class="text-secondary small mb-1">
                                            Assigned Subject{{ $teacherSubjects->count() === 1 ? '' : 's' }}
                                        </div>

                                        @forelse ($teacherSubjects as $subjectName)
                                            <div class="fw-semibold mb-1">
                                                {{ $subjectName }}
                                            </div>
                                        @empty
                                            <div class="fw-semibold">
                                                Subject not specified
                                            </div>
                                        @endforelse

                                        @if ($teacherAssignment->section)
                                            <div class="text-secondary small mt-3 mb-1">
                                                Section
                                            </div>

                                            <div>
                                                {{ $teacherAssignment->section->name }}
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>

                        @endforeach

                    </div>

                @endif
            </div>

            {{-- Subjects section --}}
            <div class="subject-panel mb-4">

                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-1">My Subjects</h5>

                    <p class="text-secondary small mb-0">
                        Subjects assigned to your class for the current term.
                    </p>
                </div>

                @forelse ($subjects as $subjectAssignment)

                    @php
                        $subjectTeacher = $assignments
                            ->first(function ($item) use ($subjectAssignment) {
                                return (string) $item->subject_id
                                    === (string) $subjectAssignment->subject_id
                                    && $item->teacher !== null;
                            });

                        $subjectTeacherRecord = $subjectTeacher?->teacher;

                        $subjectTeacherName = trim(implode(' ', array_filter([
                            $subjectTeacherRecord?->first_name,
                            $subjectTeacherRecord?->middle_name,
                            $subjectTeacherRecord?->last_name,
                        ])));

                        if ($subjectTeacherName === '') {
                            $subjectTeacherName = $subjectTeacherRecord?->user?->name
                                ?? 'Not assigned';
                        }
                    @endphp

                    <div class="subject-row">

                        <div class="d-flex align-items-center gap-3">
                            <div class="class-icon">
                                <i class="bi bi-book"></i>
                            </div>

                            <div>
                                <div class="fw-semibold">
                                    {{ $subjectAssignment->subject->name }}
                                </div>

                                <div class="text-secondary small">
                                    Teacher: {{ $subjectTeacherName }}
                                </div>
                            </div>
                        </div>

                        <span class="subject-code">
                            {{ $subjectAssignment->subject->code ?: 'SUBJECT' }}
                        </span>

                    </div>

                @empty

                    <div class="p-4 text-center text-secondary">
                        No subjects have been assigned yet.
                    </div>

                @endforelse

            </div>

            {{-- Academic information --}}
            <div class="text-secondary small">
                <i class="bi bi-calendar3 me-1"></i>

                Academic Session:
                {{ $enrollment->academicSession?->name ?? 'N/A' }}

                <span class="mx-2">|</span>

                Term:
                {{ $enrollment->term?->name ?? 'N/A' }}
            </div>

        @endif

        <div class="text-center text-secondary small mt-4">
            Generated from the School Management System.
        </div>

    </div>
</div>
