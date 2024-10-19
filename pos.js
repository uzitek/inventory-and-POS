document.addEventListener('DOMContentLoaded', function() {
    const productSearch = document.getElementById('product-search');
    const productItems = document.getElementById('product-items');
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    const checkoutBtn = document.getElementById('checkout-btn');

    let cart = [];

    // Search functionality
    productSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const products = productItems.getElementsByTagName('li');
        
        for (let product of products) {
            const productName = product.getAttribute('data-name').toLowerCase();
            if (productName.includes(searchTerm)) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        }
    });

    // Add to cart functionality
    productItems.addEventListener('click', function(e) {
        if (e.target.tagName === 'LI' || e.target.parentElement.tagName === 'LI') {
            const product = e.target.tagName === 'LI' ? e.target : e.target.parentElement;
            const productId = product.getAttribute('data-id');
            const productName = product.getAttribute('data-name');
            const productPrice = parseFloat(product.getAttribute('data-price'));

            addToCart(productId, productName, productPrice);
        }
    });

    function addToCart(id, name, price) {
        const existingItem = cart.find(item => item.id === id);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1 });
        }

        updateCartDisplay();
    }

    function updateCartDisplay() {
        cartItems.innerHTML = '';
        let total = 0;

        cart.forEach(item => {
            const li = document.createElement('li');
            li.textContent = `${item.name} x ${item.quantity} - $${(item.price * item.quantity).toFixed(2)}`;
            
            const removeBtn = document.createElement('button');
            removeBtn.textContent = 'Remove';
            removeBtn.onclick = () => removeFromCart(item.id);
            
            li.appendChild(removeBtn);
            cartItems.appendChild(li);

            total += item.price * item.quantity;
        });

        cartTotal.textContent = `Total: $${total.toFixed(2)}`;
    }

    function removeFromCart(id) {
        const index = cart.findIndex(item => item.id === id);
        if (index !== -1) {
            if (cart[index].quantity > 1) {
                cart[index].quantity--;
            } else {
                cart.splice(index, 1);
            }
            updateCartDisplay();
        }
    }

    checkoutBtn.addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Cart is empty!');
            return;
        }

        // Send cart data to the server for processing
        fetch('process_sale.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(cart),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Checkout completed! Receipt number: ' + data.receipt_number);
                // Clear the cart after successful checkout
                cart = [];
                updateCartDisplay();
                // Open receipt in a new window
                window.open('receipt.php?id=' + data.sale_id, '_blank');
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('An error occurred during checkout. Please try again.');
        });
    });
});