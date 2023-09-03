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
    <title><?= $fgmembersite->Title1 ?> | Dashboard</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->
	
	<link rel="manifest" href="manifest.json">
	
    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">

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
					//alltodaymilage(xmlhttp.responseText);
				}
				if(reson=='todayalerts'){
					todayalerts(xmlhttp.responseText);
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
	load('todayalerts');
}

</script>
</head>
<body onload="initialize()" class="fixed-navbar sidebar-scroll">

<style>
    .skin-option { position: fixed; text-align: center; right: -1px; padding: 10px; top: 80px; width: 150px; height: 133px; text-transform: uppercase; background-color: #ffffff; box-shadow: 0 1px 10px rgba(0, 0, 0, 0.05), 0 1px 4px rgba(0, 0, 0, .1); border-radius: 4px 0 0 4px; z-index: 100; }
</style>

<!-- End skin option / for demo purpose only -->

<!-- Header -->
<div id="header">
    <div class="color-line">
    </div>
    <div id="logo" class="light-version">
        <span>
            <?= $fgmembersite->Title2 ?>
        </span>
    </div>
    <nav role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo">
            <span class="text-primary"><?= $fgmembersite->Title2 ?></span>
        </div>
        <form role="search" class="navbar-form-custom" method="post" action="#">
            <div class="form-group">
                <input type="text" placeholder="" class="form-control" name="search">
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
				function todayalerts(result){
									document.getElementById("todayalerts").innerHTML = result;
				}
				</script>
				<div class="hpanel">
					<div class="panel-heading">
						<div class="panel-tools">
							<a class="showhide"><i class="fa fa-chevron-up"></i></a>
						</div>
						Alerts
					</div>
					<div class="panel-body">
						<div class="flot-pie-chart" >
							<div class="panel-body text-center h-200">
							
								<i class="fa fa-bell-o fa-4x"></i>
								
								<h1 class="m-xs"  id = "todayalerts">0</h1>

								<h3 class="font-extra-bold no-margins text-info" >
									Total Today Alerts
								</h3>
								<small>Alerts before 24 hours</small>
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

                        <h3 class="font-extra-bold no-margins text-info" >
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

                        <h3 class="font-extra-bold no-margins text-info" >
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

                        <h3 class="font-extra-bold no-margins text-info" >
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

                        <h3 class="font-extra-bold no-margins text-info" >
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

                        <h3 class="font-extra-bold no-margins text-info" >
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

                        <h3 class="font-extra-bold no-margins text-info" >
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
        color: "#008000",
    }, {
        label: "Stopped",
        data: 0,
        color: "#DF013A",
    }, {
        label: "Idle",
        data: 0,
        color: "#FE9A2E",
    }];

function getdata(runn,stop,idle)
{
	return data = [{
        label: "Running",
        data: runn,
        color: "#008000",
    }, {
        label: "Stopped",
        data: stop,
        color: "#DF013A",
    }, {
        label: "Idle",
        data: idle,
        color: "#FE9A2E",
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
        color: "#1ab394",
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
        color: "#1ab394",
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