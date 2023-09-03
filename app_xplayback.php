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
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

if(isset($_GET['username']))
{
	$_POST['username'] = $_GET['username'];
	$_POST['password'] = $_GET['password'];
}

ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");
//$fgmembersite->LogOut();
if(!$fgmembersite->CheckLogin())
{
	if(!$fgmembersite->Login())
	   {
		   echo "Authentication Error, Please re login!";
		   die;
			//$fgmembersite->RedirectToURL("login.php");
	  }
}

?>
<!doctype html>
<html>
<head>
<script src="./js/jquery-3.1.1.min.js" ></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
	<script type="text/javascript" src="Leaflet/antpath/leaflet-ant-path.js"></script>
	<script type="text/javascript" src="Leaflet/XplayDrawRouteSections.js"></script>

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAx0beF1k-G06wZez4T27V49kIsjKs5gac&language=ar&region=MA" async defer></script>
	<script type="module" src="Leaflet/GoogleMutant-master/src/Leaflet.GoogleMutant.js"></script>
<style>
.mapholder{
	overflow:hidden;
	padding-bottom: 0;
	left:0;
	right: 0;
	top:0px;
	background-color:#F7F7F7;
	position:absolute;
	bottom:0;
	z-index:1;
	//height:400px
}




</style>
<script type="text/javascript"  language="javascript">
var map;
var VeMarkers = L.layerGroup();
var markers = new Array();
var infocontent = {};
var responseresult = null;
var NotificResponseresult = null;
var objectnewestvalues = {};
var Geomarkers = new Array();
var Geopolygon = new Array();
var vehicle;
var StopMarkers = new Array();
var NotificMarkers = new Array();
var StopMarkerIndex = 0;
var NotificMarkerIndex = 0;


function initialize(){
	if(!map){
			//Google Maps
			var GoogleAerial = L.gridLayer.googleMutant({ type: 'satellite' }); 
			var GoogleRoad = L.gridLayer.googleMutant({ type: 'roadmap' }); 
			var GoogleTerrain = L.gridLayer.googleMutant({ type: 'terrain' }); 
			var GoogleHybrid = L.gridLayer.googleMutant({ type: 'hybrid' }); 
			
			map = L.map('mapholder').setView([52.40689760245717, -1.510605666657068], 8);

/*
				L.tileLayer(
				//'https://map.gsupertrack.com/apimapbox.php?provider=mapbox&type=streets-v11&z={z}&x={x}&y={y}',
				'https://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}',
				{
					id: 'mapbox/streets-v11'
				}).addTo(map);
*/
			//var GoogleAerial = L.gridLayer.googleMutant({ type: 'roadmap' }).addTo(map); 
			GoogleRoad.addTo(map);
			
			//layer control
			var baseMaps = {
			"Google Roadmap":GoogleRoad,
			"Google Aerial":GoogleAerial,
			"Google Terrain":GoogleTerrain,
			"Google Hybrid":GoogleHybrid,
			};
			
			L.control.layers(baseMaps,null,{position: 'topleft'}).addTo(map);
			
	}
	CreateVehicleMarker();
}

function CreateVehicleMarker()
{
	vehicle = L.marker([0,0]).addTo(map);
	vehicle.setIcon(SetMapIconVehicle());
}

function SetMapIconVehicle()
{
	return Vehicle = L.icon({
		iconUrl: 'mapicon/Playback/Xplayback.png',
		iconSize: [50, 50],
		iconAnchor: [25, 50],
		popupAnchor: [0, 0]
	});
}



function UpdateVehicleLocation(lat,lng)
{
    var newLatLng = new L.LatLng(lat, lng);
    vehicle.setLatLng(newLatLng); 
	map.panTo(newLatLng);
}

function webViewRefresh()
{
	removeStopMarkers();
	RemovePathStops();
}

function LoadDatatoWebview(date,username,password,vehicle)
{
	
	removeStopMarkers();
	removeNotificMarkers();
	StopMarkerIndex = 0;
	NotificMarkerIndex = 0;
	//StopMarkers = new Array();
	
	
	$.post( "app_source.php", { username: username, password: password, Xpassdata: "passdata", busno: vehicle, sttime: date+" 00:00:00:000", endtime: date+" 23:59:59:000" })
	  .done(function( data ) {
		  ShowRoutebyJson(data)
	});
	$.post( "app_source.php", { XpassdataNotific: "passdata", busno: vehicle, sttime: date+" 00:00:00:000", endtime: date+" 23:59:59:000" })
	  .done(function( data ) {
		  ShowNotificJson(data);
		  NotificResponseresult=data;
	});

}
function ShowNotificJson(jsondataNotific)
{
	var NotificArray = jsondataNotific.Notificinfo;
	CreateStopsMarkers(NotificArray);
	
}

function CreateStopsMarkers(NotificArray)
{
	NotificMarkerIndex = NotificArray.length;
	for(var i=0; i< NotificArray.length; i++){
		//console.log(NotificArray[i].long);
		//console.log(NotificArray[i].lat);
		//console.log(NotificArray[i].notificsubject);
		console.log(NotificArray[i].notificTime);
		
		NotificMarkers[i] = L.marker([NotificArray[i].lat,NotificArray[i].long]).bindPopup("<H4>"+NotificArray[i].notificTime+"</H4>"+"<H3>"+NotificArray[i].notificsubject+"</H3>"+'<img src="'+NotificArray[i].PicPath+'" alt="Girl in a jacket" width="200" height="220">').addTo(map);
		//NotificMarkers[i].setIcon(SetNotificMapIcon());
	}
}

function SetNotificMapIcon()
{
	return Park = L.icon({
		iconUrl: 'mapicon/Playback/Xplaybackpark.png',
		iconSize: [20, 20],
		iconAnchor: [10, 20],
		popupAnchor: [0, 0]
	});
}

function removeNotificMarkers()
{
	for(var i=0;i<NotificMarkerIndex;i++)
	{
		map.removeLayer(NotificMarkers[i]);
	}
}

function removeStopMarkers()
{
	for(var i=0;i<StopMarkerIndex;i++)
	{
		map.removeLayer(StopMarkers[i]);
	}
}

function myFunction1()
{
	LoadDatatoWebview("2020-10-21","94716144466","123","NC-6909");
}

function myFunctionRem()
{
	for(var i=0;i<StopMarkerIndex;i++)
	{
		map.removeLayer(StopMarkers[i]);
	}
}


</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>

<body onload="initialize()">
<!--<button onclick="myFunction1()">Click me</button>
<button onclick="myFunctionRem()">Remove Marker</button>
map division-->
<!--<button onclick="offline('checked')">Click me</button>-->
<div  id="mapholder" class="mapholder">

</div>

</body>


</html>