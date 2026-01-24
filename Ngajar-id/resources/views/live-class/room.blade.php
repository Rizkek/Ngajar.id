<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live: {{ $kelas->judul }} - Ngajar.ID</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0"
        rel="stylesheet" />
    <script src='https://meet.jit.si/external_api.js'></script>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        #meet {
            height: 100%;
            width: 100%;
        }

        .header-bar {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 100;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 15px;
        }
    </style>
</head>

<body class="bg-gray-900">

    <!-- Floating Header -->
    <div class="header-bar">
        <a href="{{ url()->previous() }}"
            class="flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition">
            <span class="material-symbols-rounded">close</span>
        </a>
        <div>
            <h1 class="font-bold text-gray-800 text-sm md:text-base">{{ $kelas->judul }}</h1>
            <div class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                <span class="text-xs text-red-500 font-bold uppercase tracking-wide">LIVE</span>
            </div>
        </div>
    </div>

    <!-- Jitsi Container -->
    <div id="meet"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const domain = 'meet.jit.si';
            const options = {
                roomName: '{{ $jitsiConfig['roomName'] }}',
                width: '100%',
                height: '100%',
                parentNode: document.querySelector('#meet'),
                userInfo: {
                    displayName: '{{ $jitsiConfig['userInfo']['displayName'] }}',
                    email: '{{ $jitsiConfig['userInfo']['email'] }}'
                },
                configOverwrite: @json($jitsiConfig['configOverwrite']),
                interfaceConfigOverwrite: @json($jitsiConfig['interfaceConfigOverwrite']),
                lang: 'id'
            };
            const api = new JitsiMeetExternalAPI(domain, options);

            // Event listener jika user keluar (hangup)
            api.addEventListeners({
                videoConferenceLeft: function () {
                    window.location.href = "{{ url()->previous() }}";
                }
            });
        });
    </script>
</body>

</html>