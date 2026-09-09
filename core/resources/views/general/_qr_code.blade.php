<script>
    "use strict";
    function printQrCode(elementId) {
        const content = document.getElementById(elementId).innerHTML;

        const printWindow = window.open('', '_blank', 'width=500,height=600');
        printWindow.document.write(`
        <html>
            <head>
                <title>{{ __('Print QR Code') }}</title>
                <style>
                    body { text-align: center; padding: 30px; font-family: sans-serif; }
                    svg { max-width: 100%; height: auto; }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                ${content}
                <p>{{ __('Scan this QR code to proceed.') }}</p>
            </body>
        </html>
    `);
        printWindow.document.close();
    }
</script>