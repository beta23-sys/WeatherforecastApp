<script setup>
import { ref, onMounted } from 'vue';
import { fetchDailyForecast } from '@/api/weatherService.js';
import { askWeatherAI } from '@/api/aiService';
import { computed } from 'vue';
import '@/styles/home.css';

const isLoggedIn = computed(() => !!localStorage.getItem('userEmail')); // Check if user is logged in by checking localStorage

//
// --- Reactive state ---
//
const searchQuery     = ref('');
const location        = ref('Melbourne');
const isLoading       = ref(false);
const error           = ref(null);

const aiQuery = ref('');
const isAskingAI = ref(false);
const aiError = ref(null);
const aiResponse = ref(null);

const currentWeather  = ref({
  temp:        0,
  condition:   '',
  high:        0,
  low:         0,
  humidity:    'N/A',
  windSpeed:   'N/A',
  pressure:    'N/A',
  visibility:  'N/A',
  uvIndex:     'N/A',
  precipitation:'N/A',
  cloudCover:  'N/A',
  dewPoint:    'N/A'
});

const weatherDetails  = ref({
  humidity:    'N/A',
  windSpeed:   'N/A',
  pressure:    'N/A',
  visibility:  'N/A',
  uvIndex:     'N/A',
  precipitation:'N/A',
  cloudCover:  'N/A',
  dewPoint:    'N/A'
});

const hourlyForecast  = ref([]);
const dailyForecast   = ref([]);


const getWeatherIcon = (condition) => {
  const c = condition.toLowerCase();
  if (c.includes('rain'))        return 'cloud-rain';
  if (c.includes('drizzle'))     return 'cloud-drizzle';
  if (c.includes('snow'))        return 'snowflake';
  if (c.includes('thunder'))     return 'bolt';
  if (c.includes('fog')||c.includes('smog')) return 'smog';
  if (c.includes('cloud'))       return 'cloud';
  if (c.includes('sunny')||c.includes('clear')) return 'sun';
  return 'cloud'; // fallback
};

const getSummarizedCondition = (weatherData) => {
  const values = weatherData.values || {};
  
  // Check for extreme conditions first
  if (values.rainIntensityMax > 10) return 'Heavy Rain';
  if (values.rainIntensityMax > 3) return 'Rain';
  if (values.rainIntensityMax > 0) return 'Light Rain';

  if (values.snowIntensityMax > 0) return 'Snow';
  if (values.freezingRainIntensityMax > 0) return 'Freezing Rain';
  
  // Check cloud cover
  const cloudCover = values.cloudCoverAvg || 0;
  if (cloudCover > 70) {
    // If it's very cloudy, check if it's foggy
    if (values.visibilityAvg < 1) return 'Fog';
    return 'Cloudy';
  }
  if (cloudCover > 30) return 'Partly Cloudy';
  
  // Check for clear conditions
  if (cloudCover <= 30) {
    // Daytime vs nighttime (simplified)
    const now = new Date();
    const sunrise = new Date(values.sunriseTime || now);
    const sunset = new Date(values.sunsetTime || now);
    
    if (now > sunrise && now < sunset) {
      return 'Sunny';
    } else {
      return 'Clear';
    }
  }
  
  // Fallback based on weather code if available
  if (values.weatherCodeMax) {
    return getWeatherCondition(values.weatherCodeMax);
  }
  
  // Default fallback
  return 'Fair';
};

//
// --- Helpers to parse & map API data ---
//
const parseWeatherData = (data) => {
  // Check if the expected data structure exists
  if (!data?.timelines?.daily) return [];
  
  return data.timelines.daily.map(item => {
    // Handle potential missing startTime
    const date = item.time ? new Date(item.time) : new Date();
    
    // Safely access values with fallbacks
    const values = item.values || {};
    
    return {
      startTime: item.time || '',
      date: date.toLocaleDateString('en-US', { 
        month: 'numeric', 
        day: 'numeric', 
        year: 'numeric' 
      }),
      dayName: date.toLocaleDateString('en-US', { weekday: 'long' }),
      high: Math.round(values.temperatureMax || 0),
      low: Math.round(values.temperatureMin || 0),
      weatherCode: values.weatherCode || 0,
      condition: getSummarizedCondition(item),
      icon: getWeatherIcon(getSummarizedCondition(item)),
      humidity: values.humidityAvg ? `${Math.round(values.humidityAvg)}%` : 'N/A',
      windSpeed: values.windSpeedAvg ? `${Math.round(values.windSpeedAvg)} km/h` : 'N/A',
      pressure: values.pressureSurfaceLevelAvg ? `${Math.round(values.pressureSurfaceLevelAvg)} hPa` : 'N/A',
      visibility: values.visibilityAvg ? `${Math.round(values.visibilityAvg)} km` : 'N/A',
      uvIndex: values.uvIndexAvg ? Math.round(values.uvIndexAvg) : '0',
      precipitation: values.precipitationProbabilityAvg ? `${Math.round(values.precipitationProbabilityAvg)}%` : 'N/A',
      cloudCover: values.cloudCoverAvg ? `${Math.round(values.cloudCoverAvg)}%` : 'N/A'
    };
  });
};

const updateWeatherFromService = (weatherData) => {
  if (!weatherData.length) return;
  const today = weatherData[0];
  console.log('Weather data:', today);
  // Current weather card
  currentWeather.value = {
    temp: today.high,
    condition: today.condition,
    high: today.high,
    low: today.low,
    humidity: today.humidity,
    windSpeed: today.windSpeed,
    pressure: today.pressure,
    visibility: today.visibility,
    uvIndex: today.uvIndex,
    precipitation: today.precipitation,
    cloudCover: today.cloudCover,
    dewPoint: 'N/A' // This would need to be calculated or provided by API
  };

  // Update weather details
  weatherDetails.value = {
    humidity: today.humidity,
    windSpeed: today.windSpeed,
    pressure: today.pressure,
    visibility: today.visibility,
    uvIndex: today.uvIndex,
    precipitation: today.precipitation,
    cloudCover: today.cloudCover,
    dewPoint: 'N/A'
  };

  // Generate hourly forecast from daily data (simulated)
  hourlyForecast.value = weatherData.slice(0, 6).map((d, i) => {
    if (i === 0) {
      return {
        time: 'Now',
        temp: d.high,
        icon: d.icon
      };
    }
    
    // Create simulated hourly times
    const hour = new Date();
    hour.setHours(hour.getHours() + (i * 4)); // Every 4 hours
    
    return {
      time: hour.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        hour12: true 
      }),
      temp: Math.round(d.high - (Math.random() * 5)), // Slight variation
      icon: d.icon
    };
  });

  // Daily forecast
  dailyForecast.value = weatherData.map(d => ({
    day: d.dayName,
    date: d.date,
    high: d.high,
    low: d.low,
    condition: d.condition,
    icon: d.icon,
    weatherCode: d.weatherCode
  }));
};

//
// --- Fetch & search handlers ---
//
const fetchWeatherData = async (loc) => {
  isLoading.value = true;
  error.value = null;
  
  try {
    const data = await fetchDailyForecast(loc);
    console.log('Fetched data:', data);
    const parsed = parseWeatherData(data);
    console.log('Parsed data:', parsed);
    updateWeatherFromService(parsed);
  } catch (err) {
    error.value = `Error fetching weather data: ${err.message}`;
    console.error(err);
  } finally {
    isLoading.value = false;
  }
};

const searchLocation = async () => {
  if (!searchQuery.value.trim()) return;
  location.value = searchQuery.value.trim();
  searchQuery.value = '';
  await fetchWeatherData(location.value);
};

const getBackgroundClass = () => {
  const cond = currentWeather.value.condition.toLowerCase();
  if (cond.includes('rain') || cond.includes('drizzle')) return 'bg-rainy';
  if (cond.includes('cloud') || cond.includes('fog')) return 'bg-cloudy';
  if (cond.includes('snow') || cond.includes('ice')) return 'bg-snowy';
  if (cond.includes('thunder')) return 'bg-stormy';
  return 'bg-sunny';
};

onMounted(() => {
  fetchWeatherData(location.value);
});

// Add this new method for AI questions
const askAI = async () => {
  if (!aiQuery.value.trim()) return;
  
  isAskingAI.value = true;
  aiError.value = null;
  aiResponse.value = null;
  
  try {
    // Prepare weather context from current data
    const weatherContext = {
      location: location.value,
      temp: currentWeather.value.temp,
      condition: currentWeather.value.condition,
      high: currentWeather.value.high,
      low: currentWeather.value.low,
      humidity: currentWeather.value.humidity,
      windSpeed: currentWeather.value.windSpeed,
      uvIndex: currentWeather.value.uvIndex,
      precipitation: currentWeather.value.precipitation
    };
    
    // Call the AI service with user question and weather context
    const response = await askWeatherAI(aiQuery.value, weatherContext);
    aiResponse.value = response;
    
    // Clear the input
    aiQuery.value = '';
  } catch (err) {
    aiError.value = `Error asking AI: ${err.message}`;
    console.error('AI Error:', err);
  } finally {
    isAskingAI.value = false;
  }
};
</script>

<template>
  <section>
    <!-- Error Message -->
    <div v-if="error" class="alert alert-danger mb-4" role="alert">
      {{ error }}
    </div>

    <!-- Search Bar -->
    <div class="search-container mb-4">
      <div class="input-group">
        <input 
          type="text" 
          class="form-control search-bar" 
          placeholder="Search location..." 
          v-model="searchQuery"
          @keyup.enter="searchLocation"
          :disabled="isLoading"
        />
        <button 
          class="btn btn-primary search-btn" 
          type="button" 
          @click="searchLocation"
          :disabled="isLoading || !searchQuery.trim()"
        >
          <i class="fas" :class="isLoading ? 'fa-spinner fa-spin' : 'fa-search'"></i>
        </button>
      </div>
    </div>

    <!-- Current Weather -->
    <div class="weather-card current-weather mb-4" :class="getBackgroundClass()">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2 class="location-title mb-0">{{ location }}</h2>
          <p class="last-updated">Last updated: {{ new Date().toLocaleTimeString() }}</p>
        </div>
        <div class="text-end">
          <h1 class="display-1 mb-0">{{ currentWeather.temp }}°</h1>
          <p class="mb-0">{{ currentWeather.condition }}</p>
        </div>
      </div>
      <hr class="my-3"/>
      <div class="d-flex justify-content-between flex-wrap">
        <div class="detail-item">
          <p><i class="fas fa-temperature-high me-2"></i> High: <span class="temp-high">{{ currentWeather.high }}°C</span></p> <!--Temperature-->
              <p><i class="fas fa-temperature-low me-2"></i> Low: <span class="temp-low">{{ currentWeather.low }}°C</span></p>
        </div>
        <div class="detail-item">
          <i class="fas fa-tachometer-alt detail-icon"></i>
          <p>Pressure {{ weatherDetails.pressure }}</p>
        </div>
        <div class="detail-item">
          <i class="fas fa-wind detail-icon"></i>
          <p>Wind {{ weatherDetails.windSpeed }}</p>
        </div>
        <div class="detail-item">
          <i class="fas fa-tint detail-icon"></i>
          <p>Humidity {{ weatherDetails.humidity }}</p>
        </div>
      </div>

      <!-- Hourly Forecast -->
    <div class="mt-4">
        <p class="fw-bold">Quadrennial Forecast</p>
        <div class="hourly-forecast">
          <div 
            v-for="(hour, index) in hourlyForecast" 
            :key="index" 
            class="hourly-item"
          >
            <p class="mb-1">{{ hour.time }}</p>
            <i class="fas" :class="`fa-${hour.icon}`"></i>
            <p class="mb-0 mt-1">{{ hour.temp }}°C</p>
          </div>
        </div>
    
      
    </div>
    </div>

    

    <!-- Daily Forecast -->
    <div class="mb-4">
      <h5 class="mb-3">7-Day Forecast</h5>
      <div class="row">
        <div 
          v-for="day in dailyForecast" 
          :key="day.day" 
          class="col-12 col-sm-6 col-lg-4 mb-3"
        >
          <div class="weather-card text-center p-3 h-100">
            <p class="mb-1 fw-bold">{{ day.day }}</p>
            <p class="mb-2 small text-muted">{{ day.date }}</p>
            <i :class="`fas fa-${day.icon} forecast-icon mb-2`"></i>
            <p class="mb-1">
              <span class="temp-high">{{ day.high }}°</span> / 
              <span class="temp-low">{{ day.low }}°</span>
            </p>
            <p class="mb-0 small">{{ day.condition }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional Weather Details -->
    <div class="weather-card p-3 mb-4">
      <h5 class="mb-3">Weather Details</h5>
      <div class="row">
        <div class="col-6 col-md-3 mb-3">
          <div class="text-center">
            <i class="fas fa-eye detail-icon mb-2"></i>
            <p class="mb-1 small">Visibility</p>
            <p class="mb-0 fw-bold">{{ weatherDetails.visibility }}</p>
          </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
          <div class="text-center">
            <i class="fas fa-sun detail-icon mb-2"></i>
            <p class="mb-1 small">UV Index</p>
            <p class="mb-0 fw-bold">{{ weatherDetails.uvIndex }}</p>
          </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
          <div class="text-center">
            <i class="fas fa-cloud-rain detail-icon mb-2"></i>
            <p class="mb-1 small">Precipitation</p>
            <p class="mb-0 fw-bold">{{ weatherDetails.precipitation }}</p>
          </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
          <div class="text-center">
            <i class="fas fa-cloud detail-icon mb-2"></i>
            <p class="mb-1 small">Cloud Cover</p>
            <p class="mb-0 fw-bold">{{ weatherDetails.cloudCover }}</p>
          </div>
        </div>
      </div>
    </div>

     <!-- Add this new section for the AI feature -->
    <div v-if="isLoggedIn" class="weather-card p-3 mb-4">
      <h5 class="mb-3">Ask AI About the Weather</h5>
      
      <!-- Error Message -->
      <div v-if="aiError" class="alert alert-danger mb-3" role="alert">
        {{ aiError }}
      </div>
      
      <!-- AI Response -->
      <div v-if="aiResponse" class="ai-response mb-3 p-3 bg-light rounded">
        <p class="mb-0">{{ aiResponse }}</p>
      </div>
      
      <!-- Input Form -->
      <div class="input-group">
        <input
          type="text"
          class="form-control ai-input"
          placeholder="Ask anything about the weather..."
          v-model="aiQuery"
          @keyup.enter="askAI"
          :disabled="isAskingAI"
        />
        <button
          class="btn btn-primary ai-btn"
          type="button"
          @click="askAI"
          :disabled="isAskingAI || !aiQuery.trim()"
        >
          <i class="fas" :class="isAskingAI ? 'fa-spinner fa-spin' : 'fa-paper-plane'"></i>
        </button>
      </div>
      
      <p class="small text-muted mt-2 mb-0">
        Example: "Will I need an umbrella today?" or "What's the UV index this weekend?"
      </p>
    </div>
  </section>
</template>