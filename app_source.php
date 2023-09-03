<?php
error_reporting(0);
require_once("./include/membersite_config.php");
require_once("./include/reports.php");

if($_POST['reason']=='GSTVersion')
{
	echo json_encode(array("status"=>"succes","value"=>"1.6"));
	exit;
}

if($_POST['type']=="login")
{
	echo $fgmembersite->app_login();
	exit;
}


if(!$fgmembersite->CheckLogin())
{
	if($fgmembersite->Login())
	   {
			//$fgmembersite->RedirectToURL($_SERVER["PHP_SELF"]);
	   }
}
if(!$fgmembersite->CheckLogin())
{
	echo "login error";
	exit;
}

if(isset($_POST['reson']) && $_POST['reson'] == "TrackIDValidate")
{

	if(!$fgmembersite->TrackIDValidate($_POST['username'],$_POST['TrackID']))
	{
		echo json_encode(array("status"=>"error","value"=>"This Tracking ID is taken by other user ! Please use different ID"));
	}else{
		echo json_encode(array("status"=>"succes","value"=>"Device Tracker ID Changed."));
	}
	exit;
}

if($_GET['mainlist']=="mainlist" || $_POST['mainlist']=="mainlist")
{
	header('Content-Type: application/json');
	echo $fgmembersite->getbuslistarray();
	exit;
}

if(isset($_GET['Xmainlist']) || isset($_POST['Xmainlist']))
{
	if($_GET['Xmainlist']=="mainlist" || $_POST['Xmainlist']=="mainlist")
	{
		header('Content-Type: application/json');
		echo $fgmembersite->Xam_getbuslistarray();
		exit;
	}
}

if(isset($_POST['XpassdataNotific']))
{
	header("Content-type: application/json");
	ini_set('max_execution_time', 300);

	if(!$fgmembersite->DBLogin())
	{
		$fgmembersite->HandleError("Database login failed!");
		return false;
	}

	$coninfo = $fgmembersite->connection;
	$busno=$_POST['busno'];
	$sttime=$_POST['sttime'];
	$endtime=$_POST['endtime'];

	$ext_con = $coninfo;

	echo $Reports->NotificPlaybackReport($busno,$sttime,$endtime,$coninfo,$ext_con);
}

if(isset($_POST['Xpassdata']))
{
	header("Content-type: application/json");
	ini_set('max_execution_time', 300);

	if(!$fgmembersite->DBLogin())
	{
		$fgmembersite->HandleError("Database login failed!");
		return false;
	}

	$coninfo = $fgmembersite->connection;
	$busno=$_POST['busno'];
	$sttime=$_POST['sttime'];
	$endtime=$_POST['endtime'];

	$ext_con = $coninfo;

	echo $Reports->PlaybackReport($fgmembersite,$busno,$sttime,$endtime);
}

if(isset($_POST['reson']) && $_POST['reson'] == "ChangePasswordInDBFrmUser")
{
	$params = json_decode($_POST['parameters'],true);
	if(!$fgmembersite->ChangePasswordInDBFrmUser($_POST['username'],$params['pwd']))
	{
		echo json_encode(array("status"=>"error","value"=>"Password not Changed!"));
	}else{
		echo json_encode(array("status"=>"succes","value"=>"Password Changed!"));
	}
	exit;
}

if(isset($_POST['reson']) && $_POST['reson'] == "UpdateDivFrmAPP")
{
	$params = json_decode($_POST['parameters'],true);
	//echo $params;
	if(!$fgmembersite->UpdateDivFrmAPP($params))
	{
		echo json_encode(array("status"=>"error","value"=>"Vehicle is not added!"));
	}else{
		echo json_encode(array("status"=>"succes","value"=>"Vehicle is added"));
	}
	exit;
}
if(isset($_POST['reson']) && $_POST['reson'] == "LoadObject")
{
	$params = json_decode($_POST['parameters'],true);
	//echo $params;
	$result = $fgmembersite->LoadObjectManagement($params['objsysno']);
	if(!$result)
	{
		echo json_encode(array("status"=>"error","value"=>"No vehicle details available!"));
	}else{
		echo json_encode(array("status"=>"succes","value"=>$result));
	}
	exit;
}
?>