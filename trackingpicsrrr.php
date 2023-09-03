<?PHP
require_once("./include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}
?>


<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tracking Pictures</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/plugins/blueimp/css/blueimp-gallery.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/plugins/select2/select2.min.css" rel="stylesheet">
	<link href="css/plugins/clockpicker/clockpicker.css" rel="stylesheet">
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

<body onload="initialize()"  style="padding-top: 0px;" class="fixed-nav fixed-sidebar">

    <div id="wrapper">

    <nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element"> <span>
                            <img alt="image" class="img" src="rrrlogo.jpg" />
                             </span>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">RRR and Company</strong>
                             </span> <span class="text-muted text-xs block">Cooperated Companies <b class="caret"></b></span> </span> </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <!--<li><a href="http://rrrandco.com/companies.php?t=rrr-group" target="_blank">RRR & Co</a></li>
                            <li><a href="http://rrrandco.com/companies.php?t=i-core-technologies" target="_blank">ICORE Technology</a></li>
                            <li><a href="http://rrrandco.com/companies.php?t=franchise-mart--pvt--ltd-" target="_blank">Technology and finance</a></li>-->
                            <li class="divider"></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                    <div class="logo-element">
                        RRR
                    </div>
                </li>
				<li>
					<a href="DashBoard.php"><i class="fa fa-tachometer"></i> <span class="nav-label">Dash Board</span></a>
					<a href="Home.php"><i class="fa fa-home"></i> <span class="nav-label">Live Vision</span></a>
					<a href="playback.php"><i class="fa fa-history"></i> <span class="nav-label">Back Track</span></a>
					<a href="ReportTrip.php"><i class="fa fa-file-text-o"></i> <span class="nav-label">Trip report</span></a>
					<a href="trackingpics.php"><i class="fa fa-picture-o"></i> <span class="nav-label">Pictures</span></a>
				</li>
            </ul>

        </div>
    </nav>

        <div id="page-wrapper" class="gray-bg">
        <div class="row border-bottom">

        <nav class="navbar white-bg navbar-fixed-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <form role="search" class="navbar-form-custom" action="">
                <div class="form-group">
                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                </div>
            </form>
        </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <span class="m-r-sm text-muted welcome-message"><?= $fgmembersite->UserFullName(); ?></span>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope"></i>  <span class="label label-warning">0</span>
                    </a>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell"></i>  <span class="label label-primary">0</span>
                    </a>
                </li>


                <li>
                    <a href="logout.php">
                        <i class="fa fa-sign-out"></i> Log out
                    </a>
                </li>
                <li>
                    <a class="right-sidebar-toggle">
                        <i class="fa fa-tasks"></i>
                    </a>
                </li>
            </ul>

        </nav>
        </div>
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Tracking Pictures</h2>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content">
            <div class="row">
			
									<div class="col-lg-12">
										<div class="ibox float-e-margins">
											<div class="ibox-title">
												<h5>Play Back & Monitoring</h5>
											</div>
											<div class="ibox-content">
													<ul class="stat-list">
														<li>
															<small></small>
															<form action="trackingpics.php" id="trackingpicform" name="trackingpicform"  method="post">
															<div class="form-group" id="data_1">
																<label class="font-normal">Enter the date and Search</label>
																<div class="input-group date">
																	<span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" name="trackdate" id="trackdate">
																</div>
															</div><br>
															<div class="input-group clockpicker" data-autoclose="true">
																<input type="text" class="form-control" value="09:00" id="tracktime"  name="tracktime">
																<span class="input-group-addon">
																	<span class="fa fa-clock-o"></span>
																</span>
															</div><br>
															<div class="col-md-4">
																<p>
																	 Select your Vehicle
																</p>
																<select class="select2_demo_2 form-control" name="trackbus" style="width: 100px;">
																	<?= $fgmembersite->VehicleListOptions(); ?>
																</select>
															</div><br>
															<div class="btn-group">
																<button class="btn btn-primary"  type="button" onclick="submitform()">Load</button>
																<button class="btn btn-white"  type="button" onclick="refresh()">Refresh</button>
															</div>
															</form>
														</li>
													</ul>
											</div>
										</div>
									</div>
                <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    <div class="ibox-content">

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
						
						
                            <!--<a href="img/gallery/1.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/1s.jpg"></a>
                            <a href="img/gallery/2.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/2s.jpg"></a>
                            <a href="img/gallery/3.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/3s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/6.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/6s.jpg"></a>
                            <a href="img/gallery/7.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/7s.jpg"></a>
                            <a href="img/gallery/8.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/8s.jpg"></a>
                            <a href="img/gallery/9.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/9s.jpg"></a>
                            <a href="img/gallery/10.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/10s.jpg"></a>
                            <a href="img/gallery/12.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/12s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/6.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/6s.jpg"></a>
                            <a href="img/gallery/7.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/7s.jpg"></a>
                            <a href="img/gallery/2.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/2s.jpg"></a>
                            <a href="img/gallery/3.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/3s.jpg"></a>
                            <a href="img/gallery/1.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/1s.jpg"></a>
                            <a href="img/gallery/9.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/9s.jpg"></a>
                            <a href="img/gallery/10.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/10s.jpg"></a>
                            <a href="img/gallery/11.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/11s.jpg"></a>
                            <a href="img/gallery/12.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/12s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/6.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/6s.jpg"></a>
                            <a href="img/gallery/12.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/12s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/10.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/10s.jpg"></a>
                            <a href="img/gallery/1.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/1s.jpg"></a>
                            <a href="img/gallery/2.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/2s.jpg"></a>
                            <a href="img/gallery/3.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/3s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/6.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/6s.jpg"></a>
                            <a href="img/gallery/7.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/7s.jpg"></a>
                            <a href="img/gallery/8.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/8s.jpg"></a>
                            <a href="img/gallery/9.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/9s.jpg"></a>
                            <a href="img/gallery/10.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/10s.jpg"></a>
                            <a href="img/gallery/11.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/11s.jpg"></a>
                            <a href="img/gallery/12.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/12s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/6.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/6s.jpg"></a>
                            <a href="img/gallery/7.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/7s.jpg"></a>
                            <a href="img/gallery/2.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/2s.jpg"></a>
                            <a href="img/gallery/3.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/3s.jpg"></a>
                            <a href="img/gallery/1.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/1s.jpg"></a>
                            <a href="img/gallery/9.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/9s.jpg"></a>
                            <a href="img/gallery/10.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/10s.jpg"></a>
                            <a href="img/gallery/11.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/11s.jpg"></a>
                            <a href="img/gallery/12.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/12s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/7.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/7s.jpg"></a>
                            <a href="img/gallery/8.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/8s.jpg"></a>
                            <a href="img/gallery/9.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/9s.jpg"></a>
                            <a href="img/gallery/10.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/10s.jpg"></a>
                            <a href="img/gallery/11.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/11s.jpg"></a>
                            <a href="img/gallery/12.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/12s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/6.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/6s.jpg"></a>
                            <a href="img/gallery/12.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/12s.jpg"></a>
                            <a href="img/gallery/4.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/4s.jpg"></a>
                            <a href="img/gallery/5.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/5s.jpg"></a>
                            <a href="img/gallery/10.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/10s.jpg"></a>
                            <a href="img/gallery/11.jpg" title="Image from Unsplash" data-gallery=""><img src="img/gallery/11s.jpg"></a>-->

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

                        </div>

                    </div>
                </div>
            </div>

            </div>
        </div>
        <div class="footer">
            <div>
                <strong>Copyright</strong> RRR and Company &copy; 2017
            </div>
        </div>

        </div>
        </div>




    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <script src="js/plugins/pace/pace.min.js"></script>

    <!-- blueimp gallery -->
    <script src="js/plugins/blueimp/jquery.blueimp-gallery.min.js"></script>
	<script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>
    <script src="js/plugins/select2/select2.full.min.js"></script>
    <!-- Clock picker -->
    <script src="js/plugins/clockpicker/clockpicker.js"></script>
</body>

</html>
