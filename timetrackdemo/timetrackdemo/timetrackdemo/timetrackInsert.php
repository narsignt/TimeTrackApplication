<?php
include_once "connection.php";
include_once "timetrackCommonFunc.php";
//$response = array();

// Check connection
$date=$_POST['date'];
$projectid=$_POST['projectid'];
$hours=$_POST['hours'];
$minutes=$_POST['minutes'];
$user_id=$_POST['user_id'];
$description=$_POST['description'];
$description=urldecode($description);
$track_id=$_POST['track_id'];
$exec_query;

if(!empty($track_id)){
    $count_sql =  "SELECT COUNT(*) FROM  `time_entry` WHERE  `id` ='$track_id'";
    $count_exec = mysql_query($count_sql) or die("Error while selecting user information" . mysql_error());
    $edit = mysql_fetch_array($count_exec);
    if($edit[0]==1){
        $exec_query ="update  time_entry SET  project_id=$projectid, date='$date', hours='$hours', minutes='$minutes', description='$description',week_mode='0'  WHERE id=$track_id";
    }else{
        $exec_query = "insert into time_entry (user_id, project_id, date, hours, minutes, description, week_mode)
        values ($user_id,$projectid,'$date','$hours','$minutes','$description','0')";
    }
}else{
    $exec_query = "insert into time_entry (user_id, project_id, date, hours, minutes, description, week_mode)
            values ($user_id,$projectid,'$date','$hours','$minutes','$description', '0')";
}


	if (mysql_query($exec_query)) {
	    $userquery = "select user_id,email_id,u_name,first_name,last_name,country,empno,role from users where user_id = '$user_id' ";
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
                        $userdetails["first_name"] = $row["first_name"];
                        $userdetails["last_name"] = $row["last_name"];
                        $userdetails["country"] = $row["country"];
			            $userdetails["empno"] = $row["empno"];
                        $userdetails["role"] = $row["role"];
                        $userrole = $row["role"];
                        $userid = $row["user_id"];
                        array_push($response["userdetails"],$userdetails);
                      
                    }
                    if(strcmp($userrole, "Admin") !==0){
                            $isadmin = "false";
                        }else{
                            $isadmin = "true";
                        }
					$response["timetrackdetails"] = getTimeTrackDetails($isadmin,$userid);
                    // if(strcmp($userrole, "Admin") !==0)
                    // {
                        // $timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$userid' ORDER BY t.updated_date DESC";
						// $timetrackexec = mysql_query($timetrackquery)  or die("Error while selecting time_entry " . mysql_error());
                        // $response["timetrackdetails"]=array();
                        // while($row = mysql_fetch_array($timetrackexec))
                        // {
                            // $timetrackdetails=array();
                            // $timetrackdetails["id"] = $row["id"];
                            // $timetrackdetails["user_id"] = $row["user_id"];
                            // $timetrackdetails["project_id"] = $row["project_id"];
                            // $timetrackdetails["project_name"] = $row["project_name"];
                            // $timetrackdetails["date"] = $row["date"];
                            // $timetrackdetails["hours"] = $row["hours"];
                            // $timetrackdetails["minutes"] = $row["minutes"];
                            // $timetrackdetails["description"] = $row["description"];
                            // $timetrackdetails["updated_date"] = $row["updated_date"];
			     // $timetrackdetails["email_id"] = $row["email_id"];
			     // $timetrackdetails["u_name"] = $row["u_name"];
                            // $timetrackdetails["first_name"] = $row["first_name"];
                            // $timetrackdetails["last_name"] = $row["last_name"];
                            // $timetrackdetails["country"]    = $row["country"];
			    // $timetrackdetails["empno"]    = $row["empno"];
                            // array_push($response["timetrackdetails"],$timetrackdetails);
                        // }

                    // }else{
							// $timetrackquery ="SELECT t . * , p.project_name, u.email_id, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
						 // $timetrackexec = mysql_query($timetrackquery)  or die("Error while selecting time_entry " . mysql_error());
                         // $response["timetrackdetails"]=array();
                         // while($row = mysql_fetch_array($timetrackexec))
                         // {
                            // $timetrackdetails=array();
                            // $timetrackdetails["id"] = $row["id"];
                            // $timetrackdetails["user_id"] = $row["user_id"];
                            // $timetrackdetails["project_id"] = $row["project_id"];
                            // $timetrackdetails["project_name"] = $row["project_name"];
                            // $timetrackdetails["date"] = $row["date"];
                            // $timetrackdetails["hours"] = $row["hours"];
                            // $timetrackdetails["minutes"] = $row["minutes"];
                            // $timetrackdetails["description"] = $row["description"];
                            // $timetrackdetails["updated_date"] = $row["updated_date"];
			     // $timetrackdetails["email_id"] = $row["email_id"];
			     // $timetrackdetails["u_name"] = $row["u_name"];
                             // $timetrackdetails["first_name"] = $row["first_name"];
                             // $timetrackdetails["last_name"] = $row["last_name"];
                           // $timetrackdetails["country"]= $row["country"];
			   // $timetrackdetails["empno"]    = $row["empno"];
                            // array_push($response["timetrackdetails"],$timetrackdetails);
                         // }
                    // }
                }else{
                      $response["error"] = 0;
                      $response["message"] = "User details not found";
                      //echo json_encode($response);
                }

            }
	} else {
    	// no userdetails found
            $response["success"] = 0;
            $response["message"] = "Failed to insert";
            //echo json_encode($response);
	}
	echo json_encode($response);
	
//}else{
	//update query
//}
 mysql_close($conn);
?>
