// src/api/weatherService.js
const API_KEY = import.meta.env.VITE_TOMORROW_API_KEY;
if (!API_KEY) throw new Error('Missing Tomorrow.io API key');

/**
 * Fetch a 5-day daily forecast from Tomorrow.io
 * @param {string} location  e.g. "melbourne" or "42.35,-71.05"
 * @returns {Promise<Object>}
 */
export async function fetchDailyForecast(location) {
  // Build query-string exactly as the docs show
  console.log('🔑 API key is', API_KEY);
  const params = new URLSearchParams({
    location,           // city name, zip, or "lat,lon"
    timesteps: '1d',    // daily blocks
    units: 'metric',    // Celsius
    apikey: API_KEY     // ← your key here
  });

  const url = `https://api.tomorrow.io/v4/weather/forecast?${params.toString()}`;
  const res = await fetch(url, {
    method: 'GET',
    headers: {
      accept: 'application/json',
      'accept-encoding': 'deflate, gzip, br'
    }
  });

  if (!res.ok) {
    // give yourself full visibility on the status/text
    throw new Error(`Weather API error ${res.status}: ${res.statusText}`);
  }
  return res.json();  // contains .timelines.daily[] :contentReference[oaicite:0]{index=0}
}
