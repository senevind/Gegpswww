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
<body onload="initialize()" >
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
                                        <th data-toggle="true">Vehicle</th>
                                        <th>Subject</th>
                                        <th data-hide="all">Max mileage</th>
										<th data-hide="all">Current mileage</th>
										<th data-hide="all">Max duration</th>
										<th data-hide="all">Current duration</th>
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
<script src="vendor/fooTable/dist/footable.all.min.js"></script>

<script>

    $(function () {
        $('#data_Device').footable();
    });
</script>
</body>
</html>
