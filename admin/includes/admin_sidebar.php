<div class="col-md-3 col-lg-2 px-0">
    <div class="admin-sidebar">
        <nav class="nav flex-column">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
               href="<?php echo SITE_URL; ?>admin/">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            
            <div class="nav-section">
                <small class="nav-section-title text-muted px-3 py-2 d-block">PRODUCTS</small>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>admin/products.php">
                    <i class="fas fa-box"></i> Manage Products
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>admin/add_product.php">
                    <i class="fas fa-plus"></i> Add Product
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>admin/categories.php">
                    <i class="fas fa-folder"></i> Categories
                </a>
            </div>
            
            <div class="nav-section">
                <small class="nav-section-title text-muted px-3 py-2 d-block">ORDERS</small>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>admin/orders.php">
                    <i class="fas fa-shopping-cart"></i> All Orders
                </a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>admin/orders.php?status=pending">
                    <i class="fas fa-clock"></i> Pending Orders
                </a>
            </div>
            
            <div class="nav-section">
                <small class="nav-section-title text-muted px-3 py-2 d-block">USERS</small>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>admin/customers.php">
                    <i class="fas fa-users"></i> Customers
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admins.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>admin/admins.php">
                    <i class="fas fa-user-shield"></i> Administrators
                </a>
            </div>
            
            <div class="nav-section">
                <small class="nav-section-title text-muted px-3 py-2 d-block">REPORTS</small>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>admin/reports.php">
                    <i class="fas fa-chart-bar"></i> Sales Reports
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>admin/analytics.php">
                    <i class="fas fa-analytics"></i> Analytics
                </a>
            </div>
            
            <div class="nav-section">
                <small class="nav-section-title text-muted px-3 py-2 d-block">SETTINGS</small>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" 
                   href="<?php echo SITE_URL; ?>admin/settings.php">
                    <i class="fas fa-cog"></i> System Settings
                </a>
                <a class="nav-link" href="<?php echo SITE_URL; ?>admin/backup.php">
                    <i class="fas fa-download"></i> Backup & Export
                </a>
            </div>
        </nav>
    </div>
</div>
