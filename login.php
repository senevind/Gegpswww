<?PHP
require_once("./include/membersite_config.php");

if(isset($_POST['submitted']))
{
   if($fgmembersite->Login())
   {
        $fgmembersite->RedirectToURL("index.php");
   }
}
if($fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("DashBoard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title><?= $fgmembersite->Title1 ?> | Login</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->
	<link rel="manifest" href="manifest.json">
	
    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />

    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">
	
	<!-- place this in a head section -->
	<link rel="apple-touch-icon" href="touch-icon-iphone.png">
	<link rel="apple-touch-icon" sizes="152x152" href="images/contract.png">
	<link rel="apple-touch-icon" sizes="180x180" href="images/contract.png">
	<link rel="apple-touch-icon" sizes="167x167" href="images/contract.png">
	
	
	<!-- place this in a head section -->
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link href="images/contract.png" sizes="2048x2732" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="1668x2224" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="1536x2048" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="1125x2436" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="1242x2208" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="750x1334" rel="apple-touch-startup-image" />
	<link href="images/contract.png" sizes="640x1136" rel="apple-touch-startup-image" />
</head>
<body class="blank">

<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>Homer - Responsive Admin Theme</h1><p>Special Admin Theme for small and medium webapp with very clean and aesthetic style and feel. </p><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<div class="color-line"></div>


<div class="login-container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-center m-b-md">
                <img src="images/logologin.png" class="m-b" alt="logo"  height="200" width="300">
            </div>
            <div class="hpanel">
                <div class="panel-body">
                        <form id='login' action='<?php echo $fgmembersite->GetSelfScript(); ?>' method='post'>
                            <input type='hidden' name='submitted' id='submitted' value='1'/>
							<div><span class='error'><?php echo $fgmembersite->GetErrorMessage(); ?></span></div>
							<div class="form-group">
                                <label class="control-label" for="username">Username</label>
                                <input type="text" class="form-control"  name='username' id='username' value='<?php echo $fgmembersite->SafeDisplay('username') ?>'  placeholder="Enter your Username"/>
                                <span class="help-block small">Your unique username to app</span>
								<span id='login_username_errorloc' class='error'></span>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="password">Password</label>
                                <input type="password" class="form-control" type='password' name='password' id='password'  placeholder="Enter your Password"/>
                                <span class="help-block small">Your strong password</span>
								<span id='login_password_errorloc' class='error'></span>
                            </div>
							<input class="btn btn-info btn-block" type='submit' name='Submit' value='Submit' />
                            <a class="btn btn-default btn-block" href="register.php">Register</a>
							<a class="btn btn-default btn-block" href="contactus.php">Contact Us</a>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>

	<div class="modal fade" id="TaskModal" tabindex="-1" role="dialog"  aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="color-line"></div>
				<div class="modal-header">
					<h4 class="modal-title">Install GPS22</h4>
				</div>
				<div class="modal-body">
					<p>Install GPS22 tracking platform on your desktop android and iOS</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary add-button">Install</button>
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

<script>
	if ("serviceWorker" in navigator) {
	  // register service worker
	  navigator.serviceWorker.register("service-worker.js");
	}
	
				
		// Detects if device is on iOS 
		const isIos = () => {
		  const userAgent = window.navigator.userAgent.toLowerCase();
		  
		  return /iphone|ipad|ipod/.test( userAgent );
		}
		// Detects if device is in standalone mode
		const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);

		// Checks if should display install popup notification:
		if (isIos() && !isInStandaloneMode()) {
			//alert("ios");
		  this.setState({ showInstallMessage: true });
		}
</script>
</body>
</html>