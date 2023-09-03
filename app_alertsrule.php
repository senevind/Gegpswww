<?PHP

ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");
//$fgmembersite->LogOut();
if(isset($_GET['username']))
{
	$_POST['username'] = $_GET['username'];
	$_POST['password'] = $_GET['password'];
}

if(!$fgmembersite->CheckLogin())
{
	if(!$fgmembersite->Login())
	   {
		   echo "Authantication Error!";
		   die;
			//$fgmembersite->RedirectToURL("login.php");
	  }
}

if(!$fgmembersite->CheckLogin())
{
    echo "Authantication Error!";
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
    <title>G supertrack | Notification rules</title>

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

    <link rel="stylesheet" href="vendor/sweetalert/lib/sweet-alert.css" />
    <link rel="stylesheet" href="vendor/toastr/build/toastr.min.css" />
	
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script src="./OpenLayers-2.13.1/OpenLayers.js"></script>

	<style>
	.toast-message {
		color: black;
	}

	</style>
	
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
	swal({
				title: "Are you sure?",
				text: "This notification rule will not function any more!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!"
			},
			function () {
				document.getElementById(id).submit();
			});
	
	
/*
  if (confirm("Are you sure you want to delete this record?")) {
  document.getElementById(id).submit();
  }
 */
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
		toastr.error('Error - Add Name, select Your Vehicle and Zone!');
		//alert("Please Name, select Your Vehicle and Zone!");
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
		toastr.error('Error - Add Name, select Your Vehicle and speeds!');
		//alert("Please select Name, Your Vehicle and speeds correctly!");
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
		toastr.error('Error - Please select Name, Your Vehicle!');
		//alert("Please select Name, Your Vehicle!");
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
		toastr.error('Error - Please select Name and Your Vehicle!');
		//alert("Please select Name, Your Vehicle!");
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
		toastr.error('Error - Please select Your Vehicle, Name and Parking correctly!');
		//alert("Please select Your Vehicle, Name and Parking correctly!");
		return false;
	}	
}
</script>
</head>
<body onload="initialize()" class="fixed-navbar sidebar-scroll">

	<div class="content" style="padding-right: 0px;padding-left: 0px;">
    <div class="col-lg-12" style="padding-left: 0px;padding-right: 0px;">
        <div class="hpanel">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab-1">View</a></li>
                <li class=""><a data-toggle="tab" href="#tab-2">Add</a></li>
            </ul>
            <div class="tab-content">
                <div id="tab-1" class="tab-pane active">
                    <div class="panel-body" style="padding-left: 5px;padding-right: 5px;">
						<table width="100%" class="table table-striped table-bordered table-hover" id="data_Device">
							<thead>
								<tr>
									<th>Vehicle</th>
									<th>Name</th>
									<th>Type</th>
									<th>Value</th>
									<th class="d-none d-lg-table-cell">Action</th>
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
                <div id="tab-2" class="tab-pane">
                    <div class="panel-body" style="padding-left: 5px;padding-right: 5px;">
						<div class="text-center m-b-md" id="wizardControl" style="margin-bottom: 5px;">
							<a class="btn btn-primary" href="#step1" data-toggle="tab">Zonel notification</a>
							
							<a class="btn btn-default" href="#step2" data-toggle="tab">Speed notification</a>
							<a class="btn btn-default" href="#step3" data-toggle="tab">Movement notification</a>
							<a class="btn btn-default" href="#step4" data-toggle="tab">Engine status notification</a>
							<a class="btn btn-default" href="#step5" data-toggle="tab">Parking notification</a>
							
						</div>
						
						<div class="tab-content">
							<div id="step1" class="tab-pane active" style="padding-left: 0px;padding-right: 0px;">
								<div class="col-lg-12">
											<div class="hpanel">
												<div class="panel-heading">
													Add Zonel notification rule
												</div>
												<div class="panel-body" style="padding-left: 0px;padding-right: 0px;">
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

							<div id="step2" class="tab-pane" style="padding-left: 0px;padding-right: 0px;">
								<div class="col-lg-12">
											<div class="hpanel">
												<div class="panel-heading">
													Add Speed notification rule
												</div>
												<div class="panel-body" style="padding-left: 0px;padding-right: 0px;">
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
							
							<div id="step3" class="tab-pane" style="padding-left: 0px;padding-right: 0px;">
								<div class="col-lg-12">
											<div class="hpanel">
												<div class="panel-heading">
													Add Movement notification rule
												</div>
												<div class="panel-body" style="padding-left: 0px;padding-right: 0px;">
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
							
							<div id="step4" class="tab-pane" style="padding-left: 0px;padding-right: 0px;">
								<div class="col-lg-12">
											<div class="hpanel">
												<div class="panel-heading">
													Add Engine status notification rule
												</div>
												<div class="panel-body" style="padding-left: 0px;padding-right: 0px;">
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
							
							<div id="step5" class="tab-pane" style="padding-left: 0px;padding-right: 0px;">
							<div class="col-lg-12">
										<div class="hpanel">
											<div class="panel-heading">
												Add Parking notification rule
											</div>
											<div class="panel-body" style="padding-left: 0px;padding-right: 0px;">
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

<script src="vendor/sweetalert/lib/sweet-alert.min.js"></script>
<script src="vendor/toastr/build/toastr.min.js"></script>
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
            buttons: [],
			"lengthChange": false
        });
        toastr.options = {
            "debug": false,
            "newestOnTop": false,
            "positionClass": "toast-top-center",
            "closeButton": true,
            "toastClass": "animated fadeInDown",
        };

		
		
    });
	

</script>
</body>
</html>