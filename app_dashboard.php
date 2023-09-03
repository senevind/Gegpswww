<?PHP
ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");
$fgmembersite->LogOut();
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

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>SASINDU | Dashboard</title>

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

<script>

function load(reson){
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
				if(reson=='onlinestatus'){
					onlinestatus(xmlhttp.responseText);
				}
				if(reson=='vcount'){
					vcount(xmlhttp.responseText);
				}
				if(reson=='comvcount'){
					comvcount(xmlhttp.responseText);
				}
				if(reson=='nocomvcount'){
					nocomvcount(xmlhttp.responseText);
				}
				if(reson=='stpvcount'){
					stpvcount(xmlhttp.responseText);
				}
				if(reson=='idlevcount'){
					idlevcount(xmlhttp.responseText);
				}
				if(reson=='runnivcount'){
					runnivcount(xmlhttp.responseText);
				}
				if(reson=='alltodaymilage'){
					alltodaymilage(xmlhttp.responseText);
				}
				}
		  }
	xmlhttp.open("GET","getcoordinates.php?reson="+reson);
	xmlhttp.send();
}

function initialize()
{
	dashboard_refresh();
	setInterval('dashboard_refresh()', 45000);
	
}
function dashboard_refresh()
{
	//load('onlinestatus');
	load('vcount');
	load('comvcount');
	load('stpvcount');
	load('nocomvcount');
	load('idlevcount');
	load('runnivcount');
	load('alltodaymilage');
}
</script>
</head>
<body onload="initialize()" style="padding-top: 0px;" class="fixed-nav fixed-sidebar">

<style>
    .skin-option { position: fixed; text-align: center; right: -1px; padding: 10px; top: 80px; width: 150px; height: 133px; text-transform: uppercase; background-color: #ffffff; box-shadow: 0 1px 10px rgba(0, 0, 0, 0.05), 0 1px 4px rgba(0, 0, 0, .1); border-radius: 4px 0 0 4px; z-index: 100; }
</style>

<!-- End skin option / for demo purpose only -->

    <div class="content">

        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading">
                        Dashboard information and statistics
                    </div>

                </div>
            </div>
        </div>
		<div class="row" >
			<div class="col-lg-3">
				<div class="hpanel">
					<div class="panel-heading">
						<div class="panel-tools">
							<a class="showhide"><i class="fa fa-chevron-up"></i></a>
						</div>
						Communications
					</div>
					<div class="panel-body">
						<div class="flot-pie-chart" >
							<div class="flot-chart-pie-content   h-200" id="onlinechart"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="hpanel">
					<div class="panel-heading">
						<div class="panel-tools">
							<a class="showhide"><i class="fa fa-chevron-up"></i></a>
						</div>
						Operations
					</div>
					<div class="panel-body">
						<div class="flot-pie-chart" >
							<div class="flot-chart-pie-content   h-200" id="flot-pie-chart"></div>
						</div>
					</div>
				</div>
			</div>
			
            <div class="col-lg-3">
				<script>
				function alltodaymilage(result){
									document.getElementById("alltodaymilage").innerHTML = result;
				}
				</script>
				<div class="hpanel">
					<div class="panel-heading">
						<div class="panel-tools">
							<a class="showhide"><i class="fa fa-chevron-up"></i></a>
						</div>
						Total Today Milage
					</div>
					<div class="panel-body">
						<div class="flot-pie-chart" >
							<div class="panel-body text-center h-200">
								<h1 class="m-xs"  id = "alltodaymilage">0</h1>

								<h3 class="font-extra-bold no-margins text-success" >
									Total Today Milage
								</h3>
								<small>Total today Traveled milage</small>
							</div>
						</div>
					</div>
				</div>
            </div>
		</div>
        <div class="row">
            <div class="col-lg-3">
				<script>
				function vcount(result){
									document.getElementById("vcount").innerHTML = result;
				}
				</script>
                <div class="hpanel">
                    <div class="panel-body text-center h-200">
                        <i class="fa fa-bus fa-4x"></i>

                        <h1 class="m-xs"  id = "vcount">0</h1>

                        <h3 class="font-extra-bold no-margins text-success" >
                            Total Vehicle Count
                        </h3>
                        <small>Total vehicle count in your fleet</small>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
			
			
			
            <div class="col-lg-3">
			<script>
			function comvcount(result){
								document.getElementById("comvcount").innerHTML = result;
			}
			</script>
                <div class="hpanel">
                    <div class="panel-body text-center h-200">
                        <i class="fa fa-exchange fa-4x"></i>

                        <h1 class="m-xs"  id = "comvcount">0</h1>

                        <h3 class="font-extra-bold no-margins text-success" >
                            Online Vehicles
                        </h3>
                        <small>Vehicles communicating with server</small>
                    </div>
                    <div class="panel-footer">
                       
                    </div>
                </div>
            </div>
			
			
            <div class="col-lg-3">
			<script>
			function nocomvcount(result){
								document.getElementById("nocomvcount").innerHTML = result;
			}
			</script>
                <div class="hpanel">
                    <div class="panel-body text-center h-200">
                        <i class="fa fa-window-close-o fa-4x"></i>

                        <h1 class="m-xs"  id = "nocomvcount">0</h1>

                        <h3 class="font-extra-bold no-margins text-success" >
                            Offline Vehicles
                        </h3>
                        <small>Vehicles not communicating with server</small>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
		</div>	
		<div class="row">
            <div class="col-lg-3">
			<script>
			function runnivcount(result){
								document.getElementById("runnivcount").innerHTML = result;
			}
			</script>
                <div class="hpanel">
                    <div class="panel-body text-center h-200">
                        <i class="fa fa-play fa-4x"></i>

                        <h1 class="m-xs"  id = "runnivcount">0</h1>

                        <h3 class="font-extra-bold no-margins text-success" >
                            Running Vehicles
                        </h3>
                        <small>Count running vehicles</small>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
			
			
            <div class="col-lg-3">
			<script>
			function stpvcount(result){
								document.getElementById("stpvcount").innerHTML = result;
			}
			</script>
                <div class="hpanel">
                    <div class="panel-body text-center h-200">
                        <i class="fa fa-stop-circle-o fa-4x"></i>

                        <h1 class="m-xs"  id = "stpvcount">0</h1>

                        <h3 class="font-extra-bold no-margins text-success" >
                            Stopped Vehicles
                        </h3>
                        <small>Count stopped vehicles</small>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
			
            <div class="col-lg-3">
			<script>
			function idlevcount(result){
								document.getElementById("idlevcount").innerHTML = result;
			}
			</script>
                <div class="hpanel">
                    <div class="panel-body text-center h-200">
                        <i class="fa fa-circle-o-notch fa-4x"></i>

                        <h1 class="m-xs"  id = "idlevcount">0</h1>

                        <h3 class="font-extra-bold no-margins text-success" >
                            Idle Vehicles
                        </h3>
                        <small>Count Engine idle vehicles</small>
                    </div>
                    <div class="panel-footer">
                        
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
<script src="vendor/jquery-flot/jquery.flot.js"></script>
<script src="vendor/jquery-flot/jquery.flot.resize.js"></script>
<script src="vendor/jquery-flot/jquery.flot.pie.js"></script>
<script src="vendor/flot.curvedlines/curvedLines.js"></script>
<script src="vendor/jquery.flot.spline/index.js"></script>
<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/peity/jquery.peity.min.js"></script>
<script src="vendor/sparkline/index.js"></script>

<!-- App scripts -->
<script src="scripts/homer.js"></script>
<script src="scripts/charts.js"></script>
<script>
	//Flot Pie Chart
$(function() {

    var data = [{
        label: "Running",
        data: 0,
        color: "#84c465",
    }, {
        label: "Stopped",
        data: 0,
        color: "#8dd76a",
    }, {
        label: "Idle",
        data: 0,
        color: "#a2c98f",
    }];

function getdata(runn,stop,idle)
{
	return data = [{
        label: "Running",
        data: runn,
        color: "#84c465",
    }, {
        label: "Stopped",
        data: stop,
        color: "#8dd76a",
    }, {
        label: "Idle",
        data: idle,
        color: "#a2c98f",
    }];
}

    var plotObj = $.plot($("#flot-pie-chart"), data, {
        series: {
            pie: {
                show: true
            }
        },
        grid: {
            hoverable: true
        },
        tooltip: true,
        tooltipOpts: {
            content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
            shifts: {
                x: 20,
                y: 0
            },
            defaultTheme: false
        }
    });
    // Update the random dataset at 25FPS for a smoothly-animating chart

    setInterval(function updateRandom() {
        plotObj.setData(getdata(document.getElementById("runnivcount").innerHTML,document.getElementById("stpvcount").innerHTML,document.getElementById("idlevcount").innerHTML));
        plotObj.draw();
    }, 500);
});

$(function() {

    var data = [{
        label: "Online",
        data: 0,
        color: "#84c465",
    }, {
        label: "Offline",
        data: 0,
        color: "#d3d3d3",
    }];

function getonlinedata(online,offline)
{
	return data = [{
        label: "Online",
        data: online,
        color: "#84c465",
    }, {
        label: "Offline",
        data: offline,
        color: "#d3d3d3",
    }];
}

    var plotObj = $.plot($("#onlinechart"), data, {
        series: {
            pie: {
                show: true
            }
        },
        grid: {
            hoverable: true
        },
        tooltip: true,
        tooltipOpts: {
            content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
            shifts: {
                x: 20,
                y: 0
            },
            defaultTheme: false
        }
    });
    // Update the random dataset at 25FPS for a smoothly-animating chart

    setInterval(function updateRandom() {
        plotObj.setData(getonlinedata(document.getElementById("comvcount").innerHTML,document.getElementById("nocomvcount").innerHTML));
        plotObj.draw();
    }, 500);
});
	</script>

</body>
</html>