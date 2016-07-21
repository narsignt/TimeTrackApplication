<?php
include_once "connection.php";
include "audi_session.php";

$sessionid = $_REQUEST['sessionid'];
$project_id = $_POST['project_id'];
$project_name = $_REQUEST['project_name'];
$cntrl=$_POST['cntrl'];
$cntrl=base64_decode($cntrl);
$exec_query;
/* cntrl is used to authenticate from hacker */
if($cntrl != "italent is the best"){
	$response["error"]     = 1;
	$response["message"] = "Token Mismatched";
	echo json_encode($response);
	
}else{
/* Checking connection*/
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
}else{
	/*deleting project name temporarily and setting status as 0 at db*/
    $updatesql = "UPDATE projects SET status=0 WHERE project_id = '$project_id'";
    if (mysql_query($updatesql)) {
        
        saveSessionUsersAudit($sessionid,"Project deleted","Day Tab", $project_name);
        $allrecords = "SELECT project_id,project_name FROM projects where status=1 ORDER BY project_name ";
        $result = mysql_query( $allrecords) or die("Error in Selecting " . mysql_error());
        while($row =mysql_fetch_assoc($result))
            {
                $projectarray[] = $row;
            }
        $response["success"] = 1;
        $response["message"] = "deleteted Successfully";
        $response["projectdetails"] = $projectarray;
        mysql_free_result($result);
    }else{
        $response["error"] = 1;
        $response["message"] = "failed to delete in projects";
    }
    echo json_encode($response);
}
}
/*closing db connection*/
mysql_close($conn);
?>





