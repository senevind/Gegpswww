<?PHP
require_once("./include/membersite_config.php");

$success = false;
if($fgmembersite->ResetPassword())
{
	$fgmembersite->RedirectToformURL("<h2>Password is Reset Successfully</h2>Your new password is sent to your email address.","logout.php",'formrespond.php');
	exit;
}else{
	$fgmembersite->RedirectToformURL("<h2>Password is not Reset</h2>","logout.php",'formrespond.php');
	exit;
}

?>
