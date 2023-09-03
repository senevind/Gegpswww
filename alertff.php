<?PHP
error_reporting(0);
ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");
require_once("./include/reports.php");
if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}
if($_POST){
	
}
?>


<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>SASINDU | Alerts</title>

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

	
	

	<script src="./OpenLayers-2.13.1/OpenLayers.js"></script>

<script>
function initialize() {
	$('#data_1 .input-group.date').datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		format: "yyyy-mm-dd"
	});
	$('#data_2 .input-group.date').datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		format: "yyyy-mm-dd"
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
            SASINDU TRACK
        </span>
    </div>
    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="header-link hide-menu"><i class="fa fa-bars"></i></div>
        <div class="small-logo">
            <span class="text-primary">SASINDU TRACK</span>
        </div>
        <form role="search" class="navbar-form-custom" method="post" action="#">
            <div class="form-group">
                <input type="text" placeholder="Search something special" class="form-control" name="search">
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
                <img src="images/logo.jpg" class="img-circle m-b" alt="logo"  height="150" width="150">
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
				<li>
					<a href="DashBoard.php"><i class="fa fa-tachometer"  style="color:#64CC34"></i> <span class="nav-label">Dash Board</span></a>
				</li>
				<li>
					<a href="Home.php"><i class="fa fa-home"  style="color:#64CC34"></i> <span class="nav-label">Tracking View</span></a>
				</li>
				<li>
					<a href="playback.php"><i class="fa fa-history"  style="color:#64CC34"></i> <span class="nav-label">History View</span></a>
				</li>
				<li>
					<a href="ReportTrip.php"><i class="fa fa-file-text-o"  style="color:#64CC34"></i> <span class="nav-label">Report View</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li><a href="report_daywise.php">Day Wise summery</a></li>
						<li><a href="report_daywisefull.php">Day Wise full</a></li>
                        <li><a href="ReportTrip.php">Monthly</a></li>
                    </ul>
				</li>
				<li>
					<a href="trackingpics.php"><i class="fa fa-picture-o"  style="color:#64CC34"></i> <span class="nav-label">Picture View</span></a>
				</li>
				<li>
					<a href="Analysis.php"><i class="fa fa-line-chart"  style="color:#64CC34"></i> <span class="nav-label">Analizer</span></a>
				</li>
				<li>
					<a href="alerts.php"><i class="fa fa-exclamation-triangle"  style="color:#64CC34"></i> <span class="nav-label">Alerts</span></a>
				</li>
        </ul>
    </div>
</aside>

<!-- Main Wrapper -->
<div id="wrapper">

<div class="content">

    <div class="row">
   <div class="col-md-6 col-sm-6 col-xs-12">
    <form class="form-horizontal" method="post">
     <div class="form-group"  style="width: 500px;">
      <label class="control-label col-sm-2" for="select">
       Select a Vehicle
      </label>
      <div class="col-sm-10">
       <select class="select form-control" id="select" name="select">

			<?= $fgmembersite->VehicleListOptions(); ?>
       </select>
      </div>
     </div>
	 
				<div class="form-group" id="data_2"  style="width: 500px;">
					<label class="control-label col-sm-2 requiredField" for="st_date">
					Start Date
					</label>
					<div class="input-group date col-sm-10"  style="padding-right:15px; padding-left:15px">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span><input name="st_date" type="text" class="form-control" id="trackdate">
					</div>
				</div>
				<div class="form-group" id="data_1"  style="width: 500px;">
					<label class="control-label col-sm-2" for="end_date">
					End Date
					</label>
					<div class="input-group date col-sm-10" style="padding-right:15px; padding-left:15px">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span><input name="end_date" type="text" class="form-control" id="trackdate">
					</div>
				</div>
				
     <div class="form-group">
      <div class="col-sm-10 col-sm-offset-2">
       <button class="btn btn-primary " name="submit" type="submit">
        Submit
       </button>
      </div>
     </div>
    </form>
   </div>

    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    </div>
					<br>
                </div>
                <div class="panel-body">
					
					
                    <input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search in table">
                    <table id="example1" class="footable table table-stripped toggle-arrow-tiny" data-page-size="8" data-filter=#filter>
													<thead>
													<tr>
														<th colspan="5">
															<?php
															if(isset($_POST['submit']))
															{
																echo "<h3>".$_POST['select']."</h3>";
															}
															?>
														</th>
													</tr>
													<tr>
														<th>Time</th>
														<th data-hide="phone,tablet">Power Cut</th>
														<th data-hide="phone,tablet">Over Voltage</th>
														<th>Upper Voltage</th>
														<th>Over Speed</th>
													</tr>
													</thead>
													<tbody>
														<?php
														if(!$fgmembersite->DBLogin())
														{
															echo "Not connected";
															return false;
														} 
														if(isset($_POST['submit']))
														{
															echo $Reports->alertreport($fgmembersite->connection,$_POST['select'],$_POST['st_date'],$_POST['end_date']); 
														}
														?>
													</tbody>
													<tfoot>
													<tr>
														<td colspan="5">
															<ul class="pagination pull-right"></ul>
														</td>
													</tr>
													</tfoot>
												</table>
					
					
					
					
					
					
					
                </div>
            </div>
        </div>

    </div>
    </div>

    <!-- Footer-->
    <footer class="footer">
<strong>Copyright</strong> SASINDU TRACKING &copy; 2017
    </footer>

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
<!-- App scripts -->
<script src="scripts/homer.js"></script>


<script>

    $(function () {

        // Initialize Example 1
        $('#example1').footable();

        // Initialize Example 2
        $('#example2').footable();


    });

</script>

</body>
</html>