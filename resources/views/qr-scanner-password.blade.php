<!DOCTYPE html>
<html>
<head>
    <title>QR Scanner Authentication</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .auth-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo h1 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(253, 210, 110, 0.25);
            border-color: #ffd166;
        }
        .btn-primary {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        .btn-primary:hover {
            background-color: #ffca2c;
            border-color: #ffc720;
            color: #212529;
        }
        .alert {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <h1>Scanner Authentication</h1>
        </div>
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('qrcode.verify') }}">
            @csrf
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required autofocus>
                <div class="form-text">
                    Enter the scanner password to continue.
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Access Scanner</button>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary mt-2">Cancel</a>
            </div>
        </form>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 