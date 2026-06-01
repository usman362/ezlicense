{{--
    Contact-our-team modal. Available on every instructor page (included from
    layouts/instructor.blade.php). Triggered by any element with
    data-bs-toggle="modal" data-bs-target="#contactTeamModal".
--}}
<div class="modal fade" id="contactTeamModal" tabindex="-1" aria-labelledby="contactTeamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bolder" id="contactTeamModalLabel">
                    <i class="bi bi-envelope-heart-fill text-warning me-2"></i>Contact our team
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">
                    Under the <a href="{{ route('instructor.support') }}" class="fw-bold text-warning-emphasis">Support</a> menu you'll find guides and quick answers to most common questions — start there for the fastest fix.
                </p>

                {{-- Phone illustration: stylised menu showing where "Support" lives --}}
                <div class="contact-modal-illu my-3">
                    <svg viewBox="0 0 360 220" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        {{-- Phone frame --}}
                        <rect x="40" y="10" width="120" height="200" rx="18" fill="#1f2937"/>
                        <rect x="46" y="22" width="108" height="180" rx="6" fill="#fff"/>
                        <rect x="84" y="14" width="32" height="4" rx="2" fill="#0f172a"/>
                        {{-- App header --}}
                        <rect x="46" y="22" width="108" height="22" fill="#fef3c7"/>
                        <text x="58" y="36" font-size="9" font-weight="700" fill="#92400e">Secure L</text>
                        <circle cx="142" cy="33" r="3" fill="#dc2626"/>
                        <rect x="138" y="29" width="10" height="8" rx="2" fill="none" stroke="#1f2937" stroke-width="1.2"/>
                        {{-- Hamburger menu icon highlighted --}}
                        <rect x="126" y="28" width="14" height="10" rx="2" fill="none" stroke="#dc2626" stroke-width="1.5"/>
                        <line x1="129" y1="31" x2="137" y2="31" stroke="#1f2937" stroke-width="1.4"/>
                        <line x1="129" y1="33.5" x2="137" y2="33.5" stroke="#1f2937" stroke-width="1.4"/>
                        <line x1="129" y1="36" x2="137" y2="36" stroke="#1f2937" stroke-width="1.4"/>
                        {{-- App body content --}}
                        <rect x="54" y="54" width="92" height="60" rx="4" fill="#f9fafb" stroke="#e5e7eb"/>
                        <rect x="60" y="60" width="40" height="3" rx="1.5" fill="#1f2937"/>
                        <rect x="60" y="68" width="64" height="2" rx="1" fill="#9ca3af"/>
                        <rect x="60" y="74" width="50" height="2" rx="1" fill="#9ca3af"/>
                        <rect x="60" y="84" width="80" height="6" rx="2" fill="#fbbf24"/>
                        <text x="64" y="89" font-size="4" font-weight="700" fill="#1f2937">VIEW BOOKING</text>

                        {{-- Arrow --}}
                        <path d="M 178 105 L 198 105" stroke="#9ca3af" stroke-width="2" fill="none" marker-end="url(#arrow)"/>
                        <defs>
                            <marker id="arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto">
                                <path d="M 0 0 L 10 5 L 0 10 z" fill="#9ca3af"/>
                            </marker>
                        </defs>

                        {{-- Phone 2 — open menu showing Support highlighted --}}
                        <rect x="210" y="10" width="120" height="200" rx="18" fill="#1f2937"/>
                        <rect x="216" y="22" width="108" height="180" rx="6" fill="#fff"/>
                        <rect x="254" y="14" width="32" height="4" rx="2" fill="#0f172a"/>
                        <rect x="216" y="22" width="108" height="22" fill="#fef3c7"/>
                        <text x="228" y="36" font-size="9" font-weight="700" fill="#92400e">Secure L</text>
                        <text x="306" y="36" font-size="11" font-weight="700" fill="#1f2937" text-anchor="middle">×</text>

                        {{-- Menu items --}}
                        <text x="224" y="60" font-size="6" font-weight="600" fill="#1f2937">Dashboard</text>
                        <text x="224" y="74" font-size="6" font-weight="600" fill="#1f2937">Calendar</text>
                        <text x="224" y="88" font-size="6" font-weight="600" fill="#1f2937">Learners</text>
                        <text x="224" y="102" font-size="6" font-weight="600" fill="#1f2937">Notifications</text>
                        <text x="224" y="116" font-size="6" font-weight="600" fill="#1f2937">Reports</text>
                        {{-- Support highlighted --}}
                        <rect x="220" y="122" width="100" height="14" rx="3" fill="#fef3c7" stroke="#dc2626" stroke-width="1.5"/>
                        <text x="224" y="132" font-size="6" font-weight="800" fill="#b45309">Support</text>
                        <text x="224" y="148" font-size="6" font-weight="600" fill="#1f2937">Contact</text>
                        <text x="224" y="162" font-size="6" font-weight="600" fill="#1f2937">Personal Details</text>
                        <text x="224" y="176" font-size="6" font-weight="600" fill="#1f2937">My Profile</text>
                    </svg>
                </div>

                <p class="text-muted">
                    Still need a hand? Submit a request and our team will get back to you within 1-2 business days.
                </p>

                <div class="d-grid gap-2 mb-3">
                    <a href="{{ route('instructor.support') }}" class="btn btn-warning fw-bold">
                        <i class="bi bi-send-fill me-1"></i>Submit a request
                    </a>
                </div>

                <div class="contact-modal-alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong>In case of an emergency,</strong> SMS the details to
                        <a href="sms:+61490418703" class="fw-bold">0490 418 703</a>.
                    </div>
                </div>

                <hr class="my-3">

                <p class="small text-muted mb-0">
                    Learners who require assistance can contact our <strong>Customer Support Team</strong> via the
                    <a href="{{ route('support.home') }}" class="fw-semibold">public support page</a>.
                </p>
            </div>
        </div>
    </div>
</div>

@once
<style>
.contact-modal-illu {
    background: linear-gradient(135deg, #fef9c3 0%, #fef3c7 100%);
    border-radius: 14px;
    padding: 0.85rem 1rem;
    text-align: center;
}
.contact-modal-illu svg { width: 100%; max-width: 340px; height: auto; }

.contact-modal-alert {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
    padding: 0.85rem 1rem;
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 12px;
    color: #92400e;
    font-size: 0.92rem;
    line-height: 1.5;
}
.contact-modal-alert i {
    font-size: 1.15rem;
    color: #d97706;
    flex-shrink: 0;
    margin-top: 0.05rem;
}
.contact-modal-alert a {
    color: #b45309;
    text-decoration: none;
}
.contact-modal-alert a:hover { text-decoration: underline; }
</style>
@endonce
