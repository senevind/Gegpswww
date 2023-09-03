<?PHP
ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");
if(!$fgmembersite->CheckLogin())
{
	if(!$fgmembersite->Login())
	   {
		   echo "Authantication Error!";
		   die;
			//$fgmembersite->RedirectToURL("login.php");
	  }
}

?>


<!DOCTYPE html>
<html>

<head>
<script language="Javascript" type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

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


	
	
<script>
var map;
var Markers_layer;
var markers = new Array();
var Parking_markers = new Array();
var responseresult= new Array();
var trackingsysno;
var lineLayer;
var pointLayer;
var Parking_layer;
var seekposition=0;
var infocontent = {};
var popups = new Array();

function initialize() {
	disablebtnclass();
	if(!map){
		var MapBox = new OpenLayers.Layer.XYZ(
		  'MapBox',
		  ['https://map.gsupertrack.com/apimapbox.php?provider=mapbox&type=streets-v11&z=${z}&x=${x}&y=${y}'],
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

			var lonLat2 = new OpenLayers.LonLat(80.641021,7.293487)
				  .transform(
					new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
					map.getProjectionObject() // to Spherical Mercator Projection
				  );

		map.setCenter (lonLat2,8);

		pointLayer = new OpenLayers.Layer.Vector("Point Layer");
		map.addLayer(pointLayer);

		lineLayer = new OpenLayers.Layer.Vector("Line Layer1"); 
		map.addLayer(lineLayer);

		Parking_layer = new OpenLayers.Layer.Markers( "Parking_layer" );
		map.addLayer(Parking_layer);

		Markers_layer = new OpenLayers.Layer.Markers( "Markers_layer" );
		map.addLayer(Markers_layer);

		markers_create();
	}
	
}

function markers_create(){

	var lonLat = new OpenLayers.LonLat(7.0106011,80.046326)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
    var size = new OpenLayers.Size(37,37);
    var offset = new OpenLayers.Pixel(-(size.w/2), -(size.h/2));
    var icon = new OpenLayers.Icon('mapicon/red_blue0.png',				//'icons/empty.png',
               size, offset);			   
	markers = new OpenLayers.Marker(lonLat,icon);
	
	
markers.events.register("click", markers, function(e){					// Info window initialize
//marker_popup_open(i);
});
Markers_layer.addMarker(markers);
}

function searchbus(date,busno){
	responseresult=[];
	infocontent = [];
	try{
		stop_play();
		markers_remove();
	}
	catch(err){
		var err_var=1;
	}
	flight_path=[];
	seekposition=0;
	
	lineLayer.destroyFeatures();
	pointLayer.destroyFeatures();
	Parking_layer.clearMarkers();
	Markers_layer.clearMarkers();
	
	disablebtnclass();
	markers_create();
	isplaying = false;
	MoveMarker(80.046326,7.0106011,0,0);
	load_bus_data(date,busno);
}

function disablebtnclass(){

}

function activatebtnclass(){

}

function load_bus_data(date,busno){
	//Android.showToast(date+"\nSpeed:"+busno);
if(busno == "" || date == ""){
	alert("Check the input values !");
}else{
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
					try{
					responseresult=JSON.parse(xmlhttp.responseText);
					if(responseresult.Vehicleinfo.length>0 && responseresult.Vehicleinfo != null)
					{
					lineLayer.addFeatures([create_path_coordinates(responseresult,'#0000FF')]);
					Parking_markersLoad(responseresult);
					Android.loaddatafinished();
					mapcentreafterload(responseresult);
					play();
					}else{
					Android.loaddatafinishedwitherror("or No data available!");
					}
					isplaying = false;
					}
					catch(err){
						Android.loaddatafinishedwitherror("");	
						isplaying = false;						
					}
					
			    }
}
	xmlhttp.open("GET","play_backjsonTest.php?busno="+busno+"&sttime="+date+" 00:00:00:000&endtime="+date+" 23:59:59:000");
	xmlhttp.send();
}
}

function mapcentreafterload(points_array)
{
	set_map_zoom(16)
	for(var i = 0; i<points_array.Vehicleinfo.length; i++){
		track(points_array.Vehicleinfo[seekposition].lat,points_array.Vehicleinfo[seekposition].lng);
		return;
	}
}

function Parking_markersLoad(points_array)
{
	for(var i = 0; i<points_array.Vehicleinfo.length; i++){
		if(points_array.Vehicleinfo[i].status == 'Stop')
		{
		//points.push(new OpenLayers.Geometry.Point(points_array.Vehicleinfo[i].lng, points_array.Vehicleinfo[i].lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()))
		Parking_markers_create(points_array.Vehicleinfo[i].lng,points_array.Vehicleinfo[i].lat,i,points_array.Vehicleinfo[i].time,points_array.Vehicleinfo[i].park);
		}
	}
}

function Parking_markers_create(Lon,Lat,i,parkintime,Duration){

	var lonLat = new OpenLayers.LonLat(Lon,Lat)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
    var size = new OpenLayers.Size(60,80);
    var offset = new OpenLayers.Pixel(-(size.w/2), -(size.h/2));
    var icon = new OpenLayers.Icon('mapicon/other/parkico.png',				//'icons/empty.png',
               size, offset);			   
	Parking_markers[i] = new OpenLayers.Marker(lonLat,icon);
	
	
Parking_markers[i].events.register("click", markers, function(e){					// Info window initialize
marker_popup_open(i,Lat,Lon);
});
Parking_markers[i].events.register("touchstart", markers, function(e){					// Info window initialize
marker_popup_open(i,Lat,Lon);
});
Parking_layer.addMarker(Parking_markers[i]);
setinfocontent(i,parkintime,Duration);
}


function play_loop(action){
	
		if(seekposition<responseresult.Vehicleinfo.length){
			MoveMarker(responseresult.Vehicleinfo[seekposition].lat,responseresult.Vehicleinfo[seekposition].lng,responseresult.Vehicleinfo[seekposition].Angle,responseresult.Vehicleinfo[seekposition].Velocity,responseresult.Vehicleinfo[seekposition].mapimage)
			track(responseresult.Vehicleinfo[seekposition].lat,responseresult.Vehicleinfo[seekposition].lng)
			updatecontentinfo(seekposition);
			if(action=='f'){
			seekposition++;
			}
			if(action=='b'){
				if(seekposition <= 0){
					seekposition = 0;
				}
				else{
				seekposition = seekposition-1;
				}
			}
			
		}
		else{
			stop_play();
		}
}

function updatecontentinfo(position){
Android.changespeed(responseresult.Vehicleinfo[position].Velocity+" Km/h");
Android.changetime(responseresult.Vehicleinfo[position].time);
Android.changemilage(responseresult.Vehicleinfo[position].Miles-responseresult.Vehicleinfo[0].Miles);
Android.changefuel(responseresult.Vehicleinfo[position].Oil);
	//document.getElementById('timeinfo').innerHTML = responseresult.Vehicleinfo[position].time;
	//document.getElementById('speedinfo').innerHTML = responseresult.Vehicleinfo[position].Velocity+" Km/h";
	//document.getElementById('milageinfo').innerHTML = responseresult.Vehicleinfo[position].Miles;
	//document.getElementById('fuelinfo').innerHTML = responseresult.Vehicleinfo[position].Oil;
	
}

function play(){
if( typeof responseresult.Vehicleinfo.length === 'undefined' || responseresult.Vehicleinfo.length === null){
    searchbus();
}else{
	if(!isplaying){
	isplaying = true;
	play_timer=setInterval("play_loop('f')", 300);
	}
}
}

function stop_play(){
clearInterval(play_timer);
isplaying = false;
}

function back(){
	stop_play();
	play_loop('b');
}

function create_path_coordinates(points_array,line_color){
try{ 
var points = []; 
	for(var i = 0; i<points_array.Vehicleinfo.length; i++){
		//alert(points_array.Vehicleinfo[i].lat);
		points.push(new OpenLayers.Geometry.Point(points_array.Vehicleinfo[i].lng, points_array.Vehicleinfo[i].lat).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()))
		
	}

	var line = new OpenLayers.Geometry.LineString(points);

	var style = { 
	  strokeColor: line_color, 
	  strokeOpacity: 1,
	  strokeWidth: 2
	};

	var lineFeature = new OpenLayers.Feature.Vector(line, null, style);
	
return lineFeature;
}
catch(err){
alert("create_path_coordinates"+err);
}
}

function MoveMarker(lastlati,lastlng,lastdirection,lastvelosity,mapimage){
try{
	var latlng;
	var newPx
	
	latlng = new OpenLayers.LonLat(lastlng,lastlati)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
		newPx = map.getLayerPxFromLonLat(latlng);
		markers.moveTo(newPx);
		markers.setUrl(select_image(lastdirection,lastvelosity,mapimage));			//('https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png');					//(select_image(lastdirection,lastvelosity));
}catch(err){
	//alert(err);
}
}

function track(lat,lng){
	var latlng;
		latlng = new OpenLayers.LonLat(lng,lat)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
	set_centre(latlng);
}

function select_image(Angle,Velocity,imagefolder){

			if (Angle>=0 && Angle<11){
				var image= 'mapicon/'+imagefolder+'/1.png';
			}
			 else if (Angle>=11 && Angle<34){
				var image= 'mapicon/'+imagefolder+'/2.png';
			}
			 else if (Angle>=34 && Angle<56){
				var image= 'mapicon/'+imagefolder+'/3.png';
			}
			 else if (Angle>=56 && Angle<78){
				var image='mapicon/'+imagefolder+'/4.png';
			}
			 else if (Angle>=78 && Angle<101){
				var image= 'mapicon/'+imagefolder+'/5.png';
			}
			 else if (Angle>=101 && Angle<124){
				var image= 'mapicon/'+imagefolder+'/6.png';
			}
			 else if (Angle>=124 && Angle<146){
				var image= 'mapicon/'+imagefolder+'/7.png';
			}
			 else if (Angle>=146 && Angle<168){
				var image= 'mapicon/'+imagefolder+'/8.png';
			}
			 else if (Angle>=168 && Angle<191){
				var image= 'mapicon/'+imagefolder+'/9.png';
			}
			 else if (Angle>=191 && Angle<214){
				var image= 'mapicon/'+imagefolder+'/10.png';
			}
			 else if (Angle>=214 && Angle<236){
				var image= 'mapicon/'+imagefolder+'/11.png';
			}
			 else if (Angle>=236 && Angle<259){
				var image= 'mapicon/'+imagefolder+'/12.png';
			}
			 else if (Angle>=259 && Angle<281){
				var image= 'mapicon/'+imagefolder+'/13.png';
			}
			 else if (Angle>=281 && Angle<304){
				var image='mapicon/'+imagefolder+'/14.png';
			}
			 else if (Angle>=304 && Angle<326){
				var image= 'mapicon/'+imagefolder+'/15.png';
			}
			 else if (Angle>=326 && Angle<348){
				var image= 'mapicon/'+imagefolder+'/16.png';
			}
			 else if (Angle>=348 && Angle<=360){
				var image= 'mapicon/'+imagefolder+'/1.png';
			}
			else{
			    var image= 'mapicon/'+imagefolder+'/1.png';
			}
			return image;
}

function set_centre(latlng){
	try{
		map.setCenter(latlng);
	}catch(err){
	}
}

function set_map_zoom(value){
map.zoomTo(value);
}

function marker_popup_open(sysno,lat,lng){
for(var i=0;i<responseresult.Vehicleinfo.length;i++){
	try{
		popups[responseresult.Vehicleinfo[i].objsysno].destroy();
	}catch(e){
		
	}
}
		var lonLat = new OpenLayers.LonLat(lng,lat)
		.transform(
		new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
		map.getProjectionObject() // to Spherical Mercator Projection
		);
		popups[sysno] = new OpenLayers.Popup.FramedCloud("popup",
		lonLat,
		new OpenLayers.Size(200, 100),
		infocontent[sysno],
		//"inf content",
		null, true);
		map.addPopup(popups[sysno]);
		popups[sysno].hide();

popups[sysno].show();
}

function setinfocontent(objsysno,parkintime,Duration)
{
    var content = "<p><strong>Duration: " + Duration  + "</strong><br><small>" + parkintime + "</small></p>";
    infocontent[objsysno] = content; 
}

function changevehiclename(vehiclename)
{
	if(vehiclename != '')
	{
   $('select[name=selValue]').val(vehiclename);
   $('select[name=selValue]').change();
		//$('select[name=selValue]').val(vehiclename);
		//$('.selectpicker').selectpicker('refresh');
		//document.getElementById('trackbus').selectedIndex = 1;
		//document.getElementById('trackbus').value=vehiclename;
	}
}

</script>
</head>

<body onload="initialize()">

<div  id="mapholder" class="mapholder">
<!--<button onclick="load_bus_data('2018-04-19','60-9494')">load</button>
<button onclick="play()">play</button>-->
</div>
</body>
</html>
