<?PHP
require_once("./include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}


if(!$fgmembersite->DBLogin())
{
	echo "Not connected!";
}


//if(isset($_GET['image_id'])) {

$sql = "SELECT `id`, `sysno`, `datetime`, `picdata` FROM `picturedata` WHERE id='".$_GET['image_id']."'"; 

$result = mysql_query($sql,$fgmembersite->connection);

$row = mysql_fetch_array($result);

//header("Content-type: " . $row["imageType"]);
header("Content-type:  image/png");
echo $row["picdata"];
//}

?>
