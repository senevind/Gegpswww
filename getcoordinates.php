<?PHP
//error_reporting(0);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
require_once("./include/membersite_config.php");

if(isset($_GET['sysnostring'])){
	echo $fgmembersite->geocordinatesstr($_GET['sysnostring']);
	exit;
}

if(isset($_GET['resonAdmin']))
{
	if($_GET['resonAdmin']=='AddToLogVcountAdmin'){
		echo $fgmembersite->AddToLogVcountAdmin();
	}
	if($_GET['resonAdmin']=='AddToLogVcountAdminDeactive'){
		echo $fgmembersite->AddToLogVcountAdminDeactive();
	}
}

if(!$fgmembersite->CheckLogin())
{
    echo "-";
    exit;
}

if(isset($_GET['revgeocode']))
{
	echo $fgmembersite->revgeocode($_GET['lat'],$_GET['lng']);
	exit;
}

if(isset($_GET['reson']))
{
	if($_GET['reson']=='starred'){
		echo $fgmembersite->Starredupdate($_GET['sysno'],$_GET['yesno']);
		exit;
	}
	if($_GET['sysno']){
		echo $fgmembersite->geocordinates($_GET['sysno']);
	}
	if($_GET['reson']=='vcount'){
		echo $fgmembersite->vcount($_GET['reson']);
	}
	if($_GET['reson']=='todayalerts'){
		echo $fgmembersite->todayalerts($_GET['reson']);
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
	if($_GET['reson']=='alltodaymilage'){
		echo $fgmembersite->alltodaymilage($_GET['reson']);
	}
	if($_GET['reson']=='smallnotification'){
		header("Content-Type: application/json; charset=UTF-8");
		echo json_encode($fgmembersite->Notifications_Small());
		exit;
	}
}
if(isset($_GET['resonAdmin']))
{
	if($_GET['resonAdmin']=='vcount'){
		echo $fgmembersite->vcountAdmin($_GET['reson']);
	}
	if($_GET['resonAdmin']=='onlinestatus'){
		echo $fgmembersite->onlinestatusAdmin($_GET['reson']);
	}
	if($_GET['resonAdmin']=='comvcount'){
		echo $fgmembersite->comvcountAdmin($_GET['reson']);
	}
	if($_GET['resonAdmin']=='nocomvcount'){
		echo $fgmembersite->nocomvcountAdmin($_GET['reson']);
	}
	if($_GET['resonAdmin']=='stpvcount'){
		echo $fgmembersite->stpvcountAdmin($_GET['reson']);
	}
	if($_GET['resonAdmin']=='idlevcount'){
		echo $fgmembersite->idlevcountAdmin($_GET['reson']);
	}
	if($_GET['resonAdmin']=='runnivcount'){
		echo $fgmembersite->runnivcountAdmin($_GET['reson']);
	}
	if($_GET['resonAdmin']=='alltodaymilage'){
		echo $fgmembersite->alltodaymilageAdmin($_GET['reson']);
	}
	if($_GET['resonAdmin']=='AddToLogVcountAdmin'){
		echo $fgmembersite->AddToLogVcountAdmin();
	}
	if($_GET['resonAdmin']=='ThisMontInstallation'){
		echo $fgmembersite->ThisMontInstallation();
	}
	if($_GET['resonAdmin']=='ThisMonthRenewal'){
		echo $fgmembersite->ThisMonthRenewal();
	}
}
if(isset($_GET['notifyread'])){
	echo $fgmembersite->notifyasread($_GET['notifyread']);
	exit;
}

?>