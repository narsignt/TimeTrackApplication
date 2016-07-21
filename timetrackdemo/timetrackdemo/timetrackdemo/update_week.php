<?php
include_once "connection.php";
include "audi_session.php";
include_once "timetrackCommonFunc.php";
$json =  $_POST['data'];
$user_id = $_POST['user_id'];
$isadmin = $_POST['isadmin'];
$sessionid = $_REQUEST['sessionid'];
//echo $json;
$str=str_replace("\\",'',(string)$json);
//echo (string)$str. "</br></br>";
$data = json_decode($str);
$wyear=$data->year;
$wmonth=$data->month;
$project_id='188';

// Check connectionx
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
	
}else{
	foreach ($data->weeks as $days) {

		$hours=$days->hours;
		$time=strtotime($days->day);
		$newformat = date('Y-m-d',$time);

		$checkweekexitquery="select count(*) as cnt from time_entry where user_id= '$user_id' and date='".$newformat."' and week_mode='1'";

		$countquery = mysql_query($checkweekexitquery);
		$data=mysql_fetch_assoc($countquery);
	
		$count=$data['cnt'];	 
	    $action="";
		if(intval($count) > 0){
			$weekquery = "update time_entry set hours= '$hours' where user_id=$user_id and date = '".$newformat."' and week_mode=1";
			$action="Update Time Track";
		}else{
			$weekquery = " insert time_entry (project_id, date, hours, week_mode,user_id) values ('$project_id','".$newformat."','".$hours."', 1,'$user_id')";
		//echo "week"+ $weekquery;
			 $action="insert Time Track";
		}
		
  	    mysql_query($weekquery)  or die("Error while selecting time_entry " . mysql_error());
  	    saveSessionUsersAudit($sessionid, $action, "Week Tab", "Date: ". $newformat .", Projectid: ".$project_id.", Hours: ".$hours.", UserID: ".$user_id);
	}
	//echo "yearmonthquery";
	//$response["message"] = "Updated Successfully";
	$userquery = "select user_id,email_id,u_name,country,empno,role from users where user_id = '$user_id' ";
	$userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());
	if (!empty($userqueryexec)) {
		if (mysql_num_rows($userqueryexec) > 0) {
			$response["success"] = 1;
			$response["message"] = "Updated Successfully";
			//if (strcmp($userrole, "Admin") !== 0)
			// if($isadmin === "true")
			// {
			// 	$timetrackquery = "SELECT t.date,t.hours,t.minutes,t.description,t.updated_date , p.project_name, u.email_id, u.u_name, u.country,u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
			// 	$response["timetrackdetails"] = getTimeTrackDetailsArray($timetrackquery);
			// } else {
			// 	$timetrackquery = "SELECT t.date,t.hours,t.minutes,t.description,t.updated_date , p.project_name, u.email_id, u.u_name, u.country,u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id = '$user_id' ORDER BY t.updated_date DESC";
			// 	$response["timetrackdetails"] = getTimeTrackDetailsArray($timetrackquery);
			// }
			$response["timetrackdetails"] = getTimeTrackDetails($isadmin, $user_id);
		} else {
			$insert_sql = "insert into users (user_id, email_id, u_name, country,empno, role) values ($user_id,'$email_id','$u_name','$country','$empno','user')";
			if (mysql_query($insert_sql)) {
				$userquery = "select user_id,email_id,u_name,role from users where user_id = '$user_id' ";
				$userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());
				if (!empty($userqueryexec)) {
					if (mysql_num_rows($userqueryexec) > 0) {
						$response["success"]     = 1;
						//if (strcmp($userrole, "Admin") !== 0)
						// if($isadmin === "false")
						// {
						// 	//$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry  where user_id = '$userid' ORDER BY t.updated_date DESC";
						// 	$timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$user_id' ORDER BY t.updated_date DESC";
						// 	$response["timetrackdetails"] = getTimeTrackDetailsArray($timetrackquery);

						// } else {
						// 	//$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry group by user_id, id ";
						// 	$timetrackquery = "SELECT t . * , p.project_name, u.email_id,u.u_name,u.country,u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
						// 	$response["timetrackdetails"] = getTimeTrackDetailsArray($timetrackquery);
						// }
						$response["timetrackdetails"] = getTimeTrackDetails($isadmin, $user_id);

					} else {
						$response["error"]   = 0;
						$response["successage"] = "User details not found";

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


	 }
mysql_close($conn);
?>
