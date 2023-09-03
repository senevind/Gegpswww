
<?PHP
ini_set('max_execution_time', 300);
require_once("./include/membersite_config.php");
require_once("./include/reports.php");
if(!$fgmembersite->CheckLogin())
{
	if(!$fgmembersite->Login())
	   {
		   echo "Authantication Error!";
		   die;
			//$fgmembersite->RedirectToURL("login.php");
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
    <title>SASINDU | Analizer</title>

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
	<link rel="stylesheet" href="vendor/fooTable/css/footable.core.min.css" />

	
	

	<script src="./OpenLayers-2.13.1/OpenLayers.js"></script>

<script>
function initialize() {
	$('#data_1 .input-group.date').datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		format: "yyyy-mm-dd"
	});
	$('#data_2 .input-group.date').datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		format: "yyyy-mm-dd"
	});
}

</script>
</head>
<body onload="initialize()" class="fixed-navbar sidebar-scroll">

<!-- Simple splash screen-->
<div class="splash"> <div class="color-line"></div><div class="splash-title"><h1>Homer - Responsive Admin Theme</h1><p>Special Admin Theme for small and medium webapp with very clean and aesthetic style and feel. </p><div class="spinner"> <div class="rect1"></div> <div class="rect2"></div> <div class="rect3"></div> <div class="rect4"></div> <div class="rect5"></div> </div> </div> </div>
<!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->

<div class="content">
    <div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-tools">
                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    <a class="closebox"><i class="fa fa-times"></i></a>
                </div>
                Vehicle Cumilative Travel Distance Analysis
            </div>
            <div class="panel-body">
                <div>
                    <canvas id="lineOptionsdistance" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-tools">
                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    <a class="closebox"><i class="fa fa-times"></i></a>
                </div>
                Vehicle Speed Analysis
            </div>
            <div class="panel-body">
                <div>
                    <canvas id="lineOptionsspeed" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading">
                <div class="panel-tools">
                    <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    <a class="closebox"><i class="fa fa-times"></i></a>
                </div>
                Vehicle Fuel Analysis
            </div>
            <div class="panel-body">
                <div>
                    <canvas id="lineOptions" height="120"></canvas>
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
<script src="vendor/fooTable/dist/footable.all.min.js"></script>
<script src="vendor/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js"></script>
<!-- App scripts -->
<script src="scripts/homer.js"></script>

<script src="vendor/chartjs/Chart.min.js"></script>
<script>

    $(function () {

        // Initialize Example 1
        $('#example1').footable();

        /**
         * Flot charts data and options
         */

		<?php
		$chartvalues = [];
		if(!$fgmembersite->DBLogin())
		{
			echo "Not connected";
			return false;
		} 
		$busno = "";
		$date = ""; 
		if(isset($_POST['submit']))
		{
			$chartvalues =  $Reports->fuelchart($fgmembersite->connection,$_POST['select'],$_POST['st_date']); 
			$busno = $_POST['select'];
			$date = $_POST['st_date'];
		}
		?>
		//*************************************** Chart for Distance **************************
        var lineDatadist = {
            labels: [<?php echo $chartvalues['lab']; ?>],
            datasets: [
                {
                    label:"Distance <?php echo $busno."  [".$date."]" ?>",
                    backgroundColor: 'rgba(98,203,49, 0.5)',
                    pointBorderWidth: 1,
                    pointBackgroundColor: "rgba(98,203,49,1)",
                    pointRadius: 3,
                    pointBorderColor: '#ffffff',
                    borderWidth: 1,
                    data: [<?php echo $chartvalues['dist']; ?>]
                }
            ]
        };
		
        var lineOptionsdist = {
            responsive: true
        };

        var ctxdist = document.getElementById("lineOptionsdistance").getContext("2d");
        new Chart(ctxdist, {type: 'line', data: lineDatadist, options:lineOptionsdist});		
		
		
		//*************************************** Chart for Speed **************************
        var lineDataspeed = {
            labels: [<?php echo $chartvalues['lab']; ?>],
            datasets: [
                {
                    label:"Speed <?php echo $busno."  [".$date."]" ?>",
                    backgroundColor: 'rgba(98,203,49, 0.5)',
                    pointBorderWidth: 1,
                    pointBackgroundColor: "rgba(98,203,49,1)",
                    pointRadius: 3,
                    pointBorderColor: '#ffffff',
                    borderWidth: 1,
                    data: [<?php echo $chartvalues['speed']; ?>]
                }
            ]
        };
		
        var lineOptionsspeed = {
            responsive: true
        };

        var ctxdist = document.getElementById("lineOptionsspeed").getContext("2d");
        new Chart(ctxdist, {type: 'line', data: lineDataspeed, options:lineOptionsspeed});
		
		
		//*************************************** Chart for Fuel **************************
        var lineData = {
            labels: [<?php echo $chartvalues['lab']; ?>],
            datasets: [

                {
                    label: "Fuel <?php echo $busno."  [".$date."]" ?>",
                    backgroundColor: 'rgba(98,203,49, 0.5)',
                    pointBorderWidth: 1,
                    pointBackgroundColor: "rgba(98,203,49,1)",
                    pointRadius: 3,
                    pointBorderColor: '#ffffff',
                    borderWidth: 1,
                    data: [<?php echo $chartvalues['val']; ?>]
                }
            ]
        };
		
        var lineOptions = {
            responsive: true
        };

        var ctx = document.getElementById("lineOptions").getContext("2d");
        new Chart(ctx, {type: 'line', data: lineData, options:lineOptions});
    });

</script>

</body>
</html>