<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['town_name'])) {
  // Get the town name entered by the user
  $town_name = urlencode($_POST['town_name']);

  // Make a request to the OpenCage geocoding API to get the latitude and longitude
  $opencage_api_key = '1fd0f36391004e618d7c875806519514';
  $opencage_url = "https://api.opencagedata.com/geocode/v1/json?q=$town_name&key=$opencage_api_key";
  $opencage_response = file_get_contents($opencage_url);

  // Parse the response and extract the latitude and longitude
  $opencage_data = json_decode($opencage_response, true);
  $latitude = $opencage_data['results'][0]['geometry']['lat'];
  $longitude = $opencage_data['results'][0]['geometry']['lng'];

  // Make a request to the OpenWeatherMap API to get the weather information
  $openweathermap_api_key = '130e86de8e552df32a477ab5301ca374';
  $openweathermap_url = "https://api.openweathermap.org/data/2.5/weather?lat=$latitude&lon=$longitude&appid=$openweathermap_api_key";
  $openweathermap_response = file_get_contents($openweathermap_url);

  // Parse the response and extract the relevant weather data
  $openweathermap_data = json_decode($openweathermap_response, true);
  $temp = $openweathermap_data['main']['temp'];
  $conditions = $openweathermap_data['weather'][0]['description'];

  // Make a request to the OpenCage geocoding API to get additional location information
  $opencage_url = "https://api.opencagedata.com/geocode/v1/json?q=$latitude+$longitude&key=$opencage_api_key";
  $opencage_response = file_get_contents($opencage_url);

// Make a request to the GeoNames API to get additional location information
$geonames_api_key = 'fyynd';
$geonames_url = "http://api.geonames.org/findNearbyPlaceNameJSON?lat=$latitude&lng=$longitude&username=$geonames_api_key";
$geonames_response = file_get_contents($geonames_url);

// Parse the response and extract the town's country and capital city
$geonames_data = json_decode($geonames_response, true);
$state = $geonames_data['geonames'][0]['countryName'];
if($state == "United States"){
    $state = "USA";
}

// Define the API endpoint and parameters
$opencage_api_key = '1fd0f36391004e618d7c875806519514';
$opencage_url = "https://api.opencagedata.com/geocode/v1/json?q=$town_name&key=$opencage_api_key";

// Call the API and parse the response
$opencage_response = file_get_contents($opencage_url);
$opencage_data = json_decode($opencage_response, true);

// Extract the latitude and longitude coordinates from the response
$latitude = $opencage_data['results'][0]['geometry']['lat'];
$longitude = $opencage_data['results'][0]['geometry']['lng'];

$url = "https://restcountries.com/v3.1/name/$state";
$data = file_get_contents($url);
$data = json_decode($data, true);
$capital_city = $data[0]['capital'][0];
$acronym = $data[0]['cca2'];

}

date_default_timezone_set('Europe/Bratislava');

$now = date('H:i:s');

require_once('config.php');

$ipAddress = file_get_contents('https://api.ipify.org');



try {
  $conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  

  /*
  // Modify the SQL statement to include the columns and bind the parameters
  $stmt = $conn->prepare("INSERT INTO connection (country, time, IP, acronym, town, latitude, longitude) VALUES (:country, :time, :ip, :acronym, :town, :latitude, :longitude)");
  
  
  $stmt->bindParam(':country', $state);
  $stmt->bindParam(':time', $now);
  $stmt->bindParam(':ip', $ipAddress);
  $stmt->bindParam(':acronym', $acronym);
  $stmt->bindParam(':town', $town_name);
  $stmt->bindParam(':latitude', $latitude);
  $stmt->bindParam(':longitude', $longitude);

*/

  // Modify the SQL statement to include the columns and bind the parameters
  $stmt = $conn->prepare("INSERT INTO connection (country, time, IP, acronym, town, latitude, longitude) SELECT :country, :time, :ip, :acronym, :town, :latitude, :longitude
  FROM dual WHERE NOT EXISTS (SELECT IP FROM connection WHERE IP = :ip)");
  
  $stmt->bindParam(':country', $state);
  $stmt->bindParam(':time', $now);
  $stmt->bindParam(':ip', $ipAddress);
  $stmt->bindParam(':acronym', $acronym);
  $stmt->bindParam(':town', $town_name);
  $stmt->bindParam(':latitude', $latitude);
  $stmt->bindParam(':longitude', $longitude);
  /*
  $stmt = $conn->prepare("INSERT INTO connection (country, time, IP, acronym, town, latitude, longitude) SELECT :country, :time, :ip, :acronym, :town, :latitude, :longitude
  FROM dual WHERE NOT EXISTS (SELECT IP FROM connection WHERE IP = :ip)");
$stmt->bindParam(':country', $state);
$stmt->bindParam(':time', $now);
$stmt->bindParam(':ip', $ipAddress);
$stmt->execute();
  */

  // Execute the SQL statement
  $stmt->execute();
   
} catch(PDOException $e) {
    echo "Error occured: " . $e->getMessage();
}
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

  <title>Weather Information</title>
  <style>
    body {
      background-color: #e8f8f5;
      height: 100vh;
    }
    .container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      background-color: white;
      padding: 20px;
      box-shadow: 0px 0px 10px rgba(0,0,0,0.3);
      margin-top: 7%;
    }
    h2, p {
      color: #336633;
      font-size: 20px;
      font-family: Arial, sans-serif;
      margin: 10px 0;
    }
    h2 {
      font-size: 28px;
      text-align: center;
    }
  </style>
</head>

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
                    <a class="nav-link" href="https://site112.webte.fei.stuba.sk/jedalne/zadanie-4/information.php">information</a>
                </li>
            </ul>
        </div>
    </nav>

  <div class="container">
    <h2>Weather Information for <?php echo $town_name; ?></h2>
    <p>Latitude: <?php echo $latitude; ?></p>
    <p>Longitude: <?php echo $longitude; ?></p>
    <p>State: <?php echo $state; ?></p>
    <p>Capital City: <?php echo $capital_city; ?></p>
    <p>Current temperature: <?php echo round($temp - 273.15); ?>&deg;C</p>
    <p>Conditions: <?php echo $conditions; ?></p>
  </div>
</body>
</html>

