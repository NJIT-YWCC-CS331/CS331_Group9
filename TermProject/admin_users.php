<?php
// Start session and include necessary files
session_start();
require 'db.php';  // Database connection
include 'admin_nav.php';  // Navigation bar with auth check

// Query to fetch all registered passengers/users
// Selects all user details ordered by PassengerID
$sql = "SELECT PassengerID, FName, MName, LName, DOB, Nationality, PhoneNumber, Email 
        FROM PASSENGER 
        ORDER BY PassengerID";

// Prepare and execute the query
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

// Fetch all users into an array for display
$users = [];
while ($row = oci_fetch_assoc($stmt)) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - All Users</title>
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
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .user-count {
            text-align: center;
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        .table-wrapper {
            overflow-x: auto;
        }
        table { 
            width: 100%; 
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: left;
        }
        th { 
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
        .user-id {
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>All Registered Users</h1>
        
        <div class="user-count">
            <strong>Total Users: <?php echo count($users); ?></strong>
        </div>

        <?php if (count($users) > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Passenger ID</th>
                            <th>First Name</th>
                            <th>M.I.</th>
                            <th>Last Name</th>
                            <th>Date of Birth</th>
                            <th>Nationality</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="user-id"><?php echo htmlspecialchars($user['PASSENGERID']); ?></td>
                                <td><?php echo htmlspecialchars($user['FNAME']); ?></td>
                                <td><?php echo htmlspecialchars($user['MNAME'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($user['LNAME']); ?></td>
                                <td><?php echo htmlspecialchars($user['DOB']); ?></td>
                                <td><?php echo htmlspecialchars($user['NATIONALITY'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['PHONENUMBER'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($user['EMAIL']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                No users found in the system.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
