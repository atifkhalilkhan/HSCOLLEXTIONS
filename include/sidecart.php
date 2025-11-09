<!-- 🛒 Sidebar Cart -->
<div class="offcanvas__overlay"></div>
<div class="sidemenu-wrapper-cart">
    <div class="sidemenu-content">
        <h4>My Cart</h4>
        <div class="sidemenu-cart-close"><i class="far fa-times"></i></div>

        <!-- Feedback Messages -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 'added_to_cart'): ?>
            <div class="alert alert-success" style="margin-bottom: 10px;">Item added to cart!</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_data'): ?>
            <div class="alert alert-danger" style="margin-bottom: 10px;">Error: Invalid product data.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_price'): ?>
            <div class="alert alert-danger" style="margin-bottom: 10px;">Error: Invalid product price.</div>
        <?php endif; ?>

        <ul class="pesco-mini-cart-list">
            <?php if (empty($_SESSION['cart'])): ?>
                <li class="empty-cart">Your cart is empty</li>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <li class="sidebar-cart-item" id="cart-item-<?= $item['id'] ?>">
                        <!-- Remove Button -->
                        <a href="cart.php?remove=<?= $item['id'] ?>" class="remove-cart" title="Remove Item">
                            <i style="color: #de3576;" class="far fa-trash-alt"></i>
                        </a>

                        <!-- Product Info -->
                        <div class="cart-item-info">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="cart image">
                            <div class="cart-text">
                                <span class="title"><?= htmlspecialchars($item['title']) ?></span>
                                <span class="quantity">
                                    <?= (int)$item['quantity'] ?> × 
                                    <span class="currency">Rs.<?= number_format((float)$item['price'], 0) ?></span>
                                </span>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <!-- Cart Total -->
        <div class="cart-mini-total">
            <strong>Subtotal:</strong>
            <span class="currency">
                <?php
                $subtotal = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $item_price = (float)($item['price'] ?? 0);
                    $item_quantity = (int)($item['quantity'] ?? 0);
                    $item_total = $item_price * $item_quantity;
                    $subtotal += $item_total;
                    error_log("Cart item ID {$item['id']}: price=$item_price, quantity=$item_quantity, item_total=$item_total");
                }
                error_log("Final subtotal: $subtotal");
                echo 'Rs.' . number_format($subtotal, 0);
                ?>
            </span>
        </div>

        <!-- Cart Buttons -->
        <div class="cart-button-box">
            <a href="cart.php" class="theme-btn style-one">View Cart</a>
            <a href="checkout.php" class="theme-btn style-one checkout-btn">Checkout</a>
        </div>
    </div>
</div>