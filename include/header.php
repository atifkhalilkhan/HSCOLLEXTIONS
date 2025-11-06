<!-- <?php
include 'config.php';
$category_ids = isset($_GET['category']) ? array_map('intval', (array)$_GET['category']) : [];
?>

<div class="search-header-main">
    <div class="container">
        <div class="search-header-inner">
            <div class="site-branding">
                <a href="index.html" class="brand-logo">
                    <img src="assets/images/logo/logo.jpg" alt="HSCOLLEXTIONS - Women’s Fashion Brand">
                </a>
            </div>
            <div class="product-search-category">
                <form action="shops.php" method="GET">
                    <select class="wide" name="category[]">
                        <option value="">All Categories</option>

                        <?php
                        $cat_sql = "SELECT id, name FROM categories";
                        $cat_result = $conn->query($cat_sql);
                        while ($cat = $cat_result->fetch_assoc()):
                            $selected = in_array($cat['id'], $category_ids) ? 'selected' : '';
                        ?>
                            <option value="<?= $cat['id'] ?>" <?= $selected ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div class="form-group">
                        <input type="text" name="search" placeholder="Search Women’s Fashion, Suits or Handbags..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        <button class="search-btn"><i class="far fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="hotline-support item-rtl">
                <div class="icon"><i class="flaticon-support"></i></div>
                <div class="info">
                    <span>24/7 Support</span>
                    <h5><a href="tel:+923462744165">+923462744165</a></h5>
                </div>
            </div>
        </div>
    </div>
</div> -->

<?php
include 'config.php';
$category_ids = isset($_GET['category']) ? array_map('intval', (array)$_GET['category']) : [];
?>

<!--====== Start Header Search Section ======-->
<div class="search-header-main">
    <div class="container">
        <div class="search-header-inner">
            
            <!--=== Site Branding ===-->
            <div class="site-branding">
                <a href="index.html" class="brand-logo">
                    <img src="assets/images/logo/logo.jpg" alt="HSCOLLEXTIONS - Women’s Fashion Brand">
                </a>
            </div>

            <!--=== Product Search Category ===-->
            <div class="product-search-category">
                <form action="shops.php" method="GET">
                    <select class="wide" name="category[]">
                        <option value="">All Categories</option>
                        <?php
                        $cat_sql = "SELECT id, name FROM categories";
                        $cat_result = $conn->query($cat_sql);
                        while ($cat = $cat_result->fetch_assoc()):
                            $selected = in_array($cat['id'], $category_ids) ? 'selected' : '';
                        ?>
                            <option value="<?= $cat['id'] ?>" <?= $selected ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endwhile; ?>
                    </select>

                    <div class="form-group">
                        <input type="text" name="search" placeholder="Search Women’s Fashion, Suits or Handbags..."
                               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        <button class="search-btn"><i class="far fa-search"></i></button>
                    </div>
                </form>
            </div>

            <!--=== Hotline Support ===-->
            <div class="hotline-support item-rtl">
                <div class="icon"><i class="flaticon-support"></i></div>
                <div class="info">
                    <span>24/7 Support</span>
                    <h5><a href="tel:+923462744165">+923462744165</a></h5>
                </div>
            </div>

        </div>
    </div>
</div>
<!--====== End Header Search Section ======-->
