<?PHP
require_once("./include/membersite_config.php");
$objname="";
$objsysno="";
$objectdiscription="";
$simno="";
$origine="";
$Destnation="";
$fixeddate=date('Y-m-d');
$model="";
$fixedby="";
$activate="0";
$sendntc="0";
$cam1="0";
$cam2="0";
$tankcapacity=0;
$tanktype=0;
$mapimage = 1;
$contact="";
$tz=0;
$reason = "new";
$message = "";
$admin_group = 0;
$contactperson = "";
$hubee = 0;
$hubeeAdmin = 0;
$expdate = "";

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}

if(isset($_POST['addadmingroup']))
{
	$fgmembersite->RedirectToformURL($fgmembersite->AdminGroupAdd($_POST['addadmingroup']),$_SERVER['PHP_SELF'],'formrespond.php');
	exit;
}
if(isset($_POST['deladmingroup']))
{
	$fgmembersite->RedirectToformURL($fgmembersite->AdminGroupDelete($_POST['deladmingroup']),$_SERVER['PHP_SELF'],'formrespond.php');
	exit;
}
if(isset($_POST['del']))
{
if($fgmembersite->ObjectDelete($_POST['del']))
{
	$fgmembersite->RedirectToformURL('Successfully Deleted!',$_SERVER['PHP_SELF'],'formrespond.php');
	exit;
}
else{
	$fgmembersite->RedirectToformURL('Not Deleted!',$_SERVER['PHP_SELF'],'formrespond.php');
	exit;
}
}

if(isset($_POST['user']))
{
		$qryresult = $fgmembersite->LoadObjectManagement($_POST['user']);
			$objname = $qryresult['objname'];
			$objsysno = $qryresult['objsysno'];
			$simno = $qryresult['simno'];
			$origine = $qryresult['origine'];
			$Destnation = $qryresult['Destnation'];
			$fixeddate = $qryresult['fixeddate'];
			$model = $qryresult['model'];
			$fixedby = $qryresult['fixedby'];
			$objectdiscription = $qryresult['objectdiscription'];
			$activate = $qryresult['activate'];
			$sendntc = $qryresult['sendntc'];
			$cam1 = $qryresult['cam1'];
			$cam2 = $qryresult['cam2'];
			$tankcapacity = $qryresult['tankcapacity'];
			$tanktype = $qryresult['tanktype'];
			$contact = $qryresult['contact'];
			$mapimage = $qryresult['mapimage'];
			$tz=$qryresult['tz'];
			$reason = "update";
			$admin_group = $qryresult['admin_group'];
			$contactperson = $qryresult['contactperson'];
			$hubee = $qryresult['hubee'];
			$hubeeAdmin = $qryresult['hubeeAdmin'];
			$expdate = $qryresult['expdate'];
}

if(isset($_POST['yearextend']))
{
	$fgmembersite->RedirectToformURL($fgmembersite->ExtendforOneyear($_POST['yearextend']),$_SERVER['PHP_SELF'],'formrespond.php');
	exit;
}
if(isset($_POST['freeextend']))
{
	$fgmembersite->RedirectToformURL($fgmembersite->FreeExtend($_POST['freeextend']),$_SERVER['PHP_SELF'],'formrespond.php');
	exit;
}
if(isset($_POST['sharewith']))
{
	$fgmembersite->RedirectToformURL($fgmembersite->sharewith($_POST['sharewith']),$_SERVER['PHP_SELF'],'formrespond.php');
	exit;
}

if(isset($_POST['objectsubmit']))
{
	//$fgmembersite->InsertObject($_POST);
		$fgmembersite->RedirectToformURL($fgmembersite->InsertObject($_POST),$_SERVER['PHP_SELF'],'formrespond.php');
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
    <title><?= $fgmembersite->Title1 ?> | Device Settings</title>

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

</script>
<script>
function delconfirm(id){
  if (confirm("Are you sure you want to delete this record?")) {
  document.getElementById(id).submit();
  }
}
function delconfirmgroup(id){
  if (confirm("Are you sure you want to delete this Group?")) {
  document.getElementById('admingroup'+id).submit();
  }
}
function addconfirmgroup(){
  if (isCharactorValide(document.getElementById('addadmingroup').value)) {
  document.getElementById('admingroupadd').submit();
  }else{
	alert("Group name not valide. Special charactors and Blank names are not allowed!");
  }
}
function userload(id){
  document.getElementById('userload'+id).submit();
}
function freeextend(id){
	document.getElementById('extend'+id).submit();
}
function yearextend(id){
	if (confirm("Are you sure you want to extend for one year?")) {
		document.getElementById('yearextend'+id).submit();
	}
}
function sharewith(id){
	document.getElementById('sharewith'+id).submit();
}
function superyearextend(id){
	if (confirm("Are you sure you want to extend for one year?")) {
		document.getElementById('superyearextend'+id).submit();
	}
}
function setspeed(sysno){
var limit = document.getElementById("speed"+sysno).value;
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
					alert(xmlhttp.responseText);
			    }
		  }
	xmlhttp.open("GET","servertoterminal.php?speedlimit="+sysno+"&limit="+limit);
	xmlhttp.send();
}
function trackinterval(sysno){
var limit = document.getElementById("trackinterval"+sysno).value;
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
					alert(xmlhttp.responseText);
			    }
		  }
	xmlhttp.open("GET","servertoterminal.php?trackinterval="+sysno+"&interval="+limit);
	xmlhttp.send();
}
function validateForm() {
    var oldobjno = document.forms["myForm"]["oldobjsysno"].value;
	var newobjno = document.forms["myForm"]["objsysno"].value;
	var objname = document.forms["myForm"]["objname"].value;
	
	if(newobjno == "" || hasWhiteSpace(newobjno))
	{
		alert("please enter valide tracking imei number without spaces");
		return false;
	}
	/*
	if(objname == "" || hasWhiteSpace(objname))
	{
		alert("please enter valide device name without spaces");
		return false;
	}
	*/
	if(!isValidDate(document.forms["myForm"]["fixeddate"].value))
	{
		alert("please enter valide date for fixed date 'yyyy-mm-dd'");
		return false;
	}
	var reson = document.forms["myForm"]["reson"].value;
	if(reson != "new")
	{
		if (oldobjno == newobjno) {
			return true;
		}else{
			var r = confirm("Do you really need to replace the object "+oldobjno+" to "+newobjno+" ?");
			if (r == true) {
				return true;
			} else {
				return false;
			}
		}
	}
}

function isCharactorValide(stringset)
{
	if(stringset.trim()=="")
	{
		return false;
	}
	var format = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;

	if(format.test(stringset)){
	  return false;
	} else {
	  return true;
	}
}

function isValidDate(dateString) {
  var regEx = /^\d{4}-\d{2}-\d{2}$/;
  if(!dateString.match(regEx)) return false;  // Invalid format
  var d = new Date(dateString);
  if(Number.isNaN(d.getTime())) return false; // Invalid date
  return d.toISOString().slice(0,10) === dateString;
}
function hasWhiteSpace(s) {
  return s.indexOf(' ') >= 0;
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
                                        <th>Object Name</th>
                                        <th>System Number</th>
                                        <th>Sim Number</th>
										<th>EXP Date</th>
										<th>Actvate</th>
                                        <th data-hide="phone,tablet">Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
								<?php
									echo $fgmembersite->ObjectManagementList("");
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
							Edit Settings
						</div>
						<div class="panel-body">
						<form class="form-horizontal" method="post" onsubmit="return validateForm()" name="myForm">
						 <input type="hidden" name="objectsubmit" value="ok" />
						 <input type="hidden" name="isadmin" value="admin" />
						 <input type="hidden" name="oldobjsysno" value="<?php echo $objsysno;?>"/>
						 <input type="hidden" name="reson" value="<?php echo $reason;?>"/>
						 
						<div class="form-group"><label class="col-sm-2 control-label">Name</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $objname; ?>" id="objname" name="objname" type="text"/></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Tracking Number</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $objsysno; ?>" id="objsysno" name="objsysno" type="text"/></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Sim Number</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $simno; ?>" id="simno" name="simno" type="text"/></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Image</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="mapimage" name="mapimage">
										<option value="1" <?php if($mapimage=='1'){echo 'selected';} ?>>
										 boat
										</option>
										<option value="2" <?php if($mapimage=='2'){echo 'selected';} ?>>
										 camper
										</option>
										<option value="3" <?php if($mapimage=='3'){echo 'selected';} ?>>
										 car
										</option>
										<option value="4" <?php if($mapimage=='4'){echo 'selected';} ?>>
										 caravan
										</option>
										<option value="5" <?php if($mapimage=='5'){echo 'selected';} ?>>
										 digger
										</option>
										<option value="6" <?php if($mapimage=='6'){echo 'selected';} ?>>
										 horsebox
										</option>
										<option value="7" <?php if($mapimage=='7'){echo 'selected';} ?>>
										 motorbike
										</option>
										<option value="8" <?php if($mapimage=='8'){echo 'selected';} ?>>
										 pin
										</option>
										<option value="9" <?php if($mapimage=='9'){echo 'selected';} ?>>
										 scooter
										</option>
										<option value="10" <?php if($mapimage=='10'){echo 'selected';} ?>>
										 semi
										</option>
										<option value="11" <?php if($mapimage=='11'){echo 'selected';} ?>>
										 tractor
										</option>
										<option value="12" <?php if($mapimage=='12'){echo 'selected';} ?>>
										 trailer
										</option>
										<option value="13" <?php if($mapimage=='13'){echo 'selected';} ?>>
										 truck
										</option>
										<option value="14" <?php if($mapimage=='14'){echo 'selected';} ?>>
										 van
										</option>
									</select>
							</div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Time Zone</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="tz" name="tz">
										<option timeZoneId="1" gmtAdjustment="GMT-12:00" useDaylightTime="0" value="-720" <?php if($tz=='-720'){echo 'selected';} ?>>(GMT-12:00) </option>
										<option timeZoneId="2" gmtAdjustment="GMT-11:00" useDaylightTime="0" value="-660" <?php if($tz=='-660'){echo 'selected';} ?>>(GMT-11:00) </option>
										<option timeZoneId="3" gmtAdjustment="GMT-10:00" useDaylightTime="0" value="-600" <?php if($tz=='-600'){echo 'selected';} ?>>(GMT-10:00) </option>
										<option timeZoneId="4" gmtAdjustment="GMT-09:00" useDaylightTime="1" value="-540" <?php if($tz=='-540'){echo 'selected';} ?>>(GMT-09:00) </option>
										<option timeZoneId="5" gmtAdjustment="GMT-08:00" useDaylightTime="1" value="-480" <?php if($tz=='-480'){echo 'selected';} ?>>(GMT-08:00) </option>
										<option timeZoneId="9" gmtAdjustment="GMT-07:00" useDaylightTime="1" value="-420" <?php if($tz=='-420'){echo 'selected';} ?>>(GMT-07:00) </option>
										<option timeZoneId="13" gmtAdjustment="GMT-06:00" useDaylightTime="0" value="-360" <?php if($tz=='-360'){echo 'selected';} ?>>(GMT-06:00) </option>
										<option timeZoneId="16" gmtAdjustment="GMT-05:00" useDaylightTime="1" value="-300" <?php if($tz=='-300'){echo 'selected';} ?>>(GMT-05:00) </option>
										<option timeZoneId="20" gmtAdjustment="GMT-04:00" useDaylightTime="1" value="-240" <?php if($tz=='-240'){echo 'selected';} ?>>(GMT-04:00) </option>
										<option timeZoneId="21" gmtAdjustment="GMT-03:30" useDaylightTime="1" value="-210" <?php if($tz=='-210'){echo 'selected';} ?>>(GMT-03:30) </option>
										<option timeZoneId="22" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-180" <?php if($tz=='-180'){echo 'selected';} ?>>(GMT-03:00) </option>
										<option timeZoneId="26" gmtAdjustment="GMT-02:00" useDaylightTime="1" value="-120" <?php if($tz=='-120'){echo 'selected';} ?>>(GMT-02:00) </option>
										<option timeZoneId="27" gmtAdjustment="GMT-01:00" useDaylightTime="0" value="-60" <?php if($tz=='-60'){echo 'selected';} ?>>(GMT-01:00) </option>
										<option timeZoneId="29" gmtAdjustment="GMT+00:00" useDaylightTime="0" value="0" <?php if($tz=='0'){echo 'selected';} ?>>(GMT+00:00) </option>
										<option timeZoneId="35" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="60" <?php if($tz=='60'){echo 'selected';} ?>>(GMT+01:00) </option>
										<option timeZoneId="44" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="120" <?php if($tz=='120'){echo 'selected';} ?>>(GMT+02:00) </option>
										<option timeZoneId="48" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="180" <?php if($tz=='180'){echo 'selected';} ?>>(GMT+03:00) </option>
										<option timeZoneId="49" gmtAdjustment="GMT+03:30" useDaylightTime="1" value="210" <?php if($tz=='210'){echo 'selected';} ?>>(GMT+03:30) </option>
										<option timeZoneId="52" gmtAdjustment="GMT+04:00" useDaylightTime="1" value="240" <?php if($tz=='240'){echo 'selected';} ?>>(GMT+04:00) </option>
										<option timeZoneId="53" gmtAdjustment="GMT+04:30" useDaylightTime="0" value="270" <?php if($tz=='270'){echo 'selected';} ?>>(GMT+04:30) </option>
										<option timeZoneId="55" gmtAdjustment="GMT+05:00" useDaylightTime="0" value="300" <?php if($tz=='300'){echo 'selected';} ?>>(GMT+05:00) </option>
										<option timeZoneId="57" gmtAdjustment="GMT+05:30" useDaylightTime="0" value="330" <?php if($tz=='330'){echo 'selected';} ?>>(GMT+05:30) </option>
										<option timeZoneId="58" gmtAdjustment="GMT+05:45" useDaylightTime="0" value="345" <?php if($tz=='345'){echo 'selected';} ?>>(GMT+05:45) </option>
										<option timeZoneId="60" gmtAdjustment="GMT+06:00" useDaylightTime="0" value="360" <?php if($tz=='360'){echo 'selected';} ?>>(GMT+06:00) </option>
										<option timeZoneId="61" gmtAdjustment="GMT+06:30" useDaylightTime="0" value="390" <?php if($tz=='390'){echo 'selected';} ?>>(GMT+06:30) </option>
										<option timeZoneId="63" gmtAdjustment="GMT+07:00" useDaylightTime="1" value="420" <?php if($tz=='420'){echo 'selected';} ?>>(GMT+07:00) </option>
										<option timeZoneId="68" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="480" <?php if($tz=='480'){echo 'selected';} ?>>(GMT+08:00) </option>
										<option timeZoneId="71" gmtAdjustment="GMT+09:00" useDaylightTime="1" value="540" <?php if($tz=='540'){echo 'selected';} ?>>(GMT+09:00) </option>
										<option timeZoneId="73" gmtAdjustment="GMT+09:30" useDaylightTime="0" value="570" <?php if($tz=='570'){echo 'selected';} ?>>(GMT+09:30) </option>
										<option timeZoneId="78" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="600" <?php if($tz=='600'){echo 'selected';} ?>>(GMT+10:00) </option>
										<option timeZoneId="79" gmtAdjustment="GMT+11:00" useDaylightTime="1" value="660" <?php if($tz=='660'){echo 'selected';} ?>>(GMT+11:00) </option>
										<option timeZoneId="81" gmtAdjustment="GMT+12:00" useDaylightTime="0" value="720" <?php if($tz=='720'){echo 'selected';} ?>>(GMT+12:00) </option>
										<option timeZoneId="82" gmtAdjustment="GMT+13:00" useDaylightTime="0" value="780" <?php if($tz=='780'){echo 'selected';} ?>>(GMT+13:00) </option>
									</select>
							</div>
						</div>
						<div class="form-group"  style="display:none;"><label class="col-sm-2 control-label">Fuel Tank Capasity</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $tankcapacity;   ?>" id="tankcapacity" name="tankcapacity" type="text"/></div>
						</div>
						<div class="form-group"  style="display:none;"><label class="col-sm-2 control-label">Tank Type</label>
							<div class="col-sm-10">
								   <select class="select form-control"  id="tanktype" name="tanktype">
									<option value="0" <?php if($tanktype=='0'){echo 'selected';} ?>>
									 BOX
									</option>
									<option value="1" <?php if($tanktype=='1'){echo 'selected';} ?>>
									 Cylinder
									</option>
								   </select>
							</div>
						</div>
						<div class="form-group" style="display:none;"><label class="col-sm-2 control-label" >Origine</label>
							<div class="col-sm-10">
								   <select class="select form-control"  id="origine" name="origine">
									<option value="">
									 Select
									</option>
									<?php echo $fgmembersite->GeofenceList($origine) ?>
								   </select>
							</div>
						</div>
						<div class="form-group" style="display:none;"><label class="col-sm-2 control-label">Destnation</label>
							<div class="col-sm-10">
								   <select class="select form-control"  id="Destnation" name="Destnation">
									<option value="">
									 Select
									</option>
									<?php echo $fgmembersite->GeofenceList($Destnation) ?>
								   </select>
							</div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Model</label>
							<div class="col-sm-10">
								   <select class="select form-control"  id="model" name="model">
									<option value="">
									 Select
									</option>
									<option value="Teltonika" <?php if($model=='Teltonika'){echo 'selected';} ?>>
									 Teltonika
									</option>
									<option value="Calamp" <?php if($model=='Calamp'){echo 'selected';} ?>>
									 Calamp
									</option>
									<option value="Kingwo" <?php if($model=='Kingwo'){echo 'selected';} ?>>
									 Kingwo
									</option>
								   </select>
							</div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Contact No</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $contact;   ?>" id="contact" name="contact" type="text"/></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Contact Person</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $contactperson;   ?>" id="contactperson" name="contactperson" type="text"/></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Fixed Date</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $fixeddate;   ?>" id="fixeddate" name="fixeddate" type="text"/></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Fixed by</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $fixedby;   ?>" id="fixedby" name="fixedby" type="text"/></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Exp Date</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $expdate;   ?>" id="expdate" name="expdate" type="text"/></div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Activate</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="activate" name="activate">
										<option value="0" <?php if($activate=='0'){echo 'selected';} ?>>
										 Deactive
										</option>
										<option value="1" <?php if($activate=='1'){echo 'selected';} ?>>
										 Active
										</option>
									</select>
							</div>
						</div>
						<div class="form-group"  style="display:none;"><label class="col-sm-2 control-label">NTC API</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="sendntc" name="sendntc">
										<option value="0" <?php if($sendntc=='0'){echo 'selected';} ?>>
										 Deactive
										</option>
										<option value="1" <?php if($sendntc=='1'){echo 'selected';} ?>>
										 Active
										</option>
									</select>
							</div>
						</div>
						<div class="form-group"  style="display:none;"><label class="col-sm-2 control-label">Camera</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="cam1" name="cam1">
										<option value="0" <?php if($cam1=='0'){echo 'selected';} ?>>
										 Off
										</option>
										<option value="1" <?php if($cam1=='1'){echo 'selected';} ?>>
										 On
										</option>
									</select>
							</div>
						</div>
						<div class="form-group"  style="display:none;"><label class="col-sm-2 control-label">Show public</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="hubee" name="hubee">
										<option value="0" <?php if($hubee=='0'){echo 'selected';} ?>>
										 Off
										</option>
										<option value="1" <?php if($hubee=='1'){echo 'selected';} ?>>
										 On
										</option>
									</select>
							</div>
						</div>
						<div class="form-group"  style="display:none;"><label class="col-sm-2 control-label">Show public Admin</label>
							<div class="col-sm-10">
									<select class="select form-control"  id="hubeeAdmin" name="hubeeAdmin">
										<option value="0" <?php if($hubeeAdmin=='0'){echo 'selected';} ?>>
										 Off
										</option>
										<option value="1" <?php if($hubeeAdmin=='1'){echo 'selected';} ?>>
										 On
										</option>
									</select>
							</div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Admin group</label>
							<div class="col-sm-10">
								   <select class="select form-control"  id="admin_group" name="admin_group">
									<option value="0" <?php if($admin_group==0){echo 'selected';} ?>>
									 Uncategorized
									</option>
									<?php echo $fgmembersite->admin_groupList($admin_group) ?>
								   </select>
							</div>
						</div>
						<div class="form-group"><label class="col-sm-2 control-label">Discription</label>
							<div class="col-sm-10"><input class="form-control" value="<?php echo $objectdiscription;   ?>" id="objectdiscription" name="objectdiscription" type="text"/></div>
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


<div class="content">
	<div class="row">
		<div class="col-lg-10">
					<div class="hpanel">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="data_Device1">
                                <thead>
                                    <tr>
                                        <th> Admin Group Name</th>
                                        <th data-hide="phone,tablet">Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
								<?php
									echo $fgmembersite->AdminGroupeTable();
								?>
								<td>
								<form id='admingroupadd' action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'><input type='text' id='addadmingroup' name='addadmingroup' value=''/></form>
								</td>
								<td>
								<a class='btn btn-primary btn-sm' onclick="addconfirmgroup('admingroupadd')" ><i class='glyphicon glyphicon-edit icon-white'></i>Add Group</a>
								</td>
                                </tbody>
                            </table>
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
            buttons: [
                {extend: 'copy',className: 'btn-sm'},
                {extend: 'csv',title: 'Device DATA', className: 'btn-sm'},
                {extend: 'pdf', title: 'Device DATA', className: 'btn-sm'},
                {extend: 'print',className: 'btn-sm'}
            ]
        });
        $('#data_Device1').dataTable( {
            dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            buttons: []
        });
    });
	

</script>
</body>
</html>