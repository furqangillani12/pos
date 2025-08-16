import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Auto-update check-in time when status changes
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const checkInInput = document.getElementById('check_in');

    if (statusSelect && checkInInput) {
        statusSelect.addEventListener('change', function() {
            if (this.value === 'late') {
                checkInInput.value = '10:00';
            } else if (this.value === 'half_day') {
                checkInInput.value = '12:00';
            } else {
                checkInInput.value = '09:00';
            }
        });
    }
});

// POS Functionality with Search
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize on POS page
    if (!document.querySelector('.product-item')) return;

    const cart = [];
    let currentPaymentMethod = '';
    const taxRate = 10; // 10% tax

    // DOM Elements
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
    const paymentMethodBtns = document.querySelectorAll('.payment-method-btn');
    const categoryTabs = document.querySelectorAll('.category-tab');

    // Initialize search functionality
    function initializeSearch() {
        if (!productSearch) return;

        const performSearch = () => {
            const searchTerm = productSearch.value.toLowerCase();

            productItems.forEach(item => {
                const productName = item.dataset.name.toLowerCase();
                const isVisible = productName.includes(searchTerm);

                // Only modify display if needed
                if (isVisible && item.style.display === 'none') {
                    item.style.display = 'block';
                } else if (!isVisible && item.style.display !== 'none') {
                    item.style.display = 'none';
                }
            });
        };

        // Search on input
        productSearch.addEventListener('input', performSearch);

        // Clear search on button click
        searchButton.addEventListener('click', function() {
            productSearch.value = '';
            performSearch();
        });
    }

    // Initialize category filtering
    function initializeCategoryFilter() {
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const categoryId = this.dataset.category;

                productItems.forEach(item => {
                    const itemCategory = item.dataset.categoryId;
                    const shouldShow = categoryId === 'all' || itemCategory === categoryId;
                    item.style.display = shouldShow ? 'block' : 'none';
                });

                // Update active tab styling
                categoryTabs.forEach(t => {
                    const isActive = t === this;
                    t.classList.toggle('bg-blue-600', isActive);
                    t.classList.toggle('text-white', isActive);
                    t.classList.toggle('bg-gray-200', !isActive);
                    t.classList.toggle('hover:bg-gray-300', !isActive);
                });
            });
        });
    }

    // Add product to cart
    function initializeProductClickHandlers() {
        productItems.forEach(item => {
            item.addEventListener('click', function() {
                const productId = this.dataset.id;
                const productName = this.dataset.name;
                const productPrice = parseFloat(this.dataset.price);

                // Check if product already in cart
                const existingItem = cart.find(item => item.product_id == productId && !item.variant_id);

                if (existingItem) {
                    existingItem.quantity += 1;
                    existingItem.total_price = existingItem.quantity * productPrice;
                } else {
                    cart.push({
                        product_id: parseInt(productId),
                        product_name: productName,
                        quantity: 1,
                        unit_price: productPrice,
                        total_price: productPrice,
                        variant_id: null,
                        variant_name: null
                    });
                }

                updateCartDisplay();
            });
        });
    }

    // Payment method selection
    function initializePaymentMethods() {
        paymentMethodBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                currentPaymentMethod = this.dataset.method;
                paymentMethodBtns.forEach(b => b.classList.remove('bg-blue-100', 'border-blue-500'));
                this.classList.add('bg-blue-100', 'border-blue-500');
                updateCheckoutButton();
            });
        });
    }

    // Clear cart
    clearCartBtn.addEventListener('click', function() {
        if (cart.length > 0 && confirm('Are you sure you want to clear the cart?')) {
            cart.length = 0;
            currentPaymentMethod = '';
            paymentMethodBtns.forEach(b => b.classList.remove('bg-blue-100', 'border-blue-500'));
            updateCartDisplay();
        }
    });

    // Checkout process
    checkoutBtn.addEventListener('click', async function() {
        if (cart.length === 0) {
            alert('Please add items to cart');
            return;
        }

        if (!currentPaymentMethod) {
            alert('Please select a payment method');
            return;
        }

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
                    items: cart,
                    payment_method: currentPaymentMethod,
                    notes: ''
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to process order');
            }

            const data = await response.json();

            if (data.success) {
                cart.length = 0;
                currentPaymentMethod = '';
                paymentMethodBtns.forEach(b => b.classList.remove('bg-blue-100', 'border-blue-500'));
                updateCartDisplay();

                if (data.receipt_url) {
                    window.open(data.receipt_url, '_blank');
                }

                alert('Order processed successfully!');
            } else {
                throw new Error(data.message || 'Failed to process order');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        }
    });

    // Update cart display
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

        cart.forEach((item, index) => {
            subtotal += item.total_price;

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
        const total = subtotal + tax;

        subtotalEl.textContent = `Rs. ${subtotal.toFixed(2)}`;
        taxEl.textContent = `Rs. ${tax.toFixed(2)}`;
        totalEl.textContent = `Rs. ${total.toFixed(2)}`;

        updateCheckoutButton();
    }

    // Handle cart item actions
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('increase-quantity')) {
            const index = e.target.dataset.index;
            cart[index].quantity += 1;
            cart[index].total_price = cart[index].quantity * cart[index].unit_price;
            updateCartDisplay();
        }
        else if (e.target.classList.contains('decrease-quantity')) {
            const index = e.target.dataset.index;
            if (cart[index].quantity > 1) {
                cart[index].quantity -= 1;
                cart[index].total_price = cart[index].quantity * cart[index].unit_price;
                updateCartDisplay();
            }
        }
        else if (e.target.classList.contains('remove-item')) {
            const index = e.target.dataset.index;
            cart.splice(index, 1);
            updateCartDisplay();
        }
    });

    // Enable/disable checkout button
    function updateCheckoutButton() {
        checkoutBtn.disabled = cart.length === 0 || !currentPaymentMethod;
    }

    // Initialize all functionality
    initializeSearch();
    initializeCategoryFilter();
    initializeProductClickHandlers();
    initializePaymentMethods();
});
