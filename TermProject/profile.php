<?php
session_start();
require 'db.php'; // connect to Oracle
include 'nav.php';

// Check if user is logged in   
if (!isset($_SESSION['PassengerID'])) {
    header("Location: login.php");
    exit();
}


// query to get user details
$passenger_id = $_SESSION['PassengerID'];
$sql = "SELECT PassengerID, FName, MName, LName, TO_CHAR(DOB, 'YYYY-MM-DD') AS DOB, Nationality, PhoneNumber, Email
        FROM PASSENGER 
        WHERE PassengerID = :pid";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":pid", $passenger_id);
oci_execute($stmt);
$user = oci_fetch_assoc($stmt);

// If user not found (should not happen), redirect to login
if (!$user) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 20px; text-align: center; }
        h2 { margin-bottom: 20px; }
        table { width: 500px; margin: 0 auto; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; font-weight: bold; width: 40%; }
        td { width: 60%; }
        tr:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h2>User Profile</h2>
    <table>
        <tr>
            <th>Passenger ID</th>
            <td><?php echo htmlspecialchars($user['PASSENGERID']); ?></td>
        </tr>
        <tr>
            <th>First Name</th>
            <td><?php echo htmlspecialchars($user['FNAME']); ?></td>
        </tr>
        <tr>
            <th>Middle Name</th>
            <td><?php echo htmlspecialchars($user['MNAME']); ?></td>
        </tr>
        <tr>
            <th>Last Name</th>
            <td><?php echo htmlspecialchars($user['LNAME']); ?></td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td><?php echo htmlspecialchars($user['DOB']); ?></td>
        </tr>
        <tr>
            <th>Nationality</th>
            <td><?php echo htmlspecialchars($user['NATIONALITY']); ?></td>
        </tr>
        <tr>
            <th>Phone Number</th>
            <td><?php echo htmlspecialchars($user['PHONENUMBER']); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($user['EMAIL']); ?></td>
        </tr>
    </table>
</body>
</html>