<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Required parameters
    if (!isset($_POST['content'], $_POST['model'], $_POST['url'])) {
        echo json_encode(['success' => false, 'error' => 'Missing required parameters.']);
        exit;
    }
    
    $content = $_POST['content'];
    $model = $_POST['model'];
    $url = $_POST['url'];
    
    // Optional parameters with defaults
    $extra_prompt = isset($_POST['extra_prompt']) ? trim($_POST['extra_prompt']) : '';
    $processType = isset($_POST['processType']) ? $_POST['processType'] : 'summary';
    $outputLanguage = isset($_POST['outputLanguage']) ? $_POST['outputLanguage'] : 'original';
    $useEmojis = isset($_POST['useEmojis']) ? filter_var($_POST['useEmojis'], FILTER_VALIDATE_BOOLEAN) : true;
    $outputTone = isset($_POST['outputTone']) ? $_POST['outputTone'] : 'default';
    $outputFormat = isset($_POST['outputFormat']) ? $_POST['outputFormat'] : 'default';
    $writingStyle = isset($_POST['writingStyle']) ? $_POST['writingStyle'] : 'default';
    
    // If process type is just transcript, return the content directly
    if ($processType === 'transcript') {
        echo json_encode([
            'success' => true, 
            'summary' => $content,
            'source_url' => $url
        ]);
        exit;
    }
    
    // Map OpenAI model names to OpenRouter model names
    $modelMap = OPENROUTER_MODELS;
    
    $openRouterModel = isset($modelMap[$model]) ? $modelMap[$model] : 'openai/gpt-4o-mini';
    
    // Construct language instruction
    $languageInstruction = "";
    if ($outputLanguage !== 'original') {
        $languageInstruction = "Provide the summary in {$outputLanguage} language. ";
    }
    
    // Construct emoji instruction
    $emojiInstruction = $useEmojis 
        ? "Use appropriate emojis to enhance readability, especially for bullet points. " 
        : "Do not use any emojis in the output. ";
        
    // Construct tone instruction
    $toneInstruction = "";
    if ($outputTone !== 'default') {
        $toneInstruction = "Use a {$outputTone} tone in your response. ";
    }
    
    // Construct format instruction
    $formatInstruction = "";
    if ($outputFormat !== 'default') {
        $formatInstruction = "Format the output as a {$outputFormat}. ";
    }
    
    // Construct writing style instruction
    $styleInstruction = "";
    if ($writingStyle !== 'default') {
        $styleInstruction = "Write in an {$writingStyle} style. ";
    }

    // Construct the base prompt - MODIFIED to remove policy-violating instructions
    $base_prompt = "Summarize the following content in a detailed yet concise manner.
    Use headings, subheadings, and markdown for clear formatting.
    Ensure the key points and essence of the text remain intact.
    Use bullet points where appropriate, and avoid overshortening the content.
    Highlight key concepts with bold text.  
    Explain details comprehensively while avoiding repetition.  
    The summary should be structured and easy to read.
    $languageInstruction
    $emojiInstruction
    $toneInstruction
    $formatInstruction
    $styleInstruction";

    // Append extra prompt if provided - in a safe way
    if (!empty($extra_prompt)) {
        $base_prompt .= "\n\nAdditional instructions: " . $extra_prompt;
    }

    // Append URL to the prompt
    $url_prompt = "URL: " . $url . "\n\n";

    // Final prompt including the content and URL
    $final_prompt = $base_prompt . "\n\nContent:\n" . $content . "\n\n" . $url_prompt;

    try {
        // Initialize CURL session for OpenRouter API
        $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
        
        // Prepare the request data for OpenRouter format
        $requestData = [
            'model' => $openRouterModel,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $final_prompt
                ]
            ],
            'max_tokens' => 4096,
            'temperature' => 0.7
        ];
        
        // Set CURL options
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . OPENROUTER_API_KEY,
                'Content-Type: application/json',
                'HTTP-Referer: https://' . OPENROUTER_WEBSITE_DOMAIN,
                'X-Title: YouTube Summarizer'
            ],
            CURLOPT_TIMEOUT => 30,
        ]);
        
        // Execute the request
        $response = curl_exec($ch);
        
        // Log the complete response for debugging
        file_put_contents('api_response_log.txt', date('Y-m-d H:i:s') . " Response:\n" . $response . "\n\n", FILE_APPEND | LOCK_EX);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Check for CURL errors
        if (curl_errno($ch)) {
            throw new Exception('CURL Error: ' . curl_error($ch));
        }
        
        // Close CURL session
        curl_close($ch);
        
        // Process the response
        if ($httpCode == 200) {
            $responseBody = json_decode($response, true);
            
            if (isset($responseBody['choices'][0]['message']['content'])) {
                echo json_encode([
                    'success' => true,
                    'summary' => trim($responseBody['choices'][0]['message']['content']),
                    'source_url' => $url,
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'error' => 'No summary available from the API response.',
                    'debug_info' => $responseBody
                ]);
            }
        } else {
            // Include the actual response in the error
            throw new Exception('API Error: HTTP status code ' . $httpCode . ' - ' . $response);
        }
    } catch (Exception $e) {
        // Log the error
        $logMessage = "[" . date('Y-m-d H:i:s') . "] ";
        $logMessage .= "Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
        file_put_contents('log.txt', $logMessage, FILE_APPEND | LOCK_EX);

        // Return the JSON error response
        echo json_encode([
            'success' => false,
            'error' => 'Failed to get summary from OpenRouter.',
            'message' => $e->getMessage(),
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}