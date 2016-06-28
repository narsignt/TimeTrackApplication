<?php
include_once "connection.php";
//include "audi_session.php";
//paramenters
//get username and password

$user_id   = $_GET['user_id'];
$email_id = $_GET['uname'];
$u_name = $_GET['UserName'];
$empno = $_GET['EmpNumber'];
$country = $_GET['Country'];
$user_id   = base64_decode($user_id);
$email_id = base64_decode($email_id);
$u_name = base64_decode($u_name);
$empno = base64_decode($empno);
$country = base64_decode($country);


$url = "https://italent.jiveon.com/api/core/v3/people/" . $user_id;
$ch  = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


$user      = "npanta@italentcorp.com";
$pass      = "Q!w2e3r4";
$post_data = $user . ":" . $pass;
$flag = false;
$obj;
curl_setopt($ch, CURLOPT_USERPWD, $post_data);
$data = curl_exec($ch);
curl_close($ch);
if ($data != strip_tags($data)) {
   $flag = true;
}else{
	$search   = "throw 'allowIllegalResourceCall is false.';";
	$jiveApiResponse = str_replace($search, '', $data);
	$obj = json_decode($jiveApiResponse, true);
}



if (($obj[error][status] != 404) && ($flag != true)) {
    //check connection
    //saveAuditSessionLogin($user_id,$u_name);
    //saveSessionUsersAudit($Session_Id,'Logged in', 'Time Tracker');
    if ($conn) {
        //echo "connecion---".$conn ."YYYYYYYYY";
        $projectquery = "SELECT * FROM  `projects` where status =1 ORDER BY project_name";
        $projectexecution = mysql_query($projectquery) or die("Error while selecting projects " . mysql_error());
        $userquery = "select user_id,email_id,u_name,country,empno,role from users where user_id = '$user_id' ";
        $userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());
        if (!empty($userqueryexec)) {
            
            if (mysql_num_rows($userqueryexec) > 0) {
                $response["success"]     = 1;
                $response["userdetails"] = array();
                while ($row = mysql_fetch_array($userqueryexec)) {
                    $userrole                 = $row["role"];
                    $userid                   = $row["user_id"];
                    if(strcmp($row["role"], "Admin") !== 0){
                        $userdetails              = array();
                        $userdetails["user_id"]   = $row["user_id"];
                        $userdetails["email_id"] = $row["email_id"];
                        $userdetails["u_name"] = $row["u_name"];
						$userdetails["country"] = $row["country"];
						$userdetails["empno"] = $row["empno"];
                        $userdetails["role"]      = $row["role"];
                        $userrole                 = $row["role"];
                        $userid                   = $row["user_id"];
                        array_push($response["userdetails"], $userdetails);
                    }else{
                        $adminquery = "(select * from users where user_id = '$user_id' ) union (select * from users where user_id != '$user_id')";
                        $adminqueryexec = mysql_query($adminquery) or die("Error while selecting admin u information" . mysql_error());
                        while ($row = mysql_fetch_array($adminqueryexec)) {
                            $userdetails              = array();
                            $userdetails["user_id"]   = $row["user_id"];
                            $userdetails["email_id"] = $row["email_id"];
                            $userdetails["u_name"] = $row["u_name"];
							$userdetails["country"] = $row["country"];
							$userdetails["empno"] = $row["empno"];
                            $userdetails["role"]      = $row["role"];
                            array_push($response["userdetails"], $userdetails);
                        }
                    }

                }
                $response["projectdetails"] = array();
                while ($row = mysql_fetch_array($projectexecution)) {
                    $projectdetails                 = array();
                    $projectdetails["project_id"]   = $row["project_id"];
                    $projectdetails["project_name"] = $row["project_name"];
                    array_push($response["projectdetails"], $projectdetails);
                }
                if (strcmp($userrole, "Admin") !== 0) {
                    //$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry where user_id = '$userid' ";
                    $timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name, u.country,IFNULL( u.empno,  '' ) empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$userid' ORDER BY t.updated_date DESC";
                    $timetrackexec = mysql_query($timetrackquery) or die("Error while selecting time_entry " . mysql_error());
                    $response["timetrackdetails"] = array();
                    while ($row = mysql_fetch_array($timetrackexec)) {
                        $timetrackdetails                 = array();
                        $timetrackdetails["id"]           = $row["id"];
                        $timetrackdetails["user_id"]      = $row["user_id"];
                        $timetrackdetails["project_id"]   = $row["project_id"];
                        $timetrackdetails["project_name"] = $row["project_name"];
                        $timetrackdetails["date"]         = $row["date"];
                        $timetrackdetails["hours"]        = $row["hours"];
                        $timetrackdetails["minutes"]      = $row["minutes"];
                        $timetrackdetails["description"]  = $row["description"];
                        $timetrackdetails["email_id"]    = $row["email_id"];
						$timetrackdetails["u_name"]    = $row["u_name"];
						$timetrackdetails["country"]    = $row["country"];
						$timetrackdetails["empno"]    = $row["empno"];
                        $timetrackdetails["updated_date"] = $row["updated_date"];
                        array_push($response["timetrackdetails"], $timetrackdetails);
                    }
                    
                } else {
                    //$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry group by user_id, id ";
                    /* $timetrackquery = "SELECT P.project_name, te.id,te.user_id,te.project_id,te.date,te.hours,te.minutes,te.description,te.updated_date, u.user_name FROM time_entry as te 
                    Join projects as P on P.project_id=te.project_id
                    Join users as u on u.user_id=te.user_id
                    group by te.user_id, te.id  order by te.updated_date desc"; */
                    ;
                    $timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
                    $timetrackexec = mysql_query($timetrackquery) or die("Error while selecting time_entry " . mysql_error());
                    $response["timetrackdetails"] = array();
                    while ($row = mysql_fetch_array($timetrackexec)) {
                        $timetrackdetails                 = array();
                        $timetrackdetails["id"]           = $row["id"];
                        $timetrackdetails["user_id"]      = $row["user_id"];
                        $timetrackdetails["project_id"]   = $row["project_id"];
                        $timetrackdetails["project_name"] = $row["project_name"];
                        $timetrackdetails["date"]         = $row["date"];
                        $timetrackdetails["hours"]        = $row["hours"];
                        $timetrackdetails["minutes"]      = $row["minutes"];
                        $timetrackdetails["description"]  = $row["description"];
                        $timetrackdetails["email_id"]    = $row["email_id"];
						$timetrackdetails["u_name"]    = $row["u_name"];
						$timetrackdetails["country"]    = $row["country"];
						$timetrackdetails["empno"]    = $row["empno"];
                        $timetrackdetails["updated_date"] = $row["updated_date"];
                        array_push($response["timetrackdetails"], $timetrackdetails);
                    }
                }
            } else {
                $insert_sql = "insert into users (user_id, email_id, u_name,country,empno,role) values ($user_id,'$email_id','$u_name','$country',$empno,'user')";
                if (mysql_query($insert_sql)) {
                    $userquery = "select user_id,email_id,u_name,country,empno,role from users where user_id = '$user_id' ";
                    $userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());
                    if (!empty($userqueryexec)) {
                        if (mysql_num_rows($userqueryexec) > 0) {
                            $response["success"]     = 1;
                            $response["userdetails"] = array();
                            while ($row = mysql_fetch_array($userqueryexec)) {
                                $userdetails              = array();
                                $userdetails["user_id"]   = $row["user_id"];
                                $userdetails["email_id"] = $row["email_id"];
								$userdetails["u_name"] = $row["u_name"];
								$userdetails["country"] = $row["country"];
								$userdetails["empno"]    = $row["empno"];
                                $userdetails["role"]      = $row["role"];
                                $userrole                 = $row["role"];
                                $userid                   = $row["user_id"];
                                array_push($response["userdetails"], $userdetails);
                            }
                            $response["projectdetails"] = array();
                            while ($row = mysql_fetch_array($projectexecution)) {
                                $projectdetails                 = array();
                                $projectdetails["project_id"]   = $row["project_id"];
                                $projectdetails["project_name"] = $row["project_name"];
                                array_push($response["projectdetails"], $projectdetails);
                            }
                            if (strcmp($userrole, "Admin") !== 0) {
                                //$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry  where user_id = '$userid' ORDER BY t.updated_date DESC";
                                $timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$userid' ORDER BY t.updated_date DESC";
                                $timetrackexec = mysql_query($timetrackquery) or die("Error while selecting time_entry " . mysql_error());
                                $response["timetrackdetails"] = array();
                                while ($row = mysql_fetch_array($timetrackexec)) {
                                    $timetrackdetails                 = array();
                                    $timetrackdetails["id"]           = $row["id"];
                                    $timetrackdetails["user_id"]      = $row["user_id"];
                                    $timetrackdetails["project_id"]   = $row["project_id"];
                                    $timetrackdetails["project_name"] = $row["project_name"];
                                    $timetrackdetails["date"]         = $row["date"];
                                    $timetrackdetails["hours"]        = $row["hours"];
                                    $timetrackdetails["minutes"]      = $row["minutes"];
                                    $timetrackdetails["description"]  = $row["description"];
                                    $timetrackdetails["updated_date"] = $row["updated_date"];
                                    $timetrackdetails["email_id"]    = $row["email_id"];
									$timetrackdetails["u_name"]    = $row["u_name"];
									$timetrackdetails["country"]    = $row["country"];
									$timetrackdetails["empno"]    = $row["empno"];
                                    array_push($response["timetrackdetails"], $timetrackdetails);
                                }
                                
                            } else {
                                //$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry group by user_id, id ";
                                $timetrackquery = "SELECT t . * , p.project_name, u.email_id,u.u_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
                                $timetrackexec = mysql_query($timetrackquery) or die("Error while selecting time_entry " . mysql_error());
                                $response["timetrackdetails"] = array();
                                while ($row = mysql_fetch_array($timetrackexec)) {
                                    $timetrackdetails                 = array();
                                    $timetrackdetails["id"]           = $row["id"];
                                    $timetrackdetails["user_id"]      = $row["user_id"];
                                    $timetrackdetails["project_id"]   = $row["project_id"];
                                    $timetrackdetails["project_name"] = $row["project_name"];
                                    $timetrackdetails["date"]         = $row["date"];
                                    $timetrackdetails["hours"]        = $row["hours"];
                                    $timetrackdetails["minutes"]      = $row["minutes"];
                                    $timetrackdetails["description"]  = $row["description"];
                                    $timetrackdetails["updated_date"] = $row["updated_date"];
                                    $timetrackdetails["email_id"]    = $row["email_id"];
									$timetrackdetails["u_name"]    = $row["u_name"];
									$timetrackdetails["country"]    = $row["country"];
									$timetrackdetails["empno"]    = $row["empno"];
                                    array_push($response["timetrackdetails"], $timetrackdetails);
                                }
                            }
                            
                            
                        } else {
                            $response["error"]   = 0;
                            $response["message"] = "User details not found";
                            
                        }
                        
                    }
                } else {
                    // no userdetails found
                    $response["success"] = 0;
                    $response["message"] = "Failed to insert";
                }
            }
            echo json_encode($response);
        }
    } else {
        die("Connection failed: " . mysql_connect_error());
    }
    mysql_close($conn);
}else if(($obj[error][status] == 404) ){
    echo json_encode($jiveApiResponse);
}else{
    $jsonErrorMsg = array('error' => 'Bad request');
    echo json_encode($jsonErrorMsg);
}




?>