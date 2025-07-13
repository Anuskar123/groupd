<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . 'pages/login.php');
    exit();
}

require_once '../config/config.php';
require_once '../config/database.php';

$page_title = "Shopping Cart";
$database = new Database();

// Get cart items for the user
$database->query("SELECT c.*, p.product_name, p.price, p.image_url, p.unit, p.stock_quantity 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.product_id 
                  WHERE c.user_id = :user_id AND p.is_active = 1
                  ORDER BY c.added_at DESC");
$database->bind(':user_id', $_SESSION['user_id']);
$cart_items = $database->resultset();

// Calculate totals
$subtotal = 0;
foreach($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping_fee = $subtotal >= 2000 ? 0 : 100; // Free shipping above Rs. 2000
$total = $subtotal + $shipping_fee;

include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <h2>Shopping Cart</h2>
            
            <?php if(empty($cart_items)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h4>Your cart is empty</h4>
                    <p class="text-muted">Start shopping to add items to your cart.</p>
                    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <!-- Cart Items -->
                <?php foreach($cart_items as $item): ?>
                <div class="cart-item" id="cart-item-<?php echo $item['cart_id']; ?>">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <img src="<?php echo SITE_URL; ?>assets/images/products/<?php echo $item['image_url'] ?: 'default-product.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                 class="cart-item-image">
                        </div>
                        <div class="col-md-4">
                            <h5 class="mb-1"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                            <p class="text-muted mb-0">Rs. <?php echo number_format($item['price'], 2); ?> per <?php echo $item['unit']; ?></p>
                            <?php if($item['stock_quantity'] <= 0): ?>
                                <small class="text-danger">Out of stock</small>
                            <?php elseif($item['stock_quantity'] < $item['quantity']): ?>
                                <small class="text-warning">Only <?php echo $item['stock_quantity']; ?> available</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <div class="quantity-controls">
                                <button type="button" class="btn btn-outline-secondary btn-sm quantity-decrease" 
                                        onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] - 1; ?>)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="form-control text-center" 
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="1" max="<?php echo $item['stock_quantity']; ?>"
                                       onchange="updateQuantity(<?php echo $item['cart_id']; ?>, this.value)">
                                <button type="button" class="btn btn-outline-secondary btn-sm quantity-increase" 
                                        onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] + 1; ?>)"
                                        <?php echo $item['quantity'] >= $item['stock_quantity'] ? 'disabled' : ''; ?>>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <strong id="item-total-<?php echo $item['cart_id']; ?>">
                                Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </strong>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                    onclick="removeFromCart(<?php echo $item['cart_id']; ?>)"
                                    title="Remove item">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Cart Actions -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="products.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                    <button type="button" class="btn btn-outline-danger" onclick="clearCart()">
                        <i class="fas fa-trash"></i> Clear Cart
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Order Summary -->
        <?php if(!empty($cart_items)): ?>
        <div class="col-md-4">
            <div class="order-summary">
                <h4>Order Summary</h4>
                <hr>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span class="cart-subtotal">Rs. <?php echo number_format($subtotal, 2); ?></span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Shipping:</span>
                    <span class="cart-shipping">
                        <?php if($shipping_fee > 0): ?>
                            Rs. <?php echo number_format($shipping_fee, 2); ?>
                        <?php else: ?>
                            <span class="text-success">Free</span>
                        <?php endif; ?>
                    </span>
                </div>
                
                <?php if($subtotal < 2000 && $shipping_fee > 0): ?>
                <div class="alert alert-info small">
                    <i class="fas fa-info-circle"></i> 
                    Add Rs. <?php echo number_format(2000 - $subtotal, 2); ?> more for free shipping!
                </div>
                <?php endif; ?>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong class="cart-total">Rs. <?php echo number_format($total, 2); ?></strong>
                </div>
                
                <div class="d-grid">
                    <a href="checkout.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                    </a>
                </div>
                
                <!-- Delivery Info -->
                <div class="mt-4 p-3 bg-light rounded">
                    <h6><i class="fas fa-truck"></i> Delivery Information</h6>
                    <small class="text-muted">
                        • Standard delivery: 1-2 business days<br>
                        • Free delivery on orders above Rs. 2000<br>
                        • Cash on delivery available
                    </small>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateQuantity(cartId, quantity) {
    if(quantity < 1) {
        removeFromCart(cartId);
        return;
    }
    
    $.ajax({
        url: 'ajax/update_cart.php',
        method: 'POST',
        data: {
            cart_id: cartId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Update the total for this item
                $('#item-total-' + cartId).text('Rs. ' + parseFloat(response.item_total).toFixed(2));
                
                // Update cart summary
                updateCartSummary();
                updateCartCount();
            } else {
                showToast(response.message || 'Failed to update quantity', 'error');
                location.reload(); // Reload to restore correct quantities
            }
        },
        error: function() {
            showToast('An error occurred. Please try again.', 'error');
            location.reload();
        }
    });
}

function removeFromCart(cartId) {
    if(confirm('Are you sure you want to remove this item from cart?')) {
        $.ajax({
            url: 'ajax/remove_from_cart.php',
            method: 'POST',
            data: { cart_id: cartId },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    $('#cart-item-' + cartId).fadeOut(function() {
                        $(this).remove();
                        updateCartSummary();
                        updateCartCount();
                        
                        // Check if cart is empty
                        if($('.cart-item').length === 0) {
                            location.reload();
                        }
                    });
                    showToast('Item removed from cart', 'success');
                } else {
                    showToast(response.message || 'Failed to remove item', 'error');
                }
            },
            error: function() {
                showToast('An error occurred. Please try again.', 'error');
            }
        });
    }
}

function clearCart() {
    if(confirm('Are you sure you want to clear your entire cart?')) {
        $.ajax({
            url: 'ajax/clear_cart.php',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    location.reload();
                } else {
                    showToast('Failed to clear cart', 'error');
                }
            },
            error: function() {
                showToast('An error occurred. Please try again.', 'error');
            }
        });
    }
}

function updateCartSummary() {
    $.get('ajax/get_cart_summary.php', function(data) {
        if(data.success) {
            $('.cart-subtotal').text('Rs. ' + parseFloat(data.subtotal).toFixed(2));
            $('.cart-total').text('Rs. ' + parseFloat(data.total).toFixed(2));
            
            // Update shipping
            if(data.shipping_fee > 0) {
                $('.cart-shipping').html('Rs. ' + parseFloat(data.shipping_fee).toFixed(2));
            } else {
                $('.cart-shipping').html('<span class="text-success">Free</span>');
            }
        }
    }, 'json');
}
</script>

<?php include '../includes/footer.php'; ?>
