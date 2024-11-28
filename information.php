<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=
    , initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <title>Information</title>
    
</head>
<body>
  <style>
	<style>
  body {
			background-color: lightgreen;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100vh;
			margin: 0;
			padding: 0;
		}
		
		p {
			font-family: Arial, sans-serif;
			font-size: 16px;
			margin: 10px 0;
			text-align: center;
		}
		table {
			background-color: white;
			border-collapse: collapse;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
			font-family: Arial, sans-serif;
			margin: 0 auto;
      margin-top: 20px;
      margin-bottom: 30px;
		}
		
		th, td {
			border: 1px solid #ddd;
			padding: 8px;
			text-align: left;
		}
		
		th {
			background-color: #4CAF50;
			color: white;
		}
		
		img {
			height: 30px;
			width: 30px;
			cursor: pointer;
		}
	</style>
    </style>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Weather</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="https://site112.webte.fei.stuba.sk/jedalne/zadanie-4/index.php">Form</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://site112.webte.fei.stuba.sk/jedalne/zadanie-4/information.php">information<span class="sr-only"></span></a>
                </li>
                </li>
            </ul>
        </div>
    </nav>



<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('config.php');

$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Select the country name and count the number of rows with the same country name
$stmt = $conn->prepare("SELECT country, acronym, COUNT(*) AS count FROM connection GROUP BY country, acronym");

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Loop through the results and display them in a table
echo "<table>";
echo "<thead><tr><th>Flag</th><th>Name</th><th>Count</th></tr></thead>";
echo "<tbody>";
$current_country = '';
foreach ($results as $row) {
if ($row['country'] !== $current_country) {

$current_country = $row['country'];
}
echo "<tr><td><img src=https://www.countryflagicons.com/FLAT/64/{$row['acronym']}.png onclick='toggleTable()'></td><td>{$row['country']}</td><td>{$row['count']}</td></tr>";
}
echo "</tbody>";
echo "</table>";

$stmt = $conn->prepare("SELECT town, COUNT(*) AS count FROM connection GROUP BY town");
$stmt->execute();
$townResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<div id='town-table'>";
echo "<table >";
echo "<tr><th>Town</th><th>Count</th></tr>";
foreach ($townResults as $row) {
  echo "<tr><td>{$row['town']}</td><td>{$row['count']}</td></tr>";
}
echo "</table>";
echo "</div>";


  // Your database connection code here
  $sql = "SELECT latitude, longitude FROM connection";
  $stmt = $conn->query($sql);
  
  $locations = array();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $locations[] = $row;
  }



// Define time ranges
$time_ranges = array(
  array("06:00:00", "15:00:00"),  // 6:00-15:00
  array("15:00:00", "21:00:00"),  // 15:00-21:00
  array("21:00:00", "23:59:59"),  // 21:00-24:00
  array("00:00:00", "06:00:00")   // 24:00-6:00
);

// Loop through time ranges and count rows
foreach ($time_ranges as $range) {
  $sql = "SELECT COUNT(*) AS count FROM connection WHERE time >= '".$range[0]."' AND time < '".$range[1]."'";
  $result = $conn->query($sql);
  $row = $result->fetch();
  echo "<p>Connections between ".$range[0]." and ".$range[1].": ".$row['count']."</p>";
}

// Close MySQL connection
$conn = null;

?>

<div id="map" style="height: 500px;"></div>

</body>

<script>
function toggleTable() {
  var tableContainer = document.getElementById("town-table");
  if (tableContainer.style.display === "none") {
    tableContainer.style.display = "flex";
  } else {
    tableContainer.style.display = "none";
  }
}

 // Initialize the map using Leaflet
  var map = L.map('map').setView([48, 17], 5); // Replace with your own starting location and zoom level

  // Add a tile layer to the map
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Map data © <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
    maxZoom: 19
  }).addTo(map);

  // Loop through the locations and add a marker for each one
  <?php foreach ($locations as $location): ?>
    L.marker([<?php echo $location['latitude']; ?>, <?php echo $location['longitude']; ?>]).addTo(map);
  <?php endforeach; ?>
</script>
</html>