<?php
session_start();

// Include database configuration (optional, included for consistency)
include 'config.php';

// Initialize sessions for cart and wishlist (for consistency with product-details.php)
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="eCommerce, shop, fashion, coming soon">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Coming Soon - H.S Collextions</title>
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Aoboshi+One&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/fonts/flaticon/flaticon_pesco.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/slick/slick.css">
    <link rel="stylesheet" href="assets/vendor/nice-select/css/nice-select.css">
    <link rel="stylesheet" href="assets/vendor/magnific-popup/dist/magnific-popup.css">
    <link rel="stylesheet" href="assets/vendor/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="assets/vendor/aos/aos.css">
    <link rel="stylesheet" href="assets/css/default.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="include/custom.css">
    <style>
        :root {
            --header-top-height: 60px;
            --header-nav-height: 50px;
            --primary-color: #de3576;
        }

        .header-area {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1100;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }

        .coming-soon-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 0;
            background: #f9f9f9;
            text-align: center;
        }

        .coming-soon-content {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .coming-soon-content h1 {
            font-size: 48px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .coming-soon-content p {
            font-size: 18px;
            color: #333;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
        }

        .countdown-item {
            background: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
            min-width: 80px;
        }

        .countdown-item span {
            display: block;
            font-size: 36px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .countdown-item label {
            font-size: 14px;
            color: #555;
        }

        .newsletter-form {
            max-width: 400px;
            margin: 0 auto 30px;
        }

        .newsletter-form input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px 0 0 6px;
            font-size: 16px;
        }

        .newsletter-form button {
            padding: 12px 20px;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .newsletter-form button:hover {
            background: #c02c65;
        }

        .back-to-shop {
            display: inline-block;
            padding: 12px 30px;
            background: #fff;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .back-to-shop:hover {
            background: var(--primary-color);
            color: #fff;
        }

        .error-message, .success-message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
        }

        @media (max-width: 768px) {
            .coming-soon-content h1 {
                font-size: 32px;
            }

            .coming-soon-content p {
                font-size: 16px;
            }

            .countdown-item span {
                font-size: 24px;
            }

            .countdown-item {
                min-width: 60px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="loader">
            <img src="assets/images/loader.gif" alt="Loader">
        </div>
    </div>
    <!-- Overlay -->
    <div class="offcanvas__overlay"></div>

    <!--====== Start Overlay ======-->
    <?php include 'include/sidecart.php' ?>
    <?php include 'include/wishlistcart.php' ?>

    <!--====== Start Header Section ======-->
    <header class="header-area">
        <?php include 'include/header.php'; ?>
        <?php include 'include/nav.php'; ?>
    </header>
    <!--====== End Header Section ======-->

    <!-- Coming Soon Section -->
    <section class="coming-soon-section">
        <div class="coming-soon-content" data-aos="fade-up">
            <h1>Coming Soon!</h1>
            <p>Our product details page is under construction. Stay tuned for an amazing shopping experience with HSCOLLEXTIONS!</p>
            <div class="countdown-timer" id="countdown">
                <div class="countdown-item">
                    <span id="days">00</span>
                    <label>Days</label>
                </div>
                <div class="countdown-item">
                    <span id="hours">00</span>
                    <label>Hours</label>
                </div>
                <div class="countdown-item">
                    <span id="minutes">00</span>
                    <label>Minutes</label>
                </div>
                <div class="countdown-item">
                    <span id="seconds">00</span>
                    <label>Seconds</label>
                </div>
            </div>
           
            <a href="shops.php" class="back-to-shop">Back to Shop</a>
        </div>
    </section>

    <!-- Footer -->
    <?php 
    if (file_exists('include/footer.php')) {
        include 'include/footer.php';
    } else {
        echo "<p>Error: include/footer.php not found</p>";
    }
    ?>

    <!-- Scripts -->
    <script src="assets/vendor/jquery-3.7.1.min.js"></script>
    <script src="assets/vendor/popper/popper.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/vendor/slick/slick.min.js"></script>
    <script src="assets/vendor/nice-select/js/jquery.nice-select.min.js"></script>
    <script src="assets/vendor/magnific-popup/dist/jquery.magnific-popup.min.js"></script>
    <script src="assets/vendor/jquery-ui/jquery-ui.min.js"></script>
    <script src="assets/vendor/simplyCountdown.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/js/theme.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize AOS
            AOS.init();

            // Countdown Timer (set to 7 days from now)
            simplyCountdown('#countdown', {
                year: <?= date('Y', strtotime('+7 days')) ?>,
                month: <?= date('m', strtotime('+7 days')) ?>,
                day: <?= date('d', strtotime('+7 days')) ?>,
                hours: 0,
                minutes: 0,
                seconds: 0,
                inline: true,
                words: {
                    days: 'Days',
                    hours: 'Hours',
                    minutes: 'Minutes',
                    seconds: 'Seconds',
                    pluralLetter: ''
                },
                onEnd: function() {
                    $('#countdown').html('<p>Launch time has arrived!</p>');
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

            // Newsletter Form Submission (basic client-side validation)
            $('.newsletter-form').submit(function(e) {
                e.preventDefault();
                var email = $('input[name="email"]').val();
                if (email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    $('.newsletter-form').before('<div class="success-message">Thank you for subscribing!</div>');
                    $('input[name="email"]').val('');
                } else {
                    $('.newsletter-form').before('<div class="error-message">Please enter a valid email address.</div>');
                }
                setTimeout(function() {
                    $('.success-message, .error-message').fadeOut();
                }, 3000);
            });
        });
    </script>
</body>
</html>