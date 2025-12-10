<?php
// Start session to access session variables
session_start();

// Remove specific admin session variables
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']);

// Destroy the entire session
session_destroy();

// Redirect to admin login page
header("Location: admin_login.php");
exit();
?>
