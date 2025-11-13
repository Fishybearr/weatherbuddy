<?php

$script_path = "getLatLong.py";

/*$requestURL = "https://api.open-meteo.com/v1/forecast?latitude=52.52&longitude=13.41&daily=temperature_2m_max,temperature_2m_min&hourly=temperature_2m,precipitation_probability,weather_code&current=temperature_2m,apparent_temperature,weather_code&timezone=America%2FNew_York&forecast_days=1&wind_speed_unit=mph&temperature_unit=fahrenheit&precipitation_unit=inch";
*/
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

$responseJSON = file_get_contents("https://api.open-meteo.com/v1/forecast?latitude=$lat&longitude=$long&daily=temperature_2m_max,temperature_2m_min&hourly=temperature_2m,precipitation_probability,weather_code&current=temperature_2m,apparent_temperature,weather_code&timezone=America%2FNew_York&forecast_days=1&wind_speed_unit=mph&temperature_unit=fahrenheit&precipitation_unit=inch");

if($responseJSON  === false)
{
	echo "Could not retreive data.\nDid you enter correct lat and long?";
}
else
{
	$parsedJSON = json_decode($responseJSON,true);
	//echo json_encode($parsedJSON);
	//TODO: add temerature sorting

	//Set this up to output formatted html with the temp, real feel,precipitation,
	//and obviously image

	//$imagePath = GetClothesFromTemp($parsedJSON->current->temperature_2m);
	//echo "Current temp: " .  $parsedJSON->current->temperature_2m . $parsedJSON->current_units->temperature_2m . "<br>Current Weather Code: " .  $parsedJSON->current->weather_code;
	//echo "<style> body{ background-image: url('$imagePath')}</style> <img src=\"$imagePath\">";


/*

This is where all data taken from api call will be assigned to variables

*/
$tempUnit = $parsedJSON['current_units']['temperature_2m'];
$currTemp = $parsedJSON['current']['temperature_2m'];

$dailyHigh =$parsedJSON['daily']['temperature_2m_max'][0];
$dailyLow =$parsedJSON['daily']['temperature_2m_min'][0];

$hourlyTemps = array_fill(0,23,0);

$hourlyPrecip = array_fill(0,23,0);

for($i = 0; $i <= 23; $i++)
	{
		$hourlyTemps[$i] = $parsedJSON['hourly']['temperature_2m'][$i];
		//echo("Temp: " . $hourlyTemps[$i]);

		$hourlyPrecip[$i] = $parsedJSON['hourly']['precipitation_probability'][$i];
		echo($hourlyPrecip[$i]); //TODO: This needs to get handled and used instead of just printing 
	}


$imagePath = GetClothesFromTemp($currTemp);


$htmlBlock = file_get_contents('../RenderPage.html');
//|IMAGE_PATH|

$modifiedHTML = str_replace('|IMAGE_PATH|',$imagePath,$htmlBlock);
$modifiedHTML = str_replace('Current Temp:',"Current Temp: $currTemp $tempUnit",$modifiedHTML);
$modifiedHTML = str_replace('|HL|', "High: $dailyHigh $tempUnit Low: $dailyLow $tempUnit",$modifiedHTML);

for($i = 5; $i <= 20; $i++) //Some kind of bug here where the am/pm is not right for most of the hours
	{
		if($i < 13)
			{
				$modifiedHTML = str_replace((string)($i-1) . ":00",(string)($i-1) . ":00am " . (string)($hourlyTemps[$i] . (string)$tempUnit),$modifiedHTML);
			}
		else if ($i == 13)
			{
				$modifiedHTML = str_replace((string)($i-1) . ":00",(string)($i-1) . ":00pm " . (string)($hourlyTemps[$i] . (string)$tempUnit),$modifiedHTML);
			}
		else
			{
				$modifiedHTML = str_replace((string)($i-1) . "p:00",(string)($i-13) . ":00pm " . (string)($hourlyTemps[$i]) . (string)$tempUnit,$modifiedHTML);
			}
		
	}

echo $modifiedHTML;

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
		return "../images/nobg/cropShorts.png";
	}
	else if($temp >= 70)
	{
		return "../images/nobg/tShirtPants.png";
	}

	else if($temp >= 65)
	{
		return "../images/nobg/sweatshirtShorts.png";
	}
	else //probably 64-60
	{
		//TOOD: Add more options
		return "../images/nobg/sweatshirtPants.png";
	}

}


?>
