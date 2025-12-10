<?php
// Reusable database connection file

$host = "prophet.njit.edu";
$port = "1521";
$sid  = "course";

$username = "csd36";
$password = 'Powertiger5$'; // single quotes if $ in password

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
