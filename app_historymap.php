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
if(!$fgmembersite->CheckLogin())
{
	if(!$fgmembersite->Login())
	   {
		   echo "Authantication Error";
		   die;
			//$fgmembersite->RedirectToURL("login.php");
	  }
}

?>
<!doctype html>
<html>
<head>
<script> var Vehicleinfo =  <?= $fgmembersite->getbuslistarray(); ?> </script>
<script language="Javascript" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<!--<script src="./OpenLayers-2.13.1/OpenLayers.js"></script>-->
<script src="http://www.openlayers.org/api/OpenLayers.js"></script>
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


function initialize(){	    	
map = new OpenLayers.Map ("mapholder", {
projection: new OpenLayers.Projection("EPSG:900913"),
displayProjection: new OpenLayers.Projection("EPSG:4326")
} ); 
  
map.addLayer(new OpenLayers.Layer.OSM());

	var lonLat2 = new OpenLayers.LonLat(80.641021,7.293487)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );

map.setCenter (lonLat2,10);

Markers_layer = new OpenLayers.Layer.Markers( "Markers_layer" );
map.addLayer(Markers_layer);

lineLayer = new OpenLayers.Layer.Vector("line_layer");
map.addLayer(lineLayer);   

Markers_layer = new OpenLayers.Layer.Markers( "Markers_layer" );
map.addLayer(Markers_layer);
load_markers();

marker_refresh();
setInterval('marker_refresh()', 45000);
}

function load_markers(){
		for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
			if(Vehicleinfo.Vehicleinfo[i].lastlati !=null && Vehicleinfo.Vehicleinfo[i].lastlati != ""){
				set_marker_array(Vehicleinfo.Vehicleinfo[i].objsysno,Vehicleinfo.Vehicleinfo[i].objname,Vehicleinfo.Vehicleinfo[i].lastlati,Vehicleinfo.Vehicleinfo[i].lastlng,Vehicleinfo.Vehicleinfo[i].lastdirection,Vehicleinfo.Vehicleinfo[i].lastvelosity);
			}
		}
}

function set_marker_array(sysno,detail,lat,lng,direction,velocity){
try{
	var lonLat = new OpenLayers.LonLat(7.293487,80.641021)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
    var size = new OpenLayers.Size(37,37);
    var offset = new OpenLayers.Pixel(-(size.w/2), -(size.h/2));
    var icon = new OpenLayers.Icon('mapicon/empty.png',	
               size, offset);			   
	markers[sysno] = new OpenLayers.Marker(lonLat,icon);
	markers[sysno].icon.imageDiv.title = detail;


markers[sysno].events.register("touchstart", markers[sysno], function(e){					// Info window initialize
marker_popup_open(sysno,detail);
});
markers[sysno].display(1);
Markers_layer.addMarker(markers[sysno]);
}catch(err){
	alert(err);
}
}

function marker_popup_open(sysno,detail){
	/*
	for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
		if(responseresult.geocordinates[i].objsysno == sysno)
		{
			Android.showToast(responseresult.geocordinates[i].status);
			Android.showToast(sysno);
			//markers[responseresult.geocordinates[i].objsysno].display(onoff);
		}
	}getspeed
	*/
	Android.showToast(detail+"\nSpeed:"+getspeed(sysno));
}

function offline(status)
{
	//Android.showToast(status);
	if(status=='checked')
	{
		displayOffline = true;
		display(0,1);
	}else{
		displayOffline = false;
		display(0,0);
	}
}

function stopped(status)
{
	//Android.showToast(status);
	if(status=='checked')
	{
		displayStopped = true;
		display(1,1);
	}else{
		displayStopped = false;
		display(1,0);
	}
}

function idle(status)
{
	//Android.showToast(status);
	if(status=='checked')
	{
		displayIdle = true;
		display("2",1);
	}else{
		displayIdle = false;
		display("2",0);
	}
}

function Running(status)
{
	//Android.showToast(status);
	if(status=='checked')
	{
		displayRunning = true;
		display("4",1);
		display("3",1);
	}else{
		displayRunning = false;
		display("4",0);
		display("3",0);
	}
}

function display(displaystatus,onoff)
{
	var j=0;
	//Android.showToast("Vehicle count "+Vehicleinfo.Vehicleinfo.length);
	for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
		try{
		if(responseresult.geocordinates[i].status == displaystatus)
		{
			j++;
			markers[responseresult.geocordinates[i].objsysno].display(onoff);
			//Android.showToast(responseresult.geocordinates[i].objsysno+"--"+status);
		}
		}catch(err)
		{
			//alert(responseresult.geocordinates[i].objsysno);
		}
	}
	//Android.showToast("loop count "+j);
}

function marker_refresh(){
	var sysnostring = "";
		for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
			if(Vehicleinfo.Vehicleinfo[i].lastlati !=null || Vehicleinfo.Vehicleinfo[i].lastlati != ""){
				//loadvehiclelist(Vehicleinfo.Vehicleinfo[i].objsysno);
				sysnostring += ";"+Vehicleinfo.Vehicleinfo[i].objsysno;
			}
		}
		if(!isbusy){
			loadvehiclelist(sysnostring);
		}
}

function loadvehiclelist(sysno){
	isbusy = true;
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
				//alert(responseresult.geocordinates.length);
				
				for(var i=0; i< responseresult.geocordinates.length; i++){
					//alert(responseresult.geocordinates[i].objsysno);
					if(responseresult.geocordinates[i] != null){
					markers_set_position(responseresult.geocordinates[i],responseresult.geocordinates[i].objsysno);
					}
				}
				}
		  }
	xmlhttp.open("GET","getcoordinates.php?sysnostring="+sysno);
	xmlhttp.send();
}

function markers_set_position(updatedresult,sysno){
	//alert(responseresult.geocordinates[0].lastdirection);
MoveMarker(sysno,updatedresult.lastlati,updatedresult.lastlng,updatedresult.lastdirection,updatedresult.lastvelosity,updatedresult.status);

if(searchtextfixed == "")
{
	//show_all_markers();


if(displayOffline)
{
	try{
		if(updatedresult.status == "0")
		{
			markers[sysno].display(1);
		}
	}catch(err)
	{
	}
}else{
		try{
		if(updatedresult.status == "0")
		{
			markers[sysno].display(0);
		}}catch(err)
		{
		}
}

if(displayStopped)
{
		try{
		if(updatedresult.status == "1")
		{
			markers[sysno].display(1);
		}}catch(err)
		{
		}
}else{
		try{
		if(updatedresult.status == "1")
		{
			markers[sysno].display(0);
		}}catch(err)
		{
		}
}

if(displayIdle)
{
		try{
		if(updatedresult.status == "2")
		{
			markers[sysno].display(1);
		}}catch(err)
		{
		}
}else{
		try{
		if(updatedresult.status == "2")
		{
			markers[sysno].display(0);
		}}catch(err)
		{
		}
}

if(displayRunning)
{
		try{
		if(updatedresult.status == "3" || updatedresult.status == "4")
		{
			markers[sysno].display(1);
		}}catch(err)
		{
		}
}else{
		try{
		if(updatedresult.status == "3" || updatedresult.status == "4")
		{
			markers[sysno].display(0);
		}}catch(err)
		{
		}
}
}
else{
	show_vehicles(searchtextfixed);
}

		//document.getElementById('velosity'+sysno).innerHTML = updatedresult.lastvelosity;
		//document.getElementById('subvelosity'+sysno).innerHTML = updatedresult.lastvelosity;
		//document.getElementById('time'+sysno).innerHTML = updatedresult.Time;
		//document.getElementById('milage'+sysno).innerHTML = updatedresult.lastmilage;
		//document.getElementById('Time'+sysno).innerHTML = responseresult[sysno].geocordinates[0].Time;
		//alert(document.getElementById('Time'+sysno).innerHTML);

}

function MoveMarker(sysno,lastlati,lastlng,lastdirection,lastvelosity,Status){
try{
	var latlng;
	var newPx;
	
	latlng = new OpenLayers.LonLat(lastlng,lastlati)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
		newPx = map.getLayerPxFromLonLat(latlng);
		markers[sysno].moveTo(newPx);
		markers[sysno].setUrl(select_image(lastdirection,lastvelosity,Status));			//('https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png');					//(select_image(lastdirection,lastvelosity));
}catch(err){
	//alert(err);
}
}

function select_image(Angle,Velocity,Status){
		if(Status == 4){
			var image= 'mapicon/red_blue1.png';
			return image;
		}
		else if(Status == 3){
			if (Angle>=0 && Angle<11){
				var image= 'mapicon/red_blue1.png';
			}
			if (Angle>=11 && Angle<34){
				var image= 'mapicon/red_blue2.png';
			}
			if (Angle>=34 && Angle<56){
				var image= 'mapicon/red_blue3.png';
			}
			if (Angle>=56 && Angle<78){
				var image='mapicon/red_blue4.png';
			}
			if (Angle>=78 && Angle<101){
				var image= 'mapicon/red_blue5.png';
			}
			if (Angle>=101 && Angle<124){
				var image= 'mapicon/red_blue6.png';
			}
			if (Angle>=124 && Angle<146){
				var image= 'mapicon/red_blue7.png';
			}
			if (Angle>=146 && Angle<168){
				var image= 'mapicon/red_blue8.png';
			}
			if (Angle>=168 && Angle<191){
				var image= 'mapicon/red_blue9.png';
			}
			if (Angle>=191 && Angle<214){
				var image= 'mapicon/red_blue10.png';
			}
			if (Angle>=214 && Angle<236){
				var image= 'mapicon/red_blue11.png';
			}
			if (Angle>=236 && Angle<259){
				var image= 'mapicon/red_blue12.png';
			}
			if (Angle>=259 && Angle<281){
				var image= 'mapicon/red_blue13.png';
			}
			if (Angle>=281 && Angle<304){
				var image='mapicon/red_blue14.png';
			}
			if (Angle>=304 && Angle<326){
				var image= 'mapicon/red_blue15.png';
			}
			if (Angle>=326 && Angle<348){
				var image= 'mapicon/red_blue16.png';
			}
			if (Angle>=348 && Angle<=360){
				var image= 'mapicon/red_blue1.png';
			}
			return image;
		}
		else if(Status == 2){
			var image= 'mapicon/red_blueidle.png';
			return image;
		}
		else if(Status == 1){
			var image= 'mapicon/red_bluestop.png';
			return image;
		}
		else{
			var image= 'mapicon/red_blueoff.png';
			return image;
		}
}

function searchvehicle(searchtxt)
{

displayOffline = true;
displayStopped = true;
displayIdle = true;
displayRunning = true;
searchtextfixed = searchtxt;

if(!searchtxt == "")
{
	//Android.showToast("P);
	show_vehicles(searchtxt);
}else{
	show_all_markers();
}
//Android.showToast(searchtxt);
}

function show_vehicles(searchtxt)
{
	
	for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
		if(0<=Vehicleinfo.Vehicleinfo[i].objname.search(new RegExp(searchtxt.trim(),"i")))
		{
			markers[Vehicleinfo.Vehicleinfo[i].objsysno].display(1);
		}
		else{
			markers[Vehicleinfo.Vehicleinfo[i].objsysno].display(0);
		}
	}
}

function show_all_markers()
{
	for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
		try{
			markers[responseresult.geocordinates[i].objsysno].display(1);
		}catch(err)
		{
			//alert(responseresult.geocordinates[i].objsysno);
		}
	}
	
}

function myFunction(){
	Running(status)
	alert('checked');
	
}

function getspeed(sysno)
{
	for(var i=0; i< responseresult.geocordinates.length; i++){
		try{
			if(responseresult.geocordinates[i].objsysno==sysno)
			{
				return responseresult.geocordinates[i].lastvelosity;
			}
		}catch(err)
		{
			//return err;
		}
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