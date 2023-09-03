<?PHP
require_once("./include/membersite_config.php");
require_once("./include/Maintanance.php");
			$id = "";
			$imei = "";
			$user = "";
			$subject = "";
			$maxmiles = "0";
			$maxdays = "0";
			$maxWorking = "0";
			$startdate = date("Y-m-d");
			$isactive = "1";
			$invoiceNo = "";

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}


if(isset($_POST['del']))
{
	if($fgmembersite->MaintananceRuleDelete($_POST['del']))
	{
		$fgmembersite->RedirectToformURL('Successfully Deleted!',$_SERVER['PHP_SELF'],'formrespond.php');
		exit;
	}
	else{
		$fgmembersite->RedirectToformURL('Not Deleted!',$_SERVER['PHP_SELF'],'formrespond.php');
		exit;
	}
}
if(isset($_POST['submit']))
{
	$fgmembersite->RedirectToformURL(InsertNotificationRule($fgmembersite,$_POST),$_SERVER['PHP_SELF'],'formrespond.php');
	exit;
}
if(isset($_POST['user']))
{
		$qryresult = $fgmembersite->LoadMaintainanceRule($_POST['user']);
			$id = $qryresult['id'];
			$imei = $qryresult['imei'];
			$subject = $qryresult['subject'];
			$maxmiles = $qryresult['maxmiles'];
			$maxdays = $qryresult['maxdays'];
			$startdate = $qryresult['startdate'];
			$isactive = $qryresult['isactive'];
			
}


if(isset($_POST['objectsubmit']))
{
				$fgmembersite->RedirectToformURL($fgmembersite->InsertObject($_POST),$_SERVER['PHP_SELF'],'formrespond.php');
				//echo $fgmembersite->InsertObject($_POST);
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
    <title><?= $fgmembersite->Title1 ?> | Maintenance</title>

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



<script>
function initialize() {
	$('#data_1 .input-group.date').datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		format: "yyyy-mm-dd"
	});
	
	if(imei!="")
	{
		document.getElementById('imei').value=imei;
		if(issubjectavailable(subject))
		{
			document.getElementById('mapimage').value=subject;
		}else{
			document.getElementById('mapimage').value="";
			document.getElementById("subjectdiv").style.display = 'inline'; 
		}
		
	}
}

function issubjectavailable(subject)
{
var x = document.getElementById("mapimage");
var i;
for (i = 0; i < x.length; i++) {
  if(x.options[i].text==subject)
  {
	  return true;
  }
}
return false;
}



<?php
if(isset($_POST['user']))
{
			echo "var imei = '$imei';";
			echo "var subject = '$subject';";
}else{
			echo "var imei = '';";
			echo "var subject = '';";
}


?>

function delconfirm(id){
  if (confirm("Are you sure you want to delete this record?")) {
  document.getElementById(id).submit();
  }
}

function userload(id){
  document.getElementById('userload'+id).submit();
}

function subject_change(subject)
{
	if(subject.value=="")
	{
		document.getElementById("subjectdiv").style.display = 'inline'; 
		document.getElementById("subject").value = "";
		
	}else{
		document.getElementById("subjectdiv").style.display = 'none'; 
		document.getElementById("subject").value = subject.value;
	}
}


function validateForm() {
    var subject = document.forms["myForm"]["subject"].value;
	var topic = document.getElementById("mapimage").value
	if(subject != "" && topic != "null")
	{
		return true;
	}else{
		alert("Please enter Your subject in Other subject input.");
		return false;
	}
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
					<div class="hpanel">
						<div class="panel-heading">
							<div class="panel-tools">
								<a class="showhide"><i class="fa fa-chevron-up"></i></a>
							</div>
							Maintenance List
						</div>
                            <table width="100%" class="table table-striped table-bordered table-hover" id="data_Device">
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Subject</th>
                                        <th>Max mileage</th>
										<th>Current mileage</th>
										<th>Max duration</th>
										<th>Current duration</th>
										<th>Start date</th>
										<th>Status</th>
										<th>Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
								<?php
									echo $fgmembersite->TableMaintainanceRule();
								?>
                                </tbody>
                            </table>
					</div>
				</div>
	</div>
</div>

<div class="content">
	<div class="row">
		<div class="col-lg-10">
					<div class="hpanel">
						<div class="panel-heading">
							<div class="panel-tools">
								<a class="showhide"><i class="fa fa-chevron-up"></i></a>
							</div>
							Add maintenance
						</div>
						<div class="panel-body">
						<form class="form-horizontal" method="post" onsubmit="return validateForm()" name="myForm">
						 <input type="hidden" name="id" value="<?php echo $id; ?>" />
						<div class="form-group"><label class="col-sm-2 control-label">Vehicle Number</label>
							<div class="col-sm-10"><select class="select2_demo_2 form-control" id="imei" name="imei">		<!-- $imei  -->
								<?= $fgmembersite->ObjectListOptions(); ?>
							</select></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Subject</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="mapimage" name="mapimage" onchange="subject_change(this)">
										<option value="null" <?php if($mapimage=='null'){echo 'selected';} ?>>
										 Select
										</option>
										<option value="Oil change" <?php if($mapimage=='Oil change'){echo 'selected';} ?>>
										 Oil change
										</option>
										<option value="Windshield wipers" <?php if($mapimage=='Windshield wipers'){echo 'selected';} ?>>
										 Windshield wipers
										</option>
										<option value="Transmission Fluid" <?php if($mapimage=='Transmission Fluid'){echo 'selected';} ?>>
										 Transmission Fluid
										</option>
										<option value="Transmission filter" <?php if($mapimage=='Transmission filter'){echo 'selected';} ?>>
										 Transmission filter
										</option>
										<option value="Tire service" <?php if($mapimage=='Tire service'){echo 'selected';} ?>>
										 Tire service
										</option>
										<option value="Tire replacement" <?php if($mapimage=='Tire replacement'){echo 'selected';} ?>>
										 Tire replacement
										</option>
										<option value="Spark plugs" <?php if($mapimage=='Spark plugs'){echo 'selected';} ?>>
										 Spark plugs
										</option>
										<option value="Power steering fluid" <?php if($mapimage=='Power steering fluid'){echo 'selected';} ?>>
										 Power steering fluid
										</option>
										<option value="Hydraulic Fluids" <?php if($mapimage=='Hydraulic Fluids'){echo 'selected';} ?>>
										 Hydraulic Fluids
										</option>
										<option value="Insurance Renewal reminder" <?php if($mapimage=='Insurance Renewal reminder'){echo 'selected';} ?>>
										 Insurance Renewal reminder
										</option>
										<option value="" <?php if($mapimage=='6'){echo 'selected';} ?>>
										 Other
										</option>
									</select>
							</div>
						</div>
						<div class="form-group" id="subjectdiv" style="display:none"><label class="col-sm-2 control-label">Other subject</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $subject; ?>" id="subject" name="subject" type="text"/></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Max mileage</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $maxmiles; ?>" id="maxmiles" name="maxmiles" type="text"/></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Max Duration (days)</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $maxdays; ?>" id="maxdays" name="maxdays" type="text"/></div>
						</div>
						<div class="form-group" id="data_1">
							<label class="col-sm-2 control-label">Start Date</label>
							<div class="input-group date col-sm-10" style="padding-left: 15px; padding-right: 15px;">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" id="startdate" name="startdate" value="<?php echo $startdate; ?>"  autocomplete="off">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-8 col-sm-offset-2">
									<button class="btn btn-default" type="submit">Cancel</button>
									   <button class="btn btn-primary " name="submit" type="submit">
										Submit
									   </button>
							</div>
						</div>
						</form>
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

<script>

    $(function () {

        $('#data_Device').dataTable( {
            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
			"ordering": false,
            buttons: [
                {extend: 'copy',className: 'btn-sm'},
                {extend: 'csv',title: 'Maintenance', className: 'btn-sm'},
                {extend: 'pdf', title: 'Maintenance', className: 'btn-sm'},
                {extend: 'print',className: 'btn-sm'}
            ]
        });
        $('#data_MaintainToBe').dataTable( {
            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
			"ordering": false,
            buttons: [
                {extend: 'copy',className: 'btn-sm'},
                {extend: 'csv',title: 'Maintenance', className: 'btn-sm'},
                {extend: 'pdf', title: 'Maintenance', className: 'btn-sm'},
                {extend: 'print',className: 'btn-sm'}
            ]
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
	}, 20000);
});
</script>
</body>
</html>
