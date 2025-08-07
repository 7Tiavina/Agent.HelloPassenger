<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelloPassenger - Facilite votre voyage à Paris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'yellow-custom': '#FFC107',
                        'yellow-hover': '#FFB300',
                        'gray-light': '#f3f4f6',
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
        
        .service-card {
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .luggage-bg {
            position: absolute;
            inset: 0;
            opacity: 0.3;
            z-index: 0;
        }
        
        .stacked-card {
            position: absolute;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .btn-hover {
            transition: all 0.3s ease;
        }
        
        .btn-hover:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .btn-yellow {
            background-color: #FFC107;
            color: #000;
            transition: all 0.3s ease;
        }
        
        .btn-yellow:hover {
            background-color: #FFB300;
        }
        
        .cloud-svg {
            opacity: 0.4;
        }
    </style>
</head>
<body class="min-h-screen bg-white">
    <!-- Header -->
    <header class="bg-yellow-custom px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                    <span class="text-yellow-custom text-sm font-bold">H</span>
                </div>
                <span class="text-black font-semibold text-lg">HelloPassenger</span>
            </div>
            <div class="flex items-center space-x-4">
                <button class="bg-gray-dark text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-gray-700 transition-colors btn-hover">
                    SE CONNECTER
                </button>
                <button class="bg-gray-dark text-white px-6 py-2 rounded-full text-sm font-medium hover:bg-gray-700 transition-colors btn-hover">
                    MES RÉSERVATIONS
                </button>
                <div class="flex items-center space-x-2 cursor-pointer">
                    <span class="text-black text-sm font-medium">MENU</span>
                    <div class="flex flex-col space-y-1">
                        <div class="w-4 h-0.5 bg-black"></div>
                        <div class="w-4 h-0.5 bg-black"></div>
                        <div class="w-4 h-0.5 bg-black"></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-yellow-custom px-6 py-16 relative overflow-hidden">
        <!-- Background luggage illustrations -->
        <div class="luggage-bg">
            <!-- Left side luggage -->
            <div class="absolute left-4 bottom-20">
                <svg width="60" height="80" viewBox="0 0 60 80" fill="none" class="text-white">
                    <rect x="10" y="20" width="40" height="50" rx="4" stroke="currentColor" stroke-width="2" fill="none"/>
                    <rect x="15" y="25" width="30" height="5" rx="2" fill="currentColor"/>
                    <rect x="15" y="35" width="30" height="5" rx="2" fill="currentColor"/>
                    <circle cx="25" cy="15" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
                    <line x1="25" y1="12" x2="25" y2="20" stroke="currentColor" stroke-width="2"/>
                    <circle cx="15" cy="75" r="4" stroke="currentColor" stroke-width="2" fill="none"/>
                    <circle cx="45" cy="75" r="4" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
            </div>

            <!-- Left side backpack -->
            <div class="absolute left-20 bottom-16">
                <svg width="40" height="60" viewBox="0 0 40 60" fill="none" class="text-white">
                    <path d="M8 15 L32 15 L32 50 L8 50 Z" stroke="currentColor" stroke-width="2" fill="none"/>
                    <rect x="12" y="8" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                    <circle cx="14" cy="25" r="2" fill="currentColor"/>
                    <circle cx="26" cy="25" r="2" fill="currentColor"/>
                    <path d="M10 20 L30 20" stroke="currentColor" stroke-width="2"/>
                    <path d="M10 35 L30 35" stroke="currentColor" stroke-width="2"/>
                </svg>
            </div>

            <!-- Right side suitcases -->
            <div class="absolute right-4 bottom-20">
                <svg width="80" height="100" viewBox="0 0 80 100" fill="none" class="text-white">
                    <rect x="45" y="30" width="25" height="35" rx="3" stroke="currentColor" stroke-width="2" fill="none"/>
                    <rect x="48" y="25" width="19" height="6" rx="2" fill="currentColor"/>
                    <circle cx="52" cy="20" r="2" stroke="currentColor" stroke-width="2" fill="none"/>
                    <line x1="52" y1="18" x2="52" y2="25" stroke="currentColor" stroke-width="2"/>
                    <circle cx="50" cy="70" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
                    <circle cx="65" cy="70" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
            </div>

            <div class="absolute right-20 bottom-12">
                <svg width="50" height="70" viewBox="0 0 50 70" fill="none" class="text-white">
                    <rect x="10" y="20" width="30" height="40" rx="3" stroke="currentColor" stroke-width="2" fill="none"/>
                    <rect x="13" y="15" width="24" height="8" rx="2" fill="currentColor"/>
                    <circle cx="20" cy="10" r="2" stroke="currentColor" stroke-width="2" fill="none"/>
                    <line x1="20" y1="8" x2="20" y2="15" stroke="currentColor" stroke-width="2"/>
                    <circle cx="17" cy="65" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
                    <circle cx="33" cy="65" r="3" stroke="currentColor" stroke-width="2" fill="none"/>
                </svg>
            </div>

            <!-- Bottom scattered luggage -->
            <div class="absolute bottom-8 left-1/4">
                <svg width="35" height="25" viewBox="0 0 35 25" fill="none" class="text-white">
                    <rect x="5" y="8" width="25" height="12" rx="2" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    <circle cx="12" cy="14" r="1.5" fill="currentColor"/>
                    <circle cx="23" cy="14" r="1.5" fill="currentColor"/>
                    <rect x="8" y="5" width="19" height="4" rx="1" fill="currentColor"/>
                </svg>
            </div>

            <div class="absolute bottom-6 right-1/3">
                <svg width="30" height="35" viewBox="0 0 30 35" fill="none" class="text-white">
                    <rect x="8" y="10" width="14" height="20" rx="2" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    <circle cx="12" cy="5" r="2" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    <line x1="12" y1="3" x2="12" y2="10" stroke="currentColor" stroke-width="1.5"/>
                    <circle cx="12" cy="32" r="2" stroke="currentColor" stroke-width="1.5" fill="none"/>
                    <circle cx="20" cy="32" r="2" stroke="currentColor" stroke-width="1.5" fill="none"/>
                </svg>
            </div>

            <!-- Clouds -->
            <div class="absolute top-16 left-10">
                <svg width="60" height="30" viewBox="0 0 60 30" fill="none" class="text-white cloud-svg">
                    <path d="M15 20 C10 20 8 16 12 14 C10 10 16 8 20 12 C24 8 30 10 28 14 C32 16 30 20 25 20 Z" fill="currentColor"/>
                </svg>
            </div>

            <div class="absolute top-20 right-16">
                <svg width="50" height="25" viewBox="0 0 50 25" fill="none" class="text-white cloud-svg">
                    <path d="M12 16 C8 16 6 13 9 12 C8 9 13 7 16 10 C19 7 24 8 23 12 C26 13 25 16 22 16 Z" fill="currentColor"/>
                </svg>
            </div>

            <div class="absolute top-12 left-1/2 transform -translate-x-1/2">
                <svg width="40" height="20" viewBox="0 0 40 20" fill="none" class="text-white cloud-svg">
                    <path d="M10 14 C7 14 5 11 8 10 C7 8 11 6 13 8 C15 6 19 7 18 10 C20 11 19 14 17 14 Z" fill="currentColor"/>
                </svg>
            </div>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-black mb-12 leading-tight">
                    HelloPassenger facilite<br />
                    votre voyage à Paris !
                </h1>

                <!-- Two cards -->
                <div class="flex flex-col md:flex-row gap-6 justify-center items-center max-w-4xl mx-auto mb-8">
                    <div class="bg-white rounded-xl p-8 w-full md:w-96 transform hover:scale-105 transition-transform duration-300 service-card">
                        <div class="text-center">
                            <div class="text-xs text-gray-500 tracking-wider mb-3">TRANSPORT DE BAGAGES</div>
                            <h3 class="font-bold text-xl mb-6 leading-tight text-gray-800">
                                Voyagez léger :<br />
                                nous acheminons<br />
                                vos bagages !
                            </h3>
                            <div class="w-12 h-12 bg-yellow-custom rounded-full mx-auto mb-6 flex items-center justify-center">
                                <span class="text-black font-bold text-lg">→</span>
                            </div>
                            <button class="bg-yellow-custom text-black hover:bg-yellow-hover w-12 h-12 rounded-full p-0 shadow-md btn-hover">
                                →
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-8 w-full md:w-96 transform hover:scale-105 transition-transform duration-300 service-card">
                        <div class="text-center">
                            <div class="text-xs text-gray-500 tracking-wider mb-3">CONSIGNE À BAGAGES</div>
                            <h3 class="font-bold text-xl mb-6 leading-tight text-gray-800">
                                Une escale à Paris ?<br />
                                Nous gardons<br />
                                vos bagages !
                            </h3>
                            <div class="w-12 h-12 bg-yellow-custom rounded-full mx-auto mb-6 flex items-center justify-center">
                                <span class="text-black font-bold text-lg">→</span>
                            </div>
                            <button class="bg-yellow-custom text-black hover:bg-yellow-hover w-12 h-12 rounded-full p-0 shadow-md btn-hover">
                                →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content section -->
    <section class="bg-gray-light px-6 py-20">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-black mb-8">
                        Avec HelloPassenger...
                    </h2>
                    <p class="text-gray-600 mb-6 text-lg">
                        Voyagez malin et voyagez bien !
                    </p>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        Nous vous proposons une solution innovante pour simplifier vos 
                        déplacements. Que vous soyez en voyage d'affaires ou en vacances, 
                        HelloPassenger vous accompagne pour un transport plus simple et 
                        plus pratique.
                    </p>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        HelloPassenger vous accompagne et vous livre partout en 
                        France ! Pas de souci plus besoin de porter vos bagages ! Nous 
                        nous occupons de tout.
                    </p>
                    <button class="bg-yellow-custom text-black hover:bg-yellow-hover px-8 py-3 text-lg font-medium rounded-lg shadow-md btn-hover">
                        Voir toutes nos offres
                    </button>
                </div>
                <div class="flex justify-center">
                    <div class="relative w-64 h-64">
                        <div class="stacked-card w-40 h-32 bg-yellow-custom transform -rotate-6"></div>
                        <div class="stacked-card w-40 h-32 bg-blue-500 absolute top-6 left-10 transform rotate-12"></div>
                        <div class="stacked-card w-32 h-24 bg-green-400 absolute top-12 left-20 transform rotate-6"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services grid -->
    <section class="bg-white px-6 py-16">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-6">
                <div class="bg-gray-200 rounded-2xl overflow-hidden service-card">
                    <div class="h-40 bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none" class="text-gray-600">
                            <path d="M15 40C10 40 8 36 12 34C10 30 16 28 20 32C24 28 30 30 28 34C32 36 30 40 25 40Z" stroke="currentColor" stroke-width="2" fill="none"/>
                            <circle cx="25" cy="25" r="5" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path d="M30 20L40 15" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-gray-800 text-lg mb-4">Location<br />de poussettes</h3>
                        <button class="bg-yellow-custom text-black hover:bg-yellow-hover rounded-full w-10 h-10 p-0 btn-hover">
                            →
                        </button>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-2xl overflow-hidden service-card">
                    <div class="h-40 bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none" class="text-gray-300">
                            <circle cx="30" cy="25" r="15" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path d="M25 40L20 55L30 50L40 55L35 40" stroke="currentColor" stroke-width="2"/>
                            <path d="M20 25H40" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-white text-lg mb-4">Objets perdus</h3>
                        <button class="bg-yellow-custom text-black hover:bg-yellow-hover rounded-full w-10 h-10 p-0 btn-hover">
                            →
                        </button>
                    </div>
                </div>

                <div class="bg-gray-600 rounded-2xl overflow-hidden service-card">
                    <div class="h-40 bg-gradient-to-br from-gray-500 to-gray-700 flex items-center justify-center">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none" class="text-gray-200">
                            <rect x="15" y="15" width="30" height="40" rx="5" stroke="currentColor" stroke-width="2" fill="none"/>
                            <path d="M20 25H40" stroke="currentColor" stroke-width="2"/>
                            <path d="M20 35H40" stroke="currentColor" stroke-width="2"/>
                            <circle cx="25" cy="20" r="2" fill="currentColor"/>
                            <circle cx="35" cy="20" r="2" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-white text-lg mb-4">Vestiaires</h3>
                        <button class="bg-yellow-custom text-black hover:bg-yellow-hover rounded-full w-10 h-10 p-0 btn-hover">
                            →
                        </button>
                    </div>
                </div>

                <div class="bg-yellow-custom rounded-2xl p-8 flex flex-col justify-center items-center service-card">
                    <h3 class="font-bold text-black text-xl leading-tight text-center">Découvrez<br />tous nos services</h3>
                    <div class="mt-4 w-12 h-12 bg-white rounded-full flex items-center justify-center animate-bounce">
                        <span class="text-black font-bold text-lg">→</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services description -->
    <section class="bg-gray-light px-6 py-20">
        <div class="max-w-7xl mx-auto text-center">
            <h2 class="text-3xl font-bold text-black mb-4">
                HelloPassenger :
            </h2>
            <p class="text-xl text-black mb-4">
                Votre plateforme de réservation
            </p>
            <p class="text-xl text-black mb-16">
                de services dans les aéroports Parisiens
            </p>

            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-yellow-custom to-yellow-hover rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-lg">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="text-yellow-custom">
                                <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15" stroke="currentColor" stroke-width="2"/>
                                <path d="M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5V7H9V5Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 12V16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="12" cy="9" r="1" fill="currentColor"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-bold text-black mb-4 text-lg">TROUVEZ ET RÉSERVEZ<br />VOS SERVICES</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Réservez en quelques clics tous les services dont vous avez besoin pour votre voyage.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-lg">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="text-blue-500">
                                <path d="M20 10C20 14.4183 16.4183 18 12 18C7.58172 18 4 14.4183 4 10C4 5.58172 7.58172 2 12 2C16.4183 2 20 5.58172 20 10Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 18V22" stroke="currentColor" stroke-width="2"/>
                                <path d="M8 22H16" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-bold text-black mb-4 text-lg">PRÉPAREZ<br />VOS VACANCES</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Anticipez et organisez votre voyage pour partir serein et détendu.
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl mx-auto mb-6 flex items-center justify-center shadow-lg">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="text-green-500">
                                <path d="M15 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M9 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M20 9H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M20 15H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M4 3H10V7H4V3Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M4 17H10V21H4V17Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-bold text-black mb-4 text-lg">PROFITEZ<br />DE VOTRE SÉJOUR</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Voyagez l'esprit tranquille en ayant tout organisé à l'avance.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-yellow-custom px-6 py-12">
        <div class="max-w-7xl mx-auto text-center">
            <p class="text-black mb-6 leading-relaxed">
                Offre valable uniquement sur hellopassenger.com - Mentions légales applicables aux commandes
            </p>
            <div class="flex justify-center space-x-8">
                <a href="#" class="text-black hover:text-gray-700 underline font-medium transition-colors">
                    POLITIQUE CONFIDENTIALE →
                </a>
            </div>
            <div class="mt-8 flex justify-center space-x-6">
                <a href="#" class="text-black hover:text-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.129 22 16.99 22 12z"/>
                    </svg>
                </a>
                <a href="#" class="text-black hover:text-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                    </svg>
                </a>
                <a href="#" class="text-black hover:text-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                </a>
            </div>
        </div>
    </footer>
</body>
</html>