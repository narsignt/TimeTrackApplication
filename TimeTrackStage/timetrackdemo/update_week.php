<?php
include_once "connection.php";

$json =  $_POST['data'];
$user_id = $_POST['user_id'];
$isadmin = $_POST['isadmin'];
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
	 
		if(intval($count) > 0){
			$weekquery = "update time_entry set hours= '$hours' where user_id=$user_id and date = '".$newformat."' and week_mode=1";
		}else{
			$weekquery = " insert time_entry (project_id, date, hours, week_mode,user_id) values ('$project_id','".$newformat."','".$hours."', 1,'$user_id')";
		echo "week"+ $weekquery;
		}
		
  	    mysql_query($weekquery)  or die("Error while selecting time_entry " . mysql_error());
	}
	//echo "yearmonthquery";
	//$response["message"] = "Updated Successfully";
	$userquery = "select user_id,email_id,u_name,role from users where user_id = '$user_id' ";
	$userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());
	if (!empty($userqueryexec)) {
		if (mysql_num_rows($userqueryexec) > 0) {
			$response["success"] = 1;
			$response["message"] = "Updated Successfully";
			//if (strcmp($userrole, "Admin") !== 0)
			if($isadmin === "true")
			{
				//$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry where user_id = '$userid' ";
				//$timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$userid' ORDER BY t.updated_date DESC";

				//Admin query
				$timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
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
					$timetrackdetails["updated_date"] = $row["updated_date"];
					array_push($response["timetrackdetails"], $timetrackdetails);
				}

			} else {
				//$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry group by user_id, id ";
				/* $timetrackquery = "SELECT P.project_name, te.id,te.user_id,te.project_id,te.date,te.hours,te.minutes,te.description,te.updated_date, u.user_name FROM time_entry as te
				Join projects as P on P.project_id=te.project_id
				Join users as u on u.user_id=te.user_id
				group by te.user_id, te.id  order by te.updated_date desc"; */
				//user query
				$timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id = '$user_id' ORDER BY t.updated_date DESC";
				//$timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
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
					$timetrackdetails["updated_date"] = $row["updated_date"];
					array_push($response["timetrackdetails"], $timetrackdetails);
				}
			}
		} else {
			$insert_sql = "insert into users (user_id, email_id, u_name, role) values ($user_id,'$email_id','$u_name','user')";
			if (mysql_query($insert_sql)) {
				$userquery = "select user_id,email_id,u_name,role from users where user_id = '$user_id' ";
				$userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());
				if (!empty($userqueryexec)) {
					if (mysql_num_rows($userqueryexec) > 0) {
						$response["success"]     = 1;
						//if (strcmp($userrole, "Admin") !== 0)
						if($isadmin === "false")
						{
							//$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry  where user_id = '$userid' ORDER BY t.updated_date DESC";
							$timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$user_id' ORDER BY t.updated_date DESC";
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
								array_push($response["timetrackdetails"], $timetrackdetails);
							}

						} else {
							//$timetrackquery = "select id,user_id,project_id,date,hours,minutes,description,updated_date from time_entry group by user_id, id ";
							$timetrackquery = "SELECT t . * , p.project_name, u.email_id,u.u_name FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id ORDER BY t.updated_date DESC";
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
								array_push($response["timetrackdetails"], $timetrackdetails);
							}
						}

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
		 /*else{
            $response["error"] = 1;
            $response["message"] = "failed to insert in weeks";
        }*/

	//}

	 }
mysql_close($conn);
?>