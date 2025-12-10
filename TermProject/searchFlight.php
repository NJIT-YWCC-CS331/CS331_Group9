<?php
session_start();

require 'db.php'; // connect to Oracle
include 'nav.php';
$message = "";
$success = false;

// Fetch airport list
$airportQuery = "SELECT AirportCode, City
                 FROM AIRPORT 
                 ORDER BY City";

$airportStmt = oci_parse($conn, $airportQuery);
oci_execute($airportStmt);

$airports = [];
while ($row = oci_fetch_assoc($airportStmt)) {
    $airports[] = $row;
}

$results = [];
// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $depart = $_POST['depart'];
    $arrive = $_POST['arrive'];

    $searched = true;

    $sql = "SELECT F.FlightNumber, 
                F.DepartureTime, 
                F.ArrivalTime, 
                F.Duration,
                A1.City as DepartureCity,
                A2.City as ArrivalCity,
                AC.Name as AirlineName
            FROM Flight F
            JOIN Airport A1 ON F.DepartureAirport = A1.AirportCode
            JOIN Airport A2 ON F.ArrivalAirport = A2.AirportCode
            JOIN Airline_Company AC ON F.AirlineID = AC.AirlineID
            WHERE 1=1";

    if (!empty($depart)) {
        $sql .= " AND F.DepartureAirport = :depart";
    }


    if (!empty($arrive)) {
        $sql .= " AND F.ArrivalAirport = :arrive";
       }

    $stmt = oci_parse($conn, $sql);

    if (!empty($depart)) {
        oci_bind_by_name($stmt, ":depart", $depart);
    }
    if (!empty($arrive)) {
        oci_bind_by_name($stmt, ":arrive", $arrive);
    }
    oci_execute($stmt);

    while ($row = oci_fetch_assoc($stmt)) {
        $results[] = $row;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Flights</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 20px; text-align: center; }
        form { margin-bottom: 30px; }
        select { padding: 8px; margin-right: 10px; }
        button { padding: 8px 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
        tr:hover { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Search Flights</h1>
    <form method="POST" action="">
        <label for="depart">Departure Airport:</label>
        <select name="depart" id="depart">
            <option value="">Select Departure Airport</option>
            <?php foreach ($airports as $airport): ?>
                <option value="<?php echo htmlspecialchars($airport['AIRPORTCODE']); ?>">
                    <?php echo htmlspecialchars($airport['CITY']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="arrive">Arrival Airport:</label>
        <select name="arrive" id="arrive">
            <option value="">Select Arrival Airport</option>
            <?php foreach ($airports as $airport): ?>
                <option value="<?php echo htmlspecialchars($airport['AIRPORTCODE']); ?>">
                    <?php echo htmlspecialchars($airport['CITY']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Search</button>
    </form>

    <?php if (!empty($results)): ?>
        <table>
            <thead>
                <tr>
                    <th>Flight Number</th>
                    <th>Departure Time</th>
                    <th>Arrival Time</th>
                    <th>Duration</th>
                    <th>Departure City</th>
                    <th>Arrival City</th>
                    <th>Airline Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $flight): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($flight['FLIGHTNUMBER']); ?></td>
                        <td><?php echo htmlspecialchars($flight['DEPARTURETIME']); ?></td>
                        <td><?php echo htmlspecialchars($flight['ARRIVALTIME']); ?></td>
                        <td><?php echo htmlspecialchars($flight['DURATION']); ?></td>
                        <td><?php echo htmlspecialchars($flight['DEPARTURECITY']); ?></td>
                        <td><?php echo htmlspecialchars($flight['ARRIVALCITY']); ?></td>
                        <td><?php echo htmlspecialchars($flight['AIRLINENAME']); ?></td>
                        <td>
                            <a href="bookTicket.php?flight=<?php echo urlencode($flight['FLIGHTNUMBER']); ?>" 
                               style="text-decoration: none; color: #007bff; font-weight: bold; text-decoration: underline;">Book</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <p>No flights found for the selected criteria.</p>
    <?php endif; ?>
</body>
</html>