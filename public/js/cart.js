function handleQuantityChange(e) {
    const target = e.target.closest('.quantity-change-btn');
    if (!target) return;

    const cartSpinner = document.getElementById('loading-spinner-cart');
    if (cartSpinner) cartSpinner.style.display = 'inline-block';

    // Remplacement du setTimeout par une exécution directe
    const action = target.dataset.action;
    const productId = target.dataset.productId;
    const product = initialProducts.find(p => p.id == productId);
    if (!product) {
        if (cartSpinner) cartSpinner.style.display = 'none';
        return;
    }

    let itemInCart = cartItems.find(item => item.productId === productId && item.itemCategory === 'baggage');

    if (action === 'plus') {
        if (itemInCart) {
            itemInCart.quantity++;
        } else {
            const mapData = productMapJs[product.libelle];
            const type = mapData ? mapData.type : 'unknown';
            cartItems.push({
                itemCategory: 'baggage',
                productId: productId,
                libelle: product.libelle,
                type: type,
                quantity: 1
            });
        }
    } else if (action === 'minus') {
        if (itemInCart) {
            itemInCart.quantity--;
            if (itemInCart.quantity <= 0) {
                cartItems = cartItems.filter(item => item.productId !== productId || item.itemCategory !== 'baggage');
            }
        }
    }
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartItemsContainer = document.getElementById('cart-items-container');
    const cartElement = document.getElementById('cart-summary');
    const emptyCartElement = document.getElementById('empty-cart');
    const durationElement = document.getElementById('cart-duration');
    cartItemsContainer.innerHTML = '';
    let total = 0;

    const dateDepot = document.getElementById('date-depot').value;
    const heureDepot = document.getElementById('heure-depot').value;
    const dateRecuperation = document.getElementById('date-recuperation').value;
    const heureRecuperation = document.getElementById('heure-recuperation').value;

    let duration_display = '';
    if (dateDepot && heureDepot && dateRecuperation && heureRecuperation) {
        const start = new Date(`${dateDepot}T${heureDepot}`);
        const end = new Date(`${dateRecuperation}T${heureRecuperation}`);
        const duration_in_minutes = Math.round((end - start) / (1000 * 60));

        if (duration_in_minutes > 0) {
            if (duration_in_minutes < 1440) {
                const hours = Math.floor(duration_in_minutes / 60);
                const minutes = duration_in_minutes % 60;
                duration_display = hours + ' heure(s)';
                if (minutes > 0) {
                    duration_display += ' et ' + minutes + ' minute(s)';
                }
            } else {
                const days = Math.floor(duration_in_minutes / 1440);
                const remaining_hours = Math.floor((duration_in_minutes % 1440) / 60);
                duration_display = days + ' jour(s)';
                if (remaining_hours > 0) {
                    duration_display += ' et ' + remaining_hours + ' heure(s)';
                }
            }
        }
    }
    durationElement.textContent = duration_display;

    cartItems.forEach((item, index) => {
        let itemTotal = 0;
        if (item.itemCategory === 'baggage') {
            const product = globalProductsData.find(p => p.id === item.productId);
            const itemPrice = product ? product.prixUnitaire : 0;

            let pricePerUnit = 0;
            let unitLabel = '';
            const start = new Date(`${dateDepot}T${heureDepot}`);
            const end = new Date(`${dateRecuperation}T${heureRecuperation}`);
            const duration_in_minutes = Math.round((end - start) / (1000 * 60));

            if (duration_in_minutes > 0) {
                if (duration_in_minutes < 1440) {
                    const total_hours = Math.max(1, duration_in_minutes / 60);
                    pricePerUnit = itemPrice / total_hours;
                    unitLabel = '/ heure';
                } else {
                    const total_days = duration_in_minutes / 1440;
                    pricePerUnit = itemPrice / total_days;
                    unitLabel = '/ jour';
                }
            } else {
                pricePerUnit = itemPrice;
            }

            itemTotal = itemPrice * item.quantity;
            cartItemsContainer.innerHTML += `
                <div class="py-2 flex justify-between items-center">
                    <div>
                        <span class="font-medium">${item.quantity} x ${item.libelle}</span>
                        <span class="block text-xs text-gray-500">${pricePerUnit.toFixed(2)} € ${unitLabel}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-semibold">${itemTotal.toFixed(2)} €</span>
                        <button type="button" class="delete-item-btn" data-index="${index}" title="Supprimer">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </div>`;
        } else if (item.itemCategory === 'option') {
            itemTotal = item.prix;
            cartItemsContainer.innerHTML += `
                <div class="py-2 flex justify-between items-center text-sm">
                    <div>
                        <span class="font-medium">+ ${item.libelle}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-semibold">${itemTotal.toFixed(2)} €</span>
                        <button type="button" class="delete-item-btn" data-index="${index}" title="Supprimer">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                </div>`;
        }
        total += itemTotal;
    });

    const summaryPriceElement = document.getElementById('summary-price');
    const totalPanierElements = document.querySelectorAll('.total-panier');

    if (cartItems.length > 0) {
        cartElement.style.display = 'block';
        emptyCartElement.style.display = 'none';
        cartElement.classList.add('cart-updated');
        setTimeout(() => cartElement.classList.remove('cart-updated'), 500);
    } else {
        cartElement.style.display = 'none';
        emptyCartElement.style.display = 'block';
    }

    summaryPriceElement.textContent = `${total.toFixed(2)} €`;
    totalPanierElements.forEach(el => el.textContent = `${total.toFixed(2)}€`);

    const payButton = document.querySelector('.summary-total-container');
    if (total > 0) {
        payButton.style.cursor = 'pointer';
        payButton.onclick = handleTotalClick;
    } else {
        payButton.style.cursor = 'default';
        payButton.onclick = null;
    }

    document.querySelectorAll('.add-option-btn').forEach(btn => {
        const isAlreadyInCart = cartItems.some(item => item.itemCategory === 'option' && item.key === btn.dataset.optionKey);
        btn.disabled = isAlreadyInCart;
        if (isAlreadyInCart) btn.textContent = 'Ajouté au panier';
    });

    document.querySelectorAll('[data-quantity-display]').forEach(span => {
        const productId = span.dataset.quantityDisplay;
        const itemInCart = cartItems.find(item => item.productId === productId && item.itemCategory === 'baggage');
        span.textContent = itemInCart ? itemInCart.quantity : '0';
    });

    document.querySelectorAll('#baggage-grid-container .baggage-option').forEach(box => {
        const productId = box.dataset.productId;
        const itemInCart = cartItems.find(item => item.productId === productId && item.itemCategory === 'baggage');
        if (itemInCart && itemInCart.quantity > 0) {
            box.classList.add('selected');
        } else {
            box.classList.remove('selected');
        }
    });

    saveStateToSession();

    const cartSpinner = document.getElementById('loading-spinner-cart');
    if (cartSpinner) cartSpinner.style.display = 'none';
}