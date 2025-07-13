<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

$page_title = "Login";
$error = '';
$success = '';

// Redirect if already logged in
if(isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL);
    exit();
}

// Handle login form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if(empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $database = new Database();
        
        // Check user credentials
        $database->query("SELECT * FROM users WHERE email = :email AND is_active = 1");
        $database->bind(':email', $email);
        $user = $database->single();
        
        if($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];
            
            // Redirect to intended page or home
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : SITE_URL;
            header('Location: ' . $redirect);
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="form-container">
                <div class="text-center mb-4">
                    <h2>Login to FreshMart</h2>
                    <p class="text-muted">Welcome back! Please sign in to your account.</p>
                </div>
                
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="mb-0">Don't have an account? 
                        <a href="register.php" class="text-decoration-none">Create one here</a>
                    </p>
                </div>
                
                <!-- Demo Credentials -->
                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="mb-2">Demo Credentials:</h6>
                    <small class="text-muted">
                        <strong>Admin:</strong> admin@freshmart.com / admin123<br>
                        <strong>Customer:</strong> customer@test.com / customer123
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
