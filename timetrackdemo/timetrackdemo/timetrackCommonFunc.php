<?php

include_once "connection.php";

function getTimeTrackDetails($isadmin, $userid){

    

    if($isadmin=="false") {
// echo "admin: False";
		$timetrackquery = "SELECT t.user_id, t.id, t.date, t.hours, t.minutes, t.description, t.updated_date, p.project_id, p.project_name, u.email_id, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$userid' ORDER BY t.updated_date DESC";
	}else{
		$timetrackquery ="SELECT t.user_id, t.id, t.date, t.hours, t.minutes, t.description, t.updated_date, p.project_id, p.project_name, u.email_id, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
// echo "admin: true";

	}

	$timetrackexec = mysql_query($timetrackquery)  or die("Error while selecting time_entry " . mysql_error());

    $response["timetrackdet"]=array();

    while($row = mysql_fetch_array($timetrackexec))

    {

        $timetrackdet=array();

        $timetrackdet["id"] = $row["id"];

        $timetrackdet["user_id"] = $row["user_id"];

        $timetrackdet["project_id"] = $row["project_id"];

        $timetrackdet["project_name"] = $row["project_name"];

        $timetrackdet["date"] = $row["date"];

        $timetrackdet["hours"] = $row["hours"];

        $timetrackdet["minutes"] = $row["minutes"];

        $timetrackdet["description"] = $row["description"];

        $timetrackdet["updated_date"] = $row["updated_date"];

		$timetrackdet["email_id"] = $row["email_id"];

		$timetrackdet["u_name"] = $row["u_name"];

        $timetrackdet["first_name"] = $row["first_name"];

        $timetrackdet["last_name"] = $row["last_name"];

        $timetrackdet["country"]    = $row["country"];

		$timetrackdet["empno"]    = $row["empno"];

        array_push($response["timetrackdet"],$timetrackdet);
    }
	return $response["timetrackdet"];                    

}



function updateUserDetails($empno, $country, $user_id){

    $updatequeryexec="UPDATE users SET empno=$empno,country='$country' WHERE user_id = '$user_id'";

    mysql_query($updatequeryexec);

}

function getProjectList(){

    $projectquery = "SELECT project_id, project_name FROM  `projects` where status =1 ORDER BY project_name";

    $projectexecution = mysql_query($projectquery) or die("Error while selecting projects " . mysql_error());

    $response["projectdetails"] = array();

    while ($row = mysql_fetch_array($projectexecution)) {

        $projectdetails                 = array();

        $projectdetails["project_id"]   = $row["project_id"];

        $projectdetails["project_name"] = $row["project_name"];

        array_push($response["projectdetails"], $projectdetails);

    }

    return $response["projectdetails"];

}



function getSelectedUserDetails($user_id){

     $userquery = "select user_id, email_id, u_name, first_name, last_name, country, empno, role from users where user_id = $user_id";

     $userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());

     return $userqueryexec;

}



function fillUserDetails($user_id, &$userdetfound=false, &$userrole){

    $userquery = "select user_id, email_id, u_name, first_name, last_name, country, empno, role from users where user_id = '$user_id' ";

    $userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());

    if (!empty($userqueryexec)) {

        if (mysql_num_rows($userqueryexec) > 0) {

            $response["success"]     = 1;

            $response["userdet"] = array();

            while ($row = mysql_fetch_array($userqueryexec)) {

                $userdet              = array();

                $userdet["user_id"]   = $row["user_id"];

                $userdet["email_id"] = $row["email_id"];

                $userdet["u_name"] = $row["u_name"];

                $userdet["first_name"]=$row["first_name"];

                $userdet["last_name"]=$row["last_name"];

                $userdet["country"] = $row["country"];

                $userdet["empno"]    = $row["empno"];

                $userdet["role"]      = $row["role"];

                $userrole                 = $row["role"];

                //$userid                   = $row["user_id"];

                array_push($response["userdet"], $userdet);

            }

            $userdetfound = true;

        } else {

            $userdetfound = false;

           

        }

        return $response["userdet"];

        

    }else{

        $userdetfound = false;

    }



}


function getUserDetailsArray($userqueryexec, &$userrole){
    //echo $userqueryexec.row;
     $response["success"] = 1;
     $response["userdetArray"] = array();
	 while($row = mysql_fetch_array($userqueryexec))
    {
        $userdetArray = array();
        $userdetArray["user_id"] = $row["user_id"];
        $userdetArray["email_id"] = $row["email_id"];
		$userdetArray["u_name"] = $row["u_name"];
        $userdetArray["first_name"] = $row["first_name"];
        $userdetArray["last_name"] = $row["last_name"];
        $userdetArray["country"] = $row["country"];
		$userdetArray["empno"] = $row["empno"];
        $userdetArray["role"] = $row["role"];
        $userrole = $row["role"];
        array_push($response["userdetArray"],$userdetArray);
        //echo "User role: --".$userrole;
    }
	return $response["userdetArray"];  
}
function getTimeTrackWeekDetails($user_id,$start_date,$end_date){
    $weeklydata = "SELECT t.user_id, t.id, t.date, t.hours, t.minutes, t.description, t.updated_date FROM time_entry t WHERE t.week_mode=1 AND (t.date BETWEEN '$start_date' AND '$end_date') AND t.user_id=$user_id";
    $weeklydataexec = mysql_query($weeklydata) or die("Error while selecting user information" . mysql_error());//startttt
    if (!empty($weeklydataexec)) {
        if (mysql_num_rows($weeklydataexec) > 0) {
            while ($row = mysql_fetch_array($weeklydataexec)) {
                $weekdetails[] = $row;
            }
            $response["weekdetails"] = $weekdetails;
            $response["wmessage"] = "Records found successfully";
            array_push($response["weekdetails"],$weekdetails);
        }else{
            $weekdetails["message"] = "No Records found ";
            array_push($response["weekdetails"],$weekdetails);
        }
    }
    return $response["weekdetails"];
}


?>