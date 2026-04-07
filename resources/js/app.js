import './bootstrap';

// SecureLicence-style API and search (use from your pages or components)
export { default as api } from './securelicence-api.js';
export {
  initSuburbAutocomplete,
  runInstructorSearch,
  loadAvailabilityForInstructor,
} from './securelicence-search.js';
export {
  searchSuburbs,
  searchInstructors,
  getInstructorProfile,
  getAvailabilityDates,
  getAvailabilitySlots,
  getBookings,
  createBooking,
  getBooking,
  rescheduleBooking,
  cancelBooking,
  submitReview,
  getInstructorDashboardProfile,
  updateInstructorProfile,
  updateInstructorServiceAreas,
  updateInstructorAvailability,
} from './securelicence-api.js';
