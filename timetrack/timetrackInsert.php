<?php
include_once "connection.php";
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
//$randomfield=$_GET['randomfield'];


$exec_query;
if(!empty($track_id)){
    $count_sql =  "SELECT COUNT( * ) FROM  `time_entry` WHERE  `id` ='$track_id'";
    $count_exec = mysql_query($count_sql) or die("Error while selecting user information" . mysql_error());
    $edit = mysql_fetch_array($count_exec);
    if($edit[0]==1){
        $exec_query ="update  time_entry SET user_id=$user_id, project_id=$projectid, date='$date', hours='$hours', minutes='$minutes', description='$description'  WHERE id=$track_id";
    }else{
        $exec_query = "insert into time_entry (user_id, project_id, date, hours, minutes, description)
        values ($user_id,$projectid,'$date','$hours','$minutes','$description')";
    }
}else{
    $exec_query = "insert into time_entry (user_id, project_id, date, hours, minutes, description)
            values ($user_id,$projectid,'$date','$hours','$minutes','$description')";
}
//echo $exec_query;
//$insert_sql = "insert into time_entry (user_id, project_id, date, hours, minutes, description) values ($user_id,$projectid,'$date','$hours','$minutes','$description')";

	if (mysql_query($exec_query)) {
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

                    if(strcmp($userrole, "Admin") !==0)
                    {
                        //$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry where user_id = '$userid' ";
                        //$timetrackquery = "select P.project_name, te.id,te.user_id,te.project_id,te.date,te.hours,te.minutes,te.description,te.updated_date FROM time_entry as te Join projects as P on P.project_id=te.project_id where te.user_id = '$userid' order by te.updated_date desc";
                        $timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$userid' ORDER BY t.updated_date DESC";
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
                         //$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry group by user_id, id ";
                         //$timetrackquery = "SELECT P.project_name, te.id,te.user_id,te.project_id,te.date,te.hours,te.minutes,te.description,te.updated_date FROM time_entry as te Join projects as P on P.project_id=te.project_id group by te.user_id, te.id  order by te.updated_date desc";
                         /*$timetrackquery = "SELECT P.project_name, te.id,te.user_id,te.project_id,te.date,te.hours,te.minutes,te.description,te.updated_date, u.user_name FROM time_entry as te 
					                    Join projects as P on P.project_id=te.project_id
										Join users as u on u.user_id=te.user_id
										group by te.user_id, te.id  order by te.updated_date desc";*/
							$timetrackquery ="SELECT t . * , p.project_name, u.email_id, u.u_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
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
                    }
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
            echo json_encode($response);
	}
	echo json_encode($response);
	
//}else{
	//update query
//}
 mysql_close($conn);
?>
