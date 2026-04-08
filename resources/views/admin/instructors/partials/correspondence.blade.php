<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-envelope-plus me-2"></i>Log New Correspondence</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.instructors.correspondences.store', $instructor) }}">
                    @csrf
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Channel <span class="text-danger">*</span></label>
                            <select name="channel" class="form-select form-select-sm" required>
                                @foreach(\App\Models\InstructorCorrespondence::channels() as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Direction <span class="text-danger">*</span></label>
                            <select name="direction" class="form-select form-select-sm" required>
                                <option value="outbound">Outbound (we sent)</option>
                                <option value="inbound">Inbound (they sent)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Date &amp; Time</label>
                        <input type="datetime-local" name="communicated_at" class="form-control form-control-sm" value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Subject</label>
                        <input type="text" name="subject" class="form-control form-control-sm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Body / Notes <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control form-control-sm" rows="6" required placeholder="Paste the email content, SMS text, or call notes here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-save me-1"></i>Save to Log</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-envelope me-2"></i>Correspondence History ({{ $instructor->correspondences->count() }})</h6>
            </div>
            <div class="card-body p-0" style="max-height:700px; overflow-y:auto;">
                @if($instructor->correspondences->count() > 0)
                    @php $channelIcons = [
                        'email'=>'envelope','sms'=>'chat-dots','phone_call'=>'telephone',
                        'in_person'=>'person-fill','system_message'=>'chat-square','other'=>'three-dots'
                    ]; @endphp
                    @foreach($instructor->correspondences as $c)
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-{{ $c->direction === 'outbound' ? 'primary' : 'info' }}">
                                        <i class="bi bi-arrow-{{ $c->direction === 'outbound' ? 'right' : 'left' }}"></i>
                                        {{ ucfirst($c->direction) }}
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-{{ $channelIcons[$c->channel] ?? 'three-dots' }} me-1"></i>
                                        {{ \App\Models\InstructorCorrespondence::channels()[$c->channel] ?? $c->channel }}
                                    </span>
                                    @if($c->subject)
                                        <strong class="ms-2">{{ $c->subject }}</strong>
                                    @endif
                                </div>
                                <div class="text-muted small text-end">
                                    {{ ($c->communicated_at ?? $c->created_at)->format('d M Y H:i') }}
                                    <br><span>by {{ $c->admin->name ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="small text-body bg-light p-2 rounded" style="white-space:pre-wrap;">{{ $c->body }}</div>
                            <div class="text-end mt-2">
                                <form method="POST" action="{{ route('admin.instructors.correspondences.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Delete this entry?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm py-0 px-2"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-envelope-open fs-1 d-block mb-2"></i>
                        No correspondence logged yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
