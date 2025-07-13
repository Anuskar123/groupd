<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

// Initialize database connection
$database = new Database();

// Get featured products for homepage
$database->query("SELECT p.*, c.category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  WHERE p.is_featured = 1 AND p.is_active = 1 
                  ORDER BY p.created_at DESC LIMIT 6");
$featured_products = $database->resultset();

// Get all categories for navigation
$database->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY category_name");
$categories = $database->resultset();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">Fresh Groceries Delivered to Your Door</h1>
                    <p class="hero-description">Get the freshest fruits, vegetables, and daily essentials delivered right to your doorstep. Quality guaranteed!</p>
                    <a href="pages/products.php" class="btn btn-primary btn-lg">Shop Now</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image">
                    <img src="assets/images/hero-grocery.jpg" alt="Fresh Groceries" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Shop by Categories</h2>
        <div class="row">
            <?php foreach($categories as $category): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="category-card text-center">
                    <div class="category-icon">
                        <img src="assets/images/categories/<?php echo $category['image_url']; ?>" 
                             alt="<?php echo htmlspecialchars($category['category_name']); ?>" 
                             class="img-fluid category-img">
                    </div>
                    <h4 class="category-name"><?php echo htmlspecialchars($category['category_name']); ?></h4>
                    <p class="category-desc"><?php echo htmlspecialchars($category['description']); ?></p>
                    <a href="pages/products.php?category=<?php echo $category['category_id']; ?>" 
                       class="btn btn-outline-primary">Browse</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5">Featured Products</h2>
        <div class="row">
            <?php foreach($featured_products as $product): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="product-card">
                    <div class="product-image">
                        <img src="assets/images/products/<?php echo $product['image_url'] ?: 'default-product.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                             class="img-fluid">
                        <div class="product-overlay">
                            <a href="pages/product_detail.php?id=<?php echo $product['product_id']; ?>" 
                               class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                        <h5 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                        <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?></p>
                        <div class="product-price-cart">
                            <span class="product-price">Rs. <?php echo number_format($product['price'], 2); ?>/<?php echo $product['unit']; ?></span>
                            <?php if(isset($_SESSION['user_id'])): ?>
                            <button class="btn btn-sm btn-primary add-to-cart" 
                                    data-product-id="<?php echo $product['product_id']; ?>">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                            <?php else: ?>
                            <a href="pages/login.php" class="btn btn-sm btn-outline-primary">Login to Buy</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="pages/products.php" class="btn btn-primary btn-lg">View All Products</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon">
                        <i class="fas fa-truck fa-3x text-primary"></i>
                    </div>
                    <h4>Free Delivery</h4>
                    <p>Free delivery on orders above Rs. 2000</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon">
                        <i class="fas fa-leaf fa-3x text-success"></i>
                    </div>
                    <h4>Fresh Products</h4>
                    <p>100% fresh and organic products</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon">
                        <i class="fas fa-clock fa-3x text-warning"></i>
                    </div>
                    <h4>24/7 Support</h4>
                    <p>Round the clock customer support</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt fa-3x text-info"></i>
                    </div>
                    <h4>Secure Payment</h4>
                    <p>100% secure payment processing</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
// Add to cart functionality
$(document).ready(function() {
    $('.add-to-cart').click(function() {
        var productId = $(this).data('product-id');
        var button = $(this);
        
        $.ajax({
            url: 'pages/ajax/add_to_cart.php',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: 1
            },
            success: function(response) {
                var result = JSON.parse(response);
                if(result.success) {
                    button.html('<i class="fas fa-check"></i> Added');
                    button.removeClass('btn-primary').addClass('btn-success');
                    
                    // Update cart count in header
                    updateCartCount();
                    
                    // Show success message
                    showToast('Product added to cart successfully!', 'success');
                } else {
                    showToast(result.message, 'error');
                }
            },
            error: function() {
                showToast('An error occurred. Please try again.', 'error');
            }
        });
    });
});

function updateCartCount() {
    $.get('pages/ajax/get_cart_count.php', function(count) {
        $('.cart-count').text(count);
    });
}

function showToast(message, type) {
    // Simple toast notification
    var toastClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var toast = $('<div class="alert ' + toastClass + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">' +
                  '<span>' + message + '</span>' +
                  '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                  '</div>');
    
    $('body').append(toast);
    setTimeout(function() {
        toast.remove();
    }, 3000);
}
</script>
