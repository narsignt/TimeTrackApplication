<?php
include_once "connection.php";

$track_id=$_POST['track_id'];
$user_id=$_POST['user_id'];
$isadmin=$_POST['isadmin'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Check connection
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
}else{
    $del_query = "delete from time_entry where id = $track_id;";
    //$delqueryexec = mysql_query($del_query) or die("Error while selecting user information" . mysql_error());
    if (mysql_query($del_query)) {
        $userquery = "select user_id,email_id,u_name,role from users where user_id = '$user_id' ";
        $userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());
        if(!empty($userqueryexec))
            {
                if(mysql_num_rows($userqueryexec)>0)
                {
                    $response["success"] = 1;
                    $response["userdetails"] = array();
                    while($row = mysql_fetch_array($userqueryexec))
                    {
                        $userdetails = array();
                        $userdetails["user_id"] = $row["user_id"];
                        $userdetails["email_id"] = $row["email_id"];
						$userdetails["u_name"] = $row["u_name"];
                        $userdetails["role"] = $row["role"];
                        $userrole = $row["role"];
                        $userid = $row["user_id"];
                        array_push($response["userdetails"],$userdetails);
                    }

                    if($isadmin === "false"){
                        $timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id = '$user_id' ORDER BY t.updated_date DESC";
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
                                /*$weekdetailsarray["success"] = 1;
                                $weekdetailsarray["message"] = "Records found successfully";*/
                                $response["weekdetailsarray"] = $weekdetailsarray;
                                $response["wmessage"] = "Records found successfully";
                                array_push($response["weekdetailsarray"],$weekdetailsarray);
                                //mysql_fetch_assoc($result);
                            }else{
                                //$weekdetailsarray["success"] = 1;
                                $weekdetailsarray["message"] = "No Records found ";
                                array_push($response["weekdetailsarray"],$weekdetailsarray);
                            }
                        }
                    }else{
                        $timetrackquery ="SELECT t . * , p.project_name, u.email_id, u.u_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
                        $weekdetailsarray[] = $row;
                        $weekdetailsarray["success"] = 1;
                        $weekdetailsarray["message"] = "Admin user";
                    }
                    $timetrackexec = mysql_query($timetrackquery)  or die("Error while selecting time_entry " . mysql_error());
                    $response["timetrackdetails"]=array();
                    while($row = mysql_fetch_array($timetrackexec))
                    {
                        $timetrackdetails=array();
                        $timetrackdetails["id"] = $row["id"];
                        $timetrackdetails["user_id"] = $row["user_id"];
                        $timetrackdetails["project_id"] = $row["project_id"];
                        $timetrackdetails["project_name"] = $row["project_name"];
                        $timetrackdetails["date"] = $row["date"];
                        $timetrackdetails["hours"] = $row["hours"];
                        $timetrackdetails["minutes"] = $row["minutes"];
                        $timetrackdetails["description"] = $row["description"];
                        $timetrackdetails["updated_date"] = $row["updated_date"];
                        $timetrackdetails["email_id"] = $row["email_id"];
                        $timetrackdetails["u_name"] = $row["u_name"];
                        array_push($response["timetrackdetails"],$timetrackdetails);
                    }
                }else{
                      $response["error"] = 0;
                      $response["message"] = "User details not found";
                      //echo json_encode($response);
                }
            }
    }else{
        $response["error"] = 1;
        $response["message"] = "failed to delete in timetrack record";
    }
    echo json_encode($response);
}
mysql_close($conn);
?>