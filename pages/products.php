<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

$page_title = "Products";
$database = new Database();

// Get filter parameters
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'name';

// Build query
$where_conditions = ["p.is_active = 1"];
$params = [];

if($category_filter > 0) {
    $where_conditions[] = "p.category_id = :category_id";
    $params[':category_id'] = $category_filter;
}

if(!empty($search_query)) {
    $where_conditions[] = "(p.product_name LIKE :search OR p.description LIKE :search)";
    $params[':search'] = '%' . $search_query . '%';
}

$where_clause = implode(' AND ', $where_conditions);

// Determine sort order
$order_clause = "";
switch($sort_by) {
    case 'name':
        $order_clause = "ORDER BY p.product_name ASC";
        break;
    case 'price_low':
        $order_clause = "ORDER BY p.price ASC";
        break;
    case 'price_high':
        $order_clause = "ORDER BY p.price DESC";
        break;
    case 'newest':
        $order_clause = "ORDER BY p.created_at DESC";
        break;
    default:
        $order_clause = "ORDER BY p.product_name ASC";
}

// Get products
$database->query("SELECT p.*, c.category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  WHERE $where_clause $order_clause");

foreach($params as $key => $value) {
    $database->bind($key, $value);
}

$products = $database->resultset();

// Get all categories for filter
$database->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY category_name");
$categories = $database->resultset();

include '../includes/header.php';
?>

<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Products</h1>
            <p class="text-muted">Fresh groceries delivered to your door</p>
        </div>
        <div class="col-md-6">
            <!-- Search Form -->
            <form method="GET" action="" class="d-flex">
                <input type="text" class="form-control me-2" name="search" 
                       placeholder="Search products..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
                <?php if($category_filter > 0): ?>
                    <input type="hidden" name="category" value="<?php echo $category_filter; ?>">
                <?php endif; ?>
                <?php if($sort_by != 'name'): ?>
                    <input type="hidden" name="sort" value="<?php echo $sort_by; ?>">
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <!-- Categories Filter -->
                    <div class="mb-4">
                        <h6>Categories</h6>
                        <div class="list-group list-group-flush">
                            <a href="?<?php echo http_build_query(['search' => $search_query, 'sort' => $sort_by]); ?>" 
                               class="list-group-item list-group-item-action <?php echo $category_filter == 0 ? 'active' : ''; ?>">
                                All Categories
                            </a>
                            <?php foreach($categories as $category): ?>
                            <a href="?<?php echo http_build_query(['category' => $category['category_id'], 'search' => $search_query, 'sort' => $sort_by]); ?>" 
                               class="list-group-item list-group-item-action <?php echo $category_filter == $category['category_id'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Sort Options -->
                    <div class="mb-4">
                        <h6>Sort By</h6>
                        <select class="form-select" onchange="window.location.href='?<?php echo http_build_query(['category' => $category_filter, 'search' => $search_query]); ?>&sort=' + this.value">
                            <option value="name" <?php echo $sort_by == 'name' ? 'selected' : ''; ?>>Name A-Z</option>
                            <option value="price_low" <?php echo $sort_by == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort_by == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="newest" <?php echo $sort_by == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="mb-0 text-muted">
                    <?php echo count($products); ?> product(s) found
                    <?php if($category_filter > 0): ?>
                        in <?php 
                        $current_category = array_filter($categories, function($cat) use ($category_filter) {
                            return $cat['category_id'] == $category_filter;
                        });
                        echo htmlspecialchars(current($current_category)['category_name']);
                        ?>
                    <?php endif; ?>
                    <?php if(!empty($search_query)): ?>
                        for "<?php echo htmlspecialchars($search_query); ?>"
                    <?php endif; ?>
                </p>
                
                <?php if($category_filter > 0 || !empty($search_query)): ?>
                <a href="products.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-times"></i> Clear Filters
                </a>
                <?php endif; ?>
            </div>

            <?php if(empty($products)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>No products found</h4>
                    <p class="text-muted">Try adjusting your search criteria or browse all products.</p>
                    <a href="products.php" class="btn btn-primary">View All Products</a>
                </div>
            <?php else: ?>
                <div class="row products-container">
                    <?php foreach($products as $product): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="product-card" data-category="<?php echo $product['category_id']; ?>">
                            <div class="product-image">
                                <img src="<?php echo SITE_URL; ?>assets/images/products/<?php echo $product['image_url'] ?: 'default-product.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                     class="img-fluid">
                                <div class="product-overlay">
                                    <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" 
                                       class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                            <div class="product-info">
                                <span class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                <h5 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                <p class="product-description">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 80)) . '...'; ?>
                                </p>
                                <div class="product-price-cart">
                                    <span class="product-price">Rs. <?php echo number_format($product['price'], 2); ?>/<?php echo $product['unit']; ?></span>
                                    <?php if(isset($_SESSION['user_id'])): ?>
                                        <?php if($product['stock_quantity'] > 0): ?>
                                        <button class="btn btn-sm btn-primary add-to-cart" 
                                                data-product-id="<?php echo $product['product_id']; ?>"
                                                onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                        <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            Out of Stock
                                        </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                    <a href="login.php" class="btn btn-sm btn-outline-primary">Login to Buy</a>
                                    <?php endif; ?>
                                </div>
                                <?php if($product['stock_quantity'] <= 10 && $product['stock_quantity'] > 0): ?>
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Only <?php echo $product['stock_quantity']; ?> left in stock
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
