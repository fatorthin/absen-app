<!DOCTYPE html>
<html>
<head>
    <title>QR Code Scanner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background-color: #f8f9fa;
        }
        #reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .scanner-container {
            max-width: 500px;
            margin: 0 auto;
        }
        .scan-result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .events-list {
            margin-top: 10px;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="mb-3">Student Attendance Scanner</h1>
                <p class="text-muted">Scan a student QR code to mark them present for today's events</p>
            </div>
        </div>
        
        <div class="scanner-container">
            <div id="reader"></div>
            <div id="scanResult" class="scan-result"></div>
        </div>
        
        <div class="text-center mt-4">
            <a href="javascript:window.close();" class="btn btn-secondary">Close Scanner</a>
        </div>
    </div>
    
    <!-- HTML5 QR Code Library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const resultContainer = document.getElementById('scanResult');
            
            // Function to show scan result
            function showResult(message, isSuccess, events = []) {
                let resultHtml = '';
                
                if (isSuccess && events && events.length > 0) {
                    resultHtml = `
                        <h4>Success!</h4>
                        <p>${message}</p>
                        <div class="events-list">
                            <strong>Events updated:</strong>
                            <ul>
                                ${events.map(event => `<li>${event}</li>`).join('')}
                            </ul>
                        </div>
                    `;
                } else {
                    resultHtml = `
                        <h4>${isSuccess ? 'Success!' : 'Error'}</h4>
                        <p>${message}</p>
                    `;
                }
                
                resultContainer.innerHTML = resultHtml;
                resultContainer.style.display = 'block';
                
                if (isSuccess) {
                    resultContainer.className = 'scan-result success';
                } else {
                    resultContainer.className = 'scan-result error';
                }
                
                // Hide after 5 seconds
                setTimeout(() => {
                    resultContainer.style.display = 'none';
                }, 5000);
            }
            
            // Function to process QR code
            function processQrCode(uuid) {
                fetch('/qrcode/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        uuid: uuid
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult(data.message, true, data.events);
                    } else {
                        showResult(data.message, false);
                    }
                })
                .catch(error => {
                    console.error('Error processing QR code:', error);
                    showResult(`Failed to process QR code: ${error.message}`, false);
                });
            }
            
            // QR code scanning callbacks
            function onScanSuccess(decodedText, decodedResult) {
                console.log(`QR code scanned: ${decodedText}`);
                processQrCode(decodedText);
            }
            
            function onScanFailure(error) {
                // Just log the error, don't stop scanning
                console.warn(`QR scan error: ${error}`);
            }
            
            // Initialize QR code scanner
            try {
                console.log('Initializing QR scanner...');
                const html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader",
                    { 
                        fps: 10, 
                        qrbox: { width: 250, height: 250 },
                        rememberLastUsedCamera: true
                    },
                    /* verbose= */ false
                );
                
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                console.log('QR scanner initialized successfully');
            } catch (error) {
                console.error('Error initializing QR scanner:', error);
                showResult(`Failed to initialize QR scanner: ${error.message}`, false);
            }
        });
    </script>
</body>
</html> 