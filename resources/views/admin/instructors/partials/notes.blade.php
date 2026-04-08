<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-sticky me-2"></i>Add Internal Note</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.instructors.notes.store', $instructor) }}">
                    @csrf
                    <div class="mb-2">
                        <textarea name="note" class="form-control" rows="5" required placeholder="Internal admin note (only visible to admins)..."></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="pinned" value="1" class="form-check-input" id="pinNoteCheck">
                        <label class="form-check-label small" for="pinNoteCheck">Pin this note to the top</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-plus-lg me-1"></i>Add Note</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-journal-text me-2"></i>All Internal Notes ({{ $instructor->adminNotes->count() }})</h6>
            </div>
            <div class="card-body p-0" style="max-height:700px; overflow-y:auto;">
                @if($instructor->adminNotes->count() > 0)
                    @foreach($instructor->adminNotes as $note)
                        <div class="border-bottom p-3 {{ $note->pinned ? 'bg-warning bg-opacity-10' : '' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    @if($note->pinned)
                                        <span class="badge bg-warning text-dark"><i class="bi bi-pin-angle-fill"></i> Pinned</span>
                                    @endif
                                    <strong class="small">{{ $note->admin->name ?? '—' }}</strong>
                                </div>
                                <small class="text-muted">{{ $note->created_at->format('d M Y H:i') }}</small>
                            </div>
                            <div class="small" style="white-space:pre-wrap;">{{ $note->note }}</div>
                            <div class="text-end mt-2">
                                <form method="POST" action="{{ route('admin.instructors.notes.toggle-pin', $note) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-outline-warning btn-sm py-0 px-2" title="{{ $note->pinned ? 'Unpin' : 'Pin' }}">
                                        <i class="bi bi-pin{{ $note->pinned ? '' : '-angle' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.instructors.notes.destroy', $note) }}" class="d-inline" onsubmit="return confirm('Delete this note?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm py-0 px-2"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-journal fs-1 d-block mb-2"></i>
                        No internal notes yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
