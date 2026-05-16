const toggleBtn = document.getElementById('magi-chat-toggle');
const chatbox = document.getElementById('magi-chatbox');

toggleBtn.onclick = () => {
    chatbox.style.display =
        chatbox.style.display === 'flex'
        ? 'none'
        : 'flex';
};

function quickAsk(question) {
    document.getElementById('magi-user-input').value = question;
    sendMessage();
}

async function sendMessage() {

    const input = document.getElementById('magi-user-input');
    const message = input.value.trim();

    if(message === '') return;

    appendMessage(message, 'user');

    input.value = '';

    showTyping();

    const response = await fetch('api/send_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: message
        })
    });

    const data = await response.json();

    removeTyping();

    appendMessage(data.reply, 'ai');
}

function appendMessage(message, sender) {

    const container = document.getElementById('magi-chat-messages');

    const div = document.createElement('div');

    div.className =
        sender === 'user'
        ? 'magi-user-message'
        : 'magi-ai-message';

    div.innerHTML = message;

    container.appendChild(div);

    container.scrollTop = container.scrollHeight;
}

function showTyping() {
    appendMessage('✨ MAGI AI is typing...', 'ai');
}

function removeTyping() {
    const msgs = document.querySelectorAll('.magi-ai-message');

    msgs.forEach(msg => {
}
