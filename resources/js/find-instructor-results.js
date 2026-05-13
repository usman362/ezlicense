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
let currentSort = 'best_match'; // best_match | rating | price | price_high | next_available
// activeFilters keys: "group:value" e.g. "gender:female", "language:Hindi", "day:next_4_days"
let activeFilters = new Set();
let filtersModal;
let availabilityModal;
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

/**
 * Decide which (single) badge to show on an instructor card, or NONE for normal instructors.
 *
 * Priority (only ONE badge shown — first match wins):
 *   1. 'top'         — exceptional rating + bookings volume
 *   2. 'high_demand' — popular / busy instructor
 *   3. 'great_value' — genuinely cheap AND already proven by some lessons/reviews
 *   4. null          — no badge (this is the default for most instructors)
 *
 * Thresholds are intentionally strict so badges feel meaningful, not spammy.
 */
function getInstructorBadge(inst, allPrices) {
  const rating = Number(inst.average_rating) || 0;
  const reviews = Number(inst.reviews_count) || 0;
  const completed = Number(inst.completed_lessons_count) || 0;
  const price = Number(inst.lesson_price);

  // ── 1. Top Instructor ──
  // Either (a) excellent rating AND many reviews, OR (b) a LOT of completed lessons.
  const isTop = (rating >= 4.8 && reviews >= 30) || completed >= 150;
  if (isTop) return 'top';

  // ── 2. High Demand ──
  // Active instructor with high booking volume OR review activity.
  // (Must have at least one of: lots of lessons OR lots of reviews.)
  const isHighDemand = completed >= 80 || reviews >= 25;
  if (isHighDemand) return 'high_demand';

  // ── 3. Great Value ──
  // Significantly below market median AND proven (≥5 reviews OR ≥10 lessons).
  if (allPrices.length >= 2 && !isNaN(price)) {
    const sorted = [...allPrices].sort((a, b) => a - b);
    const median = sorted[Math.floor(sorted.length / 2)];
    const cheapEnough = price <= median * 0.85; // at least 15% below median
    const proven = reviews >= 5 || completed >= 10;
    if (cheapEnough && proven) return 'great_value';
  }

  // ── 4. No badge (default for most instructors) ──
  return null;
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
  const badgeType = getInstructorBadge(inst, allPrices);

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

  // Decide which top badge to show — single source of truth from getInstructorBadge().
  // Most instructors get NO badge (badgeType === null) — this is intentional.
  let topBadge = '';
  if (badgeType === 'top') {
    topBadge = '<div class="ic-badge-tag ic-badge-top"><i class="bi bi-trophy-fill"></i> Top Instructor</div>';
  } else if (badgeType === 'high_demand') {
    topBadge = '<div class="ic-badge-tag ic-badge-demand"><i class="bi bi-fire"></i> High Demand</div>';
  } else if (badgeType === 'great_value') {
    topBadge = '<div class="ic-badge-tag ic-badge-value"><i class="bi bi-currency-dollar"></i> Great Value</div>';
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
// Helpers to read filter state
function getFilterValuesByGroup(group) {
  const values = new Set();
  activeFilters.forEach((key) => {
    const [g, v] = key.split(':');
    if (g === group) values.add(v);
  });
  return values;
}

function applySortAndFilter(instructors) {
  let list = [...instructors];

  // Gender filter: OR within group
  const genders = getFilterValuesByGroup('gender');
  if (genders.size > 0) {
    list = list.filter((i) => genders.has((i.gender || '').toLowerCase()));
  }
  // Language filter: instructor must have at least one matching language
  const languages = getFilterValuesByGroup('language');
  if (languages.size > 0) {
    list = list.filter((i) => {
      const instLangs = Array.isArray(i.languages) ? i.languages : (i.languages ? String(i.languages).split(',').map((s) => s.trim()) : []);
      return instLangs.some((l) => languages.has(l));
    });
  }
  // (Day / Time / Test Date / Test Centre filters are placeholders — require real availability data to wire up.)

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
      list.sort((a, b) => (Number(b.reviews_count) || 0) - (Number(a.reviews_count) || 0));
      break;
    case 'best_match':
    default:
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
  // Pills at top of page
  document.querySelectorAll('.filter-pill').forEach((p) => {
    const sort = p.dataset.sort;
    const filter = p.dataset.pill; // legacy
    let active = false;
    if (sort && currentSort === sort) active = true;
    if (filter === 'female_only' && activeFilters.has('gender:female')) active = true;
    p.classList.toggle('active', !!active);
  });

  // Filter count badge on "Filters" toolbar button (counts modal filters only, not sort)
  const badge = document.getElementById('active-filter-count');
  const total = activeFilters.size;
  if (badge) {
    badge.textContent = total;
    badge.style.display = total > 0 ? 'inline-block' : 'none';
  }

  // Modal checkboxes — sync with state
  document.querySelectorAll('#filtersModal input[type=checkbox][data-filter-group]').forEach((cb) => {
    const key = `${cb.dataset.filterGroup}:${cb.value}`;
    cb.checked = activeFilters.has(key);
  });

  // Sort dropdown: show checkmark on selected option
  document.querySelectorAll('.sort-option-item').forEach((opt) => {
    opt.classList.toggle('selected', opt.dataset.sort === currentSort);
  });
}

function wireFilterPills() {
  // Top filter pills
  document.querySelectorAll('.filter-pill').forEach((pill) => {
    pill.addEventListener('click', () => {
      const sort = pill.dataset.sort;
      const pillId = pill.dataset.pill;
      if (sort) {
        currentSort = currentSort === sort ? 'best_match' : sort;
      } else if (pillId === 'female_only') {
        const key = 'gender:female';
        if (activeFilters.has(key)) activeFilters.delete(key);
        else activeFilters.add(key);
      }
      refreshFilterPillUI();
      renderResults();
    });
  });

  // ── Sort dropdown ──
  const sortBtn = document.getElementById('open-sort-btn');
  const sortMenu = document.getElementById('sort-dropdown-menu');
  if (sortBtn && sortMenu) {
    // Ensure menu doesn't overflow viewport — reposition after opening
    function positionMenu() {
      // Reset any custom inline positioning so CSS defaults apply first
      sortMenu.style.left = '';
      sortMenu.style.right = '';
      const rect = sortMenu.getBoundingClientRect();
      const vw = document.documentElement.clientWidth;
      if (rect.left < 8) {
        // Spilling off left edge — switch from right-anchored to left-anchored,
        // and offset so the menu sits at viewport edge with 8px gutter
        const btnRect = sortBtn.getBoundingClientRect();
        sortMenu.style.right = 'auto';
        // shift so menu's left edge is 8px from viewport
        sortMenu.style.left = (8 - btnRect.left) + 'px';
      } else if (rect.right > vw - 8) {
        sortMenu.style.left = 'auto';
        sortMenu.style.right = '0px';
      }
    }
    sortBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const wasHidden = sortMenu.hidden;
      sortMenu.hidden = !wasHidden;
      sortBtn.setAttribute('aria-expanded', String(wasHidden));
      if (wasHidden) {
        // menu just opened — adjust position next frame
        requestAnimationFrame(positionMenu);
      }
    });
    window.addEventListener('resize', () => { if (!sortMenu.hidden) positionMenu(); });
    document.addEventListener('click', (e) => {
      if (!sortBtn.contains(e.target) && !sortMenu.contains(e.target)) {
        sortMenu.hidden = true;
        sortBtn.setAttribute('aria-expanded', 'false');
      }
    });
    document.querySelectorAll('.sort-option-item').forEach((opt) => {
      opt.addEventListener('click', () => {
        currentSort = opt.dataset.sort;
        sortMenu.hidden = true;
        sortBtn.setAttribute('aria-expanded', 'false');
        refreshFilterPillUI();
        renderResults();
      });
    });
  }

  // ── Filters modal ──
  const filtersBtn = document.getElementById('open-filters-btn');
  if (filtersBtn) {
    filtersBtn.addEventListener('click', () => {
      const el = document.getElementById('filtersModal');
      if (!filtersModal && window.bootstrap && window.bootstrap.Modal && el) {
        filtersModal = new window.bootstrap.Modal(el);
      }
      populateFiltersModal();
      if (filtersModal) filtersModal.show();
    });
  }
  document.querySelectorAll('#filtersModal input[type=checkbox][data-filter-group]').forEach((cb) => {
    cb.addEventListener('change', () => {
      const key = `${cb.dataset.filterGroup}:${cb.value}`;
      if (cb.checked) activeFilters.add(key); else activeFilters.delete(key);
      refreshFilterPillUI();
      refreshFiltersModalChips();
      renderResults();
      updateShowCount();
    });
  });
  const showBtn = document.getElementById('fi-show-btn');
  if (showBtn) showBtn.addEventListener('click', () => { if (filtersModal) filtersModal.hide(); });
}

// ── Filters modal: populate counts + language list ──
function populateFiltersModal() {
  const subtitle = document.getElementById('filters-modal-subtitle');
  if (subtitle) {
    const trans = (params.transmission || '').toLowerCase();
    const transLabel = trans === 'manual' ? 'Manual ' : (trans === 'auto' ? 'Auto ' : '');
    const loc = params.locationLabel || 'your area';
    subtitle.textContent = `${transLabel}Instructors in ${loc}`;
  }

  // Counts by gender (count from raw data, not filtered)
  const counts = { gender: { male: 0, female: 0, 'non-binary': 0 }, day: {}, time: {}, language: {} };
  allInstructors.forEach((i) => {
    const g = (i.gender || '').toLowerCase();
    if (counts.gender[g] !== undefined) counts.gender[g]++;
    // Languages (if API exposes them in future)
    const langs = Array.isArray(i.languages) ? i.languages : (i.languages ? String(i.languages).split(',').map((s) => s.trim()) : []);
    langs.forEach((l) => { if (l) counts.language[l] = (counts.language[l] || 0) + 1; });
  });
  // Day/Time counts — fallback to total available (no real availability data wired yet)
  ['next_4_days', 'next_7_days', 'weekend', 'select_dates'].forEach((d) => counts.day[d] = allInstructors.length);
  counts.time.am = allInstructors.length;
  counts.time.pm = allInstructors.length;

  document.querySelectorAll('.fi-count[data-count-for]').forEach((el) => {
    const [group, value] = (el.dataset.countFor || '').split(':');
    el.textContent = (counts[group] && counts[group][value]) || 0;
  });

  // Render language checkboxes (alphabetical)
  const langWrap = document.getElementById('fi-language-list');
  if (langWrap) {
    const langs = Object.keys(counts.language).sort();
    if (langs.length === 0) {
      langWrap.innerHTML = '<p class="text-muted small mb-0">No language data available for these instructors yet.</p>';
    } else {
      langWrap.innerHTML = langs.map((l) => {
        const key = `language:${l}`;
        return `<label class="fi-check"><input type="checkbox" data-filter-group="language" value="${escapeHtml(l)}"${activeFilters.has(key) ? ' checked' : ''}><span>${escapeHtml(l)}</span><span class="fi-count">${counts.language[l]}</span></label>`;
      }).join('');
      // Re-wire change handlers for newly-added checkboxes
      langWrap.querySelectorAll('input[type=checkbox]').forEach((cb) => {
        cb.addEventListener('change', () => {
          const key = `${cb.dataset.filterGroup}:${cb.value}`;
          if (cb.checked) activeFilters.add(key); else activeFilters.delete(key);
          refreshFilterPillUI();
          refreshFiltersModalChips();
          renderResults();
          updateShowCount();
        });
      });
    }
  }

  refreshFiltersModalChips();
  updateShowCount();
}

function refreshFiltersModalChips() {
  const chipsWrap = document.getElementById('fi-active-chips');
  if (!chipsWrap) return;
  if (activeFilters.size === 0) { chipsWrap.innerHTML = ''; return; }
  chipsWrap.innerHTML = Array.from(activeFilters).map((key) => {
    const [, value] = key.split(':');
    const label = value.replace(/_/g, ' ').replace(/\b\w/g, (m) => m.toUpperCase());
    return `<button type="button" class="fi-chip" data-remove-filter="${escapeHtml(key)}"><i class="bi bi-x"></i> ${escapeHtml(label)}</button>`;
  }).join('');
  chipsWrap.querySelectorAll('[data-remove-filter]').forEach((b) => {
    b.addEventListener('click', () => {
      activeFilters.delete(b.dataset.removeFilter);
      refreshFilterPillUI();
      refreshFiltersModalChips();
      renderResults();
      updateShowCount();
    });
  });
}

function updateShowCount() {
  const el = document.getElementById('fi-show-count');
  if (!el) return;
  el.textContent = applySortAndFilter(allInstructors).length;
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
