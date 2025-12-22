import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function() {
    const cart = [];
    let currentPaymentMethod = '';
    let taxRate = window.taxRate || 0;
    const taxInput = document.getElementById('custom_tax');
    if (taxInput) {
        taxInput.addEventListener('input', function() {
            taxRate = parseFloat(this.value) || 0;
            updateCartDisplay();
        });
    }

    const productItems = document.querySelectorAll('.product-item');
    const productSearch = document.getElementById('productSearch');
    const searchButton = document.querySelector('#productSearch + button');
    const cartItemsContainer = document.querySelector('.cart-items');
    const emptyCartMessage = document.querySelector('.empty-cart-message');
    const subtotalEl = document.querySelector('.subtotal');
    const taxEl = document.querySelector('.tax');
    const totalEl = document.querySelector('.total');
    const checkoutBtn = document.querySelector('.checkout-btn');
    const clearCartBtn = document.querySelector('.clear-cart-btn');
    const customerSelect = document.querySelector('.customer-select');
    const customerTypeSelect = document.querySelector('.customer-type-select');
    const paymentMethodBtns = document.querySelectorAll('.payment-method-btn');
    const categoryTabs = document.querySelectorAll('.category-tab');

    const paymentSelect = document.getElementById('payment_method');
    if (paymentSelect) {
        paymentSelect.addEventListener('change', function () {
            currentPaymentMethod = this.value || '';
            updateCheckoutButton();
        });
    }
// 🚀 Weight input
    const weightInput = document.getElementById('weight');
    let orderWeight = 0;
    if (weightInput) {
        weightInput.addEventListener('input', function() {
            orderWeight = parseFloat(this.value) || 0;
        });
    }

    // Discount input 🚀
    const discountInput = document.getElementById('discount');
    let discountValue = 0;
    if (discountInput) {
        discountInput.addEventListener('input', function() {
            discountValue = parseFloat(this.value) || 0;
            updateCartDisplay();
        });
    }

    // 🚀 Delivery charges input
    const deliveryChargesInput = document.getElementById('delivery_charges');
    let deliveryCharges = 0;
    if (deliveryChargesInput) {
        deliveryChargesInput.addEventListener('input', function() {
            deliveryCharges = parseFloat(this.value) || 0;
            updateCartDisplay();
        });
    }

    // Search
    if (productSearch) {
        const performSearch = () => {
            const term = productSearch.value.toLowerCase();
            productItems.forEach(item => {
                const name = item.dataset.name.toLowerCase();
                const barcode = item.dataset.barcode ? item.dataset.barcode.toLowerCase() : '';
                if (name.includes(term) || barcode.includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        };

        productSearch.addEventListener('input', performSearch);
        searchButton.addEventListener('click', function() {
            productSearch.value = '';
            performSearch();
        });
    }

    // Category filter
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const cat = this.dataset.category;
            productItems.forEach(item => {
                item.style.display = (cat === 'all' || item.dataset.categoryId === cat) ? 'block' : 'none';
            });
            categoryTabs.forEach(t => {
                const isActive = t === this;
                t.classList.toggle('bg-blue-600', isActive);
                t.classList.toggle('text-white', isActive);
                t.classList.toggle('bg-gray-200', !isActive);
                t.classList.toggle('hover:bg-gray-300', !isActive);
            });
        });
    });

    // Update product prices on customer type change
    if (customerTypeSelect) {
        customerTypeSelect.addEventListener('change', function() {
            const type = this.value;
            productItems.forEach(item => {
                let price = parseFloat(item.dataset.salePrice);
                if (type === 'reseller') price = parseFloat(item.dataset.resalePrice);
                else if (type === 'wholesale') price = parseFloat(item.dataset.wholesalePrice);
                item.dataset.price = price;
                const priceText = item.querySelector('.price-text');
                if (priceText) priceText.textContent = `Rs. ${price.toFixed(2)}`;
            });

            // Update cart prices if already added
            cart.forEach(cartItem => {
                const itemEl = Array.from(productItems).find(i => i.dataset.id == cartItem.product_id);
                if (itemEl) {
                    cartItem.unit_price = parseFloat(itemEl.dataset.price);
                    cartItem.total_price = cartItem.unit_price * cartItem.quantity;
                }
            });
            updateCartDisplay();
        });
    }

    // Product click
    productItems.forEach(item => {
        item.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);

            const existing = cart.find(i => i.product_id == id && !i.variant_id);
            if (existing) {
                existing.quantity += 1;
                existing.total_price = existing.quantity * price;
                existing.unit_price = price;
            } else {
                cart.push({
                    product_id: parseInt(id),
                    product_name: name,
                    quantity: 1,
                    unit_price: price,
                    total_price: price,
                    weight: parseFloat(this.dataset.weight) || 0,
                    variant_id: null,
                    variant_name: null
                });
            }
            updateCartDisplay();
        });
    });

    // Payment methods
    paymentMethodBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currentPaymentMethod = this.dataset.method;
            paymentMethodBtns.forEach(b => b.classList.remove('bg-blue-100', 'border-blue-500'));
            this.classList.add('bg-blue-100', 'border-blue-500');
            updateCheckoutButton();
        });
    });

    // Clear cart
    clearCartBtn.addEventListener('click', function() {
        if (cart.length > 0 && confirm('Are you sure you want to clear the cart?')) {
            cart.length = 0;
            currentPaymentMethod = '';
            paymentMethodBtns.forEach(b => b.classList.remove('bg-blue-100', 'border-blue-500'));
            updateCartDisplay();
        }
    });

    // Checkout
    checkoutBtn.addEventListener('click', async function() {
        let totalWeight = cart.reduce((sum, item) => sum + (item.weight || 0) * item.quantity, 0);

        if (cart.length === 0) return alert('Please add items to cart');
        if (!currentPaymentMethod) return alert('Please select a payment method');

        try {
            const posRoute = document.querySelector('[data-pos-route]').dataset.posRoute;
            const response = await fetch(posRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },

                body: JSON.stringify({
                    customer_id: customerSelect.value || null,
                    customer_type: customerTypeSelect ? customerTypeSelect.value : 'walkin',
                    items: cart,
                    payment_method: currentPaymentMethod,
                    dispatch_method: document.querySelector('#dispatch_method')?.value || null,
                    tracking_id: document.querySelector('#tracking_id')?.value || null,
                    delivery_charges: deliveryCharges,
                    total_weight: totalWeight,   // 👈 send auto-calculated weight
                    notes: '',
                    tax_rate: taxRate,
                    discount: discountValue
                })


            });

            const data = await response.json();
            if (data.success) {
                cart.length = 0;
                currentPaymentMethod = '';
                paymentMethodBtns.forEach(b => b.classList.remove('bg-blue-100', 'border-blue-500'));
                updateCartDisplay();
                if (data.receipt_url) window.open(data.receipt_url, '_blank');

                if (data.receipt_pdf_url) {
                    let phone = data.customer_phone || '';
                    if (!phone) {
                        phone = prompt("Enter customer phone number (with country code, e.g., 92300xxxxxxx):");
                    }
                    if (phone) {
                        const message = `Dear Customer, your receipt #${data.order_number} is ready.\n` +
                            `Total: Rs. ${data.total}\n` +
                            `Download PDF: ${data.receipt_pdf_url}\n\n` +
                            `Thank you for shopping with us!`;

                        const whatsappURL = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
                        window.open(whatsappURL, '_blank');
                    }
                }
                alert('Order processed successfully!');
            } else {
                throw new Error(data.message || 'Failed to process order');
            }
        } catch (err) {
            console.error(err);
            alert('Error: ' + err.message);
        }
    });

    // Cart UI
    function updateCartDisplay() {
        cartItemsContainer.innerHTML = '';
        if (cart.length === 0) {
            emptyCartMessage.classList.remove('hidden');
            cartItemsContainer.classList.add('hidden');
            subtotalEl.textContent = 'Rs. 0.00';
            taxEl.textContent = 'Rs. 0.00';
            totalEl.textContent = 'Rs. 0.00';
            updateCheckoutButton();
            return;
        }
        emptyCartMessage.classList.add('hidden');
        cartItemsContainer.classList.remove('hidden');

        let subtotal = 0;
        let totalWeight = 0;
        cart.forEach((item, index) => {
            subtotal += item.total_price;
            totalWeight += (item.weight || 0) * item.quantity;
            const itemEl = document.createElement('div');
            itemEl.className = 'flex justify-between items-center p-3 bg-gray-50 rounded mb-2';
            itemEl.innerHTML = `
            <div>
                <div class="font-medium">${item.product_name}${item.variant_name ? ` (${item.variant_name})` : ''}</div>
                <div class="flex items-center mt-2">
                    <button class="decrease-quantity px-2 py-1 border rounded-l" data-index="${index}">-</button>
                    <span class="quantity px-3 py-1 border-t border-b">${item.quantity}</span>
                    <button class="increase-quantity px-2 py-1 border rounded-r" data-index="${index}">+</button>
                    <button class="remove-item ml-3 px-2 py-1 text-red-500 text-sm" data-index="${index}">Remove</button>
                </div>
            </div>
            <div class="text-right">
                <div class="font-medium">Rs. ${item.total_price.toFixed(2)}</div>
                <div class="text-sm text-gray-500">Rs. ${item.unit_price.toFixed(2)} each</div>
            </div>
        `;
            cartItemsContainer.appendChild(itemEl);
        });

        const tax = subtotal * (taxRate / 100);
        let total = subtotal + tax - discountValue + deliveryCharges; // 🚀 include delivery
        if (total < 0) total = 0; // 🚀 prevent negative totals

        subtotalEl.textContent = `Rs. ${subtotal.toFixed(2)}`;
        taxEl.textContent = `Rs. ${tax.toFixed(2)}`;
        totalEl.textContent = `Rs. ${total.toFixed(2)}`;
        document.querySelector('.total-weight').textContent = `${totalWeight.toFixed(2)} kg`; // 👈 display weight
        updateCheckoutButton();
    }

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('increase-quantity')) {
            const index = e.target.dataset.index;
            cart[index].quantity += 1;
            cart[index].total_price = cart[index].unit_price * cart[index].quantity;
            updateCartDisplay();
        } else if (e.target.classList.contains('decrease-quantity')) {
            const index = e.target.dataset.index;
            if (cart[index].quantity > 1) {
                cart[index].quantity -= 1;
                cart[index].total_price = cart[index].unit_price * cart[index].quantity;
                updateCartDisplay();
            }
        } else if (e.target.classList.contains('remove-item')) {
            const index = e.target.dataset.index;
            cart.splice(index, 1);
            updateCartDisplay();
        }
    });

    function updateCheckoutButton() {
        checkoutBtn.disabled = cart.length === 0 || !currentPaymentMethod;
    }

    // 🚀 Dispatch method toggle (now also handles delivery charges field)
    const dispatchSelect = document.getElementById('dispatch_method');
    const trackingField = document.getElementById('tracking_id_field');
    const trackingInput = document.getElementById('tracking_id');
    const deliveryChargesField = document.getElementById('delivery_charges_field');

    const weightField = document.getElementById('weight_field');

    function toggleDispatchFields() {
        if (!dispatchSelect) return;
        let method = dispatchSelect.value.trim().toLowerCase();

        if (method === 'self pickup') {
            trackingField.style.display = 'none';
            if (trackingInput) trackingInput.value = '';

            if (deliveryChargesField) deliveryChargesField.style.display = 'none';
            deliveryCharges = 0;
            if (deliveryChargesInput) deliveryChargesInput.value = 0;

            if (weightField) weightField.style.display = 'none';
            orderWeight = 0;
            if (weightInput) weightInput.value = 0;

            updateCartDisplay();
        } else {
            trackingField.style.display = 'block';
            if (deliveryChargesField) deliveryChargesField.style.display = 'block';
            if (weightField) weightField.style.display = 'block';
        }
    }


    if (dispatchSelect) {
        dispatchSelect.addEventListener('change', toggleDispatchFields);
        toggleDispatchFields(); // run once on load
    }

});
