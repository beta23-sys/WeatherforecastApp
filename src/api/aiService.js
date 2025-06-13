// aiService.js
import { GoogleGenAI } from "@google/genai";

// Initialize Gemini AI instance
let aiInstance = null;

/**
 * Initializes the Gemini AI instance
 * @param {string} apiKey - Google Gemini API key
 */
const initGeminiAI = (apiKey) => {
  if (!aiInstance && apiKey) {
    aiInstance = new GoogleGenAI({ apiKey });
  }
};

/**
 * Asks the AI a question with optional context
 * @param {string} question - User's question
 * @param {Object} context - Optional context data
 * @param {string} model - Model to use (default: "gemini-2.0-flash")
 * @returns {Promise<string>} - AI response
 */
export const askAI = async (question, context = null, model = "gemini-2.0-flash") => {
  try {
    const apiKey = import.meta.env.VITE_GEMINI_API_KEY;
    
    if (!apiKey) {
      throw new Error('Gemini API key not configured');
    }

    initGeminiAI(apiKey);

    // Build the prompt with optional context
    let prompt = question;
    if (context) {
      prompt = `
        Context:
        ${JSON.stringify(context, null, 2)}
        
        Question: ${question}
        
        Please answer the question using the provided context.
        If the question can't be answered with the context, say so.
      `;
    }

    const response = await aiInstance.models.generateContent({
      model,
      contents: prompt,
    });

    return response.text;
  } catch (error) {
    console.error('Error asking AI:', error);
    throw error;
  }
};

/**
 * Gets a weather summary from AI
 * @param {Object} weatherData - Weather data context
 * @returns {Promise<string>} - AI-generated summary
 */
export const getWeatherSummary = async (weatherData) => {
  const prompt = `
    Generate a concise 2-3 sentence weather summary for ${weatherData.location} using this data:
    - Current temperature: ${weatherData.temp}°C
    - Condition: ${weatherData.condition}
    - High/Low: ${weatherData.high}°C/${weatherData.low}°C
    - Humidity: ${weatherData.humidity}
    - Wind: ${weatherData.windSpeed}
    - UV Index: ${weatherData.uvIndex}
    - Precipitation chance: ${weatherData.precipitation}
    
    Make it friendly and informative for someone planning their day.
  `;

  return askAI(prompt, null, "gemini-2.0-flash");
};

/**
 * Gets clothing recommendations based on weather
 * @param {Object} weatherData - Weather data context
 * @returns {Promise<string>} - AI-generated recommendations
 */
export const getClothingRecommendations = async (weatherData) => {
  const prompt = `
    Based on the following weather conditions in ${weatherData.location}, 
    what clothing would you recommend?
    - Temperature: ${weatherData.temp}°C (High: ${weatherData.high}°C / Low: ${weatherData.low}°C)
    - Weather condition: ${weatherData.condition}
    - Wind: ${weatherData.windSpeed}
    - Precipitation chance: ${weatherData.precipitation}
    
    Provide 3-4 specific clothing recommendations.
  `;

  return askAI(prompt, null, "gemini-2.0-flash");
};