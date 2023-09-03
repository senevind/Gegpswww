<?PHP
require_once("./include/membersite_config.php");
$timeZone="";
$timeFormat="";
$unit="";



		

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}

if(isset($_POST['submit']))
{
	//print_r($_POST);
	$fgmembersite->InsertSystemSettings($_POST);
	
	$fgmembersite->RedirectToformURL('System settings changed! Please login.','logout.php','formrespond.php');
	exit;
}

$qryresult = $fgmembersite->LoadSystemSettings("array");
$timeZone = $qryresult['timeZone'];
$timeFormat = $qryresult['timeFormat'];
$unit = $qryresult['unit'];
$dayLightStart = $qryresult['dayLightStart'];
$dayLightEnd = $qryresult['dayLightEnd'];
if($qryresult['dayLightEnable'] == '1')
{
	$dayLightEnable = 'checked';
}
else
{
	$dayLightEnable = '';
}


?>


<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title><?= $fgmembersite->Title1 ?> | System Settings</title>

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

function validateForm() {
	if(!document.forms["myForm"]["dayLightEnable"].checked)
	{
		return true;
	}
	if(!isValidDate(document.forms["myForm"]["dayLightStart"].value))
	{
		alert("please enter valide date for start date 'yyyy-mm-dd'");
		return false;
	}
	if(!isValidDate(document.forms["myForm"]["dayLightEnd"].value))
	{
		alert("please enter valide date for end date 'yyyy-mm-dd'");
		return false;
	}
	return true;
}

function isValidDate(dateString) {
  var regEx = /^\d{4}-\d{2}-\d{2}$/;
  if(!dateString.match(regEx)) return false;  // Invalid format
  var d = new Date(dateString);
  if(Number.isNaN(d.getTime())) return false; // Invalid date
  return d.toISOString().slice(0,10) === dateString;
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
		<div class="col-lg-10">
					<div class="hpanel">
						<div class="panel-heading">
							<div class="panel-tools">
								<a class="showhide"><i class="fa fa-chevron-up"></i></a>
							</div>
							Edit System Settings
						</div>
						<div class="panel-body">
						<form class="form-horizontal" method="post"  onsubmit="return validateForm()"" name="myForm">

						<div class="form-group"><label class="col-sm-2 control-label">Display Time Format</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="timeFormat" name="timeFormat">
										<option value="0" <?php if($timeFormat=='0'){echo 'selected';} ?>>
										 yyyy-mm-dd
										</option>
										<option value="1" <?php if($timeFormat=='1'){echo 'selected';} ?>>
										 dd/mm/yy
										</option>
									</select>
							</div>
						</div>
						
						<div class="form-group"><label class="col-sm-2 control-label">Unit</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="unit" name="unit">
										<option value="0" <?php if($unit=='0'){echo 'selected';} ?>>
										 Kilometers
										</option>
										<option value="1" <?php if($unit=='1'){echo 'selected';} ?>>
										 Miles
										</option>
									</select>
							</div>
						</div>
						
						<div class="form-group"><label class="col-sm-2 control-label">Time Zone</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="timeZone" name="timeZone">
										<option timeZoneId="1" gmtAdjustment="GMT-12:00" useDaylightTime="0" value="-720" <?php if($timeZone=='-720'){echo 'selected';} ?>>(GMT-12:00) </option>
										<option timeZoneId="2" gmtAdjustment="GMT-11:00" useDaylightTime="0" value="-660" <?php if($timeZone=='-660'){echo 'selected';} ?>>(GMT-11:00) </option>
										<option timeZoneId="3" gmtAdjustment="GMT-10:00" useDaylightTime="0" value="-600" <?php if($timeZone=='-600'){echo 'selected';} ?>>(GMT-10:00) </option>
										<option timeZoneId="4" gmtAdjustment="GMT-09:00" useDaylightTime="1" value="-540" <?php if($timeZone=='-540'){echo 'selected';} ?>>(GMT-09:00) </option>
										<option timeZoneId="5" gmtAdjustment="GMT-08:00" useDaylightTime="1" value="-480" <?php if($timeZone=='-480'){echo 'selected';} ?>>(GMT-08:00) </option>
										<option timeZoneId="9" gmtAdjustment="GMT-07:00" useDaylightTime="1" value="-420" <?php if($timeZone=='-420'){echo 'selected';} ?>>(GMT-07:00) </option>
										<option timeZoneId="13" gmtAdjustment="GMT-06:00" useDaylightTime="0" value="-360" <?php if($timeZone=='-360'){echo 'selected';} ?>>(GMT-06:00) </option>
										<option timeZoneId="16" gmtAdjustment="GMT-05:00" useDaylightTime="1" value="-300" <?php if($timeZone=='-300'){echo 'selected';} ?>>(GMT-05:00) </option>
										<option timeZoneId="20" gmtAdjustment="GMT-04:00" useDaylightTime="1" value="-240" <?php if($timeZone=='-240'){echo 'selected';} ?>>(GMT-04:00) </option>
										<option timeZoneId="21" gmtAdjustment="GMT-03:30" useDaylightTime="1" value="-210" <?php if($timeZone=='-210'){echo 'selected';} ?>>(GMT-03:30) </option>
										<option timeZoneId="22" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-180" <?php if($timeZone=='-180'){echo 'selected';} ?>>(GMT-03:00) </option>
										<option timeZoneId="26" gmtAdjustment="GMT-02:00" useDaylightTime="1" value="-120" <?php if($timeZone=='-120'){echo 'selected';} ?>>(GMT-02:00) </option>
										<option timeZoneId="27" gmtAdjustment="GMT-01:00" useDaylightTime="0" value="-60" <?php if($timeZone=='-60'){echo 'selected';} ?>>(GMT-01:00) </option>
										<option timeZoneId="29" gmtAdjustment="GMT+00:00" useDaylightTime="0" value="0" <?php if($timeZone=='0'){echo 'selected';} ?>>(GMT+00:00) </option>
										<option timeZoneId="35" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="60" <?php if($timeZone=='60'){echo 'selected';} ?>>(GMT+01:00) </option>
										<option timeZoneId="44" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="120" <?php if($timeZone=='120'){echo 'selected';} ?>>(GMT+02:00) </option>
										<option timeZoneId="48" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="180" <?php if($timeZone=='180'){echo 'selected';} ?>>(GMT+03:00) </option>
										<option timeZoneId="49" gmtAdjustment="GMT+03:30" useDaylightTime="1" value="210" <?php if($timeZone=='210'){echo 'selected';} ?>>(GMT+03:30) </option>
										<option timeZoneId="52" gmtAdjustment="GMT+04:00" useDaylightTime="1" value="240" <?php if($timeZone=='240'){echo 'selected';} ?>>(GMT+04:00) </option>
										<option timeZoneId="53" gmtAdjustment="GMT+04:30" useDaylightTime="0" value="270" <?php if($timeZone=='270'){echo 'selected';} ?>>(GMT+04:30) </option>
										<option timeZoneId="55" gmtAdjustment="GMT+05:00" useDaylightTime="0" value="300" <?php if($timeZone=='300'){echo 'selected';} ?>>(GMT+05:00) </option>
										<option timeZoneId="57" gmtAdjustment="GMT+05:30" useDaylightTime="0" value="330" <?php if($timeZone=='330'){echo 'selected';} ?>>(GMT+05:30) </option>
										<option timeZoneId="58" gmtAdjustment="GMT+05:45" useDaylightTime="0" value="345" <?php if($timeZone=='345'){echo 'selected';} ?>>(GMT+05:45) </option>
										<option timeZoneId="60" gmtAdjustment="GMT+06:00" useDaylightTime="0" value="360" <?php if($timeZone=='360'){echo 'selected';} ?>>(GMT+06:00) </option>
										<option timeZoneId="61" gmtAdjustment="GMT+06:30" useDaylightTime="0" value="390" <?php if($timeZone=='390'){echo 'selected';} ?>>(GMT+06:30) </option>
										<option timeZoneId="63" gmtAdjustment="GMT+07:00" useDaylightTime="1" value="420" <?php if($timeZone=='420'){echo 'selected';} ?>>(GMT+07:00) </option>
										<option timeZoneId="68" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="480" <?php if($timeZone=='480'){echo 'selected';} ?>>(GMT+08:00) </option>
										<option timeZoneId="71" gmtAdjustment="GMT+09:00" useDaylightTime="1" value="540" <?php if($timeZone=='540'){echo 'selected';} ?>>(GMT+09:00) </option>
										<option timeZoneId="73" gmtAdjustment="GMT+09:30" useDaylightTime="0" value="570" <?php if($timeZone=='570'){echo 'selected';} ?>>(GMT+09:30) </option>
										<option timeZoneId="78" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="600" <?php if($timeZone=='600'){echo 'selected';} ?>>(GMT+10:00) </option>
										<option timeZoneId="79" gmtAdjustment="GMT+11:00" useDaylightTime="1" value="660" <?php if($timeZone=='660'){echo 'selected';} ?>>(GMT+11:00) </option>
										<option timeZoneId="81" gmtAdjustment="GMT+12:00" useDaylightTime="0" value="720" <?php if($timeZone=='720'){echo 'selected';} ?>>(GMT+12:00) </option>
										<option timeZoneId="82" gmtAdjustment="GMT+13:00" useDaylightTime="0" value="780" <?php if($timeZone=='780'){echo 'selected';} ?>>(GMT+13:00) </option>
									</select>
							</div>
						</div>
						<hr>
						<div class="form-group"><label class="col-sm-2 control-label">Enable Daylight Savings<br/>
						</label>
							<div class="col-sm-10">
								<div class="checkbox"><label> <input type="checkbox" name="dayLightEnable" value="1" <?php echo $dayLightEnable ?>> Yes/No </label></div>
							</div>
						</div>
						<div class="form-group" id="data_2" >
							<label class="control-label col-sm-2 requiredField" for="dayLightStart">
							Start Date
							</label>
							<div class="input-group date col-sm-10"  style="padding-right:15px; padding-left:15px">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span><input value="<?php echo $dayLightStart ?>" name="dayLightStart" type="text" class="form-control" autocomplete="off">
							</div>
						</div>
						<div class="form-group" id="data_1" >
							<label class="control-label col-sm-2" for="dayLightEnd">
							End Date
							</label>
							<div class="input-group date col-sm-10" style="padding-right:15px; padding-left:15px">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span><input value="<?php echo $dayLightEnd ?>" name="dayLightEnd" type="text" class="form-control" autocomplete="off">
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
<!-- App scripts -->
<script src="scripts/homer.js"></script>
<script src="vendor/chartjs/Chart.min.js"></script>
<script src="vendor/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js"></script>

</body>
</html>