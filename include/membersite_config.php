<?PHP
require_once("./include/fg_membersite.php");
$fgmembersite = new FGMembersite();
//Provide your site name here
$fgmembersite->SetWebsiteName('www.gegps.com');

//Provide the email address where you want to get notifications
$fgmembersite->SetAdminEmail('senevind@gmail.com');
//$fgmembersite->SetAdminEmail('senevind@gmail.com');
//Provide your database login details here:
//hostname, user name, password, database name and table name
//note that the script will create the table (for example, fgusers in this case)
//by itself on submitting register.php for the first time


$fgmembersite->InitDB(/*hostname*/'localhost',
                      /*username*/'admin',
                      /*password*/'287597f2c56af673c6eca69f3eb91fb7b2794bd71c411b33',
                      /*database name*/'gpsservice');
					  
//For better security. Get a random string from this link: http://tinyurl.com/randstr
// and put it here
$fgmembersite->SetRandomKey('qSRcVS6DrTzrrvP');

?>