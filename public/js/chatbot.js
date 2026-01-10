let chatbotOpen = false;
let conversationId = null;

function toggleChat() {
    const chatbot = document.getElementById('chatbot');
    chatbotOpen = !chatbotOpen;
    chatbot.style.display = chatbotOpen ? 'flex' : 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send');
    const body = document.getElementById('chatbot-body');

    function appendMessage(text, type = 'bot') {
        const div = document.createElement('div');
        div.className = `chatbot-msg ${type}`;
        div.textContent = text;
        body.appendChild(div);
        body.scrollTop = body.scrollHeight;
    }

    async function sendMessage() {
        const message = input.value.trim();
        if (!message) return;

        appendMessage(message, 'user');
        input.value = '';

        try {
            const res = await fetch('/chatbot/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content'),
                },
                body: JSON.stringify({
                    message: message,
                    conversation_id: conversationId,
                }),
            });

            const data = await res.json();

            if (!conversationId && data.conversation_id) {
                conversationId = data.conversation_id;
            }

            appendMessage(data.answer || 'AI ไม่สามารถตอบได้ในขณะนี้', 'bot');
        } catch (err) {
            console.error(err);
            appendMessage('เกิดข้อผิดพลาดในการเชื่อมต่อ AI', 'bot');
        }
    }

    sendBtn.addEventListener('click', sendMessage);

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
});
