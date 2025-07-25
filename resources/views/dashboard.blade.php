<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelloPassenger Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        yellow: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                        }
                    }
                }
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des onglets
            const tabs = document.querySelectorAll('[data-tab]');
            const tabContents = document.querySelectorAll('[data-tab-content]');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    
                    // Mise à jour des onglets actifs
                    tabs.forEach(t => t.classList.remove('bg-yellow-400', 'text-gray-800'));
                    tab.classList.add('bg-yellow-400', 'text-gray-800');
                    
                    // Affichage du contenu correspondant
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                        if(content.id === tabId) {
                            content.classList.remove('hidden');
                        }
                    });
                });
            });
            
            // Simulation de données pour les graphiques
            const stats = [
                {
                    title: "Réservations aujourd'hui",
                    value: "24",
                    change: "+12%",
                    changeType: "increase",
                    color: "text-blue-600",
                    icon: "calendar"
                },
                {
                    title: "Revenus du mois",
                    value: "€3,240",
                    change: "+8.2%",
                    changeType: "increase",
                    color: "text-green-600",
                    icon: "euro-sign"
                },
                {
                    title: "Utilisateurs actifs",
                    value: "1,840",
                    change: "+23%",
                    changeType: "increase",
                    color: "text-purple-600",
                    icon: "users"
                },
                {
                    title: "Taux d'occupation",
                    value: "85%",
                    change: "-5%",
                    changeType: "decrease",
                    color: "text-orange-600",
                    icon: "suitcase"
                }
            ];
            
            const recentOrders = [
                {
                    id: "R001",
                    customer: "Marie Dubois",
                    email: "marie@email.com",
                    service: "Consigne 24h",
                    location: "Gare du Nord",
                    price: "€15",
                    status: "active",
                    time: "14:30"
                },
                {
                    id: "R002",
                    customer: "Pierre Martin", 
                    email: "pierre@email.com",
                    service: "Transfert Aéroport",
                    location: "CDG Terminal 2",
                    price: "€45",
                    status: "completed",
                    time: "12:15"
                },
                {
                    id: "R003",
                    customer: "Sophie Chen",
                    email: "sophie@email.com", 
                    service: "Consigne 48h",
                    location: "Châtelet",
                    price: "€25",
                    status: "pending",
                    time: "16:45"
                },
                {
                    id: "R004",
                    customer: "Lucas Bernard",
                    email: "lucas@email.com",
                    service: "Consigne 24h",
                    location: "République",
                    price: "€18",
                    status: "active",
                    time: "10:20"
                },
                {
                    id: "R005",
                    customer: "Emma Rodriguez",
                    email: "emma@email.com",
                    service: "Transfert Gare",
                    location: "Gare de Lyon",
                    price: "€35",
                    status: "completed",
                    time: "18:00"
                }
            ];
            
            // Fonction pour générer les badges de statut
            function getStatusBadge(status) {
                const statusConfig = {
                    active: { label: "Actif", color: "bg-green-100 text-green-800" },
                    completed: { label: "Terminé", color: "bg-blue-100 text-blue-800" },
                    pending: { label: "En attente", color: "bg-orange-100 text-orange-800" },
                    inactive: { label: "Inactif", color: "bg-red-100 text-red-800" }
                };
                
                const config = statusConfig[status] || statusConfig.pending;
                return `<span class="px-2 py-1 rounded-full text-xs ${config.color}">${config.label}</span>`;
            }
            
            // Générer les cartes de statistiques
            const statsContainer = document.getElementById('stats-cards');
            if(statsContainer) {
                stats.forEach(stat => {
                    statsContainer.innerHTML += `
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600 mb-1">${stat.title}</p>
                                        <p class="text-2xl text-gray-800">${stat.value}</p>
                                        <div class="flex items-center mt-2">
                                            <span class="text-xs ${stat.changeType === 'increase' ? 'text-green-600' : 'text-red-600'}">
                                                ${stat.change}
                                            </span>
                                            <span class="text-xs text-gray-500 ml-1">vs mois dernier</span>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-lg bg-gray-50 ${stat.color}">
                                        <i class="fas fa-${stat.icon} text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            
            // Générer les commandes récentes
            const ordersContainer = document.getElementById('recent-orders');
            if(ordersContainer) {
                recentOrders.forEach(order => {
                    ordersContainer.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-800">${order.id}</td>
                            <td class="px-4 py-3 text-gray-800">${order.customer}</td>
                            <td class="px-4 py-3 text-gray-600">${order.service}</td>
                            <td class="px-4 py-3 text-gray-600">${order.location}</td>
                            <td class="px-4 py-3 text-gray-800">${order.price}</td>
                            <td class="px-4 py-3">${getStatusBadge(order.status)}</td>
                            <td class="px-4 py-3 text-gray-600">${order.time}</td>
                        </tr>
                    `;
                });
            }
        });
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        
        .dashboard-header {
            position: sticky;
            top: 0;
            z-index: 40;
        }
        
        .sidebar {
            position: sticky;
            top: 73px;
            height: calc(100vh - 73px);
        }
        
        .chart-container {
            height: 300px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 8px;
        }
        
        .pie-chart {
            height: 300px;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 8px;
        }
        
        .status-badge {
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background-color: #e5e7eb;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #f59e0b;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 dashboard-header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-400 rounded-lg flex items-center justify-center">
                    <i class="fas fa-map-marker-alt text-gray-800"></i>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">HelloPassenger Admin</h1>
                    <p class="text-sm text-gray-600">Tableau de bord administrateur</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button class="p-2 rounded-full hover:bg-gray-100 relative">
                        <i class="fas fa-bell text-gray-700"></i>
                        <span class="absolute top-1 right-1 w-3 h-3 bg-red-500 rounded-full text-xs flex items-center justify-center text-white">
                            3
                        </span>
                    </button>
                </div>
                <button class="p-2 rounded-full hover:bg-gray-100">
                    <i class="fas fa-cog text-gray-700"></i>
                </button>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-gray-800 font-medium">
                        AD
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-800">Admin</p>
                        <p class="text-xs text-gray-600">admin@hellopassenger.com</p>
                    </div>
                </div>
                <button class="p-2 rounded-full hover:bg-gray-100">
                    <i class="fas fa-sign-out-alt text-gray-700"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 sidebar">
            <nav class="p-6">
                <div class="space-y-2">
                    <button 
                        data-tab="overview"
                        class="w-full flex items-center px-4 py-2 rounded-md bg-yellow-400 text-gray-800 hover:bg-yellow-500"
                    >
                        <i class="fas fa-chart-line mr-3"></i>
                        Vue d'ensemble
                    </button>
                    <button 
                        data-tab="orders"
                        class="w-full flex items-center px-4 py-2 rounded-md hover:bg-gray-100 text-gray-700"
                    >
                        <i class="fas fa-box mr-3"></i>
                        Commandes
                    </button>
                    <button 
                        data-tab="users"
                        class="w-full flex items-center px-4 py-2 rounded-md hover:bg-gray-100 text-gray-700"
                    >
                        <i class="fas fa-users mr-3"></i>
                        Utilisateurs
                    </button>
                    <button 
                        data-tab="reservations"
                        class="w-full flex items-center px-4 py-2 rounded-md hover:bg-gray-100 text-gray-700"
                    >
                        <i class="fas fa-calendar mr-3"></i>
                        Réservations
                    </button>
                    <button 
                        data-tab="analytics"
                        class="w-full flex items-center px-4 py-2 rounded-md hover:bg-gray-100 text-gray-700"
                    >
                        <i class="fas fa-chart-bar mr-3"></i>
                        Analytiques
                    </button>
                </div>

                <!-- Quick Actions -->
                <div class="mt-8">
                    <p class="text-xs text-gray-500 mb-3 uppercase tracking-wider">Actions rapides</p>
                    <div class="space-y-2">
                        <button class="w-full flex items-center px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs">
                            <i class="fas fa-plus mr-2"></i>
                            Nouvelle réservation
                        </button>
                        <button class="w-full flex items-center px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs">
                            <i class="fas fa-download mr-2"></i>
                            Exporter données
                        </button>
                    </div>
                </div>

                <!-- Live Notifications -->
                <div class="mt-8">
                    <p class="text-xs text-gray-500 mb-3 uppercase tracking-wider">Notifications</p>
                    <div class="space-y-3">
                        <div class="text-xs p-2 bg-gray-50 rounded-lg">
                            <p class="text-gray-800 mb-1">Nouvelle réservation de Marie Dubois</p>
                            <p class="text-gray-500">Il y a 5 min</p>
                        </div>
                        <div class="text-xs p-2 bg-gray-50 rounded-lg">
                            <p class="text-gray-800 mb-1">Consigne #47 bientôt libérée</p>
                            <p class="text-gray-500">Il y a 15 min</p>
                        </div>
                        <div class="text-xs p-2 bg-gray-50 rounded-lg">
                            <p class="text-gray-800 mb-1">Paiement reçu - €45</p>
                            <p class="text-gray-500">Il y a 30 min</p>
                        </div>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Vue d'ensemble -->
            <div id="overview" data-tab-content>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-gray-800">Vue d'ensemble</h2>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full border border-green-200 bg-green-50 text-green-700 text-sm">
                                <span class="w-2 h-2 bg-green-500 rounded-full inline-block mr-2"></span>
                                Système en ligne
                            </span>
                            <button class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                                <i class="fas fa-download mr-2"></i>
                                Rapport
                            </button>
                        </div>
                    </div>
                        
                    <!-- Stats Cards -->
                    <div id="stats-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Les cartes de stats seront générées dynamiquement -->
                    </div>

                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800">Évolution des revenus</h3>
                                <p class="text-sm text-gray-600">Revenus mensuels et nombre de commandes</p>
                            </div>
                            <div class="p-6">
                                <div class="chart-container"></div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800">Répartition des services</h3>
                                <p class="text-sm text-gray-600">Distribution des types de réservations</p>
                            </div>
                            <div class="p-6">
                                <div class="pie-chart"></div>
                                <div class="grid grid-cols-2 gap-2 mt-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-yellow-500"></div>
                                        <span class="text-xs text-gray-600">Consigne 24h</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-yellow-400"></div>
                                        <span class="text-xs text-gray-600">Consigne 48h</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-yellow-300"></div>
                                        <span class="text-xs text-gray-600">Transfert Aéroport</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-yellow-200"></div>
                                        <span class="text-xs text-gray-600">Autres</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Activité récente</h3>
                                <p class="text-sm text-gray-600">Dernières commandes et réservations</p>
                            </div>
                            <button class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                                Voir tout
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">ID</th>
                                        <th class="px-4 py-3">Client</th>
                                        <th class="px-4 py-3">Service</th>
                                        <th class="px-4 py-3">Localisation</th>
                                        <th class="px-4 py-3">Prix</th>
                                        <th class="px-4 py-3">Statut</th>
                                        <th class="px-4 py-3">Heure</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-orders" class="divide-y divide-gray-200 text-sm">
                                    <!-- Les commandes récentes seront générées dynamiquement -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commandes -->
            <div id="orders" data-tab-content class="hidden">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-gray-800">Gestion des commandes</h2>
                        <div class="flex items-center gap-3">
                            <button class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                                <i class="fas fa-download mr-2"></i>
                                Exporter
                            </button>
                            <button class="px-4 py-2 rounded-md bg-yellow-400 text-gray-800 hover:bg-yellow-500">
                                <i class="fas fa-plus mr-2"></i>
                                Nouvelle commande
                            </button>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="p-4">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Terminées</p>
                                        <p class="text-xl text-gray-800">156</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="p-4">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-clock text-orange-600 text-2xl"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">En cours</p>
                                        <p class="text-xl text-gray-800">24</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="p-4">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">En attente</p>
                                        <p class="text-xl text-gray-800">8</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="p-4">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-euro-sign text-blue-600 text-2xl"></i>
                                    <div>
                                        <p class="text-sm text-gray-600">Revenus jour</p>
                                        <p class="text-xl text-gray-800">€450</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="p-4">
                            <div class="flex items-center gap-4 flex-wrap">
                                <div class="relative flex-1 min-w-[200px]">
                                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" placeholder="Rechercher une commande..." class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                </div>
                                <select class="w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    <option value="">Filtrer par statut</option>
                                    <option value="active">Actif</option>
                                    <option value="completed">Terminé</option>
                                    <option value="pending">En attente</option>
                                </select>
                                <select class="w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    <option value="">Service</option>
                                    <option value="consigne">Consigne</option>
                                    <option value="transfert">Transfert</option>
                                </select>
                                <button class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-filter mr-2"></i>
                                    Filtres
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">ID</th>
                                        <th class="px-4 py-3">Client</th>
                                        <th class="px-4 py-3">Service</th>
                                        <th class="px-4 py-3">Localisation</th>
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3">Prix</th>
                                        <th class="px-4 py-3">Statut</th>
                                        <th class="px-4 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 text-sm">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-800">R001</td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-yellow-100 text-gray-800 flex items-center justify-center">
                                                    MD
                                                </div>
                                                <div>
                                                    <p class="text-gray-800">Marie Dubois</p>
                                                    <p class="text-xs text-gray-600">marie@email.com</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">Consigne 24h</td>
                                        <td class="px-4 py-3 text-gray-600">Gare du Nord</td>
                                        <td class="px-4 py-3 text-gray-600">2024-01-15</td>
                                        <td class="px-4 py-3 text-gray-800">€15</td>
                                        <td class="px-4 py-3">
                                            <span class="status-badge bg-green-100 text-green-800">Actif</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <button class="p-1 rounded-md hover:bg-gray-100">
                                                    <i class="fas fa-eye text-gray-600"></i>
                                                </button>
                                                <button class="p-1 rounded-md hover:bg-gray-100">
                                                    <i class="fas fa-edit text-gray-600"></i>
                                                </button>
                                                <button class="p-1 rounded-md hover:bg-gray-100">
                                                    <i class="fas fa-trash text-gray-600"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Plus de lignes ici -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Autres onglets -->
            <div id="users" data-tab-content class="hidden">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-gray-800">Gestion des utilisateurs</h2>
                        <div class="flex items-center gap-3">
                            <button class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                                <i class="fas fa-download mr-2"></i>
                                Exporter
                            </button>
                            <button class="px-4 py-2 rounded-md bg-yellow-400 text-gray-800 hover:bg-yellow-500">
                                <i class="fas fa-plus mr-2"></i>
                                Nouvel utilisateur
                            </button>
                        </div>
                    </div>
                    <!-- Contenu de l'onglet Utilisateurs -->
                </div>
            </div>

            <div id="reservations" data-tab-content class="hidden">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-gray-800">Gestion des réservations</h2>
                        <div class="flex items-center gap-3">
                            <button class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                                <i class="fas fa-calendar mr-2"></i>
                                Planning
                            </button>
                            <button class="px-4 py-2 rounded-md bg-yellow-400 text-gray-800 hover:bg-yellow-500">
                                <i class="fas fa-plus mr-2"></i>
                                Nouvelle réservation
                            </button>
                        </div>
                    </div>
                    <!-- Contenu de l'onglet Réservations -->
                </div>
            </div>

            <div id="analytics" data-tab-content class="hidden">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-gray-800">Analytiques avancées</h2>
                        <div class="flex items-center gap-3">
                            <select class="w-48 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="30d" selected>30 derniers jours</option>
                                <option value="7d">7 derniers jours</option>
                                <option value="90d">3 derniers mois</option>
                                <option value="1y">Année</option>
                            </select>
                            <button class="px-3 py-1 rounded-md border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                                <i class="fas fa-download mr-2"></i>
                                Exporter
                            </button>
                        </div>
                    </div>
                    <!-- Contenu de l'onglet Analytiques -->
                </div>
            </div>
        </main>
    </div>
</body>
</html>