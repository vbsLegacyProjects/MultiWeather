<?php 
$weather_api_list=array(
	 'forecastio' 		 => array(
	 		 // https://developer.forecast.io/
			 'key'		 => '3a3c60d8'
			,'latitude'	 => '17.1556'
			,'longitude' => '17.5914'
			,'delay'	 => 10*60
			,'use'		 => true
			)
	,'openweather' 		 => array(
	         // two requests per update - current and forecast
	         // http://openweathermap.org/api
	         // use city name or latitude and longitude
	         // city name will be used if all 3 fields are filled in
			 'key'		 => 'ccf359c274'
			,'cityname'  => 'london,uk'
			,'latitude'	 => '17.1556'
			,'longitude' => '17.5914'
			,'delay'	 => 10*60
			,'use'		 => true
			)
	,'worldweatheronline'=> array(
			 // no forecast
	 		 // http://developer.worldweatheronline.com/io-docs
			 'key'		 => 'nh8r'
			,'q'		 => 'Iasi'
			,'delay'	 => 10*60
			,'use'		 => true
			)
	,'hamweather'		 => array(
			 // no forecast
			 // http://www.hamweather.com/support/documentation/aeris/
			 'location'	 => 'london,uk'
			,'id'		 => 'zjS58'
			,'secret'	 => 'pu874'
			,'delay'	 => 10*60
			,'use'		 => true
			)
	,'wunderground'		 => array(
	         // two requests per update - current and forecast
			 'key'		 => 'f95'
			,'location'	 => 'London'
			,'delay'	 => 10*60
			,'use'		 => true 
			)
);

// Delay between two checks, in seconds
$main_delay=5;

// Number of loops, use null for infinite or a number for limited run
// Use $loops=null; when the script is ran as a server
// Use $loops=1 when the script is ran from crontab
// $loops=1; or $loops=null;
$loops=1;

// Storage method
// echo - displays on screen
// file - writes to 'output.txt'
$storage_class='echo';
