
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1">School Announcements</h3>
            <p class="text-muted mb-0">
                Stay informed about news and updates from your school.
            </p>
        </div>

        <div class="text-primary bg-primary bg-opacity-10 rounded-3 p-3">
            <i class="bi bi-megaphone fs-3"></i>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <label for="announcementSearch" class="form-label fw-semibold">
                Search announcements
            </label>

            <input
                id="announcementSearch"
                type="search"
                class="form-control"
                placeholder="Search by title or message..."
                wire:model.live.debounce.300ms="search"
            >
        </div>
    </div>

    @forelse ($announcements as $announcement)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">

                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <h5 class="fw-bold mb-0">
                        {{ $announcement->title }}
                    </h5>

                    <span class="badge text-bg-primary">
                        Announcement
                    </span>
                </div>

                <div class="text-secondary mb-3"
                     style="white-space: pre-line; overflow-wrap: anywhere;">
                    {{ $announcement->body }}
                </div>

                <div class="border-top pt-3 d-flex flex-wrap justify-content-between gap-2">
                    <small class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>
                        Published
                        {{ $announcement->published_at?->format('d M Y, h:i A') }}
                    </small>

                    @if ($announcement->creator)
                        <small class="text-muted">
                            Posted by {{ $announcement->creator->name }}
                        </small>
                    @endif
                </div>

            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-megaphone fs-1 text-muted"></i>

                <h5 class="fw-bold mt-3">No announcements yet</h5>

                <p class="text-muted mb-0">
                    New school announcements will appear here when published.
                </p>
            </div>
        </div>
    @endforelse

    <div class="mt-4">
        {{ $announcements->links() }}
    </div>

</div>
