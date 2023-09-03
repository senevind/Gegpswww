<?PHP
require_once("./include/membersite_config.php");

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
    <title>SASINDU | Picture View</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.css" />
    <link rel="stylesheet" href="vendor/metisMenu/dist/metisMenu.css" />
    <link rel="stylesheet" href="vendor/animate.css/animate.css" />
    <link rel="stylesheet" href="vendor/bootstrap/dist/css/bootstrap.css" />
    <link rel="stylesheet" href="vendor/blueimp-gallery/css/blueimp-gallery.min.css" />
    <link rel="stylesheet" href="vendor/select2-3.5.2/select2.css" />
    <link rel="stylesheet" href="vendor/select2-bootstrap/select2-bootstrap.css" />
    <link rel="stylesheet" href="vendor/bootstrap-datepicker-master/dist/css/bootstrap-datepicker3.min.css" />
    <link rel="stylesheet" href="vendor/clockpicker/dist/bootstrap-clockpicker.min.css" />
    <link rel="stylesheet" href="vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" />
	
    <!-- App styles -->
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css" />
    <link rel="stylesheet" href="fonts/pe-icon-7-stroke/css/helper.css" />
    <link rel="stylesheet" href="styles/style.css">
<script>

function submitform()
{
	document.getElementById("trackingpicform").submit();
}
function initialize() {
	
	$('#data_1 .input-group.date').datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		format: "yyyy-mm-dd"
	});
	$(".select2_demo_2").select2();
	$('.clockpicker').clockpicker();
	refresh();
}

function refresh()
{
	currentdateupdate();
	currenttimeupdate();
}
function currentdateupdate()
{
var today = new Date();
var dd = today.getDate();
var mm = today.getMonth()+1; //January is 0!

var yyyy = today.getFullYear();
if(dd<10){
    dd='0'+dd;
} 
if(mm<10){
    mm='0'+mm;
} 
//var today = dd+'/'+mm+'/'+yyyy;
var today = yyyy+'-'+mm+'-'+dd;
document.getElementById("trackdate").value = today;
}

function currenttimeupdate()
{
var d = new Date(); // for now
var hours = d.getHours();
d.getMinutes();
d.getSeconds();
document.getElementById("tracktime").value = hours+":00";
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
                    <div class="panel-body">

                        <div class="lightBoxGallery">
								<?php	
								if(isset($_POST['trackbus']))
								{
									if(!$fgmembersite->DBLogin())
									{
										echo "Not connected!";
									}
								$picturecount = 0;
									$sysno = $fgmembersite->GetImeino($_POST['trackbus']);
									$datetime = $_POST['trackdate']." ".$_POST['tracktime'];
									$datetime = DateTime::createFromFormat('Y-m-d H:i', $datetime);
									
								if ($datetime instanceof DateTime) {
									$sql = "SELECT `id`, `sysno`, `datetime`, `picdata` FROM `picturedata` WHERE `datetime`>='".$datetime->format('Y-m-d H:i:s')."' AND `datetime`<='".$datetime->add(new DateInterval('PT1H'))->format('Y-m-d H:i:s')."' AND `sysno` ='".$sysno."'"; 
									//echo $sql;
									$result = mysql_query($sql,$fgmembersite->connection);
									while($row = mysql_fetch_array($result)) {
										$picturecount++;
									//$image =  '<img src="data:image/jpg;base64,'.base64_encode( $row['picdata'] ).'" height="150" width="150" >';	
										echo "<a href='trackingpicssource.php?image_id=".$row['id']."' title='Image from ".$_POST['trackbus']."' data-gallery=''><img src='trackingpicssource.php?image_id=".$row['id']."'  height='150' width='150' ></a>";
									}
									if($picturecount<=0)
									{
										echo "No picture(s) found!";
									}
								}else{
									echo "Enter Valide Date and  Time!";
								}
								}
								?>		
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!-- The Gallery as lightbox dialog, should be a child element of the document body -->
<div id="blueimp-gallery" class="blueimp-gallery">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>



<!-- Vendor scripts -->
<script src="vendor/jquery/dist/jquery.min.js"></script>
<script src="vendor/slimScroll/jquery.slimscroll.min.js"></script>
<script src="vendor/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="vendor/metisMenu/dist/metisMenu.min.js"></script>
<script src="vendor/iCheck/icheck.min.js"></script>
<script src="vendor/sparkline/index.js"></script>
<script src="vendor/blueimp-gallery/js/jquery.blueimp-gallery.min.js"></script>
<script src="vendor/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js"></script>
<script src="vendor/clockpicker/dist/bootstrap-clockpicker.min.js"></script>
<script src="vendor/select2-3.5.2/select2.min.js"></script>
<!-- App scripts -->
<script src="scripts/homer.js"></script>

<!-- Local style for demo purpose -->
<style>

    .lightBoxGallery {
        text-align: center;
    }

    .lightBoxGallery a {
        margin: 5px;
        display: inline-block;
    }

</style>

</body>
</html>