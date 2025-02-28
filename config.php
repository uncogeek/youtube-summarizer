<?php
/**
 * Global Configuration File
 * 
 * This file contains all API configurations and keys required for the application.
 * Please ensure all API keys are kept secure and not committed to version control.
 */

/************************************
 * OpenAI API Configuration
 ************************************/
// Your OpenAI API key for authentication
define('OPENAI_API_KEY', 'your-openai-api-key-here');
// OpenAI's standard API endpoint for completions
define('OPENAI_API_URL', 'https://api.openai.com/v1/completions');

/**
 * OpenAI Proxy Configuration
 * Used when direct API access is restricted or for performance optimization
 */
define('OPENAI_PROXY_URL', null);
// Alternative proxy endpoint - uncomment if needed
// define('OPENAI_PROXY_URL', 'https://api.avalai.ir/v1/completions');

/************************************
 * OpenRouter Configuration
 ************************************/
// Authentication key for OpenRouter services
define('OPENROUTER_API_KEY', 'your-openrouter-api-key-here');
// Required: Your website domain for OpenRouter request identification
define('OPENROUTER_WEBSITE_DOMAIN', 'your-website-domain.com');

/**
 * OpenRouter Model Mapping
 * Maps shorthand names to full model identifiers
 */
define('OPENROUTER_MODELS', [
    'gpt-4o' => 'openai/gpt-4o',
    'gpt-4o-mini' => 'openai/gpt-4o-mini'
]);

/************************************
 * YouTube Transcript API Configuration
 ************************************/
/**
 * Multiple API Keys Strategy
 * Using multiple API keys to maximize free tier usage:
 * - YouTube-Transcript.io: 50 requests/month per key
 * - Supadata.ai: 100 requests/month per key
 */

/**
 * YouTube Transcript API Keys
 * Register at www.youtube-transcript.io for free API keys
 * Quota: 50 requests per month per key
 */
define('YOUTUBE_TRANSCRIPT_API_KEYS', [
    'api-1' => 'your-youtube-transcript.io-api-key',
    'api-3' => 'your-youtube-transcript.io-api-key',
    'api-6' => 'your-youtube-transcript.io-api-key',
    'api-7' => 'your-youtube-transcript.io-api-key',
    'default' => 'your-youtube-transcript.io-api-key'
]);

/**
 * Supadata API Keys
 * Register at supadata.ai for free API keys
 * Quota: 100 requests per month per key
 */
define('SUPADATA_API_KEYS', [
    'api-2' => 'your-supadata-api-key-here',
    'api-4' => 'your-supadata-api-key-here',
    'api-5' => 'your-supadata-api-key-here'
]);

/************************************
 * API Endpoints
 ************************************/
// Base URL for YouTube transcript service
define('YOUTUBE_TRANSCRIPT_BASE_URL', 'https://www.youtube-transcript.io/api/transcripts');
// Base URL for Supadata service
define('SUPADATA_BASE_URL', 'https://api.supadata.ai/v1/youtube/transcript');
