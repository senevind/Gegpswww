<?php
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
require_once("./include/membersite_config.php");
require_once("./include/reports.php");


if(isset($_POST['reason']) && $_POST['reason'] == "ForgotPwd")
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	header('Content-Type: application/json; charset=utf-8');
	$resultArray = $fgmembersite->EmailResetPasswordLink();
	
	if($resultArray)
	{
		echo json_encode(array('status'=>"succes",'value'=>"Reset link sent to the email."));
	}else
	{
		echo json_encode(array('status'=>"error",'value'=>$fgmembersite->GetErrorMessage()));
	}
	exit;
}


if(isset($_POST['LastTracks']))
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	
	$resultArray = SaveLastTracks($_POST['TaskJsonData'],$fgmembersite,$_POST['user']);
	header('Content-Type: application/json; charset=utf-8');
	if($resultArray)
	{
		echo json_encode(array('status'=>true,'values'=>$resultArray));
	}else
	{
		echo json_encode(array('status'=>false,'values'=>$resultArray));
	}
	exit;
}

if(isset($_POST['LoadLastTracks']))
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	header('Content-Type: application/json; charset=utf-8');
	$resultArray = LoadLastTracks($fgmembersite,$_POST['user']);
	
	if(count($resultArray) != 0)
	{
		echo json_encode(array('status'=>true,'values'=>$resultArray));
	}else
	{
		echo json_encode(array('status'=>false,'values'=>$resultArray));
	}
	exit;
}

if(isset($_POST['reason']) && $_POST['reason'] == "registerUser")
{
	//print_r($_POST);
	//exit;
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	header('Content-Type: application/json; charset=utf-8');
	$resultArray = $fgmembersite->RegisterUser();
	
	if($resultArray)
	{
		echo json_encode(array('status'=>"succes",'value'=>"User_registed"));
	}else
	{
		echo json_encode(array('status'=>"error",'value'=>str_replace("<br />","",$fgmembersite->GetErrorMessage())));
	}
	exit;
}

if(isset($_POST['reason']) && $_POST['reason'] == "contactUs")
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	header('Content-Type: application/json; charset=utf-8');
	$resultArray = ContactUs();
	
	if($resultArray)
	{
		echo json_encode(array('status'=>"succes",'value'=>"sent"));
	}else
	{
		echo json_encode(array('status'=>"error",'value'=>"Message not sent!"));
	}
	exit;
}

if(isset($_POST['reason']) && $_POST['reason'] == "NotificDelete")
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	header('Content-Type: application/json; charset=utf-8');
	$resultArray = $fgmembersite->NotificDelete($_POST);
	
	if($resultArray)
	{
		echo json_encode(array('status'=>"succes",'value'=>"Deleted!"));
	}else
	{
		echo json_encode(array('status'=>"error",'value'=>"Not deleted!"));
	}
	exit;
}


if(isset($_POST['reason']) && $_POST['reason'] == "VehicleList")
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	header('Content-Type: application/json; charset=utf-8');
	$resultArray = $fgmembersite->AppTripReportOperation();
	//$resultArray = $Reports->PlaybackReport("RIG166","2022-04-21 00:00:01","2022-04-21 23:59:59",$fgmembersite->connection,$fgmembersite->connection);
	echo json_encode($resultArray);
	exit;
}

if((isset($_GET['reason']) && $_GET['reason'] == "RevGeocoordinates") || (isset($_POST['reason']) && $_POST['reason'] == "RevGeocoordinates"))
{
	header('Content-Type: application/json; charset=utf-8');
	$resultArray = $Reports->NominatimRevCordinates($_GET['lat'],$_GET['lon']);
	echo $resultArray;
	exit;
}



///////////////////////////////////		Login api	///////////////////////////////////////
if(isset($_POST['type']) && $_POST['type']=="login")
{
	echo $fgmembersite->Login();
	exit;
}

if(isset($_GET['Token']))
{
	if(!$fgmembersite->LoginWithToken())
	{
		echo "Incorrect Login";
		exit;
	}
}
if(isset($_POST['reson']) && $_POST['reson'] == "InsertSystemSettings")
{
	$qryresult = $fgmembersite->InsertSystemSettings($_POST);
	if($qryresult)
	{
		echo json_encode(array('status'=>"succes",'value'=>""));
	}else
	{
		echo json_encode(array('status'=>"error",'value'=>""));
	}
	exit;
}

if(isset($_POST['reson']) && $_POST['reson'] == "systemSettings")
{
	$qryresult = $fgmembersite->LoadSystemSettings("array");
	echo json_encode(array('status'=>"succes",'value'=>$qryresult));
	exit;
}

if(isset($_GET['sysnostring']))
{
	if(!$fgmembersite->CheckLogin())
	{
		echo "Incorrect Login";
		exit;
	}
	$fgmembersite->geocordinatesstr($_GET['sysnostring']);
	exit;
}

if(isset($_POST['dawiseSummery']))
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	
	$reportArray = $Reports->DaywiceReport($fgmembersite,$_POST['date'],$fgmembersite->connection,$_POST['objectName']);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(array('status'=>true,'values'=>$reportArray));
	exit;
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

if(isset($_POST['reason']) && $_POST['reason'] == "ReportHistory")
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	header('Content-Type: application/json; charset=utf-8');
	$resultArray = $Reports->AppReportOperation($_POST['VehicleNo'],$_POST['sttime'],$_POST['endtime'],$fgmembersite);
	//$resultArray = $Reports->AppReportOperation($_POST['VehicleNo'],$_POST['sttime']." 00:00:01",$_POST['endtime']." 23:59:59",$fgmembersite);
	//$resultArray = $Reports->PlaybackReport("RIG166","2022-04-21 00:00:01","2022-04-21 23:59:59",$fgmembersite->connection,$fgmembersite->connection);
	echo json_encode($resultArray);
	//echo "ReportHistory";
	exit;
}

if(isset($_POST['reason']) && $_POST['reason'] == "AppTripReport")
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	header('Content-Type: application/json; charset=utf-8');
	$resultArray = $Reports->AppTripReportOperation($_POST['VehicleNo'],$_POST['sttime'],$_POST['endtime'],$fgmembersite);
	//$resultArray = $Reports->AppTripReportOperation("RIG166","2022-04-21 00:00:01","2022-04-21 23:59:59",$fgmembersite);
	echo json_encode($resultArray);
	exit;
}

if(isset($_POST['reason']) && $_POST['reason'] == "AppReportParking")
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	
	header('Content-Type: application/json; charset=utf-8');
	$resultArray = $Reports->ParkingReportApp($_POST,$fgmembersite->connection,$fgmembersite);
	//$resultArray = $Reports->AppTripReportOperation("RIG166","2022-04-21 00:00:01","2022-04-21 23:59:59",$fgmembersite);
	echo json_encode(array('Vehicleinfo'=>$resultArray));
	exit;
}

if(isset($_POST['reason']) && $_POST['reason'] == "dawiseSummery")
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	//print_r($_POST);
	//exit;
	$reportArray = array();
	header('Content-Type: application/json; charset=utf-8');
	$reportSubArray = $Reports->DaywiceReport($fgmembersite,$_POST['date'],$fgmembersite->connection,$_POST['objectName']);
	$reportArray[] = $reportSubArray;
	echo json_encode(array('Vehicleinfo'=>$reportArray));
	exit;
}

if(isset($_POST['Command']) && $_POST['Command'] == "Engine On")
{
	require_once("./include/tc_api.php");
	$qryresult = engineResume($_POST['DeviceID']);
	echo json_encode(array('status'=>"succes",'value'=>$qryresult));
	exit;
}

if(isset($_POST['Command']) && $_POST['Command'] == "Engine Off")
{
	require_once("./include/tc_api.php");
	$qryresult = engineStop($_POST['DeviceID']);
	echo json_encode(array('status'=>"succes",'value'=>$qryresult));
	exit;
}

if(isset($_POST['reason']) && $_POST['reason'] == "DaywiceReportByIMEI")
{
	if(!$fgmembersite->DBLogin())
	{
		echo "Not connected";
		return false;
	}
	//print_r($_POST);
	//exit;
	$reportArray = array();
	header('Content-Type: application/json; charset=utf-8');
	$reportSubArray = $Reports->DaywiceReportByIMEI($fgmembersite,$_POST['date'],$fgmembersite->connection,$_POST['object']);
	$reportArray[] = $reportSubArray;
	echo json_encode(array('Vehicleinfo'=>$reportArray));
	exit;
}



function ContactUs()
{	
	if(SendContactUsEmail($_POST['email'],$_POST['subject'],$_POST['msgbody']) == "Message sent!")
	{
		return true;
	}
	return false;
}

function SaveLastTracks($JsonStr,$fgmembersite,$user)
{
	$qry = "UPDATE `gpsusers` SET `LastTracks`='$JsonStr' WHERE `username` = '$user'";
	$stmt = mysqli_query($fgmembersite->connection,$qry);
	if($stmt)
	{
		return true;
	}
	return false;
}
function LoadLastTracks($fgmembersite,$user)
{
	$LastTracks = array();
	$qry = "SELECT `LastTracks` FROM `gpsusers` WHERE `username` = '$user'";
	$result = mysqli_query($fgmembersite->connection,$qry);
	
	if(!$result)
	{
		return false;
	}
	while($row = mysqli_fetch_array($result))
	{
		$LastTracks[] = $row['LastTracks'];
	}
	return $LastTracks;
}
function SendContactUsEmail($Senderemail,$subject,$msgbody)
{
	/**
	 * This example shows sending a message using a local sendmail binary.
	 */

	//require '../PHPMailerAutoload.php';
	require_once("./include/phpmailer/class.phpmailer.php");
	require_once("./include/phpmailer/class.smtp.php");

	$mail = new PHPMailer();

	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->Host = ""; // SMTP a utilizar. Por ej. ¿smtp.miservidor.com?
	$mail->Username = ""; // Correo completo a utilizar
	$mail->Password = ""; // Contraseña
	$mail->Port = 25; // Puerto a utilizar

	$mail->From = "hello@radioservices.co.uk"; // Desde donde enviamos (Para mostrar)
	$mail->FromName = "gps22.net";
	$mail->AddAddress("hello@radioservices.co.uk"); // Esta es la dirección a donde enviamos
	//$mail->AddAddress("senevind@gmail.com"); // Esta es la dirección a donde enviamos
	//$mail->AddCC("hello@nsit.lk"); // Copia
	//$mail->AddBCC("cuenta@dominio.com"); // Copia oculta
	$mail->IsHTML(true); // El correo se envía como HTML
	$mail->Subject = $subject; // Este es el titulo del email.

	$body = "From: ".$Senderemail."<br/>";
	$body .= $msgbody;
	$mail->Body = $body; // Mensaje a enviar

	//$mail->AltBody = "Hola mundo. Esta es la primer línea\n Acá continuo el mensaje"; // Texto sin html
	$exito = $mail->Send(); // Envía el correo.

	//send the message, check for errors
	if (!$exito) {
		return "Mailer Error: " . $mail->ErrorInfo;
	} else {
		return "Message sent!";
	}
}

?>