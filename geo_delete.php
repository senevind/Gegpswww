<?php
error_reporting(0);
$del_id=$_GET['del_id'];
$del_name=$_GET['del_name'];
//$del_id ="89";
require_once("./include/membersite_config.php");

if(!$fgmembersite->DBLogin())
{
	$fgmembersite->HandleError("Database login failed!");
	return false;
} 


$qry = "DELETE FROM geofences
      WHERE id='".$del_id."'";
//echo $sqldel;
$stmt = mysqli_query($fgmembersite->connection,$qry);
if(!$stmt){
echo $del_name." Not deleted!";
}
else{
echo $del_name." Succesfully deleted!";
}
?>