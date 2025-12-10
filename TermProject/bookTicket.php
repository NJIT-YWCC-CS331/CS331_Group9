<?php
session_start();
require 'db.php'; // connect to Oracle
include 'nav.php';

// Ensure user is logged in
if (!isset($_SESSION['PassengerID'])) {
    header("Location: login.php");
    exit();
}

$passenger_id = $_SESSION['PassengerID'];
$message = "";

// Must have a flight number in URL
if (!isset($_GET['flight'])) {
    $message = "No flight selected.";
} 

// Get Flight details
$flight_number = $_GET['flight'];

$sql_flight = "SELECT F.FlightNumber,
                      A1.City AS DepartureCity,
                      A2.City AS ArrivalCity,
                      F.DepartureTime,
                      F.ArrivalTime,
                      F.Duration,
                      AC.Name AS AirlineName
               FROM Flight F
               JOIN Airport A1 ON F.DepartureAirport = A1.AirportCode
               JOIN Airport A2 ON F.ArrivalAirport = A2.AirportCode
               JOIN Airline_Company AC ON F.AirlineID = AC.AirlineID
               WHERE F.FlightNumber = :flight_number";

$stmt_flight = oci_parse($conn, $sql_flight);
oci_bind_by_name($stmt_flight, ":flight_number", $flight_number);
oci_execute($stmt_flight);

$flight = oci_fetch_assoc($stmt_flight);

if(!$flight){
    $message = "Flight not found.";
}

if($_SERVER["REQUEST_METHOD"] == "POST" && $flight){
    // basic validation
    $seat = trim($_POST['seat']);
    $class = trim($_POST['class']);
    $booking_date = date('Y-m-d');

    if(empty($seat) || empty($class) || empty($booking_date)){
        $message = "Please fill in all required fields.";
    } else {
        // Generate new TickerNumber
        $sql_id = "SELECT NVL(MAX(TicketNumber), 0) + 1 AS NEW_ID FROM TICKET";
        $id_stmt = oci_parse($conn, $sql_id);
        oci_execute($id_stmt);
        $new_id_row = oci_fetch_assoc($id_stmt);
        $new_ticket_number = $new_id_row['NEW_ID'];

        // Insert ticket
        $sql_insert = "INSERT INTO TICKET
                       (TicketNumber, BookingDate, SeatNumber, Class, PassengerID, FlightNumber)
                       VALUES (:ticket_number, TO_DATE(:booking_date, 'YYYY-MM-DD'), :seat_number, :class, :passenger_id, :flight_number)";

        $stmt_insert = oci_parse($conn, $sql_insert);
        oci_bind_by_name($stmt_insert, ":ticket_number", $new_ticket_number);
        oci_bind_by_name($stmt_insert, ":booking_date", $booking_date); 
        oci_bind_by_name($stmt_insert, ":seat_number", $seat);
        oci_bind_by_name($stmt_insert, ":class", $class);
        oci_bind_by_name($stmt_insert, ":passenger_id", $passenger_id);
        oci_bind_by_name($stmt_insert, ":flight_number", $flight_number);

        $result = oci_execute($stmt_insert);

        if($result){
            $message = "Booking successful!";
        } else {
            $e = oci_error($stmt_insert);
            $message = "Booking failed: " . htmlentities($e['message']);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Ticket</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 20px; text-align: center; }
        form { width: 300px; margin: 0 auto; text-align: center; }
        label { display: block; margin-top: 10px; text-align: left; }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; }
        .message { margin-bottom: 20px; font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
        button { padding: 10px; width: 100%; cursor: pointer; }
    </style>
</head>
<body>

</body>
    <h2>Book Ticket</h2>

    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($flight): ?>
        <h3>Flight: <?php echo htmlspecialchars($flight['FLIGHTNUMBER']); ?> - <?php echo htmlspecialchars($flight['AIRLINENAME']); ?></h3>
        <p><?php echo htmlspecialchars($flight['DEPARTURECITY']); ?> (<?php echo htmlspecialchars($flight['DEPARTURETIME']); ?>) to <?php echo htmlspecialchars($flight['ARRIVALCITY']); ?> (<?php echo htmlspecialchars($flight['ARRIVALTIME']); ?>)</p>
        <form method="POST" action="">
            <label for="booking_date">Booking Date *</label>
            <input type="date" name="booking_date" required>

            <label for="seat">Seat Number *</label>
            <input type="text" name="seat" required>

            <label for="class">Class *</label>
            <select name="class" required>
                <option value="">Select Class</option>
                <option value="Economy">Economy</option>
                <option value="Business">Business</option>
                <option value="First">First</option>
            </select>

            <button type="submit">Book Ticket</button>
        </form>
    <?php endif; ?>
</html>

