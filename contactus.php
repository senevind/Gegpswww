<?PHP
ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");

if($_POST)
{
		
	$public_key = "6LeW84UUAAAAALIzVv1y1ZgwKLuR9j3dIdlWYuY8"; /* Your reCaptcha public key */
	$private_key = "6LeW84UUAAAAANUUuMD-kVqxwPMIrzBJwqZ6U6sO"; /* Enter your reCaptcha private key */
	$url = "https://www.google.com/recaptcha/api/siteverify"; /* Default end-point, please verify this before using it */
	
		/* The response given by the form being submitted */
		$response_key = $_POST['g-recaptcha-response'];
		/* Send the data to the API for a response */
		$response = file_get_contents($url.'?secret='.$private_key.'&response='.$response_key.'&remoteip='.$_SERVER['REMOTE_ADDR']);
		/* json decode the response to an object */
		$response = json_decode($response);

		/* if success */
		if($response->success == 1)
		{
			$fgmembersite->RedirectToformURL(SendContactUsEmail($_POST['email'],$_POST['subject'],$_POST['msgbody']),$_SERVER['PHP_SELF'],'formrespond.php');
			exit;
		}else
		{
			$message = "You are a robot and we don't like robots.";
			$fgmembersite->RedirectToformURL($message,$_SERVER['PHP_SELF'],'formrespond.php');
		}
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
	$mail->Host = "smtp.iomartmail.com"; // SMTP a utilizar. Por ej. ¿smtp.miservidor.com?
	$mail->Username = "hello@radioservices.co.uk"; // Correo completo a utilizar
	$mail->Password = "HelloGPS22!"; // Contraseña
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


<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title>Conact us</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">
	
	<script src='https://www.google.com/recaptcha/api.js'></script>
<script>
function validateForm() {

  let mail = document.forms["ContactusForm"]["email"].value;
  let subject = document.forms["ContactusForm"]["subject"].value;

  
  if(!validateEmail(mail))
  {
	  alert("You have entered an invalid email address!");
	  return false;
  }
  
  if (subject == "") {
    alert("Subject must be filled out");
    return false;
  }
  return true;
}

function validateEmail(email) 
{
	var re = /\S+@\S+\.\S+/;
	return re.test(email);
}
</script>
</head>
<body class="blank">

<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>Homer - Responsive Admin Theme</h1><p>Special Admin Theme for small and medium webapp with very clean and aesthetic style and feel. </p><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<div class="color-line"></div>

<div class="register-container">
		<div class="row" >
			<div class="col-md-12">
                <div class="hpanel email-compose">
                    <div class="panel-heading hbuilt">
                        <div class="p-xs h3">
                            Contact Us!
                        </div>
                    </div>
					<form name="ContactusForm" method="post" enctype="multipart/form-data" class="form-horizontal" onsubmit="return validateForm()">
					
                    <div class="panel-heading hbuilt">
                        <div class="p-xs">
                            <img src="images/GegpsContact.png" alt="profile-picture">
                        </div>
                    
                    <div class="panel-body no-padding">
						<h5>Need help?</h5>
						<p>Please contact us if you need help or more information . Tel: 212708818882+</p>
						<button class="btn btn-success" onclick=" window.open('https://wa.me/message/6W5S6SW3QNC7H1','_blank')"> Whatsapp</button>
						
						<p><small>we answer calls during working days and hours only</small></p>
                    </div>
					
					</div>
					
					</form>

                </div>
            </div>	
		</div>
</div>

<!-- Vendor scripts -->
<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/jquery-ui/jquery-ui.min.js"></script>
<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/sparkline/index.js"></script>

<!-- App scripts -->
<script src="scripts/homer.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>

</body>
</html>