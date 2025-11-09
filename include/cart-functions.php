<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

// === HANDLE ADD TO CART ===
if (isset($_POST['add_to_cart']) && $_POST['add_to_cart'] == 1) {

    if (empty($_POST['id']) || empty($_POST['title']) || empty($_POST['image']) || !isset($_POST['price'])) {
        $redirect = $_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'shops.php';
        header("Location: $redirect?error=invalid_data");
        exit;
    }

    $product_id = (int)$_POST['id'];
    $price = (float)($_POST['discount_price'] ?: $_POST['price']);
    if ($price <= 0 || !is_numeric($price)) {
        $redirect = $_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'shops.php';
        header("Location: $redirect?error=invalid_price");
        exit;
    }

    $cart_item = [
        'id' => $product_id,
        'title' => trim($_POST['title']),
        'image' => trim($_POST['image']),
        'price' => $price,
        'quantity' => 1
    ];

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $cart_item['id']) {
            $item['quantity']++;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = $cart_item;
    }

    $redirect = $_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'shops.php';
    header("Location: $redirect?success=added_to_cart");
    exit;
}

// === HANDLE REMOVE FROM CART ===
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$index]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    $redirect = $_GET['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'cart.php';
    header("Location: $redirect?success=removed_from_cart");
    exit;
}

// === HANDLE ADD/REMOVE WISHLIST ===
if (isset($_POST['add_to_wishlist'])) {
    $product_id = (int)$_POST['id'];
    $wishlist_item = [
        'id' => $product_id,
        'title' => $_POST['title'],
        'image' => $_POST['image'],
        'price' => (float)$_POST['price']
    ];

    $found = false;
    foreach ($_SESSION['wishlist'] as $index => $item) {
        if ($item['id'] == $wishlist_item['id']) {
            unset($_SESSION['wishlist'][$index]);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['wishlist'][] = $wishlist_item;
    }
    $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);

    $redirect = $_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'wishlist.php';
    header("Location: $redirect");
    exit;
}

// === HANDLE REMOVE FROM WISHLIST ===
if (isset($_GET['remove_wishlist'])) {
    $remove_id = (int)$_GET['remove_wishlist'];
    foreach ($_SESSION['wishlist'] as $index => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['wishlist'][$index]);
            break;
        }
    }
    $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);

    $redirect = $_GET['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'wishlist.php';
    header("Location: $redirect?success=removed_from_wishlist");
    exit;
}
