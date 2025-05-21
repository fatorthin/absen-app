<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Absensi App')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom styles -->
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .content {
            flex: 1;
            padding: 20px 0;
        }
        .navbar-brand {
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary no-print">
        <div class="container">
            <a class="navbar-brand" href="/">Absensi App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('qrcode/scanner') ? 'active' : '' }}" href="{{ route('qrcode.scanner') }}">QR Scanner</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('attendance/report*') ? 'active' : '' }}" href="{{ route('attendance.report') }}">Rekap Kehadiran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('staff/attendance/report*') ? 'active' : '' }}" href="{{ route('staff.attendance.report') }}">Rekap Kehadiran Staff</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <footer class="bg-light py-3 mt-4 border-top no-print">
        <div class="container text-center">
            <span class="text-muted">© {{ date('Y') }} Absensi App</span>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html> 