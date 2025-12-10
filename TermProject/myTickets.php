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
$tickets = [];

// SQL to fetch user's tickets
$sql = "SELECT T.TicketNumber,
               TO_CHAR(T.BookingDate, 'YYYY-MM-DD') AS BookingDate,
               T.SeatNumber,
               T.Class,
               F.FlightNumber,
               AC.Name AS AirlineName,
               A1.City AS DepartureCity,
               A2.City AS ArrivalCity,
               TO_CHAR(F.DepartureTime, 'YYYY-MM-DD HH24:MI') AS DepartureTime,
               TO_CHAR(F.ArrivalTime, 'YYYY-MM-DD HH24:MI') AS ArrivalTime,
               F.Duration,
               PR.Amount AS PaymentAmount,
               PR.Method AS PaymentMethod,
               CR.UpdatedStatus AS UpdatedStatus,
               TO_CHAR(CR.ChangeDate, 'YYYY-MM-DD') AS ChangeDate
        FROM TICKET T
        JOIN Flight F ON T.FlightNumber = F.FlightNumber
        JOIN Airline_Company AC ON F.AirlineID = AC.AirlineID
        JOIN Airport A1 ON F.DepartureAirport = A1.AirportCode
        JOIN Airport A2 ON F.ArrivalAirport = A2.AirportCode
        LEFT JOIN PAYMENT_RECORD PR ON T.TicketNumber = PR.TicketNumber
        LEFT JOIN CANCELLATION_RESCHEDULE CR ON T.TicketNumber = CR.TicketNumber
        WHERE T.PassengerID = :pid
        ORDER BY T.TicketNumber";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":pid", $passenger_id);
oci_execute($stmt);

while ($row = oci_fetch_assoc($stmt)) {
    $tickets[] = $row;
}

?>  

<!DOCTYPE html>
<html>
<head>
    <title>My Tickets</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 20px; text-align: center; }
        h2 { margin-bottom: 20px; }
        table { width: 90%; margin: 0 auto; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; font-weight: bold; }
        tr:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h2>My Tickets: PassengerID - <?php echo htmlspecialchars($_SESSION['PassengerID']); ?></h2>
    <?php if (empty($tickets)): ?>
        <p>You have no tickets booked.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Ticket Number</th>
                <th>Booking Date</th>
                <th>Flight Number</th>
                <th>Airline</th>
                <th>Departure City</th>
                <th>Arrival City</th>
                <th>Departure Time</th>
                <th>Arrival Time</th>
                <th>Duration</th>
                <th>Seat Number</th>
                <th>Class</th>
                <th>Payment Amount</th>
                <th>Updated Status</th>
                <th>Change Date</th>
            </tr>
            <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><?php echo htmlspecialchars($ticket['TICKETNUMBER']); ?></td>
                <td><?php echo htmlspecialchars($ticket['BOOKINGDATE']); ?></td>
                <td><?php echo htmlspecialchars($ticket['FLIGHTNUMBER']); ?></td>
                <td><?php echo htmlspecialchars($ticket['AIRLINENAME']); ?></td>
                <td><?php echo htmlspecialchars($ticket['DEPARTURECITY']); ?></td>
                <td><?php echo htmlspecialchars($ticket['ARRIVALCITY']); ?></td>
                <td><?php echo htmlspecialchars($ticket['DEPARTURETIME']); ?></td>
                <td><?php echo htmlspecialchars($ticket['ARRIVALTIME']); ?></td>
                <td><?php echo htmlspecialchars($ticket['DURATION']); ?></td>
                <td><?php echo htmlspecialchars($ticket['SEATNUMBER']); ?></td>
                <td><?php echo htmlspecialchars($ticket['CLASS']); ?></td>
                
                <!-- Payment -->
                <td>
                    <?php if ($ticket['PAYMENTAMOUNT']): ?>
                        $<?= number_format($ticket['PAYMENTAMOUNT'], 2) ?><br>
                        (<?= htmlspecialchars($ticket['PAYMENTMETHOD']) ?>)
                    <?php else: ?>
                        <i>Not Paid</i>
                    <?php endif; ?>
                </td>
                <!-- Change Status -->
                <td>
                    <?= $ticket['UPDATEDSTATUS'] ? htmlspecialchars($ticket['UPDATEDSTATUS']) : "<i>Active</i>" ?>
                </td>
                <!-- Change Date -->
                <td><?php if ($ticket['CHANGEDATE']): echo htmlspecialchars($ticket['CHANGEDATE']); else: echo "<i>N/A</i>"; endif; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>