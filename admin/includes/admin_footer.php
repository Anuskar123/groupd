    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>assets/js/script.js"></script>
    
    <!-- Admin specific scripts -->
    <script>
        $(document).ready(function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-hide alerts
            $('.alert').delay(5000).fadeOut();

            // Confirm delete actions
            $('.delete-confirm').on('click', function(e) {
                if(!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
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

            // Image preview for file uploads
            $('input[type="file"]').on('change', function() {
                var input = this;
                var preview = $(this).data('preview');
                
                if (input.files && input.files[0] && preview) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(preview).attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            });

            // DataTables initialization if present
            if($.fn.DataTable) {
                $('.data-table').DataTable({
                    responsive: true,
                    pageLength: 25,
                    order: [[0, 'desc']]
                });
            }
        });

        // Admin utility functions
        function updateOrderStatus(orderId, status) {
            if(confirm('Are you sure you want to update this order status?')) {
                $.ajax({
                    url: 'ajax/update_order_status.php',
                    method: 'POST',
                    data: {
                        order_id: orderId,
                        status: status
                    },
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            showToast('Order status updated successfully', 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            showToast(response.message || 'Failed to update order status', 'error');
                        }
                    },
                    error: function() {
                        showToast('An error occurred. Please try again.', 'error');
                    }
                });
            }
        }

        function toggleProductStatus(productId, status) {
            var action = status ? 'activate' : 'deactivate';
            if(confirm('Are you sure you want to ' + action + ' this product?')) {
                $.ajax({
                    url: 'ajax/toggle_product_status.php',
                    method: 'POST',
                    data: {
                        product_id: productId,
                        status: status
                    },
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            showToast('Product status updated successfully', 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            showToast(response.message || 'Failed to update product status', 'error');
                        }
                    },
                    error: function() {
                        showToast('An error occurred. Please try again.', 'error');
                    }
                });
            }
        }

        function exportData(type) {
            window.open('export.php?type=' + type, '_blank');
        }

        function printReport() {
            window.print();
        }
    </script>
</body>
</html>
