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