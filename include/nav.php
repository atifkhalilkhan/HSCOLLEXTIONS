<?php
include 'config.php';
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
?>

<!--====== Start Navigation Section ======-->
<div class="header-navigation style-one">
    <div class="container">
        <div class="primary-menu">

            <!--=== Mobile Logo ===-->
            <div class="site-branding d-block d-lg-none">
                <a href="index.html" class="brand-logo">
                    <img src="assets/images/logo/logo.jpg" alt="Logo">
                </a>
            </div>

            <!--=== Nav Inner ===-->
            <div class="nav-inner-menu">

                <!--=== Desktop Categories ===-->
                <div class="main-categories-wrap d-none d-lg-block">
                    <a class="categories-btn-active" href="#">
                        <span class="fas fa-list"></span>
                        <span class="text">Product Categories <i class="fas fa-angle-down"></i></span>
                    </a>
                    <div class="categories-dropdown-wrap categories-dropdown-active">
                        <div class="categori-dropdown-item">
                            <ul>
                                <?php
                                $cat_sql = "SELECT id, name, icon FROM categories";
                                $cat_result = $conn->query($cat_sql);
                                while ($cat = $cat_result->fetch_assoc()):
                                    $icon = !empty($cat['icon']) ? $cat['icon'] : 'default.png';
                                ?>
                                    <li>
                                        <a href="shops.php?category[]=<?= $cat['id'] ?>" class="category-link">
                                            <img src="assets/images/icon/<?= htmlspecialchars($icon) ?>" 
                                                 alt="<?= htmlspecialchars($cat['name']) ?>" 
                                                 style="width:22px;height:22px;object-fit:contain;margin-right:8px;">
                                            <span><?= htmlspecialchars($cat['name']) ?></span>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!--=== Main Menu (Desktop + Mobile Tabs) ===-->
                <div class="pesco-nav-main">
                    <div class="pesco-nav-menu">

                        <!--=== Search (Mobile) ===-->
                        <div class="nav-search mb-40 d-block d-lg-none text-center">
                            <form action="shops.php" method="GET" class="d-flex justify-content-center">
                                <input type="search" class="form_control me-2" placeholder="Search Here"
                                    name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                                    style="flex:1; max-width:80%;">
                                <button type="submit" class="search-btn" 
                                    style="background:#000;color:#fff;border:none;padding:9px 12px;border-radius:6px;">
                                    <i class="far fa-search"></i>
                                </button>
                            </form>
                        </div>

                        <!--=== Menu Tabs (Mobile) ===-->
                        <div class="pesco-tabs style-three d-block d-lg-none">
                            <ul class="nav nav-tabs mb-30" role="tablist">
                                <li>
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#menuTab" role="tab">
                                        Menu
                                    </button>
                                </li>
                                <li>
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#catTab" role="tab">
                                        Category
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <!--=== Mobile Menu Tab ===-->
                                <div class="tab-pane fade show active" id="menuTab">
                                    <nav class="main-menu">
                                        <ul>
                                            <li><a href="index.html">Home</a></li>
                                            <li><a href="about-us.html">About Us</a></li>
                                            <li class="menu-item has-children">
                                                <a href="#">Products</a>
                                                <ul class="sub-menu">
                                                    <li><a href="shops.php">All Products</a></li>
                                                    <li><a href="shops.php?category[]=1">Unstitched Collection</a></li>
                                                    <li><a href="shops.php?category[]=2">Stitched Collection</a></li>
                                                    <li><a href="shops.php?category[]=3">Casual Clipers</a></li>
                                                    <li><a href="shops.php?category[]=4">Footwear</a></li>
                                                    <li><a href="shops.php?category[]=5">Handbags</a></li>
                                                    <li><a href="cart.php">Cart</a></li>
                                                    <li><a href="checkout.php">Checkout</a></li>
                                                    <li><a href="wishlist.php">Wishlist</a></li>
                                                </ul>
                                            </li>
                                            <li><a href="faq.html">FAQs</a></li>
                                            <li><a href="contact.html">Contact</a></li>
                                        </ul>
                                    </nav>
                                </div>

                                <!--=== Categories (Mobile Tab) ===-->
                                <div class="tab-pane fade" id="catTab">
                                    <div class="categori-dropdown-item">
                                        <ul>
                                            <?php
                                            $cat_sql2 = "SELECT id, name, icon FROM categories";
                                            $cat_result2 = $conn->query($cat_sql2);
                                            while ($cat2 = $cat_result2->fetch_assoc()):
                                                $icon2 = !empty($cat2['icon']) ? $cat2['icon'] : 'default.png';
                                            ?>
                                                <li>
                                                    <a href="shops.php?category[]=<?= $cat2['id'] ?>">
                                                        <img src="assets/images/icon/<?= htmlspecialchars($icon2) ?>" 
                                                             alt="<?= htmlspecialchars($cat2['name']) ?>"
                                                             style="width:22px;height:22px;object-fit:contain;margin-right:8px;">
                                                        <?= htmlspecialchars($cat2['name']) ?>
                                                    </a>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--=== Desktop Menu ===-->
                        <nav class="main-menu d-none d-lg-block">
                            <ul>
                                <li><a href="index.html">Home</a></li>
                                <li><a href="about-us.html">About Us</a></li>
                                <li class="menu-item has-children">
                                    <a href="#">Products</a>
                                    <ul class="sub-menu">
                                        <li><a href="shops.php">All Products</a></li>
                                        <li><a href="shops.php?category[]=1">Unstitched Collection</a></li>
                                        <li><a href="shops.php?category[]=2">Stitched Collection</a></li>
                                        <li><a href="shops.php?category[]=3">Casual Clipers</a></li>
                                        <li><a href="shops.php?category[]=4">Footwear</a></li>
                                        <li><a href="shops.php?category[]=5">Handbags</a></li>
                                        <li><a href="cart.php">Cart</a></li>
                                        <li><a href="checkout.php">Checkout</a></li>
                                        <li><a href="wishlist.php">Wishlist</a></li>
                                    </ul>
                                </li>
                                <li><a href="faq.html">FAQs</a></li>
                                <li><a href="contact.html">Contact</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <!--=== Right Items ===-->
            <div class="nav-right-item style-one">
                <ul>
                    <li>
                        <div class="wishlist-btn d-flex align-items-center">
                            <i class="fas fa-heart"></i>
                            <span class="pro-count wishlist-count"><?= count($_SESSION['wishlist']) ?></span>
                        </div>
                    </li>
                    <li>
                        <div class="cart-button d-flex align-items-center">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="pro-count cart-count"><?= count($_SESSION['cart']) ?></span>
                        </div>
                    </li>
                </ul>
                <div class="navbar-toggler d-block d-lg-none">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>

        </div>
    </div>
</div>
<!--====== End Navigation Section ======-->