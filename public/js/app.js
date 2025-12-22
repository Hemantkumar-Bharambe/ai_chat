// Chat Widget JavaScript
const chatButton = document.getElementById('chatButton');
const chatWidget = document.getElementById('chatWidget');
const closeChat = document.getElementById('closeChat');
const userInput = document.getElementById('userInput');
const sendButton = document.getElementById('sendButton');
const chatMessages = document.getElementById('chatMessages');
const typingIndicator = document.getElementById('typingIndicator');

let conversationHistory = [];


chatButton.addEventListener('click', () => 
{
    chatWidget.classList.add('active');
    userInput.focus();
});

closeChat.addEventListener('click', () => 
{
    chatWidget.classList.remove('active');
});


async function sendMessage() 
{
    const message = userInput.value.trim();
    if (!message) return;

    
    addMessage(message, 'user');
    conversationHistory.push({ role: 'user', content: message });
    userInput.value = '';
    sendButton.disabled = true;

    
    typingIndicator.classList.add('active');
    scrollToBottom();

    try 
    {
        const response = await fetch('/generate', 
        {
            method: 'POST',
            headers: 
            {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ messages: conversationHistory })
        });

        if (!response.ok) 
        {
            const errText = await response.text();
            throw new Error(errText || 'Request failed');
        }

        const data = await response.json();
        
        
        typingIndicator.classList.remove('active');

        
        addMessage(data.content, 'bot');
        conversationHistory.push({ role: 'assistant', content: data.content });
    } 
    catch (error) 
    {
        typingIndicator.classList.remove('active');
        addMessage('Sorry, something went wrong. Please try again.', 'bot');
    }

    sendButton.disabled = false;
    userInput.focus();
}

function addMessage(text, sender) 
{
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${sender}`;
    
    const bubble = document.createElement('div');
    bubble.className = 'message-bubble';
    bubble.innerHTML = formatMessage(text);
    
    messageDiv.appendChild(bubble);
    chatMessages.appendChild(messageDiv);
    
    scrollToBottom();
}


function formatMessage(text)
{
    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\n\n/g, '<br><br>')
        .replace(/\n/g, '<br>');
}

function scrollToBottom() 
{
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

sendButton.addEventListener('click', sendMessage);

userInput.addEventListener('keypress', (e) => 
{
    if (e.key === 'Enter') 
    {
        sendMessage();
    }
});