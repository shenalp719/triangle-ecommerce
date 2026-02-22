/**
 * AI Chatbot Widget
 * Triangle Printing Solutions
 * UI Only - No backend AI
 */

document.addEventListener('DOMContentLoaded', initializeChatbot);

function initializeChatbot() {
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const chatbotWidget = document.getElementById('chatbot-widget');
    const chatbotClose = document.getElementById('chatbot-close');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotInput = document.getElementById('chatbot-input');
    const quickButtons = document.querySelectorAll('.quick-btn');
    
    if (!chatbotToggle) return;
    
    // Toggle chatbot visibility
    chatbotToggle.addEventListener('click', () => {
        chatbotWidget.classList.toggle('active');
        if (chatbotWidget.classList.contains('active')) {
            chatbotInput.focus();
            trackEvent('chatbot_opened');
        }
    });
    
    // Close chatbot
    if (chatbotClose) {
        chatbotClose.addEventListener('click', () => {
            chatbotWidget.classList.remove('active');
        });
    }
    
    // Send message on Enter key
    if (chatbotInput) {
        chatbotInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendChatMessage();
            }
        });
    }
    
    // Send button
    if (chatbotSend) {
        chatbotSend.addEventListener('click', sendChatMessage);
    }
    
    // Quick action buttons
    quickButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const action = btn.dataset.action;
            handleQuickAction(action);
        });
    });
    
    // Close chatbot when clicking outside
    document.addEventListener('click', (e) => {
        if (!chatbotWidget.contains(e.target) && !chatbotToggle.contains(e.target)) {
            if (chatbotWidget.classList.contains('active') && e.target.closest('body')) {
                // Allow some interactions to not close it
            }
        }
    });
}

// ========== SEND MESSAGE ==========
function sendChatMessage() {
    const input = document.getElementById('chatbot-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add user message
    addChatMessage(message, 'user');
    input.value = '';
    
    // Show typing indicator
    addTypingIndicator();
    
    // Get bot response
    setTimeout(() => {
        removeTy pingIndicator();
        const response = getBotResponse(message);
        addChatMessage(response, 'bot');
    }, 800);
    
    trackEvent('chatbot_message_sent', { message: message });
}

// ========== HANDLE QUICK ACTIONS ==========
function handleQuickAction(action) {
    const responses = {
        pricing: 'Our pricing varies by product type and customization. Frame posters start at $25, mugs at $12, t-shirts at $18, and caps at $15. Custom designs may have additional charges. Would you like details on a specific product?',
        delivery: 'Standard delivery is 5-7 business days ($15). Express delivery is 2-3 business days ($30). Free shipping on orders over $150. Orders are processed within 24 hours. Where are you located?',
        customize: 'We offer professional customization tools for: 📐 Frame Posters - Upload & position your image. 🖼️ Mugs - Add text with curved text option. 👕 T-Shirts - Design with text and images. 🧢 Caps - Full embroidery customization. Start with "Customize" tab!',
        contact: 'You can reach us at:\n📧 Email: info@triangleprinting.com\n📞 Phone: +1 (234) 567-890\n🏢 Address: 123 Print Street, Design City, DC 12345\n⏰ Hours: Mon-Fri 9AM-6PM, Sat 10AM-4PM'
    };
    
    const response = responses[action] || 'How can I help you today?';
    addChatMessage(action === 'pricing' ? '💰 Pricing Information' : 
                   action === 'delivery' ? '🚚 Delivery Information' :
                   action === 'customize' ? '✏️ Customization Options' : '📞 Contact Information', 'bot-action');
    
    setTimeout(() => {
        addChatMessage(response, 'bot');
    }, 300);
}

// ========== GET BOT RESPONSE ==========
function getBotResponse(userMessage) {
    const message = userMessage.toLowerCase();
    
    // Price queries
    if (message.includes('price') || message.includes('cost') || message.includes('how much')) {
        return 'Our pricing varies by product type. Frame posters start at $25, mugs at $12, t-shirts at $18, and caps at $15. Would you like details on a specific product or customization?';
    }
    
    // Delivery queries
    if (message.includes('deliver') || message.includes('shipping') || message.includes('ship')) {
        return 'Standard delivery is 5-7 business days ($15). Express delivery is 2-3 business days ($30). Free shipping on orders over $150. Orders are processed within 24 hours.';
    }
    
    // Customization queries
    if (message.includes('custom') || message.includes('design') || message.includes('upload')) {
        return 'You can customize our products using our design studio:\n• Upload your own images (JPG/PNG)\n• Add text with various fonts\n• Adjust colors and effects\n• Real-time preview\n• HD quality guaranteed\n\nReady to start? Visit our Customizer tab!';
    }
    
    // Product queries
    if (message.includes('mug') || message.includes('shirt') || message.includes('cap') || message.includes('frame') || message.includes('poster')) {
        return 'We offer several wonderful products:\n✨ Frame Posters - Perfect for office/home\n🖼️ Custom Mugs - Great for gifts\n👕 Printed T-Shirts - Comfortable & stylish\n🧢 Custom Caps - Sports & events\n\nWhich product interests you?';
    }
    
    // Quality queries
    if (message.includes('quality') || message.includes('resolution') || message.includes('dpi') || message.includes('print')) {
        return '🎨 Our print quality standards:\n• Minimum 300 DPI resolution\n• Premium color accuracy (CMYK)\n• Professional-grade materials\n• Hand-checked before shipping\n• 100% satisfaction guarantee\n\nEvery design goes through quality control!';
    }
    
    // Contact queries
    if (message.includes('contact') || message.includes('support') || message.includes('help') || message.includes('number') || message.includes('email')) {
        return '📞 Contact Us:\n📧 Email: info@triangleprinting.com\n📞 Phone: +1 (234) 567-890\n🏢 Address: 123 Print Street, Design City, DC 12345\n⏰ Hours: Mon-Fri 9AM-6PM, Sat 10AM-4PM\n\nWe respond within 24 hours! 😊';
    }
    
    // Account/Order queries
    if (message.includes('account') || message.includes('order') || message.includes('track') || message.includes('login')) {
        return 'You can:\n📦 Track orders in your Dashboard\n💾 Save designs to your account\n🔐 Securely store payment info\n📱 View order history\n\nAlready have an account? Login in the top menu!';
    }
    
    // General greetings
    if (message.includes('hi') || message.includes('hello') || message.includes('hey') || message.includes('greet')) {
        return '👋 Hey there! Welcome to Triangle Printing Solutions. How can we help you with your custom printing needs today?';
    }
    
    // Thank you
    if (message.includes('thank') || message.includes('thanks')) {
        return 'You\'re welcome! 😊 Need anything else?';
    }
    
    // Default response
    return 'That\'s a great question! 💭 I can help with:\n• Product information\n• Pricing & shipping\n• Customization help\n• Order tracking\n• Contact details\n\nWhat would you like to know?';
}

// ========== CHAT UI FUNCTIONS ==========
function addChatMessage(text, sender = 'bot') {
    const messagesContainer = document.getElementById('chatbot-messages');
    if (!messagesContainer) return;
    
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('chatbot-message', sender);
    
    const messageText = document.createElement('p');
    messageText.textContent = text;
    
    messageDiv.appendChild(messageText);
    messagesContainer.appendChild(messageDiv);
    
    // Auto-scroll to bottom
    setTimeout(() => {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }, 50);
}

function addTypingIndicator() {
    const messagesContainer = document.getElementById('chatbot-messages');
    if (!messagesContainer) return;
    
    const typingDiv = document.createElement('div');
    typingDiv.classList.add('chatbot-message', 'bot');
    typingDiv.id = 'typing-indicator';
    typingDiv.innerHTML = '<p style="display: flex; gap: 4px;"><span style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--text-light); animation: typing 1.4s infinite;"></span><span style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--text-light); animation: typing 1.4s infinite 0.2s;"></span><span style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--text-light); animation: typing 1.4s infinite 0.4s;"></span></p>';
    
    messagesContainer.appendChild(typingDiv);
    
    // Add animation style
    if (!document.querySelector('style[data-typing]')) {
        const style = document.createElement('style');
        style.setAttribute('data-typing', 'true');
        style.textContent = `
            @keyframes typing {
                0%, 60%, 100% { transform: translateY(0); }
                30% { transform: translateY(-10px); }
            }
        `;
        document.head.appendChild(style);
    }
    
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function removeTypingIndicator() {
    const indicator = document.getElementById('typing-indicator');
    if (indicator) {
        indicator.remove();
    }
}

// ========== EVENT TRACKING ==========
function trackEvent(eventName, data = {}) {
    console.log('Chatbot Event:', eventName, data);
    // Could send to analytics service
}

// ========== CHATBOT STYLES ==========
const chatbotStyles = document.createElement('style');
chatbotStyles.textContent = `
    .chatbot-message bot-action {
        font-weight: 600;
        color: var(--primary-red);
        background-color: rgba(227, 30, 36, 0.05);
    }
    
    .chatbot-message p {
        line-height: 1.5;
        word-wrap: break-word;
    }
    
    /* Code blocks in messages */
    code {
        background-color: var(--light-gray);
        padding: 0.2em 0.4em;
        border-radius: 0.25em;
        font-size: 0.9em;
        font-family: monospace;
    }
    
    .chatbot-message.bot code {
        background-color: rgba(0, 0, 0, 0.1);
    }
`;
document.head.appendChild(chatbotStyles);
