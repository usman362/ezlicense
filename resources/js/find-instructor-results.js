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
  const rating = inst.average_rating ?? '—';
  const reviews = inst.reviews_count ?? 0;
  const transLabel = (inst.transmission || '').toLowerCase() === 'manual' ? 'Manual' : 'Auto';

  // Avatar initials from name
  const nameParts = (inst.name || '?').split(' ');
  const initials = nameParts.length >= 2
    ? (nameParts[0][0] + nameParts[nameParts.length - 1][0]).toUpperCase()
    : (nameParts[0][0] || '?').toUpperCase();

  // Star display
  const fullStars = Math.floor(Number(rating) || 0);
  const halfStar = (Number(rating) || 0) - fullStars >= 0.3;
  let starsHtml = '';
  for (let i = 0; i < 5; i++) {
    if (i < fullStars) starsHtml += '<i class="bi bi-star-fill text-warning"></i>';
    else if (i === fullStars && halfStar) starsHtml += '<i class="bi bi-star-half text-warning"></i>';
    else starsHtml += '<i class="bi bi-star text-warning"></i>';
  }

  return `
    <div class="col-md-6 col-lg-4 col-xl-3">
      <div class="card h-100 shadow-sm border-0 instructor-card" style="cursor:pointer;" data-profile-id="${inst.id}">
        <div class="card-body d-flex flex-column p-3">
          <div class="d-flex align-items-center gap-3 mb-2">
            <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0" style="width:48px;height:48px;font-weight:600;font-size:1rem;color:#555;">${initials}</div>
            <div class="flex-grow-1 min-w-0">
              <h6 class="card-title mb-0 fw-bold text-truncate">${escapeHtml(inst.name)}</h6>
              <div class="d-flex align-items-center gap-1 small">${starsHtml}<span class="text-muted ms-1">${reviews} ratings</span></div>
            </div>
          </div>
          <div class="d-flex flex-wrap gap-2 small text-muted mb-2">
            <span><i class="bi bi-car-front me-1"></i>${escapeHtml(transLabel)}</span>
            ${price ? `<span><i class="bi bi-tag me-1"></i>$${price}.00/hr</span>` : ''}
          </div>
          ${location ? `<p class="card-text small text-muted mb-2"><i class="bi bi-geo-alt me-1"></i>${escapeHtml(location)}</p>` : ''}
          <div class="d-grid gap-2 mt-auto">
            <button type="button" class="btn btn-warning btn-sm fw-bold book-now-btn" data-id="${inst.id}">Book Online Now</button>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-secondary btn-sm flex-fill view-profile-btn" data-id="${inst.id}">View Profile</button>
              <button type="button" class="btn btn-outline-secondary btn-sm flex-fill availability-btn" data-id="${inst.id}">Availability</button>
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
  const card = col.querySelector('.instructor-card');

  // Clicking the card itself goes to profile (except when clicking buttons)
  if (card) {
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
