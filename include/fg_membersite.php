<?PHP
/*
    Registration/Login script from HTML Form Guide
    V1.0

    This program is free software published under the
    terms of the GNU Lesser General Public License.
    http://www.gnu.org/copyleft/lesser.html
    

This program is distributed in the hope that it will
be useful - WITHOUT ANY WARRANTY; without even the
implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.

For updates, please visit:
http://www.html-form-guide.com/php-form/php-registration-form.html
http://www.html-form-guide.com/php-form/php-login-form.html
*/

require_once($_SERVER['DOCUMENT_ROOT'].'/include/phpmailer/PHPMailerAutoload.php');
//require_once($_SERVER['DOCUMENT_ROOT']."/include/class.phpmailer.php");
require_once($_SERVER['DOCUMENT_ROOT']."/include/formvalidator.php");

class FGMembersite
{

    var $admin_email;
    var $from_address;
    
    var $username;
    var $pwd;
    var $database;
    var $tablename;
    var $connection;
    var $rand_key;
    var $headuser;
    var $error_message;
     
	var $revgeoreqlink = "map.gsupertrack.com";
	var $revgeoreqkey = "pk.eyJ1Ijoic2VuZXZpbmQiLCJhIjoiY2szNGpmdDhnMG1uazNjcDNoZXpuYjVzdSJ9.wQRVyRViK67Hr4lwlXg5EQ";

	 
    //-----Initialization -------
    function FGMembersite()
    {
        $this->sitename = 'www.gegps.com';
        $this->rand_key = '0iQx5oBk66oVZep';
		$this->Title1 = "GEGPS ";
		$this->Title2 = "GEGPS ";
    }
    
    function InitDB($host,$uname,$pwd,$database)
    {
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        $this->tablename = "gpsusers";
		$this->headuser = "STD";
    }
	
	public function sqlservercon()
	{
		$serverName = $this->db_host.",1433";
		$connection = array("Database"=>$this->sqlservdb, "UID"=>$this->sqlservuser, "PWD"=>$this->sqlpass);
		$con = sqlsrv_connect( $serverName, $connection);
		if(!$con){
			echo "not conncted";
		}
		return $con;
	}
	
	
    function Sendemail($Senderemail,$subject,$msgbody)
    {
		/**
		 * This example shows sending a message using a local sendmail binary.
		 */

		//require '../PHPMailerAutoload.php';
		require_once("phpmailer/class.phpmailer.php");
		require_once("phpmailer/class.smtp.php");

		$mail = new PHPMailer();

		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->Host = ""; // SMTP a utilizar. Por ej. ¿smtp.miservidor.com?
		$mail->Username = ""; // Correo completo a utilizar
		$mail->Password = ""; // Contraseña
		$mail->Port = 25; // Puerto a utilizar

		$mail->From = "hello@radioservices.co.uk"; // Desde donde enviamos (Para mostrar)
		$mail->FromName = "gps22.net";
		$mail->AddAddress($Senderemail); // Esta es la dirección a donde enviamos
		//$mail->AddAddress("senevind@gmail.com"); // Esta es la dirección a donde enviamos
		//$mail->AddCC("hello@nsit.lk"); // Copia
		//$mail->AddBCC("cuenta@dominio.com"); // Copia oculta
		$mail->IsHTML(true); // El correo se envía como HTML
		$mail->Subject = $subject; // Este es el titulo del email.

		//$body = "From: ".$Senderemail."<br/>";
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
	
    function SendemailwithAttachment($to,$subject,$htmlbody,$path,$attName)
    {
		$htmlbody = $htmlbody."<br>".
        "Regards,<br>".
		"info@gsupertrack.com<br>".
		"System Administrator,<br>".
        $this->sitename;
		
		$mail = new PHPMailer();
		$mail->Host = "smtp.zoho.com";
		$mail->isSMTP();
		$mail->SMTPSecure = "tls";
		$mail->Port = 587;
		$mail->SMTPAuth = true;
		$mail->Username = 'system@gsupertrack.com';
		$mail->Password = 'neotracki123!@#';
		
		$mail->setFrom('system@gsupertrack.com', 'gsupertrack.com');
		$mail->addAddress($to);
		$file_to_attach = $path;//'PATH_OF_YOUR_FILE_HERE';
		$mail->AddAttachment( $file_to_attach , $attName );
		$mail->Subject = $subject;
		$mail->Body = $htmlbody;
		$mail->IsHTML(true);  
		if ($mail->send())
		{
			return true;
		}else{
			return false;
		}
    }	
	
	
	function ActivityLog($reason, $user, $systemno,$parameters)
	{
		$parameters = print_r($parameters,true);
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "INSERT INTO `activity_log`(`reason`, `user`, `systemno`, `parameters`) VALUES ('".$reason."','".$user."','".$systemno."','".$parameters."')";

		$stmt = mysqli_query($this->connection,$qry);

	}


	function revgeocode($lat,$lng)
	{
		$url = "https://".$this->revgeoreqlink."/api.revgeo.php?lat=$lat&lng=$lng&key=".$this->revgeoreqkey;
		return file_get_contents($url);
	}

///////////////////////////////////////////////// APP Functions start	//////////////////////////////////////////////////
	function app_login(){
        if(empty($_POST['username']))
        {
            return "not";
        }
        
        if(empty($_POST['password']))
        {
            return "not";
        }
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if(!$this->CheckLoginInDB($username,$password))
        {
            return "not";
        }
        return "logedin";
	}
	
	function app_moduleLogin()
    {
        if(empty($_POST['username']))
        {
            $this->HandleError("UserName is empty!");
            return false;
        }
        
        if(empty($_POST['password']))
        {
            $this->HandleError("Password is empty!");
            return false;
        }
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if(!isset($_SESSION)){ session_start(); }
        if(!$this->CheckLoginInDB($username,$password))
        {
            return false;
        }
        
        $_SESSION[$this->GetLoginSessionVar()] = $username;
        $_SESSION['username'] = $username;
        return true;
    }
	
	function buslistjson()
	{
		$tablecontent = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		
	if($this->IsAdmin()){
		$qry = "SELECT * , a.objname as objectname1 FROM trackingobjects as a WHERE HeadUser = '".$this->headuser."' ORDER BY objname";
	}else{
		$qry = "SELECT * , b.objname as objectname1 FROM objectassign as a 
		LEFT JOIN trackingobjects as b 
		ON b.objsysno = a.objectname 
		WHERE username = '".$this->UserName()."' 
		AND HeadUser = '".$this->headuser."'
		ORDER BY objname";
	}

		$stmt = mysqli_query($this->connection,$qry);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$array1[]=array('vehiclename'=>$row['objectname1']);
		}
		$Vehicleinfolist=array('vehiclelist'=>$array1);
		
		echo json_encode($Vehicleinfolist);
	}

	
	function publicONOFF($ONOFF,$systemno)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "UPDATE `trackingobjects` SET `hubee`='$ONOFF' WHERE `objsysno` = '$systemno'";
		
		$stmt = mysqli_query($this->connection,$qry);
		
		return $this->CheckPublicONOFF($systemno);
	}
	
	function CheckPublicONOFF($systemno)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT `objsysno`, `hubee` FROM `trackingobjects` WHERE `objsysno` = '$systemno'";
		
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			return $row['hubee'];
		}
	}
///////////////////////////////////////////////// APP Functions End		//////////////////////////////////////////////////

//////////////////////////////////////////////// Feed back Start        /////////////////////////////////////
	function feedbackInseart($post_data,$filedata)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		if(getimagesize($filedata['pic']['tmp_name'])==false)
		{
		$ImageName = "";
		$Image = null;
		
		$html_body = "<h4>Rate ".$post_data['rate']."<h4><br>".$post_data['msgbody']."<br>Reply to: ".$post_data['email']."<br>User: ".$this->UserFullName()."<br>";
		$this->Sendemail("info@gsupertrack.com","feedback: ".$post_data['subject'],$html_body);
		}else{
			$ImageName = addslashes($filedata['pic']['name']);
			$Image = addslashes($filedata['pic']['tmp_name']);		
			$Image = file_get_contents($Image);
			$Image = base64_encode($Image);
			
		$html_body = "<h4>Rate ".$post_data['rate']."<h4><br>".$post_data['msgbody']."<br>Reply to: ".$post_data['email']."<br>User: ".$this->UserFullName()."<br>";
		$this->SendemailwithAttachment("info@gsupertrack.com","feedback: ".$post_data['subject'],$html_body,addslashes($filedata['pic']['tmp_name']),$ImageName);
		}
		
		$qry = "INSERT INTO `feedback`(`useremail`, `subject`, `rate`, `message`, `image_name`, `image`) 
		VALUES ('".$post_data['email']."','".$post_data['subject']."','".$post_data['rate']."','".$post_data['msgbody']."','".$ImageName."','".$Image."')";
		

		$stmt = mysqli_query($qry,$this->connection);
		if($stmt)
		{
			return "Thank you for your feed back!";
		}else{
			return "Feed back not sent. Please check your inputs!";
		}
	}



//////////////////////////////////////////////// Feed back end        /////////////////////////////////////

///////////////////////////////////////////		Start Geo Functions		/////////////////////////////////////
	function geolist()
	{
		if(!$this->CheckLogin())
		{
			return "Authantication Error!";
			exit;
		}
		
		$tablecontent = "";
		$tablecontent=$tablecontent.'';
		$tablecontent=$tablecontent.'<table id="example1" class=" toggle-arrow-tiny" data-page-size="100" data-filter=#filter>';
		$tablecontent=$tablecontent."<thead><tr><th>Name</th><th>Show/Delete</th></thead><tbody>";
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		//$this->UserName()
		
		$qry = "SELECT * FROM `geofences` WHERE geouser = '".$this->UserName()."' AND `geotype` = '1'";
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$tablecontent = $tablecontent."<tr><td><div id='id".$row['id']."' hidden>".trim($row['id'])."</div><div id='coord".$row['id']."' hidden>".trim($row['cordinates'])."</div><div id='name".$row['id']."'>".trim($row['geo_name'])."</div></td>
			<td>
			<a class='btn btn-primary btn-sm' onclick='loadgeofence(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Show</a>
			<a class='btn btn-danger btn-sm' onclick='del_fence(".$row['id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
			</td>
			<td></td></tr>";
		}

		return $tablecontent."</tbody></table>";
	}
	
	function insertgeolist($postdata)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return "Database login failed!";
        } 

		$qry = "INSERT INTO geofences
           (geo_name
           ,geouser
           ,cordinates
		   ,geotype)
			VALUES
           ('".$postdata['gname']."'
           ,'".$postdata['user']."'
           ,'".trim($postdata['coordinates'])."'
		   ,'1')";

		$stmt = mysqli_query($this->connection,$qry);
		if(!$stmt){
		return $gname." Geofence Not insert!";
		}
		else{
		return $gname." Geofence Succesfully insert!";
		}
	}
	
	function deletegeolist($postdata)
	{
		if(!$this->DBLogin())
		{
			$this->HandleError("Database login failed!");
			return false;
		} 


		$qry = "DELETE FROM geofences
			  WHERE id='".$postdata['del_id']."'";
		//echo $sqldel;
		$stmt = mysqli_query($this->connection,$qry);
		if(!$stmt){
		echo $del_name." Not deleted!";
		}
		else{
		echo $del_name." Succesfully deleted!";
		}
	}

	
	function getpointvalues($postdata)
	{
		if(!$this->CheckLogin())
		{
			return "Authantication Error!";
			exit;
		}

		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		//$this->UserName()
		
		$qry = "SELECT `id`, `geo_name`, `remarks`, `geotype`, `cordinates`, `pointlat`, `pointlong`, `geouser` FROM `geofences` WHERE id='$postdata' AND `geotype` = '2'";
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			return json_encode(array('id'=>$row['id'],'geo_name'=>$row['geo_name'],'pointlat'=>$row['pointlat'],'pointlong'=>$row['pointlong']));
		}
		return null;
	}
	
	function getpoligonvalues($postdata)
	{
		if(!$this->CheckLogin())
		{
			return "Authantication Error!";
			exit;
		}

		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		//$this->UserName()
		
		$qry = "SELECT `id`, `geo_name`, `remarks`, `geotype`, `cordinates`, `pointlat`, `pointlong`, `geouser` FROM `geofences` WHERE id='$postdata' AND `geotype` = '1'";
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			return json_encode(array('id'=>$row['id'],'geo_name'=>$row['geo_name'],'cordinates'=>$row['cordinates']));
		}
		return null;
	}
	
	function insertgeolistpoint($postdata)
	{
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return "Database login failed!";
        } 

		if($postdata['user']=='')
		{
			return "Please login correctly!";
		}
		
		$qry = "INSERT INTO `geofences`( `geo_name`, `geotype`, `pointlat`, `pointlong`, `geouser`) VALUES ('".$postdata['pointname']."','2','".$postdata['pointlat']."','".$postdata['pointlong']."','".$postdata['user']."')";


		$stmt = mysqli_query($this->connection,$qry);
		if(!$stmt){
		return $gname." Geo point Not insert!";
		}
		else{
		return $gname." Geo point Succesfully insert!";
		}
	}
	
	function geolistRoutes()
	{
		if(!$this->CheckLogin())
		{
			return "Authantication Error!";
			exit;
		}
		
		$tablecontent = "";
		$tablecontent=$tablecontent.'';
		$tablecontent=$tablecontent.'<table id="tableRoute" class=" toggle-arrow-tiny" data-page-size="100" data-filter=#filterpoint>';
		$tablecontent=$tablecontent."<thead><tr><th>Name</th><th>Show/Delete</th></thead><tbody>";
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		//$this->UserName()
		
		$qry = "SELECT `id`, `routeno`, `routedescription`, `Origine`, `geolinecodinates`, `Destination`, `WayPoints`, `RouteAdmin` FROM `geoadminlines` WHERE RouteAdmin = '".$this->UserName()."' ORDER BY `routeno`";
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$tablecontent = $tablecontent."<tr><td><div id='id".$row['id']."' hidden>".trim($row['id'])."</div><div id='name".$row['id']."'>".trim($row['routeno'])."</div></td>
			<td>
			<a class='btn btn-primary btn-sm' onclick='getandshowRoute(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Show</a>
			<a class='btn btn-danger btn-sm' onclick='del_Route(".$row['id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
			</td>
			<td></td></tr>";
		}

		return $tablecontent."</tbody></table>";
	}
	
	function geoPointSelectOptions()
	{
		$tablecontent = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		//$this->UserName()
		
		$qry = "SELECT * FROM `geofences` WHERE geouser = '".$this->UserName()."' AND `geotype` = '2' ORDER BY `geo_name`";
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$tablecontent = $tablecontent."<option value='".$row['id']."'>".$row['geo_name']."</option>";
		}

		return $tablecontent;
	}	
	function geolistpoint()
	{
		if(!$this->CheckLogin())
		{
			return "Authantication Error!";
			exit;
		}
		
		$tablecontent = "";
		$tablecontent=$tablecontent.'';
		$tablecontent=$tablecontent.'<table id="example2" class=" toggle-arrow-tiny" data-page-size="100" data-filter=#filterpoint>';
		$tablecontent=$tablecontent."<thead><tr><th>Name</th><th>Show/Delete</th></thead><tbody>";
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		//$this->UserName()
		
		$qry = "SELECT * FROM `geofences` WHERE geouser = '".$this->UserName()."' AND `geotype` = '2'";
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$tablecontent = $tablecontent."<tr><td><div id='id".$row['id']."' hidden>".trim($row['id'])."</div><div id='coord".$row['id']."' hidden>".trim($row['cordinates'])."</div><div id='name".$row['id']."'>".trim($row['geo_name'])."</div></td>
			<td>
			<a class='btn btn-primary btn-sm' onclick='getandshowpoint(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Show</a>
			<a class='btn btn-danger btn-sm' onclick='del_fence(".$row['id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
			</td>
			<td></td></tr>";
		}

		return $tablecontent."</tbody></table>";
	}
	
	function geolisttrackingview()
	{
		if(!$this->CheckLogin())
		{
			return "Authantication Error!";
			exit;
		}
		
		$tablecontent = "";
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		//$this->UserName()
		
		$qry = "SELECT * FROM `geofences` WHERE geouser = '".$this->UserName()."' ORDER BY geotype,geo_name";
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			if ($row["geotype"] != $current_cat) {
				$current_cat = $row["geotype"];
				if($row["geotype"] == '1')
				{
					$catagory = "Geofences";
				}
				if($row["geotype"] == '2')
				{
					$catagory = "Geopoints";
				}
				$tablecontent = $tablecontent."<li style='background-color: lightyellow;'>
								<a style='color: blue;'><b>
									 ".$catagory."
								</b></a>
							</li>";
				}
			$tablecontent = $tablecontent."<li>
                            <a>
                                <input type='checkbox' name='vehicle' value='".$row['geotype']."' id='checkgeo".$row['id']."' onchange='togglegeofence(".$row['geotype'].",".$row['id'].")'> ".$row['geo_name']."
                            </a>
                        </li>";
		}

		return $tablecontent;
		

		
	}
///////////////////////////////////////////////// Geo Functions End		//////////////////////////////////////////////////
///////////////////////////////////////////		Start Dashboard Functions		/////////////////////////////////////
	function AddToLogVcountAdmin()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "INSERT INTO `activity_param_log`( `event`, `count`) VALUES ('vcountAdmin','".$this->vcountAdmin()."')";

		if(!mysqli_query($this->connection,$qry))
		{
			return false;
		}

		return true;
		
	}
	function AddToLogVcountAdminDeactive()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "INSERT INTO `activity_param_log`( `event`, `count`) VALUES ('vcountAdminDeactive','".$this->vcountAdminDeactive()."')";

		if(!mysqli_query($this->connection,$qry))
		{
			return false;
		}

		return true;
		
	}
	function onlinestatus()
	{
		$countonline = 0;
		$countoffline = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		

		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestringNormalUser()."')
		AND lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry1);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$countonline++;
		}

		$qry2 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestringNormalUser()."')
		AND lasttime < (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		ORDER BY objname";


		$stmt = mysqli_query($this->connection,$qry2);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$countoffline++;
		}

		if(($countoffline+$countonline)==0){
			return 0;
		}
		$onlineprecentage = round(($countonline/($countoffline+$countonline))*100);
		return $onlineprecentage;
	}
	function onlinestatusAdmin()
	{
		$countonline = 0;
		$countoffline = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		

		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry1);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$countonline++;
		}

		$qry2 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE lasttime < (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		ORDER BY objname";


		$stmt = mysqli_query($this->connection,$qry2);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$countoffline++;
		}

		if(($countoffline+$countonline)==0){
			return 0;
		}
		$onlineprecentage = round(($countonline/($countoffline+$countonline))*100);
		return $onlineprecentage;
	}
	function todayalerts()
	{
		$count = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT C.username AS `assignuser`, B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, `notifydate` FROM `gpsmntcenotify` AS A LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno INNER JOIN objectassign AS C ON A.imei = C.objectname 
				WHERE C.username = '".$this->UserName()."' 
				AND (`user` = '".$this->UserName()."' OR `user` = '') 
				AND DATE_ADD(`notifydate` , INTERVAL 1 DAY) > NOW() 
				ORDER BY `notifydate`";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$count++;
		}
		return $count;
		//return $qry;
	}
	function vcount()
	{
		$count = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestringNormalUser()."')
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$count++;
		}
		return $count;
		//return $qry;
	}
	
	function vcountAdmin()
	{
		$count = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$count++;
		}
		return $count;
		//return $qry;
	}	
	
	function vcountAdminDeactive()
	{
		$count = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
				WHERE activate = '0'
				ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$count++;
		}
		return $count;
		//return $qry;
	}	
	
	function comvcount()
	{
		$countonline = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestringNormalUser()."')
		AND lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$countonline++;
		}
		return $countonline;
	}
	
	function comvcountAdmin()
	{
		$countonline = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$countonline++;
		}
		return $countonline;
	}	
	function nocomvcount()
	{
		$countoffline = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry2 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestringNormalUser()."')
		AND lasttime <= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		ORDER BY objname";


		$stmt = mysqli_query($this->connection,$qry2);
		//echo $qry2;
		while($row = mysqli_fetch_array($stmt))
		{
			$countoffline++;
		}
		return $countoffline;
	}

	function nocomvcountAdmin()
	{
		$countoffline = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry2 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE lasttime <= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		ORDER BY objname";


		$stmt = mysqli_query($this->connection,$qry2);
		//echo $qry2;
		while($row = mysqli_fetch_array($stmt))
		{
			$countoffline++;
		}
		return $countoffline;
	}
	
	function stpvcount()
	{
		$stopcount = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestringNormalUser()."')
		AND lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		AND lastvelosity = '0'
		AND lastengine = '0'
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$stopcount++;
		}
		return $stopcount;
	}

	function stpvcountAdmin()
	{
		$stopcount = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		AND lastvelosity = '0'
		AND lastengine = '0'
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$stopcount++;
		}
		return $stopcount;
	}
	
	function runnivcount()
	{
		$stopcount = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestringNormalUser()."')
		AND lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		AND lastvelosity > '0'
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$stopcount++;
		}
		return $stopcount;
	}

	function runnivcountAdmin()
	{
		$stopcount = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		AND lastvelosity > '0'
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$stopcount++;
		}
		return $stopcount;
	}
	
	function idlevcount()
	{
		$idlecount = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestringNormalUser()."')
		AND lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		AND lastvelosity = '0'
		AND lastengine = '1'
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$idlecount++;
		}
		return $idlecount;
	}
	
	function idlevcountAdmin()
	{
		$idlecount = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		AND lastvelosity = '0'
		AND lastengine = '1'
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$idlecount++;
		}
		return $idlecount;
	}
	
	function alltodaymilage()
	{
		$count = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		

		$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestringNormalUser()."')
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			if(	$row['IsFmilageupdate']	){
			$todaymilage = round(($row['lastmilage']-$row['Fmilage'])/1000,2);
			}else{
				$todaymilage = 0;
			}
			if($todaymilage<0)
			{
				$todaymilage = round(($this->ConfusedMilages(date("Y-m-d"),$row['objsysno']))/1000,2);
			}
			$count = $todaymilage + $count;
		}
		return (int)($count)." Km";
	}
	
	function alltodaymilageAdmin()
	{
		$count = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		

		$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			if(	$row['IsFmilageupdate']	){
			$todaymilage = round(($row['lastmilage']-$row['Fmilage'])/1000,2);
			}else{
				$todaymilage = 0;
			}
			if($todaymilage<0)
			{
				$todaymilage = round(($this->ConfusedMilages(date("Y-m-d"),$row['objsysno']))/1000,2);
			}
			$count = $todaymilage + $count;
		}
		return (int)($count)." Km";
	}
	
	function ConfusedMilages($date,$sysno)
	{
		/*
		$Startmilage = 0;
		$Maxmilage = 0;
		$Lasttmilage = 0;
		$timezone = $this->gettimezone($sysno);
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		//$qry = "SELECT `Time`, `Miles` FROM `tko$sysno` WHERE `Time` > '$date 00:00:00' AND `Time` < '$date 23:59:59' ORDER BY `Time` LIMIT 1";
		$qry = "SELECT DATE_ADD(`Time`, INTERVAL $timezone MINUTE) AS Time, `Miles` FROM `tko$sysno` WHERE DATE_ADD(`Time`, INTERVAL $timezone MINUTE) > '$date 00:00:00' AND DATE_ADD(`Time`, INTERVAL $timezone MINUTE) < '$date 23:59:59' ORDER BY `Time` LIMIT 1";
		$stmt = mysql_query($qry,$this->connection);
		while($row = mysql_fetch_array($stmt))
		{
			$Startmilage = $row['Miles'];
		}
		//$qry = "SELECT `Time`, `Miles` FROM `tko$sysno` WHERE `Time` > '$date 00:00:00' AND `Time` < '$date 23:59:59' ORDER BY `Miles` DESC LIMIT 1";
		$qry = "SELECT DATE_ADD(`Time`, INTERVAL $timezone MINUTE) AS Time, `Miles` FROM `tko$sysno` WHERE DATE_ADD(`Time`, INTERVAL $timezone MINUTE) > '$date 00:00:00' AND DATE_ADD(`Time`, INTERVAL $timezone MINUTE) < '$date 23:59:59' ORDER BY `Miles` DESC LIMIT 1";
		$stmt = mysql_query($qry,$this->connection);
		while($row = mysql_fetch_array($stmt))
		{
			$Maxmilage = $row['Miles'];
		}
		//$qry = "SELECT `Time`, `Miles` FROM `tko$sysno` WHERE `Time` > '$date 00:00:00' AND `Time` < '$date 23:59:59' ORDER BY `Time` DESC LIMIT 1";
		$qry = "SELECT DATE_ADD(`Time`, INTERVAL $timezone MINUTE) AS Time, `Miles` FROM `tko$sysno` WHERE DATE_ADD(`Time`, INTERVAL $timezone MINUTE) > '$date 00:00:00' AND DATE_ADD(`Time`, INTERVAL $timezone MINUTE) < '$date 23:59:59' ORDER BY `Time` DESC LIMIT 1";
		$stmt = mysql_query($qry,$this->connection);
		while($row = mysql_fetch_array($stmt))
		{
			$Lasttmilage = $row['Miles'];
		}

		return ($Maxmilage - $Startmilage + $Lasttmilage);
		*/
		return 0;
	}

	
	
	function LastNotification($username,$LastNotificNo)
	{
		$NotifystrSub = "";
		$NotifystrMain = "";
		$active = "";
		$count = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry = "SELECT C.username AS `assignuser`, B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, DATE_ADD(`notifydate`, INTERVAL B.`tz` MINUTE) AS notifydatetz FROM `gpsmntcenotify` AS A LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno INNER JOIN objectassign AS C ON A.imei = C.objectname WHERE C.username = '".$username."' AND (`user` = '".$username."' OR `user` = '') AND DATE_ADD(`notifydate` , INTERVAL 60 DAY) > NOW() AND A.id > $LastNotificNo ORDER BY `notifydate` DESC LIMIT 1 ";
		//$qry = "SELECT B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, `notifydate` FROM `gpsmntcenotify` AS A LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno WHERE `user` = '".$this->UserName()."' OR `user` = '' ORDER BY `notifydate` DESC LIMIT 100";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			
			$date = date('Y-m-d', strtotime($row['notifydatetz']));

			return json_encode(array('id'=>$row['id'],'datetime'=>$date,'vehicle'=>$row['vehicleNo'],'subject'=>$row['subject'],'msgbody'=>$row['msgbody'],'priority'=>$row['priority'],'isshow'=>$row['isshow']));

		}
		return json_encode(null);
	}
	
	function LastTodayNotification($username)
	{
		$NotifystrSub = "";
		$NotifystrMain = "";
		$active = "";
		$count = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		//$qry = "SELECT C.username AS `assignuser`, B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, DATE_ADD(`notifydate`, INTERVAL B.`tz` MINUTE) AS notifydatetz FROM `gpsmntcenotify` AS A LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno INNER JOIN objectassign AS C ON A.imei = C.objectname WHERE C.username = '".$username."' AND (`user` = '".$username."' OR `user` = '') AND DATE_ADD(`notifydate` , INTERVAL 60 DAY) > NOW() AND DATE(NOW()) = DATE(`notifydate`) ORDER BY `notifydate` LIMIT 1";
		$qry = "SELECT C.username AS `assignuser`, B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, DATE_ADD(`notifydate`, INTERVAL B.`tz` MINUTE) AS notifydatetz FROM `gpsmntcenotify` AS A LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno INNER JOIN objectassign AS C ON A.imei = C.objectname WHERE C.username = '".$username."' AND (`user` = '".$username."' OR `user` = '') AND DATE_ADD(`notifydate` , INTERVAL 60 DAY) > NOW() ORDER BY `notifydate` LIMIT 1";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			
			$date = date('Y-m-d', strtotime($row['notifydatetz']));

			return json_encode(array('id'=>$row['id'],'datetime'=>$date,'vehicle'=>$row['vehicleNo'],'subject'=>$row['subject'],'msgbody'=>$row['msgbody'],'priority'=>$row['priority'],'isshow'=>$row['isshow']));

		}
		return json_encode(null);
	}
	



	function ThisMontInstallation()
	{
		$idlecount = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry1 = "SELECT `id`, `reason`, `user`, `systemno`, `logtime`, `parameters` FROM `activity_log` 
					WHERE YEAR(`logtime`) = YEAR(CURDATE()) 
					AND MONTH(`logtime`) = MONTH(CURDATE())
					AND `reason` = 'InsertObjecttoDB'";

		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$idlecount++;
		}
		return $idlecount;
	}	
	function ThisMonthRenewal()
	{
		$idlecount = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry1 = "SELECT `id`, `reason`, `user`, `systemno`, `logtime`, `parameters` FROM `activity_log` 
					WHERE YEAR(`logtime`) = YEAR(CURDATE()) 
					AND MONTH(`logtime`) = MONTH(CURDATE())
					AND `reason` = 'ExtendforOneyear'";

		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$idlecount++;
		}
		return $idlecount;
	}
///////////////////////////////////////////		End Dashboard Functions		/////////////////////////////////////


//////////////////////////////////////////		Start Devise Payments and Time Extention	/////////////////////
	function FreeExtend($sysno)
	{
		$freeextends = 2;

		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "SELECT `objsysno`,`freeextend` FROM `trackingobjects` WHERE `objsysno` = '$sysno'";
		
		$stmt = mysqli_query($this->connection,$qry);

		while($row = mysqli_fetch_array($stmt))
		{
			$freeextends = $row['freeextend'];
		}
		
		if($freeextends > 1)
		{
			return "Unable to extend! You have extend more than 2 times";
		}
		
		//$qryupdate = "UPDATE `trackingobjects` SET `expdate`= CURDATE()+1,`freeextend`= `freeextend`+1,`activate` = 1 WHERE `objsysno` = '$sysno'";
		$qryupdate = "UPDATE `trackingobjects` SET `freeextend`= `freeextend`+1,`activate` = 1 WHERE `objsysno` = '$sysno'";
		$stmtupdate = mysqli_query($this->connection,$qryupdate);
		if($stmtupdate)
		{
			$this->ObjectExtendlog($sysno,"FreeExtend");
			return "Succesfully Extended for one day!";
		}
		return "Unable to Extend!";
	}

	function ExtendforOneyear($sysno)
	{
		$expdate = "";

		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return "Database login failed!";
        } 
		$qry = "SELECT `objsysno`,`expdate` FROM `trackingobjects` WHERE `objsysno` = '$sysno'";
		
		$stmt = mysqli_query($this->connection,$qry);

		while($row = mysqli_fetch_array($stmt))
		{
			$expdate = $row['expdate'];
		}
		
		/*
		if(strtotime($expdate)<strtotime("now"))
		{
			$qryupdate = "UPDATE `trackingobjects` SET `expdate`= CURDATE() + INTERVAL 1 YEAR,`freeextend`= 0,`activate` = 1 WHERE `objsysno` = '$sysno'";
		}else{
			*/
			
			$qryupdate = "UPDATE `trackingobjects` SET `expdate` = `expdate` + INTERVAL 1 YEAR,`freeextend`= 0,`activate` = 1 WHERE `objsysno` = '$sysno'";
		//}
		
		$stmtupdate = mysqli_query($this->connection,$qryupdate);
		if($stmtupdate)
		{
			$this->ActivityLog("ExtendforOneyear", $this->UserName(), $sysno,"Old expDate->".$expdate);
			$this->ObjectExtendlog($sysno,"YearExtend");
			$this->Sendemail('senevind@gmail.com',$this->sitename.' Year Extended '.$sysno,'Year Extended '.$sysno);
			$this->Sendemail($this->admin_email,$this->sitename.' Year Extended '.$sysno,'Year Extended '.$sysno);
			return "Succesfully Extended for one year! ";
		}
		return "Unable to Extend! Please Contact System Administrator.";
	}

	function ObjectExtendlog($objsysno,$type)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "INSERT INTO trackingobjectslog
						   (objsysno
						   ,action
						   ,logger)
					 VALUES
						   ('".$this->SanitizeForSQL($objsysno)."'
						   ,'".$type."'
						   ,'".$this->SanitizeForSQL($this->UserName())."')";
		
		$stmt = mysqli_query($this->connection,$qry);
		//return $qry;
	}	
	
	//////////////////////////////////////////		End Devise Payments and Time Extention	/////////////////////

	function ObjectMonitorTabele($Busno)
	{
		$sysno = $this->GetObjectsysno($Busno);
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$tablestring = '';
		$qry = "SELECT *
  FROM tko".$sysno." 
  WHERE Time <= (convert_tz(now(),@@session.time_zone,'+05:30') + INTERVAL +200 MINUTE)
  ORDER BY Time DESC
  LIMIT 20";
		$stmt = mysqli_query($this->connection,$qry);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$tablestring  = $tablestring."<tr><td>".$row['Time']."</td>"."<td>".$row['Longitude']."</td>"."<td>".$row['Latitude']."</td>"."<td>".$row['Velocity']."</td>"."<td>".$row['Angle']."</td>"."<td>".$row['DtStatus']."</td>"."<td>".$row['Oil']."</td>"."<td>".$row['Miles']."</td>"."<td>".$row['Temperature']."</td>"."<td>".$row['Alarm']."</td>"."<td>".$row['objectsystem']."</td>"."<td>".$row['send']."</td>"."<td>".$row['result']."</td></tr>";
		}
		return $tablestring;
	}
	
	function ObjectAssignList()
	{
		$list = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT id
				  ,username
				  ,objectname
				  ,headername
				FROM objectassign
				WHERE headername = '".$this->headuser."' 
				ORDER BY username";

		//echo $qry;
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$list = $list."<tr><td><form id='".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='del' value='".$row['id']."'/></form>".$row['username']."</td><td>".$this->GetObjectname($row['objectname'])."-(".$row['objectname'].") "."</td><td><a class='btn btn-danger btn-sm' onclick='delconfirm(".$row['id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a></td></tr>";
		}
		return $list;
	}
	
	function UserAssignObject($PostValues)
	{
		if($this->AssignUserObject($PostValues['Username'],$PostValues['Objectname']))
        {
            return "Object Assigned to ".$PostValues['Username'];
        }else{
			return "Object Assigning Error";
		}
	}
	
	function AssignUserObject($Username,$Objectname)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "INSERT INTO objectassign
           (username
           ,objectname
           ,headername)
			VALUES
           ('".$Username."'
           ,'".$Objectname."'
           ,'".$this->headuser."')";
		   
		   $stmt = mysqli_query($this->connection,$qry);
		   if($stmt)
		   {
			   $this->ActivityLog("AssignUserObject", $this->UserName(), $Objectname,$Username);
			   return true;
		   }
		   else{
			   return false;
		   }
	}
	
	function GetObjectsysno($Busno)
	{
		$objectsysno = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "SELECT id,objname,objsysno FROM trackingobjects WHERE objname = '$Busno'";
		
		$stmt = mysqli_query($this->connection,$qry);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$objectsysno = $row['objsysno'];
		}
		return $objectsysno;
	}
	
	function GetObjectname($systemno)
	{
		$objectname = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "SELECT id,objname,objsysno FROM trackingobjects WHERE objsysno = '$systemno'";
		
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$objectname = $row['objname'];
		}
		return $objectname;
	}
	
	function ObjectAssignUserList()
	{
		$list = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT `name`,`username` FROM `gpsusers` ORDER BY `username`";

		//echo $qry;
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$list = $list."<tr><td>".$row['username']."</td><td>".$row['name']."</td><td><a class='btn btn-primary' onclick=loaduserassignlist('".$row['username']."') >Show</a></td></tr>";
		}
		return $list;
	}
	
	function vehiclelistforassign($user)
	{
		$list = "";
		$userassignbusnoarray = $this->isassignarray($user);
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT `id`,`objname`,`objsysno` FROM `trackingobjects` ORDER BY `objname`";

		//echo $qry;
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$checked = "";
			//if($this->isassign($user,$row['objsysno']))
			if(isset($userassignbusnoarray[$row['objsysno']]) && $userassignbusnoarray[$row['objsysno']])
			{
				$checked = "checked";
			}
			$list = $list."<tr><td>".'<input type="checkbox" value="'.$row['objsysno'].'" name="checkbox[]"'." $checked/></td><td>".$row['objname']."</td><td>".$row['objsysno']."</td></tr>";
		}
		return $list;
	}
	
	function isassign($user,$sysno)
	{
		$result = false;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT * FROM `objectassign` WHERE `username` = '".$user."' AND `objectname` = '".$sysno."'";

		//echo $qry;
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$result = true;
		}
		return $result;
	}
	
	function isassignarray($user)
	{
		$result = array();
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT * FROM `objectassign` WHERE `username` = '".$user."'";

		//echo $qry;
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$result[$row['objectname']] = true;
		}
		return $result;
	}
	
	function UserAssignObjectfromarray($PostValues)
	{
		if(isset($PostValues['user']))
		{
			$user = $PostValues['user'];
		}if(isset($PostValues['username']))
		{
			$user = $this->Sanitize($PostValues['username']);
		}
		
		$result = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$this->UserAssignDelete($PostValues['user']);
		
		for($i=0;$i<count($PostValues['checkbox']);$i++)
		{
		$qry = "INSERT INTO objectassign
           (username
           ,objectname
           ,headername)
			VALUES
           ('".$user."'
           ,'".$PostValues['checkbox'][$i]."'
           ,'".$this->headuser."')";
		   
		   $stmt = mysqli_query($this->connection,$qry);
		   if($stmt)
		   {
			   $this->ActivityLog("objectassign", $this->UserName(), $PostValues['checkbox'][$i],$user);
			   $result = "Object Assigned Succesfull";
		   }
		   else{
			   $result = "Object Assigning Error ";
		   }
		}
		  return $result;
	}
	
	function UserAssignDelete($username)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "DELETE FROM objectassign
					WHERE username = '$username'";
	  
		$stmt = mysqli_query($this->connection,$qry);
	if($stmt){
		$this->ActivityLog("UserAssignDelete", $this->UserName(), "",$username);
		return "Object Assign Delete Succesfulley";
	}else
	{
		return "Delete Error!";
	}
	}
	
	function UserListOptions()
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$tablecontent = "";
		$qry = "SELECT `id_user`, `username`,`HeadUser` FROM `gpsusers` WHERE HeadUser='".$this->headuser."'";
		       
        //echo $qry;
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            echo "<option value=''>No User Records</option>";
        }
        
        while($row = mysqli_fetch_array($result))
		{
			echo "<option value='".$row['username']."'>'".$row['username']."'</option>";
		}
		//return $tablecontent;
	}
	
	function ObjectListOptions()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$tablecontent = "";
		
		$qry = "SELECT * , b.objname as objectname1 FROM objectassign as a 
				LEFT JOIN trackingobjects as b 
				ON b.objsysno = a.objectname 
				WHERE objsysno IN ('".$this->assignedvehiclestring()."')
				AND a.username = '".$this->UserName()."'
				ORDER BY objname";
		
		//$qry = "SELECT id,objname,objsysno,HeadUser  FROM trackingobjects WHERE HeadUser = '".$this->headuser."'  ORDER BY objname";
		//echo $qry;
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			echo "<option value='".$row['objsysno']."'>".$row['objectname1']."-".$row['objsysno']."</option>";
		}
	}
	
	function VehicleListOptions()
	{
		$tablecontent = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestring()."')
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			echo "<option value='".$row['objectname1']."'>".$row['objectname1']."</option>";
		}
	}
	
	function getbuslisttable()
	{ 	$list = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestring()."')
		ORDER BY objname";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			if($row['lastlng']!=""){
				$openpamsys = $row['objsysno'];
				$openpamsys = "onclick='openmap($openpamsys)'";
			}else{
				$openpamsys = "";
			}

			$list = $list."<tr>
			<td>"."<span id='status".$row['objsysno']."' class='label pull-left'>Offline</span>"."</td>
			<td><a ".$openpamsys.">".$row['objectname1']."</a></td>
			<td>".$row['origine']."-".$row['Destnation']."</td>
			<td><span id='milage".$row['objsysno']."' class='pull-left'>0</span></td>
			<td><span id='velosity".$row['objsysno']."' class='pull-left'>0</span></td>
			<td><span id='time".$row['objsysno']."' class='pull-left'>-</span><div style='display: none;'><input type='radio' id='radio".$row['objsysno']."' name='gender' ></div></td>
			</tr>";
		
		}
		return $list;
	}
	
	function getbuslisttablearraySTD()
	{ 	$list = "";
		$sub = "";
		$subtable = "";
		$selectOptions = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		/* Admin string
		$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, IFNULL(E.GroupName,'Uncategorized') user_group, trackingobjects.id AS `id`, `objname` AS objectname1, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager` 
		FROM trackingobjects 
		LEFT JOIN geofences as B ON trackingobjects.origine = B.id 
		LEFT JOIN geofences as C ON trackingobjects.Destnation = C.id 
        LEFT JOIN GroupsAdmin AS E ON trackingobjects.admin_group = E.Id
		WHERE objsysno IN ('".$this->assignedvehiclestring()."')
		ORDER BY user_group, objname";
		*/
		if($this->IsGroupUser()){
		$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, IFNULL(E.GroupName,'Uncategorized') user_group, trackingobjects.id AS `id`, `objname` AS objectname1, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager`, IF(`expdate`<NOW(),'Expired','Active') AS ExpStatus
		FROM trackingobjects 
		LEFT JOIN `geofences` as B ON trackingobjects.origine = B.id 
		LEFT JOIN geofences as C ON trackingobjects.Destnation = C.id 
		LEFT JOIN `objectassign` AS D ON trackingobjects.objsysno = D.objectname
        LEFT JOIN GroupsUsers AS E ON trackingobjects.subgroup = E.Id
		WHERE objsysno IN ('".$this->assignedvehiclestring()."')
		ORDER BY user_group, objname";
		}else{
		$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, IFNULL(E.GroupName,'Uncategorized') user_group, trackingobjects.id AS `id`, `objname` AS objectname1, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager`, IF(`expdate`<NOW(),'Expired','Active') AS ExpStatus
		FROM trackingobjects 
		LEFT JOIN geofences as B ON trackingobjects.origine = B.id 
		LEFT JOIN geofences as C ON trackingobjects.Destnation = C.id 
		LEFT JOIN `objectassign` AS D ON trackingobjects.objsysno = D.objectname
        LEFT JOIN GroupsUsers AS E ON D.usergroup = E.Id
		WHERE objsysno IN ('".$this->assignedvehiclestring()."')
		AND D.username = '".$this->UserName()."'
		ORDER BY user_group, objname";
		}

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			if($row['lastlng']!=""){
				$openpamsys = $row['objsysno'];
				$openpamsys = "onclick='openmap(`$openpamsys`)'";
			}else{
				$openpamsys = "";
			}
			
			if($row['starred'] == "1"){
				$checked = 'checked';
			}else{
				$checked = '';
			}
			{
			$list = $list."<tr>
			<td  style='padding-top: 18px;'>"."<span id='status".$row['objsysno']."' class='label pull-left'>Offline</span>"."</td>
			<td>".$row['objectname1']."</td>
			<td><span id='park".$row['objsysno']."' class='pull-left'>0</span></td>
			<td><span id='velosity".$row['objsysno']."' class='pull-left'>0</span></td>
			<td><span id='time".$row['objsysno']."' class='pull-left'>-</span><div style='display: none;'><input type='radio' id='radio".$row['objsysno']."' name='gender' ></div></td>
			<td><span id='lati".$row['objsysno']."' class='pull-left'>".$row['lastlati']."</span></td>
			<td><span id='lng".$row['objsysno']."' class='pull-left'>".$row['lastlng']."</span></td>
			<td><span id='dir".$row['objsysno']."' class='pull-left'>".$row['lastdirection']."</span></td>
			<td>".$row['model']."</td>
			<td>".$row['simno']."</td>
			<td>".$row['objsysno']."</td>
			<td>".$row['user_group']."</td>
			</tr>";
			}
			
			{	
				$subheader="";
				if ($row["user_group"] != $current_cat) {
				$current_cat = $row["user_group"];
				$subheader = "<tr>
				<td  style='color: blue;background-color: lightyellow;'> <input type='checkbox' name='vehicle' value='".$row["user_group"]."' onchange='togglegroup(this.value)' id='group".$row["user_group"]."' checked></td>
				<td  colspan='4' style='color: blue;background-color: lightyellow;'><b>".$row['user_group']."</b></td>
				</tr>";
				}
			$sub = $sub.$subheader."
							<tr>
							<td> <input type='checkbox' name='vehicle' value='".$row['objsysno']."' id='check".$row['objsysno']."' onchange='toggleCheckbox(this.value)' checked></td>
							<td><a ".$openpamsys."><button type='button' id='substatus".$row['objsysno']."' style='width:75px;' class='btn btn-xs btn-default'>".$row['objectname1']."</button></a></td>
							<td><span id='subpark".$row['objsysno']."' class='pull-left'>0</span></td>
							<!--<td><span id='submilage".$row['objsysno']."' class='pull-left'>0</span></td>-->
							<td><span id='subvelosity".$row['objsysno']."' class='pull-left'>0</span></td>
							</tr>";
			$selectOptions = $selectOptions."<option value='".$row['objsysno']."'>".$row['objectname1']."</option>";
			}
			
			$array1=array('objsysno'=>$row['objsysno'],'image'=>$row['mapimage'],'objname'=>$row['objectname1'],'lastlati'=>$row['lastlati'],'lastlng'=>$row['lastlng'],'lastdirection'=>$row['lastdirection'],'lastvelosity'=>$row['lastvelosity'],'sim'=>$row['simno'],'group'=>$row['user_group'],'ExpStatus'=>$row['ExpStatus']);
			$Vehicleinfo[]=$array1;
			$Vehicleinfosys[$row['objsysno']] = $array1;
		}
		$Vehicleinfolist=array('Vehicleinfo'=>$Vehicleinfo,'Vehicleinfosys'=>$Vehicleinfosys);
		return array('main'=>$list,'sub'=>$sub,'dataset'=>json_encode($Vehicleinfolist),'selectOptions'=>$selectOptions);
	}
	
	function getbuslisttablearraySTD_Admin()
	{ 	$list = "";
		$sub = "";
		$subtable = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, IFNULL(E.GroupName,'Uncategorized') user_group, trackingobjects.id AS `id`, `objname` AS objectname1, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, DATE(`expdate`) AS expdate, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager` , IF(`expdate`<NOW(),'Expired','Active') AS ExpStatus
		FROM trackingobjects 
		LEFT JOIN geofences as B ON trackingobjects.origine = B.id 
		LEFT JOIN geofences as C ON trackingobjects.Destnation = C.id 
        LEFT JOIN GroupsAdmin AS E ON trackingobjects.admin_group = E.Id
		WHERE objsysno IN ('".$this->assignedvehiclestring()."')
		ORDER BY user_group, objname";


		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			if($row['lastlng']!=""){
				$openpamsys = $row['objsysno'];
				$openpamsys = "onclick='openmap(`$openpamsys`)'";
			}else{
				$openpamsys = "";
			}
			
			if($row['starred'] == "1"){
				$checked = 'checked';
			}else{
				$checked = '';
			}
			{
			$list = $list."<tr>
			<td  style='padding-top: 18px;'>"."<span id='status".$row['objsysno']."' class='label pull-left'>Offline</span>"."</td>
			<td>".$row['objectname1']."</td>
			<td><span id='park".$row['objsysno']."' class='pull-left'>0</span></td>
			<td><span id='velosity".$row['objsysno']."' class='pull-left'>0</span></td>
			<td><span id='time".$row['objsysno']."' class='pull-left'>-</span><div style='display: none;'><input type='radio' id='radio".$row['objsysno']."' name='gender' ></div></td>
			<td><span id='lati".$row['objsysno']."' class='pull-left'>".$row['lastlati']."</span></td>
			<td><span id='lng".$row['objsysno']."' class='pull-left'>".$row['lastlng']."</span></td>
			<td><span id='dir".$row['objsysno']."' class='pull-left'>".$row['lastdirection']."</span></td>
			<td>".$row['model']."</td>
			<td>".$row['simno']."</td>
			<td>".$row['objsysno']."</td>
			<td>".$row['expdate']."</td>
			<td>".$row['activate']."</td>
			<td>".$row['user_group']."</td>
			</tr>";
			}
			
			{	
				$subheader="";
				if ($row["user_group"] != $current_cat) {
				$current_cat = $row["user_group"];
				$subheader = "<tr>
				<td  style='color: blue;background-color: lightyellow;'> <input type='checkbox' name='vehicle' value='".$row["user_group"]."' onchange='togglegroup(this.value)' id='group".$row["user_group"]."' checked></td>
				<td  colspan='4' style='color: blue;background-color: lightyellow;'><b>".$row['user_group']."</b></td>
				</tr>";
				}
			$sub = $sub.$subheader."
							<tr>
							<td> <input type='checkbox' name='vehicle' value='".$row['objsysno']."' id='check".$row['objsysno']."' onchange='toggleCheckbox(this.value)' checked></td>
							<td><a ".$openpamsys."><button type='button' id='substatus".$row['objsysno']."' style='width:75px;' class='btn btn-xs btn-default'>".$row['objectname1']."</button></a></td>
							<td><span id='subpark".$row['objsysno']."' class='pull-left'>0</span></td>
							<!--<td><span id='submilage".$row['objsysno']."' class='pull-left'>0</span></td>-->
							<td><span id='subvelosity".$row['objsysno']."' class='pull-left'>0</span></td>
							</tr>";
			}
			
			$array1=array('objsysno'=>$row['objsysno'],'image'=>$row['mapimage'],'objname'=>$row['objectname1'],'lastlati'=>$row['lastlati'],'lastlng'=>$row['lastlng'],'lastdirection'=>$row['lastdirection'],'lastvelosity'=>$row['lastvelosity'],'sim'=>$row['simno'],'group'=>$row['user_group'],'ExpStatus'=>$row['ExpStatus']);
			$Vehicleinfo[]=$array1;
			$Vehicleinfosys[$row['objsysno']] = $array1;
		}
		$Vehicleinfolist=array('Vehicleinfo'=>$Vehicleinfo,'Vehicleinfosys'=>$Vehicleinfosys);
		return array('main'=>$list,'sub'=>$sub,'dataset'=>json_encode($Vehicleinfolist));
	}

	
	function getbuslisttablearray()
	{ 	$list = "";
		$sub = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
		WHERE objsysno IN ('".$this->assignedvehiclestring()."')
		ORDER BY objname";
//echo $qry;
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			if($row['lastlng']!=""){
				$openpamsys = $row['objsysno'];
				$openpamsys = "onclick='openmap($openpamsys)'";
			}else{
				$openpamsys = "";
			}

			$list = $list."<tr>
			<td>"."<span id='status".$row['objsysno']."' class='label pull-left'>Offline</span>"."</td>
			<td><a ".$openpamsys.">".$row['objectname1']."</a></td>
			<td>".$row['origine']."-".$row['Destnation']."</td>
			<td><span id='milage".$row['objsysno']."' class='pull-left'>0</span></td>
			<td><span id='velosity".$row['objsysno']."' class='pull-left'>0</span></td>
			<td><span id='time".$row['objsysno']."' class='pull-left'>-</span><div style='display: none;'><input type='radio' id='radio".$row['objsysno']."' name='gender' ></div></td>
			<td>".$row['objsysno']."</td>
			</tr>";
			
			$sub = $sub."<tr>
			<td> <input type='checkbox' name='vehicle' value='".$row['objsysno']."' id='check".$row['objsysno']."' onchange='toggleCheckbox(this.value)'></td>
			<td>"."<span id='substatus".$row['objsysno']."' class='label pull-left'>Offline</span>"."</td>
			<td><a ".$openpamsys.">".$row['objectname1']."</a></td>
			<td>".$row['origine']."-".$row['Destnation']."</td>
			<td><span id='subvelosity".$row['objsysno']."' class='pull-left'>0</span></td>
			</tr>";
		}
		return array('main'=>$list,'sub'=>$sub);
	}
	
	function Starredupdate($id,$yesno)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return 0;
        } 
		$qry = "UPDATE `trackingobjects` SET `starred`='$yesno' WHERE `objsysno` = '$id'";
		$stmt = mysqli_query($qry,$this->connection);
		if(!$stmt)
		{
			return 0;
		}
		return 1;
	}
	
	function Status($peed,$dtstatus,$time){
	    //date_default_timezone_set("Asia/Colombo");
	 	$countonline = 0;

        if(strtotime('10 minutes',strtotime($time))>=time() && strtotime('-10 minutes',strtotime($time))<=time())
        {
			$countonline=1;
			if($peed > 0)
			{
				$countonline=3;
			}else{
				if($dtstatus == 1){
					$countonline=2;
				}else{
					$countonline=1;
				}
			}
        }
		return $countonline;
	}
	
	function vehiclecount()
	{ 	$count = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
	if($this->IsAdmin()){
		$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a WHERE HeadUser = '".$this->headuser."' ORDER BY objname";
	}else{
		$qry = "SELECT * , b.objname as objectname1 FROM objectassign as a 
		LEFT JOIN trackingobjects as b 
		ON b.objsysno = a.objectname 
		WHERE username = '".$this->UserName()."' 
		AND HeadUser = '".$this->headuser."' 
		ORDER BY objname";
	}
	//echo $qry;
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$count++;
		}
		//sqlsrv_close($con);
		return $count;
	}
	
	function onlineprecentage()
	{ 	$countonline = 0;
		$countoffline = 0;
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
	if($this->IsAdmin()){
		$qry1 = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a WHERE HeadUser = '".$this->headuser."'
		AND lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		ORDER BY objname";
	}else{
		$qry1 = "SELECT * , b.objname as objectname1 FROM objectassign as a 
		LEFT JOIN trackingobjects as b 
		ON b.objsysno = a.objectname 
		WHERE username = '".$this->UserName()."' 
		AND HeadUser = '".$this->headuser."' 
		AND lasttime >= (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		ORDER BY objname";
	}
		$stmt = mysqli_query($this->connection,$qry1);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			$countonline++;
		}
	if($this->IsAdmin()){
		$qry2 = "SELECT * , a.objname as objectname1 FROM trackingobjects as a WHERE HeadUser = '".$this->headuser."'
		AND lasttime < (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		ORDER BY objname";
		}else{
		$qry2 = "SELECT * , b.objname as objectname1 FROM objectassign as a 
		LEFT JOIN trackingobjects as b 
		ON b.objsysno = a.objectname 
		WHERE username = '".$this->UserName()."' 
		AND HeadUser = '".$this->headuser."' 
		AND lasttime < (convert_tz(now(),@@session.time_zone,'+00:00') + INTERVAL -10 MINUTE)
		AND lasttime <= convert_tz(now(),@@session.time_zone,'+00:00')
		ORDER BY objname";
		}

		$stmt = mysqli_query($this->connection,$qry2);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$countoffline++;
		}

		if(($countoffline+$countonline)==0){
			return 0;
		}
		$onlineprecentage = round(($countonline/($countoffline+$countonline))*100);
		return $onlineprecentage;
	}
	

	function IsAdmin()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          

        $qry = "SELECT  `username`, `HeadUser`, `Privilage` FROM `gpsusers` WHERE username = '".$this->UserName()."'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Admin Checking Error!");
            return "";
        }
        $row = mysqli_fetch_assoc($result);
	if($row['Privilage']=='admin' || $row['Privilage']=='assistant'){
		return true;
	}else{
		return false;
	}
	}
	
	function isAdminfullrights()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          

        $qry = "SELECT  `username`, `HeadUser`, `Privilage` FROM `gpsusers` WHERE username = '".$this->UserName()."'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Admin Checking Error!");
            return "";
        }
        $row = mysqli_fetch_assoc($result);
	if($row['Privilage']=='admin'){
		return true;
	}else{
		return false;
	}
	}
	
	function IsGroupUser()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          

        $qry = "SELECT  `username`, `HeadUser`, `Privilage` FROM `gpsusers` WHERE HeadUser = '".$this->headuser."' AND username = '".$this->UserName()."'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Admin Checking Error!");
            return "";
        }
        $row = mysqli_fetch_assoc($result);
	if($row['Privilage']=='group'){
		return true;
	}else{
		return false;
	}
	}
	
	function GetGroupUser()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          

        $qry = "SELECT  `username`, `HeadUser`, `Privilage`, `admingroup` FROM `gpsusers` WHERE HeadUser = '".$this->headuser."' AND username = '".$this->UserName()."'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Admin Checking Error!");
            return "";
        }
        $row = mysqli_fetch_assoc($result);
		if($row['Privilage']=='group'){
			return $row['admingroup'];
		}else{
			return null;
		}
	}
	
	function getbuslistarray()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		if($this->IsAdmin()){
		$qry = "SELECT * , a.objname as objectname1, IF(`expdate`<NOW(),'Expired','Active') AS ExpStatus 
		FROM  trackingobjects as a WHERE HeadUser = '".$this->headuser."' ORDER BY objname";
		}else{
			
		$qry = "SELECT * , b.objname as objectname1, IF(`expdate`<NOW(),'Expired','Active') AS ExpStatus
		FROM objectassign as a 
		LEFT JOIN trackingobjects as b 
		ON b.objsysno = a.objectname 
		WHERE username = '".$this->UserName()."' 
		AND HeadUser = '".$this->headuser."' 
		ORDER BY objname";
		}

		$stmt = mysqli_query($this->connection,$qry);
		//echo $qry;
		while($row = mysqli_fetch_array($stmt))
		{
			//'status'=>$this->Status($row['lastvelosity'],$row['lastengine'],$row['lasttime'])
			$array1=array('objsysno'=>$row['objsysno'],'image'=>$row['mapimage'],'objname'=>$row['objname'],'lastlati'=>$row['lastlati'],'lastlng'=>$row['lastlng'],'lastdirection'=>$row['lastdirection'],'lastvelosity'=>$row['lastvelosity'],'sim'=>$row['simno'],'ExpStatus'=>$row['ExpStatus']);
			$Vehicleinfo[]=$array1;
			$Vehicleinfosys[$row['objsysno']] = $array1;
		}
		$Vehicleinfolist=array('Vehicleinfo'=>$Vehicleinfo,'Vehicleinfosys'=>$Vehicleinfosys);
		echo json_encode($Vehicleinfolist);
	}


	function Xam_getbuslistarray()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		if($this->IsAdmin()){
		$qry = "SELECT * , a.objname as objectname1, DATE(expdate) AS expdate FROM  trackingobjects as a WHERE HeadUser = '".$this->headuser."' ORDER BY objname";
		}else{
			
		$qry = "SELECT * , b.objname as objectname1, REPLACE(`fuelstop`,'`security`',`password`) AS `oiloff`, REPLACE(`fuelstart`,'`security`',`password`) AS `oilon`, DATE(expdate) AS expdate FROM objectassign as a 
		LEFT JOIN trackingobjects as b 
		ON b.objsysno = a.objectname 
        LEFT JOIN commands as c
        ON c.model = b.model
		WHERE username = '".$this->UserName()."' 
		ORDER BY objname";
		}

		$stmt = mysqli_query($this->connection,$qry);
		//echo $qry;
		while($row = mysqli_fetch_array($stmt))
		{
			$expbtn = "0";
			if(strtotime($row['expdate'])<strtotime("+30 days"))
			{
				$expbtn = "1";
			}
			
			$array1=array('status'=>$this->Status($row['lastvelosity'],$row['lastengine'],$row['lasttime']),'expbtn'=>$expbtn,'cmd_oilon'=>$row['oilon'],'cmd_oiloff'=>$row['oiloff'],'objsysno'=>$row['objsysno'],'image'=>$row['mapimage'],'objname'=>$row['objname'],'lastlati'=>$row['lastlati'],'lastlng'=>$row['lastlng'],'lastdirection'=>$row['lastdirection'],'lastvelosity'=>$row['lastvelosity'],'lastengine'=>$row['lastengine'],'lasttime'=>$row['lasttime'],'sim'=>$row['simno'],'expdate'=>$row['expdate']);
			$Vehicleinfo[]=$array1;
		}
		$Vehicleinfolist=array('Vehicleinfo'=>$Vehicleinfo);
		echo json_encode($Vehicleinfolist);
	}	
	
	function geoAddress($sysstring)
	{
		$sysarray = explode(";",$sysstring);
		$Vehicleinfolist=array('geocordinatesAddress'=>$this->getgeocoodsAddress($sysarray));
		echo json_encode($Vehicleinfolist);
	}
	
	function getgeocoodsAddress($sysnoarray)
	{
		$ids = join("','",$sysnoarray);  
		$Vehicleinfo = array();
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$todaymilage = 0;
		
		$qry = "SELECT  `objsysno`,`lastlng`,`lastlati` 
				FROM trackingobjects
				WHERE objsysno IN ('$ids')
				ORDER BY objsysno";
		//echo  $qry;
		//$this->Todaymilage($row['objsysno'])
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$this->revgeocode($row['lastlati'],$row['lastlng']);
				$Vehicleinfo[]=array('objsysno'=>$row['objsysno'],'Address'=>$this->revgeocode($row['lastlati'],$row['lastlng']));
		}
		return $Vehicleinfo;
	}		
	
	function geocordinatesstr($sysstring)
	{
		$sysarray = explode(";",$sysstring);
		$Vehicleinfolist=array('geocordinates'=>$this->getgeocoods($sysarray),'onlineprecentage'=>0);
		echo json_encode($Vehicleinfolist);
	}
	
	function getgeocoods($sysnoarray)
	{
		//echo "1: ".round(microtime(true) * 1000)."<br>";
		$ids = join("','",$sysnoarray);  
		$Vehicleinfo = array();
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$todaymilage = 0;
		
		$tz = $this->UserTimeZone();
		$qry = "SELECT DATE_ADD(`lasttime`, INTERVAL ".$tz." MINUTE) as lasttime, CONCAT(FLOOR(TIMESTAMPDIFF(MINUTE,DATE_ADD(`lastruntime`, INTERVAL ".$tz." MINUTE),DATE_ADD(`lasttime`, INTERVAL ".$tz." MINUTE))/60),':',LPAD(MOD(TIMESTAMPDIFF(MINUTE,DATE_ADD(`lastruntime`, INTERVAL ".$tz." MINUTE),DATE_ADD(`lasttime`, INTERVAL ".$tz." MINUTE)),60),2,'0'))  AS parking,	DATE_ADD(`lastruntime`, INTERVAL ".$tz." MINUTE) as lastruntime, `lasttime` as UTCTime, `id`, `objname`, `objsysno`, `mapimage`, `objectdiscription`, `simno`, `origine`, `Destnation`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage` 
				FROM trackingobjects
				WHERE objsysno IN ('$ids')
				ORDER BY objname";
		/*
		$qry = "SELECT DATE_ADD(`lasttime`, INTERVAL `tz` MINUTE) as lasttime, CONCAT(FLOOR(TIMESTAMPDIFF(MINUTE,DATE_ADD(`lastruntime`, INTERVAL `tz` MINUTE),DATE_ADD(`lasttime`, INTERVAL `tz` MINUTE))/60),':',LPAD(MOD(TIMESTAMPDIFF(MINUTE,DATE_ADD(`lastruntime`, INTERVAL `tz` MINUTE),DATE_ADD(`lasttime`, INTERVAL `tz` MINUTE)),60),2,'0'))  AS parking,	DATE_ADD(`lastruntime`, INTERVAL `tz` MINUTE) as lastruntime, `lasttime` as UTCTime, `id`, `objname`, `objsysno`, `mapimage`, `objectdiscription`, `simno`, `origine`, `Destnation`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage` 
				FROM trackingobjects
				WHERE objsysno IN ('$ids')
				ORDER BY objname";
				*/
		//echo  $qry;
		//$this->Todaymilage($row['objsysno'])
		$stmt = mysqli_query($this->connection,$qry);
		//echo "3: ".round(microtime(true) * 1000)."<br>";
		while($row = mysqli_fetch_array($stmt))
		{
			
			if($row['lasttime'] != ""){
			    if(	$row['IsFmilageupdate']	){
			    $todaymilage = round(($row['lastmilage']-$row['Fmilage'])/1000,2);
			    }else{
			        $todaymilage = 0;
			    }
				if($todaymilage<0)
				{
					$todaymilage = round(($this->ConfusedMilages(date("Y-m-d"),$row['objsysno']))/1000,2);
					//$todaymilage = 0;
				}
				$todaymilage = $this->UserUnit($todaymilage)['value'];
				$lengtUnit = $this->UserUnit($todaymilage)['lengthUnit'];
				$speedUnit = $this->UserUnit($todaymilage)['speedUnit'];
			    
				$Time = $row['lasttime'];
				if($Time == "1999-01-01 00:00:00")
				{
					$Time = "-";
				}else{
					$Time = $this->UserTimeFormat($Time);
				}
				
				$parking = "-";
				if(explode(":",$row['parking'])[0] < 100)
				{
					$parking = $row['parking'];
				}
				$Vehicleinfo[]=array('speedUnit'=>$speedUnit,'lengtUnit'=>$lengtUnit,'simno'=>$row['simno'],'status'=>$this->Status($row['lastvelosity'],$row['lastengine'],$row['UTCTime']),'parking'=>$parking,'Time'=>$Time,'objsysno'=>$row['objsysno'],'objname'=>$row['objname'],'lastlati'=>sprintf('%0.5f', $row['lastlati']),'lastlng'=>sprintf('%0.5f', $row['lastlng']),'lastmilage'=>$todaymilage,'lastvelosity'=>$this->UserUnit($row['lastvelosity'])['value'],'lastfuel'=>$row['lastfuel'],'lastdirection'=>$row['lastdirection'],'imagefolder'=>$row['mapimage']);
			}else{
				$Vehicleinfo[] = null;
			}
		}
		//echo "4: ".round(microtime(true) * 1000)."<br>";
		return $Vehicleinfo;
	}

	
	function Todaymilage($sysno)
	{
		$milage2=0;
		$milage1=0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT * FROM `tko".$sysno."` WHERE Time <= convert_tz(now(),@@session.time_zone,'+00:00') AND Time >= CURDATE() ORDER BY Time LIMIT 1";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$milage1=$row['Miles'];
		}
		
		$qry2 = "SELECT * FROM `tko".$sysno."` WHERE Time <= convert_tz(now(),@@session.time_zone,'+00:00') AND Time >= CURDATE() ORDER BY Time DESC LIMIT 1";
		
		$stmt2 = mysqli_query($this->connection,$qry2);
		
		while($row2 = mysqli_fetch_array($stmt2))
		{
			$milage2=$row2['Miles'];
		}
			$num = $milage2 - $milage1;
			$km = $num/1000;
			$km = round($km, 0, PHP_ROUND_HALF_UP);
			return $km;
	}
	
	function UserManagementList()
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$tablecontent = "";
		$qry = "SELECT `id_user`, `name`, `email`, `phone_number`, `username`, `password`, `phone`, `company`, `confirmcode`, `HeadUser`, `Privilage` FROM `gpsusers` WHERE HeadUser='".$this->headuser."'";
		       
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            echo "No records found";
        }
        
        while($row = mysqli_fetch_array($result))
		{
			$tablecontent = $tablecontent.
			"<tr>
			<td><form id='pwd_".$row['id_user']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='resetPwd' value='".$row['id_user']."'/></form><form id='".$row['id_user']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='del' value='".$row['username']."'/></form><form id='userload".$row['id_user']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='user' value='".$row['username']."'/></form>".$row['name']."</td>
			<td>".$row['username']."</td>
			<td>".$row['phone']."</td>
			<td>".$row['company']."</td>
			<td><a class='btn btn-primary btn-sm' onclick='ResetPwd(".$row['id_user'].")' ><i class='glyphicon glyphicon-lock icon-white'></i>Reset Password</a>&nbsp<a class='btn btn-primary btn-sm' onclick='userload(".$row['id_user'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Edit</a>&nbsp<a class='btn btn-danger btn-sm' onclick='delconfirm(".$row['id_user'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a></td>
			</tr>";
		}
        //print_r($row);
		return $tablecontent;
	}
	
	function addexistinguser($vehicleno)
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$tablecontent = "";
		$qry = "SELECT `id_user`, `name`, `email`, `phone_number`, `username`, `password`, `phone`, `company`, `confirmcode`, `HeadUser`, `Privilage` FROM `gpsusers` WHERE HeadUser='".$this->headuser."'";
		       
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            echo "No records found";
        }
        
        while($row = mysqli_fetch_array($result))
		{
			$tablecontent = $tablecontent."<tr><td><form id='userload".$row['id_user']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='checkbox[]' value='".$vehicleno."'/><input type='hidden' name='user' value='".$row['username']."'/></form>".$row['name']."</td><td>".$row['username']."</td><td>".$row['phone']."</td><td>".$row['company']."</td><td><a class='btn btn-primary btn-sm' onclick='userload(".$row['id_user'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Add</a></td></tr>";
		}
        //print_r($row);
		return $tablecontent;
	}
	
	function LoadUserManagement($user)
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$tablecontent = "";
		//$qryresult[] = array;
		$qry = "SELECT `id_user`, `name`, `email`, `phone_number`, `username`, `password`, `phone`, `company`, `confirmcode`, `HeadUser`, `Privilage` FROM `gpsusers` WHERE username = '$user'";
		       
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            return false;
        }
        
        while($row = mysqli_fetch_array($result))
		{
			$qryresult['username'] = $row['username'];
			$qryresult['name'] = $row['name'];
			$qryresult['email'] = $row['email'];
			$qryresult['phone'] = $row['phone'];
			$qryresult['company'] = $row['company'];
			$qryresult['confirmcode'] = $row['confirmcode'];
			$qryresult['Privilage'] = $row['Privilage'];
			$qryresult['admingroup'] = $row['admingroup'];
		}
		return $qryresult;
	}
	
	function AddUser($USERDATA)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }  
		
		$qry = "INSERT INTO `gpsusers`(`name`, `email`, `username`, `password`, `phone`, `company`, `confirmcode`, `HeadUser`, `Privilage`) 
		VALUES ('".$this->Sanitize($USERDATA['fullname'])."','".$this->Sanitize($USERDATA['name2'])."','".$this->Sanitize($USERDATA['username'])."','".md5($USERDATA['password'])."','".$this->Sanitize($USERDATA['phone'])."','".$this->Sanitize($USERDATA['company'])."','Y','".$this->headuser."','N')";
		
        if(!mysqli_query($this->connection, $qry))
        {
            $this->HandleDBError("Error inserting data to the table");
            return false;
        }     
		$this->ActivityLog("AddUser", $this->UserName(), "",$USERDATA);		
        return true;
	}
	
	function GiveApprovel($user,$approvel)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
		
        $qry = "Update $this->tablename Set confirmcode='$approvel' Where  username='$user'";
        
        if(!mysqli_query($this->connection, $qry))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }      
		$this->ActivityLog("GiveApprovel", $this->UserName(), "",$user."->".$approvel);		
        return true;
	}
	
	function GiveApprovelwithdatachange($profilevariables,$approvel)
	{
		if(!$this->isAdminfullrights())
		{
			return false;
		}
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
		
		if($profilevariables['userrole']=='group')
		{
			$qry = "UPDATE `gpsusers` SET confirmcode='$approvel', `name`= '".$this->Sanitize($profilevariables['fullname']) ."',`email`= '".$this->Sanitize($profilevariables['name2']) ."',`phone`= '".$this->Sanitize($profilevariables['phone']) ."',`company`= '".$this->Sanitize($profilevariables['company']) ."',`Privilage`='".$profilevariables['userrole']."',`admingroup`='".$profilevariables['groupno']."' WHERE username = '".$profilevariables['username']."'";
		}else{
		
		$qry = "UPDATE `gpsusers` SET confirmcode='$approvel', `name`= '".$this->Sanitize($profilevariables['fullname']) ."',`email`= '".$this->Sanitize($profilevariables['name2']) ."',`phone`= '".$this->Sanitize($profilevariables['phone']) ."',`company`= '".$this->Sanitize($profilevariables['company']) ."',`Privilage`='".$profilevariables['userrole']."',`admingroup`= null WHERE username = '".$profilevariables['username']."'";
        }
        
        if(!mysqli_query($this->connection, $qry))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }      
		$this->ActivityLog("GiveApprovelwithdatachange", $this->UserName(), "",$profilevariables."->".$approvel);
        return true;
	}
	
	
	//////////////////////////////////////////	Object Settings started		//////////////////////////////
	function ShareWith($sysno,$email)
	{
		//check privilage
		if(!$this->checksharewithprivilage($sysno,$this->UserName()))
		{
			return "Privilage Error!...";
		}
		//check user avalability
		$resultarray = $this->isEmailAvailable($email);
		$username = $resultarray['username'];
	
		if(!$resultarray['result'])
		{
			//send invitation email
			$subject = "Gsupertrack invitation from ".$this->UserFullName();
			$htmlbody = "Hi,<BR />".$this->UserFullName()." invite you to register in gsupertrack.com to share the tracking and monitoring experience with you. follow this link to register"."<BR /><a href='service.gsupertrack.com/register.php'>Register</a>";
			$this->Sendemail($email,$subject,$htmlbody);
			return "This user is not available!...<BR />We have invited your friend to Gsupertrack.com";
		}
		if($this->isassign($username,$sysno))
		{
			return "This user is already assign this device!";
		}
		//assign object
		if($this->AssignUserObject($username,$sysno))
		{
			$subject = "Gsupertrack shared a device";
			$htmlbody = "Hi,<BR />".$this->UserFullName()." shared a tracking device with you. login to gsupertrack.com"."<BR /><a href='service.gsupertrack.com'>Login</a>";
			$this->Sendemail($email,$subject,$htmlbody);
			return "Succesfulley shared with $email";
		}
		return "Sharing Error!...";
	}
	
	function isEmailAvailable($email)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return array('result'=>false,'username'=>'');
        }

        $qry = "SELECT * FROM `gpsusers` WHERE `email` = '$email'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Admin Checking Error!");
            return array('result'=>false,'username'=>'');
        }
		$row = mysqli_fetch_assoc($result);
		return array('result'=>true,'username'=>$row['username']);
	}
	
	function checksharewithprivilage($sysno,$user){
		$rowcount = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry = "SELECT *  FROM trackingobjects WHERE objsysno = '$sysno' AND manager = '$user'";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$rowcount++;
			$sys = $row['objsysno'];
		}
		if($rowcount == 0)
		{
			return false;
		}
		return true;
	}

	function SearchObjectManagementList($serchvalue)
	{
		$superuser = $this->issuperuser();
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$tablecontent = "";
		
		$qry = "SELECT * FROM `trackingobjects` WHERE (`objname` like '%$serchvalue%'
					OR `objsysno` like '%$serchvalue%'
					OR `simno` like '%$serchvalue%'
					OR `model` like '%$serchvalue%'
					OR `expdate` like '%$serchvalue%'
					OR `objectdiscription` like '%$serchvalue%')
					AND `HeadUser` = '".$this->headuser."'
					ORDER BY `objname`";

					
		$stmt = mysql_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$freeextendbtn = "";
			$yearextendbtn = "";
			$yearextendform = "";
			$phpdate = strtotime($row['fixeddate']);
			$mysqldate = date( 'Y-m-d', $phpdate );
			
			if(strtotime($row['expdate'])<strtotime("+30 days"))
			{
				$yearextendbtn =  "<a class='btn btn-warning btn-sm' onclick='yearextend(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Year Extend</a>";
				$yearextendform = "<form id='yearextend".$row['id']."' action='payments.php' method='post'><input type='hidden' name='busno' value='".$row['objname']."'/><input type='hidden' name='systemno' value='".$row['objsysno']."'/></form>";
			}
			
			if($row['activate'] =='1')
			{
				$Active = "Active";
			}else{
				$Active = "Deactive";
				if(strtotime($row['expdate'])<strtotime("now"))
				{
				$freeextendbtn = "<a class='btn btn-primary btn-sm' onclick='freeextend(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Free Extend</a>";
				}
			}
			if($row['sendntc'] =='1')
			{
				$ntcsend = "Yes";
			}else{
				$ntcsend = "No";
			}

			$tablecontent = $tablecontent."<tr>
			<td><form id='".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='del' value='".$row['objsysno']."'/></form><form id='userload".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='user' value='".$row['objsysno']."'/></form><form id='extend".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='freeextend' value='".$row['objsysno']."'/></form>".$yearextendform.$row['objname']."</td>
			<td>".$row['objsysno']."</td><td>".$row['simno']."</td>
			<td>".date( 'Y-m-d', strtotime($row['expdate']))."</td>
			<td>".$row['origine']."</td><td>".$row['Destnation']."</td>
			<td>".$mysqldate."</td><td>".$row['model']."</td>
			<td>".$row['fixedby']."</td>
			<td>".$Active." ".$freeextendbtn."</td>
			<td>".$ntcsend."</td>
			<td>
			<a class='btn btn-primary btn-sm' onclick='userload(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Edit</a><a class='btn btn-danger btn-sm' onclick='delconfirm(".$row['id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
			$yearextendbtn
			</td></tr>";
		}
		return $tablecontent;
	}
	
	function ObjectManagementList($userstatus)
	{
		$superuser = $this->issuperuser();
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$tablecontent = "";
		if($userstatus == 'normal')
		{
			$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, trackingobjects.id AS `id`, IFNULL(D.GroupName,'Uncategorized') admingroup, `objname`, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager` 
			FROM trackingobjects 
			LEFT JOIN geofences as B 
			ON trackingobjects.origine = B.id 
			LEFT JOIN geofences as C 
			ON trackingobjects.Destnation = C.id 
			LEFT JOIN GroupsAdmin as D
			ON trackingobjects.admin_group = D.Id
			WHERE objsysno IN ('".$this->assignedvehiclestring()."')
			ORDER BY admingroup ,objname";
			//AND trackingobjects.manager = '".$this->UserName()."'
		}else{
			$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, trackingobjects.id AS `id`, IFNULL(D.GroupName,'Uncategorized') admingroup, `objname`, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager` 
			FROM trackingobjects 
			LEFT JOIN geofences as B 
			ON trackingobjects.origine = B.id 
			LEFT JOIN geofences as C 
			ON trackingobjects.Destnation = C.id 
			LEFT JOIN GroupsAdmin as D
			ON trackingobjects.admin_group = D.Id
			WHERE objsysno IN ('".$this->assignedvehiclestring()."')
			ORDER BY admingroup ,objname";
		}


		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$freeextendbtn = "";
			$yearextendbtn = "";
			$yearextendform = "";
			$superuseryearextendbtn = "";
			$superuseryearextendform = "";
			
			$phpdate = strtotime($row['fixeddate']);
			$mysqldate = date( 'Y-m-d', $phpdate );
			
			if(strtotime($row['expdate'])<strtotime("+30 days"))
			{
				if($this->isAdminfullrights())
				{	
					//$yearextendbtn =  "<a class='btn btn-warning btn-sm' onclick='yearextend(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Activate Pro</a>";
					//$yearextendform = "<form id='yearextend".$row['id']."' action='payments.php' method='post'><input type='hidden' name='busno' value='".$row['objname']."'/><input type='hidden' name='systemno' value='".$row['objsysno']."'/></form>";
				}
				if($superuser)
				{
					$superuseryearextendbtn =  "<a class='btn btn-success btn-sm' onclick='superyearextend(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Extend</a>";
					$superuseryearextendform = "<form id='superyearextend".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='yearextend' value='".$row['objsysno']."'/></form>";
				}
			}
			$sharebtn =  "<a class='btn btn-primary btn-sm' onclick='sharewith(".$row['id'].")' ><i class='glyphicon glyphicon-share icon-white'></i>share</a>";
			$shareform = "<form id='sharewith".$row['id']."' action='sharewith.php' method='post'><input type='hidden' name='sharebusno' value='".$row['objname']."'/><input type='hidden' name='sharewith' value='".$row['objsysno']."'/></form>";
			
			if($row['activate'] =='1')
			{
				$Active = "Active";
			}else{
				$Active = "Deactive";
				if(strtotime($row['expdate'])<strtotime("now"))
				{
				$freeextendbtn = "<a class='btn btn-primary btn-sm' onclick='freeextend(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Free Extend</a>";
				}
			}
			if($row['sendntc'] =='1')
			{
				$ntcsend = "Yes";
			}else{
				$ntcsend = "No";
			}

			$tablecontent = $tablecontent."<tr>
			<td><form id='".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='del' value='".$row['objsysno']."'/></form>
			<form id='userload".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='user' value='".$row['objsysno']."'/></form>
			<form id='extend".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='freeextend' value='".$row['objsysno']."'/></form>".$shareform.$yearextendform.$superuseryearextendform.$row['objname']."</td>
			<td>".$row['objsysno']."</td><td>".$row['simno']."</td>
			<td>".date( 'Y-m-d', strtotime($row['expdate']))."</td>
			<td>".$Active."</td>
			<td>
			<a class='btn btn-primary btn-sm' onclick='userload(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Edit</a> <a class='btn btn-danger btn-sm' onclick='delconfirm(".$row['id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
			</td></tr>";
		}
		return $tablecontent;
	}
	
	function ObjectInsertObject($postvalues)
	{
		if($postvalues['reson'] == "new")
		{
			if($this->checksysno($postvalues['objsysno']))
			{
				return "Emei No already exist. Please insert a dirrerent number!";
			}else{
				if($this->InsertObjecttoDB($postvalues))
				{
					$this->InsertLog($postvalues,"Insert");
					return "ok";
				}
				else{
					return "Object not inserted";
				}
			}
		}
		
		if($postvalues['reson'] == "update")
		{
			if($postvalues['objsysno'] == $postvalues['oldobjsysno'])
			{
				if($this->UpdateObjecttoDB($postvalues))
				{
					$this->InsertLog($postvalues,"Update");
					return "ok";
				}
				else{
					return "Object not updated";
				}
			}else{
				if($this->checksysno($postvalues['objsysno']))
				{
					return "Device No already exist. Please insert a dirrerent number!";
				}else{
					if($this->ReplaceObjecttoDB($postvalues))
					{
						$this->InsertLog($postvalues,"replace from:".$postvalues['oldobjsysno']);
						return "ok";
					}
					else{
						return "Object not replaced";
					}
				}
			}
		}
	}
	
	function InsertObject($postvalues)
	{
		/*
		if(!$this->isAdminfullrights())
		{
			return "You dont have admin rights. Please contact system administrator!";
		}
		*/
		if(trim($postvalues['objsysno']) == "" || trim($postvalues['objname']) == "")
		{
			return "Error! Object emei number and object name cannot be empty.";
		}
		if($postvalues['reson'] == "new")
		{
			
			if($this->checksysno($postvalues['objsysno']))
			{
				return "Emei No already exist. Please insert a different number!";
			}else{
					$inserrtresult = $this->InsertObjecttoDB($postvalues);
					
					if($inserrtresult == true)
					{
						$this->InsertLog($postvalues,"Insert");
						$this->SendAdminIntimationEmailaddDevice($postvalues['objsysno'],$postvalues['objname'],$postvalues['fixedby'],$this->UserName(),"newly installed");
						return "New object inserted";
					}else{
							return "Object not inserted! Please check the values";
					}
			}
		}
		
		if($postvalues['reson'] == "update")
		{
			if($postvalues['objsysno'] == $postvalues['oldobjsysno'])
			{
				if($this->UpdateObjecttoDB($postvalues))
				{
					$this->InsertLog($postvalues,"Update");
					$this->SendAdminIntimationEmailaddDevice($postvalues['objsysno'],$postvalues['objname'],$postvalues['fixedby'],$this->UserName(),"updated");
					return "Object updated";
				}
				else{
					return "<span style='color:red'>Object not updated</span>";
				}
			}else{
				if($this->checksysno($postvalues['objsysno']))
				{
					return "<span style='color:red'>Device No already exist. Please insert a diferent number!</span>";
				}else{
						if($this->ReplaceObjecttoDB($postvalues))
						{
							$this->InsertLog($postvalues,"replace from:".$postvalues['oldobjsysno']);
							$this->SendAdminIntimationEmailaddDevice($postvalues['oldobjsysno']." to ".$postvalues['objsysno'],$postvalues['objname'],$postvalues['fixedby'],$this->UserName(),"Replaced");
							return "Object replaced";
						}
						else{
							return "<span style='color:red'>Object not replaced</span>";
						}
				}
			}
		}
	}
	
	function ReplaceObjecttoDB($postvalues)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$activate = $this->isActivate($postvalues['objsysno']);

		if($this->checkExpdateCurrentdate($postvalues['objsysno']))
		{
			$activate = $postvalues['activate'];
		}
		
		if($postvalues['activate']==0)
		{
			$activate = $postvalues['activate'];
		}
		
		if($postvalues['isadmin']=="admin")
		{
		$qry = "UPDATE trackingobjects
				   SET objname = '".$this->SanitizeForSQL($postvalues['objname'])."'
					  ,objectdiscription = '".$this->SanitizeForSQL($postvalues['objectdiscription'])."'
					  ,simno = '".$this->SanitizeForSQL($postvalues['simno'])."'
					  ,origine = '".$this->SanitizeForSQL($postvalues['origine'])."'
					  ,Destnation = '".$this->SanitizeForSQL($postvalues['Destnation'])."'
					  ,model = '".$this->SanitizeForSQL($postvalues['model'])."'
					  ,tankcapacity = '".$this->SanitizeForSQL($postvalues['tankcapacity'])."'
					  ,tanktype = '".$this->SanitizeForSQL($postvalues['tanktype'])."'
					  ,mapimage = '".$this->SanitizeForSQL($postvalues['mapimage'])."'
					  ,contact = '".$this->SanitizeForSQL($postvalues['contact'])."'
					  ,objsysno = '".$this->SanitizeForSQL($postvalues['objsysno'])."'
					  ,tz = '".$postvalues['tz']."'
					  ,fixeddate = '".$this->SanitizeForSQL($postvalues['fixeddate'])."'
					  ,fixedby  = '".$this->SanitizeForSQL($postvalues['fixedby'])."'
					  ,activate = '".$this->SanitizeForSQL($postvalues['activate'])."'
					  ,sendntc = '".$this->SanitizeForSQL($postvalues['sendntc'])."'
					  ,cam1 = '".$this->SanitizeForSQL($postvalues['cam1'])."'
					  ,admin_group = '".$this->SanitizeForSQL($postvalues['admin_group'])."'
					  ,contactperson = '".$this->SanitizeForSQL($postvalues['contactperson'])."'
					  ,hubee = '".$this->SanitizeForSQL($postvalues['hubee'])."'
					  ,hubeeAdmin = '".$this->SanitizeForSQL($postvalues['hubeeAdmin'])."'
					  ,expdate = '".$this->SanitizeForSQL($postvalues['expdate'])."'
				 WHERE objsysno = '".$this->SanitizeForSQL($postvalues['oldobjsysno'])."'";
		}else{
		$qry = "UPDATE trackingobjects
				   SET objname = '".$this->SanitizeForSQL($postvalues['objname'])."'
					  ,objectdiscription = '".$this->SanitizeForSQL($postvalues['objectdiscription'])."'
					  ,simno = '".$this->SanitizeForSQL($postvalues['simno'])."'
					  ,origine = '".$this->SanitizeForSQL($postvalues['origine'])."'
					  ,Destnation = '".$this->SanitizeForSQL($postvalues['Destnation'])."'
					  ,model = '".$this->SanitizeForSQL($postvalues['model'])."'
					  ,tankcapacity = '".$this->SanitizeForSQL($postvalues['tankcapacity'])."'
					  ,tanktype = '".$this->SanitizeForSQL($postvalues['tanktype'])."'
					  ,mapimage = '".$this->SanitizeForSQL($postvalues['mapimage'])."'
					  ,contact = '".$this->SanitizeForSQL($postvalues['contact'])."'
					  ,objsysno = '".$this->SanitizeForSQL($postvalues['objsysno'])."'
					  ,tz = '".$postvalues['tz']."'
				 WHERE objsysno = '".$this->SanitizeForSQL($postvalues['oldobjsysno'])."'";
		}

		$stmt = mysqli_query($this->connection,$qry);
		if(!$stmt)
		{
			return false;
		}
		$this->ActivityLog("ReplaceObject old", $this->UserName(), $postvalues['oldobjsysno'], $postvalues);
		$this->ActivityLog("ReplaceObject new", $this->UserName(), $postvalues['objsysno'], $postvalues);
		if($this->isActivate($postvalues['objsysno'])!=$postvalues['activate'])
		{
			$this->RenameTable($postvalues);
			$this->ReplaceAssigning($this->UserName(),$postvalues['oldobjsysno'],$postvalues['objsysno']);
			return true;
		}
		$this->RenameTable($postvalues);
		$this->ReplaceAssigning($this->UserName(),$postvalues['oldobjsysno'],$postvalues['objsysno']);
		return true;
	}
	
	function ReplaceAssigning($user,$Oldobject,$Newobject)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "UPDATE `objectassign` SET `objectname`='$Newobject' WHERE `objectname` = '$Oldobject'";
	//echo $qry;	   
		   $stmt = mysqli_query($this->connection,$qry);
		   if($stmt)
		   {
			   $this->ActivityLog("ReplaceAssigning", $this->UserName(), $Oldobject, $user."-".$Oldobject."->".$Newobject);
			   return true;
		   }
		   else{
			   return false;
		   }
	}
	
	function RenameTable($postvalues)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "RENAME TABLE `tko".$postvalues['oldobjsysno']."` TO `tko".$postvalues['objsysno']."`";
		
		$stmt = mysqli_query($this->connection,$qry);
		if(!$stmt)
		{
			return false;
		}
		else{
			$this->ActivityLog("RenameTable", $this->UserName(), $postvalues['objsysno'], $postvalues);
			return true;
		}
	}
	
	function checkExpdateCurrentdate($sysno)
	{
		$result = false;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "SELECT `objsysno`,`expdate` FROM `trackingobjects` WHERE  (`objsysno` = '$sysno') AND (`expdate` > CURDATE())";
		
		$stmt = mysqli_query($this->connection,$qry);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			return true;
		}
		return false;
	}
	
	function isActivate($sysno)
	{
		$result = false;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "SELECT `objsysno`,`activate` FROM `trackingobjects` WHERE  `objsysno` = '$sysno'";
		
		$stmt = mysqli_query($this->connection,$qry);
		//echo $qry1;
		while($row = mysqli_fetch_array($stmt))
		{
			return $row['activate'];
		}
		return 0;
		
		
	}

	function UpdateDivFrmAPP($postvalues)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$activate = $this->isActivate($postvalues['objsysno']);

		if($this->checkExpdateCurrentdate($postvalues['objsysno']))
		{
			$activate = $postvalues['activate'];
		}
		
		if($postvalues['activate']==0)
		{
			$activate = $postvalues['activate'];
		}

		$qry = "UPDATE trackingobjects
				   SET objname = '".$this->SanitizeForSQL($postvalues['objname'])."'
					  ,model = '".$this->SanitizeForSQL($postvalues['model'])."'
					  ,mapimage = '".$this->SanitizeForSQL($postvalues['mapimage'])."'
					  ,tz = '".$postvalues['tz']."'
				 WHERE objsysno = '".$this->SanitizeForSQL($postvalues['objsysno'])."'";

		
		$stmt = mysqli_query($this->connection,$qry);
		if(!$stmt)
		{
			return false;
		}
		$this->ActivityLog("UpdateDivFrmAPP", $this->UserName(), $postvalues['objsysno'], $postvalues);
		if($this->isActivate($postvalues['objsysno'])!=$postvalues['activate'])
		{
			return true;
		}
		return true;
	}
	
	function UpdateObjecttoDB($postvalues)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$activate = $this->isActivate($postvalues['objsysno']);

		if($this->checkExpdateCurrentdate($postvalues['objsysno']))
		{
			$activate = $postvalues['activate'];
		}
		
		if($postvalues['activate']==0)
		{
			$activate = $postvalues['activate'];
		}
		if($postvalues['isadmin']=="admin")
		{
			

			
		$qry = "UPDATE trackingobjects
				   SET objname = '".$this->SanitizeForSQL($postvalues['objname'])."'
					  ,objectdiscription = '".$this->SanitizeForSQL($postvalues['objectdiscription'])."'
					  ,simno = '".$this->SanitizeForSQL($postvalues['simno'])."'
					  ,origine = '".$this->SanitizeForSQL($postvalues['origine'])."'
					  ,Destnation = '".$this->SanitizeForSQL($postvalues['Destnation'])."'
					  ,model = '".$this->SanitizeForSQL($postvalues['model'])."'
					  ,tankcapacity = '".$this->SanitizeForSQL($postvalues['tankcapacity'])."'
					  ,tanktype = '".$this->SanitizeForSQL($postvalues['tanktype'])."'
					  ,mapimage = '".$this->SanitizeForSQL($postvalues['mapimage'])."'
					  ,contact = '".$this->SanitizeForSQL($postvalues['contact'])."'
					  ,tz = '".$postvalues['tz']."'
					  ,fixeddate = '".$this->SanitizeForSQL($postvalues['fixeddate'])."'
					  ,fixedby  = '".$this->SanitizeForSQL($postvalues['fixedby'])."'
					  ,activate = '".$this->SanitizeForSQL($postvalues['activate'])."'
					  ,sendntc = '".$this->SanitizeForSQL($postvalues['sendntc'])."'
					  ,cam1 = '".$this->SanitizeForSQL($postvalues['cam1'])."'
					  ,admin_group = '".$this->SanitizeForSQL($postvalues['admin_group'])."'
					  ,contactperson = '".$this->SanitizeForSQL($postvalues['contactperson'])."'
					  ,hubee = '".$this->SanitizeForSQL($postvalues['hubee'])."'
					  ,hubeeAdmin = '".$this->SanitizeForSQL($postvalues['hubeeAdmin'])."'
					  ,expdate = '".$this->SanitizeForSQL($postvalues['expdate'])."'
				 WHERE objsysno = '".$this->SanitizeForSQL($postvalues['objsysno'])."'";
		}else{
		$qry = "UPDATE trackingobjects
				   SET objname = '".$this->SanitizeForSQL($postvalues['objname'])."'
					  ,objectdiscription = '".$this->SanitizeForSQL($postvalues['objectdiscription'])."'
					  ,simno = '".$this->SanitizeForSQL($postvalues['simno'])."'
					  ,origine = '".$this->SanitizeForSQL($postvalues['origine'])."'
					  ,Destnation = '".$this->SanitizeForSQL($postvalues['Destnation'])."'
					  ,model = '".$this->SanitizeForSQL($postvalues['model'])."'
					  ,tankcapacity = '".$this->SanitizeForSQL($postvalues['tankcapacity'])."'
					  ,tanktype = '".$this->SanitizeForSQL($postvalues['tanktype'])."'
					  ,mapimage = '".$this->SanitizeForSQL($postvalues['mapimage'])."'
					  ,contact = '".$this->SanitizeForSQL($postvalues['contact'])."'
					  ,tz = '".$postvalues['tz']."'
				 WHERE objsysno = '".$this->SanitizeForSQL($postvalues['objsysno'])."'";
		}
		
		$stmt = mysqli_query($this->connection,$qry);
		if(!$stmt)
		{
			return false;
		}
		$this->ActivityLog("UpdateObjecttoDB", $this->UserName(), $postvalues['objsysno'], $postvalues);
		if($this->isActivate($postvalues['objsysno'])!=$postvalues['activate'])
		{
			return true;
		}
		return true;
	}
	
	function InsertObjecttoDB($postvalues)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return "Database login failed!";
        } 
		
		
		//$exp_date = date("Y-m-d");
		$exp_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($postvalues['fixeddate'])) . " + 1 year"));
		if($postvalues['isadmin']=="admin")
		{
		
		$qry = "INSERT INTO trackingobjects
						   (objname
						   ,objsysno
						   ,objectdiscription
						   ,simno
						   ,origine
						   ,Destnation
						   ,model
						   ,HeadUser
						   ,expdate
						   ,tankcapacity
						   ,tanktype
						   ,mapimage
						   ,contact
						   ,tz
						   ,manager
						  ,fixeddate
						  ,fixedby
						  ,activate
						  ,sendntc
						  ,cam1
						  ,admin_group
						  ,contactperson
						  ,hubee
						  ,hubeeAdmin)
					 VALUES
						   ('".$this->SanitizeForSQL($postvalues['objname'])."'
						   ,'".$this->SanitizeForSQL($postvalues['objsysno'])."'
						   ,'".$this->SanitizeForSQL($postvalues['objectdiscription'])."'
						   ,'".$this->SanitizeForSQL($postvalues['simno'])."'
						   ,'".$this->SanitizeForSQL($postvalues['origine'])."'
						   ,'".$this->SanitizeForSQL($postvalues['Destnation'])."'
						   ,'".$this->SanitizeForSQL($postvalues['model'])."'
						   ,'".$this->SanitizeForSQL($this->headuser)."'
						   ,'".$this->SanitizeForSQL($exp_date)."'
						   ,'".$this->SanitizeForSQL($postvalues['tankcapacity'])."'
						   ,'".$this->SanitizeForSQL($postvalues['tanktype'])."'
						   ,'".$this->SanitizeForSQL($postvalues['mapimage'])."'
						   ,'".$this->SanitizeForSQL($postvalues['contact'])."'
						   ,'".$postvalues['tz']."'
						   ,'".$this->SanitizeForSQL($this->UserName())."'
						   ,'".$this->SanitizeForSQL($postvalues['fixeddate'])."'
						   ,'".$this->SanitizeForSQL($postvalues['fixedby'])."'
						   ,'".$this->SanitizeForSQL($postvalues['activate'])."'
						   ,'".$this->SanitizeForSQL($postvalues['sendntc'])."'
						   ,'".$this->SanitizeForSQL($postvalues['cam1'])."'
						   ,'".$this->SanitizeForSQL($postvalues['admin_group'])."'
						   ,'".$this->SanitizeForSQL($postvalues['contactperson'])."'
						   ,'1'
						   ,'".$this->SanitizeForSQL($postvalues['hubeeAdmin'])."'
						   )";
		}else{
		$qry = "INSERT INTO trackingobjects
						   (objname
						   ,objsysno
						   ,objectdiscription
						   ,simno
						   ,origine
						   ,Destnation
						   ,model
						   ,fixedby
						   ,HeadUser
						   ,expdate
						   ,activate
						   ,tankcapacity
						   ,tanktype
						   ,mapimage
						   ,contact
						   ,tz
						   ,manager)
					 VALUES
						   ('".$this->SanitizeForSQL($postvalues['objname'])."'
						   ,'".$this->SanitizeForSQL($postvalues['objsysno'])."'
						   ,'".$this->SanitizeForSQL($postvalues['objectdiscription'])."'
						   ,'".$this->SanitizeForSQL($postvalues['simno'])."'
						   ,'".$this->SanitizeForSQL($postvalues['origine'])."'
						   ,'".$this->SanitizeForSQL($postvalues['Destnation'])."'
						   ,'".$this->SanitizeForSQL($postvalues['model'])."'
						   ,'".$this->SanitizeForSQL($postvalues['fixedby'])."'
						   ,'".$this->SanitizeForSQL($this->headuser)."'
						   ,'".$this->SanitizeForSQL($exp_date)."'
						   ,'0'
						   ,'".$this->SanitizeForSQL($postvalues['tankcapacity'])."'
						   ,'".$this->SanitizeForSQL($postvalues['tanktype'])."'
						   ,'".$this->SanitizeForSQL($postvalues['mapimage'])."'
						   ,'".$this->SanitizeForSQL($postvalues['contact'])."'
						   ,'".$postvalues['tz']."'
						   ,'".$this->SanitizeForSQL($this->UserName())."'";
		}
		//echo $qry;
		$stmt = mysqli_query($this->connection,$qry);
		if(!$stmt)
		{
			return false;
		}
		else{
			$tablecreate = $this->createdatatable($postvalues['objsysno']);
			$this->AssignUserObject($this->UserName(),$postvalues['objsysno']);
			$this->ActivityLog("InsertObjecttoDB", $this->UserName(), $postvalues['objsysno'], $postvalues);
			return true;
		}
	}
	
	function InsertLog($postvalues,$action)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$exp_date = date("Y-m-d", strtotime(date("Y-m-d", strtotime($postvalues['fixeddate'])) . " + 30 day"));
		
		$qry = "INSERT INTO trackingobjectslog
						   (objname
						   ,objsysno
						   ,objectdiscription
						   ,simno
						   ,origine
						   ,Destnation
						   ,fixeddate
						   ,model
						   ,fixedby
						   ,HeadUser
						   ,expdate
						   ,activate
						   ,sendntc
						   ,action
						   ,logger
						   ,cam1
						   ,cam2
						   ,mapimage
						   ,contact
						   ,tankcapacity
						   ,tanktype
						   ,tz
						   ,admin_group)
					 VALUES
						   ('".$this->SanitizeForSQL($postvalues['objname'])."'
						   ,'".$this->SanitizeForSQL($postvalues['objsysno'])."'
						   ,'".$this->SanitizeForSQL($postvalues['objectdiscription'])."'
						   ,'".$this->SanitizeForSQL($postvalues['simno'])."'
						   ,'".$this->SanitizeForSQL($postvalues['origine'])."'
						   ,'".$this->SanitizeForSQL($postvalues['Destnation'])."'
						   ,'".$this->SanitizeForSQL($postvalues['fixeddate'])."'
						   ,'".$this->SanitizeForSQL($postvalues['model'])."'
						   ,'".$this->SanitizeForSQL($postvalues['fixedby'])."'
						   ,'".$this->SanitizeForSQL($this->headuser)."'
						   ,'".$this->SanitizeForSQL($exp_date)."'
						   ,'".$this->SanitizeForSQL($postvalues['activate'])."'
						   ,'".$this->SanitizeForSQL($postvalues['sendntc'])."'
						   ,'".$action."'
						   ,'".$this->SanitizeForSQL($this->UserName())."'
						   ,'".$this->SanitizeForSQL($postvalues['cam1'])."'
						   ,'0'
						   ,'".$this->SanitizeForSQL($postvalues['mapimage'])."'
						   ,'".$this->SanitizeForSQL($postvalues['contact'])."'
						   ,'".$this->SanitizeForSQL($postvalues['tankcapacity'])."'
						   ,'".$this->SanitizeForSQL($postvalues['tanktype'])."'
						   ,'".$postvalues['tz']."'
						   ,'".$postvalues['admin_group']."')";
		
		$stmt = mysqli_query($this->connection,$qry);

	}
	
	function createdatatable($tablename)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "CREATE TABLE `tko".$tablename."` ( `Time` DATETIME NOT NULL ,  `Longitude` DOUBLE NOT NULL ,  `Latitude` DOUBLE NOT NULL ,  `Velocity` INT NOT NULL ,  `Angle` INT NOT NULL ,  `Locate` INT NOT NULL ,  `DtStatus` INT NOT NULL ,  `Oil` INT NOT NULL ,  `Miles` INT NOT NULL ,  `Temperature` INT NOT NULL ,  `Alarm` VARCHAR(11) NOT NULL ,  `send` INT NOT NULL ,  `result` VARCHAR(11) NOT NULL ,    PRIMARY KEY  (`Time`) ) ENGINE = InnoDB";
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			$this->ActivityLog("createdatatable", $this->UserName(), "", $tablename);
			return true;
		}else{return false;}
	}
	
	function dropdatatable($tablename)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "DROP TABLE tko".$tablename;
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			$this->ActivityLog("dropdatatable", $this->UserName(), "", $tablename);
			return true;
		}else{return false;}
	}
	
	function checksysno($sysno){
		$rowcount = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry = "SELECT *  FROM trackingobjects WHERE objsysno = '$sysno'";
		
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$rowcount++;
			$sys = $row['objsysno'];
		}
		if($rowcount == 0)
		{
			return false;
		}
		return true;
	}
	
	function checkheadsysno($sysno){
		$rowcount = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "SELECT *  FROM trackingobjects WHERE HeadUser = '".$this->headuser."' AND objsysno='$sysno'";
		//$sqlqry = "SELECT [id],[objname],[objsysno],[objectdiscription],[simno],[origine],[Destnation],[fixeddate],[model],[fixedby],[lastlng],[lastlati],[lastvelosity],[lastdirection],[lasttime],[lastmilage],[lastfuel],[lasttemp],[HeadUser]  FROM [dbo].[trackingobjects] WHERE objsysno = '$sysno'";
		
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$rowcount++;
			$sys = $row['objsysno'];
		}
		if($rowcount == 0)
		{
			return false;
		}
		return true;
	
	}
	
	function GeofenceList($select)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$select = trim($select);
		$fencelist = "";
		$qry = "SELECT id
					  ,geo_name
					  ,remarks
					  ,cordinates
				  FROM geofences
				  WHERE `geouser` = '".$this->UserName()."'
				  ORDER BY geo_name";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($rowbus = mysqli_fetch_array($stmt))
		{
			$selectvalue = '';
			if(trim($rowbus['id'])==$select)
			{
				$selectvalue = 'selected';
			}
		$fencelist = $fencelist."<option value='".$rowbus['id']."' $selectvalue>".$rowbus['geo_name']."</option>";
		}
	return $fencelist;
	}
	function admin_groupList($select)
	{
		if($select == "")
		{
			$select = 0;
		}
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$select = trim($select);
		$fencelist = "";
		$qry = "SELECT `Id`, `GroupName`, `StartedTime` FROM `GroupsAdmin` ORDER BY `GroupName`";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($rowbus = mysqli_fetch_array($stmt))
		{
			$selectvalue = '';
			if((int)$rowbus['Id']==(int)$select)
			{
				$selectvalue = 'selected';
			}
		$fencelist = $fencelist."<option value='".$rowbus['Id']."' ".$selectvalue.">".$rowbus['GroupName']."</option>";
		}
	return $fencelist;
	}
	
	function InsertSystemSettings($PostValues)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		//print_r($PostValues);
		$dayLightEnable = "0";
		$dayLightStart = "null";
		$dayLightEnd = "null";
		
		if(isset($PostValues['dayLightEnable']) && $PostValues['dayLightEnable'] == 1)
		{
			$dayLightEnable = '1';
			$dayLightStart = "'".$PostValues['dayLightStart']."'";
			$dayLightEnd = "'".$PostValues['dayLightEnd']."'";
		}
		
		$qry = "UPDATE `gpsusers` SET `dayLightEnable`='".$dayLightEnable."', `dayLightStart`=".$dayLightStart.", `dayLightEnd`=".$dayLightEnd.", `timeZone`='".$PostValues['timeZone']."',`timeFormat`='".$PostValues['timeFormat']."',`unit`='".$PostValues['unit']."' WHERE `username` = '".$this->UserName()."'";
		//$qry = "UPDATE `gpsusers` SET `timeZone`='".$PostValues['timeZone']."',`timeFormat`='".$PostValues['timeFormat']."',`unit`='".$PostValues['unit']."' WHERE `username` = '".$this->UserName()."'";

		//echo $qry;
		//exit;

		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			return true;
		}
		return false;
	}

	function InsertSystemSettingsAdmin($PostValues)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		//print_r($PostValues);
		$dayLightEnable = "0";
		$dayLightStart = "null";
		$dayLightEnd = "null";
		
		if(isset($PostValues['dayLightEnable']) && $PostValues['dayLightEnable'] == 1)
		{
			$dayLightEnable = '1';
			$dayLightStart = "'".$PostValues['dayLightStart']."'";
			$dayLightEnd = "'".$PostValues['dayLightEnd']."'";
		}
		
		$qry = "UPDATE `gpsusers` SET `dayLightEnable`='".$dayLightEnable."', `dayLightStart`=".$dayLightStart.", `dayLightEnd`=".$dayLightEnd." WHERE 1";

		//echo $qry;
		//exit;

		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			return true;
		}
		return false;
	}	
	function LoadSystemSettings($returnType)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "SELECT `username`, `timeZone`, `timeFormat`, `unit`, `dayLightEnable`, `dayLightStart`, `dayLightEnd` FROM `gpsusers` WHERE `username` = '".$this->UserName()."'";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$qryresult['username'] = $row['username'];
			$qryresult['timeZone'] = $row['timeZone'];
			$qryresult['timeFormat'] = $row['timeFormat'];
			$qryresult['unit'] = $row['unit'];
			$qryresult['dayLightEnable'] = $row['dayLightEnable'];
			$qryresult['dayLightStart'] = $row['dayLightStart'];
			$qryresult['dayLightEnd'] = $row['dayLightEnd'];
		}
		if($returnType == "array")
		{
			return $qryresult;
		}
		return null;
	}
	
	function LoadObjectManagement($objsysno)
	{
		$tablecontent = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "SELECT *  FROM trackingobjects WHERE HeadUser = '".$this->headuser."' AND objsysno = '$objsysno'";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$qryresult['objname'] = $row['objname'];
			$qryresult['objsysno'] = $row['objsysno'];
			$qryresult['simno'] = $row['simno'];
			$qryresult['origine'] = $row['origine'];
			$qryresult['Destnation'] = $row['Destnation'];
			$qryresult['fixeddate'] = $row['fixeddate'];
			$qryresult['model'] = $row['model'];
			$qryresult['fixedby'] = $row['fixedby'];
			$qryresult['objectdiscription'] = $row['objectdiscription'];
			$qryresult['activate'] = $row['activate'];
			$qryresult['sendntc'] = $row['sendntc'];
			$qryresult['tankcapacity'] = $row['tankcapacity'];
			$qryresult['tanktype'] = $row['tanktype'];
			$qryresult['cam1'] = $row['cam1'];
			$qryresult['cam2'] = $row['cam2'];
			$qryresult['mapimage'] = $row['mapimage'];
			$qryresult['contact'] = $row['contact'];
			$qryresult['tz'] = $row['tz'];
			$qryresult['admin_group'] = $row['admin_group'];
			$qryresult['contactperson'] = $row['contactperson'];
			$qryresult['hubee'] = $row['hubee'];
			$qryresult['hubeeAdmin'] = $row['hubeeAdmin'];
			$qryresult['expdate'] = $row['expdate'];
		}
		return $qryresult;
		
	}
	
	function ObjectDelete($objsysno)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		if(!$this->isAdminfullrights())
		{
			return false;
		}
		$qry = "DELETE FROM trackingobjects
			WHERE objsysno = '$objsysno'";

	  $qrydel = "DELETE FROM objectassign
			WHERE objectname = '$objsysno'";
	  
		$stmt1 = mysqli_query($this->connection,$qrydel);
		$stmt = mysqli_query($this->connection,$qry);
		
		if($stmt)
		{
			if(!$this->dropdatatable($objsysno)){
				echo "Table not deleted";
			}
			$this->ObjectDeletelog($objsysno);
			$this->DeleteObjectassignbyObject($objsysno);
			$this->DeleteGPSMilage($objsysno);
			$this->DeleteGPSMaintenancebyObject($objsysno);
			$this->ActivityLog("ObjectDelete", $this->UserName(), $objsysno, $objsysno);
			return true;
		}
		else{
			return false;
		}
		
	}
	
	function DeleteObjectassignbyObject($sysno)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "DELETE FROM `objectassign` WHERE `objectname` = '$sysno'";
			
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			$this->ActivityLog("DeleteObjectassignbyObject", $this->UserName(), $sysno, $sysno);
			return true;
		}
		return false;
	}
	
	function DeleteObjectassignbyUser($user)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "DELETE FROM `objectassign` WHERE `username` = '$user'";
			
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			$this->ActivityLog("DeleteObjectassignbyUser", $this->UserName(), "", $user);
			return true;
		}
		return false;
	}
	
	function DeleteGroupuser($user)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "DELETE FROM `GroupsUsers` WHERE `UserName` = '$user'";
			
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			$this->ActivityLog("DeleteGroupuser", $this->UserName(), "", $user);
			return true;
		}
		return false;
	}
	
	function DeleteGPSMilage($sysno)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "DELETE FROM `gpsmilage` WHERE `sysimei` = '$sysno'";
			
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			return true;
		}
		return false;
	}
	
	function DeleteGPSMaintenancebyObject($sysno)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "DELETE FROM `gpsmaintanance` WHERE `imei` = '$sysno'";
			
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			return true;
		}
		return false;
	}
	
	function DeleteGPSMaintenancebyUser($user)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "DELETE FROM `gpsmaintanance` WHERE `user` = '$user'";
			
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			return true;
		}
		return false;
	}
	
	function DeleteGeofence($user)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		$qry = "SELECT * FROM `geofences` WHERE `geouser` = '$user'";
			
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			return true;
		}
		return false;
	}
	
	function ObjectDeletelog($objsysno)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "INSERT INTO trackingobjectslog
						   (objsysno
						   ,action
						   ,logger)
					 VALUES
						   ('".$this->SanitizeForSQL($objsysno)."'
						   ,'Delete'
						   ,'".$this->SanitizeForSQL($this->UserName())."')";
		
		$stmt = mysqli_query($this->connection,$qry);
		
		
	}
	
	function UserGroupAssigne($postvalues)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		if($this->IsGroupUser())
		{
			$qry = "UPDATE `trackingobjects` SET `subgroup`= '".$postvalues['usergroupaddgroup']."' WHERE `objsysno` = '".$postvalues['sysno']."'";
		}else{
			$qry = "UPDATE `objectassign` SET `usergroup`= '".$postvalues['usergroupaddgroup']."' WHERE `objectname` = '".$postvalues['sysno']."' AND username = '".$this->username()."'";
		}

		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			$this->ActivityLog("UserGroupAssigne", $this->UserName(), "", $postvalues);
			return "Group changed!";
		}else{
			return "<span style='color:red'> Unable to change the group!<span>";
		}
	}
	
	function UserassignedObjects()
	{
		$UserGroupeOptions = $this->UserGroupeSelectOptions();

		$sub = "";
		$subtable = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		if($this->IsGroupUser())
		{
			$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, IFNULL(E.GroupName,'Uncategorized') user_group, trackingobjects.id AS `id`, `objname` AS objectname1, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager` 
		FROM trackingobjects 
		LEFT JOIN `geofences` as B ON trackingobjects.origine = B.id 
		LEFT JOIN geofences as C ON trackingobjects.Destnation = C.id 
		LEFT JOIN `objectassign` AS D ON trackingobjects.objsysno = D.objectname
        LEFT JOIN GroupsUsers AS E ON trackingobjects.subgroup = E.Id
		WHERE objsysno IN ('".$this->assignedvehiclestring()."')
		ORDER BY user_group, objname";
		}else
		{
		$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, IFNULL(E.GroupName,'Uncategorized') user_group, trackingobjects.id AS `id`, `objname` AS objectname1, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager` 
		FROM trackingobjects 
		LEFT JOIN geofences as B ON trackingobjects.origine = B.id 
		LEFT JOIN geofences as C ON trackingobjects.Destnation = C.id 
		LEFT JOIN `objectassign` AS D ON trackingobjects.objsysno = D.objectname
        LEFT JOIN GroupsUsers AS E ON D.usergroup = E.Id
		WHERE objsysno IN ('".$this->assignedvehiclestring()."')
		AND D.username = '".$this->UserName()."'
		ORDER BY user_group, objname";
		}
		/*
		$qry = "SELECT IFNULL(E.GroupName,'Uncategorized') user_group,D.id AS AssignedID, trackingobjects.id AS `id`, `objname` AS objectname1, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager` 
		FROM trackingobjects 
		LEFT JOIN objectassign as D ON trackingobjects.objsysno = D.objectname 
        LEFT JOIN GroupsUsers AS E ON D.usergroup = E.Id
		WHERE D.username = '".$this->UserName()."' 
		ORDER BY user_group, objname";
		*/
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{

			$sub = $sub."
							<tr><form id='usergroupaddform".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'>
							<td><input type='hidden' name='sysno' value='".$row['objsysno']."'/><input type='hidden' name='AssignedID' value='".$row['AssignedID']."'/>".$row['objectname1']."</td>
							<td>".$row['user_group']."</td>
							<td><select name='usergroupaddgroup'>$UserGroupeOptions</select>&nbsp;&nbsp;<a class='btn btn-primary btn-sm' onclick='usergroupaddsubmit(".$row['id'].")' ><i class='glyphicon glyphicon-pencil icon-white'></i>Change group</a></td>
							</form></tr>";
		}
			return $sub;
	}
	
	function UserGroupeSelectOptions()
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$fencelist = "";
		if($this->IsGroupUser())
		{
			$qry = "SELECT `Id`, `GroupName`, `UserName`, `grouptype`, `StartedTime` FROM `GroupsUsers` WHERE `grouptype` = '2' AND `UserName` = '".$this->GetGroupUser()."'";
		}else{
			$qry = "SELECT `Id`, `GroupName`, `UserName`, `StartedTime` FROM `GroupsUsers` WHERE `UserName` = '".$this->UserName()."'";
		}
		

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{		
		$fencelist = $fencelist."<option value='".$row['Id']."'>".$row['GroupName']."</option>";
		}

		return $fencelist;
	}
	
	function UserGroupeTable()
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$tablecontent = "";
		if($this->IsGroupUser())
		{
			$qry = "SELECT `Id`, `GroupName`, `UserName`, `grouptype`, `StartedTime` FROM `GroupsUsers` WHERE `grouptype` = '2' AND `UserName` = '".$this->GetGroupUser()."'";
		}else
		{
			$qry = "SELECT `Id`, `GroupName`, `UserName`, `grouptype`, `StartedTime` FROM `GroupsUsers` WHERE `grouptype` = '1' AND `UserName` = '".$this->UserName()."'";
		}
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{			
			$tablecontent = $tablecontent."<tr>
			<td><form id='usergroup".$row['Id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='delusergroup' value='".$row['Id']."'/></form>".$row['GroupName']."</td>
			<td>
			<a class='btn btn-danger btn-sm' onclick='delconfirmgroup(".$row['Id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
			</td></tr>";
		}
		return $tablecontent;
	}
	
	function UserGroupAdd($GroupName)
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		if($this->IsGroupUser())
		{
			$qry = "INSERT INTO `GroupsUsers`(`GroupName`, `UserName`, `grouptype`) VALUES ('$GroupName','".$this->GetGroupUser()."','2')";
		}else{
			$qry = "INSERT INTO `GroupsUsers`(`GroupName`, `UserName`, `grouptype`) VALUES ('$GroupName','".$this->UserName()."','1')";
		}
			

		$stmt = mysqli_query($this->connection,$qry);

		if($stmt)
		{
			$this->ActivityLog("UserGroupAdd", $this->UserName(), "", "Groupname ".$GroupName);
			return "Successfully Added";
		}
		return "<span style='color:red'}>$GroupName Group not Added</span>";
	}
	
	function UserGroupDelete($GroupId)
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  

			$qry = "DELETE FROM `GroupsUsers` WHERE `Id` = '$GroupId'";

		$stmt = mysqli_query($this->connection,$qry);

		if($stmt)
		{
			$this->ActivityLog("UserGroupDelete", $this->UserName(), "", "GroupId ".$GroupId);
			return "Successfully Deleted";
		}
		return "<span style='color:red'>Not Deleted</span>";
	}
	
		
	function AdminGroupeTable()
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$tablecontent = "";
		
			$qry = "SELECT `Id`, `GroupName`, `StartedTime` FROM `GroupsAdmin` ORDER BY `GroupName`";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{			
			$tablecontent = $tablecontent."<tr>
			<td><form id='admingroup".$row['Id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='deladmingroup' value='".$row['Id']."'/></form>".$row['GroupName']."</td>
			<td>
			<a class='btn btn-danger btn-sm' onclick='delconfirmgroup(".$row['Id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
			</td></tr>";
		}
		return $tablecontent;
	}
	
	function AdminGroupAdd($GroupName)
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  

			$qry = "INSERT INTO `GroupsAdmin`( `GroupName`) VALUES ('$GroupName')";

		$stmt = mysqli_query($this->connection,$qry);

		if($stmt)
		{
			return "Successfully Added";
		}
		return "<span style='color:red'}>$GroupName Group not Added</span>";
	}
	
	function AdminGroupDelete($GroupId)
	{
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  

			$qry = "DELETE FROM `GroupsAdmin` WHERE `Id` = '$GroupId'";

		$stmt = mysqli_query($this->connection,$qry);

		if($stmt)
		{
			return "Successfully Deleted";
		}
		return "<span style='color:red'>Not Deleted</span>";
	}
	
	function TableMaintainanceRule()
	{
		$superuser = $this->issuperuser();
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$tablecontent = "";
		
			$qry = "SELECT A.`id`, A.`imei`, A.`user`, A.`subject`, A.`maxmiles`, A.`maxdays`, A.`startdate`, A.`isactive`, B.`objname` FROM `gpsmaintanance` AS A
					LEFT JOIN `trackingobjects` as B
					ON B.`objsysno` = A.`imei`
					WHERE `user` = '".$this->UserName()."' ORDER BY `startdate`";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			if($row['isactive'])
			{
				$activestatus = "ON";
			}else{
				$activestatus = "Expired";
			}
			if($row['maxmiles']=='0')
			{
				$current_milage = 0;
			}else{
				$current_milage = $this->currentmilage(date( 'Y-m-d', strtotime($row['startdate'])),$row['imei']);
			}
			
			if($row['maxdays']=='0')
			{
				$current_duration = 0;
			}else{
				$current_duration = $this->currentdurations(date( 'Y-m-d', strtotime($row['startdate'])),$row['imei']);
			}
			
			$tablecontent = $tablecontent."<tr>
			<td><form id='userload".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='user' value='".$row['id']."'/></form><form id='".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='del' value='".$row['id']."'/></form>".$row['objname']."</td>
			<td>".$row['subject']."</td>
			<td>".$row['maxmiles']."</td>
			<td>".$current_milage."</td> 
			<td>".$row['maxdays']."</td>
			<td>".$current_duration."</td>
			<td>".date( 'Y-m-d', strtotime($row['startdate']))."</td>
			<td>".$activestatus."
			</td>
			<td>
			<a class='btn btn-primary btn-sm' onclick='userload(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Clone</a> 
			<a class='btn btn-danger btn-sm' onclick='delconfirm(".$row['id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
			</td></tr>";
		}
		return $tablecontent;
	}
	
	function currentmilage($date,$imei)
	{
		$qry = "SELECT SUM(`milage`) AS totalmilage FROM `gpsmilage` WHERE `sysimei` = '$imei' AND `Date` > '$date'";
		
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			return round($row['totalmilage']/1000,0);
		}
	}
	
	function currentdurations($date,$imei)
	{
		$now = time(); // or your date as well
		$your_date = strtotime($date);
		$datediff = $now - $your_date;
		return round($datediff / (60 * 60 * 24));
	}
	
	function loadtoDBNotificationRule($PostValus)
	{
		$IDavailable = false;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "SELECT `id`, `imei`, `user`, `subject`, `maxmiles`, `maxdays`, `startdate`, `isactive` FROM `gpsmaintanance` WHERE `id` = '".$PostValus['id']."' AND `user` = '".$this->UserName()."'";
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$IDavailable = true;
		}
		
		if($IDavailable)
		{
			return $this->UpdateNotificationRule($PostValus);
		}else{
			return $this->InsertNotificationRule($PostValus);
		}
	}
	
	function InsertNotificationRule($PostValus)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "INSERT INTO `gpsmaintanance`(`imei`, `user`, `subject`, `maxmiles`, `maxdays`, `startdate`, `isactive`) VALUES ('".$PostValus['imei']."','".$this->UserName()."','".$PostValus['subject']."','".$PostValus['maxmiles']."','".$PostValus['maxdays']."','".$PostValus['startdate']."','1')";
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			return "New notification Insert!";
		}else{
			return "Notification Not insert!";
		}
	}
	
	function UpdateNotificationRule($PostValus)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "UPDATE `gpsmaintanance` SET `imei`='".$PostValus['imei']."',`user`='".$this->UserName()."',`subject`='".$PostValus['subject']."',`maxmiles`='".$PostValus['maxmiles']."',`maxdays`='".$PostValus['maxdays']."',`startdate`='".$PostValus['startdate']."', `isactive`='1' WHERE `id` = '".$PostValus['id']."'";
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			return "Notification updated!";
		}else{
			return "Notification Not updated!";
		}
	}
	
	function NotificDelete($PostValus)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		//{"592":"592","591":"591","589":"589","590":"590"}
		$NotificIdsString = "";
		$result = json_decode($PostValus['id'], true);
		//print_r($result);
		foreach($result as $NotificIds)
		{
			if($NotificIdsString == "")
			{
				$NotificIdsString = $NotificIds;
			}else{
				$NotificIdsString = $NotificIdsString.",".$NotificIds;
			}
		}
		
		$qry = "DELETE FROM `gpsmntcenotify` WHERE `id` in (".$NotificIdsString.")";
		//echo $qry;
		//exit;
		$stmt = mysqli_query($this->connection,$qry);
		if($stmt)
		{
			return true;
		}else{
			return false;
		}
	}
	
	function LoadMaintainanceRule($id)
	{
		$tablecontent = "";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "SELECT `id`, `imei`, `user`, `subject`, `maxmiles`, `maxdays`, `startdate`, `isactive` FROM `gpsmaintanance` WHERE `id` = '$id' AND `user` = '".$this->UserName()."'";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$qryresult['id'] = $row['id'];
			$qryresult['imei'] = $row['imei'];
			$qryresult['user'] = $row['user'];
			$qryresult['subject'] = $row['subject'];
			$qryresult['maxmiles'] = $row['maxmiles'];
			$qryresult['maxdays'] = $row['maxdays'];
			$qryresult['startdate'] = $row['startdate'];
			$qryresult['isactive'] = $row['isactive'];
		}
		return $qryresult;
		
	}
	
	function MaintananceRuleDelete($postvalue)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "DELETE FROM `gpsmaintanance` WHERE `id` = '$postvalue'";

		$stmt = mysqli_query($this->connection,$qry);
		
		if($stmt)
		{
			return true;
		}
		else{
			return false;
		}
		
	}
	

	function TableNotificationsRule()
	{
		$superuser = $this->issuperuser();
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		$tablecontent = "";
		$typecontent = "";
		
			$qry = "SELECT A.id, C.objname, B.geo_name, `type`, `imei`, `gfid`, `user`, `neme`, `maxspeed`, `minspeed`, `maxparking`, `isactive` FROM `geofencecrossrule` AS A
					LEFT JOIN geofences AS B
					ON A.gfid = B.id
					LEFT JOIN trackingobjects AS C
					ON C.objsysno = A.imei
					WHERE `user` = '".$this->UserName()."'
					ORDER BY `type`";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			if($row['type'] == "1")
			{
				$typecontent = "<td>Zonal notification</td>
								<td>".$row['geo_name']."</td>";
			}
			if($row['type'] == "2")
			{
				$typecontent = "<td>Hi/Low speed notification</td>
								<td>Hi-".$row['maxspeed']." Low-".$row['minspeed']."</td>";
			}
			if($row['type'] == "3")
			{
				$typecontent = "<td>Movement notification</td>
								<td></td>";
			}
			if($row['type'] == "4")
			{
				$typecontent = "<td>Engine status change notification</td>
								<td></td>";
			}
			if($row['type'] == "5")
			{
				$typecontent = "<td>Max parking notification</td>
								<td>".$row['maxparking']." min</td>";
			}
			
			$tablecontent = $tablecontent."<tr>
			<td><form id='".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='del' value='".$row['id']."'/></form>".$row['objname']."</td>
			<td>".$row['neme']."</td>
			$typecontent
			<td>
			<a class='btn btn-danger btn-sm' onclick='delconfirm(".$row['id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
			</td></tr>";
		}
		return $tablecontent;
	}	
	
	function NotificationsRuleDelete($postvalue)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		$qry = "DELETE FROM `geofencecrossrule` WHERE `id` = '$postvalue'";

		$stmt = mysqli_query($this->connection,$qry);
		
		if($stmt)
		{
			return true;
		}
		else{
			return false;
		}
		
	}
	
	
	function NotificationsRuleInseart($values)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		if($values['type']==1)
		{
			$qry = "INSERT INTO `geofencecrossrule`( `type`, `imei`, `gfid`, `user`, `neme`, `maxspeed`, `minspeed`, `maxparking`, `isactive`) VALUES (1,'".$values['imei']."','".$values['Zone']."','".$this->UserName()."','".$values['name']."',0,0,0,1)";
		}
		if($values['type']==2)
		{
			$qry = "INSERT INTO `geofencecrossrule`( `type`, `imei`, `gfid`, `user`, `neme`, `maxspeed`, `minspeed`, `maxparking`, `isactive`) VALUES (2,'".$values['imei']."','0','".$this->UserName()."','".$values['name']."','".$values['maxspeed']."','".$values['minspeed']."',0,1)";
		}
		if($values['type']==3)
		{
			$qry = "INSERT INTO `geofencecrossrule`( `type`, `imei`, `gfid`, `user`, `neme`, `maxspeed`, `minspeed`, `maxparking`, `isactive`) VALUES (3,'".$values['imei']."','0','".$this->UserName()."','".$values['name']."',0,0,0,1)";
		}
		if($values['type']==4)
		{
			$qry = "INSERT INTO `geofencecrossrule`( `type`, `imei`, `gfid`, `user`, `neme`, `maxspeed`, `minspeed`, `maxparking`, `isactive`) VALUES (4,'".$values['imei']."','0','".$this->UserName()."','".$values['name']."',0,0,0,1)";
		}
		if($values['type']==5)
		{
			$qry = "INSERT INTO `geofencecrossrule`( `type`, `imei`, `gfid`, `user`, `neme`, `maxspeed`, `minspeed`, `maxparking`, `isactive`) VALUES (5,'".$values['imei']."','0','".$this->UserName()."','".$values['name']."',0,0,'".$values['maxpark']."',1)";
		}
		
		$stmt = mysqli_query($this->connection,$qry);
		
		if($stmt)
		{
			return "<p style='color:green;'>Notification rule inserted!</p>";
		}
		return "<p style='color:red;'> Notification rule not inserted!</p>";
	}
	//////////////////////////////////////	Object Settings Stopped	////////////////////////////
	
////////////////////////////////////////////// Notifications  /////////////////////////////////////	
	
	function Notifications_Small()
	{
		$Notifystr = "";
		$style = "<i class='fa fa-bell-o'></i>";
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry = "SELECT C.username AS `assignuser`, B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, DATE_ADD(`notifydate`, INTERVAL ".$this->UserTimeZone()." MINUTE) AS notifydatetz
				FROM `gpsmntcenotify` AS A 
				LEFT JOIN trackingobjects AS B 
				ON A.imei = B.objsysno 
				INNER JOIN objectassign AS C 
				ON A.imei = C.objectname 
				WHERE C.username = '".$this->UserName()."' 
				AND (`user` = '".$this->UserName()."' OR `user` = '') 
				AND DATE_ADD(`notifydate` , INTERVAL 1 DAY) > NOW() 
				ORDER BY `notifydate` DESC LIMIT 5";
				
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			if($row['priority']=='3')
			{
				$calssname = "label label-success";
			}
			if($row['priority']=='2')
			{
				$calssname = "label label-warning";
			}
			if($row['priority']=='1')
			{
				$calssname = "label label-danger";
			}
			
			$date = $this->UserDateFormat($row['notifydatetz']);
			if($row['isshow']=='0')
			{
				$style = "<i class='fa fa-bell-o' style='color:red'></i>";
				$msgbody = "<b>".$row['vehicleNo']." </b><small class='pull-right'><b>$date</b></small>";
			}
			else{
				$msgbody = $row['vehicleNo']." <small class='pull-right'>$date</small>";
			}
			
			$Notifystr = $Notifystr."<li><a href='notifications.php'><span class='$calssname'>".$row['subject']."</span> ".$msgbody." at ".$this->UserTimeFormat($row['notifydatetz'])." </a></li>";
			
		}
		$Notifystr = $Notifystr."<li class='summary'><a href='notifications.php'>See all notifications</a></li>";
		return array('main'=>$Notifystr,'style'=>$style);

	}
	

	function NotificationsJSON()
	{
		$notificinfo = array();
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry = "SELECT C.username AS `assignuser`, B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, `PicPath`, DATE_ADD(`notifydate`, INTERVAL ".$this->UserTimeZone()." MINUTE) AS notifydatetz FROM `gpsmntcenotify` AS A LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno INNER JOIN objectassign AS C ON A.imei = C.objectname WHERE C.username = '".$this->UserName()."' AND (`user` = '".$this->UserName()."' OR `user` = '') AND DATE_ADD(`notifydate` , INTERVAL 30 DAY) > NOW() ORDER BY `notifydate` DESC";
		//$qry = "SELECT B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, `notifydate` FROM `gpsmntcenotify` AS A LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno WHERE `user` = '".$this->UserName()."' OR `user` = '' ORDER BY `notifydate` DESC LIMIT 100";
		//echo $qry;
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$date = $this->UserDateFormat($row['notifydatetz']);
			$notificinfo[] = array('id'=>$row['id'],'notificTime'=>$this->UserTimeFormat($row['notifydatetz']),'notificname'=>$row['vehicleNo'],'notificsubject'=>$row['subject'],'notificaddress'=>$row['msgbody']." at ".$this->UserTimeFormat($row['notifydatetz']),'priority'=>$row['priority'],'isshow'=>$row['isshow'],'notificlatitudes'=>0,'notificlogitude'=>0, 'PicPath'=>$row['PicPath']);
			
		}
		return json_encode(array('notificinfo'=>$notificinfo));
	}
	
	function Notifications()
	{
		$NotifystrSub = "";
		$NotifystrMain = "";
		$active = "";
		$count = 0;
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 

		$qry = "SELECT C.username AS `assignuser`, B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, DATE_ADD(`notifydate`, INTERVAL ".$this->UserTimeZone()." MINUTE) AS notifydatetz, `PicPath` FROM `gpsmntcenotify` AS A LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno INNER JOIN objectassign AS C ON A.imei = C.objectname WHERE C.username = '".$this->UserName()."' AND (`user` = '".$this->UserName()."' OR `user` = '') AND DATE_ADD(`notifydate` , INTERVAL 3 DAY) > NOW() ORDER BY `notifydate` DESC";
		//$qry = "SELECT B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, `notifydate` FROM `gpsmntcenotify` AS A LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno WHERE `user` = '".$this->UserName()."' OR `user` = '' ORDER BY `notifydate` DESC LIMIT 100";

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$count++;
			if($row['priority']=='3')
			{
				$calssname = "label label-success";
			}
			if($row['priority']=='2')
			{
				$calssname = "label label-warning";
			}
			if($row['priority']=='1')
			{
				$calssname = "label label-danger";
			}
			
			$date = $this->UserDateFormat($row['notifydatetz']);
			$msgbody = $row['msgbody'];
			
			if($row['isshow']=='0')
			{
				$date = "<span style='font-weight: bold;' id='isread".$row['id']."'>".$date."</span>";
			}else{
				$date = "<span style='font-weight: normal;' id='isread".$row['id']."'>".$date."</span>";
			}
			
			if($count == 1)
			{
				 $active = "active";
			}else{
				$active = "";
			}
			
			
			
			//$NotifystrSub = $NotifystrSub."<li><a><span class='$calssname'>".$row['subject']."</span> <b>".$row['vehicleNo']."</b> <br>".$msgbody." <br>".$date." </a></li>";
			
                    $NotifystrSub = $NotifystrSub."<div class='panel-body note-link'>
														<a href='#".$row['id']."' data-toggle='tab'>
														<small class='pull-right text-muted'>$date</small>
														<h5>
															".$row['vehicleNo']."
														</h5>
														<span class='$calssname'>".$row['subject']."</span>
															</a>
													</div>";
			
					$NotifystrMain = $NotifystrMain."<div id='".$row['id']."' class='tab-pane $active'>
														<div class='pull-right text-muted m-l-lg'>
															$date
														</div>
														<h4>".$row['vehicleNo']."</h4><h5>".$row['subject']." at ".$this->UserTimeFormat($row['notifydatetz'])."</h5>
														<hr/>
														<div class='note-content'>
															<p>".$msgbody." at ".$this->UserTimeFormat($row['notifydatetz'])."</p>
															<img class='img-responsive m-t-md' src=".$row['PicPath']." >
														</div>
													</div>";
		}
		return array('sub'=>$NotifystrSub,'main'=>$NotifystrMain);
	}
	
	function notifyasread($values)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }

        $qry = "UPDATE `gpsmntcenotify` SET `isshow`='1' WHERE `id` = '$values'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result)
        {
            return 0;
        }
			return 1;
	}
	
////////////////////////////////////////////// Notifications End  /////////////////////////////////////	
	function assignedvehiclestringNormalUser()
	{
		$objectnoarray = array();
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		if($this->IsGroupUser())
		{
			$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
			LEFT JOIN GroupsAdmin AS E ON a.admin_group = E.Id
			WHERE E.id = '".$this->GetGroupUser()."'
			ORDER BY objname";
		}else{
			$qry = "SELECT * , b.objname as objectname1 FROM objectassign as a 
			LEFT JOIN trackingobjects as b 
			ON b.objsysno = a.objectname 
			WHERE username = '".$this->UserName()."'";
		}

		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$objectnoarray[] = $row['objsysno'];
		}
		$ids = join("','",$objectnoarray);  
		return $ids;
		//objsysno IN ('$ids')
	}
	
	function assignedvehiclestring()
	{
		$objectnoarray = array();
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		if($this->IsAdmin()){
			$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
			WHERE HeadUser = '".$this->headuser."' 
			ORDER BY objname";
		}else{
				if($this->IsGroupUser())
				{
					$qry = "SELECT * , a.objname as objectname1 FROM  trackingobjects as a 
					LEFT JOIN GroupsAdmin AS E ON a.admin_group = E.Id
					WHERE E.id = '".$this->GetGroupUser()."'
					ORDER BY objname";
				}else{
					$qry = "SELECT * , b.objname as objectname1 FROM objectassign as a 
					LEFT JOIN trackingobjects as b 
					ON b.objsysno = a.objectname 
					WHERE username = '".$this->UserName()."'";
				}
		}
		$stmt = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$objectnoarray[] = $row['objsysno'];
		}
		$ids = join("','",$objectnoarray);  
		return $ids;
		//objsysno IN ('$ids')
	}

	function issuperuser()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }

        $qry = "SELECT  `username`, `HeadUser`, `Privilage`, `supuser` FROM `gpsusers` WHERE HeadUser = '".$this->headuser."' AND username = '".$this->UserName()."' AND `Privilage`='admin'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Admin Checking Error!");
            return false;
        }
        $row = mysqli_fetch_assoc($result);
		if($row['supuser']==1){
			return true;
		}else{
			return false;
		}
	}
		
	function UserDelete($username){
		if(!$this->isAdminfullrights())
		{
			return false;
		}
		
		if(!$this->DBLogin())
        {
            echo "Not connected";
            return false;
        }  
		
		$qry = "DELETE FROM `gpsusers` WHERE `username` = '$username'";

		$stmt = mysqli_query($this->connection,$qry);
		
		if($stmt)
		{
			$this->DeleteObjectassignbyUser($username);
			$this->DeleteGroupuser($username);
			$this->DeleteGPSMaintenancebyUser($username);
			$this->DeleteGeofence($username);
			$this->ActivityLog("UserDelete", $username, "","");
			return true;
		}
		else{
			return false;
		}
		
	}
	
    function SetAdminEmail($email)
    {
        $this->admin_email = $email;
    }
    
	function GetImeino($Busno)
	{
	if(!$this->DBLogin())
	{
		$this->HandleError("Database login failed!");
		return false;
	} 
	
	$Busno = trim($Busno);
	$qry = "SELECT objname,objsysno
	  FROM trackingobjects
	  WHERE objname = '$Busno'";
	  $stmt = mysqli_query($this->connection,$qry);
  
  	while($row = mysqli_fetch_array($stmt))
	{
		return $row['objsysno'];
	}
		return false;
	}

    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }
    
    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }
    
    //-------Main Operations ----------------------
    function RegisterUser()
    {
        if(!isset($_POST['submitted']))
        {
           return false;
        }
		$_POST['username'] = trim($_POST['username']);
        $formvars = array();
        
        if(!$this->ValidateRegistrationSubmission())
        {
            return false;
        }

        $this->CollectRegistrationSubmission($formvars);
        
        if(!$this->SaveToDatabase($formvars))
        {
            return false;
        }
        /*
        if(!$this->SendUserConfirmationEmail($formvars))
        {
            return false;
        }
		*/
        $this->SendAdminIntimationEmail($formvars);
        
        return true;
    }

    function ConfirmUser()
    {
        if(empty($_GET['code'])||strlen($_GET['code'])<=10)
        {
            $this->HandleError("Please provide the confirm code");
            return false;
        }
        $user_rec = array();
        if(!$this->UpdateDBRecForConfirmation($user_rec))
        {
            return false;
        }
        
        $this->SendUserWelcomeEmail($user_rec);
        
        $this->SendAdminIntimationOnRegComplete($user_rec);
        
        return true;
    }    

    function SystemLogin($user,$pass)
    {
        if(empty($user))
        {
            $this->HandleError("UserName is empty!");
            return false;
        }
        
        if(empty($pass))
        {
            $this->HandleError("Password is empty!");
            return false;
        }
        
        $username = trim($user);
        $password = trim($pass);
        
        if(!isset($_SESSION)){ session_start(); }
        if(!$this->CheckLoginInDB($username,$password))
        {
            return false;
        }
        
        $_SESSION[$this->GetLoginSessionVar()] = $username;
        $_SESSION['username'] = $username;
        return true;
    }
 
    function TokenLogin()
    {

        $Token = trim($_GET['Token']);
        $TokenUserDetails = $this->getSessionUser($Token);
		if(!$TokenUserDetails)
		{
			return false;
		}
		
        if(!isset($_SESSION)){ session_start(); }

        $_SESSION[$this->GetLoginSessionVar()] = $TokenUserDetails['user'];
        $_SESSION['username'] = $TokenUserDetails['user'];
		$_SESSION['token'] = $Token;
		$_SESSION['timeZone'] = $TokenUserDetails['timeZone'];
		$_SESSION['unit'] = $TokenUserDetails['unit'];
		
		$this->ActivityLog("LoginToken", $username, "","");
        return $LoginSessionVar;
    }
	
    function Login()
    {
		$Token = $this->GetNewToken();
        if(empty($_POST['username']))
        {
            $this->HandleError("UserName is empty!");
            return false;
        }
        
        if(empty($_POST['password']))
        {
            $this->HandleError("Password is empty!");
            return false;
        }
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if(!isset($_SESSION)){ session_start(); }
        if(!$this->CheckLoginInDB($username,$password))
        {
            return false;
        }
        
        $_SESSION[$this->GetLoginSessionVar()] = $username;
        $_SESSION['username'] = $username;
		$_SESSION['token'] = $Token;
		$this->AddSession($username,$Token);

		$TokenUserDetails = $this->getSessionUser($Token);
		

		
		$_SESSION['timeZone'] = $TokenUserDetails['timeZone'];
		$_SESSION['unit'] = $TokenUserDetails['unit'];
		$_SESSION['timeFormat'] = $TokenUserDetails['timeFormat'];
		$_SESSION['dayLightEnable'] = $TokenUserDetails['dayLightEnable'];
		$_SESSION['dayLightStart'] = $TokenUserDetails['dayLightStart'];
		$_SESSION['dayLightEnd'] = $TokenUserDetails['dayLightEnd'];
		//print_r($_SESSION);
		//exit;
		$this->ActivityLog("Login", $username, "","");
        return $Token;
    }
	
	function LoginWithToken()
	{
		$Token = $_GET['Token'];
		$TokenUserDetails = $this->getSessionUser($Token);
		if(!$TokenUserDetails)
		{
			return false;
		}
		$username = $TokenUserDetails['username'];
        $_SESSION[$this->GetLoginSessionVar()] = $username;
        $_SESSION['username'] = $username;
		$_SESSION['token'] = $Token;
		$_SESSION['timeZone'] = $TokenUserDetails['timeZone'];
		$_SESSION['unit'] = $TokenUserDetails['unit'];
		$_SESSION['timeFormat'] = $TokenUserDetails['timeFormat'];
		$_SESSION['dayLightEnable'] = $TokenUserDetails['dayLightEnable'];
		$_SESSION['dayLightStart'] = $TokenUserDetails['dayLightStart'];
		$_SESSION['dayLightEnd'] = $TokenUserDetails['dayLightEnd'];
		
		$this->ActivityLog("LoginWithToken", $username, "","");
		return true;
	}
	
	function getSessionUser($Token)
	{
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          

		$qry = "SELECT `id`, `Createdate`, `user`, `Token`, `LastUpdate`, `name`, `email`, `username`, `password`, `phone`, `company`, `timeZone`,`timeFormat`, `unit`, `confirmcode`, `HeadUser`, `Privilage`, `admingroup`, `supuser`, `LastTracks`, `dayLightEnable`, `dayLightStart`, `dayLightEnd`
		FROM `gpsUserSessions` 
		LEFT JOIN `gpsusers`
		ON `gpsUserSessions`.`user` = `gpsusers`.`username`
		WHERE `confirmcode` = 'y'
		AND `Token` = '$Token'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("No token available");
            return false;
        }
        $row = mysqli_fetch_assoc($result);
        return $row;
	}
    
    function CheckLogin()
    {
         if(!isset($_SESSION)){ session_start(); }

         $sessionvar = $this->GetLoginSessionVar();
         
         if(empty($_SESSION[$sessionvar]))
         {
            return false;
         }
         return true;
    }
    
	function LoginbyFB($email)
	{
		
		$username = $this->EmailtoUsername($email);
		echo $username;
		if($username == "")
		{
			return false;
			//$this->RedirectToformURL('Please register gsupertrack.com',"service.gsupertrack.com",'formrespond.php');
			//exit;
		}
        
        if(!isset($_SESSION)){ session_start(); }
        if(!$this->FB_CheckLoginInDB($username))
        {
            return false;
        }
        
        $_SESSION[$this->GetLoginSessionVar()] = $username;
        $_SESSION['username'] = $username;
        return true;
	}

	function AddSession($user,$token)
	{
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }  
		$qry = "INSERT INTO `gpsUserSessions`(`user`, `Token`) VALUES ('".$user."','".$token."')";
		//echo $qry;
		//exit;
		$result = mysqli_query($this->connection,$qry);
		if($reset)
		{return true;}
		else
		{return false;}
	}
	
	function EmailtoUsername($email)
	{
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }  
        $qry = "SELECT `id_user`, `name`, `email`, `phone_number`, `username`, `password`, `phone`, `company`, `confirmcode`, `HeadUser`, `Privilage` FROM `gpsusers` WHERE fb_mail = '".$email."'";

        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Empty Email Address");
            return "";
        }
        $row = mysqli_fetch_assoc($result);
        return $row['username'];
	}

	function UserTimeFormat($DateTime)
	{
		if(isset($_SESSION))
		{
			if($_SESSION['timeFormat']==1)
			{
				$phptime = strtotime($DateTime);
				$Time = date("d/m/y H:i:s", $phptime);
				return $Time;
			}
			if($_SESSION['timeFormat']==0)
			{
				$phptime = strtotime($DateTime);
				$Time = date("Y-m-d H:i:s", $phptime);
				return $Time;
			}
		}
		else{
			return $DateTime;
		}
	}
	
	function UserDateFormat($Date)
	{
		if(isset($_SESSION))
		{
			if($_SESSION['timeFormat']==1)
			{
				$phptime = strtotime($Date);
				$Time = date("d/m/y", $phptime);
				return $Time;
			}
			if($_SESSION['timeFormat']==0)
			{
				$phptime = strtotime($Date);
				$Time = date("Y-m-d", $phptime);
				return $Time;
			}
		}
		else{
			return $Date;
		}
	}
	
	function UserToken()
	{
		if(isset($_SESSION))
		{
			return $_SESSION['token'];
		}
		return null;
	}

	function UserTimeZone()
	{
		if(isset($_SESSION))
		{
			$CurrentDate = strtotime("now");
			$contractDateBegin = strtotime($_SESSION['dayLightStart']);
			$contractDateEnd = strtotime($_SESSION['dayLightEnd']);

			if($CurrentDate > $contractDateBegin && $CurrentDate < $contractDateEnd) {
			   $timeZone = $_SESSION['timeZone'] + 60;
			   return $timeZone;
			} 
			return $_SESSION['timeZone'];
		}
		return null;
	}

	function UserUnit($value)
	{
		$result = null;
		if(isset($_SESSION))
		{
			if($_SESSION['unit']==1)
			{
				$value = $value * 0.621371;
				$value = round($value);
				$result = array('value'=>$value,'valueUnitLength'=>$value." Miles",'valueUnitSpeed'=>$value." Mph",'lengthUnit'=>'Miles','speedUnit'=>'Mph');
			}
			if($_SESSION['unit']==0)
			{
				$value = round($value);
				$result = array('value'=>$value,'valueUnitLength'=>$value." km",'valueUnitSpeed'=>$value." kmph",'lengthUnit'=>'km','speedUnit'=>'kmph');
			}
		}
		return $result;
	}
	
	function gettimezone($sysno)
	{
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          
		$timeZone = 0;
        $qry = "SELECT `objsysno`,`tz` FROM `trackingobjects` WHERE `objsysno` = '$sysno'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Empty Email Address");
            return "";
        }
        $row = mysqli_fetch_assoc($result);
		$timeZone = $row['tz'];
		
		$CurrentDate = strtotime("now");
		$contractDateBegin = strtotime($_SESSION['dayLightStart']);
		$contractDateEnd = strtotime($_SESSION['dayLightEnd']);

		if($CurrentDate > $contractDateBegin && $CurrentDate < $contractDateEnd) {
		   //$timeZone = $timeZone + 60;
		} 
		$timeZone = $timeZone + 60;
        return $timeZone;
	}
	
    function UserFullName()
    {
        return isset($_SESSION['name_of_user'])?$_SESSION['name_of_user']:'';
    }
	
    function UserName()
    {
        //return isset($_SESSION['name_of_user'])?$this->username:'';
		return $_SESSION['username'];
    }
	
    function UserEmail()
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          

        $qry = "SELECT `id_user`, `name`, `email`, `phone_number`, `username`, `password`, `phone`, `company`, `confirmcode`, `HeadUser`, `Privilage` FROM `gpsusers` WHERE HeadUser = '".$this->headuser."' AND username = '".$this->UserName()."'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Empty Email Address");
            return "";
        }
        $row = mysqli_fetch_assoc($result);
        return $row['email'];
    }
	
	function phone()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          

        $qry = "SELECT `id_user`, `name`, `email`, `phone_number`, `username`, `password`, `phone`, `company`, `confirmcode`, `HeadUser`, `Privilage` FROM `gpsusers` WHERE HeadUser = '".$this->headuser."' AND username = '".$this->UserName()."'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Empty Phone Number");
            return "";
        }
        $row = mysqli_fetch_assoc($result);
        return $row['phone'];
	}
	
	function company()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          

        $qry = "SELECT `id_user`, `name`, `email`, `phone_number`, `username`, `password`, `phone`, `company`, `confirmcode`, `HeadUser`, `Privilage` FROM `gpsusers` WHERE HeadUser = '".$this->headuser."' AND username = '".$this->UserName()."'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Empty Company Name");
            return "";
        }
        $row = mysqli_fetch_assoc($result);
        return $row['company'];	
	}
	
	function CheckAdmin()
	{
		return $this->IsAdmin();
	}
	
	function profileupdate($profilevariables)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }  
		
		$qry = "UPDATE `gpsusers` SET `name`= '".$this->Sanitize($profilevariables['name']) ."',`email`= '".$this->Sanitize($profilevariables['email']) ."',`phone`= '".$this->Sanitize($profilevariables['phone']) ."',`company`= '".$this->Sanitize($profilevariables['company']) ."' WHERE username = '".$this->UserName()."'";
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result)
        {
            return "Details Not Updated";
        }
		return "Succesfully Updated";
	}
    
    function LogOut()
    {
        session_start();
        
		$_SESSION['username'] = NULL;
		
        $sessionvar = $this->GetLoginSessionVar();
        
        $_SESSION[$sessionvar]=NULL;
        
		
        unset($_SESSION[$sessionvar]);
    }
    
    function EmailResetPasswordLink()
    {
        if(empty($_POST['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        $user_rec = array();
        if(false === $this->GetUserFromEmail($_POST['email'], $user_rec))
        {
            return false;
        }
        if(false === $this->SendResetPasswordLink($user_rec))
        {
            return false;
        }
        return true;
    }

    function ResetPasswordAdmin($userName)
    {
		$newpwd = substr(md5(uniqid()),0,10);
		
        $newpwd = $this->SanitizeForSQL($newpwd);
        
        $qry = "Update $this->tablename Set password='".md5($newpwd)."' Where  id_user='".$userName."'";
        //echo $qry;
		//exit;
        if(!mysqli_query($this->connection,$qry))
        {
            $this->HandleDBError("Error updating the password \n");
            return false;
        }     
        return $newpwd;
	}
    
    function ResetPassword()
    {
        if(empty($_GET['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        if(empty($_GET['code']))
        {
            $this->HandleError("reset code is empty!");
            return false;
        }
        $email = trim($_GET['email']);
        $code = trim($_GET['code']);
        
        if($this->GetResetPasswordCode($email) != $code)
        {
            $this->HandleError("Bad reset code!");
            return false;
        }
        
        $user_rec = array();
        if(!$this->GetUserFromEmail($email,$user_rec))
        {
            return false;
        }
        
        $new_password = $this->ResetUserPasswordInDB($user_rec);
        if(false === $new_password || empty($new_password))
        {
            $this->HandleError("Error updating new password");
            return false;
        }
        
        if(false == $this->SendNewPassword($user_rec,$new_password))
        {
            $this->HandleError("Error sending new password");
            return false;
        }
        return true;
    }
    
    function ChangePassword()
    {
        if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
            return false;
        }
        
        if(empty($_POST['oldpwd']))
        {
            $this->HandleError("Old password is empty!");
            return false;
        }
        if(empty($_POST['newpwd']))
        {
            $this->HandleError("New password is empty!");
            return false;
        }
        
        $user_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$user_rec))
        {
            return false;
        }
        
        $pwd = trim($_POST['oldpwd']);
        
        if($user_rec['password'] != md5($pwd))
        {
            $this->HandleError("The old password does not match!");
            return false;
        }
        $newpwd = trim($_POST['newpwd']);
        
        if(!$this->ChangePasswordInDB($user_rec, $newpwd))
        {
            return false;
        }
        return true;
    }
    
    //-------Public Helper functions -------------
    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }    
    
    function SafeDisplay($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }
    
    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }
	
    function RedirectToformURL($massage,$backurl,$url)
    {
        header("Location: $url?message=$massage&backurl=$backurl");
        exit;
    }
    
    function GetSpamTrapInputName()
    {
        return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
    }
    
    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }    
    //-------Private Helper functions-----------
    
    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }
    
    function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysqli_error());
    }
    
    function GetFromAddress()
    {
        if(!empty($this->from_address))
        {
            return $this->from_address;
        }

        $host = $_SERVER['SERVER_NAME'];

        $from ="nobody@$host";
        return $from;
    } 

    function GetNewToken()
    {
        $retvar = md5(uniqid(rand(), true));
        $retvar = 'gps22_'.substr($retvar,0,25);
        return $retvar;
    }
	
    function GetLoginSessionVar()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,25);
        return $retvar;
    }
    
    function CheckLoginInDB($username,$password)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          
        $username = $this->SanitizeForSQL($username);
        $pwdmd5 = md5($password);
        $qry = "Select name, email from $this->tablename where username='$username' and password='$pwdmd5' and confirmcode='y' AND HeadUser = '".$this->headuser."'";
        
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Error logging in. The username or password does not match");
            return false;
        }
        
        $row = mysqli_fetch_assoc($result);
        
        
        $_SESSION['name_of_user']  = $row['name'];
        $_SESSION['email_of_user'] = $row['email'];
        
        return true;
    }
    
    function FB_CheckLoginInDB($username)
    {
        if(!$this->DBLogin())
        {
            echo "Database login failed!";
            return false;
        }          
        $username = $this->SanitizeForSQL($username);

        $qry = "Select name, email from $this->tablename where username='$username' and confirmcode='y'";

        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Error logging in. The username or password does not match");
            return false;
        }
        
        $row = mysqli_fetch_assoc($result);
        
        
        $_SESSION['name_of_user']  = $row['name'];
        $_SESSION['email_of_user'] = $row['email'];
        
        return true;
    }
	
    function UpdateDBRecForConfirmation(&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $confirmcode = $this->SanitizeForSQL($_GET['code']);
        
        $result = mysqli_query($this->connection,"Select name, email from $this->tablename where confirmcode='$confirmcode'");   
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Wrong confirm code.");
            return false;
        }
        $row = mysqli_fetch_assoc($result);
        $user_rec['name'] = $row['name'];
        $user_rec['email']= $row['email'];
        
        $qry = "Update $this->tablename Set confirmcode='Y' Where  confirmcode='$confirmcode'";
        
        if(!mysqli_query($this->connection,$qry))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }      
        return true;
    }
    
    function ResetUserPasswordInDB($user_rec)
    {
        $new_password = substr(md5(uniqid()),0,10);
        
        if(false == $this->ChangePasswordInDB($user_rec,$new_password))
        {
            return false;
        }
        return $new_password;
    }
    
    function ChangePasswordInDB($user_rec, $newpwd)
    {
        $newpwd = $this->SanitizeForSQL($newpwd);
        
        $qry = "Update $this->tablename Set password='".md5($newpwd)."' Where  id_user=".$user_rec['id_user']."";
        
        if(!mysqli_query($this->connection,$qry))
        {
            $this->HandleDBError("Error updating the password \nquery:$qry");
            return false;
        }     
        return true;
    }
    
    function ChangePasswordInDBFrmUser($username, $newpwd)
    {
		
        //$newpwd = $this->SanitizeForSQL($newpwd);
        
        $qry = "Update $this->tablename Set password='".md5($newpwd)."' Where  username='".$username."'";
        
        if(!mysqli_query($this->connection,$qry))
        {
            $this->HandleDBError("Error updating the password !");
            return false;
        }     
		
        return true;
    }

	
    function TrackIDValidate($username, $TrackID)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $url = "SELECT `id`, `VehicleName`, `objname`, `objsysno`,`manager` FROM `trackingobjects` WHERE `manager` != '".$username."' AND `objsysno` = '".$TrackID."'";
        $result = mysqli_query($this->connection,$url);  

        if(!$result || mysqli_num_rows($result) <= 0)
        {
            //$this->HandleError("There is no user with email: $email");
            return true;
        }
    return false;
	}
	
    function GetUserFromEmail($email,&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $email = $this->SanitizeForSQL($email);
        
        $result = mysqli_query($this->connection,"Select * from $this->tablename where email='$email'");  

        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("There is no user with email: $email");
            return false;
        }
        $user_rec = mysqli_fetch_assoc($result);

        
        return true;
    }
    
    function SendUserWelcomeEmail(&$user_rec)
    {
        $Subject = "Welcome to ".$this->sitename;

        $Body ="Hello ".$user_rec['name']."<br>".
        "Welcome! Your registration  with ".$this->sitename." is completed.<br>";

        if(!$this->Sendemail($user_rec['email'],$Subject,$Body))
        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
    }
    
    function SendAdminIntimationOnRegComplete(&$user_rec)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $Subject = "Registration Completed: ".$user_rec['name'];       
        $Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$user_rec['name']."\r\n".
        "Email address: ".$user_rec['email']."\r\n";
        
        if(!$this->Sendemail($this->admin_email,$Subject,$Body))
        {
            return false;
        }
        return true;
    }
    
    function GetResetPasswordCode($email)
    {
       return substr(md5($email.$this->sitename.$this->rand_key),0,10);
    }
    
    function SendResetPasswordLink($user_rec)
    {
        $email = $user_rec['email'];

        $Subject = "Your reset password request at ".$this->sitename;
        
        $link = $this->GetAbsoluteURLFolder().
                '/resetpwd.php?email='.
                urlencode($email).'&code='.
                urlencode($this->GetResetPasswordCode($email));

        $Body ="Hello ".$user_rec['name']."<br><br>".
        "There was a request to reset your password at ".$this->sitename."<br>".
        "Please click the link below to complete the request: <br>"
		."<a href='".stripslashes($link)."' style='background-color:#1ab394;border:1px solid #1ab394;border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:44px;text-align:center;text-decoration:none;width:150px;-webkit-text-size-adjust:none;mso-hide:all;'>Reset Password &rarr;</a>"
		."<br>";
        
        if(!$this->Sendemail($email,$Subject,$Body))
        {
            return false;
        }
        return true;
    }
    
    function SendNewPassword($user_rec, $new_password)
    {
        $email = $user_rec['email'];
        
        $Subject = "Your new password for ".$this->sitename;
        
        $Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Your password is reset successfully. ".
        "Here is your updated login:\r\n".
        "username:".$user_rec['username']."\r\n".
        "password:$new_password\r\n".
        "\r\n".
        "Login here: ".$this->GetAbsoluteURLFolder()."/login.php\r\n";
        
        if(!$this->Sendemail($email,$Subject,$Body))
        {
            return false;
        }
        return true;
    }    
    
    function ValidateRegistrationSubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }
        
        $validator = new FormValidator();
        $validator->addValidation("name","req","Please fill in Name");
        $validator->addValidation("email","email","The input for Email should be a valid email value");
        $validator->addValidation("email","req","Please fill in Email");
        $validator->addValidation("username","req","Please fill in UserName");
        $validator->addValidation("password","req","Please fill in Password");

        
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }        
        return true;
    }
    
    function CollectRegistrationSubmission(&$formvars)
    {
        $formvars['name'] = $this->Sanitize($_POST['name']);
        $formvars['email'] = $this->Sanitize($_POST['email']);
        $formvars['username'] = $this->Sanitize($_POST['username']);
        $formvars['password'] = $this->Sanitize($_POST['password']);
        $formvars['phone'] = $this->Sanitize($_POST['phone']);
		$formvars['company'] = $this->Sanitize($_POST['company']);
		$formvars['timeZone'] = $this->Sanitize($_POST['timeZone']);
    }
    
    function SendUserConfirmationEmail(&$formvars)
    {

        $Subject = "Your registration with ".$this->sitename;

        $confirmcode = $formvars['confirmcode'];
        
        $confirm_url = $this->GetAbsoluteURLFolder().'confirmreg.php?code='.$confirmcode;
        $confirm_url = "<a href='".stripslashes($confirm_url)."' style='background-color:#1ab394;border:1px solid #1ab394;border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:44px;text-align:center;text-decoration:none;width:150px;-webkit-text-size-adjust:none;mso-hide:all;'>Confirm Account &rarr;</a>";
        $Body ="Hello ".$formvars['name']."<br>".
        "Thanks for your registration with ".$this->sitename."<br>".
        "Please click the link below to confirm your registration.<br>".
        "$confirm_url<br>";

		
		
        if(!$this->Sendemail($formvars['email'],$Subject,$Body))
        {
            //$this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
		
        return true;
    }
    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        return $scriptFolder;
    }
    
    function SendAdminIntimationEmail(&$formvars)
    {
        if(empty($this->admin_email))
        {
            return false;
        }

        $Subject = "New registration: ".$formvars['name'];

        $Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$formvars['name']."\r\n".
        "Email address: ".$formvars['email']."\r\n".
        "UserName: ".$formvars['username'];
        
        if(!$this->Sendemail($this->admin_email,$Subject,$Body))
        {
            return false;
        }
        return true;
    }
	
    function SendAdminIntimationEmailaddDevice($Device,$Vehicle,$installedby,$sysadded,$reason)
    {
        if(empty($this->admin_email))
        {
            return false;
        }

        $Subject = $this->sitename." A device ".$reason.": ".$Device." ".$Vehicle;

        $Body ="Device ".$reason." ".$this->sitename."\r\n".
        "Device: ".$Device."\r\n".
        "Vehicle: ".$Vehicle."\r\n".
		"Installed by: ".$installedby."\r\n".
        "System added by: ".$sysadded;
        
        if(!$this->Sendemail($this->admin_email,$Subject,$Body))
        {
            return false;
        }
        return true;
    }
	
    function SaveToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        if(!$this->Ensuretable())
        {
            return false;
        }
        if(!$this->IsFieldUnique($formvars,'email'))
        {
            $this->HandleError("This email is already registered");
            return false;
        }
        
        if(!$this->IsFieldUnique($formvars,'username'))
        {
            $this->HandleError("This UserName is already used. Please try another username");
            return false;
        }        
        if(!$this->InsertIntoDB($formvars))
        {
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
        return true;
    }
    
    function IsFieldUnique($formvars,$fieldname)
    {
        $field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select username from $this->tablename where $fieldname='".$field_val."'";
        $result = mysqli_query($this->connection,$qry);   
        if($result && mysqli_num_rows($result) > 0)
        {
            return false;
        }
        return true;
    }
    
    function DBLogin()
    {

        $this->connection = mysqli_connect($this->db_host,$this->username,$this->pwd,$this->database);

        if(!$this->connection)
        {   
            $this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
            return false;
        }

        return true;
    }       
    
    function Ensuretable()
    {
        return true;
    }
    
    function CreateTable()
    {
        $qry = "Create Table $this->tablename (".
                "id_user INT NOT NULL AUTO_INCREMENT ,".
                "name VARCHAR( 128 ) NOT NULL ,".
                "email VARCHAR( 64 ) NOT NULL ,".
                "phone_number VARCHAR( 16 ) NOT NULL ,".
                "username VARCHAR( 16 ) NOT NULL ,".
                "password VARCHAR( 32 ) NOT NULL ,".
                "confirmcode VARCHAR(32) ,".
                "PRIMARY KEY ( id_user )".
                ")";
                
        if(!mysqli_query($this->connection,$qry))
        {
            $this->HandleDBError("Error creating the table \nquery was\n $qry");
            return false;
        }
        return true;
    }
    
    function InsertIntoDB(&$formvars)
    {
    
        $confirmcode = $this->MakeConfirmationMd5($formvars['email']);
        
        $formvars['confirmcode'] = $confirmcode;
        
         $insert_query = 'insert into '.$this->tablename.'(
                name,
                email,
                phone,
                username,
                password,
                confirmcode,
				company,
				HeadUser,
				Privilage,
				timeZone
                )
                values
                (
                "' . $this->SanitizeForSQL($formvars['name']) . '",
                "' . $this->SanitizeForSQL($formvars['email']) . '",
                "' . $this->SanitizeForSQL($formvars['phone']) . '",
                "' . $this->SanitizeForSQL($formvars['username']) . '",
                "' . md5($formvars['password']) . '",
                "' . $confirmcode . '",
				"' . $this->SanitizeForSQL($formvars['company']) . '",
				"' . $this->headuser . '",
				"N",
				"'.$formvars['timeZone'].'"
                )';     
        if(!mysqli_query($this->connection,$insert_query))
        {
            $this->HandleDBError("Error inserting data to the table\n");
            return false;
        }        
        return true;
    }
    function MakeConfirmationMd5($email)
    {
        $randno1 = rand();
        $randno2 = rand();
        return md5($email.$this->rand_key.$randno1.''.$randno2);
    }
    function SanitizeForSQL($str)
    {
        $ret_str = addslashes( $str );
        return $ret_str;
    }
    
 /*
    Sanitize() function removes any potential threat from the
    data submitted. Prevents email injections or any other hacker attempts.
    if $remove_nl is true, newline chracters are removed from the input.
    */
    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    } 

	function datevalidate($date)
	{
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date))
		{
			return true;
		}else{
			return false;
		}
	}

	function leftmenue()
	{
		$adminmenu = "";
		if($this->IsAdmin())
		{
			$adminmenu = '<li>
					<a href=""><i class="fa fa-cogs"  style="color:#6974FD"></i> <span class="nav-label">Admin Settings</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
						<li><a href="DashBoardAdmin.php">Admin DashBoard</a></li>
						<li><a href="Home_admin.php">Admin Tracking view</a></li>
						<li><a href="device_settings_admin.php">Device admin Settings</a></li>
						<li><a href="ReportStatusAdmin.php">Admin Status Report</a></li>
						<li><a href="usermanagement.php">User settings</a></li>
						<li><a href="userassign.php">Users and Objects</a></li>
						<li><a href="customers.php">Customers</a></li>
						<li><a href="blacklist.php">Blacklist</a></li>
						<li><a href="EventLogger.php">Activity logger</a></li>
                    </ul>
				</li>';
		}
		return '<li>
					<a href="DashBoard.php"><i class="fa fa-tachometer"  style="color:#6974FD"></i> <span class="nav-label">Dash Board</span></a>
				</li>
				<li>
					<a href="Home.php"><i class="fa fa-home"  style="color:#6974FD"></i> <span class="nav-label">Tracking View</span></a>
				</li>
				<li>
					<a href="playback.php"><i class="fa fa-history"  style="color:#6974FD"></i> <span class="nav-label">History View</span></a>
				</li>
				<li>
					<a href="report_Operation.php"><i class="fa fa-file-text-o"  style="color:#6974FD"></i> <span class="nav-label">Report View</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
						<li><a href="ReportStatus.php">Status Report</a></li>
                        <li><a href="report_daywise.php">Day Wise summery</a></li>
						<li><a href="report_daywisefull.php">Day Wise full</a></li>
						<li><a href="report_Parking.php">Parking report</a></li>
                        <li><a href="report_Operation.php">Operation Report</a></li>
						<li><a href="report_section.php">Zonal Report</a></li>
						<li><a href="report_History.php">History Report</a></li>
						<li><a href="report_Trip.php">Trip Report</a></li>
                    </ul>
				</li>
				<li>
					<a href="notifications.php"><i class="fa fa-exclamation-triangle"  style="color:#6974FD"></i> <span class="nav-label">Notifications</span></a>
				</li>
				<li>
					<a href=""><i class="fa fa-cogs"  style="color:#6974FD"></i> <span class="nav-label">Settings</span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
						<li><a href="device_settings.php">Device Settings</a></li>
						<li><a href="alertsrule_settings.php">Notification rules</a></li>
						<li><a href="deviceMaintanance_settings_admin.php">Maintenance rules</a></li>
                        <li><a href="geofunc.php">Geometry</a></li>
						<li><a href="settings_systems.php">System settings</a></li>
                    </ul>
				</li>
				<li>
					<a href="contactus.php"><i class="fa fa-comments"  style="color:#6974FD"></i> <span class="nav-label">Contact us</span></a>
				</li>
				'.$adminmenu;
	}
}
?>