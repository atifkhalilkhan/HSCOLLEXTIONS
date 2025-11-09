<!-- Sidebar Wishlist -->
<div class="sidemenu-wrapper-wishlist">
  <div class="sidemenu-content">
    <h4>My Wishlist</h4>
    <div class="sidemenu-wishlist-close"><i class="far fa-times"></i></div>

    <ul class="pesco-mini-wishlist-list">
      <?php if (empty($_SESSION['wishlist'])): ?>
        <li>Your wishlist is empty</li>
      <?php else: ?>
        <?php foreach ($_SESSION['wishlist'] as $item): ?>
          <li class="sidebar-wishlist-item">
            <a href="?remove_wishlist=<?= $item['id'] ?>" class="remove-wishlist">
              <i class="far fa-trash-alt"></i>
            </a>
            <a href="product-details.php?id=<?= $item['id'] ?>">
              <img src="<?= htmlspecialchars($item['image']) ?>" alt="wishlist image">
              <?= htmlspecialchars($item['title']) ?>
            </a>
            <span class="price currency">₨ <?= number_format($item['price'], 2) ?></span>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </div>
</div>
