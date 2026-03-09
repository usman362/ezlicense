import './bootstrap';

// EzLicence-style API and search (use from your pages or components)
export { default as api } from './ezlicense-api.js';
export {
  initSuburbAutocomplete,
  runInstructorSearch,
  loadAvailabilityForInstructor,
} from './ezlicense-search.js';
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
} from './ezlicense-api.js';
