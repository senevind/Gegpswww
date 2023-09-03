<?php


function customersDelete($fgmembersite,$postValues)
{
    if(!$fgmembersite->DBLogin())
    {
        echo "Not connected";
        return false;
    }  
    $qry = "DELETE FROM `customers` WHERE `id` = '".$postValues['del']."'";

    $stmt = mysqli_query($fgmembersite->connection,$qry);

    if($stmt)
    {
        return "Record deleted";
    }
    return "Error: Record not deleted";
}

function customersAddEdit($fgmembersite,$postValues)
{
    if($postValues['addedit'] == "edit")
    {
        return customersEdit($fgmembersite,$postValues);
    }
    if($postValues['addedit'] == "add")
    {
        return customersAdd($fgmembersite,$postValues);
    }
}

function customersAdd($fgmembersite,$postValues)
{
    if(!$fgmembersite->DBLogin())
    {
        echo "Not connected";
        return false;
    }  
    $qry = "INSERT INTO `customers`(`Name`, `licenseNo`, `Make`, `Model`, `licensePlate`, `rentStDate`, `rentDuration`, `userOwn`) 
    VALUES ('".$fgmembersite->Sanitize($postValues['Name'])."',
    '".$fgmembersite->Sanitize($postValues['licenseNo'])."',
    '".$fgmembersite->Sanitize($postValues['Make'])."',
    '".$fgmembersite->Sanitize($postValues['Model'])."',
    '".$fgmembersite->Sanitize($postValues['licensePlate'])."',
    '".$fgmembersite->Sanitize($postValues['rentStDate'])."',
    '".$fgmembersite->Sanitize($postValues['rentDuration'])."',
    '".$fgmembersite->UserName()."')";

    $stmt = mysqli_query($fgmembersite->connection,$qry);

    if($stmt)
    {
        return "Record inserted";
    }
    return "Error: Record not inserted";
}

function customersEdit($fgmembersite,$postValues)
{
    if(!$fgmembersite->DBLogin())
    {
        echo "Not connected";
        return false;
    }  
    $qry = "UPDATE `customers` SET 
    `Name`= '".$fgmembersite->Sanitize($postValues['Name'])."',
    `licenseNo`= '".$fgmembersite->Sanitize($postValues['licenseNo'])."',
    `Make`='".$fgmembersite->Sanitize($postValues['Make'])."',
    `Model`='".$fgmembersite->Sanitize($postValues['Model'])."',
    `licensePlate`='".$fgmembersite->Sanitize($postValues['licensePlate'])."',
    `rentStDate`='".$fgmembersite->Sanitize($postValues['rentStDate'])."',
    `rentDuration`='".$fgmembersite->Sanitize($postValues['rentDuration'])."'
    WHERE `id` = '".$postValues['id']."'";

    $stmt = mysqli_query($fgmembersite->connection,$qry);

    if($stmt)
    {
        return "Record updated";
    }
    return "Error: Record  not updated";
}

function customersArray($fgmembersite,$id)
{
    if(!$fgmembersite->DBLogin())
    {
        echo "Not connected";
        return false;
    }  

    $tableStr = "";

    $qry = "SELECT `id`, `Name`, `licenseNo`, `Make`, `Model`, `licensePlate`, `rentStDate`, `rentDuration`, `userOwn`, `InseartTime` 
            FROM `customers`
            WHERE `id` = '$id'";

    $result = null;
    $stmt = mysqli_query($fgmembersite->connection,$qry);

    while($row = mysqli_fetch_array($stmt))
    {
        $result = $row;
    }
    return $result;
}


function customersTable($fgmembersite)
{
    if(!$fgmembersite->DBLogin())
    {
        echo "Not connected";
        return false;
    }  

    $tableStr = "";

    $qry = "SELECT `id`, `Name`, `licenseNo`, `Make`, `Model`, `licensePlate`, `rentStDate`, `rentDuration`, `userOwn`, `InseartTime` FROM `customers` WHERE 1 ORDER BY `InseartTime` DESC";

    $stmt = mysqli_query($fgmembersite->connection,$qry);

    while($row = mysqli_fetch_array($stmt))
    {
        $tableStr = $tableStr."<tr>
        <td>
        <form id='formEditLoad".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='editid' value='".$row['id']."'/></form>
        <form id='".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'> <input type='hidden' name='del' value='".$row['id']."'/></form>"
        .$row['Name'].
        "</td>
        <td>".$row['licenseNo']."</td>
        <td>".$row['licensePlate']."</td>
        <td>".$row['rentStDate']."</td>
        <td>".$row['rentDuration']."</td>
        <td>
        <a class='btn btn-primary btn-sm' onclick='tablerowload(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Edit</a> 
        <a class='btn btn-danger btn-sm' onclick='delconfirm(".$row['id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
        </td>
        </tr>";
    }
    return $tableStr;
}

?>