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
        <title>H.S Collextions</title>
        <!--====== Favicon Icon ======-->
        <link rel="shortcut icon" href="assets/images/favicon.png" type="image/png">
        <!--====== Google Fonts ======-->
        <link href="https://fonts.googleapis.com/css2?family=Aoboshi+One&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
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
<!--====== Start Page Banner ======-->
<section class="page-banner" style="position: relative; z-index: 1;">
    <div class="page-banner-wrapper p-r z-1">

        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <!--=== Page Banner Content ===-->
                    <div class="page-banner-content">
                        <h1>Contact</h1>
                        
                    </div>
                </div>
            </div>
        </div>

        <!--=== Fixed Map Section ===-->
        <div class="map-container-wrapper" style="
            position: relative;
            width: 100%;
            margin-top: 30px;
        ">
            <div class="map-container" style="
                width: 100%;
                height: 400px;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            ">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d28917.489441602773!2d67.00113662273254!3d24.86073427783521!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3eb33bcd7e96977d%3A0xa8e5e2a4db7e987e!2sKarachi%2C%20Pakistan!5e0!3m2!1sen!2s!4v1731031443349!5m2!1sen!2s"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </div>
</section>
<!--====== End Page Banner ======-->

<style>
/* ✅ Responsive Fixes for the Map */
.map-container iframe {
    width: 100%;
    height: 100%;
}

/* Reduce map height on mobile */
@media (max-width: 768px) {
    .map-container {
        height: 300px;
        border-radius: 10px;
    }
    .page-banner-content h1 {
        font-size: 32px;
    }
}

/* Prevent navbar overlap if fixed header exists */
.page-banner {
    margin-top: 90px; /* adjust if navbar height differs */
}
</style>


            <!--====== Start Animated-headline Section ======-->
            <section class="animated-headline-area pt-90 pb-95">
                <div class="headline-wrap style-two pt-25 pb-25 white-bg">
                    <span class="marquee-wrap">
                        <span class="marquee-inner left">
                            <span class="marquee-item"><b>OUR GLOBAL OFFICE ADDRESS</b><i class="flaticon-star-2"></i></span>
                            <span class="marquee-item"><b>OUR GLOBAL OFFICE ADDRESS</b><i class="flaticon-star-2"></i></span>
                        </span>
                        <span class="marquee-inner left">
                            <span class="marquee-item"><b>OUR GLOBAL OFFICE ADDRESS</b><i class="flaticon-star-2"></i></span>
                            <span class="marquee-item"><b>OUR GLOBAL OFFICE ADDRESS</b><i class="flaticon-star-2"></i></span>
                        </span>
                        <span class="marquee-inner left">
                            <span class="marquee-item"><b>OUR GLOBAL OFFICE ADDRESS</b><i class="flaticon-star-2"></i></span>
                            <span class="marquee-item"><b>OUR GLOBAL OFFICE ADDRESS</b><i class="flaticon-star-2"></i></span>
                        </span>
                    </span>
                </div>
            </section><!--====== End Animated-headline Section ======-->
            <!--====== Start Contact Information Section ======-->
            <section class="contact-information-section">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="single-information-wrapper">
                                <div class="single-information-item d-flex justify-content-between mb-15" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="50">
                                    <div class="content mb-20">
                                        <h4 style="color: #333;" >Office:</h4>
                                        <p style="color:  #de3576;" >xyz ADDRESS</p>
                                    </div>
                                    <div class="content mb-20">
                                        <h4 style="color: #333;" >Phone:</h4>
                                        <p><a  style="color:  #de3576;" href="tel:+923462744165">+923462744165</a></p>
                                    </div>
                                    <div class="content mb-20">
                                        <h4 style="color: #333;" >Email:</h4>
                                        <p><a  style="color:  #de3576;" href="mailto:help@domain.com">hscollextions@gmail.com</a></p>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </section><!--====== End Contact Information Section ======-->
            <!--====== Start Contact Section ======-->
<section class="contact-section pt-75 pb-70">
    <div class="container">
        <div class="row">
            <!-- Form Column -->
            <div class="col-lg-8">
                <div class="contact-wrapper p-r z-1 mb-50" data-aos="fade-right" data-aos-delay="10" data-aos-duration="1000">
                    <div class="shape shape-one">
                        <span><img src="assets/images/shape/cl-line.png" alt="Line Shape"></span>
                    </div>
                    <h3>Get in touch</h3>

                    <form id="contact-form" class="pesco-contact-form" action="https://api.web3forms.com/submit" method="POST" autocomplete="off">
                        <!-- Web3Forms Access Key -->
                        <input type="hidden" name="access_key" value="e8692be6-515c-4244-a7ba-6497ae5b8e20">
                        
                        <!-- Optional Redirect (or you can remove this) -->
                        <!-- <input type="hidden" name="redirect" value="thankyou.php"> -->

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input type="text" placeholder="Name" class="form_control" name="name" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <input type="email" placeholder="Email" class="form_control" name="email" required>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <textarea class="form_control" placeholder="Write your message" name="message" cols="5" rows="9" required></textarea>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form_group">
                                    <button type="submit" class="theme-btn style-one">Send Message</button>
                                </div>
                            </div>
                        </div>
                        <!-- Message area -->
                        <div id="form-message" class="mt-3 text-center"></div>
                    </form>
                </div>
            </div>

            <!-- Image Column -->
            <div class="col-lg-4">
                <div class="contact-img-text text-center mb-50 d-none d-lg-block" data-aos="fade-left" data-aos-delay="15" data-aos-duration="1500">
                    <img src="assets/images/contact/text-img.png" alt="Text">
                </div>
            </div>
        </div>
    </div>
</section>
<!--====== End Contact Section ======-->

<!--====== Form Script ======-->
<script>
const form = document.getElementById('contact-form');
const msg = document.getElementById('form-message');

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    msg.textContent = "⏳ Sending message...";
    msg.style.color = "#777";

    const formData = new FormData(form);

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        if (data.success) {
            msg.textContent = "🎉 Message sent successfully! We'll get back to you soon.";
            msg.style.color = "green";
            form.reset();
        } else {
            msg.textContent = "⚠️ Something went wrong. Please check your access key or try again later.";
            msg.style.color = "red";
        }
    } catch (error) {
        msg.textContent = "⚠️ Network error. Please try again.";
        msg.style.color = "red";
    }
});
</script>

<style>
/* Optional - make the message look cleaner */
#form-message {
    font-size: 15px;
    font-weight: 500;
}
</style>

        </main>
    <!--====== Start Footer Main  ======-->
      <?php include 'include/footer.php' ?>
    <!--====== End Footer Main ======-->
    
    <!--====== Main js ======-->
    <script src="assets/js/wishlist.js"></script>
    </body>
</html>