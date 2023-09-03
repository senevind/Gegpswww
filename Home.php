<?PHP
ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");
require_once("./include/Routes.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title><?= $fgmembersite->Title1 ?> | Tracking View</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <link rel="shortcut icon" type="image/ico" href="favicon.ico" />
	<link rel="manifest" href="manifest.json">
	
    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />
    <link rel="stylesheet" href="vendor/select2-3.5.2/select2.css" />
    <link rel="stylesheet" href="vendor/select2-bootstrap/select2-bootstrap.css" />	
	
    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">
	<link rel="stylesheet" href="vendor/fooTable/css/footable.core.min.css" />
    <link rel="stylesheet" href="vendor/datatables.net-bs/css/dataTables.bootstrap.min.css" />
    <link rel="stylesheet" href="vendor/sweetalert/lib/sweet-alert.css" />
    <link rel="stylesheet" href="vendor/toastr/build/toastr.min.css" />
	
	<!--Draw Styles-->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.css"/>
	
	<!--Leaflet Locate-->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.76.0/dist/L.Control.Locate.min.css" />
	
	
	<!-- place this in a head section -->
	<link rel="apple-touch-icon" href="touch-icon-iphone.png">
	<link rel="apple-touch-icon" sizes="152x152" href="images/contract.png">
	<link rel="apple-touch-icon" sizes="180x180" href="images/contract.png">
	<link rel="apple-touch-icon" sizes="167x167" href="images/contract.png">
	
	
	<!-- place this in a head section -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link href="images/contract.png" sizes="2048x2732" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="1668x2224" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="1536x2048" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="1125x2436" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="1242x2208" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="750x1334" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="640x1136" rel="apple-touch-startup-image" />
	<style>
	.star {
		visibility:hidden;
		//display: none;
		font-size:17px;
		cursor:pointer;
		color: #f5c860;
	}
	.star:before {
	   content: "\2606";
	   position: absolute;
	   visibility:visible;
	}
	.star:checked:before {
	   content: "\2605";
	   position: absolute;
	}
	#OpenLayers_Control_LayerSwitcher_38,#OpenLayers_Control_LayerSwitcher_28,#OpenLayers_Control_MaximizeDiv{
		top: 80px;
	}
	</style>

	<link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="Leaflet/leaflet/leaflet.css"/>
    <script type="text/javascript" src="Leaflet/leaflet/leaflet.js"></script>
	<script type="text/javascript" src="Leaflet/MovingMarker.js"></script>

	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAx0beF1k-G06wZez4T27V49kIsjKs5gac&language=ar&region=MA" async defer></script>
	<script type="module" src="Leaflet/GoogleMutant-master/src/Leaflet.GoogleMutant.js"></script>	
	<style>
	.toast-message {
		color: black;
	}
	</style>
	<script> var Vehicleinfo =  <?php
		$objectlist = $fgmembersite->getbuslisttablearraySTD();
		echo $objectlist['dataset']; 
	?>
	</script>
	<script>
	var userName = '<?php echo $fgmembersite->UserName()?>';
	</script>
<script>
<?php echo "var Token='".$fgmembersite->UserToken()."';";?>
var map;
var VeMarkers = L.layerGroup();
var markers = new Array();
var infocontent = {};
var responseresult;
var objectnewestvalues = {};
var Geomarkers = new Array();
var Geopolygon = new Array();
var MarkerBounds = [];
var LastTracks = new Array();

function initialize() {
	
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
			
			
			VeMarkers.addTo(map);
			loadvehiclelist();
			//uncheckedAllVehicles();
			SetMarkerBounds();
			marker_refresh();
			setInterval('marker_refresh()', 15000);
			geoDrawInitialize();
			//LoadLastTracks();

//L.control.locate().addTo(map);
// create control and add to map
//var lc = L.control.locate().addTo(map);

// request location update and set location
//lc.start();
	}
}

function uncheckedAllVehicles()
{

	document.getElementById('allvehiclecheck').checked = false;
	toggleAllCheckbox();
	
}

function LoadFocusGroup(lat,lng)
{
	if(lat!=0)
	{
		MarkerBounds.push([lat,lng]);
	}
}

function SetMarkerBounds()
{
	var bounds = new L.LatLngBounds(MarkerBounds);
	map.fitBounds(bounds);
}
	
function togglegeofence(type,id)
{
	if(document.getElementById('checkgeo'+id).checked == true)
	{
		if(type == '1')
		{
			showfence(id);
		}
		if(type == '2')
		{
			shomarker(id);
		}
	}else{
		if(type == '1')
		{
			hidefence(id);
		}
		if(type == '2')
		{
			hidemarker(id);
		}
	}
}

function showfence(pointid)
{

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
				loadgeofence(xmlhttp.responseText,pointid);
			    }
		  }
		  var url= "./geohandle.php?getpoligonvalues="+pointid;

	xmlhttp.open("GET",url);
	xmlhttp.send();
}

function hidefence(id)
{
	map.removeLayer(Geopolygon[id]);
}


function shomarker(pointid)
{
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
				showmarker(xmlhttp.responseText,pointid);
			    }
		  }
		  var url= "./geohandle.php?getpointvalues="+pointid;

	xmlhttp.open("GET",url);
	xmlhttp.send();
}

function hidemarker(id)
{
	map.removeLayer(Geomarkers[id]);
}

function showmarker(valuestring,id)
{
	if(valuestring==null)
	{
		alert('Invalide data!');
		return false;
	}
	
	if(Geomarkers[id] != undefined){
		map.removeLayer(Geomarkers[id]);
	}

		var myObj = JSON.parse(valuestring);

		Geomarkers[id] = L.marker([myObj.pointlat,myObj.pointlong]).addTo(map).bindTooltip("<b>"+myObj.geo_name+"</b>",{permanent: true,direction: 'right',offset:L.point(15, 0)}).bindPopup(myObj.pointlong+"<br>"+myObj.pointlat,{offset: [0, -15]}).on('click', function(e) 
			{
				console.log(e.latlng);
				console.log(this.options.id);
			});	

		var hazardIcon = L.icon({
			iconUrl: 'mapicon/other/geopointmarker.png',
			iconSize: [50, 50],
			iconAnchor: [15, 15],
			popupAnchor: [0, 0]
		});

		Geomarkers[id].setIcon(hazardIcon);

		map.flyTo([myObj.pointlat,myObj.pointlong], 14);

}


function loadgeofence(valuestring,id)
{
	if(Geopolygon[id] != undefined){
		map.removeLayer(Geopolygon[id]);
	}
	var myObj = JSON.parse(valuestring);

	var coordinates = myObj.cordinates;

	var pointarray = coordinates.split(" ");
	
	var polygon  = new Array();
	
	for(var i=0;i < pointarray.length;i++)
	{
		var point  = new Array();
		point.push(pointarray[i].split(",")[1]);
		point.push(pointarray[i].split(",")[0]);
		
		polygon.push(point);
	}
	
	Geopolygon[id] = L.polygon(polygon).addTo(map);

	map.flyTo(point, 14);
}

function toggleAllCheckbox()
{
	
	if(document.getElementById('allvehiclecheck').checked == true)
	{
	  var x = document.getElementsByName("vehicle");
	  var i;
	  for (i = 0; i < x.length; i++) {
		if (x[i].type == "checkbox") {
			allmarkerdisplayON();
			x[i].checked = true;
		}
	  }
	}else{
	  var x = document.getElementsByName("vehicle");
	  var i;
	  for (i = 0; i < x.length; i++) {
		if (x[i].type == "checkbox") {
			allmarkerdisplayOFF();
			x[i].checked = false;
		}
	  }
	}
}

function allmarkerdisplayON()
{
		for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
			markerdisplayon(Vehicleinfo.Vehicleinfo[i].objsysno);
			document.getElementById("check"+Vehicleinfo.Vehicleinfo[i].objsysno).checked = true;
		}
}


function allmarkerdisplayOFF()
{
		for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
			markerdisplayoff(Vehicleinfo.Vehicleinfo[i].objsysno);
			document.getElementById("check"+Vehicleinfo.Vehicleinfo[i].objsysno).checked = false;
		}
}

function togglegroup(value)
{
	
		for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
			if(Vehicleinfo.Vehicleinfo[i].group==value)
			{
				if(document.getElementById('group'+value).checked == true){
					markerdisplayon(Vehicleinfo.Vehicleinfo[i].objsysno);
					document.getElementById("check"+Vehicleinfo.Vehicleinfo[i].objsysno).checked = true;
				}else{
					markerdisplayoff(Vehicleinfo.Vehicleinfo[i].objsysno);
					document.getElementById("check"+Vehicleinfo.Vehicleinfo[i].objsysno).checked = false;
				}
			}
		}
}
function toggleCheckbox(value){
	
	if(document.getElementById('check'+value).checked == true){
		markerdisplayon(value);
	}else{
		markerdisplayoff(value);
	}
}


function markerdisplayoff(markerno){
	if(markers[markerno] != undefined){
		map.removeLayer(markers[markerno]);
		LastTracks = arrayRemove(LastTracks, markerno);
	}
	//SaveLastTracks();
}

function markerdisplayon(markerno){

		markers[markerno].addTo(map);
		arrayAdd(LastTracks, markerno);
		//SaveLastTracks();
}

function arrayRemove(arr, value) { 

	return arr.filter(function(ele){ 
		return ele != value; 
	});
}

function arrayAdd(arr, value)
{
	arr.push(value);
}

function LoadLastTracks()
{
	$.post( "api.php", { LoadLastTracks:"", user:userName})
	  .done(function( TaskJsonData ) {
		  if(TaskJsonData.status == true)
		  {
			  //console.log(JSON.parse(TaskJsonData.values));
			  DisplayLoadTracks(JSON.parse(TaskJsonData.values));
		  }
		});
}

function DisplayLoadTracks(tracksArray)
{
	for(i=0;i<tracksArray.length;i++)
	{
		markerdisplayon(tracksArray[i]);
		document.getElementById("check"+tracksArray[i]).checked = true;
		//console.log(tracksArray[i]);
	}
}

function SaveLastTracks()
{
	$.post( "api.php", { LastTracks:"", TaskJsonData:JSON.stringify(LastTracks), user:userName})
	  .done(function( TaskJsonData ) {
		  if(TaskJsonData.status == true)
		  {
			  console.log(JSON.stringify(TaskJsonData));
			  toastr.success("Saved!");
		  }
		});
	
}


function loadvehiclelist(){
		for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
			
			if(Vehicleinfo.Vehicleinfo[i].lastlati !=null && Vehicleinfo.Vehicleinfo[i].lastlati != ""){
				set_marker_array(Vehicleinfo.Vehicleinfo[i].objsysno,Vehicleinfo.Vehicleinfo[i].objname,Vehicleinfo.Vehicleinfo[i].lastlati,Vehicleinfo.Vehicleinfo[i].lastlng,Vehicleinfo.Vehicleinfo[i].lastdirection,Vehicleinfo.Vehicleinfo[i].lastvelosity);
				LoadFocusGroup(Vehicleinfo.Vehicleinfo[i].lastlati,Vehicleinfo.Vehicleinfo[i].lastlng);
			}
		}
}


function set_marker_array(sysno,detail,lat,lng,direction,velocity){
	
markers[sysno] = L.Marker.movingMarker([[lat,lng]],[1000],{id:sysno}).addTo(VeMarkers).bindTooltip("<b>"+detail+"</b><br>"+"<span id='Tooltip-"+sysno+"' style='color:blue'></span>",{permanent: true,direction: 'right',offset:L.point(25, -25)}).bindPopup(setinfocontent(sysno,Vehicleinfo.Vehicleinfo),{offset: [0, -50]}).on('click', function(e) 
{
	console.log(e.latlng);
	console.log(this.options.id);
	setinfocontentPopup(this.options.id,objectnewestvalues[this.options.id]);
});

MarkerSetIcon(markers[sysno],lat,lng,direction,velocity,2,sysno);
markers[sysno].bindPopup(setinfocontent(sysno,Vehicleinfo.Vehicleinfo));
}

function marker_refresh(){

    var packetsize = 30;
    j=0;
	var sysnostring = "";
		for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
			if(Vehicleinfo.Vehicleinfo[i].lastlati !=null || Vehicleinfo.Vehicleinfo[i].lastlati != ""){
				sysnostring += ";"+Vehicleinfo.Vehicleinfo[i].objsysno;
				j++;
				if(j==packetsize)
				{
				    loadvehicleinfo(sysnostring);
				    sysnostring="";
				    j=0;
				    setTimeout(function(){  }, 2000);
				}
			}
		}
loadvehicleinfo(sysnostring);
}

function loadvehicleinfo(sysno){
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
							objectnewestvalues[responseresult.geocordinates[i].objsysno] = responseresult.geocordinates[i];
							MoveMarker(responseresult.geocordinates[i].objsysno,responseresult.geocordinates[i].objname,responseresult.geocordinates[i].lastlati,responseresult.geocordinates[i].lastlng,responseresult.geocordinates[i].lastdirection,responseresult.geocordinates[i].lastvelosity,responseresult.geocordinates[i].imagefolder,responseresult.geocordinates[i].status);
							//setinfocontent(responseresult.geocordinates[i].objsysno,responseresult.geocordinates[i]);
							markers_set_position(responseresult.geocordinates[i],responseresult.geocordinates[i].objsysno);
							
						}
					}
				}
		  }
	//xmlhttp.open("GET","getcoordinates.php?sysnostring="+sysno);
	
	xmlhttp.open("GET","api.php?Token="+Token+"&sysnostring="+sysno);
	xmlhttp.send();
}


function MoveMarker(sysno,detail,lat,lng,direction,velocity,imagefolder,Status){
	MarkerMove(markers[sysno],lat,lng,direction,velocity,Status,sysno);	
	var popup = markers[sysno].getPopup();

		if(popup.isOpen())
		{
			setinfocontentPopup(sysno,objectnewestvalues[sysno]);
			Get_RevCoordinates(sysno,lat,lng);
		}
}

function MarkerMove(marker,lat,lng,Angle,Velocity,Status,sysno)
{
	MarkerSetIcon(marker,lat,lng,Angle,Velocity,Status,sysno);
	var newLatLng = new L.LatLng(lat,lng);
	//marker.setLatLng(newLatLng);
	marker.moveTo(newLatLng, 1000);
}



function markers_set_position(updatedresult,sysno){
		
		document.getElementById('velosity'+sysno).innerHTML = updatedresult.lastvelosity+" "+updatedresult.speedUnit;
		document.getElementById('time'+sysno).innerHTML = updatedresult.Time;
		//document.getElementById('milage'+sysno).innerHTML = updatedresult.lastmilage;
		document.getElementById('lati'+sysno).innerHTML = updatedresult.lastlati;
		document.getElementById('lng'+sysno).innerHTML = updatedresult.lastlng;
		document.getElementById('dir'+sysno).innerHTML = updatedresult.lastdirection;
		document.getElementById('park'+sysno).innerHTML = updatedresult.parking;
		
		document.getElementById('subvelosity'+sysno).innerHTML = updatedresult.lastvelosity+" "+updatedresult.speedUnit;
		//document.getElementById('submilage'+sysno).innerHTML = updatedresult.lastmilage;
		document.getElementById('subpark'+sysno).innerHTML = updatedresult.parking;
		if(updatedresult.status == 4){
			document.getElementById('status'+sysno).innerHTML = 'OverSpeed';
			document.getElementById('status'+sysno).className = 'label GPSlabel-danger pull-left';
			
			//document.getElementById('substatus'+sysno).innerHTML = 'OverSpeed';
			//document.getElementById('substatus'+sysno).className = 'label GPSlabel-danger pull-left';
		}
		else if(updatedresult.status == 3){
			document.getElementById('status'+sysno).innerHTML = 'Running';		//
			document.getElementById('status'+sysno).className = 'label GPSlabel-warning pull-left';
			document.getElementById('substatus'+sysno).className = 'btn btn-xs btn-success';
			//document.getElementById('substatus'+sysno).innerHTML = 'Running';		//
			//document.getElementById('substatus'+sysno).className = 'label GPSlabel-warning pull-left';
		}
		else if(updatedresult.status == 2){
			document.getElementById('status'+sysno).innerHTML = 'Idle';		//active
			document.getElementById('status'+sysno).className = 'label GPSlabel-success pull-left';
			document.getElementById('substatus'+sysno).className = 'btn btn-xs btn-warning';		//btn btn-xs btn-warning
			//document.getElementById('substatus'+sysno).innerHTML = 'Idle';		//active
			//document.getElementById('substatus'+sysno).className = 'label GPSlabel-success pull-left';
		}
		else if(updatedresult.status == 1){
			document.getElementById('status'+sysno).innerHTML = 'Stoped';		//online
			document.getElementById('status'+sysno).className = 'label GPSlabel-info pull-left';
			document.getElementById('substatus'+sysno).className = 'btn btn-xs btn-danger';
			//document.getElementById('substatus'+sysno).innerHTML = 'Stoped';		//online
			//document.getElementById('substatus'+sysno).className = 'label GPSlabel-info pull-left';
		}
		else{
			document.getElementById('status'+sysno).innerHTML = 'Offline';
			document.getElementById('status'+sysno).className = 'label GPSlabel-offline pull-left';
			document.getElementById('substatus'+sysno).className = 'label btn btn-xs btn-default';
			//document.getElementById('substatus'+sysno).innerHTML = 'Offline';
			//document.getElementById('substatus'+sysno).className = 'label pull-left';
		}		

		if(document.getElementById('radio'+sysno).checked == true){
			//track(updatedresult.lastlati,updatedresult.lastlng);
		}
		
		UpdateTooltips(updatedresult,sysno);
		
}

function UpdateTooltips(updatedresult,sysno)
{
	document.getElementById('Tooltip-'+sysno).innerHTML = updatedresult.lastvelosity+" "+updatedresult.speedUnit;
}

function check(sysno) {
	track(objectnewestvalues[sysno].lastlati,objectnewestvalues[sysno].lastlng);
	markers[sysno].openPopup();
}

function openmap(sysno){
	if(document.getElementById('check'+sysno).checked == true){
		mapviewtabopen();
		trackingsysno = sysno;
		check(sysno);
		document.body.scrollTop = document.documentElement.scrollTop = 0;
		track(objectnewestvalues[sysno].lastlati,objectnewestvalues[sysno].lastlng);
		setinfocontentPopup(sysno,objectnewestvalues[sysno]);
	}
}

function mapviewtabopen()
{
	//$('.nav-tabs a[href="#mapholder"]').tab('show');
	$('[href="#mapholder"]').tab('show');
}

function track(lat,lng){
	//$('.nav-tabs a[href="#tab-1"]').tab('show');
	map.flyTo([lat,lng], 14);
	
}

function load_markers(){
		for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
			if(Vehicleinfo.Vehicleinfo[i].lastlati !=null && Vehicleinfo.Vehicleinfo[i].lastlati != ""){
				set_marker_array(Vehicleinfo.Vehicleinfo[i].objsysno,Vehicleinfo.Vehicleinfo[i].objname,Vehicleinfo.Vehicleinfo[i].lastlati,Vehicleinfo.Vehicleinfo[i].lastlng,Vehicleinfo.Vehicleinfo[i].lastdirection,Vehicleinfo.Vehicleinfo[i].lastvelosity);
			}
		}
}

function MarkerSetIcon(marker,lat,lng,Angle,Velocity,Status,sysno)
{
	marker.setIcon(SetMapIcon(Angle,Velocity,Status,sysno));
}

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
	
function select_image(Angle,Velocity,Status,sysno){

    var imagefolder = Vehicleinfo.Vehicleinfosys[sysno].image;

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

function StausChartUpdate(TaskJsonData,sysno)
{
			/**
			 * Pie Chart Data
			 */
			var pieChartData = [
				{ label: "", 
				data: TaskJsonData.values.runmin, 
				color: "#008000", },
				{ label: "", 
				data: TaskJsonData.values.idlemin, 
				color: "#FE9A2E", },
				{ label: "", 
				data: TaskJsonData.values.stopedmin, 
				color: "#DF013A", }
			];
			/**
			 * Pie Chart Options
			 */
			var pieChartOptions = {
				series: {
					pie: {
						show: true
					}
				},
				grid: {
					hoverable: true
				},
				tooltip: false,
				tooltipOpts: {
					content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
					shifts: {
						x: 20,
						y: 0
					},
					defaultTheme: true
				}
			};
			$.plot($("#flot-pie-chart-"+sysno), pieChartData, pieChartOptions);
			
			
			var today = new Date().getTime();
			var MidNight = new Date().setHours(0,0,0,0);
			var diffMs = (today-MidNight); // milliseconds between now & MidNight
			var resultInMinutes = Math.round(diffMs / 60000);
			var onlineStatus = (TaskJsonData.values.onlinemin/resultInMinutes)*100;
			if(onlineStatus>100)
			{
				onlineStatus = 100;
			}
			document.getElementById('popup-battery-'+sysno).innerHTML = TaskJsonData.values.battery+'%';
			document.getElementById('onlineStatus-'+sysno).style.width = onlineStatus+'%';
			document.getElementById('popup-lastmilage-'+sysno).innerHTML = TaskJsonData.values.milage+" "+TaskJsonData.values.lengtUnit;
}

function UpdateMarkerPopupContent(sysno)
{
	const d = new Date();
	var month = d.getMonth()+1;
	var formatedMont = ("0" + month).slice(-2);
	var formatedDate = ("0" + d.getDate()).slice(-2);
	var DateStr = d.getFullYear()+"-"+formatedMont+"-"+formatedDate;
	$.post( "api.php?Token="+Token, {dawiseSummery:"dawiseSummery", date:DateStr , objectName:Vehicleinfo.Vehicleinfosys[sysno].objname})
	  .done(function( TaskJsonData ) {
		  if(TaskJsonData.status == true)
		  {
			StausChartUpdate(TaskJsonData,sysno);
		  }
  });
}

function setinfocontentPopup(objsysno,geocordinates)
{
	UpdateMarkerPopupContent(objsysno);

	var status = "Off";
	if(geocordinates.status > 1)
	{
		status = "On";
	}
	
	//document.getElementById('popup-lastmilage-'+objsysno).innerHTML = geocordinates.lastmilage+" km";
	document.getElementById('popup-lastvelosity-'+objsysno).innerHTML = geocordinates.lastvelosity+" "+geocordinates.speedUnit;
	document.getElementById('popup-parking-'+objsysno).innerHTML = geocordinates.parking;
	document.getElementById('popup-status-'+objsysno).innerHTML = status;
	document.getElementById('popup-Time-'+objsysno).innerHTML = geocordinates.Time;
	rotateIcon(geocordinates.lastdirection,objsysno);
	
	Get_RevCoordinates(objsysno,geocordinates.lastlati,geocordinates.lastlng);
}

function setinfocontent(objsysno,geocordinates)
{
	var status = "Off";
	if(geocordinates.status > 1)
	{
		status = "On";
	}
	
	var content = "<div style='width:300px;'>"+
	"<div class='row'>"+
	"<div class='col-lg-12'>"+
        "<div class='hpanel'>"+
            "<div class='alert alert-success'>"+
                "<h3 class='m-b-xs' id='popup-objname-'"+objsysno+">"+
				Vehicleinfo.Vehicleinfosys[objsysno].objname+
				"</h3>"+
            "</div>"+
            "<div class='panel-body'>"+
						"<div class='row'>"+
							"<div class='stats-title pull-left'>"+
								"<h4>Today Milage</h4>"+
								"<h3 class='m-b-xs' id='popup-lastmilage-"+objsysno+"'>"+
								"-"+
								" Miles</h3>"+
							"</div>"+
							"<div class='stats-icon pull-right'>"+
								"<div class='flot-chart' style='height: 75px;'>"+
									"<div class='flot-chart-content' id='flot-pie-chart-"+objsysno+"' style='width:75px; height:75px'></div>"+
								"</div>"+
							"</div>"+
						"</div>"+
						"<div class='row'>"+
								"<span class='font-bold no-margins'>"+
									"Online Status"+
								"</span>"+
								"<div class='progress m-t-xs full progress-small'>"+
									"<div style='width: 0%'  id='onlineStatus-"+objsysno+"' aria-valuemax='100' aria-valuemin='0' aria-valuenow='55' role='progressbar' class=' progress-bar progress-bar-success'>"+
										//"<span class='sr-only'>0% Complete (success)</span>"+
									"</div>"+
								"</div>"+
								"<div class='row'>"+
									"<div class='col-xs-3'>"+
										"<small class='stats-label'>Speed </small>"+
										"<h4 id='popup-lastvelosity-"+objsysno+"'>"+
										geocordinates.lastvelosity+
										"</h4>"+
									"</div>"+
									"<div class='col-xs-3'>"+
										"<small class='stats-label'>Parking H.m</small>"+
										"<h4 id='popup-parking-"+objsysno+"'>"+
										geocordinates.parking+
										"</h4>"+
									"</div>"+
									"<div class='col-xs-3'>"+
										"<small class='stats-label'>Engine</small>"+
										"<h4 id='popup-status-"+objsysno+"'>"+
										status+
										"</h4>"+
									"</div>"+
									"<div class='col-xs-3'>"+
										"<small class='stats-label'>Battery</small>"+
										"<h4 id='popup-battery-"+objsysno+"'>"+
										"0%"+
										"</h4>"+
									"</div>"+
								"</div>"+
								"<div class='row'>"+
									"<div class='col-xs-4'>"+
										"<small class='stats-label'><i class='fa fa-clock-o' aria-hidden='true'></i> Time : </small><br>"+
										"<small id='popup-Time-"+objsysno+"'>"+
										geocordinates.Time+
										"</small>"+
									"</div>"+
									"<div class='col-xs-4'>"+
										"<small class='stats-label'><i class='fa fa-certificate' aria-hidden='true'></i> Status : </small><br>"+
										"<small id='popup-ExpStatus-"+objsysno+"'>"+
										Vehicleinfo.Vehicleinfosys[objsysno].ExpStatus+
										"</small>"+
									"</div>"+
									"<div class='col-xs-4'>"+
										"<small>"+
										"<i id='popup-Direction-"+objsysno+"' class='fa fa-arrow-circle-up fa-5x' aria-hidden='true' style='color:#566573;'></i>"+
										"</small>"+
									"</div>"+
								"</div>"+
						"</div>"+
            "</div>"+
            "<div class='panel-footer'>"+
				"<div class='btn-group'>"+
					//"<button class='btn btn-primary' type='button'>Left</button>"+
					"<small>Address: </small><small id='popup-revgeocode-"+objsysno+"' </small>"+
					//"<button class='btn btn-primary' type='button'>Right</button>"+
				"</div>"+
            "</div>"+
            "<div class='panel-footer'>"+
				"<div class='btn-group'>"+
					//"<button class='btn btn-primary' type='button'>Left</button>"+
					//"<button class='btn btn-primary ' type='button' onclick=window.open('playback.php?vehiclename="+Vehicleinfo.Vehicleinfosys[objsysno].objname+"')><i class='fa fa-undo'></i> Playback</button>"+
					"<button class='btn btn-primary ' type='button' onclick=PlaybackWindowOpen('"+ objsysno +"')><i class='fa fa-undo'></i> Playback</button>"+
					//"<button class='btn btn-primary' type='button'>Right</button>"+
				"</div>"+
            "</div>"+
        "</div>"+
    "</div>"+
	"</div>"+
	"</div>";
	return content;
}

// Function to rotate the icon by a specified degree
function rotateIcon(degrees,objsysno) {
	
    const icon = document.getElementById("popup-Direction-"+objsysno);
    icon.style.transform = `rotate(${degrees}deg)`;
}

function PlaybackWindowOpen(sysno)
{
	window.open("playback.php?vehiclename="+Vehicleinfo.Vehicleinfosys[sysno].objname+"");
}

function Get_RevCoordinates(sysno,lat,lng)
{
$.ajax({
	
    url: "https://nominatim.openstreetmap.org/reverse?format=geojson&lat="+lat+"&lon="+lng,
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    },
    type: "GET", /* or type:"GET" or type:"PUT" */
    dataType: "json",
    data: {
    },
    success: function (result) {
		var addressproperties = result.features[0].properties.address;
		var road = addressproperties.road;
		var suburb = addressproperties.suburb;
		var postcode = addressproperties.postcode;
		var display_name = result.features[0].properties.display_name;
		//var addressConcat = road+","+suburb+","+postcode+".";
        //console.log(addressConcat);
		document.getElementById('popup-revgeocode-'+sysno).innerHTML = display_name;
    },
    error: function () {
        console.log("error");
    }
});
	
}


</script>
</head>
<body onload="initialize()" class="fixed-navbar sidebar-scroll" style="margin: 0; height: 100%; overflow: hidden">
<!-- Skin option / for demo purpose only -->
<div class="skin-option animated fadeInRight">
                    <a href="#" id="sidebar" class="right-sidebar-toggle">
						<i id="demo-star" class="fa fa-car"></i><br> <small class="font-bold text-muted">Tracks</small>
                    </a>
</div>
<style>
    .skin-option { position: fixed; text-align: center; right: -1px; padding: 10px; top: 80px; text-transform: uppercase; background-color: #ffffff; box-shadow: 0 1px 10px rgba(0, 0, 0, 0.05), 0 1px 4px rgba(0, 0, 0, .1); border-radius: 4px 0 0 4px; z-index: 1000; }
</style>
<script>
    setInterval(function(){ $('#demo-star').toggleClass('text-success'); }, 300)
</script>
<!-- End skin option / for demo purpose only -->

<!-- Header -->
<div id="header" style="z-index:5000">
    <div class="color-line">
    </div>
    <div id="logo" class="light-version">
        <span>
            <?= $fgmembersite->Title2 ?>
        </span>
    </div>
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo">
            <span class="text-primary"><?= $fgmembersite->Title2 ?></span>
        </div>
        <div class="mobile-menu">
            <button type="button" class="navbar-toggle mobile-menu-toggle" data-toggle="collapse" data-target="#mobile-collapse">
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="collapse mobile-navbar" id="mobile-collapse">
                <ul class="nav navbar-nav">
					<li>
						<a href="#TaskEditor" data-toggle="tab">Task add</a>
					</li>
					<li>
						<a href="#mapholder" data-toggle="tab">Map view</a>
					</li>
					<li>
						<a href="#step2" data-toggle="tab">DATA view</a>
					</li>

                    <li>
                        <a href="change-pwd.php">Change Password</a>
                    </li>
                    <li>
                        <a href="profile.php">Profile</a>
                    </li>
                    <li>
                        <a href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="navbar-right">
            <ul class="nav navbar-nav no-borders">
			   <li class="dropdown">
                    <a href="#mapholder" data-toggle="tab">
                        <i class="pe-7s-world"></i>
                    </a>
                </li>
			   <li class="dropdown">
                    <a href="#step2" data-toggle="tab">
                        <i class="fa fa-list-ul"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                        <i class="pe-7s-map-marker"></i>
                    </a>
                    <ul class="dropdown-menu hdropdown notification animated" style="overflow-y: scroll;height: 400px;">
						<?php 
						echo $resultnotifysmall = $fgmembersite->geolisttrackingview();
						?>
                        <li class="summary"><a href="geofunc.php">Manage geo functions</a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown" >
					<div id="notificstyle">
					<?php 
					$resultnotifysmall = $fgmembersite->Notifications_Small();
					echo $resultnotifysmall['style'];
					?>
					</div>
                    </a>
                    <ul class="dropdown-menu hdropdown notification animated" id="notific_small">
					<?php
						echo $resultnotifysmall['main'];
					?>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="logout.php">
                        <i class="pe-7s-upload pe-rotate-90"></i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</div>

<!-- Navigation -->
<aside id="menu">
    <div id="navigation">
        <div class="profile-picture">
            <a href="index.php">
                <img src="images/logo.jpg" class="m-b" alt="logo"  height="100" width="150">
            </a>

            <div class="stats-label text-color">
                <span class="font-extra-bold font-uppercase"><?= $fgmembersite->UserFullName(); ?></span>

                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">
                        <small class="text-muted"><?= $fgmembersite->company(); ?><b class="caret"></b></small>
                    </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
							<li><a href="change-pwd.php">Change Password</a></li>
							<li><a href="profile.php">Profile</a></li>
							<li class="divider"></li>
							<li><a href="logout.php">Logout</a></li>
                        </ul>
                </div>
            </div>
        </div>

        <ul class="nav" id="side-menu">
				<?= $fgmembersite->leftmenue(); ?>
        </ul>
    </div>
</aside>


<!-- Main Wrapper -->
<div id="wrapper">

                        <div class="tab-content">
                        <div id="mapholder" class="p-m tab-pane active" style="height: calc(100vh - 60px); padding:0px">
                        </div>
                        <div id="step2" class="p-m tab-pane">
							<input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search Vehicle" style="margin-bottom:0px;">
								<div class="panel-body" style="padding-bottom: 0px;padding-top: 0px;overflow: scroll;height: calc(100vh - 120px);">
									
									<table id="fleetdata" class="footable table table-stripped toggle-arrow-tiny" data-page-size="5" data-filter=#filter style="margin-bottom:0px;">
										<thead>
										<tr>
											<th>Status</th>
											<th>Vehicle</th>
											<th>Parking</th>
											<th>Speed</th>
											<th data-hide="phone,tablet">Time</th>
											<th data-hide="phone,tablet">Latitude</th>
											<th data-hide="phone,tablet">Longitude</th>
											<th data-hide="phone,tablet">Direction</th>
											<th data-hide="phone,tablet">Model</th>
											<th data-hide="phone,tablet">Sim</th>
											<th data-hide="phone,tablet">Object</th>
											<th data-hide="phone,tablet">Group</th>
										</tr>
										</thead>
										<tbody>
										<?php 
										
										echo $objectlist['main'];
										?>
										</tbody>
									</table>
								</div>
                        </div>
                        </div>
</div>

    <!-- Right sidebar -->
    <div id="right-sidebar" class="animated fadeInRight" style="z-index: 5200;width: 360px!important;">

        <div class="p-m">
            <button id="sidebar-close" class="right-sidebar-toggle sidebar-button btn btn-default m-b-md" style=" margin-bottom: 0px; margin-right: 20px;"><i class="pe pe-7s-close"></i>
            </button>
			<label> <input type="checkbox" class="i-checks" id="showhidelabel" checked> Label</label>
            <div class="row m-t-sm m-b-sm">
				<input type="text" class="form-control input-sm m-b-md" id="subfilter" placeholder="Search Vehicle" style="margin-bottom:0px;">
				<div class="panel-body" style="padding-bottom: 0px;padding-top: 0px;height: calc(80vh);">
					
					<table id="subfleetdata" class="footable table table-stripped toggle-arrow-tiny" data-page-size="5" data-filter=#subfilter style="margin-bottom:0px;">
						<thead><tr>
							<th data-sort-ignore='true'><input type='checkbox' name='allvehiclecheck' id="allvehiclecheck" value='all' onchange='toggleAllCheckbox(this.value)' checked></th>
							<th data-sort-ignore='true'>Vehicle</th>
							<th data-sort-ignore='true'>Parking</th>
							<!--<th data-sort-ignore='true'>Milage</th>-->
							<th data-sort-ignore='true'>Speed</th>
						</tr>
						</thead><tbody>
						<?php
						echo $objectlist['sub'];
						?>
						</tbody>
					</table>
				</div>
            </div>
        </div>


    </div>
<div class="modal fade" id="TasAddkModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="color-line"></div>			
            <div class="row">
                <div class="col-lg-12" id="SheduleList"  style="height: calc(100vh - 200px);overflow-y: auto;">
				
                </div>
            </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="PolygonSaveModel" tabindex="-1" role="dialog"  aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="color-line"></div>
			<div class="modal-header" style="padding-top: 0;padding-bottom: 0;">
				<h4 class="modal-title">Save Polygon</h4>
				<label class="col-sm-2 control-label">Name:</label>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div class="col-sm-12"><input class="form-control" value="" id="PolygonName" name="PolygonName" type="text"/></div>
				</div>
				<br>
				<p id="polygonValidationHelp"></p>
				<br>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="btnClickSavePolygon()">Save</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="PointSaveModel" tabindex="-1" role="dialog"  aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="color-line"></div>
			<div class="modal-header" style="padding-top: 0;padding-bottom: 0;">
				<h4 class="modal-title">Save Point</h4>
				<label class="col-sm-2 control-label">Name:</label>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div class="col-sm-12"><input class="form-control" value="" id="PointName" name="PointName" type="text"/></div>
				</div>
				<br>
				<p id="pointValidationHelp"></p>
				<br>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="btnClickSavePoint()">Save</button>
			</div>
		</div>
	</div>
</div>

<!-- Vendor scripts -->
<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/sparkline/index.js"></script>
<script src="vendor/fooTable/dist/footable.all.min.js"></script>
<script src="vendor/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js"></script>
<script src="vendor/select2-3.5.2/select2.min.js"></script>


<script src="vendor/jquery-flot/jquery.flot.js"></script>
<script src="vendor/jquery-flot/jquery.flot.resize.js"></script>
<script src="vendor/jquery-flot/jquery.flot.pie.js"></script>
<script src="vendor/peity/jquery.peity.min.js"></script>
<!-- DataTables -->
<script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="vendor/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- DataTables buttons scripts -->
<script src="vendor/pdfmake/build/pdfmake.min.js"></script>
<script src="vendor/pdfmake/build/vfs_fonts.js"></script>
<script src="vendor/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="vendor/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="vendor/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="vendor/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<script src="vendor/sweetalert/lib/sweet-alert.min.js"></script>
<script src="vendor/toastr/build/toastr.min.js"></script>
<!--Draw scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.js"></script>
<script type="text/javascript" src="Leaflet/Geo/draw.js"></script>

<!-- App scripts -->
<script src="scripts/homer.js"></script>

<script>

    $(function () {
		$('#stDateDiv .input-group.date').datepicker({
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('#endDateDiv .input-group.date').datepicker({
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });
		
        $('#fleetdata').footable({ paginate: false });
		$('#subfleetdata').footable({ paginate: false });
		$('#Taskdata').footable();
		
		$(".js-source-states-2").select2();
		$('.star').change(function() {
			if(this.checked) {
			$.get( "getcoordinates.php", { reson: "starred", sysno: this.id, yesno: "1" } )
			.done(function( data ) {
				if(data == '1'){
					$(this).prop("checked", true );
				}else{
					$(this).prop("checked", false );
				}
			});
			}else{
			$.get( "getcoordinates.php", { reson: "starred", sysno: this.id, yesno: "0" } )
			.done(function( data ) {
				if(data == '1'){
					$(this).prop("checked", false );
				}else{
					$(this).prop("checked", true );
				}
			});
			}
		});
    });
	
	
$(function () {
	setInterval(function() {
		$.get( "getcoordinates.php", {reson: "smallnotification"} )
		.done(function( data ) {
			var notific =data;
			if(notific['main'] === undefined)
			{
				window.location.reload();
			}else{
				document.getElementById('notificstyle').innerHTML = notific['style'];
				document.getElementById('notific_small').innerHTML = notific['main'];
			}
		});	
	}, 900000);
});

$(document).ready(function(){
	// Toastr options
	toastr.options = {
		"debug": true,
		"newestOnTop": false,
		"positionClass": "toast-top-center",
		"closeButton": true,
		"toastClass": "animated fadeInDown",
	};
	
	
	$('#showhidelabel').on('ifChanged', function(event) {
		if(event.target.checked == true)
		{
			map.eachLayer(function(l) {
			  if (l.getTooltip) {
				var toolTip = l.getTooltip();
				if (toolTip) {
				  this.map.addLayer(toolTip);
				}
			  }
			});
		}else{
			map.eachLayer(function(l) {
			  if (l.getTooltip) {
				var toolTip = l.getTooltip();
				if (toolTip) {
				  this.map.closeTooltip(toolTip);
				}
			  }
			});
		}
	});

});
</script>
<script>
	if ("serviceWorker" in navigator) {
	  // register service worker
	  navigator.serviceWorker.register("service-worker.js");
	}
	
	
	
// Detects if device is on iOS 
const isIos = () => {
  const userAgent = window.navigator.userAgent.toLowerCase();
  return /iphone|ipad|ipod/.test( userAgent );
}
// Detects if device is in standalone mode
const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);

// Checks if should display install popup notification:
if (isIos() && !isInStandaloneMode()) {
  this.setState({ showInstallMessage: true });
}
</script>
</body>
</html>