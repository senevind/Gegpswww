<?PHP
$Reports = new Reports();

class Reports
{
function TaskReport($connection,$Busno,$date,$end_date){
//$date = '2017-02-01';
//$end_date = '2017-02-28';
$date = trim($date);
$end_date = trim($end_date);

	if($date=="" || $end_date==""){
		return "Please enter valide Date!";
	}
	
	if($this->GetImeino($Busno,$connection)){
	$sysnoarray = $this->SysInfoArray($Busno,$connection);
	}
	else{
		return "<tr><td colspan='5'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
	}
	
$date1=date_create($date);
$date2=date_create($end_date);
$diff=date_diff($date1,$date2);


if(trim($date) == '' || trim($end_date) == '' || $diff->format("%a")>31|| strtotime($date) > strtotime($end_date)){
return "<tr><td colspan='8'><font color='red'>Maximum allowed 31 days. Please correct the inputs</font></td></tr>";
}
	
	
$TaskRow = "";

	$qry = "SELECT `idTask`, `TaskName`, `routeID`, `TrackerID`, `stDate`, `endDate`,DATE_ADD(`StartTIme`, INTERVAL `tz` MINUTE) AS StartTIme,DATE_ADD(`CompleteTime`, INTERVAL `tz` MINUTE) AS CompleteTime, `TaskAdd`, `addedTime`, routeno, objname, tz
			FROM `geoTask`
			LEFT JOIN geoadminlines
			ON geoTask.routeID = geoadminlines.id
			LEFT JOIN trackingobjects
			ON geoTask.TrackerID = trackingobjects.objsysno 
			WHERE DATE_ADD(`stDate`, INTERVAL `tz` MINUTE) >= '$date'
			AND DATE_ADD(`endDate`, INTERVAL `tz` MINUTE) <= '$end_date'
			AND `TrackerID` = '".$sysnoarray['sysno']."'
			ORDER BY `addedTime` DESC";

			
	$stmt = mysqli_query($connection,$qry);
	
	while($row = mysqli_fetch_array($stmt))
	{
		$status = "Pending";
		if($row['StartTIme'] == "" && $row['CompleteTime'] == "")
		{
			$status = "Pending";
		}
		if($row['StartTIme'] != "" && $row['CompleteTime'] == "")
		{
			$status = "Started";
		}
		if($row['StartTIme'] != "" && $row['CompleteTime'] != "")
		{
			$status = "Completed";
		}
		
		$TaskRow = $TaskRow."<tr><td>".trim($row['TaskName'])."</div></td>
		<td>".$row['routeno']."</td>
		<td>".$row['objname']."</td>
		<td>".$row['stDate']."</td>
		<td>".$row['endDate']."</td>
		<td>".$row['StartTIme']."</td>
		<td>".$row['CompleteTime']."</td>
		<td>".$status."</td></tr>";
	}

return $TaskRow;
}


///////////////////////////////////////		Status Report	/////////////////////////////////////
function StatusReport($fgmembersite)
{
		$tz = $fgmembersite->UserTimeZone();
		if($fgmembersite->IsGroupUser()){
		$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, IFNULL(E.GroupName,'Uncategorized') user_group, trackingobjects.id AS `id`, `objname` AS objectname1, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, DATE_ADD(`lasttime`, INTERVAL ".$tz." MINUTE) as lasttimez, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager` 
		FROM trackingobjects 
		LEFT JOIN `geofences` as B ON trackingobjects.origine = B.id 
		LEFT JOIN geofences as C ON trackingobjects.Destnation = C.id 
		LEFT JOIN `objectassign` AS D ON trackingobjects.objsysno = D.objectname
        LEFT JOIN GroupsUsers AS E ON trackingobjects.subgroup = E.Id
		WHERE objsysno IN ('".$fgmembersite->assignedvehiclestring()."')
		ORDER BY user_group, objname";
		}else{
		$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, IFNULL(E.GroupName,'Uncategorized') user_group, trackingobjects.id AS `id`, `objname` AS objectname1, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, DATE_ADD(`lasttime`, INTERVAL ".$tz." MINUTE) as lasttimez, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, `expdate`, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager` 
		FROM trackingobjects 
		LEFT JOIN geofences as B ON trackingobjects.origine = B.id 
		LEFT JOIN geofences as C ON trackingobjects.Destnation = C.id 
		LEFT JOIN `objectassign` AS D ON trackingobjects.objsysno = D.objectname
        LEFT JOIN GroupsUsers AS E ON D.usergroup = E.Id
		WHERE objsysno IN ('".$fgmembersite->assignedvehiclestring()."')
		AND D.username = '".$fgmembersite->UserName()."'
		ORDER BY user_group, objname";
		}
		
		if(!$fgmembersite->DBLogin())
		{
			echo "Not connected";
			return false;
		}
	
		$stmt = mysqli_query($fgmembersite->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$DisplayTime = $fgmembersite->UserTimeFormat($row['lasttimez']);
			
			if($row['lastlng']!=""){
				$openpamsys = $row['objsysno'];
				$openpamsys = "onclick='openmap($openpamsys)'";
			}else{
				$openpamsys = "";
			}
			
			if($row['starred'] == "1"){
				$checked = 'checked';
			}else{
				$checked = '';
			}
			$status = $fgmembersite->Status($row['lastvelosity'],$row['lastengine'],$row['lasttime']);
			
			if($status == 0)
			{
				$statusName = "Offline";
				$statusStyle = "label GPSlabel-offline pull-left";
			}
			if($status == 1)
			{
				$statusName = "Stoped";
				$statusStyle = "label GPSlabel-info pull-left";
			}
			if($status == 2)
			{
				$statusName = "Idle";
				$statusStyle = "label GPSlabel-success pull-left";
			}
			if($status >= 3)
			{
				$statusName = "Running";
				$statusStyle = "label GPSlabel-warning pull-left";
			}	

			if($row['lastengine'] > 0)
			{
				$lastengine = "ON";
			}else
			{
				$lastengine = "OFF";
			}
			{
			$list = $list."<tr>
			<td  style='padding-top: 18px;'>"."<span id='status".$row['objsysno']."' class='$statusStyle'>$statusName</span>"."</td>
			<td>".$row['objectname1']."</td>
			<td><span id='velosity".$row['objsysno']."' class='pull-left'>".$fgmembersite->UserUnit(round($row['lastvelosity']))['value'].$fgmembersite->UserUnit(0)['speedUnit']."</span></td>
			<td><span id='time".$row['objsysno']."' class='pull-left'>".$DisplayTime."</span></div></td>
			<td><span id='lati".$row['objsysno']."' class='pull-left'>".$row['lastlati']."</span></td>
			<td><span id='lng".$row['objsysno']."' class='pull-left'>".$row['lastlng']."</span></td>
			<td><span id='dir".$row['objsysno']."' class='pull-left'>".$row['lastdirection']."</span></td>
			<td><span id='eng".$row['objsysno']."' class='pull-left'>".$lastengine."</span></td>
			<td>".$row['model']."</td>
			<td>".$row['simno']."</td>
			<td>".$row['objsysno']."</td>
			<td>".$row['user_group']."</td>
			</tr>";
			}
		}
		echo $list;	
}
	
function StatusReportAdmin($fgmembersite)
{
		$tz = $fgmembersite->UserTimeZone();
		$qry = "SELECT B.geo_name AS `origine`, C.geo_name AS `Destnation`, IFNULL(E.GroupName,'Uncategorized') user_group, trackingobjects.id AS `id`, `objname` AS objectname1, `objsysno`, `mapimage`, `starred`, `tz`, `objectdiscription`, `simno`, `fixeddate`, `contact`, `model`, `cam1`, `cam2`, `tankcapacity`, `tanktype`, `fixedby`, `lastlng`, `lastlati`, `lastvelosity`, `lastdirection`, `lasttime`, DATE_ADD(`lasttime`, INTERVAL ".$tz." MINUTE) as lasttimez, `lastmilage`, `lastengine`, `lastfuel`, `lasttemp`, `Fmilage`, `IsFmilageupdate`, `HeadUser`, DATE(`expdate`) AS expdate, `activate`, `sendntc`, `freeextend`, `paymentdisc`, `Todaymilage`,`manager` 
		FROM trackingobjects 
		LEFT JOIN geofences as B ON trackingobjects.origine = B.id 
		LEFT JOIN geofences as C ON trackingobjects.Destnation = C.id 
        LEFT JOIN GroupsAdmin AS E ON trackingobjects.admin_group = E.Id
		WHERE objsysno IN ('".$fgmembersite->assignedvehiclestring()."')
		ORDER BY user_group, objname";
		
		if(!$fgmembersite->DBLogin())
		{
			echo "Not connected";
			return false;
		}
	
		$stmt = mysqli_query($fgmembersite->connection,$qry);
		
		while($row = mysqli_fetch_array($stmt))
		{
			$DisplayTime = $fgmembersite->UserTimeFormat($row['lasttimez']);
			if($row['lastlng']!=""){
				$openpamsys = $row['objsysno'];
				$openpamsys = "onclick='openmap($openpamsys)'";
			}else{
				$openpamsys = "";
			}
			
			if($row['starred'] == "1"){
				$checked = 'checked';
			}else{
				$checked = '';
			}
			$status = $fgmembersite->Status($row['lastvelosity'],$row['lastengine'],$row['lasttime']);
			
			if($status == 0)
			{
				$statusName = "Offline";
				$statusStyle = "label GPSlabel-offline pull-left";
			}
			if($status == 1)
			{
				$statusName = "Stoped";
				$statusStyle = "label GPSlabel-info pull-left";
			}
			if($status == 2)
			{
				$statusName = "Idle";
				$statusStyle = "label GPSlabel-success pull-left";
			}
			if($status >= 3)
			{
				$statusName = "Running";
				$statusStyle = "label GPSlabel-warning pull-left";
			}	

			if($row['lastengine'] > 0)
			{
				$lastengine = "ON";
			}else
			{
				$lastengine = "OFF";
			}
			{
			$list = $list."<tr>
			<td  style='padding-top: 18px;'>"."<span id='status".$row['objsysno']."' class='$statusStyle'>$statusName</span>"."</td>
			<td>".$row['objectname1']."</td>
			<td><span id='velosity".$row['objsysno']."' class='pull-left'>".$fgmembersite->UserUnit(round($row['lastvelosity']))['value'].$fgmembersite->UserUnit(0)['speedUnit']."</span></td>
			<td><span id='time".$row['objsysno']."' class='pull-left'>".$DisplayTime."</span></div></td>
			<td><span id='lati".$row['objsysno']."' class='pull-left'>".$row['lastlati']."</span></td>
			<td><span id='lng".$row['objsysno']."' class='pull-left'>".$row['lastlng']."</span></td>
			<td><span id='dir".$row['objsysno']."' class='pull-left'>".$row['lastdirection']."</span></td>
			<td><span id='eng".$row['objsysno']."' class='pull-left'>".$lastengine."</span></td>
			<td>".$row['model']."</td>
			<td>".$row['simno']."</td>
			<td>".$row['objsysno']."</td>
			<td>".$row['user_group']."</td>
			</tr>";
			}
		}
		echo $list;	
}	
	
///////////////////////////////////////		Event Logger end	/////////////////////////////////////
function EventLoggerEmei($connection,$Emei){
//$date = '2017-02-01';
//$end_date = '2017-02-28';
	
	
$bus_rowst = "";

	
	$qry="SELECT `id`, `reason`, `user`, `systemno`, `logtime`, `parameters` FROM `activity_log` 
	WHERE `systemno` = '".$Emei."' 
	OR `parameters` LIKE '%".$Emei."%'";
//echo $sql3."<br>";
$stmt = mysqli_query($connection,$qry);

	while($row = mysqli_fetch_array($stmt))
		{
			$bus_rowst = $bus_rowst."<tr><td>".$row['logtime']."</td><td>".$row['systemno']."</td><td>".$row['user']."</td><td>".$row['reason']."</td><td>".$row['parameters']."</td></tr>";
		}
return $bus_rowst;
}

function EventLoggerDate($connection,$date,$end_date){
//$date = '2017-02-01';
//$end_date = '2017-02-28';
$date = trim($date);
$end_date = trim($end_date);

	if($date=="" || $end_date==""){
		return "Please enter valide Date!";
	}
		
$date1=date_create($date);
$date2=date_create($end_date);
$diff=date_diff($date1,$date2);


if(trim($date) == '' || trim($end_date) == '' || $diff->format("%a")>=31|| strtotime($date) > strtotime($end_date)){
return "<tr><td colspan='5'><font color='red'>Maximum allowed 31 days. Please correct the inputs</font></td></tr>";
}
	
	
$bus_rowst = "";

	
	$qry="SELECT `id`, `reason`, `user`, `systemno`, `logtime`, `parameters` FROM `activity_log` 
	WHERE `logtime` >= '".$date."' 
	AND `logtime` <= '".$end_date."'";
//echo $sql3."<br>";
$stmt = mysqli_query($connection,$qry);

	while($row = mysqli_fetch_array($stmt))
		{
			$bus_rowst = $bus_rowst."<tr><td>".$row['logtime']."</td><td>".$row['systemno']."</td><td>".$row['user']."</td><td>".$row['reason']."</td><td>".$row['parameters']."</td></tr>";
		}
return $bus_rowst;
}

///////////////////////////////////////		Event Logger end	/////////////////////////////////////
	
function GetImeino($Busno,$connection){
	$Busno = trim($Busno);
	$qry = "SELECT objname,objsysno,battery
  FROM trackingobjects
  WHERE objname = '$Busno'";
  $stmt = mysqli_query($connection,$qry);
  
  	while($row = mysqli_fetch_array($stmt))
	{
		return $row['objsysno'];
	}
return false;
}

function SysInfoArray($Busno,$connection){
	$Busno = trim($Busno);
	$qry = "SELECT objname,objsysno,tz,mapimage,battery
  FROM trackingobjects
  WHERE objname = '$Busno'";

  $stmt = mysqli_query($connection,$qry);
  
  	while($row = mysqli_fetch_array($stmt))
	{
		return array('sysno'=>$row['objsysno'],'tz'=>$row['tz'],'BusNo'=>$Busno,'image'=>$row['mapimage'],'battery'=>$row['battery']);
	}
return false;
}

function alertreport($connection,$Busno,$date,$end_date)
{
	if($this->GetImeino($Busno,$connection)){
	$sysno = $this->GetImeino($Busno,$connection);
	}
	else{
		return "Vehicle number is not available";
	}
$tablebodystring = "";
	$qry = "SELECT `id`, `trackingid`, `alerttime`, `pwercut`, `overvoltage`, `upervoltage`, `overspeed` 
			FROM `alerts` 
			WHERE `alerttime` > '".$date." 00:00:00.000'
			AND `alerttime` < '".$end_date." 23:59:59.000'
			AND `trackingid` = '".$sysno."'
			ORDER BY alerttime DESC";


$stmt = mysqli_query($connection,$qry);
	while($row = mysqli_fetch_array($stmt))
{
	$tablebodystring = $tablebodystring."<tr><td>".$row['alerttime']."</td><td>".$row['pwercut']."</td><td>".$row['overvoltage']."</td><td>".$row['upervoltage']."</td><td>".$row['overspeed']."</td></tr>";
}	
return $tablebodystring;
}

function fuelchart($connection,$Busno,$date){
	
	if($this->GetImeino($Busno,$connection)){
	$sysnoarray = $this->SysInfoArray($Busno,$connection);
	}
	else{
		return "Vehicle number is not available";
	}
$lable = "";	
$fuelvalues = "";
$distance = "";
$startdistance = 0;
$speed = "";
$tanksize = $this->GetTankSize($connection,$sysno);

  $qry="SELECT   DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result`
  FROM `tko".$sysnoarray['sysno']."`
  WHERE DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) > '".$date." 00:00:00.000'
  AND DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) < '".$date." 23:59:59.000'
  ORDER BY DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE)";
  
//echo $qry."<br>";
$rowcount = 0;

$stmt = mysqli_query($connection,$qry);
if(!$stmt)
{
	echo "result not found";
	
}
	while($row = mysqli_fetch_array($stmt))
{
	if($rowcount == 0)
	{
		$startdistance = $row['Miles'];
		$comma = "";
	}else{
		$comma = ",";
	}
	

	$cumilativedistance = ($row['Miles']-$startdistance)/1000;
	$lable = $lable."$comma'".date("H:i", strtotime($row['Time']))."'";
	$fuelvalues = $fuelvalues."$comma".($row['Oil']*$tanksize);
	$distance = $distance."$comma".$cumilativedistance;
	$speed = $speed."$comma".$row['Velocity'];
	$rowcount++;
}

return array('lab'=>$lable,'val'=>$fuelvalues,'dist'=>$distance,'speed'=>$speed);
}

function GetTankSize($connection,$systemno)
{
	$tanksize = 0;

	$qry = "SELECT id,objname,objsysno,tanktype,tankcapacity 
	FROM trackingobjects WHERE objsysno = '$systemno'";
	
	$stmt = mysql_query($qry,$connection);
	
	while($row = mysql_fetch_array($stmt))
	{
		$tanksize = $row['tankcapacity'];
	}
	return $tanksize;
}


function SummeryRTPget_row($fgmembersite,$connection,$Busno,$date,$end_date){
//$date = '2017-02-01';
//$end_date = '2017-02-28';
$date = trim($date);
$end_date = trim($end_date);

	if($date=="" || $end_date==""){
		return "Please enter valide Date!";
	}
	
	if($this->GetImeino($Busno,$connection)){
	$sysnoarray = $this->SysInfoArray($Busno,$connection);
	}
	else{
		return "<tr><td colspan='5'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
	}
	
$date1=date_create($date);
$date2=date_create($end_date);
$diff=date_diff($date1,$date2);


if(trim($date) == '' || trim($end_date) == '' || $diff->format("%a")>=31|| strtotime($date) > strtotime($end_date)){
return "<tr><td colspan='5'><font color='red'>Maximum allowed 31 days. Please correct the inputs</font></td></tr>";
}
	
	
$bus_rowst = "";
while (strtotime($date) <= strtotime($end_date)) {
	//$date_string = $date."  ".date ("Y-m-d", strtotime("+1 day", strtotime($date))).";".$date_string;
	$bus_rowst = $bus_rowst.$this->SummeryRTP_row_creat($fgmembersite,$date,date ("Y-m-d", strtotime("+1 day", strtotime($date))),$connection,$sysnoarray);
	$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
}
return $bus_rowst;
}


function SummeryRTP_row_creat($fgmembersite,$sttime,$endtime,$connection,$sysnoarray){

	
	$qry="SELECT   DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result`
	FROM `tko".$sysnoarray['sysno']."`
	WHERE DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) > '".$sttime." 00:00:00.000'
	AND DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) < '".$sttime." 23:59:59.000'
	ORDER BY DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) DESC";
//echo $sql3."<br>";
$stmt = mysqli_query($connection,$qry);
$milageCumilat = 0;
$passLat = 0;
$passLong = 0;
$milege1=0;
$milege2=0;
$rowcount = 0;

$milege1=0;
$milege2=0;
$rowcount = 0;
$totalvelosity=0;
$topspeed=0;
$starttimecount = false;
$stoptimecount = false;

$e = new DateTime('00:00');
$f = clone $e;


	while($row = mysqli_fetch_array($stmt))
		{
			////////////////////////////  Date Topspeed  averagespeed todaymilage  Traveltime  //////////////////////////
			if($rowcount==0){
				$milege2 = $row['Miles'];
			}else{
				$date = new DateTime($passTime);
				$date2 = new DateTime($row['Time']);
				$travalTime = $date2->getTimestamp() - $date->getTimestamp();
				$distance = $this->vincentyGreatCircleDistance($passLat, $passLong, $row['Latitude'], $row['Longitude']);
				$speed = $distance/$travalTime;
				if($speed<100)
				{
					$milageCumilat = $milageCumilat + $distance;
				}else{
					$milageCumilat = $milageCumilat + 0;
				}
			}
			
			if($rowcount==0){
				$milege2 = $row['Miles'];
			}

			if($row['Velocity']>2 && $starttimecount == false){
				$starttimecount = true;
				$sratrtime = $row['Time'];
			}

			if($row['Velocity']<2 && $starttimecount == true){
				$stoptimecount = true;
				$stoptime = $row['Time'];
				$e->add($this->time_diffobj($sratrtime,$stoptime));
				$starttimecount = false;
				$stoptimecount = false;
			}

			if($topspeed<$row['Velocity']){
				$topspeed=$row['Velocity'];
			}

			if($row['Velocity']>2){
				$totalvelosity=$row['Velocity']+$totalvelosity;
				$rowcount++;
			}
			
		$passLat = $row['Latitude'];
		$passLong = $row['Longitude'];
		$passTime = $row['Time'];
		$milege1 = $row['Miles'];
		}

if($rowcount==0){
$avgvelosity=0;
}
else{
$avgvelosity = $totalvelosity/$rowcount;
}
$avgvelosity = round(number_format($avgvelosity, 2, '.', ''));

$milage_diff = ($milege2-$milege1)/1000;

if($milage_diff<0)
{
	$milage_diff = round(($this->ConfusedMilages($sttime,$sysnoarray,$connection))/1000,2);
}
$milageCumilat = round($milageCumilat/1000);
return "<tr><td>".$fgmembersite->UserDateFormat($sttime)."</td><td>".$fgmembersite->UserUnit($topspeed)['value'].$fgmembersite->UserUnit(0)['speedUnit']."</td><td>".$fgmembersite->UserUnit($avgvelosity)['value'].$fgmembersite->UserUnit(0)['speedUnit']."</td><td>".$fgmembersite->UserUnit($milageCumilat)['value'].$fgmembersite->UserUnit(0)['lengthUnit']."</td><td>".$f->diff($e)->format("%H:%I")."</td></tr>";
}


function MarkerPopupDaywiceReport($sttime,$connection,$sysno){
$sysnoarray = $this->SysInfoArraySysNo($sysno,$connection);
	//print_r($sysnoarray);
	
	$MonitoringDateTime = date('Y-m-d', strtotime($sysnoarray['tz']." minutes"));
	$sttime = $MonitoringDateTime;

	$qry="SELECT   DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result`
	FROM `tko".$sysnoarray['sysno']."`
	WHERE DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) > '".$sttime." 00:00:00.000'
	AND DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) < '".$sttime." 23:59:59.000'
	AND `Longitude` != '0'
	AND `Latitude` != '0'
	ORDER BY DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE)";
	
	//echo $qry;
	  
	$stmt = mysqli_query($connection,$qry);

	$milageCumilat = 0;
	$passLat = 0;
	$passLong = 0;
	$milege1=0;
	$milege2=0;
	$rowcount = 0;
	$totalvelosity=0;
	$topspeed=0;
	$starttimecount = false;
	$stoptimecount = false;
	$runningcount = 0;
	$idlecount = 0;
	$stoppedcount = 0;
	$timestemparray = array();
	$rownumber = 0;
	$sratrtime = '-';
	$stoptime = '-';
	$getstoptime = '-';
	$stoptimeset = false;
	
	$e = new DateTime('00:00');
	$f = clone $e;


		while($row = mysqli_fetch_array($stmt))
		{
			
			////////////////////////////  Date Topspeed  averagespeed todaymilage  Traveltime  //////////////////////////
			if($rowcount==0){
				$milege2 = $row['Miles'];
			}else{
				$date = new DateTime($passTime);
				$date2 = new DateTime($row['Time']);
				$travalTime = $date2->getTimestamp() - $date->getTimestamp();
				$distance = $this->vincentyGreatCircleDistance($passLat, $passLong, $row['Latitude'], $row['Longitude']);
				$speed = $distance/$travalTime;
				if($speed<100)
				{
					$milageCumilat = $milageCumilat + $distance;
				}else{
					$milageCumilat = $milageCumilat + 0;
				}
			}

			if($row['Velocity']>2 && $starttimecount == false){
				$starttimecount = true;
				$sratrtime = $row['Time'];
			}

			if($row['Velocity']>2 && $starttimecount == true){
				if(!$stoptimeset)
				{
					$getstoptime = $row['Time'];
					$stoptimeset = true;
				}
			}

			
			if($row['Velocity']<=2 && $starttimecount == true){
				$stoptimecount = true;
				$stoptime = $row['Time'];
				$e->add($this->time_diffobj($sratrtime,$stoptime));
				$starttimecount = false;
				$stoptimecount = false;
			}

			if($topspeed<$row['Velocity']){
				$topspeed=$row['Velocity'];
			}

			if($row['Velocity']>2){
				$totalvelosity=$row['Velocity']+$totalvelosity;
				$rowcount++;
			}
		$milege1 = $row['Miles'];
		
		////////////////////////////////  Runningtime Idletime Stoppedtime  //////////////////////////////
			if($row['Velocity']>2 ){
				$runningcount++;
			}
			if($row['Velocity'] <= 2 && $row['DtStatus'] > 0){
				$idlecount++;
			}
			if($row['Velocity'] <= 2 && $row['DtStatus'] == 0){
				$stoppedcount++;
			}
			
		//////////////////////////////  Tracking Intervel  ////////////////////////////////////////
		
			if($rownumber<10){
				$timestemparray[$rownumber] = $row['Time'];
			}
		$rownumber++;
		
		$passLat = $row['Latitude'];
		$passLong = $row['Longitude'];
		$passTime = $row['Time'];
		
		}

		$trackinginterval = round($this->gettimeinterval($timestemparray));
		if($trackinginterval<0)
		{
			$trackinginterval = $trackinginterval*-1;
		}
		if($rowcount==0){
			$avgvelosity=0;
		}
		else{
			$avgvelosity = $totalvelosity/$rowcount;
		}
	$avgvelosity = number_format($avgvelosity, 2, '.', '');

	//$milage_diff = ($milege2-$milege1)/1000;
	$milage_diff = round(($milageCumilat/1000),2);

	//return "<tr><td>".$sttime."</td><td>".$topspeed."</td><td>".$avgvelosity."</td><td>".$milage_diff."</td><td>".$f->diff($e)->format("%H:%I")."</td></tr>";
	
	if($rownumber==0)
	{
		return array('milage'=>'0','battery'=>$sysnoarray['battery']);
	}else{		
	$offlinemin = round(1440-($rownumber*$trackinginterval/60));
		if($offlinemin < 0)
		{
			$offlinemin = 0;
		}
		$onlinemin = round($rownumber*$trackinginterval/60);
		if($onlinemin > 1440)
		{
			$onlinemin = 1439;
		}
	return array('starttime'=>$sratrtime,'stoptime'=>$getstoptime,'busno'=>$Busno,'sysno'=>$sysno,'trackinterval'=>$trackinginterval,'date'=>$sttime,'topspeed'=>$topspeed,'avgspeed'=>$avgvelosity,'milage'=>$milage_diff,'traveltime'=>$f->diff($e)->format("%H:%I"),'runcount'=>$runningcount,'idlecount'=>$idlecount,'stopcount'=>$stoppedcount,'totalrowcount'=>$rownumber,'onlinetime'=>date('H:i',mktime(0,($onlinemin))),'offlinetime'=>date('H:i',mktime(0,($offlinemin))),'onlinemin'=>round($rownumber*$trackinginterval/60),'offlinemin'=>$offlinemin,'runmin'=>round($runningcount*$trackinginterval/60),'idlemin'=>round($idlecount*$trackinginterval/60),'stopedmin'=>round($stoppedcount*$trackinginterval/60),'runningtime'=>date('H:i',mktime(0,0,($runningcount*$trackinginterval))),'idletime'=>date('H:i',mktime(0,0,($idlecount*$trackinginterval))),'stoppedtime'=>date('H:i',mktime(0,0,($stoppedcount*$trackinginterval))),'battery'=>$sysnoarray['battery']);
	}

}
	
	
function DaywiceReport($fgmembersite,$sttime,$connection,$Busno){
$sysnoarray = $this->SysInfoArray($Busno,$connection);
	//print_r($sysnoarray);
	//exit;

	$qry="SELECT   DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result`
	FROM `tko".$sysnoarray['sysno']."`
	WHERE DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) > '".$sttime." 00:00:00.000'
	AND DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) < '".$sttime." 23:59:59.000'
	AND `Longitude` != '0'
	AND `Latitude` != '0'
	ORDER BY DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE)";
	
	//echo $qry;
	  //exit;
	$stmt = mysqli_query($connection,$qry);

	$milageCumilat = 0;
	$passLat = 0;
	$passLong = 0;
	$milege1=0;
	$milege2=0;
	$rowcount = 0;
	$totalvelosity=0;
	$topspeed=0;
	$starttimecount = false;
	$stoptimecount = false;
	$runningcount = 0;
	$idlecount = 0;
	$stoppedcount = 0;
	$timestemparray = array();
	$rownumber = 0;
	$sratrtime = '-';
	$stoptime = '-';
	$getstoptime = '-';
	$stoptimeset = false;
	
	$e = new DateTime('00:00');
	$f = clone $e;


		while($row = mysqli_fetch_array($stmt))
		{
			
			////////////////////////////  Date Topspeed  averagespeed todaymilage  Traveltime  //////////////////////////
			if($rowcount==0){
				$milege2 = $row['Miles'];
			}else{
				$date = new DateTime($passTime);
				$date2 = new DateTime($row['Time']);
				$travalTime = $date2->getTimestamp() - $date->getTimestamp();
				$distance = $this->vincentyGreatCircleDistance($passLat, $passLong, $row['Latitude'], $row['Longitude']);
				$speed = $distance/$travalTime;
				if($speed<100)
				{
					$milageCumilat = $milageCumilat + $distance;
				}else{
					$milageCumilat = $milageCumilat + 0;
				}
			}

			if($row['Velocity']>2 && $starttimecount == false){
				$starttimecount = true;
				$sratrtime = $row['Time'];
			}

			if($row['Velocity']>2 && $starttimecount == true){
				if(!$stoptimeset)
				{
					$getstoptime = $row['Time'];
					$stoptimeset = true;
				}
			}

			
			if($row['Velocity']<=2 && $starttimecount == true){
				$stoptimecount = true;
				$stoptime = $row['Time'];
				$e->add($this->time_diffobj($sratrtime,$stoptime));
				$starttimecount = false;
				$stoptimecount = false;
			}

			if($topspeed<$row['Velocity']){
				$topspeed=$row['Velocity'];
			}

			if($row['Velocity']>2){
				$totalvelosity=$row['Velocity']+$totalvelosity;
				$rowcount++;
			}
		$milege1 = $row['Miles'];
		
		////////////////////////////////  Runningtime Idletime Stoppedtime  //////////////////////////////
			if($row['Velocity']>2 ){
				$runningcount++;
			}
			if($row['Velocity'] <= 2 && $row['DtStatus'] > 0){
				$idlecount++;
			}
			if($row['Velocity'] <= 2 && $row['DtStatus'] == 0){
				$stoppedcount++;
			}
			
		//////////////////////////////  Tracking Intervel  ////////////////////////////////////////
		
			if($rownumber<10){
				$timestemparray[$rownumber] = $row['Time'];
			}
		$rownumber++;
		
		$passLat = $row['Latitude'];
		$passLong = $row['Longitude'];
		$passTime = $row['Time'];
		
		}

		$trackinginterval = round($this->gettimeinterval($timestemparray));
		if($trackinginterval<0)
		{
			$trackinginterval = $trackinginterval*-1;
		}
		if($rowcount==0){
			$avgvelosity=0;
		}
		else{
			$avgvelosity = $totalvelosity/$rowcount;
		}
	$avgvelosity = number_format($avgvelosity, 2, '.', '');

	//$milage_diff = ($milege2-$milege1)/1000;
	$milage_diff = round(($milageCumilat/1000),2);
	$milage_diff = $fgmembersite->UserUnit($milage_diff)['value'];
	$lengtUnit = $fgmembersite->UserUnit($milage_diff)['lengthUnit'];
	$speedUnit = $fgmembersite->UserUnit($milage_diff)['speedUnit'];
	//return "<tr><td>".$sttime."</td><td>".$topspeed."</td><td>".$avgvelosity."</td><td>".$milage_diff."</td><td>".$f->diff($e)->format("%H:%I")."</td></tr>";
	
	if($rownumber==0)
	{
		return array('battery'=>$sysnoarray['battery'],'milage'=>'0','speedUnit'=>$speedUnit,'lengtUnit'=>$lengtUnit);
	}else{		
	$offlinemin = round(1440-($rownumber*$trackinginterval/60));
		if($offlinemin < 0)
		{
			$offlinemin = 0;
		}
		$onlinemin = round($rownumber*$trackinginterval/60);
		if($onlinemin > 1440)
		{
			$onlinemin = 1439;
		}
	return array('speedUnit'=>$speedUnit,'lengtUnit'=>$lengtUnit,'stoptime'=>$fgmembersite->UserTimeFormat($sratrtime),'starttime'=>$fgmembersite->UserTimeFormat($getstoptime),'busno'=>$Busno,'sysno'=>$sysnoarray['sysno'],'trackinterval'=>$trackinginterval,'date'=>$fgmembersite->UserDateFormat($sttime),'topspeed'=>$fgmembersite->UserUnit($topspeed)['value'],'avgspeed'=>$fgmembersite->UserUnit($avgvelosity)['value'],'milage'=>$milage_diff,'traveltime'=>$f->diff($e)->format("%H:%I"),'runcount'=>$runningcount,'idlecount'=>$idlecount,'stopcount'=>$stoppedcount,'totalrowcount'=>$rownumber,'onlinetime'=>date('H:i',mktime(0,($onlinemin))),'offlinetime'=>date('H:i',mktime(0,($offlinemin))),'onlinemin'=>round($rownumber*$trackinginterval/60),'offlinemin'=>$offlinemin,'runmin'=>round($runningcount*$trackinginterval/60),'idlemin'=>round($idlecount*$trackinginterval/60),'stopedmin'=>round($stoppedcount*$trackinginterval/60),'runningtime'=>date('H:i',mktime(0,0,($runningcount*$trackinginterval))),'idletime'=>date('H:i',mktime(0,0,($idlecount*$trackinginterval))),'stoppedtime'=>date('H:i',mktime(0,0,($stoppedcount*$trackinginterval))),'battery'=>$sysnoarray['battery']);
	}

}
	
function vincentyGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
{
	$earthRadius = 6371000;
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $lonDelta = $lonTo - $lonFrom;
  $a = pow(cos($latTo) * sin($lonDelta), 2) +
    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

  $angle = atan2(sqrt($a), $b);
  return $angle * $earthRadius;
}
	
function DaywiceReportfull($fgmembersite,$sttime,$connection,$Busno){
$sysnoarray = $this->SysInfoArray($Busno,$connection);
	
	$qry="SELECT   DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result`
	FROM `tko".$sysnoarray['sysno']."`
	WHERE DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) > '".$sttime." 00:00:00.000'
	AND DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) < '".$sttime." 23:59:59.000'
	AND `Longitude` != '0'
	AND `Latitude` != '0'
	ORDER BY DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE)";
	
	//echo $qry;
	  
	$stmt = mysqli_query($connection,$qry);
	
	$milageCumilat = 0;
	$passLat = 0;
	$passLong = 0;
	$milege1=0;
	$milege2=0;
	$rowcount = 0;
	$totalvelosity=0;
	$topspeed=0;
	$starttimecount = false;
	$stoptimecount = false;
	$runningcount = 0;
	$idlecount = 0;
	$stoppedcount = 0;
	$timestemparray = array();
	$rownumber = 0;
	$sratrtime = '-';
	$stoptime = '-';
	$getstoptime = '-';
	$stoptimeset = false;
	$tablecontent = "";
	$status = "";
	
	$e = new DateTime('00:00');
	$f = clone $e;


		while($row = mysqli_fetch_array($stmt))
		{
			////////////////////////////  Date Topspeed  averagespeed todaymilage  Traveltime  //////////////////////////
			if($rowcount==0){
				$milege2 = $row['Miles'];
			}else{
				$date = new DateTime($passTime);
				$date2 = new DateTime($row['Time']);
				$travalTime = $date2->getTimestamp() - $date->getTimestamp();
				$distance = $this->vincentyGreatCircleDistance($passLat, $passLong, $row['Latitude'], $row['Longitude']);
				$speed = $distance/$travalTime;
				$milageCumilat = $milageCumilat + $distance;

				/*
				if($speed<100)
				{
					$milageCumilat = $milageCumilat + $distance;
				}else{
					$milageCumilat = $milageCumilat + 0;
				}
				*/
			}

			if($row['Velocity']>2 && $starttimecount == false){
				$starttimecount = true;
				$sratrtime = $row['Time'];
			}

			if($row['Velocity']>2 && $starttimecount == true){
				if(!$stoptimeset)
				{
					$getstoptime = $row['Time'];
					$stoptimeset = true;
				}
			}
			
			
			if($row['Velocity']<=2 && $starttimecount == true){
				$stoptimecount = true;
				$stoptime = $row['Time'];
				$e->add($this->time_diffobj($sratrtime,$stoptime));
				$starttimecount = false;
				$stoptimecount = false;
			}

			if($topspeed<$row['Velocity']){
				$topspeed=$row['Velocity'];
			}

			if($row['Velocity']>2){
				$totalvelosity=$row['Velocity']+$totalvelosity;
				$rowcount++;
			}
		$milege1 = $row['Miles'];
		
		////////////////////////////////  Runningtime Idletime Stoppedtime  //////////////////////////////
			if($row['Velocity']>2 ){
				$runningcount++;
				$status = "<span class='label GPSlabel-warning pull-left'>Run</span>";
			}
			if($row['Velocity'] <= 2 && $row['DtStatus'] > 0){
				$idlecount++;
				$status = "<span class='label GPSlabel-success pull-left'>Idle</span>";
			}
			if($row['Velocity'] <= 2 && $row['DtStatus'] == 0){
				$stoppedcount++;
				$status = "<span class='label GPSlabel-info pull-left'>Stoped</span>";
			}
			
		//////////////////////////////  Tracking Intervel  ////////////////////////////////////////
		
			if($rownumber<10){
				$timestemparray[$rownumber] = $row['Time'];
			}
		$rownumber++;
		
		$passLat = $row['Latitude'];
		$passLong = $row['Longitude'];
		$passTime = $row['Time'];
		/////////////////////////////////  Table content ////////////////////////////////////
		$tablecontent = $tablecontent."<tr><td>".$fgmembersite->UserTimeFormat($row['Time'])."</td><td>".$fgmembersite->UserUnit($row['Velocity'])['value'].$fgmembersite->UserUnit(0)['speedUnit']."</td><td>".$status."</td><td>".$row['Latitude']." ".$row['Longitude']."</td><td>".$fgmembersite->UserUnit(round($milageCumilat/1000))['value'].$fgmembersite->UserUnit(0)['lengthUnit']."</td><td>".$row['Oil']."</td></tr>";
		
		
		}
		
		$trackinginterval = round($this->gettimeinterval($timestemparray));
		if($trackinginterval<0)
		{
			$trackinginterval = $trackinginterval*-1;
		}
		
		if($rowcount==0){
			$avgvelosity=0;
		}
		else{
			$avgvelosity = $totalvelosity/$rowcount;
		}
	$avgvelosity = number_format($avgvelosity, 2, '.', '');

	$milage_diff = round(($milageCumilat/1000));
	$milage_diff = $fgmembersite->UserUnit($milage_diff)['value'];
	$lengtUnit = $fgmembersite->UserUnit($milage_diff)['lengthUnit'];
	$speedUnit = $fgmembersite->UserUnit($milage_diff)['speedUnit'];
	//return "<tr><td>".$sttime."</td><td>".$topspeed."</td><td>".$avgvelosity."</td><td>".$milage_diff."</td><td>".$f->diff($e)->format("%H:%I")."</td></tr>";
	if($rownumber==0)
	{
		return null;
	}else{
		$offlinemin = round(1440-($rownumber*$trackinginterval/60));
		if($offlinemin < 0)
		{
			$offlinemin = 0;
		}
		
		$onlinemin = round($rownumber*$trackinginterval/60);
		if($onlinemin > 1440)
		{
			$onlinemin = 1439;
		}
	return array('speedUnit'=>$speedUnit,'lengtUnit'=>$lengtUnit,'tablecontent'=>$tablecontent,'starttime'=>$fgmembersite->UserTimeFormat($getstoptime),'stoptime'=>$fgmembersite->UserTimeFormat($sratrtime),'busno'=>$Busno,'sysno'=>$sysno,'trackinterval'=>$trackinginterval,'date'=>$sttime,'topspeed'=>$topspeed,'avgspeed'=>$avgvelosity,'milage'=>$milage_diff,'traveltime'=>$f->diff($e)->format("%H:%I"),'runcount'=>$runningcount,'idlecount'=>$idlecount,'stopcount'=>$stoppedcount,'totalrowcount'=>$rownumber,'onlinetime'=>date('H:i',mktime(0,($onlinemin))),'offlinetime'=>date('H:i',mktime(0,($offlinemin))),'onlinemin'=>round($rownumber*$trackinginterval/60),'offlinemin'=>$offlinemin,'runmin'=>round($runningcount*$trackinginterval/60),'idlemin'=>round($idlecount*$trackinginterval/60),'stopedmin'=>round($stoppedcount*$trackinginterval/60),'runningtime'=>date('H:i',mktime(0,0,($runningcount*$trackinginterval))),'idletime'=>date('H:i',mktime(0,0,($idlecount*$trackinginterval))),'stoppedtime'=>date('H:i',mktime(0,0,($stoppedcount*$trackinginterval))));
	}
}
	
	
	
function gettimeinterval($timestamps)
{
	$totaltimediff = 0;
	for($i=0;$i<count($timestamps);$i++)
	{
		if($i!=0)
		{
			$timeFirst  = strtotime($passtimestemp);
			$timeSecond = strtotime($timestamps[$i]);
			$totaltimediff = ($timeFirst-$timeSecond) + $totaltimediff;
		}
		$passtimestemp = $timestamps[$i];
	}
	return $totaltimediff/9;
}



function time_diffobj($time1,$time2){
	if($time1!=null && $time2!=null && $time1!="" && $time2!="" && $time1!="--" && $time2!="--"){
			$now = new DateTime($time1);
			$exp = new DateTime($time2);

			$diff = $now->diff($exp);
			//printf('%d hours, %d minutes, %d seconds', $diff->h, $diff->i, $diff->s);

			if($diff->d<=0){
			$days="";
			}
			else{
			$days=$diff->d."d ";
			}
			//return $days.$diff->h.":".$diff->i;
			return $diff;
	}
	else{
		return "--";
	}
}

//////////////////////////////////  Playback Report  /////////////////////////////

	function PlaybackReport($fgmembersite,$VehicleNo,$StDate,$EndDate)
	{
		
		$sysnoarray = $this->SysInfoArray($VehicleNo,$fgmembersite->connection);
		$sysno = $sysnoarray['sysno'];
		if(!$sysnoarray)
		{
			return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
		}

		
		$sttime= $StDate;
		$endtime= $EndDate;
		
		$Vehicleinfolist=array('Vehicleinfo'=>$this->PlaybackReportCreate($fgmembersite,$sysnoarray,$sttime,$endtime,$fgmembersite->connection));
		
		return json_encode($Vehicleinfolist);
	}

	function ConfusedMilagesTZ($stdatetime,$enddatetime,$sysno,$timezone,$connection)
	{
		$Startmilage = 0;
		$Maxmilage = 0;
		$Lasttmilage = 0;

		//$qry = "SELECT `Time`, `Miles` FROM `tko$sysno` WHERE `Time` > '$date 00:00:00' AND `Time` < '$date 23:59:59' ORDER BY `Time` LIMIT 1";
		$qry = "SELECT DATE_ADD(`Time`, INTERVAL $timezone MINUTE) AS Time, `Miles` FROM `tko$sysno` WHERE DATE_ADD(`Time`, INTERVAL $timezone MINUTE) > '$stdatetime' AND DATE_ADD(`Time`, INTERVAL $timezone MINUTE) < '$enddatetime' ORDER BY `Time` LIMIT 1";
		//echo $qry;
		$stmt = mysqli_query($connection,$qry);
		while($row = mysqli_fetch_array($stmt))
		{
			$Startmilage = $row['Miles'];
		}
		//$qry = "SELECT `Time`, `Miles` FROM `tko$sysno` WHERE `Time` > '$date 00:00:00' AND `Time` < '$date 23:59:59' ORDER BY `Miles` DESC LIMIT 1";
		$qry = "SELECT DATE_ADD(`Time`, INTERVAL $timezone MINUTE) AS Time, `Miles` FROM `tko$sysno` WHERE DATE_ADD(`Time`, INTERVAL $timezone MINUTE) > '$stdatetime' AND DATE_ADD(`Time`, INTERVAL $timezone MINUTE) < '$enddatetime' ORDER BY `Miles` DESC LIMIT 1";
		//echo $qry;
		$stmt = mysqli_query($connection,$qry);
		while($row = mysqli_fetch_array($stmt))
		{
			$Maxmilage = $row['Miles'];
		}
		//$qry = "SELECT `Time`, `Miles` FROM `tko$sysno` WHERE `Time` > '$date 00:00:00' AND `Time` < '$date 23:59:59' ORDER BY `Time` DESC LIMIT 1";
		$qry = "SELECT DATE_ADD(`Time`, INTERVAL $timezone MINUTE) AS Time, `Miles` FROM `tko$sysno` WHERE DATE_ADD(`Time`, INTERVAL $timezone MINUTE) > '$stdatetime' AND DATE_ADD(`Time`, INTERVAL $timezone MINUTE) < '$enddatetime' ORDER BY `Time` DESC LIMIT 1";
		$stmt = mysqli_query($connection,$qry);
		while($row = mysqli_fetch_array($stmt))
		{
			$Lasttmilage = $row['Miles'];
		}
		//echo $qry;
		return ($Maxmilage - $Startmilage + $Lasttmilage);
	}

	function NotificPlaybackReport($VehicleNo,$StDate,$EndDate,$connection,$ext_con)
	{
		
		$sysnoarray = $this->SysInfoArray($VehicleNo,$connection);
		
		$sysno = $sysnoarray['sysno'];
		if(!$sysnoarray)
		{
			return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
		}

		
		$sttime= $StDate;
		$endtime= $EndDate;
		//$this->NotificPlaybackReportCreate($sysnoarray,$sttime,$endtime,$ext_con);
		$Notificfolist=array('Notificinfo'=>$this->NotificPlaybackReportCreate($sysnoarray,$sttime,$endtime,$ext_con));

		return json_encode($Notificfolist);
	}
	
function NotificPlaybackReportCreate($sysnoarray,$sttime,$endtime,$connection)
{
	$notificinfo = array();

	$qry = "SELECT B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, `PicPath`, DATE_ADD(`notifydate`, INTERVAL B.`tz` MINUTE) AS notifydatetz, C.longitude, C.latitude
	FROM `gpsmntcenotify` AS A 
	LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno 
	RIGHT JOIN `alerts` AS C ON A.`PicPath` = C.`picname`
	WHERE `imei` = '".$sysnoarray['sysno']."'
	AND DATE_ADD(`notifydate`, INTERVAL ".$sysnoarray['tz']." MINUTE) <= '$endtime' 
	AND DATE_ADD(`notifydate`, INTERVAL ".$sysnoarray['tz']." MINUTE) >= '$sttime'
	AND DATE_ADD(`notifydate` , INTERVAL 30 DAY) > NOW()
	ORDER BY `notifydate` DESC";

	//$qry = "SELECT B.objname AS vehicleNo, A.id, `imei`, `user`, `subject`, `msgbody`, `isshow`, `priority`, `notifydate` FROM `gpsmntcenotify` AS A LEFT JOIN trackingobjects AS B ON A.imei = B.objsysno WHERE `user` = '".$this->UserName()."' OR `user` = '' ORDER BY `notifydate` DESC LIMIT 100";
	
	$stmt = mysqli_query($connection,$qry);
	
	while($row = mysqli_fetch_array($stmt))
	{
		$date = date('Y-m-d', strtotime($row['notifydatetz']));

		$notificinfo[] = array('long'=>$row['longitude'],'lat'=>$row['latitude'],'id'=>$row['id'],'notificTime'=>$row['notifydatetz'],'notificname'=>$row['vehicleNo'],'notificsubject'=>$row['subject'],'notificaddress'=>$row['msgbody'],'priority'=>$row['priority'],'notificlatitudes'=>0,'notificlogitude'=>0, 'PicPath'=>$row['PicPath']);
		
	}
	return $notificinfo;
}
function PlaybackReportCreate($fgmembersite,$sysnoarray,$sttime,$endtime,$connection)
{
		$qry="SELECT DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result` 
		FROM `tko".$sysnoarray['sysno']."` 
		WHERE DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) <= '$endtime' 
		AND DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) >= '$sttime'
		AND `Longitude` != '0'
		AND `Latitude` != '0'
		ORDER BY DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE)";
//echo $qry;

		$pass_geo_time = "";
		$stmt = mysqli_query($connection,$qry);

	if(!$stmt){
		return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
	}
	
	$sql_rec_count = mysqli_num_rows($result);


	

		
	$pass_point = '';
	$start_loop = 1;
	$record_count = 0;
	$total_record_count = 0;
	$point='';
	$velosity='';
	$tablebody = "";
	$row_count = 0;
	$lastplusmilage = 0;
		while($row = mysqli_fetch_array($stmt))
	{
		if($row_count == 0)
		{
			$firstmilage = 0;
		}
		
		if($row_count==0){
			$milageCumilat = 0;
			$milage = 0;
		}else{
			$date = new DateTime($passTime);
			$date2 = new DateTime($row['Time']);
			$travalTime = $date2->getTimestamp() - $date->getTimestamp();
			$distance = $this->vincentyGreatCircleDistance($passLat, $passLng, $row['Latitude'], $row['Longitude']);
			//echo $passLat." ".$passLng." ".$row['Latitude']." ".$row['Longitude']."<br>";
			//echo $distance."<br>";
			$milageCumilat = $milageCumilat + $distance;

			/*
			$speed = $distance/$travalTime;
			if($speed<200)
			{
				$milageCumilat = $milageCumilat + $distance;
			}else{
				$milageCumilat = $milageCumilat + 0;
			}
			*/
			$milage = $milageCumilat;
		}
		//$milage = round(($milage/1000)*0.621371);		//Convert to miles
		
		
		
		$row_count++;
		
		$total_record_count++;
		$pass_point=$point;
		$passLat = $row['Latitude'];
		$passLng = $row['Longitude'];
		$point = $row['Latitude'].",".$row['Longitude'];
		$pass_velosity=$velosity;
		$velosity=$row['Velocity'];
		
		if($start_loop==1){
			if ($row['Velocity']==0){
				$pass_point=$point;
				$passLat = $row['Latitude'];
				$passLng = $row['Longitude'];
				$start_count = 1;
				$pass_stop_time = $row['Time'];
			}
			$start_loop=0;
		}



		if ($row['Velocity']==0){
			$start_count = 1;
		}
		else
		{
			if ($start_count==1){
				$record_count++;
				$Vehicleinfo[] = $this->PlaybackCreate_stopping_row($fgmembersite,$milage,$row['Time'],$pass_point,$pass_stop_time,$record_count,$pass_velosity,$sysnoarray['image'],$row['Oil'],$passLat,$passLng,$row['Angle'],$row['DtStatus']);
				}
			$record_count++;
			$Vehicleinfo[] = $this->PlaybackCreate_stopping_row($fgmembersite,$milage,$row['Time'],$point,$row['Time'],$record_count,$row['Velocity'],$sysnoarray['image'],$row['Oil'],$row['Latitude'],$row['Longitude'],$row['Angle'],$row['DtStatus']);
			$pass_stop_time = $row['Time'];
			$start_count = 0;
		}

	if ($total_record_count==$sql_rec_count){
			if ($start_count==1){
				$record_count++;
				$Vehicleinfo[] = $this->PlaybackCreate_stopping_row($fgmembersite,$milage,$row['Time'],$point,$pass_stop_time,$record_count,$pass_velosity,$sysnoarray['image'],$row['Oil'],$row['Latitude'],$row['Longitude'],$row['Angle'],$row['DtStatus']);
				}
			$pass_stop_time = $row['Time'];
			$start_count = 0;
	}
		

	}
	return $Vehicleinfo;
}

function PlaybackCreate_stopping_row($fgmembersite,$milage,$time,$point,$pass_stop_time,$record_count,$velosity,$image,$Oil,$Lat,$lng,$Angle,$DtStatus)
{
	$PassTimeDisplay = $fgmembersite->UserTimeFormat($pass_stop_time);
	$TimeDisplay = $fgmembersite->UserTimeFormat($time);
	$TimeOnlyDisplay = date("H:i:s", strtotime($pass_stop_time));
	//$PassTimeDisplay = $pass_stop_time;
	//$TimeDisplay = $time;
	$milage = $fgmembersite->UserUnit(round($milage/1000))['value'].$fgmembersite->UserUnit(0)['lengthUnit'];

		$VelocityRow = $velosity;
		if ($velosity==0){
			//echo $pass_stop_time." ".$time."<br>";
			$velosity = $fgmembersite->UserUnit(round($velosity))['value'].$fgmembersite->UserUnit(0)['speedUnit'];
			return array('mapimage'=>$image,'Oil'=>$Oil,'Miles'=>$milage,'StartTime'=>$TimeOnlyDisplay,'time'=>$PassTimeDisplay,'lng'=>$lng,'lat'=>$Lat,'VelocityRow'=>$VelocityRow,'Velocity'=>$velosity,'Angle'=>$Angle,'DtStatus'=>$DtStatus,'park'=>$this->time_diff($pass_stop_time,$time),'status'=>'Stop');

		//return "<tr><td>".$pass_stop_time."  -  ".$time."</td><td>".$velosity."</td><td>Stop</td><td>".$milage."</td><td><div id='".$record_count."address'>".$point."</div></td><td>".$this->time_diff($pass_stop_time,$time)."</td></tr>";
		}
		else {
			$velosity = $fgmembersite->UserUnit(round($velosity))['value'].$fgmembersite->UserUnit(0)['speedUnit'];
			return array('mapimage'=>$image,'Oil'=>$Oil,'Miles'=>$milage,'StartTime'=>$TimeOnlyDisplay,'time'=>$PassTimeDisplay,'lng'=>$lng,'lat'=>$Lat,'VelocityRow'=>$VelocityRow,'Velocity'=>$velosity,'Angle'=>$Angle,'DtStatus'=>$DtStatus,'park'=>$this->time_diff($pass_stop_time,$time),'status'=>'Run');
		//return "<tr><td>".$pass_stop_time."</td><td>".$velosity."</td><td>Run</td><td>".$milage."</td><td><div id='".$record_count."address'>".$point."</div></td><td>".$this->time_diff($pass_stop_time,$time)."</td></tr>";
		}
}

///////////////////////////////  Playback report End  ////////////////////////////


//////////////////////////////	App Report Operation	//////////////////////////
function AppReportOperation($VehicleNo,$StDate,$EndDate,$fgmembersite)
{
	
	$sysnoarray = $this->SysInfoArray($VehicleNo,$fgmembersite->connection);
	$sysno = $sysnoarray['sysno'];
	if(!$sysnoarray)
	{
		return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
	}

	//print_r($sysnoarray);
	//exit;
	
	$sttime= $StDate;
	$endtime= $EndDate;
	
	$Vehicleinfolist=array('Vehicleinfo'=>$this->AppReportOpeCreate($sysnoarray,$sttime,$endtime,$fgmembersite));
	
	return $Vehicleinfolist;
}

function AppReportOpeCreate($sysnoarray,$sttime,$endtime,$fgmembersite)
{
		$qry="SELECT DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result` 
		FROM `tko".$sysnoarray['sysno']."` 
		WHERE DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) <= '$endtime' 
		AND DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) >= '$sttime'
		AND `Longitude` != '0'
		AND `Latitude` != '0'
		ORDER BY DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE)";
//echo $qry;

		$pass_geo_time = "";
		$stmt = mysqli_query($fgmembersite->connection,$qry);

	if(!$stmt){
		return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
	}
	
	$sql_rec_count = mysqli_num_rows($stmt);
//echo "Record count ".$sql_rec_count."<br>";

	

	
$pass_point = '';
$start_loop = 1;
$record_count = 0;
$total_record_count = 0;
$point='';
$velosity='';
$tablebody = "";
$row_count = 0;
$lastplusmilage = 0;
	while($row = mysqli_fetch_array($stmt))
{
	if($row_count == 0)
	{
		$firstmilage = 0;
	}
	

	
	
	if($row_count==0){
		$milageCumilat = 0;
		$milage = 0;
	}else{
		$date = new DateTime($passTime);
		$date2 = new DateTime($row['Time']);
		$travalTime = $date2->getTimestamp() - $date->getTimestamp();
		$distance = $this->vincentyGreatCircleDistance($passLat, $passLng, $row['Latitude'], $row['Longitude']);
		//echo $passLat." ".$passLng." ".$row['Latitude']." ".$row['Longitude']."<br>";
		//echo $distance."<br>";
		$speed = $distance/$travalTime;
		if($speed<100)
		{
			$milageCumilat = $milageCumilat + $distance;
		}else{
			$milageCumilat = $milageCumilat + 0;
		}
		$milage = $milageCumilat;
	}
	$row_count++;
	
	$total_record_count++;
	$pass_point=$point;
	$passLat = $row['Latitude'];
	$passLng = $row['Longitude'];
	$point = $row['Latitude'].",".$row['Longitude'];
	$pass_velosity=$velosity;
	$velosity=$row['Velocity'];
	
	if($start_loop==1){
		if ($row['Velocity']==0){
			$pass_point=$point;
			$passLat = $row['Latitude'];
			$passLng = $row['Longitude'];
			$start_count = 1;
			$pass_stop_time = $row['Time'];
		}
		$start_loop=0;
	}



	if ($row['Velocity']==0){
		$start_count = 1;
	}
	else
	{
		if ($start_count==1){
			$record_count++;
			$Vehicleinfo[] = $this->AppReportOpeCreate_stopping_row($fgmembersite,$milage,$row['Time'],$pass_point,$pass_stop_time,$record_count,$pass_velosity,$sysnoarray['image'],$row['Oil'],$passLat,$passLng,$row['Angle'],$row['DtStatus']);
			}
		$record_count++;
		$Vehicleinfo[] = $this->AppReportOpeCreate_stopping_row($fgmembersite,$milage,$row['Time'],$point,$row['Time'],$record_count,$row['Velocity'],$sysnoarray['image'],$row['Oil'],$row['Latitude'],$row['Longitude'],$row['Angle'],$row['DtStatus']);
		$pass_stop_time = $row['Time'];
		$start_count = 0;
	}

if ($total_record_count==$sql_rec_count){
		if ($start_count==1){
			$record_count++;
			$Vehicleinfo[] = $this->AppReportOpeCreate_stopping_row($fgmembersite,$milage,$row['Time'],$point,$pass_stop_time,$record_count,$pass_velosity,$sysnoarray['image'],$row['Oil'],$row['Latitude'],$row['Longitude'],$row['Angle'],$row['DtStatus']);
			}
		$pass_stop_time = $row['Time'];
		$start_count = 0;
}
	

}
return $Vehicleinfo;
}


function AppReportOpeCreate_stopping_row($fgmembersite,$milage,$time,$point,$pass_stop_time,$record_count,$velosity,$image,$Oil,$Lat,$lng,$Angle,$DtStatus)
{
$PassTimeDisplay = $fgmembersite->UserTimeFormat($pass_stop_time);
$TimeDisplay = $fgmembersite->UserTimeFormat($time);
$TimeOnlyDisplay = date("H:i:s", strtotime($pass_stop_time));
$TimeHour = date("H", strtotime($pass_stop_time));
//$PassTimeDisplay = $pass_stop_time;
//$TimeDisplay = $time;
	
	$milage = $fgmembersite->UserUnit(round($milage/1000))['value'].$fgmembersite->UserUnit(0)['lengthUnit'];

	if ($velosity==0){
		$velosity = $fgmembersite->UserUnit(round($velosity))['value'].$fgmembersite->UserUnit(0)['speedUnit'];
		return array('mapimage'=>$image,'Oil'=>$Oil,'Miles'=>round($milage),'StartTime'=>$TimeOnlyDisplay,'time'=>"(".$PassTimeDisplay.")<br>(".$TimeDisplay.")",'lng'=>$lng,'lat'=>$Lat,'hour'=>$TimeHour,'address'=>$Lat.",".$lng,'Velocity'=>$velosity,'Angle'=>$Angle,'DtStatus'=>$DtStatus,'park'=>$this->time_diff($pass_stop_time,$time),'status'=>'Stop');
		//$this->NominatimRevCordinates($Lat,$lng)
	//return "<tr><td>".$pass_stop_time."  -  ".$time."</td><td>".$velosity."</td><td>Stop</td><td>".$milage."</td><td><div id='".$record_count."address'>".$point."</div></td><td>".$this->time_diff($pass_stop_time,$time)."</td></tr>";
	}
	else {
		$velosity = $fgmembersite->UserUnit(round($velosity))['value'].$fgmembersite->UserUnit(0)['speedUnit'];
		return array('mapimage'=>$image,'Oil'=>$Oil,'Miles'=>round($milage),'StartTime'=>$TimeOnlyDisplay,'time'=>"(".$PassTimeDisplay.")",'lng'=>$lng,'lat'=>$Lat,'hour'=>$TimeHour,'address'=>$Lat.",".$lng,'Velocity'=>$velosity,'Angle'=>$Angle,'DtStatus'=>$DtStatus,'park'=>$this->time_diff($pass_stop_time,$time),'status'=>'Run');
	//return "<tr><td>".$pass_stop_time."</td><td>".$velosity."</td><td>Run</td><td>".$milage."</td><td><div id='".$record_count."address'>".$point."</div></td><td>".$this->time_diff($pass_stop_time,$time)."</td></tr>";		$this->NominatimRevCordinates($Lat,$lng)
	}
}
//////////////////////////////	App Report Operation End	//////////////////////////

//////////////////////////////////  Parking Report  /////////////////////////////

function ParkingReport($postvalue,$connection,$fgmembersite)
{
	
	$sysnoarray = $this->SysInfoArray($postvalue['select'],$connection);
	$sysno = $sysnoarray['sysno'];
	if(!$sysnoarray)
	{
		return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
	}
	$earlier = new DateTime($postvalue['st_date']);
	$later = new DateTime($postvalue['end_date']);

	$diff = $later->diff($earlier)->format("%a");

	if($diff>=32)
	{
		return "<tr><td colspan='6'><font color='red'>Maximum allowed 31 days. Please correct the inputs</font></td></tr>";
	}
	$sttime= $postvalue['st_date']." 00:00:00";
	$endtime= $postvalue['end_date']." 23:59:00";
	return $this->DrivingReportCreate_Parking($fgmembersite,$sysnoarray,$sttime,$endtime,$connection,"Table");		//Array
}
function ParkingReportApp($postvalue,$connection,$fgmembersite)
{
	
	$sysnoarray = $this->SysInfoArray($postvalue['VehicleNo'],$connection);

	$sysno = $sysnoarray['sysno'];
	if(!$sysnoarray)
	{
		$fgmembersite->HandleError("This vehicle is not available");
		return null;
	}
	$earlier = new DateTime($postvalue['st_date']);
	$later = new DateTime($postvalue['end_date']);

	$diff = $later->diff($earlier)->format("%a");

	if($diff>=32)
	{
		$fgmembersite->HandleError("Maximum duration is 31 days");
		return null;
	}
	$sttime= $postvalue['st_date'];
	$endtime= $postvalue['end_date'];
	return $this->DrivingReportCreate_Parking($fgmembersite,$sysnoarray,$sttime,$endtime,$connection,"Array");		//Array
}
function DrivingReportCreate_Parking($fgmembersite,$sysnoarray,$sttime,$endtime,$connection,$responseType)
{
		$qry="SELECT DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result` 
		FROM `tko".$sysnoarray['sysno']."` 
		WHERE DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) <= '$endtime' 
		AND DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) >= '$sttime'
		ORDER BY DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE)";

		$pass_geo_time = "";
		$stmt = mysqli_query($connection,$qry);
		
		$sql_rec_count = mysqli_num_rows($stmt);
		
	if(!$stmt || $sql_rec_count == 0){
		
		if($responseType == "Table")
		{
			return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
		}if($responseType == "Array")
		{
			$fgmembersite->HandleError("No data available");
			return null;
		}
		
		
		
	}
	
	


	
$milageCumilat = 0;
$passLat = 0;
$passLong = 0;
$milege1=0;
$milege2=0;
$rowcount = 0;
	
$pass_point = '';
$start_loop = 1;
$record_count = 0;
$total_record_count = 0;
$point='';
$velosity='';
$tablebody = "";
$ReportArray = array();

	while($row = mysqli_fetch_array($stmt))
{
			////////////////////////////  Date Topspeed  averagespeed todaymilage  Traveltime  //////////////////////////
			if($rowcount==0){
				$milege2 = $row['Miles'];
			}else{
				$date = new DateTime($passTime);
				$date2 = new DateTime($row['Time']);
				$travalTime = $date2->getTimestamp() - $date->getTimestamp();
				$distance = $this->vincentyGreatCircleDistance($passLat, $passLong, $row['Latitude'], $row['Longitude']);
				$speed = $distance/$travalTime;
				if($speed<100)
				{
					$milageCumilat = $milageCumilat + $distance;
				}else{
					$milageCumilat = $milageCumilat + 0;
				}
			}
			
	$rowcount++;
	$total_record_count++;
	$pass_point=$point;
	$point = $row['Latitude'].",".$row['Longitude'];
	$pass_velosity=$velosity;
	$velosity=$row['Velocity'];
	if($start_loop==1){
		if ($row['Velocity']==0){
			$pass_point=$point;
			$start_count = 1;
			$pass_stop_time = $row['Time'];
		}
		$start_loop=0;
	}

		$passLat = $row['Latitude'];
		$passLong = $row['Longitude'];
		$passTime = $row['Time'];


	if ($row['Velocity']==0){
		$start_count = 1;
	}
	else
	{
		if ($start_count==1){
			$record_count++;
			$tablebody = $tablebody.$this->create_stopping_row_Parking($fgmembersite,$milageCumilat,$row['Time'],$pass_point,$pass_stop_time,$record_count,$pass_velosity,"Table");
			$rowArray = $this->create_stopping_row_Parking($fgmembersite,$milageCumilat,$row['Time'],$pass_point,$pass_stop_time,$record_count,$pass_velosity,"Array");
				if($rowArray != null)
				{
					$ReportArray[] = $rowArray;
				}
			}
		$record_count++;
		$tablebody = $tablebody.$this->create_stopping_row_Parking($fgmembersite,$milageCumilat,$row['Time'],$point,$row['Time'],$record_count,$row['Velocity'],"Table");
		$rowArray = $this->create_stopping_row_Parking($fgmembersite,$milageCumilat,$row['Time'],$point,$row['Time'],$record_count,$row['Velocity'],"Array");
				if($rowArray != null)
				{
					$ReportArray[] = $rowArray;
				}
		$pass_stop_time = $row['Time'];
		$start_count = 0;
	}

if ($total_record_count==$sql_rec_count){
		if ($start_count==1){
			$record_count++;
			$tablebody = $tablebody.$this->create_stopping_row_Parking($fgmembersite,$milageCumilat,$row['Time'],$point,$pass_stop_time,$record_count,$pass_velosity,"Table");
			$rowArray = $this->create_stopping_row_Parking($fgmembersite,$milageCumilat,$row['Time'],$point,$row['Time'],$record_count,$row['Velocity'],"Array");
				if($rowArray != null)
				{
					$ReportArray[] = $rowArray;
				}
			}
		$pass_stop_time = $row['Time'];
		$start_count = 0;
}
	

}
if($responseType == "Table")
{
	return $tablebody;
}if($responseType == "Array")
{
	return $ReportArray;
}else{
	return null;
}
}

function create_stopping_row_Parking($fgmembersite,$milage,$time,$point,$pass_stop_time,$record_count,$velosity,$responseType)
{
	//$fgmembersite->UserUnit(round($milageCumilat/1000))['value'].$fgmembersite->UserUnit(0)['lengthUnit']
	$TimeDiff = $this->time_diff($pass_stop_time,$time);
	$TimeDiffHM = explode(":",$TimeDiff);
	$TimeDiffMin = $TimeDiffHM[0]*60+$TimeDiffHM[1];
	$milage = $fgmembersite->UserUnit(round($milage/1000))['value'].$fgmembersite->UserUnit(0)['lengthUnit'];
	$hour = explode(":",explode(" ",$pass_stop_time)[1])[0];
	
	if($responseType == "Table")
	{
		if($TimeDiffMin >= 3){
			if ($velosity==0){
				return "<tr><td>".$fgmembersite->UserTimeFormat($pass_stop_time)."  -  ".$fgmembersite->UserTimeFormat($time)."</td><td>".$fgmembersite->UserUnit($velosity)['value'].$fgmembersite->UserUnit(0)['speedUnit']."</td><td>Stop</td><td>".$fgmembersite->UserUnit($milage)['value'].$fgmembersite->UserUnit(0)['lengthUnit']."</td><td>".$point."</td><td>".$TimeDiff."</td></tr>";
			}
			else {
				return "<tr><td>".$fgmembersite->UserTimeFormat($pass_stop_time)."</td><td>".$fgmembersite->UserUnit($velosity)['value'].$fgmembersite->UserUnit(0)['speedUnit']."</td><td>Run</td><td>".$fgmembersite->UserUnit($milage)['value'].$fgmembersite->UserUnit(0)['lengthUnit']."</td><td>".$point."</td><td>".$TimeDiff."</td></tr>";
			}
		}else{
			return "";
		}
	}if($responseType == "Array")
	{
		if($TimeDiffMin >= 3){
			if ($velosity==0){
				return array('hour'=>$hour,'startTime'=>$fgmembersite->UserTimeFormat($pass_stop_time),'endTime'=>$fgmembersite->UserTimeFormat($time),'velosity'=>$fgmembersite->UserUnit($velosity)['value'].$fgmembersite->UserUnit(0)['speedUnit'],'milage'=>$fgmembersite->UserUnit($milage)['value'].$fgmembersite->UserUnit(0)['lengthUnit'],'point'=>$point,'timeDiff'=>$TimeDiff);
			}
			else {
				return array('hour'=>$hour,'startTime'=>$fgmembersite->UserTimeFormat($pass_stop_time),'velosity'=>$fgmembersite->UserUnit($velosity)['value'].$fgmembersite->UserUnit(0)['speedUnit'],'milage'=>$fgmembersite->UserUnit($milage)['value'].$fgmembersite->UserUnit(0)['lengthUnit'],'point'=>$point,'timeDiff'=>$TimeDiff);
			}
		}else{
			return null;
		}
	}else{
		return null;
	}
}


///////////////////////////////  Parking report End  ////////////////////////////


//////////////////////////////////  Driving Report  /////////////////////////////

function DrivingReport($postvalue,$fgmembersite,$connection)
{
	
	$sysnoarray = $this->SysInfoArray($postvalue['select'],$connection);
	$sysno = $sysnoarray['sysno'];
	if(!$sysnoarray)
	{
		return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
	}
	$earlier = new DateTime($postvalue['st_date']);
	$later = new DateTime($postvalue['end_date']);

	$diff = $later->diff($earlier)->format("%a");

	if($diff>=32)
	{
		return "<tr><td colspan='6'><font color='red'>Maximum allowed 31 days. Please correct the inputs</font></td></tr>";
	}
	$sttime= $postvalue['st_date']." 00:00:00";
	$endtime= $postvalue['end_date']." 23:59:00";
	return $this->DrivingReportCreate($sysnoarray,$sttime,$endtime,$fgmembersite,$connection);
}

function DrivingReportCreate($sysnoarray,$sttime,$endtime,$fgmembersite,$connection)
{
		$qry="SELECT DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result` 
		FROM `tko".$sysnoarray['sysno']."` 
		WHERE DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) <= '$endtime' 
		AND DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) >= '$sttime'
		ORDER BY DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE)";

		$pass_geo_time = "";
		$stmt = mysqli_query($connection,$qry);

	if(!$stmt){
		return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
	}
	
	$sql_rec_count = mysqli_num_rows($stmt);


	

	
$pass_point = '';
$start_loop = 1;
$record_count = 0;
$total_record_count = 0;
$point='';
$velosity='';
$tablebody = "";
$StartMilage = 0;
$row_count=0;

	while($row = mysqli_fetch_array($stmt))
{
//echo $row['Longitude'];
	$total_record_count++;
	$pass_point=$point;
	$point = $row['Longitude'].",".$row['Latitude'];
	$pass_velosity=$velosity;
	$velosity=$row['Velocity'];
	

	
	if($start_loop==1){
		if ($row['Velocity']==0){
			$pass_point=$point;
			$start_count = 1;
			$pass_stop_time = $row['Time'];
		}
		$start_loop=0;
	}
	
	if($row_count==0){
		$milageCumilat = 0;
		$milage = 0;
	}else{
		$date = new DateTime($passTime);
		$date2 = new DateTime($row['Time']);
		$travalTime = $date2->getTimestamp() - $date->getTimestamp();
		$distance = $this->vincentyGreatCircleDistance($passLat, $passLng, $row['Latitude'], $row['Longitude']);
		//echo $passLat." ".$passLng." ".$row['Latitude']." ".$row['Longitude']."<br>";
		//echo $distance."<br>";
		$speed = $distance/$travalTime;
		$milageCumilat = $milageCumilat + $distance;

		/*
		if($speed<100)
		{
			$milageCumilat = $milageCumilat + $distance;
		}else{
			$milageCumilat = $milageCumilat + 0;
		}
		*/
		$milage = $milageCumilat;
	}
	//$milage = round(($milage/1000)*0.621371);		//Convert to miles
	$row_count++;
	$passLat = $row['Latitude'];
	$passLng = $row['Longitude'];
	
	
	
	if ($row['Velocity']==0){
		$start_count = 1;
	}
	else
	{
		if ($start_count==1){
			$record_count++;
			$tablebody = $tablebody.$this->create_stopping_row($fgmembersite,$milage,$row['Time'],$pass_point,$pass_stop_time,$record_count,$pass_velosity);
			}
		$record_count++;
		$tablebody = $tablebody.$this->create_stopping_row($fgmembersite,$milage,$row['Time'],$point,$row['Time'],$record_count,$row['Velocity']);
		$pass_stop_time = $row['Time'];
		$start_count = 0;
	}

	if ($total_record_count==$sql_rec_count){
			if ($start_count==1){
				$record_count++;
				$tablebody = $tablebody.$this->create_stopping_row($fgmembersite,$milage,$row['Time'],$point,$pass_stop_time,$record_count,$pass_velosity);
				}
			$pass_stop_time = $row['Time'];
			$start_count = 0;
	}
	

}
return $tablebody;
}

function create_stopping_row($fgmembersite,$milage,$time,$point,$pass_stop_time,$record_count,$velosity)
{

	if ($velosity==0){
	return "<tr><td>".$fgmembersite->UserTimeFormat($pass_stop_time)."  -  ".$fgmembersite->UserTimeFormat($time)."</td><td>".$fgmembersite->UserUnit($velosity)['value'].$fgmembersite->UserUnit(0)['speedUnit']."</td><td>Stop</td><td>".$fgmembersite->UserUnit(round($milage/1000))['value'].$fgmembersite->UserUnit(0)['lengthUnit']."</td><td>".$point."</td><td>".$this->time_diff($pass_stop_time,$time)."</td></tr>";
	}

	else {
	return "<tr><td>".$fgmembersite->UserTimeFormat($pass_stop_time)."</td><td>".$fgmembersite->UserUnit($velosity)['value'].$fgmembersite->UserUnit(0)['speedUnit']."</td><td>Run</td><td>".$fgmembersite->UserUnit(round($milage/1000))['value'].$fgmembersite->UserUnit(0)['lengthUnit']."</td><td>".$point."</td><td>".$this->time_diff($pass_stop_time,$time)."</td></tr>";
	}
}


///////////////////////////////  Driving report End  ////////////////////////////

///////////////////////////////		App Trip Report	/////////////////////////////////
function WebTripReport($VehicleNo,$StDate,$EndDate,$fgmembersite)
{
	$StDate = $StDate." 00:00:01";
	$EndDate = $EndDate." 23:59:59";
	$TripReportArray = $this->AppTripReportOperation($VehicleNo,$StDate,$EndDate,$fgmembersite);
	//print_r($TripReportArray);
	//exit;
	
	$TableStr = null;
	for($i=0;$i<count($TripReportArray['Vehicleinfo']);$i++)
	{
		$RepArrayHead = $TripReportArray['Vehicleinfo'][$i];
		
		if($RepArrayHead['TripStartTimeDisplay'] == 0)
		{
			//continue;
		}

		$TableStr = $TableStr."<tr><td>".$RepArrayHead['TripStartTimeDisplay']."</td><td>".$RepArrayHead['StartAddress']."</td><td>".$RepArrayHead['TripEndTimeDisplay']."</td><td>".$RepArrayHead['EndAddress']."</td><td>".$RepArrayHead['TripDuration']."</td><td>".$RepArrayHead['TripMilage']."</td></tr>";
	}
	return $TableStr;
}

function AppTripReportOperation($VehicleNo,$StDate,$EndDate,$fgmembersite)
{
	
	$sysnoarray = $this->SysInfoArray($VehicleNo,$fgmembersite->connection);
	$sysno = $sysnoarray['sysno'];
	if(!$sysnoarray)
	{
		return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
	}

	//print_r($sysnoarray);
	//exit;
	
	$sttime= $StDate;
	$endtime= $EndDate;
	
	//$Vehicleinfolist=array('Vehicleinfo'=>$this->AppTripReportOpeCreate($sysnoarray,$sttime,$endtime,$fgmembersite));
	
	$Vehicleinfolist = $this->AppTripReportOpeCreate($sysnoarray,$sttime,$endtime,$fgmembersite);
	
	//print_r($Vehicleinfolist);
	//exit;
	
	$startMilage = 0;
	$LastTripEndTime = "-";
	$LastTripEndTimeDisplay = "-";
	$TripValues = array();
	for($i = 0; $i < count($Vehicleinfolist); $i++)
	{
		//echo $Vehicleinfolist[$i]['StartTime']."<br>";
		
		if($LastTripEndTime == "-")
		{
			$LastTripEndTime = $Vehicleinfolist[$i]['StartTime'];
			$LastTripEndTimeDisplay = $Vehicleinfolist[$i]['StartTimeDisplay'];
			$LastTripLat = $Vehicleinfolist[$i]['lat'];
			$LastTripLon = $Vehicleinfolist[$i]['lng'];
		}
		
		if($Vehicleinfolist[$i]['parkInMinuits'] > 5 || $i == (count($Vehicleinfolist)-1))
		{
			$TripDuration = $this->time_diff($LastTripEndTime,$Vehicleinfolist[$i]['StartTime']);
			$TripMilageDisplat = ($Vehicleinfolist[$i]['Miles']-$startMilage);
			//$TripValues[] = array('StartAddress'=>$this->NominatimRevCordinates($LastTripLat,$LastTripLon),'EndAddress'=>$this->NominatimRevCordinates($Vehicleinfolist[$i]['lat'],$Vehicleinfolist[$i]['lng']),'TripStartTimeDisplay'=>$LastTripEndTimeDisplay,'TripEndTimeDisplay'=>$Vehicleinfolist[$i]['StartTimeDisplay'],'TripStartTime'=>$LastTripEndTime,'TripEndTime'=>$Vehicleinfolist[$i]['StartTime'],'TotalMilage'=>$Vehicleinfolist[$i]['Miles'],'TripMilage'=>$fgmembersite->UserUnit($TripMilageDisplat)['value'].$fgmembersite->UserUnit(0)['lengthUnit'],'TripDuration'=>$TripDuration);
			$TripValues[] = array('StartAddress'=>$LastTripLat.",".$LastTripLon,'EndAddress'=>$Vehicleinfolist[$i]['lat'].",".$Vehicleinfolist[$i]['lng'],'TripStartTimeDisplay'=>$LastTripEndTimeDisplay,'TripEndTimeDisplay'=>$Vehicleinfolist[$i]['StartTimeDisplay'],'TripStartTime'=>$LastTripEndTime,'TripEndTime'=>$Vehicleinfolist[$i]['StartTime'],'TotalMilage'=>$Vehicleinfolist[$i]['Miles'],'TripMilage'=>$fgmembersite->UserUnit($TripMilageDisplat)['value'].$fgmembersite->UserUnit(0)['lengthUnit'],'TripDuration'=>$TripDuration);
			//echo "Trip Devide<br>";
			
			$LastTripLat = $Vehicleinfolist[$i]['lat'];
			$LastTripLon = $Vehicleinfolist[$i]['lng'];
			$LastTripEndTime = $Vehicleinfolist[$i]['EndTime'];
			$LastTripEndTimeDisplay = $Vehicleinfolist[$i]['EndTimeDisplay'];
			$startMilage = $Vehicleinfolist[$i]['Miles'];
		}
	}
	//print_r($TripValues);
	//exit;
	
	$Vehicleinfolist=array('Vehicleinfo'=>$TripValues);
	return $Vehicleinfolist;
}

function AppTripReportOpeCreate($sysnoarray,$sttime,$endtime,$fgmembersite)
{
		$qry="SELECT DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result` 
		FROM `tko".$sysnoarray['sysno']."` 
		WHERE DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) <= '$endtime' 
		AND DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) >= '$sttime'
		AND `Longitude` != '0'
		AND `Latitude` != '0'
		ORDER BY DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE)";
		//echo $qry;

		$pass_geo_time = "";
		$stmt = mysqli_query($fgmembersite->connection,$qry);

	if(!$stmt){
		return "<tr><td colspan='6'>NO DATA ".$sysnoarray['BusNo']."</td></tr>";
	}
	
	$sql_rec_count = mysqli_num_rows($stmt);


$ParkingVelocityValue = 3;

	
$pass_point = '';
$start_loop = 1;
$record_count = 0;
$total_record_count = 0;
$point='';
$velosity='';
$tablebody = "";
$row_count = 0;
$lastplusmilage = 0;
	while($row = mysqli_fetch_array($stmt))
{
	if($row_count == 0)
	{
		$firstmilage = 0;
	}
	

	
	
	if($row_count==0){
		$milageCumilat = 0;
		$milage = 0;
	}else{
		$date = new DateTime($passTime);
		$date2 = new DateTime($row['Time']);
		$travalTime = $date2->getTimestamp() - $date->getTimestamp();
		$distance = $this->vincentyGreatCircleDistance($passLat, $passLng, $row['Latitude'], $row['Longitude']);
		//echo $passLat." ".$passLng." ".$row['Latitude']." ".$row['Longitude']."<br>";
		//echo $distance."<br>";
		$speed = $distance/$travalTime;
		if($speed<100)
		{
			$milageCumilat = $milageCumilat + $distance;
		}else{
			$milageCumilat = $milageCumilat + 0;
		}
		$milage = $milageCumilat;
	}
	//$milage = round($milage/1000);
	$row_count++;
	
	$total_record_count++;
	$pass_point=$point;
	$passLat = $row['Latitude'];
	$passLng = $row['Longitude'];
	$point = $row['Latitude'].",".$row['Longitude'];
	$pass_velosity=$velosity;
	$velosity=$row['Velocity'];
	
	if($start_loop==1){
		if ($row['Velocity']<=$ParkingVelocityValue){
			$pass_point=$point;
			$passLat = $row['Latitude'];
			$passLng = $row['Longitude'];
			$start_count = 1;
			$pass_stop_time = $row['Time'];
		}
		$start_loop=0;
	}



	if ($row['Velocity']<=$ParkingVelocityValue){
		$start_count = 1;
	}
	else
	{
		if ($start_count==1){
			$record_count++;
			$Vehicleinfo[] = $this->AppTripReportOpeCreate_stopping_row($fgmembersite, $milage,$row['Time'],$pass_point,$pass_stop_time,$record_count,$pass_velosity,$sysnoarray['image'],$row['Oil'],$passLat,$passLng,$row['Angle'],$row['DtStatus']);
			}
		$record_count++;
		$Vehicleinfo[] = $this->AppTripReportOpeCreate_stopping_row($fgmembersite, $milage,$row['Time'],$point,$row['Time'],$record_count,$row['Velocity'],$sysnoarray['image'],$row['Oil'],$row['Latitude'],$row['Longitude'],$row['Angle'],$row['DtStatus']);
		$pass_stop_time = $row['Time'];
		$start_count = 0;
	}

if ($total_record_count==$sql_rec_count){
		if ($start_count==1){
			$record_count++;
			$Vehicleinfo[] = $this->AppTripReportOpeCreate_stopping_row($fgmembersite, $milage,$row['Time'],$point,$pass_stop_time,$record_count,$pass_velosity,$sysnoarray['image'],$row['Oil'],$row['Latitude'],$row['Longitude'],$row['Angle'],$row['DtStatus']);
			}
		$pass_stop_time = $row['Time'];
		$start_count = 0;
}
}
return $Vehicleinfo;
}


function AppTripReportOpeCreate_stopping_row($fgmembersite, $milage,$time,$point,$pass_stop_time,$record_count,$velosity,$image,$Oil,$Lat,$lng,$Angle,$DtStatus)
{
$PassTimeDisplay = $fgmembersite->UserTimeFormat($pass_stop_time);
$TimeDisplay = $fgmembersite->UserTimeFormat($time);
$TimeOnlyDisplay = date("H:i:s", strtotime($pass_stop_time));
$TimeHour = date("H", strtotime($pass_stop_time));
//$PassTimeDisplay = $pass_stop_time;
//$TimeDisplay = $time;
$milage = $fgmembersite->UserUnit(round($milage/1000))['value'].$fgmembersite->UserUnit(0)['lengthUnit'];
	
	if ($velosity==0){
		return array('mapimage'=>$image,'Oil'=>$Oil,'Miles'=>$milage,'StartTimeDisplay'=>$PassTimeDisplay,'EndTimeDisplay'=>$TimeDisplay,'StartTime'=>$pass_stop_time,'EndTime'=>$time,'time'=>"(".$PassTimeDisplay.")<br>(".$TimeDisplay.")",'lng'=>$lng,'lat'=>$Lat,'hour'=>$TimeHour,'address'=>"",'Velocity'=>$fgmembersite->UserUnit($velosity)['value'].$fgmembersite->UserUnit(0)['speedUnit'],'Angle'=>$Angle,'DtStatus'=>$DtStatus,'park'=>$this->time_diff($pass_stop_time,$time),'parkInMinuits'=>$this->timeDiffInMinutes($pass_stop_time,$time),'status'=>'Stop');
		//$this->NominatimRevCordinates($Lat,$lng)
	//return "<tr><td>".$pass_stop_time."  -  ".$time."</td><td>".$velosity."</td><td>Stop</td><td>".$milage."</td><td><div id='".$record_count."address'>".$point."</div></td><td>".$this->time_diff($pass_stop_time,$time)."</td></tr>";
	}
	else {
		return array('mapimage'=>$image,'Oil'=>$Oil,'Miles'=>$milage,'StartTimeDisplay'=>$PassTimeDisplay,'EndTimeDisplay'=>$TimeDisplay,'StartTime'=>$pass_stop_time,'EndTime'=>$time,'time'=>"(".$PassTimeDisplay.")",'lng'=>$lng,'lat'=>$Lat,'hour'=>$TimeHour,'address'=>"",'Velocity'=>$fgmembersite->UserUnit($velosity)['value'].$fgmembersite->UserUnit(0)['speedUnit'],'Angle'=>$Angle,'DtStatus'=>$DtStatus,'park'=>$this->time_diff($pass_stop_time,$time),'parkInMinuits'=>$this->timeDiffInMinutes($pass_stop_time,$time),'status'=>'Run');
	//return "<tr><td>".$pass_stop_time."</td><td>".$velosity."</td><td>Run</td><td>".$milage."</td><td><div id='".$record_count."address'>".$point."</div></td><td>".$this->time_diff($pass_stop_time,$time)."</td></tr>";		$this->NominatimRevCordinates($Lat,$lng)
	}
}


///////////////////////////////		App Trip Report End	/////////////////////////////

////////////////////////////////  Section Report /////////////////////////////////////
function section_report($postvalue,$fgmembersite,$connection,$user)
{
	//print_r($postvalue['geofences']);
	$geofenceProperties = array();
	//echo $postvalue['select'];
	$sysnoarray = $this->SysInfoArray($postvalue['select'],$connection);
	$sysno = $sysnoarray['sysno'];
	$month = date('m',strtotime($postvalue['st_date'].'-01'));
	$year = date('Y',strtotime($postvalue['st_date'].'-01'));
	$month = str_pad($month, 2, "0", STR_PAD_LEFT);
	$number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$day_count = $this->days_in_month($month, $year);
	
	$sttime=$postvalue['st_date']." 00:00:00";
	$endtime=$postvalue['end_date']." 23:59:00";
	
	for($i=0;$i<count($postvalue['geofences']);$i++)
	{
		$geofenceProperties[] = $this->prepareGeofence($postvalue['geofences'][$i],$connection,$user);
		
	}

	return $this->CreateForSections($fgmembersite,$connection,$sysno,$geofenceProperties,$sttime,$endtime,$sysnoarray);
	exit;
}


function CreateForSections($fgmembersite,$connection,$sysno,$geofenceProperties,$sttime,$endtime,$sysnoarray)
{
	$pointLocation = new pointLocation();
	
	$geoInOut = array();
	
	$passtime = "-";
	
	$tableBody = "";
	
	$rowcount = 0;
	for($i=0; $i<count($geofenceProperties); $i++)
	{
		$geoInOut[$geofenceProperties[$i]['geoname']] = 'outside';
		$geoInOut[$geofenceProperties[$i]['Time']] = '';
	}
		
	$qry="SELECT DATE_ADD(`Time`, INTERVAL ".$fgmembersite->UserTimeZone()." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm` FROM `tko".$sysno."` WHERE DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) > '$sttime' AND DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) < '$endtime' ORDER BY `Time`";

	$stmt = mysqli_query($connection,$qry);
  
  	while($row = mysqli_fetch_array($stmt))
	{
		$rowcount++;
		
		$point = $row['Latitude']." ".$row['Longitude'];
		
		for($i=0; $i<count($geofenceProperties); $i++)
		{
			//echo $geofenceProperties[$i]['geoname']."->".$pointLocation->pointInPolygon($point, $geofenceProperties[$i]['coordinates'])."<br>";
			
			$isInOut = $pointLocation->pointInPolygon($point, $geofenceProperties[$i]['coordinates']);
			if ($isInOut != $geoInOut[$geofenceProperties[$i]['geoname']]  && trim($geofenceProperties[$i]['geoname']) != "")
			{
				if($isInOut == 'inside')
				{
					$tableBody = $tableBody."<tr><td>".$geofenceProperties[$i]['geoname']."</td><td>IN</td><td>".$fgmembersite->UserTimeFormat($row['Time'])."</td><td>".$this->dutation($passtime,$row['Time'])."</td></tr>";
					//echo $geofenceProperties[$i]['geoname']." insiede ".$row['Time']."----".$this->dutation($passtime,$row['Time'])."<br>";		//$this->time_diff($row['Time'],$geoInOut[$geofenceProperties[$i]['Time']]).
				}else{
					$tableBody = $tableBody."<tr><td>".$geofenceProperties[$i]['geoname']."</td><td>OUT</td><td>".$fgmembersite->UserTimeFormat($row['Time'])."</td><td>".$this->dutation($passtime,$row['Time'])."</td></tr>";
					//echo $geofenceProperties[$i]['geoname']." outside ".$row['Time']."----".$this->dutation($passtime,$row['Time'])."<br>";
				}
				$passtime = $row['Time'];
			}
			$geoInOut[$geofenceProperties[$i]['geoname']] = $isInOut;
			
		}
	}
	if($rowcount==0)
	{
		return "<tr><td colspan='4'>NO Data Available</td></tr>";
	}
	if($tableBody == "")
	{
		return "<tr><td colspan='4'>NO OPERATIONS IN SELECTED AREA</td></tr>";
	}else{
		return $tableBody;
	}
}

function dutation($time1,$time2)
{
	if($time1 == "-")
	{
		return "-";
	}else{
		return $this->time_diff($time1,$time2);
	}
	
}

function prepareGeofence($geofenceid,$connection,$user)
{
$G1php = $this->find_geocord($geofenceid,$connection,$user);

$G1polygon=array();

$pointss = explode(" ", $G1php['coordinates']);
foreach($pointss as $key => $pointp) {
	$lanlat = explode(",",$pointp);  
	array_push($G1polygon,"$lanlat[1] $lanlat[0]");
  }
return array('coordinates'=>$G1polygon,'id'=>$geofenceid,'geoname'=>$G1php['geoname']);
}

////////////////////////////////  Section Report End /////////////////////////////////////



////////////////////////////////  O/D Report ////////////////////////////	
function odreport($postvalue,$connection,$user)
{

	$sysnoarray = $this->SysInfoArray($postvalue['select'],$connection);
	$sysno = $sysnoarray['sysno'];
	$month = date('m',strtotime($postvalue['st_date'].'-01'));
	$year = date('Y',strtotime($postvalue['st_date'].'-01'));
	$month = str_pad($month, 2, "0", STR_PAD_LEFT);
	$number = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	$day_count = $this->days_in_month($month, $year);
	
	$sttime="$year-$month-01 00:00:00";
	$endtime="$year-$month-$number 23:59:00";
	
	$geofencepoints = $this->ODgeofence_points($sysno,$connection,$user);
//print_r($geofencepoints);
$busdetail_array['maxduration'] = '5:00';
$busdetail_array['minduration'] = '1:00';
$busdetail_array['origine'] = $geofencepoints['origine'];
$busdetail_array['destination'] = $geofencepoints['destination'];

	$rows = $this->create_rows_for_bus($sysnoarray,$sttime,$endtime,$geofencepoints['G1polygon'],$geofencepoints['G2polygon'],$geofencepoints['G1lat'],$geofencepoints['G1lng'],$geofencepoints['G2lat'],$geofencepoints['G2lng'],$connection,$busdetail_array);
//print_r($rows);
	$tablestring = "";	
	for($i=1;$i<=$day_count;$i++){
		$date = $year."-".str_pad($month, 2, "0", STR_PAD_LEFT)."-".str_pad($i, 2, "0", STR_PAD_LEFT);
		for($j=0; $j < count($rows['tripdetalis'][$date]['trips']); ++$j) {
			$tablestring = $tablestring.'<tr><td>'.array_keys($rows['tripdetalis'][$date]['trips'][$j])[0].'</td><td>'.$rows['tripdetalis'][$date]['trips'][$j][array_keys($rows['tripdetalis'][$date]['trips'][$j])[0]].'</td>'
											.'<td>'.array_keys($rows['tripdetalis'][$date]['trips'][$j])[1].'</td><td>'.$rows['tripdetalis'][$date]['trips'][$j][array_keys($rows['tripdetalis'][$date]['trips'][$j])[1]].'</td>'
											.'<td>'.$rows['tripdetalis'][$date]['trips'][$j]['duration'].'</td></tr>';
		}
		//print_r($rows['tripdetalis'][$date]['trips']);
			
	}
	$tablestring = $tablestring;
	return $tablestring;
}

function create_rows_for_bus($sysnoarray,$sttime,$endtime,$G1polygon,$G2polygon,$G1lat,$G1lng,$G2lat,$G2lng,$connection,$busdetail_array){



$trips = array();
$datearray = array();

global $gpsdbhost;


$pointLocation = new pointLocation();
	$row_count=1;
	$pass_date = '';
	$trips_per_day = 0;
	$sisuseriya_trips_per_day = 0;
	$G1_in='';
	$G2_in='';
	$G1status='';
	$G2status='';
	$pass_G2_out_time='';
	$G2_out_time='';
	$pass_G1_out_time='';
	$first_trip_start='--';
	$first_trip_stop='--';
	$return_trip_operated=0;
	$fist_trip_operated=0;
	$return_trip_start_D="";
	$first_trip_start_D="";
	$trip_date = '';

	$qry="SELECT DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) as Time, `Longitude`, `Latitude`, `Velocity`, `Angle`, `Locate`, `DtStatus`, `Oil`, `Miles`, `Temperature`, `Alarm`, `send`, `result` FROM `tko".$sysnoarray['sysno']."` WHERE DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) > '$sttime' AND DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) < '$endtime' ORDER BY `Time`";
  //echo $qry;
  $stmt = mysqli_query($connection,$qry);
  
  	while($row = mysqli_fetch_array($stmt))
	{
				
		if($row_count==1){
			$pass_date = date('Y-m-d',strtotime($row['Time']));
			$pass_datenonformat = $row['Time'];
		}

				$passG1=$G1_in;
				$passG2=$G2_in;
				$point = $row['Latitude']." ".$row['Longitude'];

				if ($pointLocation->pointInPolygon($point, $G1polygon)=='inside'){
				//echo " Bastiyan mawatha in ".$row['Time']."<br>";
				$G1_in=1;
				}
				else{
				$G1_in=0;
				}

				if ($G1_in>$passG1){
				$G1status="1";					//come in to G1
				//echo $row['Time']->format('Y-m-d H:i:s')." G1_In <br>";
				$G1_in_time=$row['Time'];
				}

				if ($G1_in<$passG1){
				$G1status="0";					//Go out from G1
				//echo $row['Time']->format('Y-m-d H:i:s')." G1_Out <br>";
				//$G1_out_time=$row['Time'];
				$G1_out_time=$pass_datenonformat;
				}

				if ($pointLocation->pointInPolygon($point, $G2polygon)=='inside'){
				//echo " badulla in".$row['Time']."<br>";
				$G2_in=1;
				}
				else{
				$G2_in=0;
				}

				if ($G2_in<$passG2){
				$G2status="0";					//Go out from G2
				//echo $pass_datenonformat->format('Y-m-d H:i:s')." G2_Out <br>";
				//$G2_out_time=$row['Time'];
				$G2_out_time=$pass_datenonformat;
				}

				if ($G2_in>$passG2){
				$G2status="1";					//come in to G2
				//echo $row['Time']->format('Y-m-d H:i:s')." G2_In <br>";
				$G2_in_time=$row['Time'];
				}

//*************************************************First Trip Quary********************************************//
				//if($G1status<$G2status){					//Out from G1 & Come to G2
				if($G1status=="0" && $G2status=="1"){
					if($pass_G1_out_time != $G1_out_time){
					//echo "G1 out ".$G1_out_time." G2 in ".$G2_in_time." ";              	//FIRST TRIP
					//echo time_diff($G1_out_time,$G2_in_time)."<br>";
					$first_trip_start = $G1_out_time;
					$first_trip_stop = $G2_in_time;
					$first_trip_start_D = date('d',strtotime($G1_out_time));
					$fist_trip_operated=1;
					$return_trip_operated=0;
					

					if($return_trip_start_D !="" && $return_trip_start_D != $first_trip_start_D){
					//echo "<tr><td>Break</td></tr>";
					
						if($return_trip_operated==0){
						//echo "<tr><td colspan='6' style='height: 25px; text-align: center;'>".$trip_date." Trips - ".$sisuseriya_trips_per_day."</td></tr>";
						
						$datearray[$trip_date] = array('tripcount'=>$sisuseriya_trips_per_day,'trips'=>$trips);
						//print_r($trips);
						$trips = [];

						$trips_per_day=0;
						$sisuseriya_trips_per_day=0;
						}
						else{
						//echo "<td></td><td></td><td></td></tr>";
						//echo "<tr><td colspan='6' style='height: 25px; text-align: center;'>".$trip_date." Trips - ".$sisuseriya_trips_per_day."</td></tr>";
						
						$datearray[$trip_date] = array('tripcount'=>$sisuseriya_trips_per_day,'trips'=>$trips);
						//print_r($trips);
						$trips = [];

						$trips_per_day=0;
						$sisuseriya_trips_per_day=0;
						}
					}
					$trip_date = date('Y-m-d',strtotime($G1_out_time));
					

						if($this->compair_time_diffmax($first_trip_start,$first_trip_stop,$busdetail_array['maxduration'])>=0  && $this->compair_time_diffmin($first_trip_start,$first_trip_stop,$busdetail_array['minduration'])>=0){
							//echo "<tr><td bgcolor='#BDBDBD'>".$first_trip_start."</td><td bgcolor='#BDBDBD' style='text-align: center;'>".time_diff($first_trip_start,$first_trip_stop)."</td><td bgcolor='#BDBDBD'>".$first_trip_stop."</td>";
							
							$trips[] = array($busdetail_array['origine']=>$first_trip_start,$busdetail_array['destination']=>$first_trip_stop,'duration'=>$this->time_diff($first_trip_start,$first_trip_stop),'validity'=>'true');
							
							$sisuseriya_trips_per_day++;
							$trips_per_day++;
						}
						else{
							//echo "<tr><td>".$first_trip_start."</td><td style='text-align: center;'>".time_diff($first_trip_start,$first_trip_stop)."</td><td>".$first_trip_stop."</td>";
							
							$trips[] = array($busdetail_array['origine']=>$first_trip_start,$busdetail_array['destination']=>$first_trip_stop,'duration'=>$this->time_diff($first_trip_start,$first_trip_stop),'validity'=>'false');
							
							$trips_per_day++;
						}
					}
					$pass_G1_out_time = $G1_out_time;
				}
				
//******************************************Return Trip Quary*****************************************************//				
				if($G1status>$G2status){					//Out from G2 $ Come to G1
				if($pass_G2_out_time != $G2_out_time){
				//echo "G2 out ".$G2_out_time." G1 in ".$G1_in_time." ";					//RETURN TRIP
				//echo time_diff($G2_out_time,$G1_in_time)."<br>";
				
				
					$return_trip_start = $G2_out_time;
					$return_trip_stop = $G1_in_time;
					$return_trip_start_D = date('d',strtotime($G2_out_time));
					$return_trip_operated=1;
					
					
					if($first_trip_start_D !="" && $return_trip_start_D != $first_trip_start_D){
						//echo "<td></td><td></td><td></td></tr><tr><td colspan='6' style='height: 25px; text-align: center;'>".$trip_date." Trips - ".$sisuseriya_trips_per_day."</td></tr>";
						
						$datearray[$trip_date] = array('tripcount'=>$sisuseriya_trips_per_day,'trips'=>$trips);
						//print_r($trips);
						$trips = [];
				
						$trips_per_day=0;
						$sisuseriya_trips_per_day=0;
					}
					
					$trip_date = date('Y-m-d',strtotime($G2_out_time));
					
					
					if($trips_per_day==0){
						if($this->compair_time_diffmax($return_trip_start,$return_trip_stop,'5:00')>=0  && $this->compair_time_diffmin($return_trip_start,$return_trip_stop,$busdetail_array['minduration'])>=0){
							//echo "<tr><td/><td/><td/><td bgcolor='#BDBDBD'>".$return_trip_start."</td><td bgcolor='#BDBDBD' style='text-align: center;'>".time_diff($return_trip_start,$return_trip_stop)."</td><td bgcolor='#BDBDBD'>".$return_trip_stop."</td></tr>";
							
							$trips[] = array($busdetail_array['destination']=>$return_trip_start,$busdetail_array['origine']=>$return_trip_stop,'duration'=>$this->time_diff($return_trip_start,$return_trip_stop),'validity'=>'true');
							
							$trips_per_day++;
							$sisuseriya_trips_per_day++;
						}
						else{
							//echo "<tr><td/><td/><td/><td>".$return_trip_start."</td><td style='text-align: center;'>".time_diff($return_trip_start,$return_trip_stop)."</td><td>".$return_trip_stop."</td></tr>";
							
							$trips[] = array($busdetail_array['destination']=>$return_trip_start,$busdetail_array['origine']=>$return_trip_stop,'duration'=>$this->time_diff($return_trip_start,$return_trip_stop),'validity'=>'false');
							
							$trips_per_day++;
						}
					}
					else{
						if($this->compair_time_diffmax($return_trip_start,$return_trip_stop,$busdetail_array['maxduration'])>=0  && $this->compair_time_diffmin($return_trip_start,$return_trip_stop,$busdetail_array['minduration'])>=0){
							//echo "<td bgcolor='#BDBDBD'>".$return_trip_start."</td><td bgcolor='#BDBDBD' style='text-align: center;'>".time_diff($return_trip_start,$return_trip_stop)."</td><td bgcolor='#BDBDBD'>".$return_trip_stop."</td></tr>";
							
							$trips[] = array($busdetail_array['destination']=>$return_trip_start,$busdetail_array['origine']=>$return_trip_stop,'duration'=>$this->time_diff($return_trip_start,$return_trip_stop),'validity'=>'true');
							
							$trips_per_day++;
							$sisuseriya_trips_per_day++;
						}
						else{ 
							//echo "<td>".$return_trip_start."4</td><td style='text-align: center;'>".time_diff($return_trip_start,$return_trip_stop)."</td><td>".$return_trip_stop."</td></tr>";
							
							$trips[] = array($busdetail_array['destination']=>$return_trip_start,$busdetail_array['origine']=>$return_trip_stop,'duration'=>$this->time_diff($return_trip_start,$return_trip_stop),'validity'=>'false');
							
							$trips_per_day++;
						}
					}
				}
				$pass_G2_out_time = $G2_out_time;
				}
				
				
				$row_count++;
				$pass_date = date('Y-m-d',strtotime($row['Time']));
				$pass_datenonformat = $row['Time'];
			}
			if($return_trip_operated==0){
			//echo "<td></td><td></td><td></td></tr>";
			}
				//echo "<tr><td colspan='10' style='height: 25px; text-align: center;'>".$pass_date." Trips - ".$sisuseriya_trips_per_day."</td></tr>";
				
				$datearray[$trip_date] = array('tripcount'=>$sisuseriya_trips_per_day,'trips'=>$trips);
				//print_r($trips);
				$trips = [];
				
				$trips_per_day=0;
				$sisuseriya_trips_per_day=0;

			
			
			$sysno="";
if($return_trip_operated!=1&&$fist_trip_operated!=1){
			//echo"<tr><td colspan='10'>".$busno." NOT OPERATED IN THIS AREA</td></tr>";
}

return array('tripdetalis'=>$datearray,'busdetails'=>$busdetail_array);
}

function ODgeofence_points($sysno,$connection,$user)
{
	$qry = "SELECT `objsysno`, `origine`, `Destnation` FROM `trackingobjects` WHERE `objsysno` = '$sysno'";

	$stmt = mysqli_query($connection,$qry);
  
  	while($row = mysqli_fetch_array($stmt))
	{
		$origine = $row['origine'];
		$destination = $row['Destnation'];
	}
	
$G1phparray = $this->find_geocord($origine,$connection,$user);
$G2phparray = $this->find_geocord($destination,$connection,$user);

$G1php = $G1phparray['coordinates'];
$G2php = $G2phparray['coordinates'];

$Originename = $G1phparray['geoname'];
$Destinationname = $G2phparray['geoname'];

if($G1php =="" || $G2php==""){
return "Please Check the geofence settings...";
}

$G1polygon=array();
$G2polygon=array();
$G1lat=array();
$G1lng=array();
$G2lat=array();
$G2lng=array();
$margine = "0.003";


	  $pointss = explode(" ", $G1php);
		foreach($pointss as $key => $pointp) {
		$lanlat = explode(",",$pointp);  
		array_push($G1polygon,"$lanlat[1] $lanlat[0]");
		array_push($G1lat,"$lanlat[1]");    //6.033445203423446
		array_push($G1lng,"$lanlat[0]");	//80.2148017202378
		  }


	  $pointss = explode(" ", $G2php);
		foreach($pointss as $key => $pointp) {
		$lanlat = explode(",",$pointp);  
		array_push($G2polygon,"$lanlat[1] $lanlat[0]");
		array_push($G2lat,"$lanlat[1]");    //6.033445203423446
		array_push($G2lng,"$lanlat[0]");	//80.2148017202378
		  }
	$array1=array('origine'=>$Originename,'destination'=>$Destinationname,'G1polygon'=>$G1polygon,'G2polygon'=>$G2polygon,'G1lat'=>$G1lat,'G1lng'=>$G1lng,'G2lat'=>$G2lat,'G2lng'=>$G2lng);
	return $array1;
}


function find_geocord($geofence,$connection,$user)
{
	$qry = "SELECT `id`, `geo_name`, `cordinates`, `geouser` FROM `geofences` WHERE `id` = '$geofence'";
	//echo $qry;
	//$qry = "SELECT `geo_name`, `cordinates`, `geouser` FROM `geofences` WHERE `geo_name` = '$geofence' AND `geouser` = ''";
	$stmt = mysqli_query($connection,$qry);
  
  	while($row = mysqli_fetch_array($stmt))
	{
		return array('coordinates'=>trim($row['cordinates']),'geoname'=>$row['geo_name']);
	}
}
	

function days_in_month($month, $year) 
{ 
return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31); 
} 

function time_diff($time1,$time2){
if($time1!=null && $time2!=null && $time1!="" && $time2!="" && $time1!="--" && $time2!="--"){
		$now = new DateTime($time1);
		$exp = new DateTime($time2);

		$diff = $now->diff($exp);
		//printf('%d hours, %d minutes, %d seconds', $diff->h, $diff->i, $diff->s);

		if($diff->d<=0){
			$days="";
		}
		else{
			$days=$diff->d."d ";
		}
		return $days.sprintf('%02d',$diff->h).":".sprintf('%02d',$diff->i);
		//return $diff;
}
else{
return "--";
}
}

function timeDiffInMinutes($time1,$time2){
	$to_time = strtotime($time2);
	$from_time = strtotime($time1);
	return round(abs($to_time - $from_time) / 60,2);
}

function compair_time_diffmin($time1,$time2,$maxmintime){
if($time1!=null && $time2!=null && $time1!="" && $time2!="" && $time1!="--" && $time2!="--" && $maxmintime!=null && trim($maxmintime)!=""){
		$now = new DateTime($time1);
		$exp = new DateTime($time2);

		$array1 = explode(':',trim($maxmintime));

		$maxminhours = $array1[0];
		$maxminmins = $array1[1];
		
		
		$diff = $now->diff($exp);
		$difconst = ($diff->d*24*60)+($diff->h*60)+$diff->i;
		$maxmintimeconst = ($maxminhours*60)+($maxminmins);
		

	return $difconst-$maxmintimeconst;
}
else{
return -1;
}
}

function compair_time_diffmax($time1,$time2,$maxmintime){
	if($time1!=null && $time2!=null && $time1!="" && $time2!="" && $time1!="--" && $time2!="--" && $maxmintime!=null && trim($maxmintime)!=""){
			$now = new DateTime($time1);
			$exp = new DateTime($time2);

			$array1 = explode(':',trim($maxmintime));

			$maxminhours = $array1[0];
			$maxminmins = $array1[1];
			
			
			$diff = $now->diff($exp);
			$difconst = ($diff->d*24*60)+($diff->h*60)+$diff->i;
			$maxmintimeconst = ($maxminhours*60)+($maxminmins);
			

		return $maxmintimeconst-$difconst;
	}
	else{
	return -1;
	}
}

//////////////////////////////////End O/D Report //////////////////////////////


function ConfusedMilages($date,$sysnoarray,$connection)
{
	$Startmilage = 0;
	$Maxmilage = 0;
	$Lasttmilage = 0;
	
	$qry = "SELECT DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) as Time, `Miles` FROM `tko".$sysnoarray['sysno']."` WHERE DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) > '$date 00:00:00' AND DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) < '$date 23:59:59' ORDER BY `Time` LIMIT 1";

	$stmt = mysqli_query($connection,$qry);
	while($row = mysqli_fetch_array($stmt))
	{
		$Startmilage = $row['Miles'];
	}
	$qry = "SELECT DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) as Time, `Miles` FROM `tko".$sysnoarray['sysno']."` WHERE DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE)> '$date 00:00:00' AND DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) < '$date 23:59:59' ORDER BY `Miles` DESC LIMIT 1";

	$stmt = mysqli_query($connection,$qry);
	while($row = mysqli_fetch_array($stmt))
	{
		$Maxmilage = $row['Miles'];
	}
	$qry = "SELECT DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) as Time, `Miles` FROM `tko".$sysnoarray['sysno']."` WHERE DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) > '$date 00:00:00' AND DATE_ADD(`Time`, INTERVAL ".$sysnoarray['tz']." MINUTE) < '$date 23:59:59' ORDER BY `Time` DESC LIMIT 1";

	$stmt = mysqli_query($connection,$qry);
	while($row = mysqli_fetch_array($stmt))
	{
		$Lasttmilage = $row['Miles'];
	}

	return ($Maxmilage - $Startmilage + $Lasttmilage);
}

function NominatimRevCordinates($lat,$lon)
{
	$jsonurl = "https://maps.gps22.net/nominatim/reverse?format=geojson&lat=".$lat."&lon=".$lon;
	$json = file_get_contents($jsonurl);
	$ResultArray = json_decode($json);
	$addressConcat = "";
		//print_r($ResultArray->features[0]->properties->address);
		$addressproperties = $ResultArray->features[0]->properties->address;

		$road = $addressproperties->road;
		$suburb = $addressproperties->suburb;
		$postcode = $addressproperties->postcode;
		$addressConcat = $road.",".$suburb.",".$postcode.".";

	return $addressConcat;
	//var_dump(json_decode($json));
}
}



class pointLocation {
    var $pointOnVertex = true; // Check if the point sits exactly on one of the vertices?
 
    function pointLocation() {
    }
 
        function pointInPolygon($point, $polygon, $pointOnVertex = true) {
        $this->pointOnVertex = $pointOnVertex;
 
        // Transform string coordinates into arrays with x and y values
        $point = $this->pointStringToCoordinates($point);
        $vertices = array(); 
        foreach ($polygon as $vertex) {
            $vertices[] = $this->pointStringToCoordinates($vertex); 
        }
 
        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            return "vertex";
        }

        // Check if the point is inside the polygon or on the boundary
        $intersections = 0; 
        $vertices_count = count($vertices);
 
        for ($i=1; $i < $vertices_count; $i++) {
            $vertex1 = $vertices[$i-1]; 
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) { 
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x']; 
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++; 
                }
            } 
        } 
        // If the number of edges we passed through is odd, then it's in the polygon. 
        if ($intersections % 2 != 0) {
            return "inside";
        } else {
            return "outside";
        }
    }
 
    function pointOnVertex($point, $vertices) {
        foreach($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }
 
    }
 
    function pointStringToCoordinates($pointString) {
        $coordinates = explode(" ", $pointString);
        return array("x" => $coordinates[0], "y" => $coordinates[1]);
    }
 
}
?>