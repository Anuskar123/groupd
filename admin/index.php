<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

// Check if user is admin
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ' . SITE_URL . 'pages/login.php');
    exit();
}

$page_title = "Admin Dashboard";
$database = new Database();

// Get dashboard statistics
$stats = [];

// Total products
$database->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
$stats['total_products'] = $database->single()['count'];

// Total users
$database->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'customer' AND is_active = 1");
$stats['total_customers'] = $database->single()['count'];

// Total orders
$database->query("SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $database->single()['count'];

// Total revenue
$database->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE payment_status = 'paid'");
$stats['total_revenue'] = $database->single()['revenue'];

// Recent orders
$database->query("SELECT o.*, u.first_name, u.last_name 
                  FROM orders o 
                  JOIN users u ON o.user_id = u.user_id 
                  ORDER BY o.order_date DESC LIMIT 5");
$recent_orders = $database->resultset();

// Low stock products
$database->query("SELECT * FROM products WHERE stock_quantity <= 10 AND is_active = 1 ORDER BY stock_quantity ASC LIMIT 5");
$low_stock_products = $database->resultset();

include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        
        <div class="col-md-9 col-lg-10">
            <div class="admin-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <div class="text-muted">
                        Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card bg-primary">
                            <div class="stat-value"><?php echo number_format($stats['total_products']); ?></div>
                            <div class="stat-label">Total Products</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card bg-success">
                            <div class="stat-value"><?php echo number_format($stats['total_customers']); ?></div>
                            <div class="stat-label">Total Customers</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card bg-info">
                            <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
                            <div class="stat-label">Total Orders</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card bg-warning">
                            <div class="stat-value">Rs. <?php echo number_format($stats['total_revenue'], 2); ?></div>
                            <div class="stat-label">Total Revenue</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-lg-8 mb-4">
                        <div class="admin-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Recent Orders</h5>
                                <a href="orders.php" class="btn btn-outline-primary btn-sm">View All</a>
                            </div>
                            
                            <?php if(empty($recent_orders)): ?>
                                <p class="text-muted">No orders yet.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($recent_orders as $order): ?>
                                            <tr>
                                                <td>
                                                    <a href="order_detail.php?id=<?php echo $order['order_id']; ?>" class="text-decoration-none">
                                                        <?php echo $order['order_number']; ?>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                                <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $order['order_status']; ?>">
                                                        <?php echo ucfirst($order['order_status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Low Stock Alert -->
                    <div class="col-lg-4 mb-4">
                        <div class="admin-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Low Stock Alert</h5>
                                <a href="products.php" class="btn btn-outline-warning btn-sm">Manage</a>
                            </div>
                            
                            <?php if(empty($low_stock_products)): ?>
                                <p class="text-muted">All products are well stocked.</p>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach($low_stock_products as $product): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                                            <br><small class="text-muted">SKU: <?php echo $product['sku']; ?></small>
                                        </div>
                                        <span class="badge bg-<?php echo $product['stock_quantity'] == 0 ? 'danger' : 'warning'; ?>">
                                            <?php echo $product['stock_quantity']; ?> left
                                        </span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="admin-card">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="add_product.php" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="add_category.php" class="btn btn-success w-100">
                                <i class="fas fa-folder-plus"></i> Add Category
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="orders.php" class="btn btn-info w-100">
                                <i class="fas fa-list"></i> View Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="customers.php" class="btn btn-warning w-100">
                                <i class="fas fa-users"></i> View Customers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
