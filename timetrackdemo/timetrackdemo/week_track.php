<?php
include_once "connection.php";

$user_id = $_POST['user_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$newformat = date('Y-m-d',$time);
/*Check connection*/
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
	
}else{
	/*get data based on userid for admin*/
    $weeklydata = "SELECT u.u_name,u.first_name,u.last_name,u.country,u.empno, t.user_id, t.hours, t.date, IFNULL( t.description,  '' ) description, t.updated_date, p.project_name, u.email_id FROM  `time_entry` t
LEFT JOIN projects p ON p.`project_id` = t.project_id
LEFT JOIN users u ON u.`user_id` = t.user_id
WHERE t.week_mode =1 AND (date BETWEEN '$start_date' AND '$end_date')";
    if($user_id == "alluser"){
        $weeklydata = $weeklydata;
    }elseif($user_id == "indianuser"){
        $weeklydata = $weeklydata." And country='India' ORDER BY u.u_name, t.updated_date DESC";
    }elseif($user_id == "ususer"){
        $weeklydata = $weeklydata." And country='USA' ORDER BY u.u_name, t.updated_date DESC";
    }else{
	/*get data based on userid for normal user*/
        $weeklydata = "SELECT te.date,te.hours,IFNULL(te.description,'') as description, te.updated_date, u.user_id, u.email_id, p.project_name, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM time_entry te
        LEFT JOIN projects p ON p.`project_id` = te.project_id
Join users u on u.user_id = te.user_id
WHERE te. week_mode=1 AND (date BETWEEN '$start_date' AND '$end_date') AND te.user_id=$user_id";
    }
    $weeklydataexec = mysql_query($weeklydata) or die("Error while selecting user information" . mysql_error());//startttt
    if (!empty($weeklydataexec)) {
        if (mysql_num_rows($weeklydataexec) > 0) {
            while ($row = mysql_fetch_array($weeklydataexec)) {
                $weekdetailsarray[] = $row;
            }
            $response["success"] = 1;
            $response["message"] = "Records found successfully";
            $response["weekdetailsarray"] = $weekdetailsarray;
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
/*closing db connection*/
mysql_close($conn);
?>