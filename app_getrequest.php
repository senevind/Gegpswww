<?php
//ini_set('allow_url_fopen', '1');
error_reporting(0);
try{
$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$_GET['latlng']."&key=AIzaSyDt4jojupu3t4ZrXFvvjPxO0RdjMh2ES_s";
//$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$_GET['latlng'];
$result = file_get_contents($url);
//echo $url;
$obj = json_decode($result);
//echo $result;
echo $obj->{'results'}[0]->{'formatted_address'};
}catch(Exception $e){
	echo "Adress not found";
}
?>