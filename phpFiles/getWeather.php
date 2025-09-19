<?php

$script_path = "getLatLong.py";

$requestURL = "https://api.open-meteo.com/v1/forecast?latitude=52.52&longitude=13.41&current=temperature_2m,weather_code&timezone=America%2FNew_York&wind_speed_unit=mph&temperature_unit=fahrenheit&precipitation_unit=inch";

$lat = "";
$long = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{	$city_name = $_POST['city'];
	$lat = $_POST['lat'];
	$long = $_POST['long'];

	if(!empty($city_name))
	{
		$output = shell_exec("python3 $script_path \"$city_name\"");
		//echo "<p>Output of command: $output</p>";
		$split_string = explode(',',$output);
		$lat = $split_string[0];
		$long = $split_string[1];
		$long = substr($long,0,-1); //longitude get passed with a space?
		//echo "Lat: $lat, Long: $long";
	}
	else
	{
		echo "no city";
	}
}
else if($_SERVER["REQUEST_METHOD"] == "GET")
{
	die("<p>Don't GET request this page</p>");
}

$responseJSON = file_get_contents("https://api.open-meteo.com/v1/forecast?latitude=$lat&longitude=$long&current=temperature_2m,weather_code&timezone=America%2FNew_York&wind_speed_unit=mph&temperature_unit=fahrenheit&precipitation_unit=inch");

if($responseJSON  === false)
{
	echo "Could not retreive data.\nDid you enter correct lat and long?";
}
else
{
	$parsedJSON = json_decode($responseJSON);
	//TODO: add temerature sorting

	//Set this up to output formatted html with the temp, real feel,precipitation,
	//and obviously image

	$imagePath = GetClothesFromTemp($parsedJSON->current->temperature_2m);
	echo "Current temp: " .  $parsedJSON->current->temperature_2m . $parsedJSON->current_units->temperature_2m . "<br>Current Weather Code: " .  $parsedJSON->current->weather_code;
	echo "<style> body{ background-image: url('$imagePath'); background-size: cover; background-repeat: no-repeat;}</style>";

	//echo file_get_contents("../weatherBuddy.html");
}


/**
This is going to be set up for deg F by defualt, set a bool for deg C
**/
function GetClothesFromTemp($tempString): string
{
	//The idea is that clothes will depend on temp but also weather
	//Need a way to have weather on top of the temp
	//Don't really want to have an if statement for all weather types
	//Could also have it determine clothes based on what you're doing/going to
	//and projected precipitation forthe day

	//ALSO Factor in the apperant temp and display it
	$temp = (int)$tempString;

	if($temp > 100)
	{
		//TODO: replace this with a different one for super hot
		return "../images/CropTopShorts.png";
	}
	else if($temp >= 75)
	{
		return "../images/CropTopShorts.png";
	}
	else if($temp >= 70)
	{
		return "../images/ShirtPants.png";
	}

	else if($temp >= 65)
	{
		return "../images/SweatshirtShorts.png";
	}
	else //probably 64-60
	{
		//TOOD: Add more options
		return "../images/SweatshirtPants.png";
	}

}


?>
