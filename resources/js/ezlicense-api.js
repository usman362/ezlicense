/**
 * EzLicence-style API client for same-origin requests (session auth).
 * All routes are under /api and use GET/POST/PUT with CSRF token for mutations.
 */

import axios from 'axios';

const api = axios.create({
  baseURL: '/api',
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

// Send CSRF token and session cookie (same origin)
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
  api.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

export default api;

// ——— Public ———

export async function searchSuburbs(q) {
  const { data } = await api.get('/suburbs/search', { params: { q: q || '' } });
  return data.data || [];
}

export async function searchInstructors({ suburb_id, transmission, test_pre_booked } = {}) {
  const params = {};
  if (suburb_id) params.suburb_id = suburb_id;
  if (transmission) params.transmission = transmission;
  if (test_pre_booked != null) params.test_pre_booked = test_pre_booked ? 1 : 0;
  const { data } = await api.get('/instructors', { params });
  return data.data || [];
}

export async function getInstructorProfile(instructorProfileId) {
  const { data } = await api.get(`/instructors/${instructorProfileId}`);
  return data.data;
}

export async function getAvailabilityDates(instructorProfileId, days = 30) {
  const { data } = await api.get(`/instructors/${instructorProfileId}/availability/dates`, {
    params: { days },
  });
  return data.data || [];
}

export async function getAvailabilitySlots(instructorProfileId, date) {
  const { data } = await api.get(`/instructors/${instructorProfileId}/availability/slots`, {
    params: { date },
  });
  return data.data || [];
}

// ——— Auth required ———

export async function getBookings(params = {}) {
  const { data } = await api.get('/bookings', { params });
  return data;
}

export async function createBooking(payload) {
  const { data } = await api.post('/bookings', payload);
  return data.data;
}

export async function getBooking(bookingId) {
  const { data } = await api.get(`/bookings/${bookingId}`);
  return data.data;
}

export async function rescheduleBooking(bookingId, scheduled_at) {
  const { data } = await api.put(`/bookings/${bookingId}/reschedule`, { scheduled_at });
  return data.data;
}

export async function cancelBooking(bookingId, cancellation_reason) {
  const { data } = await api.put(`/bookings/${bookingId}/cancel`, { cancellation_reason });
  return data.data;
}

export async function submitReview({ booking_id, rating, comment }) {
  const { data } = await api.post('/reviews', { booking_id, rating, comment });
  return data.data;
}

// ——— Instructor ———

export async function getInstructorDashboardProfile() {
  const { data } = await api.get('/instructor/profile');
  return data.data;
}

export async function updateInstructorProfile(payload) {
  await api.put('/instructor/profile', payload);
}

export async function uploadInstructorProfilePhoto(file) {
  const formData = new FormData();
  formData.append('profile_photo', file);
  const { data } = await api.post('/instructor/profile/photo', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return data.data;
}

export async function uploadInstructorVehiclePhoto(file) {
  const formData = new FormData();
  formData.append('vehicle_photo', file);
  const { data } = await api.post('/instructor/profile/vehicle-photo', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return data.data;
}

export async function updateInstructorServiceAreas(suburb_ids) {
  await api.put('/instructor/profile/service-areas', { suburb_ids });
}

export async function updateInstructorAvailability(slots) {
  await api.put('/instructor/profile/availability', { slots });
}

export async function updateInstructorCalendarSettings(payload) {
  await api.put('/instructor/profile/calendar-settings', payload);
}

export async function updateInstructorBanking(payload) {
  await api.put('/instructor/profile/banking', payload);
}
