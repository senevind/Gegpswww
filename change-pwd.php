<?PHP
require_once("./include/membersite_config.php");
$passworderror = "";

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}

if(isset($_POST['submitted']))
{
   if($fgmembersite->ChangePassword())
   {
	   	if($_POST['newpwd'] == $_POST['confirm']){
        $fgmembersite->RedirectToURL("changed-pwd.html");
		}
		else{
			$passworderror = "Confirm password does not match \n ";
		}
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
    <title>Change Password</title>

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
                <h3>Change Password</h3>
            </div>
            <div class="hpanel">
                <div class="panel-body">
                        <form id='changepwd' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post' accept-charset='UTF-8'>
						<input type='hidden' name='submitted' id='submitted' value='1'/>
						<input type='hidden'  class='spmhidip' name='<?php echo $fgmembersite->GetSpamTrapInputName(); ?>' />
	
							<div class="row">
                            <div class="form-group col-lg-12">
                                <label>Old Password</label>
									<input type="password" class="form-control" name="oldpwd" id="oldpwd"  placeholder="Enter your Old Password"/>
                            <span id='register_name_errorloc' class='error'></span>
							</div>
                            <div class="form-group col-lg-6">
                                <label>New Password</label>
                                <input type="password" class="form-control" name="newpwd" id="newpwd"  placeholder="Enter your New Password"/>
                            <span id='register_email_errorloc' class='error'></span>
							</div>
                            <div class="form-group col-lg-6">
                                <label>Confirm Password</label>
                                <input type="password" class="form-control" name="confirm" id="confirm"  placeholder="Confirm your New Password"/>
                            <span id='register_username_errorloc' class='error'></span>
							</div>
							<div><span class='error'><?php echo $passworderror.$fgmembersite->GetErrorMessage(); ?></span></div>
                            </div>
                            <div class="text-center">
							<input type='submit' name='Submit' class="btn btn-success" value='Submit' />
                            
                            </div>
                        </form>
						<div class="login-register">
				            <a href="index.php">Home</a>
				        </div>
                </div>
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
<script type='text/javascript'>
// <![CDATA[
    var pwdwidget = new PasswordWidget('oldpwddiv','oldpwd');
    pwdwidget.enableGenerate = false;
    pwdwidget.enableShowStrength=false;
    pwdwidget.enableShowStrengthStr =false;
    pwdwidget.MakePWDWidget();
    
    var pwdwidget = new PasswordWidget('newpwddiv','newpwd');
    pwdwidget.MakePWDWidget();
    
    
    var frmvalidator  = new Validator("changepwd");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("oldpwd","req","Please provide your old password");
    
    frmvalidator.addValidation("newpwd","req","Please provide your new password");

// ]]>
</script>
</body>
</html>