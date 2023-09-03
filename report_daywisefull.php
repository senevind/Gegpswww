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

if(!$fgmembersite->DBLogin())
{
echo "Not connected";
return false;
} 

$maindata = null;
if($_POST){
	$maindata = $Reports->DaywiceReportfull($fgmembersite,$_POST['st_date'],$fgmembersite->connection,$_POST['select']);
}

//print_r($maindata); 
//exit;
?>


<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title><?= $fgmembersite->Title1 ?> | Day Wise full Report <?php if($_POST){ echo $_POST['select'];} ?></title>

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

<script>
function initialize() {
	$('#data_1 .input-group.date').datepicker({
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
	 
				<div class="form-group" id="data_1"  style="width: 500px;">
					<label class="control-label col-sm-2 requiredField" for="st_date">
					Start Date
					</label>
					<div class="input-group date col-sm-10"  style="padding-right:15px; padding-left:15px">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span><input name="st_date" type="text" class="form-control" id="trackdate" autocomplete="off">
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

    <div class="row"  <?php if($maindata == null){echo "hidden";} ?>>
				<div class="col-lg-12">
					<div class="hpanel">
						<div class="panel-heading">
							<div class="panel-tools">
							</div>
							<?php echo $_POST['select'] ?>
						</div>
						<div class="panel-body">

							<div class="col-lg-4">
								<div class="hpanel">
									<div class="panel-heading">
										<div class="panel-tools">
											<a class="showhide"><i class="fa fa-chevron-up"></i></a>
										</div>
										Online/Offline
									</div>
									<div class="panel-body">
										<div>
											<canvas id="comChart" height="200"></canvas>
										</div>
									</div>
								</div>
							</div>				
							<div class="col-lg-4">
								<div class="hpanel">
									<div class="panel-heading">
										<div class="panel-tools">
											<a class="showhide"><i class="fa fa-chevron-up"></i></a>
										</div>
										Operation 
									</div>
									<div class="panel-body">
										<div>
											<canvas id="operationChart" height="200"></canvas>
										</div>
									</div>
								</div>
							</div>	
						</div>
					</div>
				</div>
				<div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        </div>
                        Operation Summery <?php echo $_POST['select'] ?>
                    </div>
                    <div class="panel-body list">
                        <div class="table-responsive project-list">
                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                    <td>Total Milage :
                                    </td>
                                    <td><?php echo $maindata['milage']." ".$maindata['lengtUnit']; ?></td>
                                </tr>
                                <tr>
                                    <td>Total Traval Time :
                                    </td>
                                    <td><?php echo $maindata['traveltime']; ?></td>
                                </tr>
                                <tr>
                                    <td>Trip Started Time :
                                    </td>
                                    <td><?php echo $maindata['starttime']; ?></td>
                                </tr>
                                <tr>
                                    <td>Trip Stopped Time :
                                    </td>
                                    <td><?php echo $maindata['stoptime']; ?></td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
			
			
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    </div>
                    Running Details
                </div>
                <div class="panel-body">
                    <table id="optdetails" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
						<?php echo "<tr><th  colspan='6'>".$_POST['select']."</th></tr>"; ?>
                        <tr>
							<th>Time</th>
							<th>Speed</th>
							<th>Status</th>
							<th>Position</th>
							<th>Milage</th>
							<th>Fuel</th>
                        </tr>
                        </thead>
						<tbody>
							<?php echo $maindata['tablecontent']; ?>
						</tbody>
					</table>
                </div>
            </div>
        </div>
    </div>
	
    <div class="row"  <?php if($maindata != null){echo "hidden";} ?>>
				<div class="col-lg-12">
					<div class="hpanel">
						<div class="panel-heading">
							<div class="panel-tools">
								<a class="showhide"><i class="fa fa-chevron-up"></i></a>
							</div>
							Running Details
						</div>
						<div class="panel-body">
							<table id="optdetailsEmpty" class="table table-striped table-bordered table-hover" width="100%">
								<thead>
								<?php echo "<tr><th  colspan='6' style='text-align: center;'>".$_POST['select']." Full Report </th></tr>"; ?>
								<tr>
									<th>Time</th>
									<th>Speed</th>
									<th>Status</th>
									<th>Position</th>
									<th>Milage</th>
									<th>Fuel</th>
								</tr>
								</thead>
								<tbody>
									<tr><td  colspan='6' style='text-align: center;'>No data available</td></tr>
								</tbody>
							</table>
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
<!-- App scripts -->
<script src="scripts/homer.js"></script>
<script src="vendor/chartjs/Chart.min.js"></script>
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

<script src="vendor/select2-3.5.2/select2.min.js"></script>
<script>

    $(function () {

        // Initialize Example 1
        $('#example1').footable();

        // Initialize Example 2
        $('#example2').footable();
		
		$(".select").select2();
    });
	
	$(function () {
        var doughnutData = {
            labels: [
                "Online <?php echo $maindata['onlinetime']; ?>",
                "Offline <?php echo $maindata['offlinetime']; ?>"
            ],
            datasets: [
                {
                    data: [<?php if($maindata == null){echo "0";}else{echo $maindata['onlinemin'];}?>, <?php if($maindata == null){echo "0";}else{echo $maindata['offlinemin'];} ?>],
                    backgroundColor: [
                        "#84c465",
                        "#d3d3d3"
                    ],
                    hoverBackgroundColor: [
                        "#57b32c",
                        "#BDC3C7"
                    ]
                }]
        }


        var doughnutOptions = {
            responsive: true
        };

        var ctx = document.getElementById("comChart").getContext("2d");
        new Chart(ctx, {type: 'doughnut', data: doughnutData, options:doughnutOptions});
    });

	
	$(function () {
        var doughnutData = {
            labels: [
                "Running <?php echo $maindata['runningtime']; ?>",
                "Idle <?php echo $maindata['idletime']; ?>",
                "Stopped <?php echo $maindata['stoppedtime']; ?>"
            ],
            datasets: [
                {
                    data: [<?php if($maindata == null){echo "0";}else{echo $maindata['runmin'];} ?>, <?php if($maindata == null){echo "0";}else{echo $maindata['idlemin'];} ?>, <?php if($maindata == null){echo "0";}else{echo $maindata['stopedmin'];} ?>],
                    backgroundColor: [
                        "#84c465",
                        "#8dd76a",
                        "#a2c98f"
                    ],
                    hoverBackgroundColor: [
                        "#57b32c",
                        "#57b32c",
                        "#57b32c"
                    ]
                }]
        }


        var doughnutOptions = {
            responsive: true
        };

        var ctx = document.getElementById("operationChart").getContext("2d");
        new Chart(ctx, {type: 'doughnut', data: doughnutData, options:doughnutOptions});
    });

    $(function () {

        // Initialize Example 1
        $('#optdetails').dataTable( {
            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            buttons: [
                {extend: 'copy',className: 'btn-sm'},
                {extend: 'csv',title: '<?php if($_POST){ echo $_POST['select'];} ?>', className: 'btn-sm'},
                {extend: 'pdf', title: '<?php if($_POST){ echo $_POST['select'];} ?>', className: 'btn-sm'},
                {extend: 'print',className: 'btn-sm'}
            ]
        });

    });

</script>
</body>
</html>