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
    <title><?= $fgmembersite->Title1 ?> | Geo Functions</title>

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
	<script type="text/javascript" src="Leaflet/Leaflet.Editable.js"></script>

<script>
<?php $username = $fgmembersite->UserName(); ?>
var map;
var VeMarkers = L.layerGroup();
var Markers_layer;
var markers;

var RoutePath;
var markerOrigine;
var markerDestination;
var markerWaypoints = new Array();

var responseresult = new Array();
var trackingsysno;
var lineLayer;
var pointLayer;
var seekposition=0;
var polygonLayer;
var polygonLayer1;
var polygonEditor;
var user = '<?php echo $username ?>';

var Polygon1;
var polygon2;

function initialize() {

	
	if(!map){
			map = L.map('mapholder', {editable: true}).setView([52.40689760245717, -1.510605666657068], 8);

				L.tileLayer(
				'https://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}',
				//'https://map.gsupertrack.com/apimapbox.php?provider=mapbox&type=streets-v11&z={z}&x={x}&y={y}',
				{
					maxZoom: 18,
					id: 'mapbox/streets-v11'
				}).addTo(map);
				map.on('click', function(ev){
				  var latlng = map.mouseEventToLatLng(ev.originalEvent);
				  document.getElementById('pointlat').value = latlng.lat;
				  document.getElementById('pointlong').value = latlng.lng;
				});

			VeMarkers.addTo(map);
	}
	loadgeolist();
	loadgeopointlist();
	LoadListRoute();
}


function removepoints()
{
	try{
		markers.remove();
	}catch(err){
	}
}

function getandshowpoint(pointid)
{
	removepoints();
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
				showmarker(xmlhttp.responseText);
			    }
		  }
		  var url= "./geohandle.php?getpointvalues="+pointid;

	xmlhttp.open("GET",url);
	xmlhttp.send();
}

function showmarker(valuestring)
{
	if(valuestring==null)
	{
		alert('Invalide data!');
		return false;
	}
	var myObj = JSON.parse(valuestring);
	markers = L.marker([myObj.pointlat,myObj.pointlong]).addTo(map).bindPopup(myObj.geo_name);
	map.flyTo([myObj.pointlat,myObj.pointlong], 13)

}

function addpoint()
{
	if(!validatefloat(document.getElementById('pointlat').value))
	{
		alert('Latitude input is not valid!');
		return false;
	}
	if(!validatefloat(document.getElementById('pointlong').value))
	{
		alert('Longitude input is not valid!');
		return false;
	}
	if(!isCharactorValide(document.getElementById('pointname').value))
	{
		alert('Point name is not valid!');
		return false;
	}
	


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
				alert(xmlhttp.responseText);
				loadgeopointlist();
			    }
		  }
		  var url= "./geohandle.php?pointlat="+document.getElementById('pointlat').value+"&pointname="+document.getElementById('pointname').value+"&pointlong="+document.getElementById('pointlong').value+"&reson=insertpoint&user="+user;

	xmlhttp.open("GET",url);
	xmlhttp.send();
	
}

function isCharactorValide(stringset)
{
	if(stringset.trim()=="")
	{
		return false;
	}
	var format = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;

	if(format.test(stringset)){
	  return false;
	} else {
	  return true;
	}
}

function validatefloat(validatevalue)
{
	var reg = new RegExp("^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}");
	return !reg.exec(validatevalue);
}

function handleMapClick(evt)
{
var ref_this = $("ul.tabs li.active")

	if(ref_this.data("id")=='2')
	{	
	var lonlat = map.getLonLatFromViewPortPx(evt.xy);
	var converted = lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
	document.getElementById('pointlat').value = converted.lat;
	document.getElementById('pointlong').value = converted.lon;
	//alert("latitude : " + converted.lat + ", longitude : " + converted.lon);
	}

}

function onPoly1Click(){
	Polygon1.disableEdit();
	coordinates = [];
	console.log(Polygon1.getLatLngs()[0]);
	latlngs = Polygon1.getLatLngs()[0];
	console.log(latlngs.length);
	var coordinatesStr = "";
	    for (var i = 0; i < latlngs.length; i++) {
			coordinatesStr = latlngs[i].lng + "," + latlngs[i].lat + "," + "0 " + coordinatesStr;
        }
	coordinatesStr = latlngs[0].lng + "," + latlngs[0].lat + "," + "0 " + coordinatesStr;
	console.log(coordinatesStr);

	var gname=prompt("Please enter Geofence name");

	if (gname!=null)
	{
		inseart_fence(coordinatesStr, gname);
	}
}

function addfence(){
	Polygon1 = map.editTools.startPolygon();
	Polygon1.on('click', onPoly1Click);
	Polygon1.enableEdit();

}


function removefence(){
	try{
			Polygon1.remove();
		}catch(err){
	}
	try{
			polygon2.remove();
		}catch(err){
	}
}

function loadgeofence(id)
{
	removefence();
	var coordinates = document.getElementById('coord'+id).innerHTML;


	var sitePoints = [];
	//var coordinates ="79.85384129612969,6.935091338204091,0 79.85355223568668,6.933578953879044,0 79.85698645894701,6.932389058659427,0 79.85778992143993,6.933907772594215,0 79.85384129612969,6.935091338204091,0";
	var pointarray = coordinates.split(" ");
	
	for(var i=0;i < pointarray.length;i++)
	{
		var point = [];
		point.push(pointarray[i].split(",")[1]);
		point.push(pointarray[i].split(",")[0]);
		sitePoints.push(point);
	}
	console.log(sitePoints);
	polygon2 = L.polygon(sitePoints, {color: 'red'});
	polygon2.addTo(map);

	map.fitBounds(polygon2.getBounds());

		//set_centre(latlng);
		//set_map_zoom(14);
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


function loadgeolist(){
document.getElementById('geo_list').innerHTML ="";
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
				document.getElementById('geo_list').innerHTML = xmlhttp.responseText;
				$('#example1').footable();
			    }
		  }
	xmlhttp.open("GET","geohandle.php?geolist=list");
	xmlhttp.send();
}

function loadgeopointlist(){

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
				document.getElementById('geopoint_list').innerHTML = xmlhttp.responseText;
				$('#example2').footable();
			    }
		  }
	xmlhttp.open("GET","geohandle.php?geopointlist=list");
	xmlhttp.send();
}

function inseart_fence(coordinate_string, gname){

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
				alert(xmlhttp.responseText);
				loadgeolist();
				Polygon1.remove();
			    }
		  }
		  var url= "./geohandle.php?coordinates="+coordinate_string+"&gname="+gname+"&user="+user+"&reson=insert";

	xmlhttp.open("GET",url);
	xmlhttp.send();
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
function del_fence(rowid){
var del_id=document.getElementById('id'+rowid).innerHTML;
var del_name=document.getElementById('name'+rowid).innerHTML;

if (confirm('Are you sure you want to Delete '+del_name+'?')) {

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
				alert(xmlhttp.responseText);
				loadgeolist();
				loadgeopointlist();
				removepoints();
			    }
		  }
	xmlhttp.open("GET","geohandle.php?del_id="+del_id+"&reson=delete");
	xmlhttp.send();
} else {
    // Do nothing!
}
//clear_fence();
}

function addRoute()
{
	var routeName = document.getElementById('RouteName').value;
	var Origine = document.getElementById('origine').value;
	var Destination = document.getElementById('destination').value;
	var Via = $('#via').val(); 
	
	if(routeName == "")
	{
		alert("Route name can not be empty!");
	}else{
		$.post( "geohandle.php", { AddRoute:"AddRoute", RouteName: routeName, Origine: Origine , Destination: Destination, via: Via})
		  .done(function( data ) {
			alert(data );
			LoadListRoute();
		  });
	}
}

function getandshowRoute(RouteID)
{
	removeODpoints();
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

function del_Route(id)
{
	if (confirm('Are you sure you want to Delete Route?')) {
		$.post( "geohandle.php", { DelRoute:"DelRoute", RouteId: id})
		  .done(function( data ) {
			alert(data );
			LoadListRoute();
		  });
	}
}

function LoadListRoute()
{
		$.post( "geohandle.php", { ListRoute:"ListRoute"})
		  .done(function( data ) {
				document.getElementById('geoRoute_list').innerHTML = data;
				$('#tableRoute').footable();
		  });
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
                                <h5>Geo Functions</h5>
                            </div>
                            <div class="ibox-content">

                                <div class="col-lg-8" >
                                    <div class="flot-chart" style="height:80vh;">
                                        <div class="flot-chart-content" id="mapholder"></div>
                                    </div>
                                </div>
								
								
								<div class="col-lg-4">
									<div class="hpanel">
										<ul class="nav nav-tabs tabs">
											<li class="active" data-id="1" ><a data-toggle="tab" href="#tab-1">Geo Fences</a></li>
											<li class="" data-id="2" ><a data-toggle="tab" href="#tab-2">Geo Points</a></li>
											<!--
											<li class="" data-id="3" ><a data-toggle="tab" href="#tab-3">Routes</a></li>
											-->
										</ul>
										<div class="tab-content">
											<div id="tab-1" class="tab-pane active">
											<div class="panel-body">
												<div class="row">
													<div class="ibox float-e-margins">
														<div class="ibox-content">
																		<div class="btn-group">
																			<button class="btn btn-white" id="btnsearch" type="button" onclick="addfence()">Add</button>
																			<button class="btn btn-white" id="btnback" type="button" onclick="removefence()">Remove</button>
																		</div>
																		<br>
														</div>
													</div>
												</div>
												<div class="row">
												<input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search">
													<div class="panel-body" style="overflow-y:auto; height:60vh;" id="geo_list">
													<?php //echo $fgmembersite->geolist(); ?>
													</div>
												</div>
											</div>
											</div>
											<div id="tab-2" class="tab-pane">
											<div class="panel-body">
												<div class="row">
														<div class="ibox-content">
															<div class="row" style="margin-left: 0px;">
																<button class="btn btn-white" id="btnback" type="button" onclick="removepoints()">Remove points</button>
															</div>
															<div class="row" style="margin-left: 0px;">
																<form class="form-horizontal" method="post" onsubmit="return validateForm()" name="myForm">
																 <input type="hidden" name="objectsubmit" value="ok" />
																 
																<div class="form-group"><label class="col-sm-2 control-label">Name</label>
																	<div class="col-sm-8"><input class="form-control" value="" id="pointname" name="pointname" type="text"/></div>
																</div>
																<div class="form-group"><label class="col-sm-2 control-label">Latitude</label>
																	<div class="col-sm-8"><input class="form-control" value="" id="pointlat" name="pointlat" type="text"/></div>
																</div>
																<div class="form-group"><label class="col-sm-2 control-label">Longitude</label>
																	<div class="col-sm-8"><input class="form-control" value="" id="pointlong" name="pointlong" type="text"/></div>
																</div>
																<button class="btn btn-white" id="btnback" type="button" onclick="addpoint()">Submit</button>
																</form>
															</div>
														</div>
												</div>
												<div class="row">
												<input type="text" class="form-control input-sm m-b-md" id="filterpoint" placeholder="Search points">
													<div class="panel-body" style="overflow-y:auto; height:35vh;" id="geopoint_list">
													<?php //echo $fgmembersite->geolist(); ?>
													</div>
												</div>
											</div>
											</div>
											<div id="tab-3" class="tab-pane">
											<div class="panel-body">
												<div class="row">
														<div class="ibox-content">
															<div class="row" style="margin-left: 0px;">
																<form class="form-horizontal" method="post" onsubmit="return validateFormRoute()" name="myFormRoute">
																 <input type="hidden" name="routes" value="ok" />
																 
																<div class="form-group"><label class="col-sm-2 control-label">Route Name</label>
																	<div class="col-sm-8"><input class="form-control" value="" id="RouteName" name="RouteName" type="text"/></div>
																</div>
																<div class="form-group"><label class="col-sm-2 control-label">Origine</label>
																	<div class="col-sm-8">
																		<select class="js-source-states" style="width: 100%" id="origine" name="origine">
																			<?php 
																				$PointOptions = $fgmembersite->geoPointSelectOptions();
																				echo $PointOptions;
																			?>
																		</select>
																	</div>
																</div>
																<div class="form-group"><label class="col-sm-2 control-label">Destination</label>
																	<div class="col-sm-8">
																		<select class="js-source-states" style="width: 100%" id="destination" name="destination">
																			<?php
																				echo $PointOptions;
																			?>
																		</select>
																	</div>
																</div>
																<div class="form-group"><label class="col-sm-2 control-label">Via</label>
																	<div class="col-sm-8">
																		<select class="js-source-states-2" multiple="multiple" style="width: 100%" id="via" name="via[]">
																			<?php
																				echo $PointOptions;
																			?>
																		</select>
																	</div>
																</div>
																<button class="btn btn-white" id="btnAddRoute" type="button" onclick="addRoute()">Submit</button>
																</form>
															</div>
														</div>
												</div>
												<div class="row">
												<input type="text" class="form-control input-sm m-b-md" id="filterRoutes" placeholder="Search points">
													<div class="panel-body" style="overflow-y:auto; height:35vh;" id="geoRoute_list">
													<?php //echo $fgmembersite->geolistRoutes(); ?>
													</div>
												</div>
											</div>
											</div>
										</div>


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
<script src="vendor/select2-3.5.2/select2.min.js"></script>
<script src="vendor/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js"></script>
<!-- App scripts -->
<script src="scripts/homer.js"></script>


<script>

    $(function () {

        // Initialize Example 1
        $('#example1').footable();
		$(".js-source-states").select2();
		$(".js-source-states-2").select2();
        // Initialize Example 2
        //$('#example2').footable();


    });

</script>

</body>
</html>