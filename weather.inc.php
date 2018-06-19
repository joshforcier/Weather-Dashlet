<?php

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

weather_dashlet_init();

function weather_dashlet_init() 
{	
	$name="Weather";
	
	$args=array(

		DASHLET_NAME => $name,
		
		DASHLET_VERSION => "1.1",
		DASHLET_DATE => "2018-06-13",
		DASHLET_AUTHOR => "Josh Forcier",
		DASHLET_DESCRIPTION => "Weather<BR>",
						
		DASHLET_FUNCTION => "weather_dashlet_func",
		
		DASHLET_TITLE => "Weather",
		
		DASHLET_OUTBOARD_CLASS => "weather_outboardclass",
		DASHLET_INBOARD_CLASS => "weather_inboardclass",
		DASHLET_PREVIEW_CLASS => "weather_previewclass",
		
		DASHLET_CSS_FILE => "",
	);

	register_dashlet($name,$args);
}

function weather_dashlet_func($mode=DASHLET_MODE_PREVIEW,$id="",$args=null) 
{
	$output="";

	$imgbase=get_dashlet_url_base("weather")."/images/";

	switch($mode){
	case DASHLET_MODE_GETCONFIGHTML:
		$output='
			<BR CLASS="nobr" />
			<LABEL FOR="zip">Please enter 5-digit ZIP code</LABEL>
			<BR CLASS="nobr" />
			<INPUT TYPE="number" NAME="zip" pattern="[0-9]{5}">
			<BR CLASS="nobr" />	
		';
		break;

	case DASHLET_MODE_OUTBOARD:
		break;

	case DASHLET_MODE_INBOARD:
		$zipCode = $args["zip"];
		$weatherAPI = "http://api.openweathermap.org/data/2.5/weather?zip=" . $zipCode . ",us&APPID=b0fd2dfdae3715ca115c9767204d9314";				
		$jsonWeather = file_get_contents($weatherAPI);
		$weatherData = json_decode($jsonWeather,true);

		$locationAPI = "http://api.zippopotam.us/us/" . $zipCode;
		$jsonLocation = file_get_contents($locationAPI);
		$locationData = json_decode($jsonLocation,true);
		$location = $locationData['places']['0']['place name'];

		$tempK = $weatherData['main']['temp'];
		$tempF = (round(($tempK - 273.15) * 1.8) + 32);
		$humidity = $weatherData['main']['humidity'];
		$windSpeed = $weatherData['wind']['speed'];
		$currentCond = $weatherData['weather']['0']['main'];


		echo '
		<table>
			<tr>
				<td width="110px">Location: </td>
				<td>' . $location . '</td>
			</tr>    	
			<tr>
				<td width="110px">Current Condition: </td>
				<td>' . $currentCond . '</td>
			</tr>
			<tr>
				<td>Tempurature:  </td>
				<td>' . $tempF . 'F</td>
			</tr>
			<tr>
				<td>Humidity: </td>
				<td>' . $humidity . '%</td>
			</tr>
			<tr>
				<td>Wind Speed: </td>
				<td>' . $windSpeed .'MPH</td>
			</tr> 
		</table>';

		break;

	case DASHLET_MODE_PREVIEW:
		$output="<p><img src='".$imgbase."weather.png'></p>";
		break;
	}
			
	return $output;
}
