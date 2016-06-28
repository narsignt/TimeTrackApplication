<?php
include_once "connection.php";

//paramenters
//get username and password

$user_id = $_GET['user_id'];
$user_name = $_GET['uname'];

echo base64_decode($user_id);
echo base64_decode($user_name);

//check connection
if($conn)
{
    $projectquery = "select project_id, project_name  from  projects";
    $projectexecution = mysql_query($projectquery)  or die("Error while selecting projects " . mysql_error());
    $userquery = "select user_id,user_name,role from users where user_id = '$user_id' ";
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
                $userdetails["user_name"] = $row["user_name"];
                $userdetails["role"] = $row["role"];
                $userrole = $row["role"];
                $userid = $row["user_id"];
                array_push($response["userdetails"],$userdetails);
            }
            $response["projectdetails"]=array();
                while($row = mysql_fetch_array($projectexecution))
                {
                    $projectdetails=array();
                    $projectdetails["project_id"] = $row["project_id"];
                    $projectdetails["project_name"] = $row["project_name"];
                    array_push($response["projectdetails"],$projectdetails);
                }
                if(strcmp($userrole, "Admin") !==0)
                {
                    //$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry where user_id = '$userid' ";
                    $timetrackquery = "SELECT t . * , p.project_name, u.user_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$userid'";
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
							$timetrackdetails["user_name"] = $row["user_name"];
                            $timetrackdetails["updated_date"] = $row["updated_date"];
                            array_push($response["timetrackdetails"],$timetrackdetails);
                        }

                }else{
                     //$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry group by user_id, id ";
                     /* $timetrackquery = "SELECT P.project_name, te.id,te.user_id,te.project_id,te.date,te.hours,te.minutes,te.description,te.updated_date, u.user_name FROM time_entry as te 
					                    Join projects as P on P.project_id=te.project_id
										Join users as u on u.user_id=te.user_id
										group by te.user_id, te.id  order by te.updated_date desc"; */;
					 $timetrackquery = "SELECT t . * , p.project_name, u.user_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
                     $timetrackexec = mysql_query( $timetrackquery)  or die("Error while selecting time_entry " . mysql_error());
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
							$timetrackdetails["user_name"] = $row["user_name"];
                            $timetrackdetails["updated_date"] = $row["updated_date"];
                            array_push($response["timetrackdetails"],$timetrackdetails);
                        }
                }
        }else
        {
            $insert_sql = "insert into users (user_id, user_name, role) values ($user_id,'$user_name','user')";
            if (mysql_query($insert_sql)){
                $userquery = "select user_id,user_name,role from users where user_id = '$user_id' ";
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
                                $userdetails["user_name"] = $row["user_name"];
                                $userdetails["role"] = $row["role"];
                                $userrole = $row["role"];
                                $userid = $row["user_id"];
                                array_push($response["userdetails"],$userdetails);
                            }
                            $response["projectdetails"]=array();
                                            while($row = mysql_fetch_array($projectexecution))
                                            {
                                                $projectdetails=array();
                                                $projectdetails["project_id"] = $row["project_id"];
                                                $projectdetails["project_name"] = $row["project_name"];
                                                array_push($response["projectdetails"],$projectdetails);
                                            }
                                            if(strcmp($userrole, "Admin") !==0)
                                            {
                                                //$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry  where user_id = '$userid' ";
                                                $timetrackquery = "SELECT t . * , p.project_name, u.user_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$userid' ORDER BY t.updated_date DESC";
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
														$timetrackdetails["user_name"] = $row["user_name"];
                                                        array_push($response["timetrackdetails"],$timetrackdetails);
                                                    }

                                            }else{
                                                 //$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry group by user_id, id ";
                                                 $timetrackquery = "SELECT t . * , p.project_name, u.user_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
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
														$timetrackdetails["user_name"] = $row["user_name"];
                                                        array_push($response["timetrackdetails"],$timetrackdetails);
                                                    }
                                            }


                        }else{
                              $response["error"] = 0;
                              $response["message"] = "User details not found";
                              //echo json_encode($response);
                        }

                    }
                } else{
                    	// no userdetails found
                        $response["success"] = 0;
                        $response["message"] = "Failed to insert";
                    }
            }
        echo json_encode($response);
    }
}else{
    die("Connection failed: " . mysql_connect_error());
}
mysql_close();
?>