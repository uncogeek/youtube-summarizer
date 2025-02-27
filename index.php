<?php
$url = '';
$prompt = '';
if (isset($_GET['url'])) {
    $url = $_GET['url'];
}

if (isset($_GET['prompt'])) {
    $prompt = $_GET['prompt'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube Summarizer</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
        @font-face {
            font-family: 'Vazir';
            src: url('https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/Farsi-Digits-Without-Latin/Vazir-Regular-FD-WOL.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
        }
    
    #resultDiv {
        margin-top: 20px;
        border: 1px solid #ccc;
        padding: 10px;
        white-space: pre-wrap;
        background-color: #f9f9f9;
        font-family: Arial, sans-serif;
    }
    
    #markdownPreview {
        font-family: "Vazir", Arial, sans-serif;
        margin-top: 20px;
        padding: 10px;
        border: 1px solid #ccc;
        background-color: #fff;
        direction: rtl; /* Moved from inline style */
    }
    
    .spinner-border {
        display: none; /* Initially hidden */
    }
    
    /* Print-specific styles */
    @media print {
        /* Hide all elements */
        body * {
            display: none !important;
            
        }
        
        /* Display only the markdownPreview div and its children */
        #markdownPreview, #markdownPreview * {
            display: block !important;
        }
        
        /* Optional: Additional print-specific styling */
        #markdownPreview {
            font-size: 14pt; /* Adjust as needed */
            background-color: white;
            color: black;
            margin: 4px;
            padding: 4px;
            border: none;
            direction: rtl; /* Ensure right-to-left directionality */
        }
        
        /* Ensure headings and lists retain their styles */
        #markdownPreview h1,
        #markdownPreview h2,
        #markdownPreview h3,
        #markdownPreview ul,
        #markdownPreview ol,
        #markdownPreview li,
        #markdownPreview p {
            display: block !important;
        }
    }
    
    .option-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .form-label {
        font-weight: 500;
    }
    
    


</style>

</head>
<body class="container py-4">
    <h3 class="mb-4 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="red" class="bi bi-youtube" viewBox="0 0 16 16">
            <path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"/>
        </svg>
        YouTube Video Summarizer
    </h3> 

    <!-- Input Section -->
    <form id="crawlForm" class="mb-3">
        <!-- Row 1: URL Input -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-4">
                <label for="url" class="form-label">Youtube Video URL:</label>
                <input
                    type="url"
                    id="urlInput"
                    name="url"
                    class="form-control"
                    placeholder="Enter YouTube URL"
                    value="<?php echo $url ?>"
                    required
                >
            </div>
            
            
                                <!-- Provider Selection -->
                <div class="col-md-2">
                    <label for="provider" class="form-label">AI Provider:</label>
                    <select id="provider" name="provider" class="form-select">
                        <option value="openai" selected>OpenAI</option>
                        <option value="openrouter">OpenRouter</option>
                    </select>
                </div>
                
                <!-- Model Selection -->
                <div class="col-md-2">
                    <label for="model" class="form-label">AI Model:</label>
                    <select id="model" name="model" class="form-select" required>
                        <option value="gpt-4o-mini" selected>GPT-4o-mini</option>
                        <option value="gpt-4o">GPT-4o</option>
                    </select>
                </div>
            
            
            
                <!-- API Endpoint Selection -->
                <div class="col-md-2">
            <label for="endpoint" class="form-label">Transcript API:</label>
                    <select id="endpoint" name="endpoint" class="form-select">
                        <option value="api-1">api-1</option>
                        <option value="api-2">api-2 (100)</option>
                        <option value="api-3">api-3</option>
                        <option value="api-4" selected>api-4 (100)</option>
                        <option value="api-5">api-5 (100)</option>
                        <option value="api-6">api-6</option>
                        <option value="api-7">api-7</option>
                    </select>
                </div>            
                    <!-- Emoji
                    
                    Toggle - Button Style with Status -->
                    <div class="col-md-2 d-flex align-items-center mt-4">
                        
                        <input type="checkbox" class="btn-check" id="useEmojis" name="useEmojis" autocomplete="off" checked>
                        <label class="btn btn-outline-secondary" for="useEmojis">
                            <i class="bi bi-emoji-smile me-1"></i> <!-- Requires Bootstrap Icons -->
                            <span class="status-text">Emojis: On</span>
                        </label>
                    </div>

                
                
                
                
        </div>
        


        <!-- Row 2: Options Section -->
        <div class="option-section">
            <div class="row mb-3">
                <!-- Processing Type -->
                <div class="col-md-2">
                    <label for="processType" class="form-label">Processing Type:</label>
                    <select id="processType" name="processType" class="form-select">
                        <option value="summary" selected>Get Summary</option>
                        <option value="transcript">Get Full Transcript</option>
                    </select>
                </div>
                
                <!-- Output Language -->
                <div class="col-md-2">
                    <label for="outputLanguage" class="form-label">Output Language:</label>
                    <select id="outputLanguage" name="outputLanguage" class="form-select">
                        <option value="original" selected>Original</option>
                        <option value="english">English</option>
                        <option value="persian">Persian</option>
                        <option value="arabic">Arabic</option>
                        <option value="french">French</option>
                        <option value="german">German</option>
                        <option value="spanish">Spanish</option>
                        <option value="italian">Italian</option>
                        <option value="russian">Russian</option>
                        <option value="chinese">Chinese</option>
                        <option value="japanese">Japanese</option>
                    </select>
                </div>
                

                

                
                
                <!-- Tone Selection -->
                <div class="col-md-2">
                    <label for="outputTone" class="form-label">Output Tone:</label>
                    <select id="outputTone" name="outputTone" class="form-select">
                        <option value="default" selected>Default</option>
                        <option value="authoritative">Authoritative</option>
                        <option value="clinical">Clinical</option>
                        <option value="cold">Cold</option>
                        <option value="confident">Confident</option>
                        <option value="cynical">Cynical</option>
                        <option value="emotional">Emotional</option>
                        <option value="empathetic">Empathetic</option>
                        <option value="formal">Formal</option>
                        <option value="friendly">Friendly</option>
                        <option value="humorous">Humorous</option>
                        <option value="informal">Informal</option>
                        <option value="ironic">Ironic</option>
                        <option value="optimistic">Optimistic</option>
                        <option value="pessimistic">Pessimistic</option>
                        <option value="playful">Playful</option>
                        <option value="sarcastic">Sarcastic</option>
                        <option value="serious">Serious</option>
                        <option value="sympathetic">Sympathetic</option>
                        <option value="tentative">Tentative</option>
                        <option value="warm">Warm</option>
                    </select>
                </div>
                
                <!-- Output Format -->
                <div class="col-md-2">
                    <label for="outputFormat" class="form-label">Output Format:</label>
                    <select id="outputFormat" name="outputFormat" class="form-select">
                        <option value="default" selected>Default</option>
                        <option value="concise">Concise</option>
                        <option value="step-by-step">Step-by-step</option>
                        <option value="extreme-detail">Extreme Detail</option>
                        <option value="eli5">ELI5</option>
                        <option value="essay">Essay</option>
                        <option value="report">Report</option>
                        <option value="summary">Summary</option>
                        <option value="table">Table</option>
                        <option value="faq">FAQ</option>
                        <option value="listicle">Listicle</option>
                        <option value="interview">Interview</option>
                        <option value="review">Review</option>
                        <option value="news">News</option>
                        <option value="opinion">Opinion</option>
                        <option value="tutorial">Tutorial</option>
                        <option value="case-study">Case Study</option>
                        <option value="profile">Profile</option>
                        <option value="blog">Blog</option>
                        <option value="poem">Poem</option>
                        <option value="script">Script</option>
                        <option value="whitepaper">Whitepaper</option>
                        <option value="ebook">eBook</option>
                        <option value="press-release">Press Release</option>
                        <option value="infographic">Infographic</option>
                        <option value="webinar">Webinar</option>
                        <option value="podcast-script">Podcast Script</option>
                        <option value="email-campaign">Email Campaign</option>
                        <option value="social-media-post">Social Media Post</option>
                        <option value="proposal">Proposal</option>
                        <option value="brochure">Brochure</option>
                        <option value="newsletter">Newsletter</option>
                        <option value="presentation">Presentation</option>
                        <option value="product-description">Product Description</option>
                        <option value="research-paper">Research Paper</option>
                        <option value="speech">Speech</option>
                        <option value="memo">Memo</option>
                        <option value="policy-document">Policy Document</option>
                        <option value="user-guide">User Guide</option>
                        <option value="technical-documentation">Technical Documentation</option>
                        <option value="qa">Q&A</option>
                    </select>
                </div>
                
                <!-- Writing Style -->
                <div class="col-md-2">
                    <label for="writingStyle" class="form-label">Writing Style:</label>
                    <select id="writingStyle" name="writingStyle" class="form-select">
                        <option value="default" selected>Default</option>
                        <option value="academic">Academic</option>
                        <option value="analytical">Analytical</option>
                        <option value="argumentative">Argumentative</option>
                        <option value="conversational">Conversational</option>
                        <option value="creative">Creative</option>
                        <option value="critical">Critical</option>
                        <option value="descriptive">Descriptive</option>
                        <option value="epigrammatic">Epigrammatic</option>
                        <option value="epistolary">Epistolary</option>
                        <option value="expository">Expository</option>
                        <option value="informative">Informative</option>
                        <option value="instructive">Instructive</option>
                        <option value="journalistic">Journalistic</option>
                        <option value="metaphorical">Metaphorical</option>
                        <option value="narrative">Narrative</option>
                        <option value="persuasive">Persuasive</option>
                        <option value="poetic">Poetic</option>
                        <option value="satirical">Satirical</option>
                        <option value="technical">Technical</option>
                    </select>
                </div>
            </div>
            


                


            

        </div>
        
        <!-- Extra Prompt Input and Go Button -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-10">
                <input
                    type="text"
                    id="extra_prompt"
                    name="extra_prompt"
                    class="form-control"
                    value="<?php echo $prompt ?>"
                    placeholder="Enter extra prompt (Optional)"
                    list="thelist"
                >
                <datalist id="thelist">
                    <option value="In English language">
                    <option value="In Persian language">
                    <option value="Give the summary in Persian">
                    <option value="Summarize the key points concisely, providing a maximum of 10 bullet points.">
                    <option value="translate summary to Persian">
                    <option value="Explain in detailed and structured"> 
                    <option value="Only give me a brief in bullets an tell what it says as short as possible">  
                    <option value="Explain in the most way possible. give me a comprehensive summary but not short. the longer the better">  
                </datalist>
            </div>
            <div class="col-md-2 text-md-end mt-2 mt-md-0">
                <button type="submit" id="submit_id" class="btn btn-primary w-100 w-md-auto">Go</button>
            </div>
        </div>
    </form>

    <!-- Action Buttons -->
    <div class="d-flex gap-2 mb-4" id="initdiv">
        <button type="button" id="copyButton" class="btn btn-light border">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
            </svg>
        </button>
        <button type="button" id="rtlToggleButton" class="btn btn-light border">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-text-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-4-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5m4-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-4-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
            </svg>
        </button>
        <button type="button" id="resetButton" class="btn btn-light border">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
            </svg>
        </button>
        <button type="button" id="downloadBtn" class="btn btn-light border">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-word" viewBox="0 0 16 16">
                <path d="M14 4.5V11h-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5zm-6.839 9.688v-.522a1.5 1.5 0 0 0-.117-.641.86.86 0 0 0-.322-.387.86.86 0 0 0-.469-.129.87.87 0 0 0-.471.13.87.87 0 0 0-.32.386 1.5 1.5 0 0 0-.117.641v.522q0 .384.117.641a.87.87 0 0 0 .32.387.9.9 0 0 0 .471.126.9.9 0 0 0 .469-.126.86.86 0 0 0 .322-.386 1.55 1.55 0 0 0 .117-.642m.803-.516v.513q0 .563-.205.973a1.47 1.47 0 0 1-.589.627q-.381.216-.917.216a1.86 1.86 0 0 1-.92-.216 1.46 1.46 0 0 1-.589-.627 2.15 2.15 0 0 1-.205-.973v-.513q0-.569.205-.975.205-.411.59-.627.386-.22.92-.22.535 0 .916.22.383.219.59.63.204.406.204.972M1 15.925v-3.999h1.459q.609 0 1.005.235.396.233.589.68.196.445.196 1.074 0 .634-.196 1.084-.197.451-.595.689-.396.237-.999.237zm1.354-3.354H1.79v2.707h.563q.277 0 .483-.082a.8.8 0 0 0 .334-.252q.132-.17.196-.422a2.3 2.3 0 0 0 .068-.592q0-.45-.118-.753a.9.9 0 0 0-.354-.454q-.237-.152-.61-.152Zm6.756 1.116q0-.373.103-.633a.87.87 0 0 1 .301-.398.8.8 0 0 1 .475-.138q.225 0 .398.097a.7.7 0 0 1 .273.26.85.85 0 0 1 .12.381h.765v-.073a1.33 1.33 0 0 0-.466-.964 1.4 1.4 0 0 0-.49-.272 1.8 1.8 0 0 0-.606-.097q-.534 0-.911.223-.375.222-.571.633-.197.41-.197.978v.498q0 .568.194.976.195.406.571.627.375.216.914.216.44 0 .785-.164t.551-.454a1.27 1.27 0 0 0 .226-.674v-.076h-.765a.8.8 0 0 1-.117.364.7.7 0 0 1-.273.248.9.9 0 0 1-.401.088.85.85 0 0 1-.478-.131.83.83 0 0 1-.298-.393 1.7 1.7 0 0 1-.103-.627zm5.092-1.76h.894l-1.275 2.006 1.254 1.992h-.908l-.85-1.415h-.035l-.852 1.415h-.862l1.24-2.015-1.228-1.984h.932l.832 1.439h.035z"/>
            </svg>
        </button>
        
                <button type="button" id="copyClipboard" class="btn btn-light border"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard" viewBox="0 0 16 16">
  <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z"/>
  <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z"/>
</svg></button>
        
    </div>

    <!-- Spinner -->
    <div id="loadingSpinner" class="text-center mb-3">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Markdown Preview -->
    <div id="markdownPreview" style="direction: rtl;"></div>

    <!-- Include App JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html-docx-js/0.3.1/html-docx.min.js"></script>
    <script src="assets/app.js?v=1.3.4"></script>
    <script>
    $(document).ready(function(){
        
        $('#copyClipboard').on('click', function() {
        // Get the text from the div
        var textToCopy = $('#markdownPreview').text();
        
        // Create a temporary textarea element
        var $temp = $('<textarea>');
        $('body').append($temp);
        
        // Set the value of the textarea to the text
        $temp.val(textToCopy).select();
        
        // Copy the text to clipboard
        document.execCommand('copy');
        
        // Remove the temporary textarea
        $temp.remove();
        
    });
    
        
        // Set display to none for elements with specific IDs
        $('#initdiv').css('visibility', 'hidden');
        $('#markdownPreview').css('visibility', 'hidden');
        
        // RTL Toggle Button
        $('#rtlToggleButton').on('click', function () {
            const currentDirection = $('#markdownPreview').css('direction');
            const newDirection = currentDirection === 'rtl' ? 'ltr' : 'rtl';
            $('#markdownPreview').css('direction', newDirection);
        });

        // Reset Button
        $('#resetButton').on('click', function () {
            $('#urlInput').val('');
            $('#markdownPreview').empty();
            $('#loadingSpinner .spinner-border').hide();
            $('#initdiv').css('visibility', 'hidden');
            $('#markdownPreview').css('visibility', 'hidden');
        });
        
        // Auto-submit if URL is present
        if ($('#urlInput').val().trim() !== '') {
            $('#submit_id').click();
        }
    });
    
    
    // Add this JavaScript to update the button text
document.getElementById('useEmojis').addEventListener('change', function() {
    const statusText = document.querySelector('#useEmojis + label .status-text');
    statusText.textContent = this.checked ? 'Emojis: On' : 'Emojis: Off';
});

    </script>
</body>
</html>
