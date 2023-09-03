<?PHP
require_once("./include/membersite_config.php");

if(isset($_GET['code']))
{
   if($fgmembersite->ConfirmUser())
   {
        //$fgmembersite->RedirectToURL("thank-you-regd.html");
		$fgmembersite->RedirectToformURL('Thank you for the registration! <br>Now you can login to gsupertrack.com','login.php','formrespond.php');
		exit;
   }else{
	   	$fgmembersite->RedirectToformURL('Confirmation code error! <br>Please contact gsupertrack.com info@gsupertrack.com','login.php','formrespond.php');
		exit;
   }
}
	   	$fgmembersite->RedirectToformURL('Confirmation code error! <br>Please contact gsupertrack.com info@gsupertrack.com','login.php','formrespond.php');
		exit;
?>