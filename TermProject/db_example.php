<?php
// Example database connection file for Oracle
// don't want to leak our actual db.php credentials
// so this is how your db.php should look

$host = "your-oracle-host.domain.com"; // e.g., prophet.njit.edu
$port = "1521";
$sid  = "sid"; // e.g., course

$username = "username";   // ucid
$password = 'password'; // your actual password

$tns = "
(DESCRIPTION =
    (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))
    (CONNECT_DATA = (SID = $sid))
)
";

$conn = oci_connect($username, $password, $tns);

if (!$conn) {
    $e = oci_error();
    die("Database connection failed: " . htmlentities($e['message']));
}
?>
