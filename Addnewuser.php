<?PHP
require_once("./include/membersite_config.php");
$phone = '';
$fullname = '';
$username = '';
$email = '';
$company = '';
$autharized = '';

if(!$fgmembersite->CheckLogin())
{
    $fgmembersite->RedirectToURL("login.php");
    exit;
}

if(!$fgmembersite->CheckAdmin())
{
	    $fgmembersite->RedirectToURL("index.php");
	    exit;
}



if(isset($_POST['username']) && $_POST['username'] != ''  && $_POST['password'] != '')
{
	if($fgmembersite->AddUser($_POST))
	{
		$fgmembersite->RedirectToformURL($fgmembersite->UserAssignObjectfromarray($_POST),'Objects.php','formrespond.php');
		//$fgmembersite->RedirectToformURL('Successfully Enabled!',$_SERVER['PHP_SELF'],'formrespond.php');
		exit;
	}else{
		$fgmembersite->RedirectToformURL("User not added. Please check your inputs!",$_SERVER['PHP_SELF'],'formrespond.php');
		exit;
	}
}

if(isset($_GET['vehicleno']))
{
$vehicleno = $_GET['vehicleno'];
}else{
	$fgmembersite->RedirectToformURL("Vehicle Number Not Set!","Objects.php",'formrespond.php');
	exit;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>User Management</title>

    <!-- Bootstrap Core CSS -->
    <link href="./vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="./vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="./vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="./vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="./dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="./vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

<script>userload
function delconfirm(id){
  if (confirm("Are you sure you want to delete this record?")) {
  document.getElementById(id).submit();
  }
}

function userload(id){
  document.getElementById('userload'+id).submit();
}
</script>

</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">Sathsindu GPS</a>
            </div>
            <!-- /.navbar-header -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?= $fgmembersite->UserFullName(); ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="profile.php"><i class="fa fa-fw fa-user"></i> Profile</a>
                        </li>
                        <li>
                            <a href="change-pwd.php"><i class="fa fa-fw fa-exchange"></i> Change Password</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="Admin.php"><i class="fa fa-dashboard fa-fw"></i> Admin</a>
                        </li>
                        <li>
                            <a href="ObjectManagement.php"><i class="fa fa-dashboard fa-fw"></i> Tracking Objects</a>
                        </li>
                        <li>
                            <a href="UserAssignObjects.php"><i class="fa fa-dashboard fa-fw"></i>Object Assigning</a>
                        </li>
                        <li>
                            <a href="geo.php"><i class="fa fa-dashboard fa-fw"></i>Geofences</a>
                        </li>
                        <li>
                            <a href="ObjectMonitor.php"><i class="fa fa-dashboard fa-fw"></i>Object Monitoring</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">User Management</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			
			<div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i>Add new user</h3>
                            </div>
                            <div class="panel-body">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<form class="form-horizontal" method="post">
									<input type='hidden' name='checkbox[]' value='<?php echo $vehicleno ?>'/>
									 <div class="form-group form-group-sm">
									  <label class="control-label col-sm-2" for="fullname">
									   Full Name
									  </label>
									  <div class="col-sm-10">
									   <input class="form-control" value="" id="fullname" name="fullname" type="text"/>
									  </div>
									 </div>
									 <div class="form-group form-group-sm">
									  <label class="control-label col-sm-2" for="username">
									   Username
									  </label>
									  <div class="col-sm-10">
									   <input class="form-control" value="" id="username" name="username" type="text"/>
									  </div>
									 </div>
									 <div class="form-group form-group-sm">
									  <label class="control-label col-sm-2" for="password">
									   Password
									  </label>
									  <div class="col-sm-10">
									   <input class="form-control" value="" id="password" name="password" type="password"/>
									  </div>
									 </div>
									 <div class="form-group form-group-sm">
									  <label class="control-label col-sm-2" for="name2">
									   Email
									  </label>
									  <div class="col-sm-10">
									   <input class="form-control" value="" id="name2" name="name2" type="text"/>
									  </div>
									 </div>
									 <div class="form-group form-group-sm">
									  <label class="control-label col-sm-2" for="company">
									   Company
									  </label>
									  <div class="col-sm-10">
									   <input class="form-control" value="" id="company" name="company" type="text"/>
									  </div>
									 </div>
									 <div class="form-group form-group-sm">
									  <label class="control-label col-sm-2" for="phone">
									   Phone
									  </label>
									  <div class="col-sm-10">
									   <input class="form-control" value="" id="phone" name="phone" type="text"/>
									  </div>
									 </div>
									 <div class="form-group form-group-sm">
									  <label class="control-label col-sm-2">
									   Autharized
									  </label>
									  <div class="col-sm-10 ">
									   <div class="checkbox">
										<label class="checkbox">
										 <input name="autharized" type="checkbox"/>
										 Yes/No
										</label>
									   </div>
									  </div>
									 </div>
									 <div class="form-group">
									  <div class="col-sm-10 col-sm-offset-2">
									   <button class="btn btn-primary " name="submit" type="submit">
										Submit
									   </button>
									  </div>
									 </div>
									</form>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- /.row -->
                <!-- /.col-lg-6 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="./vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="./vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="./vendor/metisMenu/metisMenu.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="./vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="./vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="./vendor/datatables-responsive/dataTables.responsive.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="./dist/js/sb-admin-2.js"></script>

    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });
    </script>

</body>

</html>
