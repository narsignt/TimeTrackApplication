<?php
include_once "connection.php";

$project_id = $_POST['project_id'];

// Check connection
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
}else{
    $updatesql = "UPDATE projects SET status=0 WHERE project_id = '$project_id'";
    if (mysql_query($updatesql)) {
        $allrecords = "SELECT * FROM projects where status=1 ORDER BY project_name ";
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
mysql_close($conn);
?>





