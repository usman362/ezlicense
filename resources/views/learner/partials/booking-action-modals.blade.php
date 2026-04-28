{{-- ── Shared Cancel modal (used by calendar + dashboard) ── --}}
<div class="modal fade" id="learnerCancelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger-subtle">
        <h5 class="modal-title"><i class="bi bi-x-circle text-danger me-2"></i>Cancel Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="learner-cancel-form">
        <input type="hidden" id="learner-cancel-booking-id">
        <div class="modal-body">
          <div class="alert alert-warning small mb-3" id="learner-cancel-cutoff-warning" style="display:none;">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <strong>Note:</strong> Cancelling within 24 hours of the lesson may incur a cancellation fee per our policy.
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold">Why are you cancelling? <span class="text-danger">*</span></label>
            <select class="form-select" name="cancellation_reason_code" id="learner-cancel-reason-code" required>
              <option value="">Select a reason</option>
              <option value="illness_family_emergency">Illness / Family emergency</option>
              <option value="weather_conditions">Weather conditions</option>
              <option value="double_booked">Double booked / scheduling conflict</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="mb-3" id="learner-cancel-reason-other-wrap" style="display:none;">
            <label class="form-label small">Please specify</label>
            <input type="text" class="form-control" name="cancellation_reason" id="learner-cancel-reason-text" maxlength="500" placeholder="Brief reason">
          </div>

          <div class="mb-3">
            <label class="form-label small">Message to your instructor (optional)</label>
            <textarea class="form-control" name="cancellation_message" id="learner-cancel-message" rows="2" maxlength="1000" placeholder="e.g. Sorry, I'll rebook next week..."></textarea>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="learner-cancel-policy" required>
            <label class="form-check-label small" for="learner-cancel-policy">
              I have read and accept the <a href="{{ route('policies.refund-cancellation') }}" target="_blank">cancellation policy</a>.
            </label>
          </div>

          <div class="alert alert-danger small mt-3 d-none" id="learner-cancel-error"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Keep Booking</button>
          <button type="submit" class="btn btn-danger btn-sm" id="learner-cancel-submit">
            <i class="bi bi-x-circle me-1"></i>Cancel Booking
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ── Shared Reschedule modal ── --}}
<div class="modal fade" id="learnerRescheduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary-subtle">
        <h5 class="modal-title"><i class="bi bi-arrow-repeat text-primary me-2"></i>Reschedule Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="learner-reschedule-form">
        <input type="hidden" id="learner-reschedule-booking-id">
        <input type="hidden" id="learner-reschedule-instructor-profile-id">
        <div class="modal-body">
          <div class="alert alert-info small mb-3">
            <i class="bi bi-info-circle me-1"></i>
            Pick a new date and time. Your instructor will be notified.
          </div>

          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label small fw-bold">New date <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="learner-reschedule-date" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold">New time <span class="text-danger">*</span></label>
              <select class="form-select" id="learner-reschedule-time" required>
                <option value="">Select a date first</option>
              </select>
            </div>
          </div>

          <div class="mb-3 mt-3">
            <label class="form-label small fw-bold">Why are you rescheduling? <span class="text-danger">*</span></label>
            <select class="form-select" id="learner-reschedule-reason-code" required>
              <option value="">Select a reason</option>
              <option value="illness_family_emergency">Illness / Family emergency</option>
              <option value="weather_conditions">Weather conditions</option>
              <option value="double_booked">Double booked / scheduling conflict</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="learner-reschedule-policy" required>
            <label class="form-check-label small" for="learner-reschedule-policy">
              I have read and accept the <a href="{{ route('policies.refund-cancellation') }}" target="_blank">cancellation policy</a>.
            </label>
          </div>

          <div class="alert alert-danger small mt-3 d-none" id="learner-reschedule-error"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm" id="learner-reschedule-submit">
            <i class="bi bi-arrow-repeat me-1"></i>Reschedule
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
