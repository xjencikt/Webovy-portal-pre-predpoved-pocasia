<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['town_name'])) {
  // Get the town name entered by the user
  $town_name = urlencode($_POST['town_name']);

  // Make a request to the OpenCage geocoding API
  $api_key = '1fd0f36391004e618d7c875806519514';
  $url = "https://api.opencagedata.com/geocode/v1/json?q=$town_name&key=$api_key";
  $response = file_get_contents($url);

  // Parse the response and extract the latitude and longitude
  $data = json_decode($response, true);
  $latitude = $data['results'][0]['geometry']['lat'];
  $longitude = $data['results'][0]['geometry']['lng'];

  // Make a request to the OpenWeatherMap API
  $api_key = '130e86de8e552df32a477ab5301ca374';
  $url = "https://api.openweathermap.org/data/2.5/weather?lat=$latitude&lon=$longitude&appid=$api_key";
  $response = file_get_contents($url);

  // Parse the response and extract the relevant data
  $data = json_decode($response, true);
  $temp = $data['main']['temp'];
  $conditions = $data['weather'][0]['description'];

  // Display the weather data to the user
  echo "<h2>Weather in $town_name</h2>";
  echo "<p>Current temperature: " . round($temp - 273.15) . "&deg;C</p>";
  echo "<p>Conditions: $conditions</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">

    <title>Weather</title>
    <style>
    body {
        background-color: #e8f8f5;
        height: 100vh;
        margin: 0;
    }

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0,0,0,0.3);
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
        max-width: 400px; /* Added this */
        margin: 0 auto; /* Added this */
        margin-top: 15%;
    }

    label, input {
        margin: 10px;
    }

    button {
        margin-top: 20px;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Weather</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
            <li class="nav-item active">
                    <a class="nav-link" href="https://site112.webte.fei.stuba.sk/jedalne/zadanie-4/index.php">Form<span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://site112.webte.fei.stuba.sk/jedalne/zadanie-4/information.php">information</a>
                </li>
            </ul>
        </div>
    </nav>
    <form method="post" action="weather.php">
        <label for="town_name">Enter the town name:</label>
        <input type="text" name="town_name" id="town_name">
        <button type="submit">Get Weather</button>
    </form>
</body>
</html>
