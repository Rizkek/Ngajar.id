{{-- Modal Popup Donasi (Multi-Step) --}}
<div id="donationModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Header Modal -->
        <div
            class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between rounded-t-3xl">
            <h2 class="text-xl font-bold text-slate-900" id="modalTitle">Konfirmasi Donasi</h2>
            <button onclick="closeDonationModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-rounded text-2xl">close</span>
            </button>
        </div>

        <!-- Indikator Step (1-2-3) -->
        <div class="px-6 py-4 border-b border-gray-50">
            <div class="flex items-center justify-between max-w-md mx-auto">
                <div class="flex items-center gap-2" id="step1Indicator">
                    <div
                        class="w-8 h-8 rounded-full bg-brand-500 text-white flex items-center justify-center font-bold text-sm">
                        1</div>
                    <span class="text-sm font-medium text-brand-600">Konfirmasi</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-2"></div>
                <div class="flex items-center gap-2" id="step2Indicator">
                    <div
                        class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm">
                        2</div>
                    <span class="text-sm font-medium text-gray-400">Pembayaran</span>
                </div>
                <div class="flex-1 h-0.5 bg-gray-200 mx-2"></div>
                <div class="flex items-center gap-2" id="step3Indicator">
                    <div
                        class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm">
                        3</div>
                    <span class="text-sm font-medium text-gray-400">Selesai</span>
                </div>
            </div>
        </div>

        <!-- Step 1: Form Data Diri -->
        <div id="step1" class="p-6">
            <div class="text-center mb-6">
                <div class="w-20 h-20 rounded-full bg-brand-50 mx-auto mb-4 flex items-center justify-center">
                    <span class="material-symbols-rounded text-4xl text-brand-600">volunteer_activism</span>
                </div>
                <p class="text-sm text-slate-500 mb-2">Nominal donasi Anda</p>
                <p class="text-4xl font-black text-slate-900" id="selectedAmountDisplay">Rp 0</p>
            </div>

            <div class="bg-brand-50 border border-brand-100 rounded-2xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-rounded text-brand-600">info</span>
                    <div class="text-sm text-brand-800">
                        <p class="font-bold mb-1">Donasi Anda akan disalurkan untuk:</p>
                        <ul class="list-disc list-inside space-y-1 text-brand-700">
                            <li>Beasiswa pendidikan siswa tidak mampu</li>
                            <li>Bantuan fasilitas belajar</li>
                            <li>Subsidi kuota internet</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="space-y-5 mb-6">
                <div class="relative">
                    <input type="text" id="donorName"
                        class="peer w-full px-4 py-3 pt-6 border-2 border-gray-200 rounded-xl focus:border-brand-500 focus:outline-none transition-all"
                        placeholder=" " required>
                    <label for="donorName"
                        class="absolute left-4 top-3.5 text-gray-400 text-sm transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:text-xs peer-focus:top-1.5 peer-focus:text-brand-600 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:top-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                </div>

                <div class="relative">
                    <input type="email" id="donorEmail"
                        class="peer w-full px-4 py-3 pt-6 border-2 border-gray-200 rounded-xl focus:border-brand-500 focus:outline-none transition-all"
                        placeholder=" " required>
                    <label for="donorEmail"
                        class="absolute left-4 top-3.5 text-gray-400 text-sm transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:text-xs peer-focus:top-1.5 peer-focus:text-brand-600 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:top-1.5">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-slate-500 mt-2 ml-1 flex items-center gap-1">
                        <span class="material-symbols-rounded text-sm">info</span> Untuk menerima bukti donasi & invoice
                    </p>
                </div>

                <div class="relative">
                    <textarea id="donorMessage" rows="4"
                        class="peer w-full px-4 py-3 pt-6 border-2 border-gray-200 rounded-xl focus:border-brand-500 focus:outline-none transition-all resize-none"
                        placeholder=" "></textarea>
                    <label for="donorMessage"
                        class="absolute left-4 top-3.5 text-gray-400 text-sm transition-all duration-200 peer-placeholder-shown:text-base peer-placeholder-shown:top-3.5 peer-focus:text-xs peer-focus:top-1.5 peer-focus:text-brand-600 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:top-1.5">
                        Pesan / Doa (Opsional)
                    </label>
                </div>
            </div>

            <button onclick="goToStep2()"
                class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg transition-all">
                Lanjut ke Pembayaran
            </button>
        </div>

        <!-- Step 2: Metode Pembayaran -->
        <div id="step2" class="p-6 hidden">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Pilih Metode Pembayaran</h3>
            <div class="space-y-3 mb-6">
                <!-- Transfer Bank -->
                <button onclick="selectPaymentMethod('bank')" data-payment="bank"
                    class="payment-method-btn w-full p-4 border-2 border-gray-200 rounded-xl hover:border-brand-500 transition-all text-left flex items-center justify-between group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-secondary-50 flex items-center justify-center group-hover:bg-secondary-100 transition-colors">
                            <span class="material-symbols-rounded text-secondary-600">account_balance</span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">Transfer Bank</p>
                            <p class="text-sm text-slate-500">BCA, Mandiri, BNI, BRI</p>
                        </div>
                    </div>
                    <span class="material-symbols-rounded text-gray-400">chevron_right</span>
                </button>

                <!-- E-Wallet -->
                <button onclick="selectPaymentMethod('ewallet')" data-payment="ewallet"
                    class="payment-method-btn w-full p-4 border-2 border-gray-200 rounded-xl hover:border-brand-500 transition-all text-left flex items-center justify-between group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center group-hover:bg-orange-100 transition-colors">
                            <span class="material-symbols-rounded text-orange-600">wallet</span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">E-Wallet</p>
                            <p class="text-sm text-slate-500">GoPay, OVO, DANA, ShopeePay</p>
                        </div>
                    </div>
                    <span class="material-symbols-rounded text-gray-400">chevron_right</span>
                </button>

                <!-- QRIS -->
                <button onclick="selectPaymentMethod('qris')" data-payment="qris"
                    class="payment-method-btn w-full p-4 border-2 border-gray-200 rounded-xl hover:border-brand-500 transition-all text-left flex items-center justify-between group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center group-hover:bg-brand-100 transition-colors">
                            <span class="material-symbols-rounded text-brand-600">qr_code</span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">QRIS</p>
                            <p class="text-sm text-slate-500">Semua aplikasi pembayaran</p>
                        </div>
                    </div>
                    <span class="material-symbols-rounded text-gray-400">chevron_right</span>
                </button>
            </div>

            <div class="flex gap-3">
                <button onclick="goToStep1()"
                    class="flex-1 py-3 border-2 border-gray-200 text-slate-700 font-bold rounded-xl hover:bg-gray-50 transition-all">Kembali</button>
                <button onclick="goToStep3()" id="confirmPaymentBtn" disabled
                    class="flex-1 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">Konfirmasi</button>
            </div>
        </div>

        <!-- Step 3: Sukses -->
        <div id="step3" class="p-6 text-center hidden">
            <div class="w-24 h-24 rounded-full bg-brand-50 mx-auto mb-6 flex items-center justify-center">
                <span class="material-symbols-rounded text-6xl text-brand-600">check_circle</span>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">Terima Kasih! 🙏</h3>
            <p class="text-slate-600 mb-6">Donasi Anda sedang diproses. Kami akan mengirimkan instruksi pembayaran ke
                email Anda.</p>
            <button onclick="closeDonationModal()"
                class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-lg transition-all">Selesai</button>
        </div>

        <!-- Footer Modal Note -->
        <div class="px-6 py-4 bg-slate-50 border-t border-gray-100 flex items-center justify-center gap-4">
            <a href="{{ route('privacy-policy') }}" class="text-[10px] text-slate-400 hover:text-brand-600">Privacy
                Policy</a>
            <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
            <a href="{{ route('terms-of-service') }}" class="text-[10px] text-slate-400 hover:text-brand-600">Terms of
                Service</a>
        </div>
    </div>
</div>

<style>
    .payment-method-btn.selected {
        border-color: #14b8a6;
        background-color: #f0fdfa;
    }
</style>

<script>
    let selectedPaymentMethodModal = '';

    function openDonationModal() {
        if (selectedAmount === 0) {
            alert('Silakan pilih nominal donasi terlebih dahulu.');
            return;
        }

        document.getElementById('donationModal').classList.remove('hidden');
        document.getElementById('donationModal').classList.add('flex');
        document.getElementById('selectedAmountDisplay').textContent = `Rp ${formatRupiah(selectedAmount)}`;
        document.body.style.overflow = 'hidden';
    }

    function closeDonationModal() {
        document.getElementById('donationModal').classList.add('hidden');
        document.getElementById('donationModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
        goToStep1();
    }

    function goToStep1() { showStep(1); }
    function goToStep2() {
        const nameInput = document.getElementById('donorName');
        const emailInput = document.getElementById('donorEmail');
        const nameValue = nameInput.value.trim();
        const emailValue = emailInput.value.trim();

        if (!nameValue) { alert('❌ Nama wajib diisi!'); nameInput.focus(); return; }
        if (!emailValue) { alert('❌ Email wajib diisi!'); emailInput.focus(); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) { alert('❌ Format email tidak valid!'); emailInput.focus(); return; }

        showStep(2);
    }

    function goToStep3() {
        if (!selectedPaymentMethodModal) { alert('Pilih metode pembayaran terlebih dahulu!'); return; }

        const confirmBtn = document.getElementById('confirmPaymentBtn');
        const originalText = confirmBtn.textContent;
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Memproses...';

        const formData = {
            jumlah: selectedAmount,
            nama: document.getElementById('donorName').value || 'Hamba Allah',
            email: document.getElementById('donorEmail').value,
            pesan: document.getElementById('donorMessage').value,
            metode_pembayaran: selectedPaymentMethodModal,
        };

        fetch('{{ route("donasi.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(formData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.snap_token) {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = originalText;
                    closeDonationModal();
                    window.snap.pay(data.data.snap_token, {
                        onSuccess: (result) => window.location.href = "{{ route('donasi.payment.finish') }}?order_id=" + result.order_id,
                        onPending: (result) => window.location.href = "{{ route('donasi.payment.finish') }}?order_id=" + result.order_id,
                        onError: () => alert("Pembayaran gagal!"),
                        onClose: () => alert('Anda menutup popup tanpa menyelesaikan pembayaran')
                    });
                } else {
                    throw new Error(data.message || 'Gagal menyimpan donasi');
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan: ' + error.message);
                confirmBtn.disabled = false;
                confirmBtn.textContent = originalText;
            });
    }

    function showStep(step) {
        document.getElementById('step1').classList.add('hidden');
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step3').classList.add('hidden');
        document.getElementById(`step${step}`).classList.remove('hidden');
        updateStepIndicator(step);
        const titles = ['', 'Konfirmasi Donasi', 'Pilih Pembayaran', 'Donasi Berhasil'];
        document.getElementById('modalTitle').textContent = titles[step];
    }

    function updateStepIndicator(activeStep) {
        for (let i = 1; i <= 3; i++) {
            const indicator = document.getElementById(`step${i}Indicator`);
            const circle = indicator.querySelector('div');
            const text = indicator.querySelector('span');
            if (i <= activeStep) {
                circle.className = 'w-8 h-8 rounded-full bg-brand-500 text-white flex items-center justify-center font-bold text-sm';
                text.className = 'text-sm font-medium text-brand-600';
            } else {
                circle.className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold text-sm';
                text.className = 'text-sm font-medium text-gray-400';
            }
        }
    }

    function selectPaymentMethod(method) {
        selectedPaymentMethodModal = method;
        document.querySelectorAll('.payment-method-btn').forEach(btn => btn.classList.remove('selected'));
        document.querySelector(`[data-payment="${method}"]`).classList.add('selected');
        document.getElementById('confirmPaymentBtn').disabled = false;
    }

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    document.getElementById('donationModal')?.addEventListener('click', function (e) {
        if (e.target === this) closeDonationModal();
    });
</script>