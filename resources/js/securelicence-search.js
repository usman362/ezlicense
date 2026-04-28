/**
 * Find-instructor search: suburb autocomplete + filters (transmission, test pre-booked).
 * Wire to your HTML: data-search-suburb, data-search-transmission, data-search-test-booked, data-search-btn, data-results.
 */

import {
  searchSuburbs,
  searchInstructors,
  getAvailabilityDates,
  getAvailabilitySlots,
} from './securelicence-api.js';

function debounce(fn, ms) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), ms);
  };
}

export function initSuburbAutocomplete(inputEl, onSelect) {
  if (!inputEl) return;
  const listId = inputEl.dataset.listId || 'suburb-list';
  let listEl = document.getElementById(listId);
  if (!listEl) {
    listEl = document.createElement('ul');
    listEl.id = listId;
    inputEl.parentNode?.appendChild(listEl);
  }
  listEl.className = 'list-group position-absolute w-100 shadow-lg';
  listEl.style.cssText = 'z-index:1050; max-height:260px; overflow-y:auto; border-radius:0 0 8px 8px; top:100%; left:0;';

  const search = debounce(async (q) => {
    if (!q || q.length < 2) {
      listEl.innerHTML = '';
      listEl.hidden = true;
      return;
    }
    const suburbs = await searchSuburbs(q);
    listEl.innerHTML = suburbs
      .map(
        (s) =>
          `<li class="list-group-item list-group-item-action" data-id="${s.id}" data-label="${(s.label || '').replace(/"/g, '&quot;')}">${s.label}</li>`
      )
      .join('');
    listEl.hidden = suburbs.length === 0;
    listEl.querySelectorAll('li').forEach((li) => {
      li.addEventListener('click', () => {
        const id = li.dataset.id;
        const label = (li.dataset.label || li.textContent || '').replace(/&quot;/g, '"');
        listEl.innerHTML = '';
        listEl.hidden = true;
        onSelect?.(id, label);
        inputEl.value = label;
        inputEl.dataset.suburbId = id;
      });
    });
  }, 250);

  inputEl.addEventListener('input', () => {
    inputEl.removeAttribute('data-suburb-id');
    search(inputEl.value.trim());
  });
  inputEl.addEventListener('blur', () => setTimeout(() => { listEl.hidden = true; }, 200));
}

export async function runInstructorSearch({ suburbId, transmission, testPreBooked, instructorGender }) {
  const instructors = await searchInstructors({
    suburb_id: suburbId || undefined,
    transmission: transmission || undefined,
    test_pre_booked: testPreBooked,
    instructor_gender: instructorGender || undefined,
  });
  return instructors;
}

export async function loadAvailabilityForInstructor(instructorProfileId, date) {
  const [dates, slots] = await Promise.all([
    date ? [] : getAvailabilityDates(instructorProfileId, 30),
    date ? getAvailabilitySlots(instructorProfileId, date) : [],
  ]);
  return { dates, slots };
}
