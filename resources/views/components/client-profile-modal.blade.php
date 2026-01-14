<div id="clientProfileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border max-w-xl shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Mettre à jour mes informations</h3>
            <p class="mt-2 text-sm text-gray-500 px-4">
                Tous les bagages déposés en consigne sont préalablement contrôlés par Rayon X. Pour des raisons de sûreté - sécurité nous avons donc besoin d'informations complémentaires pour valider votre commande.
            </p>
            <div class="mt-4 px-7 py-3">
                <form id="clientProfileForm" class="space-y-4">
                    @csrf
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="modal-email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                    </div>
                    <!-- Nom -->
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                        <input type="text" name="nom" id="modal-nom" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                    </div>
                    <!-- Prenom -->
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                        <input type="text" name="prenom" id="modal-prenom" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                    </div>
                    <!-- Telephone -->
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="tel" name="telephone" id="modal-telephone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                    </div>
                    <!-- Civilite -->
                    <div>
                        <label for="civilite" class="block text-sm font-medium text-gray-700">Civilité</label>
                        <select name="civilite" id="modal-civilite" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                            <option value="M.">M.</option>
                            <option value="Mme">Mme</option>
                            <option value="Mlle">Mlle</option>
                        </select>
                    </div>
                    <!-- Nom Société -->
                    <div>
                        <label for="nomSociete" class="block text-sm font-medium text-gray-700">Nom Société (optionnel)</label>
                        <input type="text" name="nomSociete" id="modal-nomSociete" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                    </div>
                    <!-- Adresse -->
                    <div>
                        <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                        <input type="text" name="adresse" id="modal-adresse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                    </div>
                    <!-- Complément Adresse -->
                    <div>
                        <label for="complementAdresse" class="block text-sm font-medium text-gray-700">Complément Adresse (optionnel)</label>
                        <input type="text" name="complementAdresse" id="modal-complementAdresse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                    </div>
                    <!-- Ville -->
                    <div>
                        <label for="ville" class="block text-sm font-medium text-gray-700">Ville</label>
                        <input type="text" name="ville" id="modal-ville" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                    </div>
                    <!-- Code Postal -->
                    <div>
                        <label for="codePostal" class="block text-sm font-medium text-gray-700">Code Postal</label>
                        <input type="text" name="codePostal" id="modal-codePostal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                    </div>
                    <!-- Pays -->
                    <div>
                        <label for="pays" class="block text-sm font-medium text-gray-700">Pays</label>
                        <input type="text" name="pays" id="modal-pays" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-base">
                    </div>

                    <div class="items-center px-4 py-3">
                        <button id="saveClientProfileBtn" type="submit" class="px-4 py-2 bg-yellow-custom text-gray-dark font-medium rounded-md w-full shadow-sm hover:bg-yellow-hover focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
                            Enregistrer
                        </button>
                        <button id="closeClientProfileModalBtn" type="button" class="mt-3 px-4 py-2 bg-gray-200 text-gray-800 font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>