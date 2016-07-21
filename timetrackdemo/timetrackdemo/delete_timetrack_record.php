<?php
include "connection.php";
include "audi_session.php";
include_once "timetrackCommonFunc.php";
$track_id=$_REQUEST['track_id'];
$user_id=$_POST['user_id'];
$isadmin=$_POST['isadmin'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$sessionid = $_REQUEST['sessionid'];
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
}else{
    $del_query = "delete from time_entry where id = $track_id;";
    // Added for audit log by sunil
    $trackDetails= getTrackDetials($track_id);

    if (mysql_query($del_query)) {
        // Added for audit log by sunil
        if ($trackDetails !='') {
            saveSessionUsersAudit($sessionid,"Deleted Track details","Day Tab", $trackDetails);
        }
        $userquery = "select user_id,email_id,u_name,country,empno,role from users where user_id = '$user_id' ";
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
						$userdetails["country"] = $row["country"];
						$userdetails["empno"] = $row["empno"];
                        $userdetails["role"] = $row["role"];
                        $userrole = $row["role"];
                        $userid = $row["user_id"];
                        array_push($response["userdetails"],$userdetails);
                    }
                    if($isadmin === "false"){
                        $timetrackquery = "SELECT t.date,t.hours,t.minutes,t.description,t.updated_date , p.project_name, u.email_id, u.u_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id = '$user_id' ORDER BY t.updated_date DESC";
                        $weeklydata = "SELECT * FROM time_entry WHERE week_mode=1 AND (date BETWEEN '$start_date' AND '$end_date') AND user_id=$user_id";
                        $weeklydataexec = mysql_query($weeklydata) or die("Error while selecting user information" . mysql_error());//startttt
                        if (!empty($weeklydataexec)) {
                            if (mysql_num_rows($weeklydataexec) > 0) {
                                while ($row = mysql_fetch_array($weeklydataexec)) {
                                    $weekdetailsarray[] = $row;
                                }
                                $response["weekdetailsarray"] = $weekdetailsarray;
                                $response["wmessage"] = "Records found successfully";
                                array_push($response["weekdetailsarray"],$weekdetailsarray);
                            }else{
                                $weekdetailsarray["message"] = "No Records found ";
                                array_push($response["weekdetailsarray"],$weekdetailsarray);
                            }
                        }
                    }else{
                        $timetrackquery ="SELECT t.date,t.hours,t.minutes,t.description,t.updated_date, p.project_name, u.email_id, u.u_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
                        $weekdetailsarray[] = $row;
                        $weekdetailsarray["success"] = 1;
                        $weekdetailsarray["message"] = "Admin user";
                    }
					$response["timetrackdetails"] = getTimeTrackDetailsArray($timetrackquery);
                    
                }else{
                      $response["error"] = 0;
                      $response["message"] = "User details not found";
                      //echo json_encode($response);
                }
            }else{
				$response["error"] = 1;
				$response["message"] = "User record not found in db. Please, Redirect to home page for login.";
			}
    }else{
        $response["error"] = 1;
        $response["message"] = "failed to delete in timetrack record";
    }
    echo json_encode($response);
}
mysql_close($conn);
?>
