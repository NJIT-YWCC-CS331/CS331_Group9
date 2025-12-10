<?php
if(!isset($_SESSION)) { 
    session_start(); 
}

if(isset($_SESSION['PassengerID'])):
?>
<nav style="text-align: center; margin-bottom: 20px; padding: 10px; background-color: #acacadff; border-bottom: 3px solid #001affff;">
    <a href="profile.php" style="margin: 0 15px; text-decoration: none; color: #ffffff; font-weight: bold;">Profile</a> |
    <a href="searchFlight.php" style="margin: 0 15px; text-decoration: none; color: #ffffff; font-weight: bold;">Search Flights</a> |
    <a href="myTickets.php" style="margin: 0 15px; text-decoration: none; color: #ffffff; font-weight: bold;">My Tickets</a> |
    <a href="logout.php" style="margin: 0 15px; text-decoration: none; color: #dc3545; font-weight: bold;">Logout</a>
</nav>
<?php else: ?>
<nav style="text-align: center; margin-bottom: 20px; padding: 10px; background-color: #acacadff; border-bottom: 3px solid #001affff; position: relative;">
    <a href="login.php" style="margin: 0 15px; text-decoration: none; color: #ffffff; font-weight: bold;">Login</a> |
    <a href="register.php" style="margin: 0 15px; text-decoration: none; color: #ffffff; font-weight: bold;">Register</a>
    <a href="admin_login.php" style="position: absolute; right: 20px; text-decoration: none; color: #700801ff; font-weight: bold; font-size: 12px; padding: 3px 8px; border: 1px solid #700801ff; border-radius: 3px;">🔒 Admin</a>
</nav>
<?php endif; ?>