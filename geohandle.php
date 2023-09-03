<?php
error_reporting(0);
require_once("./include/membersite_config.php");
require_once("./include/Routes.php");
include_once('./include/geoPHP/geoPHP.inc');

if(isset($_GET['geolist']))
{
	echo $fgmembersite->geolist();
	
}
if($_GET['reson']=='insert')
{
	echo $fgmembersite->insertgeolist($_GET);
}
if($_GET['reson']=='delete')
{
	echo $fgmembersite->deletegeolist($_GET);
}
if(isset($_GET['geopointlist']))
{
	echo $fgmembersite->geolistpoint();
}
if($_GET['reson']=='insertpoint')
{
	echo $fgmembersite->insertgeolistpoint($_GET);
}
if($_GET['reson']=='deletepoint')
{
	echo $fgmembersite->deletegeolistpoint($_GET);
}
if(isset($_GET['getpointvalues']))
{
	echo $fgmembersite->getpointvalues($_GET['getpointvalues']);
}
if(isset($_GET['getpoligonvalues']))
{
	echo $fgmembersite->getpoligonvalues($_GET['getpoligonvalues']);
}
if(isset($_POST['ListRoute']))
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Please login!";
		return false;
	} 
	echo $fgmembersite->geolistRoutes();
	exit;
}
	
if(isset($_POST['AddRoute']))
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Please login!";
		return false;
	} 
	AddRoute($_POST,$fgmembersite);
}
if(isset($_POST['GetRoute']))
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Please login!";
		return false;
	} 
	GetRoute($_POST,$fgmembersite);
}
if(isset($_POST['DelRoute']))
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Please login!";
		return false;
	} 
	echo Delete_Route($_POST,$fgmembersite);
	exit;
}
if(isset($_POST['TaskAdd']))
{
	echo Add_Task($_POST,$fgmembersite);
	exit;
}
if(isset($_POST['UpdateTaskTable']))
{
	echo geolistTasks($_POST,$fgmembersite);
	exit;
}
if(isset($_POST['del_Task']))
{
	echo del_Task($_POST,$fgmembersite);
	exit;
}
if(isset($_POST['CompleteTask']))
{
	echo CompleteTask($_POST,$fgmembersite);
	exit;
}
if(isset($_POST['StartTask']))
{
	echo StartTask($_POST,$fgmembersite);
	exit;
}
if(isset($_POST['SheduleList']))
{
	echo TasksListFrontend($_POST,$fgmembersite);
	exit;
}
if(isset($_POST['SheduleTaskStarted']))
{
	echo TasksListFrontendStarted($_POST,$fgmembersite);
	exit;
}
if(isset($_POST['TaskProgress']))
{
	echo TaskProgress($_POST,$fgmembersite);
	exit;
}
?>