<?PHP
/*
ini_set('max_execution_time', 300);

require_once("./include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}
*/
?>
<?PHP
ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");
$fgmembersite->LogOut();
if(!$fgmembersite->CheckLogin())
{
	if(!$fgmembersite->Login())
	   {
		   //echo "Authantication Error!";
		   //die;
			//$fgmembersite->RedirectToURL("login.php");
	  }
}

?>
<!doctype html>
<html>
<head>
<script language="Javascript" type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="./OpenLayers-2.13.1/OpenLayers.js"></script>
<!--<script src="http://www.openlayers.org/api/OpenLayers.js"></script>-->
<!--<script src="OpenLayers_2.10_mod.2.js"></script>-->
	<!--<script type="text/javascript" src="markerwithlabel.js" ></script>
<script type="text/javascript" src="infobox.js"></script>-->
<style>
.mapholder{
	overflow:hidden;
	padding-bottom: 0;
	left:0;
	right: 0;
	top:0;
	background-color:#F7F7F7;
	position:absolute;
	bottom:0;
	z-index:1;
}




</style>
<script type="text/javascript"  language="javascript">
<?php

echo "var lng = ".$_GET['lat'].";";
echo "var lat = ".$_GET['lng'].";";
?>

</script>

<script type="text/javascript"  language="javascript">
var map;
var Markers_layer;
var lineLayer;
var markers = new Array();
var points = new Array();
var isbusy = false;

var displayOffline = true;
var displayStopped = true;
var displayIdle = true;
var displayRunning = true;
var searchtextfixed = "";
var responseresult;

function initialize(){
		var MapBox = new OpenLayers.Layer.XYZ(
		  'MapBox',
			//['https://api.maptiler.com/maps/basic/256/${z}/${x}/${y}@2x.png?key=K51C4jphtYy1ouGPfn8b'],
		  //['https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/256/${z}/${x}/${y}?access_token=pk.eyJ1Ijoic2VuZXZpbmQiLCJhIjoiY2szNGpmdDhnMG1uazNjcDNoZXpuYjVzdSJ9.wQRVyRViK67Hr4lwlXg5EQ'],
		  //['http://203.189.65.202:8080/styles/klokantech-basic/${z}/${x}/${y}.png'],
		  'https://map.gsupertrack.com/apimapbox.php?provider=mapbox&type=streets-v11&z=${z}&x=${x}&y=${y}',
		  {
			sphericalMercator: true,
			wrapDateLine: true
		  }
		);	
			
			
		map = new OpenLayers.Map ("mapholder", {
		projection: new OpenLayers.Projection("EPSG:900913"),
		displayProjection: new OpenLayers.Projection("EPSG:4326")
		} ); 

		//map.addLayer(new OpenLayers.Layer.OSM());
		map.addLayer(MapBox);

	var lonLat2 = new OpenLayers.LonLat(lat,lng)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );

map.setCenter (lonLat2,13);
Markers_layer = new OpenLayers.Layer.Markers( "Markers_layer" );
map.addLayer(Markers_layer);
set_marker(1,"NB-0001",lat,lng);
}


function set_marker(sysno,detail,lat,lng){
	try{
			var lonLat = new OpenLayers.LonLat(lat,lng)
				  .transform(
					new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
					map.getProjectionObject() // to Spherical Mercator Projection
				  );
			var size = new OpenLayers.Size(22,37);
			var offset = new OpenLayers.Pixel(-(size.w/2), -(size.h));
			var icon = new OpenLayers.Icon('mapicon/point.png',	
					   size, offset);			   
			markers[sysno] = new OpenLayers.Marker(lonLat,icon);
			markers[sysno].icon.imageDiv.title = detail;


		markers[sysno].display(1);
		Markers_layer.addMarker(markers[sysno]);

	}catch(err){
		alert(err);
	}
}

</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>

<body onload="initialize()">
<!--map division-->

<div  id="mapholder" class="mapholder">
<!--<button onclick="myFunction()">Click me</button>-->
</div>

</body>


</html>