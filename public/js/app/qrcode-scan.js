// const { Html5QrcodeScanner } = require("html5-qrcode");

function onScanSuccess(decodedText, decodedResult) {
    // handle the scanned code as you like, for example:
    // console.log(`Code matched = ${decodedText}`, decodedResult);
    document.getElementById("qrcode").value = decodedText;
    console.log(document.getElementById("qrcode").value);
    // Send the decoded data to the server
    // fetch("/api/add-student", {
    //     method: "POST",
    //     headers: {
    //         "Content-Type": "application/json",
    //         "X-CSRF-TOKEN": document
    //             .querySelector('meta[name="csrf-token"]')
    //             .getAttribute("content"),
    //     },
    //     body: JSON.stringify({ qrcode: decodedText }),
    // })
    //     .then((response) => {
    //         if (!response.ok) {
    //             throw new Error("Network response was not ok");
    //         }
    //         return response.json();
    //     })
    //     .then((data) => {
    //         console.log("Success:", data);
    //         // Handle success response
    //         // window.location.href = "/admin/events/1/detail";
    //     })
    //     .catch((error) => {
    //         console.error("Error:", error);
    //         // Handle error response
    //     });
}

function onScanFailure(error) {
    // handle scan failure, usually better to ignore and keep scanning.
    // for example:
    // console.warn(`Code scan error = ${error}`);
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    { facingMode: "environment" },
    { fps: 10, qrbox: { width: 250, height: 250 } },
    /* verbose= */ false
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
