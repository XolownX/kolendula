// Виджет чата с поддержкой
(function(){
    const fab = document.getElementById('chat-fab');
    const panel = document.getElementById('chat-panel');
    const closeBtn = document.getElementById('chat-close');
    const body = document.getElementById('chat-body');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const opBtn = document.getElementById('chat-operator-btn');
    if (!fab || !panel) return;

    let pollTimer = null;
    let lastIds = new Set();

    function escapeHtml(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function render(messages) {
        const initial = !lastIds.size;
        let appended = false;
        messages.forEach(m => {
            if (lastIds.has(m.id)) return;
            lastIds.add(m.id);
            const cls = m.author === 'user' ? 'msg msg-user' : (m.author === 'bot' ? 'msg msg-bot' : 'msg msg-op');
            const label = m.author === 'user' ? 'Вы' : (m.author === 'bot' ? '🤖 Бот' : '👤 Оператор');
            const div = document.createElement('div');
            div.className = cls;
            div.innerHTML = '<div class="msg-author">' + label + '</div><div class="msg-text">' + escapeHtml(m.text) + '</div>';
            body.appendChild(div);
            appended = true;
        });
        if (appended) body.scrollTop = body.scrollHeight;
        if (initial && !messages.length) {
            const hello = document.createElement('div');
            hello.className = 'msg msg-bot';
            hello.innerHTML = '<div class="msg-author">🤖 Бот</div><div class="msg-text">Здравствуйте! Я бот-помощник Kolendula. Могу ответить про доставку, возврат, гарантию, оплату. Если нужен человек — нажмите «Связаться с оператором».</div>';
            body.appendChild(hello);
        }
    }

    function fetchMessages() {
        fetch('/api/chat/messages', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(d => render(d.messages || []))
            .catch(() => {});
    }

    function open() {
        panel.hidden = false;
        document.body.classList.add('chat-open');
        fetchMessages();
        if (!pollTimer) pollTimer = setInterval(fetchMessages, 4000);
        setTimeout(() => input?.focus(), 100);
    }
    function close() {
        panel.hidden = true;
        document.body.classList.remove('chat-open');
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    fab.addEventListener('click', () => panel.hidden ? open() : close());
    closeBtn.addEventListener('click', close);

    form.addEventListener('submit', function(e){
        e.preventDefault();
        const text = input.value.trim();
        if (!text) return;
        input.value = '';
        fetch('/api/chat/send', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text })
        }).then(r => r.json()).then(d => render(d.messages || [])).catch(() => {});
    });

    opBtn.addEventListener('click', function(){
        fetch('/api/chat/operator', { method: 'POST', credentials: 'same-origin' })
            .then(r => r.json()).then(d => render(d.messages || [])).catch(() => {});
    });
})();
