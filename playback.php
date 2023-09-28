<?PHP
ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");

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
    <title><?= $fgmembersite->Title1 ?> | History View</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">
	<link rel="stylesheet" href="vendor/fooTable/css/footable.core.min.css" />
    <link rel="stylesheet" href="vendor/select2-3.5.2/select2.css" />
    <link rel="stylesheet" href="vendor/select2-bootstrap/select2-bootstrap.css" />	

	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
	<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
	<script type="text/javascript" src="Leaflet/antpath/leaflet-ant-path.js"></script>
	<script type="text/javascript" src="Leaflet/XplayDrawRouteSections.js"></script>

<script>
<?php
$vehiclename = "";
if(isset($_GET['vehiclename']) && $_GET['vehiclename'] != '')
{
	echo "var vehiclename = '".$_GET['vehiclename']."';";
}else{
	echo "var vehiclename = '';";
}
?>

</script>
<script type="text/javascript"  language="javascript">

var map;<?php echo "var Token='".$fgmembersite->UserToken()."';";?>
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
var seekposition=0;
var playStatus = false;
var Var_play_timer = null;
function initialize(){
	if(!map){
		
			map = L.map('mapholder').setView([43.141216, -79.610058], 8);

				L.tileLayer(
				'https://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}',
				//'https://map.gsupertrack.com/apimapbox.php?provider=mapbox&type=streets-v11&z={z}&x={x}&y={y}',
				{
					maxZoom: 18,
					id: 'mapbox/streets-v11'
				}).addTo(map);

			VeMarkers.addTo(map);

	}
	CreateVehicleMarker();
	changevehiclename(vehiclename);
}
function searchbus(){
	seekposition=0;
	webViewRefresh();
	load_bus_data();
}
function load_bus_data(){
	var date = document.getElementById('trackdate').value;
	var busno = document.getElementById('trackbus').value;
	LoadDatatoWebview(date,"","",busno);
}

function play(){
if( typeof responseresult.Vehicleinfo.length === 'undefined' || responseresult.Vehicleinfo.length === null ){
    searchbus();
}else{
	if(!playStatus)
	{
		Var_play_timer=setInterval("play_loop('f')", 300);
		playStatus = true;
	}else{
		stop_play();
	}
}
}
function stop_play(){
	if(Var_play_timer !== null)
	{
		clearInterval(Var_play_timer);
		playStatus = false;
	}
}

function back(){
	stop_play();
	play_loop('b');
}

function next(){
	stop_play();
	play_loop('f');
}

function play_loop(action){
		if(seekposition<responseresult.Vehicleinfo.length){
			UpdateVehicleLocation(responseresult.Vehicleinfo[seekposition].lat,responseresult.Vehicleinfo[seekposition].lng);
			updatecontentinfo(seekposition);
			updateSeekValue(seekposition);
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
			//stop_play();
		}
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
	stop_play();
	removeStopMarkers();
	removeNotificMarkers();
	StopMarkerIndex = 0;
	NotificMarkerIndex = 0;
	responseresult = null;
	updateSeekValue(0);
	//StopMarkers = new Array();
	
	
	$.post( "api.php?Token="+Token, { Xpassdata: "passdata", busno: vehicle, sttime: date+" 00:00:00:000", endtime: date+" 23:59:59:000" })
	  .done(function( data ) {
		  ShowRoutebyJson(data);
		  responseresult=data;
		  activatebtnclass();
		  play();
	});
	$.post( "api.php?Token="+Token, { XpassdataNotific: "passdata", busno: vehicle, sttime: date+" 00:00:00:000", endtime: date+" 23:59:59:000" })
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

function updateSeekValue(seekValue)
{
	document.getElementById("seekbar").value = seekValue;
}

function updatecontentinfo(position){
	//alert("Time: "+responseresult.Vehicleinfo[position].time);
	
	document.getElementById('timeinfo').innerHTML = responseresult.Vehicleinfo[position].time;
	document.getElementById('speedinfo').innerHTML = responseresult.Vehicleinfo[position].Velocity;
	//document.getElementById('milageinfo').innerHTML = (responseresult.Vehicleinfo[position].Miles-responseresult.Vehicleinfo[0].Miles)+" Km";
	document.getElementById('milageinfo').innerHTML = responseresult.Vehicleinfo[position].Miles;
	document.getElementById('fuelinfo').innerHTML = responseresult.Vehicleinfo[position].Oil;
	document.getElementById('parking').innerHTML = responseresult.Vehicleinfo[position].park;
	document.getElementById('status').innerHTML = responseresult.Vehicleinfo[position].status;
	
}
function disablebtnclass(){
	document.getElementById("btnplay").className = "btn btn-primary disabled";
	document.getElementById("seekbar").max = 0;
}

function activatebtnclass(){
	document.getElementById("btnplay").className = "btn btn-primary active";
	document.getElementById("seekbar").max = (responseresult.Vehicleinfo.length-1);
}
function changevehiclename(vehiclename)
{
	if(vehiclename != '')
	{
	   $('select[name=selValue]').val(vehiclename);
	   $('select[name=selValue]').change();
	}
}

</script>
<script>


function showVal(seekValue)
{
	seekposition = seekValue;
	updateSeekValue(seekValue);
	
	UpdateVehicleLocation(responseresult.Vehicleinfo[seekposition].lat,responseresult.Vehicleinfo[seekposition].lng);
	updatecontentinfo(seekposition);
	console.log(seekValue);
}
</script>
</head>
<body onload="initialize()" class="fixed-navbar sidebar-scroll">

<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>Homer - Responsive Admin Theme</h1><p>Special Admin Theme for small and medium webapp with very clean and aesthetic style and feel. </p><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

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
        <form role="search" class="navbar-form-custom" method="post" action="#">
            <div class="form-group">
                
            </div>
        </form>
        <div class="mobile-menu">
            <button type="button" class="navbar-toggle mobile-menu-toggle" data-toggle="collapse" data-target="#mobile-collapse">
                <i class="fa fa-chevron-down"></i>
            </button>
            <div class="collapse mobile-navbar" id="mobile-collapse">
                <ul class="nav navbar-nav">
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

<div class="content">

    <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5>Back Track Monitoring</h5>
                            </div>
                            <div class="ibox-content">

                                <div class="col-lg-9" >
                                    <div class="flot-chart" style="height:80vh;">
                                        <div class="flot-chart-content" id="mapholder"></div>
                                    </div>
                                </div>
								
									<div class="col-lg-3">
										<div class="ibox float-e-margins">
										<hr>
											<div class="ibox-title">
												<span class="label label-success pull-right" id="status"  style="font-size:16px"></span>
												<h4>Tracking Info</h4>
											</div>
											<div class="ibox-content">
											<table class="table table-striped">
											<tr>
											<td height="70px"><label class="font-normal">Time:-</label></td><td><label class="font-normal"><span id="timeinfo"></span></label></td>
											</tr>
											<tr>
											<td><label class="font-normal">Parking:-</label></td><td><label class="font-normal"><span id="parking"></span></label></td>
											</tr>
											<tr>
											<td><label class="font-normal">Milage:-</label></td><td><label class="font-normal"><span id="milageinfo"></span></label></td>
											</tr>
											<tr>
											<td><label class="font-normal">Fuel:-</label></td><td><label class="font-normal"><span id="fuelinfo"></span></label></td>
											</tr>
											<tr>
											<td><label class="font-normal">Speed:-</label></td><td><label class="font-normal"><span id="speedinfo"></span></label></td>
											</tr>
											</table>
												<small></small>
											</div>
										</div>
									</div>							

									<div class="col-lg-3">
										<div class="ibox float-e-margins">
										<hr>
											<div class="ibox-title">
												<h4>Play Back & Monitoring</h4>
											</div>
											<div class="ibox-content">
															<small></small>
															<div class="form-group" id="data_1">
																<label class="font-normal">Enter the date and Search</label>
																<div class="input-group date">
																	<span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" id="trackdate"  autocomplete="off">
																</div>
															</div>
															<div class="form-group">
																<p>
																	 Select your Vehicle
																</p>
																<select class="select2_demo_2 form-control" id="trackbus" name="selValue">
																	<?= $fgmembersite->VehicleListOptions(); ?>
																</select>
															</div>
															<div class="form-group">
																<button class="btn btn-primary" id="btnsearch" type="button" onclick="searchbus()">Search</button>
																<button class="btn btn-primary disabled" id="btnplay" type="button"onclick="play()">Play/Stop</button>
															</div>
															<div class="form-group">
																<input type="range" class="form-range" min="0" max="5" step="1" id="seekbar" oninput="showVal(this.value)"  onchange="showVal(this.value)">
															</div>
															<br>
											</div>
											<hr>
										</div>
									</div>


                                </div>

                            </div>
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

<!-- App scripts -->
<script src="scripts/homer.js"></script>


<script>

    $(function () {
		$('#data_1 .input-group.date').datepicker({
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		$('#data_1 .input-group.date').datepicker("setDate", new Date());
		$(".select2_demo_2").select2();
        // Initialize Example 1
        $('#example1').footable();

        // Initialize Example 2
        $('#example2').footable();


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
</script>

</body>
</html>