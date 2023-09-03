<?php
set_time_limit(300);
require_once("./include/membersite_config.php");

if(!$fgmembersite->DBLogin())
{
	//$fgmembersite->HandleError("Database login failed!");
	echo "Database login failed!";
	return false;
}

if(!isset($_GET['id']))
{
	exit;
}

$TrackingID = $_GET['id'];
$DateTime = $_GET['LocTime'];
$Username = $_GET['username'];
$Longitude = $_GET['LocLongitude'];
$Latitude = $_GET['LocLatitude'];
$reason = $_GET['reason'];

$selfe = 0;
$Sleepy = 0;
$HandLost = 0;
$priority = 3;
if($reason == "Selfie")
{
	$selfe = 1;
}
if($reason == "Sleepy")
{
	$Sleepy = 1;
	$priority = 1;
}
if($reason == "HandLost")
{
	$HandLost = 1;
	$priority = 1;
}
//print_r($_GET);
$phpdatetime = new DateTime($DateTime);
$Date = $phpdatetime->format('Y-m-d');
$FilePath = 'uploads/'.$Date.'/';

$uploadfile = "";

//https://rrr.gsupertrack.com/app_photoupload.php?id=123&LocTime=2021-06-27 22:42:00&username=Nuwan&LocLongitude=6.23456&LocLatitude=81.5432



$isGood = false;
try{
	
if (!file_exists($FilePath)) {
    mkdir($FilePath, 0777, true);
}

$uploaddir = $FilePath;

$fileName = basename($_FILES['fileToUpload']['name']);
$filenameEdited = preg_replace('/\s+/', '_', $fileName);
$uploadfile = $uploaddir . $filenameEdited;

//CHECK IF ITS AN IMAGE OR NOT
$allowed_types = array ('image/jpeg', 'image/png', 'image/bmp', 'image/gif' );
$fileInfo = finfo_open(FILEINFO_MIME_TYPE);
$detected_type = finfo_file( $fileInfo, $_FILES['fileToUpload']['tmp_name'] );
if($_FILES['fileToUpload']['error'] == "1")
{
	echo json_encode(array("status"=>"error","value"=>"file is too large"));
	die;		// ( '{"status" : "error", "value" : "file is too large"}' );
}

if ( !in_array($detected_type, $allowed_types) ) {
	echo json_encode(array("status"=>"error","value"=>"Not a valid image"));
	die;		// ( '{"status" : "error", "value" : "Not a valid image"}' );
}

//

if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadfile)) {
//echo "File is valid, and was successfully uploaded.\n";
echo json_encode(array("status"=>"succes","value"=>$fileName));

	$sql = "INSERT INTO `alerts`(`trackingid`, `alerttime`, `Sleepy`, `HandLost`, `selfe`, `longitude`, `latitude`, `picname`) 
			VALUES ('".$TrackingID."','".$DateTime."','".$Sleepy."','".$HandLost."','".$selfe."','".$Longitude."','".$Latitude."','".$uploadfile."')"; 

	$result = mysql_query($sql,$fgmembersite->connection);
	
	$sql2 = "INSERT INTO `gpsmntcenotify`(`imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, `notifydate`, `PicPath`) 
	VALUES ('".$TrackingID."','','Device photo alert','The device camera detected ".$reason." at GMT(".$DateTime.")','0','".$priority."','".$DateTime."','".$uploadfile."')";

	$result = mysql_query($sql2,$fgmembersite->connection);
	exit;

$isGood = true;
} else {
//echo "Possible file upload attack!\n";
echo json_encode(array("status"=>"error","value"=>"Unable to Upload Profile Image"));
//echo '{"status" : "error", "value" : "Unable to Upload Profile Image"}';
}

}
catch(Exception $e) {
	echo json_encode(array("status"=>"error","value"=>$e->getMessage()));
	exit;
//echo '{"status" : "error", "value" : "'.$e->getMessage().'"}';
}



?>
