<?PHP
error_reporting(0);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
require_once("./include/membersite_config.php");



if(isset($_GET['sysnostring'])){
	echo $fgmembersite->geocordinatesstr($_GET['sysnostring']);
	exit;
}

if(isset($_GET['sysnostringAddress'])){
	echo $fgmembersite->geoAddress($_GET['sysnostringAddress']);
	exit;
}
if($_GET['notific']=='notify'){
	echo "Notifications result";
	exit;
}

if(isset($_GET['lastnotific'])){
	echo $fgmembersite->LastNotification($_GET['user'],$_GET['lastnotific']);
	exit;
}

if(isset($_GET['LastTodayNotificationuser'])){
	echo $fgmembersite->LastTodayNotification($_GET['LastTodayNotificationuser']);
	//echo $_GET['LastTodayNotificationuser'];
	exit;
}

if(!$fgmembersite->CheckLogin())
{
	if(isset($_POST['submitted']))
	{
		$fgmembersite->Login();
	}
	if(!$fgmembersite->CheckLogin())
	{
		exit;
	}
}
if(isset($_GET['NotificationsJSON']))
{
	echo $fgmembersite->NotificationsJSON();
	exit;
}
if(isset($_GET['sysno'])){
	echo $fgmembersite->geocordinates($_GET['sysno']);
}

if(isset($_GET['reson']))
{
	if($_GET['reson']=='vcount'){
		echo $fgmembersite->vcount($_GET['reson']);
	}
	if($_GET['reson']=='onlinestatus'){
		echo $fgmembersite->onlinestatus($_GET['reson']);
	}
	if($_GET['reson']=='comvcount'){
		echo $fgmembersite->comvcount($_GET['reson']);
	}
	if($_GET['reson']=='nocomvcount'){
		echo $fgmembersite->nocomvcount($_GET['reson']);
	}
	if($_GET['reson']=='stpvcount'){
		echo $fgmembersite->stpvcount($_GET['reson']);
	}
	if($_GET['reson']=='idlevcount'){
		echo $fgmembersite->idlevcount($_GET['reson']);
	}
	if($_GET['reson']=='runnivcount'){
		echo $fgmembersite->runnivcount($_GET['reson']);
	}
	if($_GET['reson']=='buslistjson'){
		echo $fgmembersite->buslistjson();
		exit;
	}
}

if(isset($_GET['publicONOFF']))
{
	echo $fgmembersite->publicONOFF($_GET['publicONOFF'],$_GET['systemno']);
	//http://lkgps.gsupertrack.com/app_getcoordinates.php?publicONOFF=1&systemno=13137910000
}
if(isset($_GET['CheckPublicONOFF']))
{
	echo $fgmembersite->CheckPublicONOFF($_GET['CheckPublicONOFF']);
}
?>