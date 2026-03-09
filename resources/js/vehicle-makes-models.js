/**
 * Car makes and models for instructor vehicle form.
 * Make = company name; Model = models for that make.
 */
export const VEHICLE_MAKES_MODELS = {
  Toyota: ['Corolla', 'Camry', 'Yaris', 'RAV4', 'Hilux', 'Land Cruiser', 'Kluger', 'Prado', 'C-HR', 'Fortuner'],
  Tesla: ['Model S', 'Model 3', 'Model X', 'Model Y'],
  Mazda: ['Mazda3', 'Mazda6', 'CX-5', 'CX-3', 'CX-30', 'CX-60', 'BT-50'],
  Ford: ['Ranger', 'Focus', 'Fiesta', 'Everest', 'Puma', 'Escape'],
  Hyundai: ['i30', 'i20', 'Tucson', 'Kona', 'Santa Fe', 'Venue'],
  Kia: ['Cerato', 'Sportage', 'Seltos', 'Stonic', 'Carnival', 'Sorento'],
  Honda: ['Civic', 'Accord', 'CR-V', 'HR-V', 'Jazz'],
  Nissan: ['X-Trail', 'Navara', 'Qashqai', 'Juke', 'Patrol', 'Leaf'],
  Volkswagen: ['Golf', 'Polo', 'Tiguan', 'T-Roc', 'Amarok'],
  Subaru: ['Outback', 'Forester', 'XV', 'Impreza', 'BRZ'],
  Mitsubishi: ['Triton', 'Outlander', 'ASX', 'Eclipse Cross', 'Pajero Sport'],
  Isuzu: ['D-Max', 'MU-X'],
  BMW: ['3 Series', '5 Series', '1 Series', 'X1', 'X3'],
  'Mercedes-Benz': ['A-Class', 'C-Class', 'E-Class', 'GLA', 'GLC'],
  Audi: ['A3', 'A4', 'A6', 'Q3', 'Q5'],
  Holden: ['Commodore', 'Colorado', 'Trax', 'Equinox'],
  MG: ['MG3', 'ZS', 'HS', 'MG4'],
  BYD: ['Atto 3', 'Seal', 'Dolphin', 'Han'],
  Suzuki: ['Swift', 'Baleno', 'Vitara', 'Jimny', 'S-Cross'],
  Skoda: ['Octavia', 'Fabia', 'Kodiaq', 'Kamiq'],
  Volvo: ['XC40', 'XC60', 'S60', 'V60'],
  Jeep: ['Compass', 'Renegade', 'Wrangler', 'Grand Cherokee'],
  'Land Rover': ['Discovery', 'Defender', 'Range Rover Evoque'],
  Lexus: ['IS', 'ES', 'NX', 'RX', 'UX'],
  Peugeot: ['208', '308', '2008', '3008'],
  Renault: ['Clio', 'Megane', 'Captur', 'Koleos'],
  GWM: ['Haval H6', 'Cannon', 'Ute'],
  LDV: ['T60', 'D90', 'eT60'],
};

export function getMakes() {
  return Object.keys(VEHICLE_MAKES_MODELS).sort();
}

export function getModelsForMake(make) {
  if (!make) return [];
  return VEHICLE_MAKES_MODELS[make] || [];
}
