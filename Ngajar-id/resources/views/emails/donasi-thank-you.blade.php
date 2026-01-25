<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - Ngajar.ID</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #334155;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
        }

        .header {
            background: linear-gradient(135deg, #14b8a6 0%, #0891b2 100%);
            padding: 40px 20px;
            text-align: center;
        }

        .header h1 {
            color: white;
            margin: 0;
            font-size: 32px;
            font-weight: bold;
        }

        .content {
            padding: 40px 30px;
        }

        .donation-box {
            background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%);
            border-left: 4px solid #14b8a6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .donation-amount {
            font-size: 36px;
            font-weight: bold;
            color: #14b8a6;
            margin: 10px 0;
        }

        .details-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .details-table td:first-child {
            color: #64748b;
            width: 40%;
        }

        .details-table td:last-child {
            font-weight: bold;
            color: #0f172a;
        }

        .impact-box {
            background: #fef3c7;
            border: 2px solid #f59e0b;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }

        .impact-box h3 {
            color: #92400e;
            margin: 0 0 10px 0;
        }

        .button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #14b8a6 0%, #0891b2 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: bold;
            margin: 20px 0;
        }

        .footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 30px 20px;
            text-align: center;
            font-size: 14px;
        }

        .footer a {
            color: #14b8a6;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Terima Kasih!</h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">
                Donasi Anda Telah Berhasil Diproses
            </p>
        </div>

        <!-- Content -->
        <div class="content">
            <p style="font-size: 16px;">Halo <strong>{{ $donasi->nama }}</strong>,</p>

            <p>
                Kami sangat berterima kasih atas kepercayaan dan kebaikan hati Anda untuk berdonasi di
                <strong>Ngajar.ID</strong>.
                Donasi Anda akan sangat membantu anak-anak Indonesia mendapatkan pendidikan yang lebih baik.
            </p>

            <!-- Donation Amount Box -->
            <div class="donation-box">
                <div style="text-align: center;">
                    <div style="color: #64748b; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                        Total Donasi Anda
                    </div>
                    <div class="donation-amount">
                        Rp {{ number_format($donasi->jumlah, 0, ',', '.') }}
                    </div>
                    <div style="color: #059669; font-weight: bold; margin-top: 10px;">
                        ✓ Pembayaran Berhasil
                    </div>
                </div>
            </div>

            <!-- Details Table -->
            <table class="details-table">
                <tr>
                    <td>Nomor Transaksi</td>
                    <td>{{ $donasi->nomor_transaksi }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>{{ $donasi->tanggal->format('d F Y, H:i') }} WIB</td>
                </tr>
                <tr>
                    <td>Metode Pembayaran</td>
                    <td style="text-transform: capitalize;">{{ $donasi->metode_pembayaran }}</td>
                </tr>
                @if($donasi->pesan)
                    <tr>
                        <td>Pesan Anda</td>
                        <td style="font-style: italic;">"{{ $donasi->pesan }}"</td>
                    </tr>
                @endif
            </table>

            <!-- Impact Message -->
            <div class="impact-box">
                <h3>💝 Dampak Donasi Anda</h3>
                <p style="margin: 0; color: #78350f;">
                    Dengan donasi sebesar <strong>Rp {{ number_format($donasi->jumlah, 0, ',', '.') }}</strong>,
                    Anda telah membantu
                    @if(floor($donasi->jumlah / 150000) > 0)
                        <strong>{{ floor($donasi->jumlah / 150000) }} siswa</strong>
                    @else
                        <strong>anak-anak Indonesia</strong>
                    @endif
                    untuk mendapatkan akses pendidikan yang lebih baik!
                </p>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('donasi') }}" class="button">
                    Donasi Lagi untuk Membantu Lebih Banyak 🙏
                </a>
            </div>

            <p style="color: #64748b; font-size: 14px; margin-top: 30px;">
                <strong>Catatan:</strong> Invoice ini dikirim secara otomatis. Jika Anda memiliki pertanyaan,
                silakan hubungi kami di <a href="mailto:halo@ngajar.id" style="color: #14b8a6;">halo@ngajar.id</a>.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 10px 0; font-size: 18px; font-weight: bold; color: white;">
                Ngajar.ID
            </p>
            <p style="margin: 0 0 15px 0;">
                Platform Pendidikan Terpercaya untuk Indonesia
            </p>
            <p style="margin: 0;">
                <a href="{{ route('home') }}">Beranda</a> |
                <a href="{{ route('programs') }}">Program</a> |
                <a href="{{ route('donasi') }}">Donasi</a> |
                <a href="{{ route('tentang-kami') }}">Tentang Kami</a>
            </p>
            <p style="margin-top: 20px; font-size: 12px; color: #64748b;">
                © {{ date('Y') }} Ngajar.ID. All rights reserved.<br>
                Email ini dikirim karena Anda telah berdonasi di Ngajar.ID.
            </p>
        </div>
    </div>
</body>

</html>