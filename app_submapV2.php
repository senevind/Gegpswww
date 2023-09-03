
<?PHP
//print_r($_GET);


ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");


//$fgmembersite->LogOut();
if(!$fgmembersite->CheckLogin())
{
	if(!$fgmembersite->Login())
	   {
		   echo "Authantication Error!";
		   die;
			//$fgmembersite->RedirectToURL("login.php");
	  }
}

//$fgmembersite->getbuslistarraySysno("355227046173282");
//exit;
?>
<!doctype html>
<html>
<head>
	<link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
	<script type="text/javascript" src="Leaflet/MovingMarker.js"></script>
	

<style>	
	#mapid{
	height: 100%;
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
echo "var sysno = ".$_GET['objsysno'].";";
//echo "var lng = ".$_GET['lat'].";";
//echo "var lat = ".$_GET['lng'].";";

//echo "var lng = 0;";
//echo "var lat = 0;";
//echo "var sysno = '356646105166664'";
?>

</script>

</head>

<body>
<div id="mapid" ></div>
<script type="text/javascript"  language="javascript">
var map;
var VeMarkers = L.layerGroup();
var markers = new Array();
var infocontent = {};
var responseresult;
map = L.map('mapid').setView([7.293487,80.641021], 8);

	L.tileLayer(
	'https://map.gsupertrack.com/apimapbox.php?provider=mapbox&type=streets-v11&z={z}&x={x}&y={y}',
	{
		maxZoom: 20,
		id: 'mapbox/streets-v11'
	}).addTo(map);
	VeMarkers.addTo(map);
	loadvehiclelist();
	setInterval('loadvehiclelist()', 10000);
	
function SetMapIcon(Angle,Velocity,Status,sysno)
{
	var hazardIcon = L.icon({
		iconUrl: select_image(Angle,Velocity,Status,sysno),
		iconSize: [50, 50],
		iconAnchor: [25, 50],
		popupAnchor: [0, 0]
	});
	return hazardIcon;
}
	
	function loadvehiclelist(){

			var xmlhttp;
			if (window.XMLHttpRequest)
			  {// code for IE7+, Firefox, Chrome, Opera, Safari
			  xmlhttp=new XMLHttpRequest();
			  }
			else
			  {// code for IE6, IE5
			  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			  }
			xmlhttp.onreadystatechange=function()
			  {
			  if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
						isbusy = false;
					responseresult =JSON.parse(xmlhttp.responseText);

						if(!markers[responseresult.geocordinates[0].objsysno])
						{
							set_marker_array(responseresult.geocordinates[0].objsysno,responseresult.geocordinates[0].objname,responseresult.geocordinates[0].lastlati,responseresult.geocordinates[0].lastlng,responseresult.geocordinates[0].lastdirection,responseresult.geocordinates[0].lastvelosity,responseresult.geocordinates[0].imagefolder,responseresult.geocordinates[0].status);
						}else{
							MoveMarker(responseresult.geocordinates[0].objsysno,responseresult.geocordinates[0].objname,responseresult.geocordinates[0].lastlati,responseresult.geocordinates[0].lastlng,responseresult.geocordinates[0].lastdirection,responseresult.geocordinates[0].lastvelosity,responseresult.geocordinates[0].imagefolder,responseresult.geocordinates[0].status);
							//MoveMarker(responseresult.value[i].objsysno,responseresult.value[i].lastlati,responseresult.value[i].lastlng,responseresult.value[i].lastdirection,responseresult.value[i].lastvelosity,responseresult.value[i].status);
						}
					}
			  }
		xmlhttp.open("GET","getcoordinates.php?sysnostring="+sysno);
		xmlhttp.send();
	}
	
	function set_marker_array(sysno,detail,lat,lng,direction,velocity,imagefolder,Status){
		
	markers[sysno] = L.Marker.movingMarker([[lat,lng]],[30000],{id:sysno}).addTo(VeMarkers).bindPopup("",{offset: [0, -50]}).on('click', function(e) 
	{
		console.log(e.latlng);
		console.log(this.options.id);
		load_revgeoaddress(sysno,lat,lng);
	});

	MarkerSetIcon(markers[sysno],lat,lng,direction,velocity,Status,imagefolder);
	markers[sysno].bindPopup(setinfocontent(sysno,responseresult.geocordinates[0]));
	map.flyTo([lat,lng], 14);
	markers[sysno].openPopup();
	}
	
	function MoveMarker(sysno,detail,lat,lng,direction,velocity,imagefolder,Status){
	MarkerMove(markers[sysno],lat,lng,direction,velocity,Status,imagefolder);
	markers[sysno].bindPopup(setinfocontent(sysno,responseresult.geocordinates[0]));
	
	var popup = markers[sysno].getPopup();

		if(popup.isOpen())
		{
			load_revgeoaddress(sysno,lat,lng);
		}
	}

	function MarkerMove(marker,lat,lng,Angle,Velocity,Status,imagefolder)
	{
		MarkerSetIcon(marker,lat,lng,Angle,Velocity,Status,imagefolder);
		var newLatLng = new L.LatLng(lat,lng);
		//marker.setLatLng(newLatLng);
		marker.moveTo(newLatLng, 30000);
	}
	
	function MarkerSetIcon(marker,lat,lng,Angle,Velocity,Status,imagefolder)
	{
		marker.setIcon(SetMapIcon(Angle,Velocity,Status,imagefolder));
	}
	
	
function select_image(Angle,Velocity,Status,imagefolder){


		if(Status == 4){
			var image= 'mapicon/N'+imagefolder+'/4.png';
		}
		else if(Status == 3){
			var image= 'mapicon/N'+imagefolder+'/4.png';
		}
		else if(Status == 2){
			var image= 'mapicon/N'+imagefolder+'/3.png';
		}
		else if(Status == 1){
			var image= 'mapicon/N'+imagefolder+'/2.png';
		}
		else{
			var image= 'mapicon/N'+imagefolder+'/1.png';
		}	

return image;
}
	
function setinfocontent(objsysno,geocordinates)
{
    //var content = "<div id='infowindo' style='background-color: lightgreen;'><table border='0' style='font-family:Arial; font-size:12px;'><tr><th style='text-align:center'>"+Vehicleinfo.Vehicleinfosys[objsysno].objname+"</th></tr><tr><td><div>"+objsysno+"</div></td></tr><tr><td><div>Contact: "+Vehicleinfo.Vehicleinfosys[objsysno].sim+"</div></td></tr><tr><td><div>Speed: "+geocordinates.lastvelosity+"</div></td></tr><tr><td><div>Milage: "+geocordinates.lastmilage+"</div></td></tr><tr><td><div>Time: "+geocordinates.Time+"</div></td></tr></table></div>";
	var content = "<div style='width:400px;'><table cellpadding='1' cellspacing='1' class='table table-condensed table-striped'><thead><tr><th style='text-align:center'>"+geocordinates.objname+"</th></tr></thead><tbody><tr><td>Contact: "+geocordinates.simno+"</td></tr><tr><td>Speed: "+geocordinates.lastvelosity+"</td></tr><tr><td>Milage: "+geocordinates.lastmilage+"</td></tr><tr><td>Time: "+geocordinates.Time+"</td></tr><tr><td>Parking: "+geocordinates.parking+"</td></tr><tr><td colspan='2'><span id='popupinfo"+objsysno+"'><img src='/images/loading.gif' alt='Loading...' width='15' height='15'></span></td></tr></tbody></table></div>";	
	//infocontent[objsysno] = content; 
	return content;
}

function load_revgeoaddress(sysno,lat,lng){
		var xmlhttp;
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
					document.getElementById('popupinfo'+sysno).innerHTML = xmlhttp.responseText;
			    }
		  }
	xmlhttp.open("GET","getcoordinates.php?revgeocode=true&lat="+lat+"&lng="+lng);
	xmlhttp.send();
}
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</body>


</html>