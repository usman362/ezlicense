/**
 * Find-instructor results page: load instructors from API and render cards. Only works when opened from find-instructor (params in URL/data).
 */
import {
  runInstructorSearch,
  loadAvailabilityForInstructor,
} from './securelicence-search.js';

const params = window.findInstructorResultsParams || {};
const isLearner = !!window.isLearner;
const learnerBookingNewUrl = window.learnerBookingNewUrl || '';

let availabilityModal;
let availabilityInstructor = null;

function escapeHtml(s) {
  const div = document.createElement('div');
  div.textContent = s ?? '';
  return div.innerHTML;
}

function renderCard(inst) {
  const price = inst.lesson_price != null ? Math.round(inst.lesson_price) : null;
  const location = (inst.service_areas && inst.service_areas.length)
    ? `${inst.service_areas[0].name} ${inst.service_areas[0].postcode} ${inst.service_areas[0].state || ''}`.trim()
    : '';
  const ratingNum = Number(inst.average_rating) || 0;
  const ratingDisplay = ratingNum > 0 ? ratingNum.toFixed(1) : '—';
  const reviews = inst.reviews_count ?? 0;
  const transRaw = (inst.transmission || '').toLowerCase();
  const transLabel = transRaw === 'manual' ? 'Manual' : (transRaw === 'both' ? 'Auto & Manual' : 'Auto');

  // Avatar initials from name
  const nameParts = (inst.name || '?').split(' ').filter(Boolean);
  const initials = nameParts.length >= 2
    ? (nameParts[0][0] + nameParts[nameParts.length - 1][0]).toUpperCase()
    : (nameParts[0][0] || '?').toUpperCase();

  // Profile photo fallback
  const photo = inst.profile_photo_url || inst.profile_photo || '';
  const photoHtml = photo
    ? `<img src="${escapeHtml(photo)}" alt="${escapeHtml(inst.name || '')}" class="instructor-photo">`
    : `<div class="instructor-initials">${initials}</div>`;

  // Star display (5 stars, filled based on rating)
  const fullStars = Math.floor(ratingNum);
  const halfStar = ratingNum - fullStars >= 0.3 && ratingNum - fullStars < 0.8;
  let starsHtml = '';
  for (let i = 0; i < 5; i++) {
    if (i < fullStars) starsHtml += '<i class="bi bi-star-fill"></i>';
    else if (i === fullStars && halfStar) starsHtml += '<i class="bi bi-star-half"></i>';
    else starsHtml += '<i class="bi bi-star"></i>';
  }

  // Top-badge area: verified + popularity
  const verifiedBadge = inst.is_verified !== false
    ? '<span class="sl-verified-badge" title="Verified instructor"><i class="bi bi-patch-check-fill"></i> Verified</span>'
    : '';
  const popularBadge = (reviews >= 20 && ratingNum >= 4.7)
    ? '<span class="sl-popular-badge" title="Highly rated with many bookings"><i class="bi bi-fire"></i> Popular</span>'
    : '';

  return `
    <div class="col-md-6 col-lg-4 col-xl-3">
      <div class="card h-100 border-0 instructor-card-v2" data-profile-id="${inst.id}">
        <div class="instructor-card-v2-header">
          ${photoHtml}
          <div class="instructor-card-v2-badges">
            ${verifiedBadge}
            ${popularBadge}
          </div>
        </div>
        <div class="card-body d-flex flex-column p-3 pt-4">
          <h6 class="fw-bolder mb-1 text-truncate" style="font-size:1.05rem; letter-spacing:-0.01em;">${escapeHtml(inst.name)}</h6>
          <div class="d-flex align-items-center gap-2 mb-3">
            <div class="sl-stars">${starsHtml}</div>
            <span class="fw-bold small">${ratingDisplay}</span>
            <span class="small text-muted">(${reviews})</span>
          </div>

          <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="sl-chip"><i class="bi bi-gear-fill"></i>${escapeHtml(transLabel)}</span>
            ${inst.vehicle_year && inst.vehicle_make ? `<span class="sl-chip"><i class="bi bi-car-front-fill"></i>${escapeHtml(inst.vehicle_year + ' ' + inst.vehicle_make)}</span>` : ''}
          </div>

          ${location ? `<div class="small text-muted mb-3 d-flex align-items-start gap-2"><i class="bi bi-geo-alt-fill" style="color:var(--sl-primary-600); margin-top:2px;"></i><span class="text-truncate">${escapeHtml(location)}</span></div>` : ''}

          <div class="mt-auto pt-2 border-top">
            ${price ? `
              <div class="d-flex align-items-baseline justify-content-between mb-2">
                <span class="small text-muted">from</span>
                <div>
                  <span class="fw-bolder" style="font-size:1.35rem; color:var(--sl-gray-900);">$${price}</span>
                  <span class="small text-muted">/hr</span>
                </div>
              </div>
            ` : ''}
            <div class="d-grid gap-2">
              <button type="button" class="btn btn-primary btn-sm fw-bold book-now-btn" data-id="${inst.id}">
                <i class="bi bi-calendar-check me-1"></i>Book Online
              </button>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill view-profile-btn" data-id="${inst.id}">
                  <i class="bi bi-person"></i> Profile
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm flex-fill availability-btn" data-id="${inst.id}">
                  <i class="bi bi-clock"></i> Times
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

function attachCardHandlers(col, inst) {
  const id = inst.id;
  const bookBtn = col.querySelector('.book-now-btn');
  const viewBtn = col.querySelector('.view-profile-btn');
  const availBtn = col.querySelector('.availability-btn');
  const card = col.querySelector('.instructor-card-v2');

  // Clicking the card itself goes to profile (except when clicking buttons)
  if (card) {
    card.style.cursor = 'pointer';
    card.addEventListener('click', (e) => {
      if (e.target.closest('button')) return; // Don't navigate when button clicked
      window.location.href = `/instructors/${id}`;
    });
  }

  if (bookBtn) {
    bookBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      if (isLearner && learnerBookingNewUrl) {
        window.location.href = `${learnerBookingNewUrl}?instructor_profile_id=${id}`;
      } else {
        window.location.href = '/learner/login';
      }
    });
  }
  if (viewBtn) {
    viewBtn.addEventListener('click', (e) => { e.stopPropagation(); window.location.href = `/instructors/${id}`; });
  }
  if (availBtn) {
    availBtn.addEventListener('click', (e) => { e.stopPropagation(); openAvailability(inst); });
  }
}

function openAvailability(inst) {
  availabilityInstructor = inst;
  const modalEl = document.getElementById('availabilityModal');
  if (!modalEl) return;
  if (!availabilityModal && window.bootstrap && window.bootstrap.Modal) {
    availabilityModal = new window.bootstrap.Modal(modalEl);
  }
  const headingSpan = document.getElementById('availability-instructor-heading');
  const nameSpan = document.getElementById('availability-instructor-name');
  const loadingEl = document.getElementById('availability-loading');
  const contentEl = document.getElementById('availability-content');
  const bookBtn = document.getElementById('availability-book-btn');
  if (headingSpan) headingSpan.textContent = inst.name || '';
  if (nameSpan) nameSpan.textContent = inst.name || '';
  if (loadingEl) loadingEl.style.display = 'block';
  if (contentEl) {
    contentEl.style.display = 'none';
    contentEl.innerHTML = '';
  }
  if (bookBtn) {
    bookBtn.onclick = () => {
      if (!availabilityInstructor) return;
      if (isLearner && learnerBookingNewUrl) {
        window.location.href = `${learnerBookingNewUrl}?instructor_profile_id=${availabilityInstructor.id}`;
      } else {
        window.location.href = '/learner/login';
      }
    };
  }
  if (availabilityModal) availabilityModal.show();

  loadAvailabilityForInstructor(inst.id)
    .then(({ dates }) => {
      if (!contentEl || !loadingEl) return;
      loadingEl.style.display = 'none';
      const ds = dates || [];
      if (!ds.length) {
        contentEl.innerHTML = '<p class="text-muted mb-0 small">No availability data to show here. You can still continue by clicking "Book with ' + escapeHtml(inst.name || '') + '" to choose a time in the booking flow.</p>';
      } else {
        const list = ds.slice(0, 7).map((d) => {
          const label = typeof d === 'string' ? d : d.label || d.date || '';
          return `<span class="badge bg-light text-dark border me-1 mb-1">${escapeHtml(label)}</span>`;
        }).join('');
        contentEl.innerHTML = '<p class="small mb-2">Next available dates:</p><div>' + list + '</div><p class="small text-muted mt-3 mb-0">To view exact times, continue to the booking flow.</p>';
      }
      contentEl.style.display = 'block';
    })
    .catch((e) => {
      if (!contentEl || !loadingEl) return;
      loadingEl.style.display = 'none';
      contentEl.innerHTML = '<p class="text-danger small mb-0">Could not load availability. Please try again or continue to the booking flow.</p>';
      contentEl.style.display = 'block';
      console.error(e);
    });
}

function init() {
  const resultsEl = document.getElementById('results');
  const resultsLoading = document.getElementById('results-loading');
  const resultsEmpty = document.getElementById('results-empty');
  const resultsHeading = document.getElementById('results-heading');
  const resultsFromPrice = document.getElementById('results-from-price');
  const moreSection = document.getElementById('more-section');
  const moreResults = document.getElementById('more-results');

  const suburbId = params.suburbId || null;
  const transmission = params.transmission || null;
  const testPreBooked = params.testPreBooked === true;

  runInstructorSearch({ suburbId, transmission, testPreBooked })
    .then((instructors) => {
      resultsLoading.style.display = 'none';
      if (!instructors.length) {
        resultsEmpty.style.display = 'block';
        return;
      }
      const prices = instructors.map((i) => (i.lesson_price != null ? Number(i.lesson_price) : null)).filter((p) => p != null && !Number.isNaN(p));
      const minPrice = prices.length ? Math.min(...prices) : null;
      const transLabel = transmission === 'manual' ? 'Manual' : (transmission === 'auto' ? 'Auto' : '');
      const headingText = transLabel
        ? `${instructors.length} ${transLabel} Instructor${instructors.length !== 1 ? 's' : ''} Available`
        : `${instructors.length} Instructor${instructors.length !== 1 ? 's' : ''} Available`;
      if (resultsHeading) {
        resultsHeading.textContent = headingText;
        resultsHeading.style.display = 'block';
      }
      if (resultsFromPrice) {
        resultsFromPrice.textContent = minPrice != null ? `from $${Math.round(minPrice)}.00/hr` : '';
        resultsFromPrice.style.display = 'block';
      }
      instructors.slice(0, 12).forEach((inst) => {
        const col = document.createElement('div');
        col.innerHTML = renderCard(inst).trim();
        const card = col.firstElementChild;
        if (card) {
          attachCardHandlers(card, inst);
          resultsEl.appendChild(card);
        }
      });
      if (instructors.length > 12 && moreSection && moreResults) {
        moreSection.style.display = 'block';
        instructors.slice(12).forEach((inst) => {
          const col = document.createElement('div');
          col.innerHTML = renderCard(inst).trim();
          const card = col.firstElementChild;
          if (card) {
            attachCardHandlers(card, inst);
            moreResults.appendChild(card);
          }
        });
      }
    })
    .catch((e) => {
      resultsLoading.style.display = 'none';
      resultsEmpty.innerHTML = 'Unable to load instructors. <a href="/find-instructor">Try again</a>.';
      resultsEmpty.style.display = 'block';
      console.error(e);
    });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
