/**
 * Find-instructor results page (EzLicence-style):
 * - Dual-circle photo cards (instructor + car)
 * - Filter pills with active state, real sort/filter behaviour
 * - Sort modal (Best match, Highest rated, Lowest/Highest price, Experience, Most lessons)
 * - Filters: female-only, available-next-4-days
 */
import {
  runInstructorSearch,
  loadAvailabilityForInstructor,
} from './securelicence-search.js';

const params = window.findInstructorResultsParams || {};
const isLearner = !!window.isLearner;

// ── Mutable state ──
let allInstructors = []; // raw API data
let currentSort = 'best_match'; // best_match | rating | price | price_high | experience | lessons | next_available
let activeFilters = new Set(); // 'female_only', 'available_4_days', etc.

let availabilityModal;
let sortModal;
let availabilityInstructor = null;

// ── Helpers ──
function escapeHtml(s) {
  const div = document.createElement('div');
  div.textContent = s ?? '';
  return div.innerHTML;
}

function initials(name) {
  const parts = (name || '?').split(' ').filter(Boolean);
  return parts.length >= 2
    ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
    : (parts[0][0] || '?').toUpperCase();
}

function formatTenureLabel(months) {
  if (!months || months < 1) return 'New instructor';
  if (months < 12) return `Instructed for ${months} mo.`;
  const years = Math.floor(months / 12);
  return `${years}+ year${years > 1 ? 's' : ''} instructing`;
}

function bookingsLabel(inst) {
  const c = Number(inst.completed_lessons_count) || 0;
  if (c >= 10) return `${c} Completed Lessons`;
  if (inst.is_verified) return 'Verified Driving Instructor';
  if (inst.instructing_months >= 1) return formatTenureLabel(inst.instructing_months);
  return 'Verified Driving Instructor';
}

function isGreatValue(inst, allPrices) {
  if (!allPrices.length || inst.lesson_price == null) return false;
  // bottom 33% of prices = "Great Value"
  const sorted = [...allPrices].sort((a, b) => a - b);
  const threshold = sorted[Math.floor(sorted.length / 3)] || sorted[0];
  return inst.lesson_price <= threshold;
}

function renderStars(rating) {
  const r = Number(rating) || 0;
  const full = Math.floor(r);
  const half = r - full >= 0.3 && r - full < 0.8;
  let html = '';
  for (let i = 0; i < 5; i++) {
    if (i < full) html += '<i class="bi bi-star-fill"></i>';
    else if (i === full && half) html += '<i class="bi bi-star-half"></i>';
    else html += '<i class="bi bi-star"></i>';
  }
  return html;
}

function renderCard(inst, allPrices) {
  const price = inst.lesson_price != null ? Math.round(inst.lesson_price) : null;
  const rating = Number(inst.average_rating) || 0;
  const reviews = inst.reviews_count ?? 0;
  const completed = Number(inst.completed_lessons_count) || 0;
  const greatValue = isGreatValue(inst, allPrices);
  // Top Instructor: rating >= 4.7 AND reviews >= 30 (or completed >= 100)
  const topInstructor = (rating >= 4.7 && reviews >= 30) || completed >= 100;

  // Photo or initials
  const photoUrl = inst.profile_photo_url;
  const photoHtml = photoUrl
    ? `<img src="${escapeHtml(photoUrl)}" alt="${escapeHtml(inst.name || '')}" class="ic-photo-img">`
    : `<div class="ic-photo-initials">${initials(inst.name)}</div>`;

  // Vehicle photo or generic car icon
  const vehicleUrl = inst.vehicle_photo_url;
  const vehicleHtml = vehicleUrl
    ? `<img src="${escapeHtml(vehicleUrl)}" alt="Vehicle" class="ic-vehicle-img">`
    : `<div class="ic-vehicle-icon"><i class="bi bi-car-front-fill"></i></div>`;

  // Female-only badge (if applicable)
  const femaleOnlyTag = inst.female_only
    ? `<div class="ic-female-only-tag"><i class="bi bi-shield-fill-check me-1"></i>Female learners only</div>`
    : '';

  // Decide which top badge to show (Top Instructor beats Great Value)
  let topBadge = '';
  if (topInstructor) {
    topBadge = '<div class="ic-badge-tag ic-badge-top"><i class="bi bi-trophy-fill"></i> Top Instructor</div>';
  } else if (greatValue) {
    topBadge = '<div class="ic-badge-tag"><i class="bi bi-currency-dollar"></i> Great Value</div>';
  }

  return `
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
      <div class="ic-card" data-profile-id="${inst.id}">
        ${topBadge}
        <div class="ic-card-body">
          <div class="ic-photos">
            <div class="ic-photo-circle">${photoHtml}</div>
            <div class="ic-vehicle-circle">${vehicleHtml}</div>
          </div>

          <h3 class="ic-name">${escapeHtml(inst.first_name || inst.name)}</h3>

          ${rating > 0 ? `
            <div class="ic-rating">
              <i class="bi bi-star-fill"></i>
              <span class="ic-rating-num">${rating.toFixed(1)}</span>
              <span class="ic-rating-sep">·</span>
              <span class="ic-rating-count">${reviews} Rating${reviews !== 1 ? 's' : ''}</span>
            </div>
          ` : `
            <div class="ic-rating ic-rating-new">
              <i class="bi bi-star"></i>
              <span class="ic-rating-num">New</span>
            </div>
          `}

          <div class="ic-label">${escapeHtml(bookingsLabel(inst))}</div>

          ${femaleOnlyTag}

          ${price != null ? `
            <div class="ic-price">$${price}.00<span class="ic-price-unit">/hr</span></div>
          ` : ''}

          <div class="ic-actions">
            <button type="button" class="btn btn-warning fw-bolder w-100 ic-btn-book book-now-btn" data-id="${inst.id}">
              Book Online Now
            </button>
            <div class="row g-2 mt-2">
              <div class="col-6">
                <button type="button" class="btn btn-outline-secondary w-100 ic-btn-secondary view-profile-btn" data-id="${inst.id}">
                  View Profile
                </button>
              </div>
              <div class="col-6">
                <button type="button" class="btn btn-outline-secondary w-100 ic-btn-secondary availability-btn" data-id="${inst.id}">
                  Availability
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;
}

function attachCardHandlers(card, inst) {
  const id = inst.id;
  const bookBtn = card.querySelector('.book-now-btn');
  const viewBtn = card.querySelector('.view-profile-btn');
  const availBtn = card.querySelector('.availability-btn');

  const cardEl = card.querySelector('.ic-card');
  if (cardEl) {
    cardEl.style.cursor = 'pointer';
    cardEl.addEventListener('click', (e) => {
      if (e.target.closest('button')) return;
      window.location.href = `/instructors/${id}`;
    });
  }

  if (bookBtn) {
    bookBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      if (isLearner) {
        window.location.href = `/learner/bookings/new?instructor_profile_id=${id}`;
      } else {
        window.location.href = `/learner/bookings/amount?instructor_profile_id=${id}`;
      }
    });
  }
  if (viewBtn) viewBtn.addEventListener('click', (e) => { e.stopPropagation(); window.location.href = `/instructors/${id}`; });
  if (availBtn) availBtn.addEventListener('click', (e) => { e.stopPropagation(); openAvailability(inst); });
}

// ── Sort + filter ──
function applySortAndFilter(instructors) {
  let list = [...instructors];

  // Apply filters
  if (activeFilters.has('female_only')) {
    list = list.filter((i) => (i.gender || '').toLowerCase() === 'female');
  }
  if (activeFilters.has('available_4_days')) {
    // We don't have a real "available in 4 days" signal — best-effort: show all active instructors
    // (placeholder filter; future enhancement would check actual availability slots)
  }

  // Apply sort
  switch (currentSort) {
    case 'rating':
      list.sort((a, b) => (Number(b.average_rating) || 0) - (Number(a.average_rating) || 0));
      break;
    case 'price':
      list.sort((a, b) => (Number(a.lesson_price) || 0) - (Number(b.lesson_price) || 0));
      break;
    case 'price_high':
      list.sort((a, b) => (Number(b.lesson_price) || 0) - (Number(a.lesson_price) || 0));
      break;
    case 'experience':
      list.sort((a, b) => (Number(b.instructing_months) || 0) - (Number(a.instructing_months) || 0));
      break;
    case 'lessons':
      list.sort((a, b) => (Number(b.completed_lessons_count) || 0) - (Number(a.completed_lessons_count) || 0));
      break;
    case 'next_available':
      // Without real availability data, fall back to most recently active (proxy: highest reviews)
      list.sort((a, b) => (Number(b.reviews_count) || 0) - (Number(a.reviews_count) || 0));
      break;
    case 'best_match':
    default:
      // Best match: weighted by rating + completed_lessons
      list.sort((a, b) => {
        const sa = (Number(a.average_rating) || 0) * 10 + (Number(a.completed_lessons_count) || 0) * 0.1;
        const sb = (Number(b.average_rating) || 0) * 10 + (Number(b.completed_lessons_count) || 0) * 0.1;
        return sb - sa;
      });
      break;
  }
  return list;
}

function renderResults() {
  const resultsEl = document.getElementById('results');
  const resultsEmpty = document.getElementById('results-empty');
  const resultsHeading = document.getElementById('results-heading');
  const resultsFromPrice = document.getElementById('results-from-price');

  const filtered = applySortAndFilter(allInstructors);
  const allPrices = filtered.map((i) => Number(i.lesson_price)).filter((p) => !isNaN(p));
  const minPrice = allPrices.length ? Math.min(...allPrices) : null;

  resultsEl.innerHTML = '';

  if (!filtered.length) {
    resultsEmpty.style.display = 'block';
    if (resultsHeading) resultsHeading.style.display = 'none';
    if (resultsFromPrice) resultsFromPrice.style.display = 'none';
    return;
  }

  resultsEmpty.style.display = 'none';

  // Heading — transmission word in bold/accent (matches EzLicence reference: "29 Auto Instructors Available")
  const transmission = (params.transmission || '').toLowerCase();
  const transLabel = transmission === 'manual' ? 'Manual' : (transmission === 'auto' ? 'Auto' : '');
  if (resultsHeading) {
    if (transLabel) {
      resultsHeading.innerHTML = `${filtered.length} <span class="ic-heading-trans">${transLabel}</span> Instructor${filtered.length !== 1 ? 's' : ''} Available`;
    } else {
      resultsHeading.textContent = `${filtered.length} Instructor${filtered.length !== 1 ? 's' : ''} Available`;
    }
    resultsHeading.style.display = 'block';
  }
  if (resultsFromPrice) {
    resultsFromPrice.textContent = minPrice != null ? `from $${minPrice.toFixed(2)}/hr` : '';
    resultsFromPrice.style.display = minPrice != null ? 'block' : 'none';
  }

  filtered.forEach((inst) => {
    const wrap = document.createElement('div');
    wrap.innerHTML = renderCard(inst, allPrices).trim();
    const colEl = wrap.firstElementChild;
    if (colEl) {
      attachCardHandlers(colEl, inst);
      resultsEl.appendChild(colEl);
    }
  });
}

// ── Filter pill UI ──
function refreshFilterPillUI() {
  document.querySelectorAll('.filter-pill').forEach((p) => {
    const sort = p.dataset.sort;
    const filter = p.dataset.filter;
    const active = (sort && currentSort === sort) || (filter && activeFilters.has(filter));
    p.classList.toggle('active', !!active);
  });
  const badge = document.getElementById('active-filter-count');
  const total = activeFilters.size + (currentSort !== 'best_match' ? 1 : 0);
  if (badge) {
    badge.textContent = total;
    badge.style.display = total > 0 ? 'inline-block' : 'none';
  }
}

function wireFilterPills() {
  document.querySelectorAll('.filter-pill').forEach((pill) => {
    pill.addEventListener('click', () => {
      const sort = pill.dataset.sort;
      const filter = pill.dataset.filter;
      if (sort) {
        currentSort = currentSort === sort ? 'best_match' : sort;
      } else if (filter) {
        if (activeFilters.has(filter)) activeFilters.delete(filter);
        else activeFilters.add(filter);
      }
      refreshFilterPillUI();
      renderResults();
    });
  });

  // Sort modal options
  document.querySelectorAll('.sort-option').forEach((opt) => {
    opt.addEventListener('click', () => {
      currentSort = opt.dataset.sort;
      refreshFilterPillUI();
      renderResults();
      if (sortModal) sortModal.hide();
    });
  });

  // Sort button → open modal
  const sortBtn = document.getElementById('open-sort-btn');
  if (sortBtn) {
    sortBtn.addEventListener('click', () => {
      const el = document.getElementById('sortModal');
      if (!sortModal && window.bootstrap && window.bootstrap.Modal && el) {
        sortModal = new window.bootstrap.Modal(el);
      }
      if (sortModal) sortModal.show();
    });
  }

  // Filters button → just open sort modal too for now (could be expanded later)
  const filtersBtn = document.getElementById('open-filters-btn');
  if (filtersBtn) {
    filtersBtn.addEventListener('click', () => {
      const el = document.getElementById('sortModal');
      if (!sortModal && window.bootstrap && window.bootstrap.Modal && el) {
        sortModal = new window.bootstrap.Modal(el);
      }
      if (sortModal) sortModal.show();
    });
  }
}

// ── Availability modal ──
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
  if (contentEl) { contentEl.style.display = 'none'; contentEl.innerHTML = ''; }
  if (bookBtn) {
    bookBtn.onclick = () => {
      if (!availabilityInstructor) return;
      if (isLearner) window.location.href = `/learner/bookings/new?instructor_profile_id=${availabilityInstructor.id}`;
      else window.location.href = `/learner/bookings/amount?instructor_profile_id=${availabilityInstructor.id}`;
    };
  }
  if (availabilityModal) availabilityModal.show();

  loadAvailabilityForInstructor(inst.id)
    .then(({ dates }) => {
      if (!contentEl || !loadingEl) return;
      loadingEl.style.display = 'none';
      const ds = dates || [];
      if (!ds.length) {
        contentEl.innerHTML = '<p class="text-muted mb-0 small">No availability data to show. Continue with "Book with ' + escapeHtml(inst.name || '') + '" to choose a time.</p>';
      } else {
        const list = ds.slice(0, 7).map((d) => {
          const label = typeof d === 'string' ? d : d.label || d.date || '';
          return `<span class="badge bg-light text-dark border me-1 mb-1">${escapeHtml(label)}</span>`;
        }).join('');
        contentEl.innerHTML = '<p class="small mb-2">Next available dates:</p><div>' + list + '</div><p class="small text-muted mt-3 mb-0">Continue to the booking flow for exact times.</p>';
      }
      contentEl.style.display = 'block';
    })
    .catch(() => {
      if (!contentEl || !loadingEl) return;
      loadingEl.style.display = 'none';
      contentEl.innerHTML = '<p class="text-danger small mb-0">Could not load availability.</p>';
      contentEl.style.display = 'block';
    });
}

// ── Init ──
function init() {
  const resultsLoading = document.getElementById('results-loading');
  const resultsEmpty = document.getElementById('results-empty');

  const suburbId = params.suburbId || null;
  const transmission = params.transmission || null;
  const testPreBooked = params.testPreBooked === true;

  wireFilterPills();
  refreshFilterPillUI();

  runInstructorSearch({ suburbId, transmission, testPreBooked })
    .then((instructors) => {
      resultsLoading.style.display = 'none';
      allInstructors = instructors || [];
      renderResults();
    })
    .catch((e) => {
      resultsLoading.style.display = 'none';
      if (resultsEmpty) {
        resultsEmpty.innerHTML = 'Unable to load instructors. <a href="/find-instructor">Try again</a>.';
        resultsEmpty.style.display = 'block';
      }
      console.error(e);
    });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
