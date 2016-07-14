<?php
include_once "connection.php";
include "audi_session.php";

$sessionid = $_REQUEST['sessionid'];
$project_name = $_POST['project_name'];

// Check connection
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
}else{
    $checkprojectexitquery="SELECT count(*) as total FROM `projects` WHERE `project_name`='$project_name'";
    $countquery = mysql_query($checkprojectexitquery);
    $result = mysql_num_rows($countquery);
    if($result>0){
    $data=mysql_fetch_assoc($countquery);
    $count=$data['total'];
    if(intval($count) > 0){
        $projectquery = "UPDATE projects SET status=1 WHERE project_name = '$project_name'";
    }else{
        $projectquery = "insert into projects (project_name, status ) values ('$project_name', 1)";
    }
     if (mysql_query($projectquery)) {
            saveSessionUsersAudit($sessionid,"Project Added","Day Tab", $project_name);
            $lastrecord = "SELECT * FROM projects where status=1 ORDER BY project_name";
            $result = mysql_query($lastrecord) or die("Error in Selecting " . mysql_error());
            while($row =mysql_fetch_assoc($result))
                {
                    $projectarray[] = $row;
                }
            $response["success"] = 1;
            $response["message"] = "Inserted Successfully";
            $response["projectdetails"] = $projectarray;
            mysql_free_result($result);
        }else{
            $response["error"] = 1;
            $response["message"] = "failed to insert in projects";
        }

        echo json_encode($response);
    }else{
         $response["error"] = 1;
         $response["message"] = "failed to insert in projects";
         echo json_encode($response);
         }
         mysql_free_result($countquery);
}
mysql_close($conn);
?>
