<?php

function balcklistDelete($fgmembersite,$postValues)
{
    if(!$fgmembersite->DBLogin())
    {
        echo "Not connected";
        return false;
    }  
    $qry = "DELETE FROM `blacklist` WHERE `id` = '".$postValues['del']."'";

    $stmt = mysqli_query($fgmembersite->connection,$qry);

    if($stmt)
    {
        return "Record deleted";
    }
    return "Error: Record not deleted";
}

function blacklistAddEdit($fgmembersite,$postValues)
{
    if($postValues['addedit'] == "edit")
    {
        return balcklistEdit($fgmembersite,$postValues);
    }
    if($postValues['addedit'] == "add")
    {
        return balcklistAdd($fgmembersite,$postValues);
    }
}

function balcklistAdd($fgmembersite,$postValues)
{
    if(!$fgmembersite->DBLogin())
    {
        echo "Not connected";
        return false;
    }  
    $qry = "INSERT INTO `blacklist`(`name`, `surName`, `nic`, `rate`) 
    VALUES ('".$fgmembersite->Sanitize($postValues['name'])."','".$fgmembersite->Sanitize($postValues['surName'])."','".$fgmembersite->Sanitize($postValues['nic'])."','".$fgmembersite->Sanitize($postValues['rate'])."')";

    $stmt = mysqli_query($fgmembersite->connection,$qry);

    if($stmt)
    {
        return "Record inserted";
    }
    return "Error: Record not inserted";
}

function balcklistEdit($fgmembersite,$postValues)
{
    if(!$fgmembersite->DBLogin())
    {
        echo "Not connected";
        return false;
    }  
    $qry = "UPDATE `blacklist` SET `name`= '".$fgmembersite->Sanitize($postValues['name'])."',`surName`= '".$fgmembersite->Sanitize($postValues['surName'])."',`nic`='".$fgmembersite->Sanitize($postValues['nic'])."',`rate`='".$fgmembersite->Sanitize($postValues['rate'])."' WHERE `id` = '".$postValues['id']."'";

    $stmt = mysqli_query($fgmembersite->connection,$qry);

    if($stmt)
    {
        return "Record updated";
    }
    return "Error: Record  not updated";
}

function blacklistArray($fgmembersite,$id)
{
    if(!$fgmembersite->DBLogin())
    {
        echo "Not connected";
        return false;
    }  

    $tableStr = "";

    $qry = "SELECT `id`, `name`, `surName`, `nic`, `rate`, `inseartstamp` 
            FROM `blacklist` 
            WHERE `id` = '$id'
            ORDER BY `nic`";

    $result = null;
    $stmt = mysqli_query($fgmembersite->connection,$qry);

    while($row = mysqli_fetch_array($stmt))
    {
        $result = $row;
    }
    return $result;
}


function blacklistTable($fgmembersite)
{
    if(!$fgmembersite->DBLogin())
    {
        echo "Not connected";
        return false;
    }  

    $tableStr = "";

    $qry = "SELECT `id`, `name`, `surName`, `nic`, `rate`, `inseartstamp` 
            FROM `blacklist` 
            WHERE 1
            ORDER BY `nic`";

    $stmt = mysqli_query($fgmembersite->connection,$qry);

    while($row = mysqli_fetch_array($stmt))
    {
        $tableStr = $tableStr."<tr>
        <td>
        <form id='formEditLoad".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'><input type='hidden' name='editid' value='".$row['id']."'/></form>
        <form id='".$row['id']."' action='".$_SERVER['PHP_SELF']."' method='post'> <input type='hidden' name='del' value='".$row['id']."'/></form>".$row['nic']."</td>
        <td>".$row['name']."</td><td>".$row['surName']."</td><td>".$row['rate']."</td>
        <td>
        <a class='btn btn-primary btn-sm' onclick='tablerowload(".$row['id'].")' ><i class='glyphicon glyphicon-edit icon-white'></i>Edit</a> 
        <a class='btn btn-danger btn-sm' onclick='delconfirm(".$row['id'].")' ><i class='glyphicon glyphicon-trash icon-white'></i>Delete</a>
        </td>
        </tr>";
    }
    return $tableStr;
}

?>