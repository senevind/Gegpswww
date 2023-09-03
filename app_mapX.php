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

//print_r($_POST);
if(isset($_GET['username']))
{
	$_POST['username'] = $_GET['username'];
	$_POST['password'] = $_GET['password'];
}


ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");
require_once("./include/Routes.php");
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
//print_r($_GET);
//exit;
?>
<!doctype html>
<html>
<head>
<script> var Vehicleinfo =  <?= $fgmembersite->getbuslistarray(); ?> </script>

<link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />
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
	
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
<script type="text/javascript" src="Leaflet/MovingMarker.js"></script>
<script type="text/javascript" src="Leaflet/antpath/leaflet-ant-path.js"></script>

<style>
.map {
	/*
    padding: 0;
    position:relative;
    width:100% ;
    height: 100%;
	*/
}

.mapContainer{
        width: 100vw;
        height: 100vh; /* Fallback for browsers that do not support Custom Properties */
        /* height: calc(var(--vh, 1vh) * 92.6); */
        padding: 0 ;
}


.mainMenu {
	/*
    padding: 6px 8px;
    font: 14px/16px Arial, Helvetica, sans-serif;
    background: white;
    background: rgba(255,255,255,0.8);
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
    border-radius: 5px;
	*/
    min-width: 300px;
}

/* width */
::-webkit-scrollbar {
  width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
  background: #f1f1f1; 
}
 
/* Handle */
::-webkit-scrollbar-thumb {
  background: #888; 
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
  background: #555; 
}

</style>
<script type="text/javascript"  language="javascript">
var map;
var VeMarkers = L.layerGroup();
var markers = new Array();
var infocontent = {};
var responseresult;
var objectnewestvalues = {};
var Geomarkers = new Array();
var Geopolygon = new Array();
var MarkerBounds = [];

var displayOffline = true;
var displayStopped = true;
var displayIdle = true;
var displayRunning = true;
var searchtextfixed = "";

var MarkerCurrentLocation = null;
var currentlat = 0;
var currentlng = 0;

var RoutePath;
var markerOrigine;
var markerDestination;
var markerWaypoints = new Array();

var TrackingID = "";
var ForcusDevID = "";
function initialize(){
	if(!map){
		
			map = L.map('mapholder').setView([52.40689760245717, -1.510605666657068], 8);

				L.tileLayer(
				//'https://map.gsupertrack.com/apimapbox.php?provider=mapbox&type=streets-v11&z={z}&x={x}&y={y}',
				'https://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}',
				{
					maxZoom: 18,
					id: 'mapbox/streets-v11'
				}).addTo(map);

			VeMarkers.addTo(map);
			loadvehiclelist();
			SetMarkerBounds();
			
			marker_refresh();
			setInterval('marker_refresh()', 10000);
			
	}
}


function loadvehiclelist(){
	
		for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
			if(Vehicleinfo.Vehicleinfo[i].lastlati !=null && Vehicleinfo.Vehicleinfo[i].lastlati != ""){
				set_marker_array(Vehicleinfo.Vehicleinfo[i].objsysno,Vehicleinfo.Vehicleinfo[i].objname,Vehicleinfo.Vehicleinfo[i].lastlati,Vehicleinfo.Vehicleinfo[i].lastlng,Vehicleinfo.Vehicleinfo[i].lastdirection,Vehicleinfo.Vehicleinfo[i].lastvelosity,Vehicleinfo.Vehicleinfo[i].Status);
				LoadFocusGroup(Vehicleinfo.Vehicleinfo[i].lastlati,Vehicleinfo.Vehicleinfo[i].lastlng);
			}
		}
		forcusDeviceSet();
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

function set_marker_array(sysno,detail,lat,lng,direction,velocity,Status){
	
markers[sysno] = L.Marker.movingMarker([[lat,lng]],[5000],{id:sysno}).addTo(VeMarkers).bindTooltip("<b>"+detail+"</b>",{permanent: true,direction: 'right',offset:L.point(25, -25)}).bindPopup(setinfocontent(sysno,Vehicleinfo.Vehicleinfo),{offset: [0, -50]}).on('click', function(e) 
{
	console.log(e.latlng);
	console.log(this.options.id);
	setinfocontentPopup(this.options.id,objectnewestvalues[this.options.id]);
});
console.log(sysno);
MarkerSetIcon(markers[sysno],lat,lng,direction,velocity,Status,sysno);
markers[sysno].bindPopup(setinfocontent(sysno,Vehicleinfo.Vehicleinfo));


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
	//Android.showToast("Vehicle count "+Vehicleinfo.Vehicleinfo.length);
	for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
		try{
			if(objectnewestvalues[Vehicleinfo.Vehicleinfo[i].objsysno].status == displaystatus)
			{
				
				if(onoff == '1')
				{
					markerdisplayon(Vehicleinfo.Vehicleinfo[i].objsysno);
				}else{
					markerdisplayoff(Vehicleinfo.Vehicleinfo[i].objsysno);
				}
			}
		}catch(err)
		{
			alert(err.message);
		}
	}
	//Android.showToast("loop count "+j);
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
SetSheduleList();
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
	xmlhttp.open("GET","getcoordinates.php?sysnostring="+sysno);
	xmlhttp.send();
}

function markers_set_position(updatedresult,sysno){
	//alert(responseresult.geocordinates[0].lastdirection);

if(searchtextfixed == "")
{
	//show_all_markers();


if(displayOffline)
{
	try{
		if(updatedresult.status == "0")
		{
			markerdisplayon(sysno);
		}
	}catch(err)
	{
	}
}else{
		try{
		if(updatedresult.status == "0")
		{
			markerdisplayoff(sysno);
		}}catch(err)
		{
		}
}

if(displayStopped)
{
		try{
		if(updatedresult.status == "1")
		{
			markerdisplayon(sysno);
		}}catch(err)
		{
		}
}else{
		try{
		if(updatedresult.status == "1")
		{
			markerdisplayoff(sysno);
		}}catch(err)
		{
		}
}

if(displayIdle)
{
		try{
		if(updatedresult.status == "2")
		{
			markerdisplayon(sysno);
		}}catch(err)
		{
		}
}else{
		try{
		if(updatedresult.status == "2")
		{
			markerdisplayoff(sysno);
		}}catch(err)
		{
		}
}

if(displayRunning)
{
		try{
		if(updatedresult.status == "3" || updatedresult.status == "4")
		{
			markerdisplayon(sysno);
		}}catch(err)
		{
		}
}else{
		try{
		if(updatedresult.status == "3" || updatedresult.status == "4")
		{
			markerdisplayoff(sysno);
		}}catch(err)
		{
		}
}
}
else{
	//show_vehicles(searchtextfixed);
}
}

function MoveMarker(sysno,detail,lat,lng,direction,velocity,imagefolder,Status){
	MarkerMove(markers[sysno],lat,lng,direction,velocity,Status,sysno);
	//markers[sysno].bindPopup(setinfocontent(sysno,objectnewestvalues[sysno]));

	var popup = markers[sysno].getPopup();

		if(popup.isOpen())
		{
			setinfocontentPopup(sysno,objectnewestvalues[sysno]);
			//load_revgeoaddress(sysno,lat,lng);
		}
}

function MarkerMove(marker,lat,lng,Angle,Velocity,Status,sysno)
{
	MarkerSetIcon(marker,lat,lng,Angle,Velocity,Status,sysno);
	var newLatLng = new L.LatLng(lat,lng);
	//marker.setLatLng(newLatLng);
	marker.moveTo(newLatLng, 5000);
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
		try{
			if(0<=Vehicleinfo.Vehicleinfo[i].objname.search(new RegExp(searchtxt,"i")))
			{
				markerdisplayon(Vehicleinfo.Vehicleinfo[i].objsysno);
			}
			else{
				markerdisplayoff(Vehicleinfo.Vehicleinfo[i].objsysno);
			}
		}catch(e){
			//alert(Vehicleinfo.Vehicleinfo[i].objname+"  "+ e.message);
		}
	}
}

function show_all_markers()
{
	for(var i=0;i<Vehicleinfo.Vehicleinfo.length;i++){
		try{
		markerdisplayon(Vehicleinfo.Vehicleinfo[i].objsysno);
		}catch(e)
		{
		}
	}
	
}

function markerdisplayoff(markerno){
	if(markers[markerno] != undefined){
		map.removeLayer(markers[markerno]);
	}
}

function markerdisplayon(markerno){

		markers[markerno].addTo(map);
}

function myFunction(){
	Running(status)
	alert('checked');
	
}

function getspeed(sysno)
{
	return Vehicleinfo.Vehicleinfosys[sysno].lastvelosity;

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
			document.getElementById('popup-lastmilage-'+sysno).innerHTML = parseInt(TaskJsonData.values.milage*0.621371)+" Miles";
}

function UpdateMarkerPopupContent(sysno)
{
	const d = new Date();
	var month = d.getMonth()+1;
	var formatedMont = ("0" + month).slice(-2);
	var formatedDate = ("0" + d.getDate()).slice(-2);
	var DateStr = d.getFullYear()+"-"+formatedMont+"-"+formatedDate;
	$.post( "api.php", { dawiseSummery:"dawiseSummery", date:DateStr , objectName:Vehicleinfo.Vehicleinfosys[sysno].objname})
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
	document.getElementById('popup-lastvelosity-'+objsysno).innerHTML = parseInt(geocordinates.lastvelosity*0.621371);
	document.getElementById('popup-parking-'+objsysno).innerHTML = geocordinates.parking;
	document.getElementById('popup-status-'+objsysno).innerHTML = status;
	document.getElementById('popup-Time-'+objsysno).innerHTML = geocordinates.Time;

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
										"<small class='stats-label'>Speed Mph</small>"+
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
									"<div class='col-xs-6'>"+
										"<small class='stats-label'><i class='fa fa-clock-o' aria-hidden='true'></i> Time : </small><br>"+
										"<small id='popup-Time-"+objsysno+"'>"+
										geocordinates.Time+
										"</small>"+
									"</div>"+
									"<div class='col-xs-6'>"+
										"<small class='stats-label'><i class='fa fa-certificate' aria-hidden='true'></i> Status : </small><br>"+
										"<small id='popup-ExpStatus-"+objsysno+"'>"+
										Vehicleinfo.Vehicleinfosys[objsysno].ExpStatus+
										"</small>"+
									"</div>"+
								"</div>"+
						"</div>"+
            "</div>"+
            "<div class='panel-footer'>"+
				"<div class='btn-group'>"+
					//"<button class='btn btn-primary' type='button'>Left</button>"+
					//"<button class='btn btn-primary ' type='button' onclick=window.open('playback.php?vehiclename="+Vehicleinfo.Vehicleinfosys[objsysno].objname+"')><i class='fa fa-undo'></i> Playback</button>"+
					//"<button class='btn btn-primary' type='button'>Right</button>"+
				"</div>"+
            "</div>"+
        "</div>"+
    "</div>"+
	"</div>"+
	"</div>";
	return content;
}

	function setmyLocation()
	{
		if(currentlat !=0 && currentlng != 0)
		{
			map.flyTo([currentlat,currentlng], 14);
		}
	}

	function setCurrentLocationMarker(lastlng,lastlati)
	{

		var Icon = L.icon({
		iconUrl: 'mapicon/other/CurrectLocationMarker.gif',
		iconSize: [30, 30],
		iconAnchor: [15, 15],
		popupAnchor: [0, 0]});	
		
		MarkerCurrentLocation = L.marker([currentlat,currentlng]).addTo(map);

		MarkerCurrentLocation.setIcon(Icon);
		//map.flyTo([currentlat,currentlng], 14)
	
	}

	function moveCurrentLocationMarker(lastlngt,lastlatit)
	{
	currentlat = lastlatit;
	currentlng = lastlngt;

		
		if(!MarkerCurrentLocation)
		{
			setCurrentLocationMarker(currentlat,currentlng);
		}

		
			var newLatLng = new L.LatLng(currentlat,currentlng);
			//marker.setLatLng(newLatLng);
			//MarkerCurrentLocation.moveTo(newLatLng, 2000);
			MarkerCurrentLocation.setLatLng(newLatLng);
			
	}
	function SetTrackingID(TrackingIDDev)
	{
		TrackingID = TrackingIDDev;
		SetSheduleList();
	}
	
	function SetSheduleList()
	{
		if(TrackingID != "")
		{
		$.post( "geohandle.php", {SheduleList:TrackingID})
		  .done(function( data ) {
			document.getElementById('SheduleList').innerHTML = data;
		  });
		}else{
			//alert("TrackingID not set");
		}
		
	}
	
	function CompleteTask(id)
	{
		$.post( "geohandle.php", {CompleteTask:id})
		  .done(function( data ) {
			if(data == "Task updated")
			{
				document.getElementById("compltsk"+id).style.display = "none";
				document.getElementById("completed"+id).style.display = "block";
			}
		  });
	}
	
	function StartTask(id)
	{
		$.post( "geohandle.php", {StartTask:id})
		  .done(function( data ) {
			if(data == "Task updated")
			{
				document.getElementById("statsk"+id).style.display = "none";
				document.getElementById("compltsk"+id).style.display = "block";
			}
		  });

	}
	
	function ShowTaskRoute(RouteID)
	{
		removeODpoints();
		$('#TaskModal').modal('hide')
		$.post( "geohandle.php", { GetRoute:"GetRoute", RouteID:RouteID})
		  .done(function( data ) {
			ShowRoutebyJson(data);
			CreateRouteMarkers(data);
			
		  });
	}
	
	function ShowRoutebyJson(data)
	{
		var CoordinatesArray = data.RoutePath;
		AddPathToMap(CoordinatesArray);
	}

	function AddPathToMap(linecordinates)
	{
		RoutePath = CreatePath(linecordinates);
		map.addLayer(RoutePath);
		map.fitBounds(RoutePath.getBounds());
	}

	function CreatePath(linecordinates)
	{
		const path = L.polyline.antPath(pathArray(linecordinates), {
		  "delay": 400,
		  "dashArray": [10,20],
		  "weight": 5,
		  "color": "#0000FF",
		  "pulseColor": "#FFFFFF",
		  "paused": false,
		  "reverse": false,
		  "hardwareAccelerated": true
		});
		return path;
	}

	function pathArray(line_json){
	var points =[];
	var LatLng = [];
		for(var i=0; i< line_json.length; i++){
			
					LatLng = [];
					LatLng.push(line_json[i].lng,line_json[i].lat);
					points.push(LatLng);
		}
	return points;
	}

	function CreateRouteMarkers(data)
	{
		markerOrigine = L.marker([data.Origine.pointlat,data.Origine.pointlong]).addTo(map).bindTooltip(data.Origine.geo_name,{permanent: true,direction: 'right'});
		markerDestination = L.marker([data.Destination.pointlat,data.Destination.pointlong]).addTo(map).bindTooltip(data.Destination.geo_name,{permanent: true,direction: 'right'});

		for(var i=0;i<data.wayPoints.length;i++)
		{
			markerWaypoints[i] = L.marker([data.wayPoints[i].pointlat,data.wayPoints[i].pointlong]).addTo(map).bindTooltip(data.wayPoints[i].geo_name,{permanent: true,direction: 'right'});
		}

	}

	function removeODpoints()
	{
		if(RoutePath != undefined)
		{
			map.removeLayer(RoutePath);
		}
		try{
			markerOrigine.remove();
		}catch(err){
		}
		try{
			markerDestination.remove();
		}catch(err){
		}
		try{
			for(var i=0;i<markerWaypoints.length;i++)
			{
				markerWaypoints[i].remove();
			}
		}catch(err){
		}
	}
	function forcusDeviceSet()
	{
		setTimeout(function(){
					if(ForcusDevID != "")
					{
						markers[ForcusDevID].openPopup();
						setinfocontentPopup(ForcusDevID,objectnewestvalues[ForcusDevID]);
						ForcusDevID = "";
					}
				}, 1000); 
	}
	function forcusIdSet(DeviceID)
	{
		ForcusDevID = DeviceID;
	}
	
	function load_revgeoaddress(sysno,lat,lng){
		$.get( "https://maps.googleapis.com/maps/api/geocode/json", { latlng:lat+","+lng, key:"AIzaSyBnhJFh6bcguKMn_rE6C1HdWnups0-KelY"})
		  .done(function( TaskJsonData ) {
			  if(TaskJsonData.status=="OK")
			  {
				  document.getElementById('popup-revgeocode-'+sysno).innerHTML = TaskJsonData.results[0].formatted_address;
			  }
	  });
	}
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

</head>

<body onload="initialize()"  class="fixed-navbar sidebar-scroll hide-sidebar" style="margin: 0; height: 100%; overflow: hidden">
<!--map division-->
<!--<button onclick="offline('checked')">Click me</button>-->
<header style="height: calc(100vh - 0px); padding:0px;">
	<div class="container-fluid" >
		<div class="row">
			<div class="col-sm-10 mapContainer" id="mapholder">
			</div>
		</div>

	</div>
</header>

<div class="modal fade" id="TaskModal" tabindex="-1" role="dialog" aria-hidden="true">
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

<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>


<script src="vendor/jquery-flot/jquery.flot.js"></script>
<script src="vendor/jquery-flot/jquery.flot.resize.js"></script>
<script src="vendor/jquery-flot/jquery.flot.pie.js"></script>
<script src="vendor/peity/jquery.peity.min.js"></script>

</body>


</html>