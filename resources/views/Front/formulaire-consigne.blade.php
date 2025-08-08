<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('favicon-hellopassenger.png') }}">
    <title>Réserver une consigne - HelloPassenger</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'yellow-custom': '#FFC107',
                        'yellow-hover': '#FFB300',
                        'gray-dark': '#1f2937'
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #ffffff;
        }

        .baggage-option {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }

        .baggage-option:hover {
            border-color: #d1d5db;
        }

        .baggage-option.selected {
            border-color: #FFC107;
            background-color: #fef9e7;
        }

        .btn-hover {
            transition: all 0.3s ease;
        }

        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .input-style {
            border: 1px solid #e5e7eb;
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .input-style:focus {
            border-color: #FFC107;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.2);
        }

        .checkbox-custom {
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #9ca3af;
            border-radius: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .checkbox-custom.checked {
            background-color: #FFC107;
            border-color: #FFC107;
        }

        .checkbox-custom.checked::after {
            content: "✓";
            color: white;
            font-size: 0.875rem;
        }

        .custom-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }
        
        .add-baggage-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #FFC107;
            font-weight: 500;
            cursor: pointer;
        }
        
        .add-baggage-btn:hover {
            color: #FFB300;
        }
        
        .remove-baggage-btn {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .remove-baggage-btn:hover {
            background: #e9ecef;
        }
    </style>
</head>
<body class="min-h-screen bg-white">

@include('Front.header-front')

<!-- Main Content -->
<div class="max-w-6xl mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Réserver une consigne</h1>
    <p class="text-gray-600 mb-8">
        Sélectionnez le type de consigne et suivez les étapes du formulaire. Nous vous indiquerons les informations à fournir.
    </p>

    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-8">
        <span>Accueil</span>
        <span>→</span>
        <span class="text-gray-800 font-medium">Réserver une consigne</span>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Formulaire principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Lieu -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <p class="text-sm text-red-500 mb-4">* Tous les champs sont obligatoires</p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            DANS QUEL AÉROPORT SOUHAITEZ-VOUS LAISSER VOS BAGAGES ? *
                        </label>
                        <select class="input-style custom-select w-full">
                            <option value="" selected disabled>Sélectionner un aéroport</option>
                            <option value="cdg">Paris-Charles-de-Gaulle</option>
                            <option value="orly">Paris-Orly</option>
                            <option value="beauvais">Paris-Beauvais</option>
                        </select>
                    </div>

                    <!-- Premier bloc de bagage -->
                    <div class="baggage-block">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-medium text-gray-700">
                                QUEL EST LE TYPE DE BAGAGE CONSIGNÉ ? *
                            </label>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-3">
                            <!-- Bagage en cabine -->
                            <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="cabin">
                                <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                                        <rect x="6" y="8" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/>
                                        <circle cx="10" cy="18" r="1" fill="currentColor"/>
                                        <circle cx="14" cy="18" r="1" fill="currentColor"/>
                                        <path d="M10 10v4M14 10v4" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Bagage en cabine</span>
                            </div>

                            <!-- Bagage en soute -->
                            <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="hold">
                                <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                                        <rect x="5" y="6" width="14" height="12" rx="1" stroke="currentColor" stroke-width="2"/>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M5 10h14" stroke="currentColor" stroke-width="1.5"/>
                                        <circle cx="9" cy="15" r="1" fill="currentColor"/>
                                        <circle cx="15" cy="15" r="1" fill="currentColor"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Bagage en soute</span>
                            </div>

                            <!-- Vestiaire -->
                            <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="cloakroom">
                                <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                                        <path d="M16 10V8a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 10h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 10v-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M12 14v2" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Vestiaire</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="block text-sm text-gray-600 mb-2">COMBIEN ? *</label>
                            <div class="flex items-center space-x-2">
                                <button type="button" class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover btn-minus">−</button>
                                <input type="text" class="input-style w-16 text-center" value="1" readonly />
                                <button type="button" class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover btn-plus">+</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Conteneur pour les bagages supplémentaires -->
                    <div id="additional-baggages-container" class="space-y-6 mt-6"></div>
                    
                    <!-- Bouton pour ajouter un bagage supplémentaire -->
                    <div class="mt-4">
                        <div class="add-baggage-btn" id="add-baggage-type">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            <span>AJOUTER UN TYPE DE BAGAGE SUPPLÉMENTAIRE</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">DATE DE DÉPÔT DES BAGAGES *</h3>
                    <input type="date" class="input-style w-full mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">HEURE DE DÉPÔT *</label>
                    <input type="time" class="input-style w-full">
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">DATE DE RÉCUPÉRATION DES BAGAGES *</h3>
                    <input type="date" class="input-style w-full mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">HEURE DE RÉCUPÉRATION *</label>
                    <input type="time" class="input-style w-full">
                </div>
            </div>

            <!-- Alerte -->
            <div class="bg-yellow-custom rounded-lg p-6">
                <h3 class="font-bold text-black mb-2">ATTENTION !</h3>
                <p class="text-sm text-black leading-relaxed">
                    Les trajets pour la livraison ou la récupération des bagages peuvent inclure les gares : Gare du Nord, Châtelet Les Halles, Gare de Lyon, ou Saint-Michel Notre-Dame.
                </p>
            </div>

            <!-- CTA -->
            <div class="bg-gray-800 rounded-lg p-4 flex items-center justify-between">
                <p class="text-white text-sm">
                    Vous êtes un professionnel du tourisme ? Facilitez le voyage de vos clients !
                </p>
                <button class="bg-transparent border border-white text-white px-4 py-2 rounded-full text-sm hover:bg-white hover:text-gray-800 transition-colors">
                    DEVENIR PARTENAIRE →
                </button>
            </div>
        </div>

        <!-- Colonne sticky -->
        <div class="w-full lg:w-full relative" id="sticky-wrapper">
            <div id="sticky-summary" class="space-y-6">
                <!-- Résumé -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm text-center">
                    <p class="text-sm text-gray-600 mb-2">Notre tarif :</p>
                    <p class="text-xs text-gray-500 mb-4">Pour 24h (TVA incluse)</p>
                    <div class="text-4xl font-bold text-gray-800">0 €</div>
                </div>
                <!-- Panier vide -->
                <div class="bg-white border-2 border-yellow-400 rounded-lg p-6 shadow-sm text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-lg mx-auto mb-4 flex items-center justify-center">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="text-gray-400">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m1.6 8L9 11m-2 2v6a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-black mb-2">Votre panier est vide :(</h3>
                    <div class="bg-gray-100 rounded p-3 mt-4">
                        <p class="text-sm text-gray-600 mb-2">Total:</p>
                        <p class="text-2xl font-bold text-black">0€</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Front.footer-front')

<!-- Ajoute un ID pour détecter le footer en JS -->
<footer id="footer"></footer>

<script>
    // Sélection du type de bagage
    document.querySelectorAll('.baggage-option').forEach(option => {
        option.addEventListener('click', function () {
            // Désélectionner toutes les options dans le même bloc
            const block = this.closest('.baggage-block');
            block.querySelectorAll('.baggage-option').forEach(el => el.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Incrémentation dans chaque bloc
    document.addEventListener('click', function(e) {
        // Bouton plus
        if (e.target.classList.contains('btn-plus')) {
            const input = e.target.parentNode.querySelector('input');
            let value = parseInt(input.value) || 1;
            input.value = Math.max(1, Math.min(10, value + 1));
        }
        
        // Bouton moins
        if (e.target.classList.contains('btn-minus')) {
            const input = e.target.parentNode.querySelector('input');
            let value = parseInt(input.value) || 1;
            input.value = Math.max(1, Math.min(10, value - 1));
        }
    });

    // Ajout d'un nouveau type de bagage
    document.getElementById('add-baggage-type').addEventListener('click', function() {
        const container = document.getElementById('additional-baggages-container');
        const blocks = document.querySelectorAll('.baggage-block');
        
        // Limite à 5 blocs
        if (blocks.length >= 5) return;
        
        // Création du nouveau bloc
        const newBlock = document.createElement('div');
        newBlock.className = 'baggage-block relative border-t border-gray-200 pt-6 mt-6';
        newBlock.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-medium text-gray-700">
                    TYPE DE BAGAGE SUPPLÉMENTAIRE
                </label>
                <button type="button" class="remove-baggage-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-3">
                <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="cabin">
                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                            <rect x="6" y="8" width="12" height="10" rx="1" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/>
                            <circle cx="10" cy="18" r="1" fill="currentColor"/>
                            <circle cx="14" cy="18" r="1" fill="currentColor"/>
                            <path d="M10 10v4M14 10v4" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Bagage en cabine</span>
                </div>

                <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="hold">
                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                            <rect x="5" y="6" width="14" height="12" rx="1" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/>
                            <path d="M5 10h14" stroke="currentColor" stroke-width="1.5"/>
                            <circle cx="9" cy="15" r="1" fill="currentColor"/>
                            <circle cx="15" cy="15" r="1" fill="currentColor"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Bagage en soute</span>
                </div>

                <div class="baggage-option p-4 rounded-lg flex flex-col items-center space-y-2 cursor-pointer" data-type="cloakroom">
                    <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" class="text-gray-600">
                            <path d="M16 10V8a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 10h8v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 10v-2a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 14v2" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Vestiaire</span>
                </div>
            </div>

            <div class="mt-3">
                <label class="block text-sm text-gray-600 mb-2">COMBIEN ? *</label>
                <div class="flex items-center space-x-2">
                    <button type="button" class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover btn-minus">−</button>
                    <input type="text" class="input-style w-16 text-center" value="1" readonly />
                    <button type="button" class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center text-gray-600 hover:bg-gray-50 btn-hover btn-plus">+</button>
                </div>
            </div>
        `;
        
        container.appendChild(newBlock);
        
        // Ajouter l'événement pour la suppression
        newBlock.querySelector('.remove-baggage-btn').addEventListener('click', function() {
            newBlock.remove();
        });
    });

    // Sticky
    document.addEventListener("DOMContentLoaded", function () {
        const sticky = document.getElementById("sticky-summary");
        const wrapper = document.getElementById("sticky-wrapper");
        const footer = document.getElementById("footer");

        function updateStickyPosition() {
            const stickyRect = sticky.getBoundingClientRect();
            const footerRect = footer.getBoundingClientRect();
            const wrapperRect = wrapper.getBoundingClientRect();
            const viewportHeight = window.innerHeight;

            sticky.style.width = wrapperRect.width + "px";
            sticky.style.left = wrapperRect.left + "px";

            if (stickyRect.bottom > footerRect.top) {
                sticky.style.position = "absolute";
                sticky.style.top = "auto";
                sticky.style.bottom = "0";
            } else if (window.scrollY + viewportHeight < footer.offsetTop) {
                sticky.style.position = "fixed";
                sticky.style.top = "100px";
                sticky.style.bottom = "auto";
            }
        }

        window.addEventListener("scroll", updateStickyPosition);
        window.addEventListener("resize", updateStickyPosition);
        updateStickyPosition();
    });
</script>

</body>
</html>