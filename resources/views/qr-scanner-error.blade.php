<!DOCTYPE html>
<html>
<head>
    <title>QR Scanner Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .error-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .error-icon {
            font-size: 50px;
            color: #dc3545;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-icon">❌</div>
            <h2 class="mb-4">Scanner Error</h2>
            <p class="lead text-danger mb-4">{{ $message }}</p>
            <div class="mt-4">
                <a href="javascript:window.close();" class="btn btn-secondary">Close Scanner</a>
            </div>
        </div>
    </div>
</body>
</html> 