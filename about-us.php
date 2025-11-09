<?php
session_start();
include 'config.php';
include 'include/cart-functions.php';
// endeddddd /////
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
    <title>Pesco - eCommerce HTML Template</title>
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
    <link rel="stylesheet" href="include/custom.css">


  <style>
        .cart-button.active::after {
            content: '<?= count($_SESSION['cart']) ?>';
            position: absolute;
            top: -10px;
            right: -10px;
            background: #ff4d4d;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }
        .nav-search { position: relative; }
        .nav-search input { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd; }
        .nav-search button { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; }
        .error { color: red; font-size: 14px; margin-top: 10px; }
        
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
    <!--====== Start Overlay ======-->
    <div class="offcanvas__overlay"></div>
    <!--====== Start Sidemenu-wrapper-cart Area ======-->
     <!-- Overlay and Side Carts -->
     <div class="offcanvas__overlay"></div>
    <?php include 'include/sidecart.php'; ?>
    <?php include 'include/wishlistcart.php'; ?>
    
    <!-- Header Section -->
    <header class="header-area">
        <?php include 'include/header.php'; ?>
        <?php include 'include/nav.php'; ?>
    </header>
    <!--====== End Header Section ======-->

        <!--====== Main Bg  ======-->
        <main class="main-bg">

            <!--====== Start Page Banner Section ======-->
            <section class="page-banner">
                <div class="page-banner-wrapper p-r z-1">
                    <svg class="lineanm" viewBox="0 0 1920 347" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="line"
                            d="M-39 345.187C70 308.353 397.628 293.477 436 145.186C490 -63.5 572 -57.8156 688 255.186C757.071 441.559 989.5 -121.315 1389 98.6856C1708.6 274.686 1940.33 156.519 1964.5 98.6856"
                            stroke="white" stroke-width="3" stroke-dasharray="2 2" />
                    </svg>
                    <div class="page-image"><img src="assets/images/about/about.jpg" alt="image"></div>
                    <svg class="page-svg" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M21.1742 33.0065C14.029 35.2507 7.5486 39.0636 0 40.7339V86H1937V64.9942C1933.1 60.1623 1912.65 65.1777 1904.51 62.6581C1894.22 59.4678 1884.93 55.0079 1873.77 52.7742C1861.2 50.2585 1823.41 36.3854 1811.99 39.9252C1805.05 42.0727 1796.94 37.6189 1789.36 36.6007C1769.18 33.8879 1747.19 31.1848 1726.71 29.7718C1703.81 28.1919 1678.28 27.0012 1657.53 34.4442C1636.45 42.005 1606.07 60.856 1579.5 55.9191C1561.6 52.5906 1543.41 47.0959 1528.45 56.9075C1510.85 68.4592 1485.74 74.2518 1460.44 76.136C1432.32 78.2297 1408.53 70.6879 1384.73 62.2987C1339.52 46.361 1298.19 27.1677 1255.08 9.28534C1242.58 4.10111 1214.68 15.4762 1200.55 16.6533C1189.77 17.5509 1181.74 15.4508 1172.12 12.8795C1152.74 7.70033 1133.23 2.88525 1111.79 2.63621C1088.85 2.36971 1073.94 7.88289 1056.53 15.8446C1040.01 23.3996 1027.48 26.1777 1007.8 26.1777C993.757 26.1777 975.854 25.6887 962.844 28.9632C941.935 34.2258 932.059 38.7874 914.839 28.6037C901.654 20.8061 866.261 -2.56499 844.356 7.12886C831.264 12.9222 820.932 21.5146 807.663 27.5255C798.74 31.5679 779.299 42.0561 766.33 39.1166C758.156 37.2637 751.815 31.6349 745.591 28.2443C730.967 20.2774 715.218 13.2948 695.846 10.723C676.168 8.11038 658.554 23.1787 641.606 27.4357C617.564 33.4742 602.283 27.7951 579.244 27.7951C568.142 27.7951 548.414 30.4002 541.681 23.6618C535.297 17.2722 530.162 9.74921 523.263 3.71444C517.855 -1.01577 505.798 -0.852017 498.318 2.09709C479.032 9.7007 453.07 10.0516 431.025 9.64475C407.556 9.21163 368.679 1.61612 346.618 10.3636C319.648 21.0575 291.717 53.8338 254.67 45.2266C236.134 40.9201 225.134 37.5813 204.78 40.7339C186.008 43.6415 171.665 50.7785 156.051 57.3567C146.567 61.3523 152.335 52.6281 151.12 47.9222C149.535 41.7853 139.994 34.5585 132.991 30.4008C120.206 22.8098 90.2848 24.3246 74.2546 24.6502C55.5552 25.0301 37.9201 27.747 21.1742 33.0065Z"
                            fill="#FFFAF3" />
                    </svg>
                    <div class="shape shape-one"><span></span></div>
                    <div class="shape shape-two"><span></span></div>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="page-banner-content">
                                    <h1>About Us</h1>
                                    <ul class="breadcrumb-link">
                                        <li><a href="index.html" style="color: #de3576;">Home</a></li>
                                        <li><i class="far fa-long-arrow-right"></i></li>
                                        <li class="active">About Us</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section><!--====== End Page Banner Section ======-->
            <!--====== Start About Us Section ======-->
            <section class="about-us-section pt-120">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-6">
                            <!--====== Section Image Box ======-->
                            <div class="section-image-box style-one mb-50" data-aos="fade-up" data-aos-delay="30"
                                data-aos-duration="1000">
                                <div class="image-one">
                                    <img src="assets/images/about/about2.jpg" alt="Fashion store interior">
                                    <div class="img-shape"></div>
                                </div>
                                <div class="image-two">
                                    <img src="assets/images/about/about3.jpg" alt="New clothing collection display">
                                    <span class="line"></span>
                                </div>
                                <div class="experience-box">
                                    <div class="icon">
                                        <!-- <img src="assets/images/about/star.svg" alt="Icon"> -->
                                    </div>
                                    <div class="text">
                                        <div class="year" style="color: #de3576;">New</div>
                                        <div class="duration">Freshly<br>Launched</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <!--====== Section Content Box ======-->
                            <div class="section-content-box style-one" data-aos="fade-up" data-aos-delay="50"
                                data-aos-duration="1200">
                                <div class="section-title mb-30">
                                    <div class="sub-heading d-inline-flex align-items-center">
                                        <i class="flaticon-sparkler"></i>
                                        <span class="sub-title">About Us</span>
                                    </div>
                                    <h2>Discover fashion with elegance, comfort & authenticity.</h2>
                                </div>

                                <p>
                                    Founded by <strong>Salman Nazar</strong>, our online store brings together
                                    Pakistan’s favorite fashion brands and designers — all in one place.
                                    From elegant <strong>unstitched suits</strong> to premium <strong>stitched
                                        collections</strong>, shoes, and accessories,
                                    we aim to provide a stylish yet affordable experience for every customer.
                                </p>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <ul class="list mb-25">
                                            <li><i class="flaticon-star-3"></i> 100% authentic Pakistani brands</li>
                                            <li><i class="flaticon-star-3"></i> Trendy, comfortable & elegant styles
                                            </li>
                                            <li><i class="flaticon-star-3"></i> Safe payments & fast nationwide delivery
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="thumbnail-img mb-25">
                                                    <img src="assets/images/about/about4.jpg"
                                                        alt="Unstitched collection showcase">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="thumbnail-img mb-25">
                                                    <img src="assets/images/about/about5.jpg"
                                                        alt="Model wearing stitched outfit">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="content-wrap-box d-flex mt-25 align-items-center">
                                    <div class="author-item">
                                        <div class="author-thumb">
                                            <img src="assets/images/logo/salman.PNG"
                                                alt="Salman Nazar portrait">
                                        </div>
                                        <div class="author-info">
                                            <h5>Salman Nazar</h5>
                                            <span class="position">Founder & CEO</span>
                                        </div>
                                    </div>
                                    <div class="divider">
                                        <img src="assets/images/about/divider.png" alt="divider">
                                    </div>
                                    <div class="signature">
                                        <img src="assets/images/logo/logo.jpg" alt="signature">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--====== End About Us Section ======-->


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
                                    <p>Once products are selected, customers proceed to checkout and make payment
                                        securely.
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

            <!--====== Start Testimonial Sections  ======-->
            <section class="testimonial-section">
                <div class="testimonial-wrapper overflow-x-hidden pt-190 pb-90 white-bg " style="width: 100%;">
                    <div class="shape svg-shape1"><img src="assets/images/testimonial/tl-svg1.svg" alt="svg shape">
                    </div>
                    <div class="shape svg-shape2"><img src="assets/images/testimonial/tl-svgBottom.svg" alt="svg shape">
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-4">
                                <!--=== Section Content Box ===-->
                                <div class="section-content-box mb-40" data-aos="fade-right" data-aos-delay="30"
                                    data-aos-duration="800">
                                    <div class="section-title mb-50">
                                        <h2>What Our Clients Say About Us</h2>
                                    </div>
                                    <div class="testimonial-arrows style-one"></div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <!--=== Testimonial Slider ===-->
                                <div class="testimonial-slider-one" data-aos="fade-left" data-aos-delay="50"
                                    data-aos-duration="1000">
                                    <!--=== Testimonial Item ===-->
                                    <div class="testimonial-item style-one mb-40">
                                        <div class="testimonial-content">
                                            <p>The stitched suit I ordered arrived on time and matched the pictures
                                                perfectly. The stitching and embroidery are detailed, and I am very
                                                happy
                                                with my purchase.</p>

                                            <div
                                                class="author-quote-item d-flex justify-content-between align-items-center">
                                                <div class="author-item">
                                                    <div class="author-thumb">
                                                        <img src="assets/images/logo/logo2.jpg" alt="author image">
                                                    </div>
                                                    <div class="author-info">
                                                        <h5>Hamza Raza</h5>
                                                        <ul class="ratings rating5">
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="quote-icon">
                                                    <i class="flaticon flaticon-right-quote"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--=== Testimonial Item ===-->
                                    <div class="testimonial-item style-one mb-40">
                                        <div class="testimonial-content">
                                            <p>The casual slippers are very comfortable and durable. I wear them daily
                                                at
                                                home, and they provide good support. Definitely a great buy for comfort
                                                lovers.</p>

                                            <div
                                                class="author-quote-item d-flex justify-content-between align-items-center">
                                                <div class="author-item">
                                                    <div class="author-thumb">
                                                        <img src="assets/images/logo/logo2.jpg" alt="author image">
                                                    </div>
                                                    <div class="author-info">
                                                        <h5>Fatima Noor</h5>
                                                        <ul class="ratings rating5">
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="quote-icon">
                                                    <i class="flaticon flaticon-right-quote"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--=== Testimonial Item ===-->
                                    <div class="testimonial-item style-one mb-40">
                                        <div class="testimonial-content">
                                            <p>I bought this handbag last week, and it exceeded my expectations. The
                                                quality
                                                is excellent, and it looks elegant with both casual and formal outfits.
                                            </p>

                                            <div
                                                class="author-quote-item d-flex justify-content-between align-items-center">
                                                <div class="author-item">
                                                    <div class="author-thumb">
                                                        <img src="assets/images/logo/logo2.jpg" alt="author image">
                                                    </div>
                                                    <div class="author-info">
                                                        <h5>Sana Khan</h5>
                                                        <ul class="ratings rating5">
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="quote-icon">
                                                    <i class="flaticon flaticon-right-quote"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--=== Testimonial Item ===-->
                                    <div class="testimonial-item style-one mb-40">
                                        <div class="testimonial-content">
                                            <p>The new kurta I ordered was stylish and fit perfectly. I received
                                                compliments
                                                from friends and family, and the fabric feels soft and comfortable for
                                                everyday wear.</p>

                                            <div
                                                class="author-quote-item d-flex justify-content-between align-items-center">
                                                <div class="author-item">
                                                    <div class="author-thumb">
                                                        <img src="assets/images/logo/logo2.jpg" alt="author image">
                                                    </div>
                                                    <div class="author-info">
                                                        <h5>Ahmed Ali</h5>
                                                        <ul class="ratings rating5">
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                            <li><i class="fas fa-star"></i></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="quote-icon">
                                                    <i class="flaticon flaticon-right-quote"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section><!--====== End Testimonial Sections  ======-->

            <!--====== Start Newsletter Section ======-->
            <?php include 'include/newslatter.php' ?>    
            <!--====== End Newsletter Section ======-->

        </main>


    <!--====== Start Footer Main  ======-->
    <?php include 'include/footer.php' ?>
    <!--====== End Footer Main ======-->
      
        <script src="assets/js/wishlist.js"></script>
</body>

</html>