<!DOCTYPE html>
<html>
<head>
    <title>QR Code Scanner</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        #reader {
            width: 500px;
            height: 500px;
            margin: 0 auto;
        }
        .instructions {
            margin-bottom: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Scan Student QR Code</h1>
    <p class="instructions">Position the QR code inside the scanner area</p>
    
    <div id="reader"></div>
    <div id="result"></div>
    
    <script src="{{ asset('vendor/html5-qrcode/html5-qrcode.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the form field ID from the parent window
            const formId = 'qrcode';
            
            // QR code success callback
            function onScanSuccess(decodedText, decodedResult) {
                document.getElementById('result').innerHTML = `
                    <p>UUID scanned: <strong>${decodedText}</strong></p>
                    <p>Saving...</p>
                `;
                
                // Set value in the parent window form field
                if (window.parent && window.parent.document) {
                    const inputField = window.parent.document.getElementById(formId);
                    if (inputField) {
                        inputField.value = decodedText;
                        
                        // Submit the form
                        setTimeout(() => {
                            const submitBtn = window.parent.document.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.click();
                                console.log('Form submitted');
                            } else {
                                console.error('Submit button not found');
                            }
                        }, 1000);
                    } else {
                        console.error(`Input field with ID "${formId}" not found in parent document`);
                    }
                } else {
                    console.error('Parent window not accessible');
                }
            }
            
            // QR code failure callback
            function onScanFailure(error) {
                // Just log the error, don't stop scanning
                console.warn(`QR code scan error: ${error}`);
            }
            
            // Initialize the scanner
            try {
                console.log('Initializing QR code scanner...');
                const html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader",
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    /* verbose= */ false
                );
                
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                console.log('QR code scanner initialized successfully');
            } catch (error) {
                console.error('Error initializing QR code scanner:', error);
                document.getElementById('result').innerHTML = `
                    <p style="color: red;">Error initializing scanner: ${error.message}</p>
                `;
            }
        });
    </script>
</body>
</html> 