// assets/app.js

// Load additional scripts if needed
function loadScript(url, callback) {
    var script = document.createElement("script")
    script.type = "text/javascript";
    script.onload = function() {
        callback();
    };
    script.src = url;
    document.getElementsByTagName("head")[0].appendChild(script);
}

// Download as DOCX functionality
$("#downloadBtn").click(function() {
    var content = document.getElementById("markdownPreview").innerHTML;
    
    // Regular expression to check for Persian characters (Unicode range: 0600–06FF)
    var persianRegex = /[\u0600-\u06FF]/;
    
    // Check if the content contains any Persian characters
    var hasPersian = persianRegex.test(content);
    var directionCheck = hasPersian ? 'rtl' : 'ltr';
    
    var fullHtml = `<!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <style>
                            body { direction: ${directionCheck}; font-family: vazir, iransans, iransansfanum, Arial, sans-serif; }
                        </style>
                    </head>
                    <body>${content}</body>
                    </html>`;

    if (typeof htmlDocx === 'undefined') {
        loadScript("https://cdn.jsdelivr.net/npm/html-docx-js@0.3.1/dist/html-docx.min.js", function() {
            convertToDocx(fullHtml);
        });
    } else {
        convertToDocx(fullHtml);
    }
});

function convertToDocx(fullHtml) {
    setTimeout(function() {
        if (typeof htmlDocx !== 'undefined') {
            try {
                var converted = htmlDocx.asBlob(fullHtml);
                var link = document.createElement("a");
                link.href = URL.createObjectURL(converted);
                link.download = "youtube_summary.docx"; 
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error) {
                console.error('Error during conversion:', error);
                alert('An error occurred while generating the DOCX file. Please try again.');
            }
        } else {
            console.error('htmlDocx is still not defined after loading. There might be an issue with the library.');
            alert('Unable to generate DOCX. The required library could not be loaded. Please check your internet connection and try again.');
        }
    }, 500);
}

// Markdown to HTML conversion
function markdownCleanerResult(data){
    // Retrieve raw HTML content
    var rawHtmlContent = data;

    // Decode HTML entities (including &amp;gt;)
    var decodedMarkdown = rawHtmlContent
        .replace(/&amp;/g, '&')  // Decode &amp;
        .replace(/&gt;/g, '>')   // Decode &gt;
        .replace(/&lt;/g, '<');  // Decode &lt;

    // Convert Markdown syntax to HTML
    var htmlText = decodedMarkdown
        // Headings
        .replace(/^######\s*(.+)/gm, '<h6>$1</h6>')
        .replace(/^#####\s*(.+)/gm, '<h5>$1</h5>')
        .replace(/^####\s*(.+)/gm, '<h4>$1</h4>')
        .replace(/^###\s*(.+)/gm, '<h3>$1</h3>')
        .replace(/^##\s*(.+)/gm, '<h2>$1</h2>')
        .replace(/^#\s*(.+)/gm, '<h1>$1</h1>')
        // Bold and Italic
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/__(.+?)__/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/_(.+?)_/g, '<em>$1</em>')
        // Blockquote (multi-line and nested support)
        .replace(/(^|\n)> ?(.*(?:\n(?!\n)[^>].*)*)/g, function(match, prefix, content) {
            let cleanedContent = content.replace(/^> ?/gm, ''); // Remove leading '>' and optional spaces
            return prefix + '<blockquote>' + cleanedContent + '</blockquote>';
        })
        // Unordered List Items - Add a specific class to differentiate
        .replace(/^\s*[-*]\s+(.+)/gm, '<li class="ul-item">$1</li>')
        // Ordered List Items - Add a specific class to differentiate
        .replace(/^\s*\d+\.\s+(.+)/gm, '<li class="ol-item">$1</li>')
        // Wrap consecutive unordered list items with <ul>
        .replace(/(<li class="ul-item">[^<]+<\/li>)+/g, function(match) {
            return '<ul>' + match + '</ul>';
        })
        // Wrap consecutive ordered list items with <ol>
        .replace(/(<li class="ol-item">[^<]+<\/li>)+/g, function(match) {
            return '<ol>' + match + '</ol>';
        })
        // Inline Code
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        // Horizontal Rule
        .replace(/^\s*(---|\*\*\*)\s*$/gm, '<hr>')
        // Links
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>')
        // Images
        .replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img alt="$1" src="$2">')
        // Remove unnecessary nested list tags (optional, based on new implementation)
        .replace(/<\/(ul|ol)>\s*<\1>/g, '');

    // Optional: Remove the specific classes after wrapping if not needed
    htmlText = htmlText
        .replace(/<li class="ul-item">/g, '<li>')
        .replace(/<li class="ol-item">/g, '<li>');

    // Return the converted HTML
    return htmlText;
}

document.addEventListener('DOMContentLoaded', function () {
    let isRTL = false;

    // Form Submission Handling
    $('#crawlForm').on('submit', function (event) {
        event.preventDefault();

        const url = $('#urlInput').val().trim();
        const model = $('#model').val();
        const endpoint = $('#endpoint').val();
        const extraPrompt = $('#extra_prompt').val().trim();
        const processType = $('#processType').val();
        const outputLanguage = $('#outputLanguage').val();
        const provider = $('#provider').val();
        const useEmojis = $('#useEmojis').is(':checked');
        const outputTone = $('#outputTone').val();
        const outputFormat = $('#outputFormat').val();
        const writingStyle = $('#writingStyle').val();

        // Validate that a model is selected
        if (!model) {
            alert('Please select a model from the dropdown.');
            return;
        }

        // Show spinner and clear previous result
        $('#loadingSpinner .spinner-border').show();
        $('#markdownPreview').empty();

        // Send AJAX request to api.php to get the transcript
        $.ajax({
            url: 'api.php',
            type: 'POST',
            data: { url: url, endpoint: endpoint },
            dataType: 'text',  // Use 'text' to get raw response
            success: function(response) {
                try {
                    // Attempt to parse the response
                    let parsedResponse = JSON.parse(response);
                    
                    // Log the parsed response for debugging
                    console.log("Parsed API Response:", parsedResponse);

                    if (parsedResponse.transcript && parsedResponse.success) {
                        // If process type is just transcript, show it directly
                        if (processType === 'transcript') {
                            $('#loadingSpinner .spinner-border').hide();
                            
                            // Format the transcript text with basic paragraph spacing
                            let formattedTranscript = parsedResponse.transcript
                                .replace(/\.\s+/g, '.\n\n')  // Add paragraph breaks after periods
                                .replace(/\?\s+/g, '?\n\n')  // Add paragraph breaks after question marks
                                .replace(/\!\s+/g, '!\n\n'); // Add paragraph breaks after exclamation marks
                                
                            // Check for Persian text to set direction
                            var persianRegex = /[\u0600-\u06FF]/;
                            var hasPersian = persianRegex.test(formattedTranscript);
                            
                            $('#markdownPreview').css('direction', hasPersian ? 'rtl' : 'ltr');
                            $('#markdownPreview').html(formattedTranscript)
                                .append(`<div class="mt-4"><a href='${url}' target="_blank">Source: ${url}</a></div>`);
                                
                            $('#initdiv').css('visibility', 'visible');
                            $('#markdownPreview').css('visibility', 'visible');
                            return;
                        }
                        
                        // Prepare data for AI processing
                        const aiData = {
                            content: parsedResponse.transcript,
                            model: model,
                            url: url,
                            extra_prompt: extraPrompt,
                            processType: processType,
                            outputLanguage: outputLanguage,
                            useEmojis: useEmojis,
                            outputTone: outputTone,
                            outputFormat: outputFormat,
                            writingStyle: writingStyle
                        };
                        
                        // Determine which API endpoint to use based on provider selection
                        const apiEndpoint = provider === 'openai' ? 'openai.php' : 'openrouter.php';

                        // Send content and additional data for summarization
                        $.ajax({
                            url: apiEndpoint,
                            type: 'POST',
                            data: aiData,
                            dataType: 'json',
                            success: function(aiResponse) {
                                $('#loadingSpinner .spinner-border').hide();

                                if (aiResponse.success) {
                                    // Regular expression to check for Persian characters (Unicode range: 0600–06FF)
                                    var persianRegex = /[\u0600-\u06FF]/;

                                    // Check if the content contains any Persian characters
                                    var hasPersian = persianRegex.test(aiResponse.summary);
                                    
                                    if(hasPersian){
                                        $('#markdownPreview').css('direction', 'rtl');
                                    } else {
                                        $('#markdownPreview').css('direction', 'ltr');
                                    }

                                    $('#markdownPreview').html(markdownCleanerResult(aiResponse.summary))
                                        .append(`<div class="mt-4"><a href='${url}' target="_blank">Source: ${url}</a></div>`);
                                    $('#initdiv').css('visibility', 'visible');
                                    $('#markdownPreview').css('visibility', 'visible');
                                } else {
                                    $('#markdownPreview').text(`Error: ${aiResponse.error}`);
                                    if (aiResponse.message) {
                                        console.error('API Error Message:', aiResponse.message);
                                    }
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error("AI request failed:", textStatus, errorThrown);
                                $('#markdownPreview').text(`Error: AI request failed - ${textStatus}`);
                                $('#loadingSpinner .spinner-border').hide();
                            }
                        });
                    } else {
                        console.error("Unexpected response format:", parsedResponse);
                        $('#markdownPreview').text("Error: No transcript or success flag found in the API response");
                        $('#loadingSpinner .spinner-border').hide();
                    }
                } catch (error) {
                    console.error("Error parsing API response:", error);
                    console.log("Problematic JSON string:", response);
                    
                    // Attempt to clean the response and parse again
                    try {
                        let cleanedResponse = response.trim().replace(/^\s+|\s+$/g, '');
                        let parsedResponse = JSON.parse(cleanedResponse);
                        console.log("Parsed cleaned API Response:", parsedResponse);
                        
                        if (parsedResponse.transcript && parsedResponse.success) {
                            // Reuse the successful path from above
                            const aiData = {
                                content: parsedResponse.transcript,
                                model: model,
                                url: url,
                                extra_prompt: extraPrompt,
                                processType: processType,
                                outputLanguage: outputLanguage,
                                useEmojis: useEmojis,
                                outputTone: outputTone,
                                outputFormat: outputFormat,
                                writingStyle: writingStyle
                            };
                            
                            // Determine which API endpoint to use
                            const apiEndpoint = provider === 'openai' ? 'openai.php' : 'openrouter.php';
                            
                            // Send to AI API with the same logic as above
                            $.ajax({
                                url: apiEndpoint,
                                type: 'POST',
                                data: aiData,
                                dataType: 'json',
                                success: function(aiResponse) {
                                    $('#loadingSpinner .spinner-border').hide();
                                    
                                    if (aiResponse.success) {
                                        var persianRegex = /[\u0600-\u06FF]/;
                                        var hasPersian = persianRegex.test(aiResponse.summary);
                                        
                                        $('#markdownPreview').css('direction', hasPersian ? 'rtl' : 'ltr');
                                        $('#markdownPreview').html(markdownCleanerResult(aiResponse.summary))
                                            .append(`<div class="mt-4"><a href='${url}' target="_blank">Source: ${url}</a></div>`);
                                        $('#initdiv').css('visibility', 'visible');
                                        $('#markdownPreview').css('visibility', 'visible');
                                    } else {
                                        $('#markdownPreview').text(`Error: ${aiResponse.error}`);
                                    }
                                },
                                error: function() {
                                    $('#loadingSpinner .spinner-border').hide();
                                    $('#markdownPreview').text("Error: AI processing failed");
                                }
                            });
                        } else {
                            $('#markdownPreview').text("Error: No transcript or success flag found after cleaning response");
                            $('#loadingSpinner .spinner-border').hide();
                        }
                    } catch (secondError) {
                        console.error("Error parsing cleaned API response:", secondError);
                        $('#markdownPreview').text("Error: Unable to parse API response from api.php");
                        $('#loadingSpinner .spinner-border').hide();
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX request to api.php failed:", textStatus, errorThrown);
                console.log("Raw error response:", jqXHR.responseText);
                $('#markdownPreview').text(`Error: api.php request failed - ${textStatus}`);
                $('#loadingSpinner .spinner-border').hide();
            }
        });
    });

    // Copy Button Functionality
    document.getElementById('copyButton').addEventListener('click', function () {
        const outputElement = document.getElementById('markdownPreview');
        const htmlContent = outputElement.innerHTML;

        // Create a Blob with the HTML content
        const blob = new Blob([htmlContent], { type: 'text/html' });

        // Use the Clipboard API to write the rich text to the clipboard
        navigator.clipboard.write([
            new ClipboardItem({
                'text/html': blob
            })
        ]).then(() => {
            const button = document.getElementById('copyButton');
            const originalHTML = button.innerHTML;
            
            // Visual feedback that copy worked
            button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/></svg>';
            
            // Revert back after 1.5 seconds
            setTimeout(function() {
                button.innerHTML = originalHTML;
            }, 1500);
        }).catch((err) => {
            console.error('Failed to copy:', err);
        });
    });

    // Provider change updates model options
    $('#provider').on('change', function() {
        const provider = $(this).val();
        const modelSelect = $('#model');
        
        // Always keep GPT models regardless of provider
        // Both OpenAI and OpenRouter support these models
        // This simplifies the implementation while keeping options consistent
    });
});
                                
