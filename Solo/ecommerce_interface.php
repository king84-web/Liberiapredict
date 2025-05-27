<?php
session_start();
include "./db_connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopHub - Your Online Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <i class="fas fa-shopping-bag"></i>
                ShopHub
            </div>
            <div class="user-info">
                <span>Welcome, <strong id="userName"><?php echo $_SESSION['username'] ?></strong></span>
                <div class="cart-icon" onclick="showCart()">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showSection('products')">
                <i class="fas fa-store"></i>
                Products
            </button>
            <button class="nav-tab" onclick="showSection('cart')">
                <i class="fas fa-shopping-cart"></i>
                Cart
            </button>
            <button class="nav-tab" onclick="showSection('history')">
                <i class="fas fa-history"></i>
                Purchase History
            </button>
        </div>

        <!-- Products Section -->
        <div id="products" class="content-section active">
            <div class="products-grid" id="productsGrid">
                <?php
                // Fetch products from database
                $sql = "SELECT * FROM products";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while($product = mysqli_fetch_assoc($result)) {
                        // Determine stock status
                        if ($product['quantity'] == 0) {
                            $stockClass = 'stock-out';
                            $stockText = 'Out of Stock';
                        } elseif ($product['quantity'] < 10) {
                            $stockClass = 'stock-low';
                            $stockText = "Low Stock ({$product['quantity']})";
                        } else {
                            $stockClass = 'stock-available';
                            $stockText = "In Stock ({$product['quantity']})";
                        }
                        
                        echo "
                        <div class='product-card'>
                            <div class='product-header'>
                                <div>
                                    <div class='product-name'>{$product['name']}</div>
                                    <div class='stock-info'>
                                        <i class='fas fa-box {$stockClass}'></i>
                                        <span class='{$stockClass}'>{$stockText}</span>
                                    </div>
                                </div>
                                <div class='product-price'>$" . number_format($product['price'], 2) . "</div>
                            </div>
                            <button class='buy-btn' onclick='addToCart({$product['id']})' " . 
                            ($product['quantity'] == 0 ? 'disabled' : '') . ">
                                <i class='fas fa-cart-plus'></i>
                                " . ($product['quantity'] == 0 ? 'Out of Stock' : 'Add to Cart') . "
                            </button>
                        </div>";
                    }
                } else {
                    echo "<div class='empty-state'>
                            <i class='fas fa-box-open'></i>
                            <h3>No products available</h3>
                            <p>Check back later for new products!</p>
                          </div>";
                }
                ?>
            </div>
        </div>

        <!-- Cart Section -->
        <div id="cart" class="content-section">
            <div class="cart-items" id="cartItems">
                <!-- Cart items will be loaded here -->
            </div>
            <div class="cart-total" id="cartTotal" style="display: none;">
                Total: $0.00
            </div>
            <button class="checkout-btn" id="checkoutBtn" onclick="checkout()" style="display: none;">
                <i class="fas fa-credit-card"></i>
                Proceed to Checkout
            </button>
        </div>

        <!-- Purchase History Section -->
        <div id="history" class="content-section">
            <div class="history-list" id="historyList">
                <!-- Purchase history will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification"></div>

    <script>
        // Initialize products array from PHP
        let products = <?php
            $products_array = array();
            $sql = "SELECT * FROM products";
            $result = mysqli_query($conn, $sql);
            while($row = mysqli_fetch_assoc($result)) {
                $products_array[] = $row;
            }
            echo json_encode($products_array);
        ?>;

        let cart = [];

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            updateCartDisplay();
            loadPurchaseHistory();
        });

        // Navigation functions
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected section and activate tab
            document.getElementById(sectionName).classList.add('active');
            event.target.classList.add('active');
        }

        function showCart() {
            showSection('cart');
            // Update the active tab
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.nav-tab')[1].classList.add('active');
        }

        // Add to cart
        function addToCart(productId) {
            const product = products.find(p => p.id == productId);
            if (!product || product.quantity == 0) return;

            const existingItem = cart.find(item => item.id == productId);
            
            if (existingItem) {
                if (existingItem.quantity < product.quantity) {
                    existingItem.quantity++;
                    showNotification(`Added another ${product.name} to cart`);
                } else {
                    showNotification(`Sorry, only ${product.quantity} ${product.name} available`);
                    return;
                }
            } else {
                cart.push({
                    id: productId,
                    name: product.name,
                    price: parseFloat(product.price),
                    quantity: 1
                });
                showNotification(`${product.name} added to cart`);
            }

            updateCartDisplay();
        }

        // Update cart display
        function updateCartDisplay() {
            const cartCount = document.getElementById('cartCount');
            const cartItems = document.getElementById('cartItems');
            const cartTotal = document.getElementById('cartTotal');
            const checkoutBtn = document.getElementById('checkoutBtn');

            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            cartCount.textContent = totalItems;

            if (cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Your cart is empty</h3>
                        <p>Add some products to get started!</p>
                    </div>
                `;
                cartTotal.style.display = 'none';
                checkoutBtn.style.display = 'none';
            } else {
                cartItems.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <div>
                            <div class="history-product">${item.name}</div>
                            <div class="history-date">Quantity: ${item.quantity}</div>
                        </div>
                        <div>
                            <div class="history-price">$${(item.price * item.quantity).toFixed(2)}</div>
                            <button onclick="removeFromCart(${item.id})" style="background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 5px; margin-top: 5px; cursor: pointer;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
                
                cartTotal.innerHTML = `Total: $${totalPrice.toFixed(2)}`;
                cartTotal.style.display = 'block';
                checkoutBtn.style.display = 'block';
            }
        }

        // Remove from cart
        function removeFromCart(productId) {
            const itemIndex = cart.findIndex(item => item.id === productId);
            if (itemIndex > -1) {
                const item = cart[itemIndex];
                showNotification(`${item.name} removed from cart`);
                cart.splice(itemIndex, 1);
                updateCartDisplay();
            }
        }

        // Checkout
        function checkout() {
            if (cart.length === 0) return;

            // Process each cart item
            cart.forEach(cartItem => {
                const product = products.find(p => p.id === cartItem.id);
                if (product && product.quantity >= cartItem.quantity) {
                    // Update product quantity
                    product.quantity -= cartItem.quantity;
                    
                    // Add to purchase history
                    const purchase = {
                        id: purchases.length + 1,
                        customer_id: currentCustomer.id,
                        product_id: cartItem.id,
                        date: new Date().toISOString().split('T')[0],
                        price: cartItem.price * cartItem.quantity,
                        quantity: cartItem.quantity
                    };
                    purchases.push(purchase);
                }
            });

            // Clear cart
            cart = [];
            
            // Update displays
            updateCartDisplay();
            loadProducts();
            loadPurchaseHistory();
            
            showNotification('Purchase completed successfully!');
        }

        // Load purchase history
        function loadPurchaseHistory() {
            const historyList = document.getElementById('historyList');
            const userPurchases = purchases.filter(p => p.customer_id === currentCustomer.id);

            if (userPurchases.length === 0) {
                historyList.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-receipt"></i>
                        <h3>No purchase history</h3>
                        <p>Your purchased items will appear here.</p>
                    </div>
                `;
                return;
            }

            historyList.innerHTML = userPurchases.map(purchase => {
                const product = products.find(p => p.id === purchase.product_id);
                const quantity = purchase.quantity || 1;
                return `
                    <div class="history-item">
                        <div class="history-info">
                            <div class="history-product">${product ? product.name : 'Unknown Product'}</div>
                            <div class="history-date">
                                <i class="fas fa-calendar"></i>
                                ${new Date(purchase.date).toLocaleDateString()}
                                ${quantity > 1 ? `• Qty: ${quantity}` : ''}
                            </div>
                        </div>
                        <div class="history-price">$${purchase.price.toFixed(2)}</div>
                    </div>
                `;
            }).join('');
        }

        // Show notification
        function showNotification(message) {
            const notification = document.getElementById('notification');
            notification.innerHTML = `
                <i class="fas fa-check-circle"></i>
                ${message}
            `;
            notification.classList.add('show');
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>