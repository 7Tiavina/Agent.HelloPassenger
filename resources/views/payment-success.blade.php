@php
    // Il est préférable de ne pas avoir de logique complexe dans la vue.
    // Le contenu de la facture est dans $apiResult['content']
    // L'ID de la commande pour le téléchargement est dans $lastCommandeId
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon-hellopassenger.png') }}">
    <title>Paiement Réussi - HelloPassenger</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="bg-gray-50">

@include('Front.header-front')

<div class="container mx-auto max-w-7xl my-12 px-4">
    <div class="bg-white p-8 rounded-lg shadow-lg text-center border border-gray-200">
        
        <!-- Icône de succès -->
        <div class="mx-auto bg-green-100 rounded-full h-16 w-16 flex items-center justify-center mb-4">
            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-2">Paiement réussi !</h1>
        <p class="text-gray-600 mb-6">Votre commande a été confirmée et votre facture a été générée.</p>

        <!-- Boutons d'action -->
        <div class="flex justify-center space-x-4 mb-8">
            <button id="download-invoice-btn"
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 flex items-center justify-center">
                <span id="download-text">Télécharger ma facture</span>
                <svg id="download-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>

        <!-- Aperçu de la facture -->
        <div class="bg-gray-100 p-4 rounded-lg border border-gray-200">
            <h2 class="text-xl font-semibold text-left mb-4">Aperçu de la facture</h2>
            <div class="w-full h-[80vh] border rounded-md bg-white">
                <iframe id="invoice-iframe" src="{{ route('invoices.show', ['id' => $lastCommandeId]) }}" class="w-full h-full" frameborder="0"></iframe>
            </div>
        </div>

    </div>
</div>

@include('Front.footer-front')

<script>
    document.getElementById('download-invoice-btn').addEventListener('click', async function() {
        const { jsPDF } = window.jspdf;
        const downloadBtn = this;
        const downloadText = document.getElementById('download-text');
        const spinner = document.getElementById('download-spinner');
        
        // Show spinner and disable button
        downloadText.classList.add('hidden');
        spinner.classList.remove('hidden');
        downloadBtn.disabled = true;

        let hiddenContainer = null;

        try {
            // 1. Fetch the invoice HTML
            const response = await fetch("{{ route('invoices.show', ['id' => $lastCommandeId]) }}");
            if (!response.ok) {
                throw new Error('Failed to fetch invoice HTML. Status: ' + response.status);
            }
            const html = await response.text();

            // 2. Create a hidden container, append it to the body, and inject the HTML
            hiddenContainer = document.createElement('div');
            hiddenContainer.style.position = 'fixed';
            hiddenContainer.style.top = '-9999px'; // Position it off-screen
            hiddenContainer.style.left = '-9999px';
            document.body.appendChild(hiddenContainer);
            hiddenContainer.innerHTML = html;
            
            // Find the specific invoice element within the fetched HTML
            const invoiceElement = hiddenContainer.querySelector('.invoice-container');
            if (!invoiceElement) {
                throw new Error('Could not find .invoice-container in the fetched HTML.');
            }

            // Give the browser a moment to render styles and fonts from the injected HTML
            await new Promise(resolve => setTimeout(resolve, 500));

            // 3. Run html2canvas on the element
            const canvas = await html2canvas(invoiceElement, {
                scale: 2, // Increase scale for better resolution
                useCORS: true,
                imageTimeout: 5000, // Allow more time for images if any
                logging: false // Disable logging to console
            });

            // 4. Create jsPDF instance and add the image
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'p',
                unit: 'px',
                format: 'a4'
            });

            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = pdf.internal.pageSize.getHeight();
            const canvasWidth = canvas.width;
            const canvasHeight = canvas.height;
            const ratio = canvasWidth / canvasHeight;
            
            let newCanvasWidth = pdfWidth;
            let newCanvasHeight = newCanvasWidth / ratio;
            
            // Adjust dimensions if the content is taller than the page
            if (newCanvasHeight > pdfHeight) {
                newCanvasHeight = pdfHeight;
                newCanvasWidth = newCanvasHeight * ratio;
            }
            
            const x = (pdfWidth - newCanvasWidth) / 2;
            const y = 0;

            pdf.addImage(imgData, 'PNG', x, y, newCanvasWidth, newCanvasHeight);
            
            const commandeRef = "{{ $commande->paymentClient->monetico_order_id ?? $commande->id }}";
            pdf.save(`facture-HelloPassenger-${commandeRef}.pdf`);

        } catch (err) {
            console.error("PDF Generation Error:", err);
            alert("An error occurred while generating the PDF. Please try again.");
        } finally {
            // 5. Clean up by removing the hidden container
            if (hiddenContainer) {
                document.body.removeChild(hiddenContainer);
            }
            
            // Hide spinner and re-enable button
            downloadText.classList.remove('hidden');
            spinner.classList.add('hidden');
            downloadBtn.disabled = false;
        }
    });
</script>

</body>
</html>
