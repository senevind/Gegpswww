
<?php
require_once("./include/membersite_config.php");
		if(!$fgmembersite->DBLogin())
        {
            $fgmembersite->HandleError("Database login failed!");
            return false;
        } 

$qry = "SELECT id
      ,geo_name
      ,remarks
      ,cordinates
  FROM geofences
  ORDER BY geo_name";
$rowid=1;
echo '<input type="text" class="form-control input-sm m-b-md" id="filter" placeholder="Search in table">';
echo '<table id="example1" class=" toggle-arrow-tiny" data-page-size="100" data-filter=#filter>';
echo "<tr><th>Name</th><th  data-hide='phone,tablet'>Remarks</th><th></th><th  data-hide='phone,tablet'></th></tr>";
							$stmt = mysqli_query($fgmembersite->connection,$qry);
							if(!$stmt){
							echo "Query err";
							}
					
							  while($rowgeof = mysqli_fetch_array($stmt))
					{
					echo "<tr><td style = 'display:none'><div id='id".$rowid."'>".trim($rowgeof['id'])."</div></td>
					<td nowrap='wrap' style='max-width: 115px; width: 115px; word-wrap:break-word'>
					<div id='name".$rowid."'>".trim($rowgeof['geo_name'])."</div>
					</td><td nowrap='wrap' style='max-width: 100px; width: 100px; word-wrap:break-word'>".trim($rowgeof['remarks'])."</td>
					<td style = 'display:none'><div id='".$rowid."'>".trim($rowgeof['cordinates'])."</div></td>
					<td><button onclick=del_fence('".$rowid."')>Del</button></td><td><button onclick=show_gfence('".$rowid."')>show</button></td></tr>";
					$rowid++;
					}
echo "</table>";
?>
