<?php
include_once "connection.php";

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

//echo $start_date;
//echo $end_date;
//This for showing the session log into UI by sunil
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
    
}else{
    $sessiondata = "SELECT SU.UserName, SU.LoginTime, SUA.Session_id, SUA.Time_Stamp, SUA.ActionTaken, SUA.AccessArea, IFNULL( SUA.performed_data,  '' ) performed_data
                    From Session_Users SU
                    LEFT JOIN Session_Users_Audit SUA ON SUA.Session_Id = SU.Session_Id
                    WHERE (Time_Stamp BETWEEN '$start_date' AND '$end_date')";
                    //echo $sessiondata;
    $sessiondataexec = mysql_query($sessiondata) or die("Error while selecting session log data" . mysql_error());//startttt
    //echo $sessiondataexec;
    if (!empty($sessiondataexec)) {
        if (mysql_num_rows($sessiondataexec) > 0) {
            while ($row = mysql_fetch_array($sessiondataexec)) {
                $sessiondetailsarray[] = $row;
            }
            $response["success"] = 1;
            $response["message"] = "Records found successfully";
            $response["sessionarray"] = $sessiondetailsarray;
            //mysql_fetch_assoc($result);
        }else{
            $response["success"] = 1;
            $response["message"] = "No Records found";
        }
    }
   echo json_encode($response);
}
    mysql_close($conn);
?>