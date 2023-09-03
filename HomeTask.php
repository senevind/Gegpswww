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
    <title>G supertrack | Tracking View</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

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

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
	<script type="text/javascript" src="Leaflet/MovingMarker.js"></script>
	<script type="text/javascript" src="Leaflet/antpath/leaflet-ant-path.js"></script>
	
	<script> var Vehicleinfo =  <?php
		$objectlist = $fgmembersite->getbuslisttablearraySTD();
		echo $objectlist['dataset']; 
	?>
	</script>

	<script>
	var userName = '<?php echo $fgmembersite->UserName()?>';
	</script>
<script>

var map;
var VeMarkers = L.layerGroup();
var markers = new Array();
var infocontent = {};
var responseresult;
var objectnewestvalues = {};
var Geomarkers = new Array();
var Geopolygon = new Array();

var RoutePath;
var markerOrigine;
var markerDestination;
var markerWaypoints = new Array();
var TrackerMarker;

var TrackingID = "";
var TaskJsonData;
var CurrentTask = null;


function initialize() {
	
	if(!map){
		
			map = L.map('mapholder').setView([7.293487,80.641021], 8);

				L.tileLayer(
				//'https://map.gsupertrack.com/apimapbox.php?provider=mapbox&type=streets-v11&z={z}&x={x}&y={y}',
				'https://mt0.google.com/vt/lyrs=m&hl=en&x={x}&y={y}&z={z}',
				{
					maxZoom: 18,
					id: 'mapbox/streets-v11'
				}).addTo(map);

			VeMarkers.addTo(map);

	}
	UpdateTaskTable();
	TaskSheduleTaskStarted();
	setInterval('UpdateCurrentTaskProgress()', 30000);
	
}

function addTask()
{
	var taskName = document.getElementById('TaskName').value;
	var Route = document.getElementById('Route').value;
	var tracker = document.getElementById('tracker').value;
	var stDate = document.getElementById('stDate').value;
	var endDate = document.getElementById('endDate').value;

	
	if(taskName == "")
	{
		alert("Task name can not be empty!");
	}else{
		$.post( "geohandle.php", {TaskAdd:"TaskAdd", routeID:Route, TaskName: taskName, TrackerID: tracker , stDate: stDate, endDate: endDate, UserName:userName})
		  .done(function( data ) {
			alert(data );
			$('#TasAddkModal').modal('hide')
			UpdateTaskTable();
		  });
	}
}
function UpdateTaskTable()
{
	$.post( "geohandle.php", {UpdateTaskTable:"UpdateTaskTable",UserName:userName})
	  .done(function( data ) {
		  TaskJsonData = JSON.parse(data);
		document.getElementById('TaskPanel').innerHTML = TaskJsonData.content;
	  }, "json");
}
function del_Task(taskID)
{
	if (confirm("Are you sure you want to delete this task?")) {
		$.post( "geohandle.php", {del_Task:"del_Task",TaskID:taskID,UserName:userName})
		  .done(function( data ) {
			  UpdateTaskTable();
		  });
	}
}

function TaskSheduleTaskStarted()
{
	//$.post( "geohandle.php", { GetRoute:"GetRoute", RouteID:'12'})
		$.post( "geohandle.php", {SheduleTaskStarted:"SheduleTaskStarted",TrackerID:"94716144466"})
		  .done(function( data ) {
			//console.log(data);
		  });
}

function ShowTaskRoute(RouteID,TaskID)
{
	removeODpoints();
	$('#TaskModal').modal('hide')
	$.post( "geohandle.php", { GetRoute:"GetRoute", RouteID:RouteID})
	  .done(function( data ) {
		ShowRoutebyJson(data);
		CreateRouteMarkers(data);
		TaskProgress(TaskID);
		CurrentTask = TaskID;
	  });
}

function UpdateCurrentTaskProgress()
{
	if(CurrentTask != null)
	{
		TaskProgress(CurrentTask);
	}
}

function TaskProgress(TaskID)
{
	var TrackerID = TaskJsonData.contentjson[TaskID].TrackerID;
	var StartTImeUTC = TaskJsonData.contentjson[TaskID].StartTImeUTC;
	var CompleteTimeUTC = TaskJsonData.contentjson[TaskID].CompleteTimeUTC;
	
	ShowTaskProgress(TrackerID,StartTImeUTC,CompleteTimeUTC,markerOrigine,markerOrigine.getLatLng().lat,markerOrigine.getLatLng().lng);
	ShowTaskProgress(TrackerID,StartTImeUTC,CompleteTimeUTC,markerDestination,markerDestination.getLatLng().lat,markerDestination.getLatLng().lng);
	
	for(var i=0;i<markerWaypoints.length;i++)
	{
		ShowTaskProgress(TrackerID,StartTImeUTC,CompleteTimeUTC,markerWaypoints[i],markerWaypoints[i].getLatLng().lat,markerWaypoints[i].getLatLng().lng);
	}
	
	TrackerMarkerUpdate(TaskID);
}

function TrackerMarkerUpdate(TaskID)
{
	TrackerMarker.setIcon(L.icon({iconUrl: 'mapicon/Playback/Xplayback.png',iconSize: [50, 50],iconAnchor: [25, 50]}));
	TrackerMarkerLocationUpdate(TaskID);
	//TrackerMarker.bindTooltip(TaskJsonData.contentjson[TaskID].TrackerID,{permanent: true,direction: 'top'})
	//TrackerMarker.setIcon(L.icon({iconUrl: 'mapicon/other/progCompl.png'}));
	//alert("Tracker marker updated");
}

function TrackerMarkerLocationUpdate(TaskID)
{
	var sysno = TaskJsonData.contentjson[TaskID].TrackerID;
	$.post( "getcoordinates.php?sysnostring="+sysno, {sysno:sysno})
	  .done(function( TaskJsonData ) {
			responseresult = JSON.parse(TaskJsonData);
			for(var i=0; i< responseresult.geocordinates.length; i++){
					var newLatLng = new L.LatLng(responseresult.geocordinates[i].lastlati,responseresult.geocordinates[i].lastlng);
					TrackerMarker.setLatLng(newLatLng);
					
			}
	  });
}

function ShowTaskProgress(TrackerID,StartTImeUTC,CompleteTimeUTC,Marker,PointLat,PointLong)
{
	if(StartTImeUTC == null || StartTImeUTC.trim() == "")
	{
		return 0;
	}
	$.post( "geohandle.php", { TaskProgress:"TaskProgress", TrackerID:TrackerID,StartTImeUTC:StartTImeUTC,CompleteTimeUTC:CompleteTimeUTC,PointLat:PointLat,PointLong:PointLong})
	  .done(function( TaskJsonData ) {
		  //TaskJsonData = JSON.parse(data);
		  if(TaskJsonData.status == true)
		  {
			  var progressCompleteIcon = L.icon({
									iconUrl: 'mapicon/other/progCompl.png'
								});
			  Marker.setIcon(progressCompleteIcon);
		  }
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
	TrackerMarker = L.marker([0,0]).addTo(map);
	markerOrigine = L.marker([data.Origine.pointlat,data.Origine.pointlong]).addTo(map).bindTooltip(data.Origine.geo_name,{permanent: true,direction: 'right'});
	markerDestination = L.marker([data.Destination.pointlat,data.Destination.pointlong]).addTo(map).bindTooltip(data.Destination.geo_name,{permanent: true,direction: 'right'});

	for(var i=0;i<data.wayPoints.length;i++)
	{
		markerWaypoints[i] = L.marker([data.wayPoints[i].pointlat,data.wayPoints[i].pointlong]).addTo(map).bindTooltip(data.wayPoints[i].geo_name,{permanent: true,direction: 'right'});
	}
}

function removeODpoints()
{
	CurrentTask = null;
	
	if(RoutePath != undefined)
	{
		map.removeLayer(RoutePath);
	}
	try{
		TrackerMarker.remove();
	}catch(err){
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


function ShowAddTaskModel()
{
	$('#TasAddkModal').modal('show')
}
</script>
</head>
<body onload="initialize()" class="fixed-navbar sidebar-scroll" style="margin: 0; height: 100%; overflow: hidden">
<!-- Skin option / for demo purpose only -->
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
            G supertrack
        </span>
    </div>
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo">
            <span class="text-primary">G supertrack</span>
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
		<div id="TaskMapview" class="p-m tab-pane active" style="height: calc(100vh - 60px); padding:0px">
			<div class="ibox-content">
				<div class="col-lg-9" >
					<div class="flot-chart" style="height:85vh;">
						<div class="flot-chart-content" id="mapholder"></div>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="hpanel panel-group" id="accordion" role="tablist" aria-multiselectable="true">
						<div class="panel-body">
							<b style="font-size: 20px;">Task Manager</b>
							<button onclick="ShowAddTaskModel()" class="btn btn-primary btn-circle pull-right" type="button"><i class="fa fa-plus"></i></button>
						</div>
						<div id="TaskPanel" style="scrollbar-width: thin; height:75vh; overflow-y: auto;">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="TaskEditor" class="p-m tab-pane" style="height: calc(100vh - 60px); padding:0px;overflow-y: scroll;">
			<div class="panel-body">
				<div class="row">
						<div  class="col-lg-10">

						</div>
				</div>
			</div>
			<div class="hpanel">
				<div class="panel-heading">
					<div class="panel-tools">
						<a class="showhide"><i class="fa fa-chevron-up"></i></a>
						<a class="closebox"><i class="fa fa-times"></i></a>
					</div>
					Task Data
				</div>
				<div class="panel-body">
					<?php
						//echo geolistTasks($fgmembersite);
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="TasAddkModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="color-line"></div>			
            <div class="row">
                <div class="col-lg-12" style="height: calc(100vh - 200px);overflow-y: auto;">
					<div class="hpanel">
						<div class="panel-heading">
							<div class="panel-tools">
								<a class="showhide"><i class="fa fa-chevron-up"></i></a>
							</div>
							Add Task
						</div>
						<div class="panel-body">
							<form class="form-horizontal" method="post" onsubmit="return validateFormRoute()" name="myFormRoute">
								 <input type="hidden" name="TaskAdd" value="ok" />
								 
								<div class="form-group"><label class="col-sm-3 control-label">Task Name</label>
									<div class="col-sm-9"><input class="form-control" value="" id="TaskName" name="TaskName" type="text"/></div>
								</div>
								<div class="form-group"><label class="col-sm-3 control-label">Route</label>
									<div class="col-sm-9">
										<select class="js-source-states-2" style="width: 100%" id="Route" name="Route">
											<?php echo RouteSelectOptions($fgmembersite); ?>
										</select>
									</div>
								</div>
								<div class="form-group"><label class="col-sm-3 control-label">Tracker</label>
									<div class="col-sm-9">
										<select class="js-source-states-2" style="width: 100%" id="tracker" name="tracker">
											<?php echo $objectlist['selectOptions']; ?>
										</select>
									</div>
								</div>
								<div class="form-group" id="stDateDiv">
									<label class="col-sm-3 control-label">Start Date</label>
									<div class="input-group date col-sm-9" style="padding: 0px 15px 0px 15px;">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="text" class="form-control" id="stDate"  autocomplete="off">
									</div>
								</div>
								<div class="form-group" id="endDateDiv">
									<label class="col-sm-3 control-label">End Date</label>
									<div class="input-group date col-sm-9" style="padding: 0px 15px 0px 15px;">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										<input type="text" class="form-control" id="endDate"  autocomplete="off">
									</div>
								</div>
								<div class="form-group" id="endDateDiv">
									<label class="col-sm-2 control-label"></label>
									<button class="btn btn-white" id="btnAddRoute" type="button" onclick="addTask()">Submit</button>
								</div>
							</form>
						</div>
					</div>
                </div>
            </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
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

			document.getElementById('notificstyle').innerHTML = notific['style'];
			document.getElementById('notific_small').innerHTML = notific['main'];
		});	
	}, 900000);
});

$(document).ready(function(){
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

</body>
</html>