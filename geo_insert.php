<?php
//error_reporting(0);
$coordinates = $_GET['coordinates'];
$gname = $_GET['gname'];
if(isset($_GET['remarks'])){
$remarks = $_GET['remarks'];
}else{
$remarks = "";
}
require_once("./include/membersite_config.php");
		if(!$fgmembersite->DBLogin())
        {
            $fgmembersite->HandleError("Database login failed!");
            return false;
        } 

$qry = "INSERT INTO geofences
           (geo_name
           ,remarks
           ,cordinates)
     VALUES
           ('".$gname."'
           ,'".$remarks."'
           ,'".trim($coordinates)."')";

$stmt = mysqli_query($fgmembersite->connection,$qry);
if(!$stmt){
echo $gname." Geofence Not insert!";
}
else{
echo $gname." Geofence Succesfully insert!";
}
?>