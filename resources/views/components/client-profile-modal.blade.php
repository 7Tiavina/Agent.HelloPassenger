<div id="clientProfileModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center p-4" style="background-image: linear-gradient(rgba(33, 33, 33, 0.8), rgba(33, 33, 33, 0.8)), url('{{ asset('rayonx.png') }}'); background-size: cover; background-position: center;">
    <div class="relative mx-auto border-none max-w-5xl w-full shadow-2xl rounded-3xl bg-white overflow-hidden transform transition-all">
        
        <div class="bg-[#ffc107] p-6 text-[#212121] text-center rounded-t-3xl">
            <h3 class="text-2xl font-bold">Votre sécurité, notre priorité !</h3>
            <p class="text-[#212121] text-opacity-90 text-sm mt-1">
                Pour la protection de vos biens et le respect des normes aéroportuaires (contrôle par rayons X), veuillez compléter vos informations de contact.
            </p>        </div>

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
                                <label class="block text-xs font-bold text-gray-700 uppercase">Prénom</label>
                                <input type="text" name="prenom" id="modal-prenom" placeholder="Prénom" class="mt-1 block w-full rounded-2xl border-2 border-gray-400 bg-gray-200 focus:bg-white focus:border-[#ffc107] focus:ring-[#ffc107] transition-all py-3 text-gray-800 font-medium text-lg">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Nom</label>
                                <input type="text" name="nom" id="modal-nom" placeholder="Nom" class="mt-1 block w-full rounded-2xl border-2 border-gray-400 bg-gray-200 focus:bg-white focus:border-[#ffc107] focus:ring-[#ffc107] transition-all py-3 text-gray-800 font-medium text-lg">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Téléphone mobile</label>
                                <input type="tel" name="telephone" id="modal-telephone" class="mt-1 block w-full rounded-2xl border-2 border-gray-400 bg-gray-200 focus:bg-white focus:border-[#ffc107] focus:ring-[#ffc107] transition-all py-3 text-gray-800 font-medium text-lg">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Adresse</label>
                            <input type="text" name="adresse" id="modal-adresse" class="mt-1 block w-full rounded-2xl border-2 border-gray-400 bg-gray-200 focus:bg-white focus:border-[#ffc107] focus:ring-[#ffc107] transition-all py-3 text-gray-800 font-medium text-lg">
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
                        
                        <div id="additional-fields-container" class="hidden mt-6 pt-6 border-t border-gray-300 grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in">
                            <!-- Civilite -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Civilité</label>
                                <select name="civilite" id="modal-civilite" class="mt-1 block w-full rounded-2xl border-2 border-gray-400 bg-gray-200 focus:ring-[#ffc107] text-gray-800 font-medium text-lg">
                                    <option value="M.">Monsieur</option>
                                    <option value="Mme">Madame</option>
                                </select>
                            </div>
                            <!-- Nom Société -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Société</label>
                                <input type="text" name="nomSociete" id="modal-nomSociete" class="mt-1 block w-full rounded-2xl border-2 border-gray-400 bg-gray-200 focus:ring-[#ffc107] text-gray-800 font-medium text-lg">
                            </div>
                            <!-- Complément Adresse -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Complément</label>
                                <input type="text" name="complementAdresse" id="modal-complementAdresse" class="mt-1 block w-full rounded-2xl border-2 border-gray-400 bg-gray-200 focus:ring-[#ffc107] text-gray-800 font-medium text-lg">
                            </div>
                            <!-- Ville -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Ville</label>
                                <input type="text" name="ville" id="modal-ville" class="mt-1 block w-full rounded-2xl border-2 border-gray-400 bg-gray-200 focus:ring-[#ffc107] py-3 text-gray-800 font-medium text-lg">
                            </div>
                            <!-- Code Postal -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Code Postal</label>
                                <input type="text" name="codePostal" id="modal-codePostal" class="mt-1 block w-full rounded-2xl border-2 border-gray-400 bg-gray-200 focus:ring-[#ffc107] py-3 text-gray-800 font-medium text-lg">
                            </div>
                            <!-- Pays -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase">Pays</label>
                                <input type="text" name="pays" id="modal-pays" placeholder="Ex: France" class="mt-1 block w-full rounded-2xl border-2 border-gray-400 bg-gray-200 focus:bg-white focus:border-[#ffc107] focus:ring-[#ffc107] transition-all py-3 text-gray-800 font-medium text-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex flex-col md:flex-row gap-4 items-center justify-center">
                    <button id="closeClientProfileModalBtn" type="button" class="order-2 md:order-1 px-8 py-3 text-gray-400 font-bold hover:text-gray-600 transition-all border-2 border-gray-300 rounded-2xl hover:border-gray-400">
                        Annuler
                    </button>
                    <button id="saveClientProfileBtn" type="submit" class="order-1 md:order-2 px-12 py-4 bg-[#ffc107] text-[#212121] font-bold rounded-2xl shadow-lg hover:bg-[#e6ae02] transform transition-all flex items-center text-lg">
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