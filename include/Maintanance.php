<?php

	function InsertNotificationRule($fgmembersite,$PostValus)
	{
		if(!$fgmembersite->DBLogin())
        {
            $fgmembersite->HandleError("Database login failed!");
            return false;
        } 
		$PostValues = null;
		$notification = "No";
		if(isset($PostValus['notification']) && $PostValus['notification'] == "Yes")
		{
			$notification = "Yes";
		}
		$qry = "INSERT INTO `gpsmaintanance`(`imei`, `user`, `subject`, `maxmiles`, `maxdays`, `startdate`, `isactive`) 
				VALUES ('".$PostValus['imei']."','".$fgmembersite->UserName()."','".$PostValus['subject']."','".$PostValus['maxmiles']."','".$PostValus['maxdays']."','".$PostValus['startdate']."','1')";
		$stmt = mysqli_query($fgmembersite->connection,$qry);
		if($stmt)
		{
			return "New notification Insert!";
		}else{
			return "Notification Not insert!";
		}
	}




?>
