/**
 * Homepage search: suburb autocomplete + form submission to find-instructor results.
 */
import { initSuburbAutocomplete } from './ezlicense-search.js';

function init() {
  const suburbInput = document.getElementById('home-suburb-input');
  const formSuburbId = document.getElementById('home-suburb-id');
  const formQ = document.getElementById('home-q');
  const form = document.getElementById('home-search-form');

  if (!suburbInput) return;

  initSuburbAutocomplete(suburbInput, (id, label) => {
    suburbInput.dataset.selected = '1';
    if (formSuburbId) formSuburbId.value = id || '';
    if (formQ) formQ.value = label || suburbInput.value.trim();
  });

  // Keep q in sync as user types
  suburbInput.addEventListener('input', () => {
    if (suburbInput.dataset.selected) {
      delete suburbInput.dataset.selected;
      return;
    }
    if (formSuburbId) formSuburbId.value = '';
    if (formQ) formQ.value = suburbInput.value.trim();
  });

  // Validate before submit
  if (form) {
    form.addEventListener('submit', (e) => {
      const q = (suburbInput.value || '').trim();
      if (!q) {
        e.preventDefault();
        suburbInput.classList.add('is-invalid');
        suburbInput.focus();
        return;
      }
      suburbInput.classList.remove('is-invalid');
      if (formQ) formQ.value = q;
    });
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
