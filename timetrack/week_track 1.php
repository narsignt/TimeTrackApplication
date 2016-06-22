<?php
include_once "connection.php";

$user_id = $_POST['user_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];


$newformat = date('Y-m-d',$time);
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
	
}else{
    $weeklydata = "SELECT * FROM time_entry WHERE week_mode=1 AND (date BETWEEN '$start_date' AND '$end_date') AND user_id=$user_id";
    $weeklydataexec = mysql_query($weeklydata) or die("Error while selecting user information" . mysql_error());//startttt
    //echo "on $weekquery";
    if (!empty($weeklydataexec)) {
        // echo "not empty";
        if (mysql_num_rows($weeklydataexec) > 0) {
            //echo "yearmonthquery";
            //$response["weekdetailsarray"] = array();
            while ($row = mysql_fetch_array($weeklydataexec)) {
                $weekdetailsarray[] = $row;
            }
            $response["success"] = 1;
            $response["message"] = "Records found successfully";
            $response["weekdetailsarray"] = $weekdetailsarray;
            //mysql_fetch_assoc($result);
        }else{
            $response["success"] = 1;
            $response["message"] = "No Records found for user";
        }
        echo json_encode($response);
    } else {
        echo "failed";
        $response["error"] = 1;
        $response["message"] = "failed to insert in weeks";
        echo json_encode($response);
    }
}
	mysql_close($conn);
?>