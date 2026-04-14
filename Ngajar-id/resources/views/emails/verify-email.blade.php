@component('mail::message')
# Verifikasi Email Anda

Halo **{{ $user->name }}**,

Kami menerima permintaan untuk memverifikasi email Anda di Ngajar.ID. Klik tombol di bawah untuk melanjutkan:

@component('mail::button', ['url' => $verificationUrl])
Verifikasi Email Sekarang
@endcomponent

Jika Anda tidak membuat permintaan ini, abaikan email ini.

---

**Link berlaku selama 24 jam.**

@component('mail::subcopy')
Atau copy dan paste URL ini di browser Anda:
{{ $verificationUrl }}
@endcomponent

@endcomponent
