<div>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <p class="mb-4 text-center text-sm text-gray-500">Position the QR code in the scanner. The form will automatically submit when a code is detected.</p>

    <div style="width: 500px; height: 500px; margin: 0 auto;" id="reader"></div>

    <!-- Include HTML5 QR code library directly -->
    <script src="{{ asset('vendor/html5-qrcode/html5-qrcode.min.js') }}"></script>
    
    <!-- Custom QR code scanning script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function onScanSuccess(decodedText, decodedResult) {
                console.log(`Code scanned = ${decodedText}`);
                
                // Find the input field and set its value
                const qrcodeInput = document.getElementById('qrcode');
                if (qrcodeInput) {
                    qrcodeInput.value = decodedText;
                    
                    // Auto submit the form
                    setTimeout(() => {
                        const submitButton = document.querySelector('button[type="submit"]');
                        if (submitButton) {
                            submitButton.click();
                        } else {
                            console.error('Submit button not found');
                        }
                    }, 500);
                } else {
                    console.error('QR code input field not found');
                }
            }
            
            function onScanFailure(error) {
                console.warn(`Code scan error = ${error}`);
            }
            
            try {
                let html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader",
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    /* verbose= */ false
                );
                
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
                console.log('QR scanner initialized successfully');
            } catch (error) {
                console.error('Error initializing QR scanner:', error);
            }
        });
    </script>
</div>
