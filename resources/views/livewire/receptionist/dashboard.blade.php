
<div class="container-fluid px-0">

    {{-- Welcome section --}}
    <div class="card border-0 shadow-sm mb-4 overflow-hidden"
         style="border-radius: 16px;">

        <div class="card-body p-4 p-lg-5"
             style="background: linear-gradient(120deg, #1d4ed8, #2563eb, #3b82f6);">

            <div class="row align-items-center">

                <div class="col-lg-8">

                    <span class="badge bg-white text-primary mb-3 px-3 py-2">
                        <i class="bi bi-person-badge me-1"></i>
                        Receptionist Portal
                    </span>

                    <h3 class="text-white fw-bold mb-2">
                        Welcome back, {{ $receptionist->first_name }}!
                    </h3>

                    <p class="text-white mb-3 opacity-75">
                        Welcome to your school's front office management dashboard.
                        Have a productive day.
                    </p>

                    <div class="d-flex flex-wrap gap-3 text-white small">

                        <span>
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $today }}
                        </span>

                        <span>
                            <i class="bi bi-building me-1"></i>
                            {{ $school?->name ?? 'School' }}
                        </span>

                    </div>

                </div>

                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25"
                         style="width: 100px; height: 100px;">

                        <i class="bi bi-person-workspace text-white"
                           style="font-size: 55px;"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Statistics --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100"
                 style="border-radius: 14px;">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted small mb-2">
                                Staff Number
                            </p>

                            <h5 class="fw-bold mb-0">
                                {{ $receptionist->staff_number ?: 'Not assigned' }}
                            </h5>
                        </div>

                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: #eff6ff; color: #2563eb;">

                            <i class="bi bi-person-vcard fs-4"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100"
                 style="border-radius: 14px;">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted small mb-2">
                                Employment Status
                            </p>

                            <h5 class="fw-bold mb-0 text-capitalize">
                                {{ $receptionist->employment_status ?: 'Not specified' }}
                            </h5>
                        </div>

                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: #ecfdf5; color: #059669;">

                            <i class="bi bi-person-check fs-4"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100"
                 style="border-radius: 14px;">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted small mb-2">
                                Department
                            </p>

                            <h5 class="fw-bold mb-0">
                                Front Office
                            </h5>
                        </div>

                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: #fff7ed; color: #ea580c;">

                            <i class="bi bi-building fs-4"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100"
                 style="border-radius: 14px;">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <p class="text-muted small mb-2">
                                Date Hired
                            </p>

                            <h5 class="fw-bold mb-0">
                                {{ $receptionist->hired_at?->format('d M Y') ?? 'Not specified' }}
                            </h5>
                        </div>

                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: #f5f3ff; color: #7c3aed;">

                            <i class="bi bi-calendar-check fs-4"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Main dashboard content --}}
    <div class="row g-4">

        {{-- Profile --}}
        <div class="col-xl-7">

            <div class="card border-0 shadow-sm h-100"
                 style="border-radius: 14px;">

                <div class="card-header bg-white border-0 p-4">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <h5 class="fw-bold mb-1">
                                My Profile
                            </h5>

                            <p class="text-muted small mb-0">
                                Your personal and employment information
                            </p>
                        </div>

                        <i class="bi bi-person-circle text-primary fs-3"></i>

                    </div>

                </div>


                <div class="card-body px-4 pb-4">

                    <div class="d-flex align-items-center gap-3 mb-4">

                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 65px; height: 65px; background: #dbeafe; color: #1d4ed8;">

                            <i class="bi bi-person-fill fs-2"></i>

                        </div>

                        <div>

                            <h5 class="fw-bold mb-1">
                                {{ $receptionist->full_name }}
                            </h5>

                            <span class="text-muted small">
                                Receptionist
                            </span>

                        </div>

                    </div>


                    <hr class="border-light">


                    <div class="row g-4">

                        <div class="col-md-6">

                            <label class="text-muted small mb-1">
                                First Name
                            </label>

                            <div class="fw-semibold">
                                {{ $receptionist->first_name ?: 'N/A' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small mb-1">
                                Last Name
                            </label>

                            <div class="fw-semibold">
                                {{ $receptionist->last_name ?: 'N/A' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small mb-1">
                                Middle Name
                            </label>

                            <div class="fw-semibold">
                                {{ $receptionist->middle_name ?: 'N/A' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small mb-1">
                                Phone Number
                            </label>

                            <div class="fw-semibold">
                                {{ $receptionist->phone ?: 'N/A' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small mb-1">
                                Qualification
                            </label>

                            <div class="fw-semibold">
                                {{ $receptionist->qualification ?: 'N/A' }}
                            </div>

                        </div>


                        <div class="col-md-6">

                            <label class="text-muted small mb-1">
                                Employment Status
                            </label>

                            <div class="fw-semibold text-capitalize">
                                {{ $receptionist->employment_status ?: 'N/A' }}
                            </div>

                        </div>


                        <div class="col-12">

                            <label class="text-muted small mb-1">
                                Residential Address
                            </label>

                            <div class="fw-semibold">
                                {{ $receptionist->address ?: 'N/A' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- School information --}}
        <div class="col-xl-5">

            <div class="card border-0 shadow-sm mb-4"
                 style="border-radius: 14px;">

                <div class="card-header bg-white border-0 p-4">

                    <h5 class="fw-bold mb-1">
                        School Information
                    </h5>

                    <p class="text-muted small mb-0">
                        Your assigned school
                    </p>

                </div>


                <div class="card-body px-4 pb-4">

                    <div class="d-flex align-items-center gap-3 mb-3">

                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width: 55px; height: 55px; background: #eff6ff; color: #2563eb;">

                            <i class="bi bi-buildings fs-3"></i>

                        </div>

                        <div>
                            <h6 class="fw-bold mb-1">
                                {{ $school?->name ?? 'School not available' }}
                            </h6>

                            <small class="text-muted">
                                Assigned Institution
                            </small>
                        </div>

                    </div>

                    <hr class="border-light">

                    <div class="mb-3">

                        <small class="text-muted d-block mb-1">
                            Staff Number
                        </small>

                        <strong>
                            {{ $receptionist->staff_number ?: 'N/A' }}
                        </strong>

                    </div>

                    <div class="mb-3">

                        <small class="text-muted d-block mb-1">
                            Employment Date
                        </small>

                        <strong>
                            {{ $receptionist->hired_at?->format('d F Y') ?? 'N/A' }}
                        </strong>

                    </div>

                    <div>

                        <small class="text-muted d-block mb-1">
                            Account Email
                        </small>

                        <strong class="text-break">
                            {{ $receptionist->user?->email ?? 'N/A' }}
                        </strong>

                    </div>

                </div>

            </div>


            {{-- Reception desk --}}
            <div class="card border-0 shadow-sm"
                 style="border-radius: 14px;">

                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-3">

                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width: 45px; height: 45px; background: #ecfdf5; color: #059669;">

                            <i class="bi bi-headset fs-4"></i>

                        </div>

                        <div>
                            <h6 class="fw-bold mb-1">
                                Reception Desk
                            </h6>

                            <small class="text-muted">
                                Front office responsibilities
                            </small>
                        </div>

                    </div>

                    <p class="text-muted small mb-0">
                        Manage visitors, admission enquiries, appointments,
                        incoming documents and communication with parents
                        and other school departments.
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- Quick access --}}
    <div class="mt-4">

        <div class="mb-3">

            <h5 class="fw-bold mb-1">
                Quick Access
            </h5>

            <p class="text-muted small mb-0">
                Your front office management workspace
            </p>

        </div>


        <div class="row g-3">

            <div class="col-xl-3 col-md-6">

                <div class="card border-0 shadow-sm h-100"
                     style="border-radius: 14px;">

                    <div class="card-body p-4">

                        <div class="mb-3 text-primary">
                            <i class="bi bi-person-plus fs-2"></i>
                        </div>

                        <h6 class="fw-bold">
                            Visitor Management
                        </h6>

                        <p class="text-muted small mb-0">
                            Register and manage school visitors.
                        </p>

                        <span class="badge bg-light text-secondary mt-3">
                            Coming soon
                        </span>

                    </div>

                </div>

            </div>


            <div class="col-xl-3 col-md-6">

                <div class="card border-0 shadow-sm h-100"
                     style="border-radius: 14px;">

                    <div class="card-body p-4">

                        <div class="mb-3 text-success">
                            <i class="bi bi-chat-left-text fs-2"></i>
                        </div>

                        <h6 class="fw-bold">
                            Admission Enquiries
                        </h6>

                        <p class="text-muted small mb-0">
                            Record and follow up admission enquiries.
                        </p>

                        <span class="badge bg-light text-secondary mt-3">
                            Coming soon
                        </span>

                    </div>

                </div>

            </div>


            <div class="col-xl-3 col-md-6">

                <div class="card border-0 shadow-sm h-100"
                     style="border-radius: 14px;">

                    <div class="card-body p-4">

                        <div class="mb-3 text-warning">
                            <i class="bi bi-calendar-check fs-2"></i>
                        </div>

                        <h6 class="fw-bold">
                            Appointments
                        </h6>

                        <p class="text-muted small mb-0">
                            Manage meetings and scheduled appointments.
                        </p>

                        <span class="badge bg-light text-secondary mt-3">
                            Coming soon
                        </span>

                    </div>

                </div>

            </div>


            <div class="col-xl-3 col-md-6">

                <div class="card border-0 shadow-sm h-100"
                     style="border-radius: 14px;">

                    <div class="card-body p-4">

                        <div class="mb-3 text-danger">
                            <i class="bi bi-folder2-open fs-2"></i>
                        </div>

                        <h6 class="fw-bold">
                            Received Documents
                        </h6>

                        <p class="text-muted small mb-0">
                            Keep track of documents received by the school.
                        </p>

                        <span class="badge bg-light text-secondary mt-3">
                            Coming soon
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
