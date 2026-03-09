/**
 * Find-instructor page: suburb autocomplete only. Form submits to /find-instructor/results.
 */
import { initSuburbAutocomplete } from './ezlicense-search.js';

function init() {
  const suburbInput = document.getElementById('suburb-input');
  const formSuburbId = document.getElementById('form-suburb-id');
  const formQ = document.getElementById('form-q');

  if (!suburbInput) return;

  initSuburbAutocomplete(suburbInput, (id, label) => {
    suburbInput.dataset.selected = '1';
    if (formSuburbId) formSuburbId.value = id || '';
    if (formQ) formQ.value = label || suburbInput.value.trim();
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
