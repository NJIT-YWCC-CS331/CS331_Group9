<?php
// Authentication check: Ensure admin is logged in before showing navigation
// This file is included in all admin pages to protect them
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login if not authenticated
    header("Location: admin_login.php");
    exit();
}
// If authenticated, navigation bar is displayed below
?>
<nav style="text-align: center; margin-bottom: 20px; padding: 10px; background-color: #acacadff; border-bottom: 3px solid #001affff;">
    <a href="admin_dashboard.php" style="margin: 0 15px; text-decoration: none; color: #ffffff; font-weight: bold;">Dashboard</a> |
    <a href="admin_users.php" style="margin: 0 15px; text-decoration: none; color: #ffffff; font-weight: bold;">Users</a> |
    <a href="admin_tickets.php" style="margin: 0 15px; text-decoration: none; color: #ffffff; font-weight: bold;">Tickets</a> |
    <a href="admin_logout.php" style="margin: 0 15px; text-decoration: none; color: #dc3545; font-weight: bold;">Logout</a>
</nav>
