<?PHP
require_once("./include/membersite_config.php");
$passworderror = "";
$message = "";
if(isset($_POST['submitted']))
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
			
			if($fgmembersite->RegisterUser()){
				if($_POST['password'] == $_POST['confirm']){
				$fgmembersite->RedirectToURL("thank-you.html");
				}
				else{
					$passworderror = "Confirm password does not match \n ";
				}
			}
			
		}
		else
		{
			$message = "You are a robot and we don't like robots.";
		}

}
$timeZone = '0';
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title><?= $fgmembersite->Title1 ?> | Register</title>

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
</head>
<body class="blank">

<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>Homer - Responsive Admin Theme</h1><p>Special Admin Theme for small and medium webapp with very clean and aesthetic style and feel. </p><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<div class="color-line"></div>

<div class="register-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <h3>Registration</h3>
            </div>
            <div class="hpanel">
                <div class="panel-body">
				<?php echo $message; ?>
                        <form id='register' name='RegForm' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' onsubmit="return validateForm()">
						<input type='hidden' name='submitted' id='submitted' value='1'/>
						<input type='hidden'  class='spmhidip' name='<?php echo $fgmembersite->GetSpamTrapInputName(); ?>' />
							
							<div class="row">
                            <div class="form-group col-lg-12">
                                <label>Your Full Name</label>
									<input type="text" class="form-control" name='name' id='name' value='<?php echo $fgmembersite->SafeDisplay('name') ?>'  placeholder="Enter your Name"/>
                            <span id='register_name_errorloc' class='error'></span>
							</div>
                            <div class="form-group col-lg-6">
                                <label>Password</label>
                                <input type="password" class="form-control" name="password" id="password"  placeholder="Enter your Password"/>
                            <div id='register_password_errorloc' class='error' style='clear:both'></div>
							</div>
                            <div class="form-group col-lg-6">
                                <label>Repeat Password</label>
                                <input type="password" class="form-control" name="confirm" id="confirm"  placeholder="Confirm your Password"/>
                            <div id='register_password_errorloc' class='error' style='clear:both'></div>
							</div>
                            <div class="form-group col-lg-6">
                                <label>Email Address</label>
                                <input type="text" class="form-control" name='email' id='email' value='<?php echo $fgmembersite->SafeDisplay('email') ?>'  placeholder="Enter your Email"/>
                            <span id='register_email_errorloc' class='error'></span>
							</div>
                            <div class="form-group col-lg-6">
                                <label>Username</label>
                                <input type="text" class="form-control" name='username' id='username' value='<?php echo $fgmembersite->SafeDisplay('username') ?>'  placeholder="Enter your Username"/>
                            <span id='register_username_errorloc' class='error'></span>
							</div>
							<div class="form-group  col-lg-6">
								<label>Time Zone</label>
										<select class="select form-control"  id="timeZone" name="timeZone">
											<option timeZoneId="1" gmtAdjustment="GMT-12:00" useDaylightTime="0" value="-720" <?php if($timeZone=='-720'){echo 'selected';} ?>>(GMT-12:00) </option>
											<option timeZoneId="2" gmtAdjustment="GMT-11:00" useDaylightTime="0" value="-660" <?php if($timeZone=='-660'){echo 'selected';} ?>>(GMT-11:00) </option>
											<option timeZoneId="3" gmtAdjustment="GMT-10:00" useDaylightTime="0" value="-600" <?php if($timeZone=='-600'){echo 'selected';} ?>>(GMT-10:00) </option>
											<option timeZoneId="4" gmtAdjustment="GMT-09:00" useDaylightTime="1" value="-540" <?php if($timeZone=='-540'){echo 'selected';} ?>>(GMT-09:00) </option>
											<option timeZoneId="5" gmtAdjustment="GMT-08:00" useDaylightTime="1" value="-480" <?php if($timeZone=='-480'){echo 'selected';} ?>>(GMT-08:00) </option>
											<option timeZoneId="9" gmtAdjustment="GMT-07:00" useDaylightTime="1" value="-420" <?php if($timeZone=='-420'){echo 'selected';} ?>>(GMT-07:00) </option>
											<option timeZoneId="13" gmtAdjustment="GMT-06:00" useDaylightTime="0" value="-360" <?php if($timeZone=='-360'){echo 'selected';} ?>>(GMT-06:00) </option>
											<option timeZoneId="16" gmtAdjustment="GMT-05:00" useDaylightTime="1" value="-300" <?php if($timeZone=='-300'){echo 'selected';} ?>>(GMT-05:00) </option>
											<option timeZoneId="20" gmtAdjustment="GMT-04:00" useDaylightTime="1" value="-240" <?php if($timeZone=='-240'){echo 'selected';} ?>>(GMT-04:00) </option>
											<option timeZoneId="21" gmtAdjustment="GMT-03:30" useDaylightTime="1" value="-210" <?php if($timeZone=='-210'){echo 'selected';} ?>>(GMT-03:30) </option>
											<option timeZoneId="22" gmtAdjustment="GMT-03:00" useDaylightTime="1" value="-180" <?php if($timeZone=='-180'){echo 'selected';} ?>>(GMT-03:00) </option>
											<option timeZoneId="26" gmtAdjustment="GMT-02:00" useDaylightTime="1" value="-120" <?php if($timeZone=='-120'){echo 'selected';} ?>>(GMT-02:00) </option>
											<option timeZoneId="27" gmtAdjustment="GMT-01:00" useDaylightTime="0" value="-60" <?php if($timeZone=='-60'){echo 'selected';} ?>>(GMT-01:00) </option>
											<option timeZoneId="29" gmtAdjustment="GMT+00:00" useDaylightTime="0" value="0" <?php if($timeZone=='0'){echo 'selected';} ?>>(GMT+00:00) </option>
											<option timeZoneId="35" gmtAdjustment="GMT+01:00" useDaylightTime="1" value="60" <?php if($timeZone=='60'){echo 'selected';} ?>>(GMT+01:00) </option>
											<option timeZoneId="44" gmtAdjustment="GMT+02:00" useDaylightTime="1" value="120" <?php if($timeZone=='120'){echo 'selected';} ?>>(GMT+02:00) </option>
											<option timeZoneId="48" gmtAdjustment="GMT+03:00" useDaylightTime="0" value="180" <?php if($timeZone=='180'){echo 'selected';} ?>>(GMT+03:00) </option>
											<option timeZoneId="49" gmtAdjustment="GMT+03:30" useDaylightTime="1" value="210" <?php if($timeZone=='210'){echo 'selected';} ?>>(GMT+03:30) </option>
											<option timeZoneId="52" gmtAdjustment="GMT+04:00" useDaylightTime="1" value="240" <?php if($timeZone=='240'){echo 'selected';} ?>>(GMT+04:00) </option>
											<option timeZoneId="53" gmtAdjustment="GMT+04:30" useDaylightTime="0" value="270" <?php if($timeZone=='270'){echo 'selected';} ?>>(GMT+04:30) </option>
											<option timeZoneId="55" gmtAdjustment="GMT+05:00" useDaylightTime="0" value="300" <?php if($timeZone=='300'){echo 'selected';} ?>>(GMT+05:00) </option>
											<option timeZoneId="57" gmtAdjustment="GMT+05:30" useDaylightTime="0" value="330" <?php if($timeZone=='330'){echo 'selected';} ?>>(GMT+05:30) </option>
											<option timeZoneId="58" gmtAdjustment="GMT+05:45" useDaylightTime="0" value="345" <?php if($timeZone=='345'){echo 'selected';} ?>>(GMT+05:45) </option>
											<option timeZoneId="60" gmtAdjustment="GMT+06:00" useDaylightTime="0" value="360" <?php if($timeZone=='360'){echo 'selected';} ?>>(GMT+06:00) </option>
											<option timeZoneId="61" gmtAdjustment="GMT+06:30" useDaylightTime="0" value="390" <?php if($timeZone=='390'){echo 'selected';} ?>>(GMT+06:30) </option>
											<option timeZoneId="63" gmtAdjustment="GMT+07:00" useDaylightTime="1" value="420" <?php if($timeZone=='420'){echo 'selected';} ?>>(GMT+07:00) </option>
											<option timeZoneId="68" gmtAdjustment="GMT+08:00" useDaylightTime="0" value="480" <?php if($timeZone=='480'){echo 'selected';} ?>>(GMT+08:00) </option>
											<option timeZoneId="71" gmtAdjustment="GMT+09:00" useDaylightTime="1" value="540" <?php if($timeZone=='540'){echo 'selected';} ?>>(GMT+09:00) </option>
											<option timeZoneId="73" gmtAdjustment="GMT+09:30" useDaylightTime="0" value="570" <?php if($timeZone=='570'){echo 'selected';} ?>>(GMT+09:30) </option>
											<option timeZoneId="78" gmtAdjustment="GMT+10:00" useDaylightTime="1" value="600" <?php if($timeZone=='600'){echo 'selected';} ?>>(GMT+10:00) </option>
											<option timeZoneId="79" gmtAdjustment="GMT+11:00" useDaylightTime="1" value="660" <?php if($timeZone=='660'){echo 'selected';} ?>>(GMT+11:00) </option>
											<option timeZoneId="81" gmtAdjustment="GMT+12:00" useDaylightTime="0" value="720" <?php if($timeZone=='720'){echo 'selected';} ?>>(GMT+12:00) </option>
											<option timeZoneId="82" gmtAdjustment="GMT+13:00" useDaylightTime="0" value="780" <?php if($timeZone=='780'){echo 'selected';} ?>>(GMT+13:00) </option>
										</select>
							</div>
                            <div class="form-group col-lg-6">
                                <label>Phone</label>
                                <input type="text" class="form-control" name='phone' id='phone' value='<?php echo $fgmembersite->SafeDisplay('phone') ?>'  placeholder="Enter your Phone Number"/>
                            <span id='register_username_errorloc' class='error'></span>
							</div>
                            <div class="form-group col-lg-6">
                                <label>Company</label>
                                <input type="text" class="form-control" name='company' id='company' value='<?php echo $fgmembersite->SafeDisplay('company') ?>'  placeholder="Enter your Company Name"/>
                            <span id='register_username_errorloc' class='error'></span>
							</div>
							<div><span class='error'><?php echo $passworderror.$fgmembersite->GetErrorMessage(); ?></span></div>
                            </div>
							<div class="g-recaptcha" data-sitekey="6LeW84UUAAAAALIzVv1y1ZgwKLuR9j3dIdlWYuY8"></div>
                            <div class="text-center">
							<input type='submit' name='Submit' class="btn btn-info" value='Submit' />
                            </div>
                        </form>
						<div class="login-register">
				            <a href="index.php">Home</a>
				        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
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
		<script type='text/javascript'>
		function validateForm(){
		  let username = document.forms["RegForm"]["username"].value;
		  if (username == "") {
			alert("Username must be filled out");
			return false;
		  }
		  if(!validateUsername(username))
		  {
			  alert("Remove spaces and special characters is username!");
			  return false;
		  }
		  return true;
		}
		function validateUsername(str)
		{
			return str.match("^[A-Za-z0-9]+$");
		}
		
		// <![CDATA[
			var pwdwidget = new PasswordWidget('thepwddiv','password');
			pwdwidget.MakePWDWidget();
			
			var frmvalidator  = new Validator("register");
			frmvalidator.EnableOnPageErrorDisplay();
			frmvalidator.EnableMsgsTogether();
			frmvalidator.addValidation("name","req","Please provide your name");

			frmvalidator.addValidation("email","req","Please provide your email address");

			frmvalidator.addValidation("email","email","Please provide a valid email address");

			frmvalidator.addValidation("username","req","Please provide a username");
			
			frmvalidator.addValidation("password","req","Please provide a password");

		// ]]>
		</script>
</body>
</html>