<?php
require 'db.php'; // connect to Oracle
include 'nav.php';
$message = "";
$success = false;

// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname']);
    $lname = trim($_POST['lname']);
    $dob = trim($_POST['dob']);
    $nationality = trim($_POST['nationality']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    // Basic validation
    if (empty($fname) || empty($lname) || empty($dob) || empty($nationality) || empty($phone) || empty($email)) {
        $message = "Please fill in all required fields.";
    } else {
        // Generate PassengerID automatically
        $sql_id = "SELECT NVL(MAX(PassengerID), 0) + 1 AS NEW_ID FROM PASSENGER";
        $id_stmt = oci_parse($conn, $sql_id);
        oci_execute($id_stmt);
        $row = oci_fetch_assoc($id_stmt);
        $new_id = $row['NEW_ID'];

        // Prepare insert
        $sql = "INSERT INTO PASSENGER 
                (PassengerID, FName, MName, LName, DOB, Nationality, PhoneNumber, Email)
                VALUES (:id, :fn, :mn, :ln, TO_DATE(:dob, 'YYYY-MM-DD'), :nat, :phone, :email)";

        $stmt = oci_parse($conn, $sql);

        oci_bind_by_name($stmt, ":id", $new_id);
        oci_bind_by_name($stmt, ":fn", $fname);
        oci_bind_by_name($stmt, ":mn", $mname);
        oci_bind_by_name($stmt, ":ln", $lname);
        oci_bind_by_name($stmt, ":dob", $dob);
        oci_bind_by_name($stmt, ":nat", $nationality);
        oci_bind_by_name($stmt, ":phone", $phone);
        oci_bind_by_name($stmt, ":email", $email);

        $result = oci_execute($stmt);

        if ($result) {
            $success = true;
            $message = "Registration successful! Your Passenger ID is <b>$new_id</b>.";
        } else {
            $e = oci_error($stmt);
            $message = "Registration failed: " . htmlentities($e['message']);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 20px; text-align: center; }
        form { width: 350px; margin: 0 auto; text-align: center; }
        label{ display: block; margin-top: 10px; text-align: center; }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; }
        .message { margin-bottom: 20px; font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
        button { padding: 10px; width: 100%; cursor: pointer; }
    </style>
</head>
<body>

<h2>Create an Account</h2>

<div class="message <?php echo $success ? 'success' : 'error'; ?>">
    <?php echo $message; ?>
</div>

<form method="POST">
    <label>First Name *</label>
    <input type="text" name="fname" required>

    <label>Middle Initial</label>
    <input type="text" name="mname" maxlength="1">

    <label>Last Name *</label>
    <input type="text" name="lname" required>

    <label>Date of Birth *</label>
    <input type="date" name="dob" required>

    <label>Nationality *</label>
    <input type="text" name="nationality" required>

    <label>Phone Number *</label>
    <input type="text" name="phone" required>

    <label>Email *</label>
    <input type="email" name="email" required>

    <button type="submit">Register</button>
</form>

</body>
</html>
