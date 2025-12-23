<!DOCTYPE html>
<html>
<head>
    <title>Grawlix</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<h1 style="text-align: center; color: #333; margin-bottom: 20px;">Your Website Content Here</h1>
<p style="text-align: center; color: #666;">The chat assistant will float on the bottom-right corner</p>



<div class="chat-button" id="chatButton">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
    </svg>
</div>


<div class="chat-widget" id="chatWidget">
    <div class="chat-header">
        <div class="chat-header-info">
            <div class="chat-avatar">G</div>
            <div>
                <div class="chat-title">Grawlix Chatbot</div>
                <div class="chat-status">
                    <span class="status-dot"></span>
                    <span id="activeModel">Llama 3.3 70B</span>
                </div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 5px;">
            <button class="close-button" id="closeChat">&times;</button>
        </div>
    </div>

    <div class="chat-messages" id="chatMessages">
        <div class="message bot">
            <div class="message-bubble">
                Hello! I'm the AI Assistant. I'm a bot, Thank you for choosing me and I can help you find information. How can I assist you today?
            </div>
        </div>
    </div>

    <div class="typing-indicator" id="typingIndicator">
        <div class="typing-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <div class="chat-input">
        <div class="input-wrapper">
            <input type="text" id="userInput" placeholder="Type a message..." />
            <button class="send-button" id="sendButton">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>
