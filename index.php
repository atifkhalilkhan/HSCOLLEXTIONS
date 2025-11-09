<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'include/cart-functions.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!--====== Required meta tags ======-->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="eCommerce,shop,fashion">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--====== Title ======-->
    <title>H.S Collextions</title>
    <!--====== Favicon Icon ======-->
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/png">
    <!--====== Google Fonts ======-->
    <link
        href="https://fonts.googleapis.com/css2?family=Aoboshi+One&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap"
        rel="stylesheet">
    <!--====== Flaticon css ======-->
    <link rel="stylesheet" href="assets/fonts/flaticon/flaticon_pesco.css">
    <!--====== FontAwesome css ======-->
    <link rel="stylesheet" href="assets/fonts/fontawesome/css/all.min.css">
    <!--====== Bootstrap css ======-->
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <!--====== Slick-popup css ======-->
    <link rel="stylesheet" href="assets/vendor/slick/slick.css">
    <!--====== Nice Select css ======-->
    <link rel="stylesheet" href="assets/vendor/nice-select/css/nice-select.css">
    <!--====== Magnific-popup css ======-->
    <link rel="stylesheet" href="assets/vendor/magnific-popup/dist/magnific-popup.css">
    <!--====== Jquery UI css ======-->
    <link rel="stylesheet" href="assets/vendor/jquery-ui/jquery-ui.min.css">
    <!--====== Animate css ======-->
    <link rel="stylesheet" href="assets/vendor/aos/aos.css">
    <!--====== Default css ======-->
    <link rel="stylesheet" href="assets/css/default.css">
    <!--====== Style css ======-->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .show-all-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(#de3576, #de3576, #de3576);
  color: #fff;
  font-weight: 600;
  padding: 12px 28px;
  border-radius: 50px;
  text-decoration: none;
  box-shadow: 0 4px 12px rgba(222, 53, 118, 0.4);
  transition: all 0.3s ease;
}

.show-all-btn:hover {
  background: linear-gradient(#de3576, #de3576, #de3576);
  transform: translateY(-2px);
  color: #fff;
}

.show-all-btn i {
  transition: transform 0.3s ease;
}

.show-all-btn:hover i {
  transform: translateX(5px);
}
.header-area .header-nav .nav-inner {
  width: 100%;
  max-width: 1200px;
}

.nav-right-item ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  align-items: center;
  gap: 12px;
}

.nav-right-item .wishlist-btn,
.nav-right-item .cart-button {
  position: relative;
  font-size: 20px;
  color: #333;
}

.nav-right-item .pro-count {
  position: absolute;
  top: -6px;
  right: -8px;
  background: #de3576;
  color: #fff;
  width: 18px;
  height: 18px;
  line-height: 18px;
  font-size: 11px;
  font-weight: 600;
  text-align: center;
  border-radius: 50%;
  box-shadow: 0 1px 2px rgba(0,0,0,0.15);
}

.sidemenu-wrapper-wishlist {
    position: fixed;
    top: 0;
    right: -360px;
    width: 360px;
    height: 100%;
    background: #fff;
    z-index: 1200;
    transition: right 0.3s ease;
    box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
}

.sidemenu-wrapper-wishlist.open {
    right: 0;
}

.sidemenu-content {
    padding: 20px;
    position: relative;
    height: 100%;
    overflow-y: auto;
}

.sidemenu-wishlist-close {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    font-size: 20px;
    color: #333;
}

.pesco-mini-wishlist-list {
    list-style: none;
    padding: 0;
    margin: 0 0 20px;
}

.sidebar-wishlist-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.sidebar-wishlist-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    margin-right: 10px;
}

.sidebar-wishlist-item a {
    flex: 1;
    color: #333;
    text-decoration: none;
}

.sidebar-wishlist-item .remove-wishlist {
    margin-right: 10px;
    color: #de3576;
}

.sidebar-wishlist-item .price {
    font-weight: 600;
    color: #de3576;
}

.wishlist-button-box {
    text-align: center;
}

.wishlist-button-box .theme-btn {
    display: inline-block;
    padding: 10px 20px;
    background: #de3576;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
}

.wishlist-button-box .theme-btn:hover {
    background: #c02c65;
}

.icon-btn.wishlist-added {
    color: #de3576;
}

.icon-btn.wishlist-added i {
    color: #de3576;
}

.offcanvas__overlay.open {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1199;
}




    </style>
</head>

<body>
    <!--====== Preloader ======-->
    <div class="preloader">
        <div class="loader">
            <img src="assets/images/loader.gif" alt="Loader">
        </div>
    </div>
    <!--======  Start Overlay  ======-->
     <?php include 'include/sidecart.php'?>
     <?php include 'include/wishlistcart.php'?>
   

     <!--====== Start Header Section ======-->
    <header class="header-area">
        <?php include 'include/header.php'; ?>
        <?php include 'include/nav.php'; ?>
    </header><!--====== End Header Section ======-->

    <!--====== Main Bg  ======-->
    <main class="main-bg">
        <!--====== Start Hero Section ======-->
        <section class="hero-section">
            <!--=== Hero Wrapper ===-->
            <div class="hero-wrapper-one">
                <div class="container">
                    <div class="hero-dots"></div>
                    <div class="hero-slider-one">

                        <!--=== Single Slider ===-->
                        <div class="single-hero-slider">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <!--=== Hero Content ===-->
                                    <div class="hero-content style-one mb-50">
                                        <span class="sub-heading" style="color: #de3576;">Perfect Style for Every
                                            Woman</span>
                                        <h1>Exclusive Collection <br>
                                            in <span style="color: #de3576;">Our Online Store</span></h1>
                                        <p>Discover the latest trends in women’s fashion at HSCOLLEXTIONS. Shop elegant,
                                            trendy and premium outfits designed to elevate your everyday style.</p>
                                        <ul>
                                            <li>
                                                <div class="price-box">
                                                    <div class="currency">
                                                        <img src="assets/images/icon/marketing.png" alt="">
                                                    </div>
                                                    <div class="text">
                                                        <span class="discount">For Every Woman</span>
                                                        <h3>Luxury Wear</h3>
                                                    </div>
                                                </div>
                                            </li>
                                            <li><img src="assets/images/hero/line-1.png" alt=""></li>
                                            <li><a href="shops.php" class="theme-btn style-one">
                                                    <i class="fas fa-shopping-bag"></i>Shop Now</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <!--=== Hero Image ===-->
                                    <div class="hero-image-box">
                                        <div class="hero-image">
                                            <img src="assets/images/hero/gril1.jpg" alt="HSCOLLEXTIONS Hero Image 1">
                                            <div class="hero-shape bg_cover"
                                                style="background-image: url(assets/images/hero/hero-one-shape1.png);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--=== Single Slider ===-->
                        <div class="single-hero-slider">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <!--=== Hero Content ===-->
                                    <div class="hero-content style-one mb-50">
                                        <span class="sub-heading" style="color: #de3576;">Luxury You Can Wear</span>
                                        <h1>New Arrivals <br>
                                            in <span style="color: #de3576;">Women's Fashion</span></h1>
                                        <p>Explore premium dresses, abayas and accessories crafted for modern women.
                                            Experience timeless designs and unmatched quality at HSCOLLEXTIONS.</p>
                                        <ul>
                                            <li>
                                                <div class="price-box">
                                                    <div class="currency">
                                                        <img src="assets/images/icon/marketing.png" alt="">
                                                    </div>
                                                    <div class="text">
                                                        <span class="discount">Unveil Your Style</span>
                                                        <h3>Trendy Fits</h3>
                                                    </div>
                                                </div>
                                            </li>
                                            <li><img src="assets/images/hero/line-1.png" alt=""></li>
                                            <li><a href="shops.php" class="theme-btn style-one">
                                                    <i class="fas fa-shopping-bag"></i>Shop Now</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <!--=== Hero Image ===-->
                                    <div class="hero-image-box">
                                        <div class="hero-image">
                                            <img src="assets/images/hero/gril2.jpg" alt="HSCOLLEXTIONS Hero Image 2">
                                            <div class="hero-shape bg_cover"
                                                style="background-image: url(assets/images/hero/hero-one-shape1.png);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--=== Single Slider ===-->
                        <div class="single-hero-slider">
                            <div class="row align-items-center">
                                <div class="col-lg-6">
                                    <!--=== Hero Content ===-->
                                    <div class="hero-content style-one mb-50">
                                        <span class="sub-heading" style="color: #de3576;">Trendy • Elegant •
                                            Confident</span>
                                        <h1>Shop Exclusive <br>
                                            <span style="color: #de3576;">Women's Wear</span> Online
                                        </h1>
                                        <p>Find your perfect look with HSCOLLEXTIONS. From chic casuals to luxurious
                                            outfits — redefine your wardrobe with fashion made for confidence.</p>
                                        <ul>
                                            <li>
                                                <div class="price-box">
                                                    <div class="currency">
                                                        <img src="assets/images/icon/marketing.png" alt="">
                                                    </div>
                                                    <div class="text">
                                                        <span class="discount">Elegance Awaits</span>
                                                        <h3>Shop Today</h3>
                                                    </div>
                                                </div>
                                            </li>
                                            <li><img src="assets/images/hero/line-1.png" alt=""></li>
                                            <li><a href="shops.php" class="theme-btn style-one">
                                                    <i class="fas fa-shopping-bag"></i>Shop Now</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <!--=== Hero Image ===-->
                                    <div class="hero-image-box">
                                        <div class="hero-image">
                                            <img src="assets/images/hero/gril3.jpg" alt="HSCOLLEXTIONS Hero Image 3">
                                            <div class="hero-shape bg_cover"
                                                style="background-image: url(assets/images/hero/hero-one-shape1.png);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <!--====== End Hero Section ======-->

        <!--====== Start Animated Headline Section ======-->
        <section class="animated-headline-area pt-25 pb-25">
            <div class="headline-wrap style-one">
                <span class="marquee-wrap">
                    <span class="marquee-inner left">
                        <span class="marquee-item"><b>Unstitched Suits</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Stitched Collection</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Fancy Footwear</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Casual Slippers</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Luxury Handbags</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>New Arrivals</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>70% Off Sale</b><i class="fas fa-bahai"></i></span>
                    </span>
                    <span class="marquee-inner left">
                        <span class="marquee-item"><b>Unstitched Suits</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Stitched Collection</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Fancy Footwear</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Casual Slippers</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Luxury Handbags</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>New Arrivals</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>70% Off Sale</b><i class="fas fa-bahai"></i></span>
                    </span>
                    <span class="marquee-inner left">
                        <span class="marquee-item"><b>Unstitched Suits</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Stitched Collection</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Fancy Footwear</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Casual Slippers</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>Luxury Handbags</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>New Arrivals</b><i class="fas fa-bahai"></i></span>
                        <span class="marquee-item"><b>70% Off Sale</b><i class="fas fa-bahai"></i></span>
                    </span>
                </span>
            </div>
        </section>
        <!--====== End Animated Headline Section ======-->

        <!--====== Start Features Section ======-->
        <section class="features-section pt-130">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!--=== Features Wrapper ===-->
                        <div class="features-wrapper" data-aos="fade-up" data-aos-delay="10" data-aos-duration="1000">
                            <!--=== Iconic Box Item ===-->
                            <div class="iconic-box-item icon-left-box mb-25">
                                <div class="icon">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <div class="content">
                                    <h5>Reliable Delivery</h5>
                             <p>Your orders are delivered securely and on time.</p>

                                </div>
                            </div>
                            <!--=== Divider ===-->
                            <div class="divider mb-25">
                                <img src="assets/images/divider.png" alt="divider">
                            </div>
                            <!--=== Iconic Box Item ===-->
                            <div class="iconic-box-item icon-left-box mb-25">
                                <div class="icon">
                                    <i class="fas fa-microphone"></i>
                                </div>
                                <div class="content">
                                    <h5>Great Support 24/7</h5>
                                    <p>Our customer support team is available around the clock </p>
                                </div>
                            </div>
                            <!--=== Divider ===-->
                            <div class="divider mb-25">
                                <img src="assets/images/divider.png" alt="divider">
                            </div>
                            <!--=== Iconic Box Item ===-->
                            <div class="iconic-box-item icon-left-box mb-25">
                                <div class="icon">
                                    <i class="far fa-handshake"></i>
                                </div>
                                <div class="content">
                                    <h5>Return Available</h5>
                                    <p>Making it easy to return any items if you're not satisfied.</p>
                                </div>
                            </div>
                            <!--=== Divider ===-->
                            <div class="divider mb-25">
                                <img src="assets/images/divider.png" alt="divider">
                            </div>
                            <!--=== Iconic Box Item ===-->
                            <div class="iconic-box-item icon-left-box mb-25">
                                <div class="icon">
                                    <i class="fas fa-sack-dollar"></i>
                                </div>
                                <div class="content">
                                    <h5>Secure Payment</h5>
                                    <p>Shop with confidence knowing that our secure payment</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section><!--====== End Features Section ======-->
        <!--====== Start Category Section ======-->
        <section class="category-section pt-125 overflow-hidden">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-8">
                        <!--=== Section Title ===-->
                        <div class="section-title mb-50" data-aos="fade-right" data-aos-delay="10"
                            data-aos-duration="800">
                            <div class="sub-heading d-inline-flex align-items-center">
                                <i class="flaticon-sparkler"></i>
                                <span class="sub-title">Categories</span>
                            </div>
                            <h2>Browse Top Categories</h2>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4">
                        <!--=== Arrows ===-->
                        <div class="category-arrows style-one mb-60" data-aos="fade-left" data-aos-delay="15"
                            data-aos-duration="1000"></div>
                    </div>
                </div>
            </div>
            <!--=== Category Slider ===-->
            <div class="category-slider-one" data-aos="fade-up" data-aos-delay="20" data-aos-duration="1200">
                <!--=== Category Item ===-->
                <div class="category-item style-one text-center">
                    <div class="category-img">
                        <img src="assets/images/category/category-4.jpg" alt="Unstitched Suits">
                    </div>
                    <div class="category-content">
                        <a href="#" class="category-btn">Unstitched Suits</a>
                    </div>
                </div>
                <!--=== Category Item ===-->
                <div class="category-item style-one text-center">
                    <div class="category-img">
                        <img src="assets/images/category/category-1.jpg" alt="Ready To Wear Dresses">
                    </div>
                    <div class="category-content">
                        <a href="#" class="category-btn">Ready To Wear</a>
                    </div>
                </div>
                <!--=== Category Item ===-->
                <div class="category-item style-one text-center">
                    <div class="category-img">
                        <img src="assets/images/category/category-2.jpg" alt="Footwear Collection">
                    </div>
                    <div class="category-content">
                        <a href="#" class="category-btn">Footwear</a>
                    </div>
                </div>
                <!--=== Category Item ===-->
                <div class="category-item style-one text-center">
                    <div class="category-img">
                        <img src="assets/images/category/category-5.jpg" alt="Trendy Handbags">
                    </div>
                    <div class="category-content">
                        <a href="#" class="category-btn">Handbags</a>
                    </div>
                </div>

                <!--=== Category Item ===-->
                <div class="category-item style-one text-center">
                    <div class="category-img">
                        <img src="assets/images/category/category-6.jpg" alt="Winter Collection">
                    </div>
                    <div class="category-content">
                        <a href="#" class="category-btn">Winter Collection</a>
                    </div>
                </div>
                <!--=== Category Item ===-->
                <div class="category-item style-one text-center">
                    <div class="category-img">
                        <img src="assets/images/category/category-3.jpg" alt="Luxury Pret">
                    </div>
                    <div class="category-content">
                        <a href="#" class="category-btn">Casual Slippers</a>
                    </div>
                </div>
            </div>
        </section>
        <!--====== End Category Section ======-->

        <!--====== Start Banner Section ======-->
        <section class="banner-section pt-130">
            <div class="container">
                <div class="row">
                    <!--=== Banner Item 1 ===-->
                    <div class="col-lg-6">
                        <div class="banner-item style-one bg-one mb-40" data-aos="fade-up" data-aos-delay="10"
                            data-aos-duration="900">
                            <div class="shape shape-two"><span><img src="assets/images/banner/line.png"
                                        alt="shape"></span></div>
                            <div class="banner-img">
                                <img src="assets/images/banner/banner-1.jpg" alt="Women's Collection - HSCollextions">
                            </div>
                            <div class="banner-content">
                                <span>Latest Trends</span>
                                <h4>Explore Unstitched & Stitched Suits</h4>
                                <a href="shops.php" class="theme-btn style-one">
                                    <i class="fas fa-shopping-bag"></i> Shop Now
                                </a>
                            </div>
                        </div>
                    </div>

                    <!--=== Banner Item 2 ===-->
                    <div class="col-lg-6">
                        <div class="banner-item style-one bg-two mb-40" data-aos="fade-up" data-aos-delay="20"
                            data-aos-duration="1100">
                            <div class="shape shape-two"><span><img src="assets/images/banner/line.png"
                                        alt="shape"></span></div>
                            <div class="banner-img">
                                <img src="assets/images/banner/banner-2.jpg" alt="Women's Accessories - HSCollextions">
                            </div>
                            <div class="banner-content">
                                <span>New Arrivals</span>
                                <h4>Casual Slippers, Fancy Footwear & Handbags</h4>
                                <a href="shops.php" class="theme-btn style-one">
                                    <i class="fas fa-store"></i> Explore Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--====== End Banner Section ======-->


       
       <!--====== Start Featured Products Section ======-->
<section class="features-products pt-90 pb-60">
  <div class="container">
    <!-- Section Heading -->
    <div class="row align-items-center mb-40">
      <div class="col-lg-6 col-md-12 text-center text-lg-start mb-3 mb-lg-0">
        <div class="section-title">
          <h2 class="title">Featured Products</h2>
        </div>
      </div>
    </div>

    <!-- Product Grid -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      <?php
      include 'config.php'; // database connection

      $sql = "SELECT * FROM products ORDER BY id DESC LIMIT 8";
      $result = $conn->query($sql);

      if ($result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
      ?>
      <div class="col">
        <div class="product-card">

          <!-- Product Image -->
          <div class="product-thumbnail">
            <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
            <div class="hover-content">
              <form method="POST" action="shops.php">
                <input type="hidden" name="add_to_wishlist" value="1">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="hidden" name="title" value="<?= htmlspecialchars($row['title']) ?>">
                <input type="hidden" name="image" value="<?= htmlspecialchars($row['image']) ?>">
                <input type="hidden" name="price" value="<?= $row['discount_price'] ?: $row['price'] ?>">
                <button type="submit"
                        class="icon-btn <?= in_array($row['id'], array_column($_SESSION['wishlist'], 'id')) ? 'wishlist-added' : '' ?>"
                        title="Add to Wishlist">
                  <i class="fa fa-heart"></i>
                </button>
              </form>
            </div>
          </div>

          <!-- Product Info -->
          <div class="product-info-wrap">
            <div class="product-info">
              <h4 class="title"><?= htmlspecialchars($row['title']) ?></h4>
              <p><?= htmlspecialchars($row['description'] ?: 'Stylish and comfortable fashion item.') ?></p>
            </div>

            <!-- Product Price -->
            <div class="product-price">
              <?php if (!empty($row['discount_price'])): ?>
                <span class="real-price">₨ <?= number_format($row['price']) ?></span>
                <span class="discount-price">₨ <?= number_format($row['discount_price']) ?></span>
              <?php else: ?>
                <span class="discount-price">₨ <?= number_format($row['price']) ?></span>
              <?php endif; ?>
            </div>

            <!-- Buttons -->
            <div class="product-actions">
              <form method="POST" action="shops.php" style="margin:0;">
                <input type="hidden" name="add_to_cart" value="1">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="hidden" name="title" value="<?= htmlspecialchars($row['title']) ?>">
                <input type="hidden" name="image" value="<?= htmlspecialchars($row['image']) ?>">
                <input type="hidden" name="price" value="<?= $row['price'] ?>">
                <input type="hidden" name="discount_price" value="<?= $row['discount_price'] ?>">
                <button type="submit" class="btn btn-cart">Add to Cart</button>
              </form>
              <a href="product-details.php?id=<?= $row['id'] ?>" class="btn btn-details">View Details</a>
            </div>
          </div>
        </div>
      </div>
      <?php endwhile; else: ?>
        <div class="col-12 text-center py-5">
          <h5>No featured products found!</h5>
        </div>
      <?php endif; ?>
    </div>

    <!-- ✅ Show All Products Button -->
    <div class="text-center mt-5">
      <a href="shops.php" class="show-all-btn">
        Show All Products <i class="fa fa-arrow-right ms-2"></i>
      </a>
    </div>
  </div>
</section>
<!--====== End Featured Products Section ======-->



        <!--====== End Features Section ======-->

        <!--====== Start Work Processing Section  ======-->
        <section class="work-processing-section pt-30 pb-90">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!--=== Section Title  ===-->
                        <div class="section-title text-center mb-60" data-aos="fade-up" data-aos-delay="10"
                            data-aos-duration="800">
                            <div class="sub-heading d-inline-flex align-items-center">
                                <i class="flaticon-sparkler"></i>
                                <span class="sub-title">Work Processing</span>
                                <i class="flaticon-sparkler"></i>
                            </div>
                            <h2>How It Works</h2>
                        </div>
                    </div>
                </div>
                <div class="row g-4"> <!-- added gutter for spacing -->
                    <div class="col-xl-3 col-md-6">
                        <!--=== Iconic Box Item  ===-->
                        <div class="iconic-box-item style-two text-center p-4 mb-40" data-aos="fade-up"
                            data-aos-duration="1000">
                            <div class="sn-number">01</div>
                            <div class="icon mb-3">
                                <i class="flaticon-searching"></i>
                            </div>
                            <div class="content">
                                <h6 class="mb-2">Browsing & Choosing</h6>
                                <p>Customers visit your online store and browse products to find what they want.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <!--=== Iconic Box Item  ===-->
                        <div class="iconic-box-item style-two text-center p-4 mb-40" data-aos="fade-up"
                            data-aos-duration="1200">
                            <div class="sn-number">02</div>
                            <div class="icon mb-3">
                                <i class="flaticon-payment-method"></i>
                            </div>
                            <div class="content">
                                <h6 class="mb-2">Checkout & Payment</h6>
                                <p>Once products are selected, customers proceed to checkout and make payment securely.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <!--=== Iconic Box Item  ===-->
                        <div class="iconic-box-item style-two text-center p-4 mb-40" data-aos="fade-up"
                            data-aos-duration="1400">
                            <div class="sn-number">03</div>
                            <div class="icon mb-3">
                                <i class="flaticon-currency"></i>
                            </div>
                            <div class="content">
                                <h6 class="mb-2">Order Fulfillment</h6>
                                <p>The order is processed and prepared by the fulfillment team for delivery.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <!--=== Iconic Box Item  ===-->
                        <div class="iconic-box-item style-two text-center p-4 mb-40" data-aos="fade-up"
                            data-aos-duration="1600">
                            <div class="sn-number">04</div>
                            <div class="icon mb-3">
                                <i class="flaticon-delivery"></i>
                            </div>
                            <div class="content">
                                <h6 class="mb-2">Delivery to Customer</h6>
                                <p>The packed order is shipped promptly to the customer via trusted carriers.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--====== End Work Processing Section  ======-->

         <!--====== Start Testiminal and Brands Section ======-->
           <?php include 'include/testiminalbrands.php' ?>
         <!--====== End Testiminal and Brands Section ======-->



        <!--====== Start Newsletter Section ======-->
        <?php include 'include/newslatter.php' ?>
        <!--====== End Newsletter Section ======-->

    </main>


    <!--====== Start Footer Main  ======-->
    <?php include 'include/footer.php' ?>
    <!--====== End Footer Main ======-->
    <script>
         $(document).ready(function() {
            // Initialize Magnific Popup for eye icon
            $('.img-popup').magnificPopup({
                type: 'image',
                mainClass: 'mfp-zoom-in',
                removalDelay: 500,
                zoom: {
                    enabled: true,
                    duration: 300,
                    opener: function(element) {
                        return element.find('img') || element;
                    }
                }
            });

            // Cart Sidebar Toggle
            $('.cart-button').click(function(e) {
                e.preventDefault();
                $('.sidemenu-wrapper-cart').toggleClass('open');
                $('.offcanvas__overlay').toggleClass('open');
            });
            $('.sidemenu-cart-close').click(function() {
                $('.sidemenu-wrapper-cart').removeClass('open');
                $('.offcanvas__overlay').removeClass('open');
            });

            // Wishlist Sidebar Toggle
            $('.wishlist-btn').click(function(e) {
                e.preventDefault();
                $('.sidemenu-wrapper-wishlist').toggleClass('open');
                $('.offcanvas__overlay').toggleClass('open');
            });
            $('.sidemenu-wishlist-close').click(function() {
                $('.sidemenu-wrapper-wishlist').removeClass('open');
                $('.offcanvas__overlay').removeClass('open');
            });

            // Close sidebars when overlay is clicked
            $('.offcanvas__overlay').click(function() {
                $('.sidemenu-wrapper-cart').removeClass('open');
                $('.sidemenu-wrapper-wishlist').removeClass('open');
                $(this).removeClass('open');
            });

            // Mobile Menu Toggle
            $('.navbar-toggler').click(function() {
                $('.pesco-nav-menu').toggleClass('open');
            });

            // Ensure filter form submits on checkbox change
            $('.form-check-input').on('change', function() {
                $('#filterForm').submit();
            });

            // Update wishlist count after removal
            $('.remove-wishlist').click(function(e) {
                $('.sidemenu-wrapper-wishlist').addClass('open');
                $('.offcanvas__overlay').addClass('open');
            });
        }); 
    </script>
</body>

</html>