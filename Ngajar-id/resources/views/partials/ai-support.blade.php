{{-- Floating AI Support Widget --}}
<div class="fixed bottom-6 right-6 z-40 flex flex-col items-end gap-3">

    {{-- Chat Panel --}}
    <div id="support-panel"
        class="hidden w-80 sm:w-96 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden flex-col"
        style="max-height: 560px;">

        {{-- Header --}}
        <div
            class="bg-gradient-to-r from-brand-600 to-brand-700 px-5 py-4 text-white flex items-center gap-3 shrink-0">
            <div
                class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md shrink-0">
                <span class="material-symbols-rounded text-xl">smart_toy</span>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-sm leading-none">Ngaji - Asisten Ngajar.id</h3>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <p class="text-xs text-brand-100">Online • Siap membantu</p>
                </div>
            </div>
            <button onclick="toggleSupport()" class="text-white/70 hover:text-white shrink-0">
                <span class="material-symbols-rounded text-xl">close</span>
            </button>
        </div>

        {{-- Messages Area --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50/50"
            style="min-height: 240px; max-height: 320px;">
            {{-- Pesan sambutan awal --}}
            <div class="flex items-end gap-2">
                <div class="w-7 h-7 bg-brand-600 rounded-full flex items-center justify-center shrink-0 mb-0.5">
                    <span class="material-symbols-rounded text-white text-sm">smart_toy</span>
                </div>
                <div
                    class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm max-w-[80%]">
                    <p class="text-sm text-slate-700 leading-relaxed">Halo! Saya <strong>Ngaji</strong>, asisten
                        virtual Ngajar.id 👋<br>Ada yang bisa saya bantu?</p>
                </div>
            </div>
        </div>

        {{-- Quick Topics --}}
        <div id="quick-topics" class="px-4 py-2 border-t border-gray-100 bg-white shrink-0">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-2">Pertanyaan Cepat</p>
            <div class="flex flex-wrap gap-1.5">
                <button onclick="sendQuickMessage('Bagaimana cara daftar sebagai Murid?')"
                    class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-xs text-brand-700 font-medium transition-colors border border-brand-100">
                    Cara daftar Murid
                </button>
                <button onclick="sendQuickMessage('Bagaimana cara reset password yang lupa?')"
                    class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-xs text-brand-700 font-medium transition-colors border border-brand-100">
                    Lupa Password
                </button>
                <button onclick="sendQuickMessage('Apa itu sistem token di Ngajar.id?')"
                    class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-xs text-brand-700 font-medium transition-colors border border-brand-100">
                    Sistem Token
                </button>
                <button onclick="sendQuickMessage('Bagaimana jika donasi saya sudah berhasil didaftarkan?')"
                    class="px-2.5 py-1 rounded-lg bg-brand-50 hover:bg-brand-100 text-xs text-brand-700 font-medium transition-colors border border-brand-100">
                    Donasi Berhasil?
                </button>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="px-4 py-3 border-t border-gray-100 bg-white shrink-0">
            <div class="flex items-end gap-2">
                <textarea id="chat-input"
                    class="flex-1 resize-none border border-gray-200 rounded-2xl px-4 py-2.5 text-sm focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-100 transition-all leading-relaxed"
                    placeholder="Ketik pertanyaanmu..." rows="1" maxlength="500"
                    onkeydown="handleChatKeydown(event)" oninput="autoResizeTextarea(this)"></textarea>
                <button id="send-btn" onclick="sendChatMessage()"
                    class="w-10 h-10 bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-rounded text-xl">send</span>
                </button>
            </div>
            <p class="text-[10px] text-slate-400 text-center mt-2">Ngaji dapat membuat kesalahan. Selalu verifikasi
                info penting.</p>
        </div>
    </div>

    {{-- Toggle Button --}}
    <button onclick="toggleSupport()" id="support-toggle-btn"
        class="group bg-brand-600 hover:bg-brand-700 text-white w-14 h-14 rounded-2xl shadow-xl shadow-brand-600/30 flex items-center justify-center transition-all hover:scale-110 active:scale-95 relative">
        <span id="support-icon" class="material-symbols-rounded text-3xl transition-all">question_answer</span>
        <span id="support-badge"
            class="absolute -top-1 -right-1 w-4 h-4 bg-orange-500 border-2 border-white rounded-full animate-pulse"></span>
    </button>
</div>

<script>
    // ===== State =====
    let chatHistory = []; // Array of {role: 'user'|'model', text: '...'}
    let isChatLoading = false;

    // ===== Toggle Widget =====
    function toggleSupport() {
        const panel = document.getElementById('support-panel');
        const icon = document.getElementById('support-icon');
        const badge = document.getElementById('support-badge');

        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            panel.classList.add('flex');
            icon.textContent = 'close';
            badge.classList.add('hidden'); // Sembunyikan badge saat dibuka
            scrollChatToBottom();
        } else {
            panel.classList.add('hidden');
            panel.classList.remove('flex');
            icon.textContent = 'question_answer';
        }
    }

    // ===== Kirim Pesan =====
    async function sendChatMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();

        if (!message || isChatLoading) return;

        // Tampilkan pesan user
        appendMessage('user', message);
        input.value = '';
        autoResizeTextarea(input);

        // Tampilkan loading indicator
        const loadingId = showLoadingBubble();
        isChatLoading = true;
        document.getElementById('send-btn').disabled = true;

        try {
            const response = await fetch('{{ route("ai.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message: message,
                    history: chatHistory.slice(-10), // Kirim max 10 history
                }),
            });

            const data = await response.json();
            removeLoadingBubble(loadingId);

            if (data.success) {
                appendMessage('model', data.reply);
                // Simpan ke history
                chatHistory.push({ role: 'user', text: message });
                chatHistory.push({ role: 'model', text: data.reply });
                // Trim history ke 20 entry (10 percakapan)
                if (chatHistory.length > 20) chatHistory = chatHistory.slice(-20);
            } else {
                appendMessage('model', data.message || 'Maaf, terjadi kesalahan. Silakan coba lagi. 🙏');
            }
        } catch (err) {
            removeLoadingBubble(loadingId);
            appendMessage('model', 'Koneksi bermasalah. Pastikan internet kamu aktif ya! 🌐');
        } finally {
            isChatLoading = false;
            document.getElementById('send-btn').disabled = false;
        }
    }

    // ===== Kirim via Quick Topic Button =====
    function sendQuickMessage(text) {
        const input = document.getElementById('chat-input');
        input.value = text;
        sendChatMessage();
    }

    // ===== Append Message Bubble =====
    function appendMessage(role, text) {
        const container = document.getElementById('chat-messages');
        const isUser = role === 'user';

        const wrapper = document.createElement('div');
        wrapper.className = `flex items-end gap-2 ${isUser ? 'flex-row-reverse' : ''}`;

        // Avatar
        const avatar = document.createElement('div');
        avatar.className = `w-7 h-7 rounded-full flex items-center justify-center shrink-0 mb-0.5 ${isUser ? 'bg-slate-200' : 'bg-brand-600'}`;
        avatar.innerHTML = isUser
            ? '<span class="material-symbols-rounded text-slate-500 text-sm">person</span>'
            : '<span class="material-symbols-rounded text-white text-sm">smart_toy</span>';

        // Bubble
        const bubble = document.createElement('div');
        bubble.className = isUser
            ? 'bg-brand-600 text-white rounded-2xl rounded-br-sm px-4 py-3 max-w-[80%] shadow-sm'
            : 'bg-white border border-gray-100 text-slate-700 rounded-2xl rounded-bl-sm px-4 py-3 max-w-[80%] shadow-sm';
        bubble.innerHTML = `<p class="text-sm leading-relaxed">${escapeHtml(text).replace(/\n/g, '<br>')}</p>`;

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
        container.appendChild(wrapper);
        scrollChatToBottom();
    }

    // ===== Loading Bubble =====
    function showLoadingBubble() {
        const id = 'loading-' + Date.now();
        const container = document.getElementById('chat-messages');
        const wrapper = document.createElement('div');
        wrapper.id = id;
        wrapper.className = 'flex items-end gap-2';
        wrapper.innerHTML = `
            <div class="w-7 h-7 bg-brand-600 rounded-full flex items-center justify-center shrink-0 mb-0.5">
                <span class="material-symbols-rounded text-white text-sm">smart_toy</span>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl rounded-bl-sm px-4 py-3 shadow-sm">
                <div class="flex gap-1 items-center">
                    <div class="w-2 h-2 bg-brand-400 rounded-full animate-bounce" style="animation-delay:0s"></div>
                    <div class="w-2 h-2 bg-brand-500 rounded-full animate-bounce" style="animation-delay:0.15s"></div>
                    <div class="w-2 h-2 bg-brand-600 rounded-full animate-bounce" style="animation-delay:0.3s"></div>
                </div>
            </div>`;
        container.appendChild(wrapper);
        scrollChatToBottom();
        return id;
    }

    function removeLoadingBubble(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    // ===== Helpers =====
    function scrollChatToBottom() {
        const container = document.getElementById('chat-messages');
        container.scrollTop = container.scrollHeight;
    }

    function autoResizeTextarea(el) {
        el.style.height = 'auto';
        el.style.height = Math.min(el.scrollHeight, 96) + 'px';
    }

    function handleChatKeydown(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendChatMessage();
        }
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
</script>
