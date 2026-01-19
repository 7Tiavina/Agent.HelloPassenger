<div id="clientProfileModal" class="fixed inset-0 bg-[#212121] bg-opacity-80 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="relative mx-auto border-none max-w-5xl w-full shadow-2xl rounded-3xl bg-white overflow-hidden transform transition-all">
        
        <div class="bg-[#ffc107] p-6 text-[#212121] text-center">
            <h3 class="text-2xl font-bold">Dernière étape pour votre sécurité</h3>
            <p class="text-[#212121] text-opacity-90 text-sm mt-1">
                Conformément aux normes aéroportuaires, merci de valider vos informations de contact.
            </p>
        </div>

        <div class="p-8">
            <form id="clientProfileForm">
                @csrf
                
                <input type="hidden" name="email" id="modal-email">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div class="space-y-4">
                        <h4 class="font-bold text-[#212121] flex items-center">
                            <span class="w-8 h-8 bg-[#ffc107] bg-opacity-20 text-[#212121] rounded-full flex items-center justify-center mr-2 text-sm font-bold">1</span>
                            Vos coordonnées
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase">Prénom</label>
                                <input type="text" name="prenom" id="modal-prenom" placeholder="Prénom" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#ffc107] focus:ring-[#ffc107] transition-all py-3">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase">Nom</label>
                                <input type="text" name="nom" id="modal-nom" placeholder="Nom" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#ffc107] focus:ring-[#ffc107] transition-all py-3">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase">Téléphone mobile</label>
                            <input type="tel" name="telephone" id="modal-telephone" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#ffc107] focus:ring-[#ffc107] transition-all py-3">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase">Adresse</label>
                            <input type="text" name="adresse" id="modal-adresse" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#ffc107] focus:ring-[#ffc107] transition-all py-3">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <button id="toggleAdditionalFieldsBtn" type="button" class="text-sm font-medium text-[#212121] flex items-center">
                                <span id="toggleText">Compléter mon profil (facultatif)</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                        
                        <div id="additional-fields-container" class="hidden mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in">
                            <!-- Civilite -->
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase">Civilité</label>
                                <select name="civilite" id="modal-civilite" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-[#ffc107]">
                                    <option value="M.">Monsieur</option>
                                    <option value="Mme">Madame</option>
                                </select>
                            </div>
                            <!-- Nom Société -->
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase">Société</label>
                                <input type="text" name="nomSociete" id="modal-nomSociete" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-[#ffc107]">
                            </div>
                            <!-- Complément Adresse -->
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase">Complément</label>
                                <input type="text" name="complementAdresse" id="modal-complementAdresse" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-[#ffc107]">
                            </div>
                            <!-- Ville -->
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase">Ville</label>
                                <input type="text" name="ville" id="modal-ville" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-[#ffc107] py-3">
                            </div>
                            <!-- Code Postal -->
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase">Code Postal</label>
                                <input type="text" name="codePostal" id="modal-codePostal" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-[#ffc107] py-3">
                            </div>
                            <!-- Pays -->
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase">Pays</label>
                                <input type="text" name="pays" id="modal-pays" placeholder="Ex: France" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-[#ffc107] focus:ring-[#ffc107] transition-all py-3">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex flex-col md:flex-row gap-4 items-center justify-center">
                    <button id="closeClientProfileModalBtn" type="button" class="order-2 md:order-1 px-8 py-3 text-gray-400 font-semibold hover:text-gray-600 transition-all">
                        Annuler
                    </button>
                    <button id="saveClientProfileBtn" type="submit" class="order-1 md:order-2 px-12 py-4 bg-[#ffc107] text-[#212121] font-bold rounded-full shadow-lg hover:bg-[#212121] hover:text-white transform transition-all flex items-center">
                        Confirmer et payer
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>