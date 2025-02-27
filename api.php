<?php
// At the beginning of api.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure no output before this point
ob_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

function extractYoutubeId($input) {
    // If the input contains 'youtube.com' or 'youtu.be', it's a URL
    if (strpos($input, 'youtube.com') !== false || strpos($input, 'youtu.be') !== false) {
        // Parse the URL to get query parameters
        $parsed = parse_url($input);
        
        // If URL contains query parameters
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $params);
            
            // Return the 'v' parameter if it exists
            if (isset($params['v'])) {
                return $params['v'];
            }
        }
        
        // Handle youtu.be format
        if (strpos($input, 'youtu.be/') !== false) {
            $parts = explode('youtu.be/', $input);
            // Remove any additional parameters after video ID
            $videoId = explode('?', $parts[1])[0];
            return $videoId;
        }
    }
    
    // If input doesn't match URL patterns, assume it's already a video ID
    return $input;
}

// Extract parameters
$videoId = $_POST['url'];
$cleanVideoId = extractYoutubeId($videoId);
$videoId = $cleanVideoId;

// API choice
$apiKey_youtube_transcript_io = '';
$api_endpoint = '';
$switched = '';

switch($_POST['endpoint']){
    case 'api-1':
        $api_endpoint = 'https://www.youtube-transcript.io/api/transcripts';
        $apiKey_youtube_transcript_io = 'YOUR-API-KEY';
        $switched = 'api-1';
        break;
    case 'api-2':
        $api_endpoint = 'https://api.supadata.ai/v1/youtube/transcript';
        $apiKey_youtube_transcript_io = 'YOUR-API-KEY';
        $switched = 'api-2';
        break;
    case 'api-3':
        $api_endpoint = 'https://www.youtube-transcript.io/api/transcripts';
        $apiKey_youtube_transcript_io = 'YOUR-API-KEY';
        $switched = 'api-3';
        break;
    case 'api-4':
        $api_endpoint = 'https://api.supadata.ai/v1/youtube/transcript';
        $apiKey_youtube_transcript_io = 'YOUR-API-KEY';
        $switched = 'api-4';
        break;        
    case 'api-5':
        $api_endpoint = 'https://api.supadata.ai/v1/youtube/transcript';
        $apiKey_youtube_transcript_io = 'YOUR-API-KEY';
        $switched = 'api-5';
        break;   
    case 'api-6':
        $api_endpoint = 'https://www.youtube-transcript.io/api/transcripts';
        $apiKey_youtube_transcript_io = 'YOUR-API-KEY';
        $switched = 'api-6';
        break;  
    case 'api-7':
        $api_endpoint = 'https://www.youtube-transcript.io/api/transcripts';
        $apiKey_youtube_transcript_io = 'YOUR-API-KEY';
        $switched = 'api-7';
        break;          
    default:        
        $api_endpoint = 'https://www.youtube-transcript.io/api/transcripts';
        $apiKey_youtube_transcript_io = 'YOUR-API-KEY';
        $switched = 'default';
        break;
}

// API calls for transcript based on endpoint
if (strpos($switched, 'api-') === 0 && $switched === 'api-1' || $switched === 'api-3' || $switched === 'api-6' || $switched === 'api-7') {
    // YouTube Transcript.io API
    $ch = curl_init($api_endpoint);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Basic '. $apiKey_youtube_transcript_io,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode(['ids' => [$videoId]])
    ]);

    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
        exit;
    }

    curl_close($ch);

    // Decode JSON response
    $data = json_decode($response, true);

    // Debugging JSON response
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'JSON Decode Error: ' . json_last_error_msg(), 'success' => false]);
        exit;
    }

    // Ensure response is in expected format
    if (!isset($data[0]['tracks'][0]['transcript'])) {
        echo json_encode(['error' => 'Invalid API response', 'response' => $data, 'success' => false]);
        exit;
    }

    // Extract title
    $title = $data[0]['title'] ?? '';

    // Extract transcript texts
    $transcriptTexts = [];
    foreach ($data[0]['tracks'][0]['transcript'] as $entry) {
        $transcriptTexts[] = $entry['text'];
    }

    // Combine transcript into a single string
    $fullTranscript = implode(". ", $transcriptTexts);

    // Clear output buffer
    ob_end_clean();

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'videoId' => $videoId,
        'title' => $title,
        'transcript' => $fullTranscript,
        'apiPost' => $_POST['endpoint'],
        'switchedApi' => $switched
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;

} elseif (strpos($switched, 'api-') === 0 && in_array($switched, ['api-2', 'api-4', 'api-5'])) {
    // Supadata.ai API
    $api_key = $apiKey_youtube_transcript_io;
    
    // Construct the YouTube URL
    $mainUrl = 'https://www.youtube.com/watch?v=' . $videoId;
    
    // URL-encode the base URL and add the text parameter separately
    $encodedUrl = urlencode($mainUrl) . '&text=true';
    
    // Construct the API endpoint
    $api_endpoint = 'https://api.supadata.ai/v1/youtube/transcript?url=' . $encodedUrl;
    
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $api_endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'x-api-key: ' . $api_key,
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
        exit;
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (!$data || (!isset($data['transcript']) && !isset($data['content']))) {
        echo json_encode([
            'error' => 'Invalid response or no data found',
            'raw_response' => $response,
            'http_code' => $httpCode
        ]);
        exit;
    }
    
    $fullTranscript = $data['transcript'] ?? $data['content'] ?? '';
    
    // Prepare the result
    $result = [
        'videoId' => $videoId,
        'title' => $data['title'] ?? '',
        'transcript' => $fullTranscript,
        'apiPost' => $_POST['endpoint'],
        'switchedApi' => $switched,
        'success' => true
    ];
    
    // Output the result as JSON
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
} else {
    // Default fallback or error handling
    echo json_encode([
        'error' => 'Invalid API endpoint selection',
        'success' => false
    ]);
    exit;
}
