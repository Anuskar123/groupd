<?php
session_start();

// Destroy all session data
session_destroy();

// Redirect to homepage
header('Location: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../index.php'));
exit();
?>
