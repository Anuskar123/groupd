// FreshMart JavaScript Functions

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if( target.length ) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });

    // Update cart count on page load
    if($('.cart-count').length) {
        updateCartCount();
    }

    // Quantity controls
    $(document).on('click', '.quantity-increase', function() {
        var input = $(this).siblings('input[type="number"]');
        var currentVal = parseInt(input.val());
        input.val(currentVal + 1).trigger('change');
    });

    $(document).on('click', '.quantity-decrease', function() {
        var input = $(this).siblings('input[type="number"]');
        var currentVal = parseInt(input.val());
        if(currentVal > 1) {
            input.val(currentVal - 1).trigger('change');
        }
    });

    // Auto-hide alerts after 5 seconds
    $('.alert').delay(5000).fadeOut();

    // Confirm delete actions
    $('.delete-confirm').on('click', function(e) {
        if(!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
        }
    });

    // Form validation
    $('form').on('submit', function() {
        var isValid = true;
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if(!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        return isValid;
    });

    // Remove validation classes on input
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });

    // Price formatting
    $('.price').each(function() {
        var price = parseFloat($(this).text());
        $(this).text('Rs. ' + price.toFixed(2));
    });

    // Search functionality
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.product-card').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Image lazy loading
    $('img[data-src]').each(function() {
        var img = $(this);
        img.attr('src', img.data('src')).removeAttr('data-src');
    });
});

// Global Functions

// Update cart count
function updateCartCount() {
    $.get('pages/ajax/get_cart_count.php', function(count) {
        $('.cart-count').text(count || '0');
    }).fail(function() {
        $('.cart-count').text('0');
    });
}

// Show toast notification
function showToast(message, type = 'info', duration = 3000) {
    var alertClass = 'alert-' + type;
    var icon = getToastIcon(type);
    
    var toast = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="${icon}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('body').append(toast);
    
    setTimeout(function() {
        toast.fadeOut(function() {
            toast.remove();
        });
    }, duration);
}

function getToastIcon(type) {
    switch(type) {
        case 'success': return 'fas fa-check-circle';
        case 'error': 
        case 'danger': return 'fas fa-exclamation-circle';
        case 'warning': return 'fas fa-exclamation-triangle';
        case 'info': 
        default: return 'fas fa-info-circle';
    }
}

// Add to cart function
function addToCart(productId, quantity = 1) {
    $.ajax({
        url: 'pages/ajax/add_to_cart.php',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                updateCartCount();
                showToast('Product added to cart successfully!', 'success');
                
                // Update button state
                var button = $(`[data-product-id="${productId}"]`);
                button.html('<i class="fas fa-check"></i> Added')
                      .removeClass('btn-primary')
                      .addClass('btn-success')
                      .prop('disabled', true);
                
                setTimeout(function() {
                    button.html('<i class="fas fa-cart-plus"></i> Add to Cart')
                          .removeClass('btn-success')
                          .addClass('btn-primary')
                          .prop('disabled', false);
                }, 2000);
            } else {
                showToast(response.message || 'Failed to add product to cart', 'error');
            }
        },
        error: function() {
            showToast('An error occurred. Please try again.', 'error');
        }
    });
}

// Remove from cart function
function removeFromCart(cartId) {
    if(confirm('Are you sure you want to remove this item from cart?')) {
        $.ajax({
            url: 'pages/ajax/remove_from_cart.php',
            method: 'POST',
            data: { cart_id: cartId },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    updateCartCount();
                    location.reload(); // Reload to update cart display
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

// Update cart quantity
function updateCartQuantity(cartId, quantity) {
    if(quantity < 1) {
        removeFromCart(cartId);
        return;
    }
    
    $.ajax({
        url: 'pages/ajax/update_cart.php',
        method: 'POST',
        data: {
            cart_id: cartId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Update the total for this item
                var itemTotal = response.item_total;
                $(`#item-total-${cartId}`).text('Rs. ' + parseFloat(itemTotal).toFixed(2));
                
                // Update cart summary
                updateCartSummary();
            } else {
                showToast(response.message || 'Failed to update quantity', 'error');
            }
        },
        error: function() {
            showToast('An error occurred. Please try again.', 'error');
        }
    });
}

// Update cart summary
function updateCartSummary() {
    $.get('pages/ajax/get_cart_summary.php', function(data) {
        if(data.success) {
            $('.cart-subtotal').text('Rs. ' + parseFloat(data.subtotal).toFixed(2));
            $('.cart-total').text('Rs. ' + parseFloat(data.total).toFixed(2));
        }
    }, 'json');
}

// Loading state management
function showLoading(element) {
    var originalHtml = element.html();
    element.data('original-html', originalHtml);
    element.html('<span class="loading"></span> Loading...');
    element.prop('disabled', true);
}

function hideLoading(element) {
    var originalHtml = element.data('original-html');
    element.html(originalHtml);
    element.prop('disabled', false);
}

// Product filter functions
function filterProducts(categoryId) {
    if(categoryId === 'all') {
        $('.product-card').show();
    } else {
        $('.product-card').hide();
        $(`.product-card[data-category="${categoryId}"]`).show();
    }
}

function sortProducts(sortBy) {
    var container = $('.products-container');
    var products = container.children('.product-card').get();
    
    products.sort(function(a, b) {
        var aVal, bVal;
        
        switch(sortBy) {
            case 'name':
                aVal = $(a).find('.product-name').text().toLowerCase();
                bVal = $(b).find('.product-name').text().toLowerCase();
                return aVal.localeCompare(bVal);
                
            case 'price-low':
                aVal = parseFloat($(a).find('.product-price').text().replace(/[^\d.]/g, ''));
                bVal = parseFloat($(b).find('.product-price').text().replace(/[^\d.]/g, ''));
                return aVal - bVal;
                
            case 'price-high':
                aVal = parseFloat($(a).find('.product-price').text().replace(/[^\d.]/g, ''));
                bVal = parseFloat($(b).find('.product-price').text().replace(/[^\d.]/g, ''));
                return bVal - aVal;
                
            default:
                return 0;
        }
    });
    
    container.empty().append(products);
}

// Form utilities
function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    var re = /^[0-9]{10}$/;
    return re.test(phone.replace(/\D/g, ''));
}

// Image preview for file uploads
function previewImage(input, previewElement) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $(previewElement).attr('src', e.target.result).show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-save functionality for forms
function enableAutoSave(formSelector, saveUrl) {
    var form = $(formSelector);
    var saveTimeout;
    
    form.find('input, textarea, select').on('input change', function() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(function() {
            var formData = form.serialize();
            $.post(saveUrl, formData, function(response) {
                if(response.success) {
                    showToast('Changes saved automatically', 'success', 1000);
                }
            }, 'json');
        }, 2000);
    });
}

// Print functionality
function printDiv(divId) {
    var printContents = document.getElementById(divId).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
