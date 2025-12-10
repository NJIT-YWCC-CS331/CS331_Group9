<?php
// Start session and include necessary files
session_start();
require 'db.php';  // Database connection
include 'admin_nav.php';  // Navigation bar with auth check

// Complex query to fetch all ticket information with related data
// Uses multiple JOINs to get passenger, flight, airport, airline, and payment details
$sql = "SELECT 
            T.TicketNumber,
            T.BookingDate,
            T.SeatNumber,
            T.Class,
            P.FName || ' ' || P.LName as PassengerName,  -- Concatenate passenger full name
            P.Email as PassengerEmail,
            F.FlightNumber,
            F.DepartureTime,
            F.ArrivalTime,
            A1.City as DepartureCity,  -- Join departure airport
            A2.City as ArrivalCity,     -- Join arrival airport
            AC.Name as AirlineName,
            PR.Amount as PaymentAmount,
            PR.Method as PaymentMethod
        FROM TICKET T
        JOIN PASSENGER P ON T.PassengerID = P.PassengerID          -- Get passenger info
        JOIN FLIGHT F ON T.FlightNumber = F.FlightNumber           -- Get flight info
        JOIN AIRPORT A1 ON F.DepartureAirport = A1.AirportCode     -- Get departure airport
        JOIN AIRPORT A2 ON F.ArrivalAirport = A2.AirportCode       -- Get arrival airport
        JOIN AIRLINE_COMPANY AC ON F.AirlineID = AC.AirlineID      -- Get airline info
        LEFT JOIN PAYMENT_RECORD PR ON T.TicketNumber = PR.TicketNumber  -- Payment may not exist yet
        ORDER BY T.BookingDate DESC, T.TicketNumber DESC";  

// Prepare and execute the query
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

// Fetch all tickets into an array for display
$tickets = [];
while ($row = oci_fetch_assoc($stmt)) {
    $tickets[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - All Tickets</title>
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
            max-width: 1600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .ticket-count {
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
            font-size: 14px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left;
        }
        th { 
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            position: sticky;
            top: 0;
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
        .ticket-number {
            font-weight: bold;
            color: #007bff;
        }
        .class-economy {
            background-color: #d4edda;
            padding: 3px 8px;
            border-radius: 3px;
            display: inline-block;
        }
        .class-business {
            background-color: #cce5ff;
            padding: 3px 8px;
            border-radius: 3px;
            display: inline-block;
        }
        .class-first {
            background-color: #fff3cd;
            padding: 3px 8px;
            border-radius: 3px;
            display: inline-block;
        }
        .amount {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>All Ticket Reservations</h1>
        
        <div class="ticket-count">
            <strong>Total Tickets: <?php echo count($tickets); ?></strong>
        </div>

        <?php if (count($tickets) > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Booking Date</th>
                            <th>Passenger</th>
                            <th>Email</th>
                            <th>Flight #</th>
                            <th>Route</th>
                            <th>Airline</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                            <th>Seat</th>
                            <th>Class</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td class="ticket-number"><?php echo htmlspecialchars($ticket['TICKETNUMBER']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['BOOKINGDATE']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['PASSENGERNAME']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['PASSENGEREMAIL']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['FLIGHTNUMBER']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['DEPARTURECITY']) . ' → ' . htmlspecialchars($ticket['ARRIVALCITY']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['AIRLINENAME']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['DEPARTURETIME']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['ARRIVALTIME']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['SEATNUMBER']); ?></td>
                                <td>
                                    <?php 
                                    $class = $ticket['CLASS'];
                                    $classStyle = '';
                                    if ($class == 'Economy') $classStyle = 'class-economy';
                                    elseif ($class == 'Business') $classStyle = 'class-business';
                                    elseif ($class == 'First Class') $classStyle = 'class-first';
                                    ?>
                                    <span class="<?php echo $classStyle; ?>"><?php echo htmlspecialchars($class); ?></span>
                                </td>
                                <td class="amount">
                                    <?php echo $ticket['PAYMENTAMOUNT'] ? '$' . number_format($ticket['PAYMENTAMOUNT'], 2) : 'N/A'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($ticket['PAYMENTMETHOD'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                No tickets found in the system.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
