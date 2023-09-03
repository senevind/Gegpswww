<?PHP
require_once("./include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}

if(isset($_POST['del']))
{
	if($fgmembersite->NotificationsRuleDelete($_POST['del']))
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
		$fgmembersite->RedirectToformURL($fgmembersite->NotificationsRuleInseart($_POST),$_SERVER['PHP_SELF'],'formrespond.php');
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
    <title><?= $fgmembersite->Title1 ?> | Notification rules</title>

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

	
	
	<script src='https://www.google.com/recaptcha/api.js'></script>
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
}


function delconfirm(id){
  if (confirm("Are you sure you want to delete this record?")) {
  document.getElementById(id).submit();
  }
}

function userload(id){
  document.getElementById('userload'+id).submit();
}



function validateForm1() {
    var zone = document.forms["Form1"]["Zone"].value;
	var imei = document.forms["Form1"]["imei"].value;
	var name = document.forms["Form1"]["name"].value;
	
	if(name != "" && imei != "" && zone != "")
	{
		return true;
	}else{
		alert("Please Name, select Your Vehicle and Zone!");
		return false;
	}	
}

function validateForm2() {
    var maxspeed = document.forms["Form2"]["maxspeed"].value;
	var minspeed = document.forms["Form2"]["minspeed"].value;
	var name = document.forms["Form2"]["name"].value;
	var imei = document.forms["Form2"]["imei"].value;
	
	if(name != "" && imei != "" && maxspeed >= 0 && minspeed >= 0)
	{
		return true;
	}else{
		alert("Please select Name, Your Vehicle and speeds correctly!");
		return false;
	}	
}

function validateForm3() {
	var imei = document.forms["Form3"]["imei"].value;
	var name = document.forms["Form3"]["name"].value;
	
	if(name != "" && imei != "")
	{
		return true;
	}else{
		alert("Please select Name, Your Vehicle!");
		return false;
	}	
}

function validateForm4() {
	var imei = document.forms["Form4"]["imei"].value;
	var name = document.forms["Form4"]["name"].value;
	
	if(name != "" && imei != "")
	{
		return true;
	}else{
		alert("Please select Name, Your Vehicle!");
		return false;
	}	
}

function validateForm5() {
	var imei = document.forms["Form5"]["imei"].value;
	var maxspeed = document.forms["Form5"]["maxpark"].value;
	var name = document.forms["Form5"]["name"].value;
	
	if(name != "" && imei != "" && maxspeed > 0)
	{
		return true;
	}else{
		alert("Please select Your Vehicle, Name and Parking correctly!");
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
								<table width="100%" class="table table-striped table-bordered table-hover" id="data_Device">
									<thead>
										<tr>
											<th>Vehicle</th>
											<th>Name</th>
											<th>Type</th>
											<th>Value</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
									<?php
										echo $fgmembersite->TableNotificationsRule();
									?>
									</tbody>
								</table>
						</div>
					</div>
		</div>
	</div>


	<div class="content">
		<div class="row">
			<div class="col-lg-12">
				<div class="text-center m-b-md" id="wizardControl" style="margin-bottom: 5px;">
					<a class="btn btn-primary" href="#step1" data-toggle="tab">Zonel notification</a>
					
					<a class="btn btn-default" href="#step2" data-toggle="tab">Speed notification</a>
					<a class="btn btn-default" href="#step3" data-toggle="tab">Movement notification</a>
					<a class="btn btn-default" href="#step4" data-toggle="tab">Engine status notification</a>
					<a class="btn btn-default" href="#step5" data-toggle="tab">Parking notification</a>
					
				</div>
				
				<div class="tab-content">
				
				<div id="step1" class="p-m tab-pane active">
					<div class="col-lg-10">
								<div class="hpanel">
									<div class="panel-heading">
										<div class="panel-tools">
											<a class="showhide"><i class="fa fa-chevron-up"></i></a>
										</div>
										Add Zonel notification rule
									</div>
									<div class="panel-body">
									<form class="form-horizontal" method="post" onsubmit="return validateForm1()" name="Form1">
									<input type="hidden" name="type" value="1" />
									<div class="form-group"><label class="col-sm-2 control-label">Name</label>
										<div class="col-sm-10"><input class="form-control" value="" name="name" type="text"/></div>
									</div>
									<div class="form-group"><label class="col-sm-2 control-label">Vehicle Number</label>
										<div class="col-sm-10"><select class="select2_demo_2 form-control" name="imei">		<!-- $imei  -->
											<?php echo $fgmembersite->ObjectListOptions(); ?>
										</select></div>
									</div>
									<div class="form-group"><label class="col-sm-2 control-label">Zone</label>
										<div class="col-sm-10">
											   <select class="select form-control"  id="Zone" name="Zone">
												<?php echo $fgmembersite->GeofenceList("") ?>
											   </select>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-8 col-sm-offset-2">
												<button class="btn btn-default" type="submit">Cancel</button>
												   <button class="btn btn-primary " name="submit" type="submit">
													Add
												   </button>
										</div>
									</div>
									</form>
									</div>
								</div>
							</div>
				</div>

				<div id="step2" class="p-m tab-pane">
					<div class="col-lg-10">
								<div class="hpanel">
									<div class="panel-heading">
										<div class="panel-tools">
											<a class="showhide"><i class="fa fa-chevron-up"></i></a>
										</div>
										Add Speed notification rule
									</div>
									<div class="panel-body">
									<form class="form-horizontal" method="post" onsubmit="return validateForm2()" name="Form2">
									<input type="hidden" name="type" value="2" />
									<div class="form-group"><label class="col-sm-2 control-label">Name</label>
										<div class="col-sm-10"><input class="form-control" value="" name="name" type="text"/></div>
									</div>
									<div class="form-group"><label class="col-sm-2 control-label">Vehicle Number</label>
										<div class="col-sm-10"><select class="select2_demo_2 form-control" name="imei">		<!-- $imei  -->
											<?php echo $fgmembersite->ObjectListOptions(); ?>
										</select></div>
									</div>
									<div class="form-group"><label class="col-sm-2 control-label">Max speed</label>
										<div class="col-sm-10"><input class="form-control" value="0" id="maxspeed" name="maxspeed" type="text"/></div>
									</div>
									<div class="form-group"><label class="col-sm-2 control-label">Min speed</label>
										<div class="col-sm-10"><input class="form-control" value="0" id="minspeed" name="minspeed" type="text"/></div>
									</div>
									<div class="form-group">
										<div class="col-sm-8 col-sm-offset-2">
												<button class="btn btn-default" type="submit">Cancel</button>
												   <button class="btn btn-primary " name="submit" type="submit">
													Add
												   </button>
										</div>
									</div>
									</form>
									</div>
								</div>
							</div>
				</div>
				
				<div id="step3" class="p-m tab-pane">
					<div class="col-lg-10">
								<div class="hpanel">
									<div class="panel-heading">
										<div class="panel-tools">
											<a class="showhide"><i class="fa fa-chevron-up"></i></a>
										</div>
										Add Movement notification rule
									</div>
									<div class="panel-body">
									<form class="form-horizontal" method="post" onsubmit="return validateForm3()" name="Form3">
									<input type="hidden" name="type" value="3" />
									<div class="form-group"><label class="col-sm-2 control-label">Name</label>
										<div class="col-sm-10"><input class="form-control" value="" name="name" type="text"/></div>
									</div>
									<div class="form-group"><label class="col-sm-2 control-label">Vehicle Number</label>
										<div class="col-sm-10"><select class="select2_demo_2 form-control" id="imei" name="imei">		<!-- $imei  -->
											<?php echo $fgmembersite->ObjectListOptions(); ?>
										</select></div>
									</div>
									<div class="form-group">
										<div class="col-sm-8 col-sm-offset-2">
												<button class="btn btn-default" type="submit">Cancel</button>
												   <button class="btn btn-primary " name="submit" type="submit">
													Add
												   </button>
										</div>
									</div>
									</form>
									</div>
								</div>
							</div>
				</div>
				
				<div id="step4" class="p-m tab-pane">
					<div class="col-lg-10">
								<div class="hpanel">
									<div class="panel-heading">
										<div class="panel-tools">
											<a class="showhide"><i class="fa fa-chevron-up"></i></a>
										</div>
										Add Engine status notification rule
									</div>
									<div class="panel-body">
									<form class="form-horizontal" method="post" onsubmit="return validateForm4()" name="Form4">
									<input type="hidden" name="type" value="4" />
									<div class="form-group"><label class="col-sm-2 control-label">Name</label>
										<div class="col-sm-10"><input class="form-control" value="" name="name" type="text"/></div>
									</div>
									<div class="form-group"><label class="col-sm-2 control-label">Vehicle Number</label>
										<div class="col-sm-10"><select class="select2_demo_2 form-control" id="imei" name="imei">		<!-- $imei  -->
											<?php echo $fgmembersite->ObjectListOptions(); ?>
										</select></div>
									</div>
									<div class="form-group">
										<div class="col-sm-8 col-sm-offset-2">
												<button class="btn btn-default" type="submit">Cancel</button>
												   <button class="btn btn-primary " name="submit" type="submit">
													Add
												   </button>
										</div>
									</div>
									</form>
									</div>
								</div>
							</div>
				</div>
				
				<div id="step5" class="p-m tab-pane">
					<div class="col-lg-10">
								<div class="hpanel">
									<div class="panel-heading">
										<div class="panel-tools">
											<a class="showhide"><i class="fa fa-chevron-up"></i></a>
										</div>
										Add Parking notification rule
									</div>
									<div class="panel-body">
									<form class="form-horizontal" method="post" onsubmit="return validateForm5()" name="Form5">
									<input type="hidden" name="type" value="5" />
									<div class="form-group"><label class="col-sm-2 control-label">Name</label>
										<div class="col-sm-10"><input class="form-control" value="" name="name" type="text"/></div>
									</div>
									<div class="form-group"><label class="col-sm-2 control-label">Vehicle Number</label>
										<div class="col-sm-10"><select class="select2_demo_2 form-control" id="imei" name="imei">		<!-- $imei  -->
											<?php echo $fgmembersite->ObjectListOptions(); ?>
										</select></div>
									</div>
									<div class="form-group"><label class="col-sm-2 control-label">Max parking minuites</label>
										<div class="col-sm-10"><input class="form-control" value="0" id="maxpark" name="maxpark" type="text"/></div>
									</div>
									<div class="form-group">
										<div class="col-sm-8 col-sm-offset-2">
												<button class="btn btn-default" type="submit">Cancel</button>
												   <button class="btn btn-primary " name="submit" type="submit">
													Add
												   </button>
										</div>
									</div>
									</form>
									</div>
								</div>
							</div>
				</div>
				</div>

</div></div></div>

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
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            $('a[data-toggle="tab"]').removeClass('btn-primary');
            $('a[data-toggle="tab"]').addClass('btn-default');
            $(this).removeClass('btn-default');
            $(this).addClass('btn-primary');
        });
        $('#data_Device').dataTable( {
            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            buttons: [
                {extend: 'copy',className: 'btn-sm'},
                {extend: 'csv',title: 'Device DATA', className: 'btn-sm'},
                {extend: 'pdf', title: 'Device DATA', className: 'btn-sm'},
                {extend: 'print',className: 'btn-sm'}
            ]
        });
    });
	

</script>
</body>
</html>