<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - HelloPassenger</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">Mes Réservations</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Succès!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($commandes->isEmpty())
            <p class="text-gray-600">Vous n'avez pas encore de commandes.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                ID Commande
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Plateforme
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Total TTC
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commandes as $commande)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-b border-gray-200 text-sm text-gray-900">
                                    {{ $commande->id_api_commande ?? $commande->id }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200 text-sm text-gray-900">
                                    {{ $commande->id_plateforme }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200 text-sm text-gray-900">
                                    {{ number_format($commande->total_prix_ttc, 2) }} €
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200 text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $commande->statut == 'completed' ? 'bg-green-100 text-green-800' : ($commande->statut == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($commande->statut) }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200 text-sm text-gray-900">
                                    {{ $commande->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200 text-sm text-gray-900">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">Détails</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>