<?php
session_start();
require 'db.php'; // connect to Oracle
include 'nav.php';
$message = "";
$success = false;

// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $dob = trim($_POST['dob']);
    $email = trim($_POST['email']);

    // Basic validation
    if (empty($dob) || empty($email)) {
        $message = "Please fill in all required fields.";
    } else {
        // SQL to verify user/login
        $sql = "SELECT PassengerID, FName, LName
        FROM PASSENGER 
        WHERE DOB = TO_DATE(:dob, 'YYYY-MM-DD') 
        AND Email = :email";

        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ":dob", $dob);
        oci_bind_by_name($stmt, ":email", $email);

        $result = oci_execute($stmt);

        if ($result) {
            $row = oci_fetch_assoc($stmt);
            
            if ($row) {
                // User found - login successful
                $_SESSION['PassengerID'] = $row['PASSENGERID'];
                $_SESSION['FName'] = $row['FNAME'];
                $_SESSION['LName'] = $row['LNAME'];
                
                $success = true;
                $message = "Login successful!";
                header("refresh:2; url=profile.php");
            } else {
                // No matching user found
                $message = "Invalid email or date of birth.";
            }
        } else {
            $e = oci_error($stmt);
            $message = "Login failed: " . htmlentities($e['message']);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 20px; text-align: center; }
        form { width: 300px; margin: 0 auto; text-align: center; }
        label{ display: block; margin-top: 10px; text-align: center; }
        input { width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; }
        .message { margin-bottom: 20px; font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
        button { padding: 10px; width: 100%; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Login</h2>
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="">
        <label>Email *</label>
        <input type="email" name="email" required>

        <label>Date of Birth *</label>
        <input type="date" name="dob" required>

        <button type="submit">Login</button>
    </form>
</body>
</html>