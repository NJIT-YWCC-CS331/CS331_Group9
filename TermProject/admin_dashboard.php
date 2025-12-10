<?php
// Start session and include necessary files
session_start();
require 'db.php';  // Database connection
include 'admin_nav.php';  // Navigation bar with auth check

// Query 1: Count total registered passengers/users
$totalUsersQuery = "SELECT COUNT(*) as total FROM PASSENGER";
$totalUsersStmt = oci_parse($conn, $totalUsersQuery);
oci_execute($totalUsersStmt);
$totalUsersRow = oci_fetch_assoc($totalUsersStmt);
$totalUsers = $totalUsersRow['TOTAL'];

// Query 2: Count total booked tickets
$totalTicketsQuery = "SELECT COUNT(*) as total FROM TICKET";
$totalTicketsStmt = oci_parse($conn, $totalTicketsQuery);
oci_execute($totalTicketsStmt);
$totalTicketsRow = oci_fetch_assoc($totalTicketsStmt);
$totalTickets = $totalTicketsRow['TOTAL'];

// Query 3: Count total flights in system
$totalFlightsQuery = "SELECT COUNT(*) as total FROM FLIGHT";
$totalFlightsStmt = oci_parse($conn, $totalFlightsQuery);
oci_execute($totalFlightsStmt);
$totalFlightsRow = oci_fetch_assoc($totalFlightsStmt);
$totalFlights = $totalFlightsRow['TOTAL'];

// Query 4: Calculate total revenue from all payments
// Uses ?? operator to default to 0 if no payments exist
$totalRevenueQuery = "SELECT SUM(Amount) as total FROM PAYMENT_RECORD";
$totalRevenueStmt = oci_parse($conn, $totalRevenueQuery);
oci_execute($totalRevenueStmt);
$totalRevenueRow = oci_fetch_assoc($totalRevenueStmt);
$totalRevenue = $totalRevenueRow['TOTAL'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1 { 
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .stat-card h3 {
            color: #666;
            font-size: 16px;
            margin: 0 0 15px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stat-card .number {
            font-size: 48px;
            font-weight: bold;
            color: #007bff;
            margin: 0;
        }
        .stat-card.revenue .number {
            color: #28a745;
        }
        .quick-links {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .quick-links h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .link-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .link-button {
            display: block;
            padding: 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.2s;
        }
        .link-button:hover {
            background-color: #0056b3;
        }
        .welcome-message {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        .welcome-message h2 {
            color: #007bff;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Admin Dashboard</h1>
        
        <div class="welcome-message">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>
            <p>Flight Ticket Booking System - Administration Panel</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p class="number"><?php echo number_format($totalUsers); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Tickets</h3>
                <p class="number"><?php echo number_format($totalTickets); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Flights</h3>
                <p class="number"><?php echo number_format($totalFlights); ?></p>
            </div>
            
            <div class="stat-card revenue">
                <h3>Total Revenue</h3>
                <p class="number">$<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
        </div>

        <div class="quick-links">
            <h2>Quick Links</h2>
            <div class="link-grid">
                <a href="admin_users.php" class="link-button">Manage Users</a>
                <a href="admin_tickets.php" class="link-button">View All Tickets</a>
            </div>
        </div>
    </div>
</body>
</html>
