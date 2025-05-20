function onScanSuccess(decodedText, decodedResult) {
    // Set the value in the form field
    document.getElementById('qrcode').value = decodedText;
    
    // Log for debugging
    console.log(`Code scanned = ${decodedText}`, decodedResult);
    
    // Optionally auto-submit the form after scanning
    setTimeout(() => {
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.click();
        }
    }, 500);
}

function onScanFailure(error) {
    // handle scan failure, usually better to ignore and keep scanning.
    // for example:
    console.warn(`Code scan error = ${error}`);
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    { fps: 10, qrbox: { width: 250, height: 250 } },
    /* verbose= */ false
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
