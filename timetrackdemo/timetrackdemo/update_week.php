<?php
include_once "connection.php";
include "audi_session.php";
include_once "timetrackCommonFunc.php";
$json =  $_POST['data'];
$user_id = $_POST['user_id'];
$isadmin = $_POST['isadmin'];
$sessionid = $_REQUEST['sessionid'];
$str=str_replace("\\",'',(string)$json);
$data = json_decode($str);
$wyear=$data->year;
$wmonth=$data->month;
$project_id='188';
$cntrl=$_POST['cntrl'];
$cntrl=base64_decode($cntrl);
$exec_query;
/* cntrl is used to authenticate from hacker */
if($cntrl != "italent is the best"){
	$response["error"]     = 1;
	$response["message"] = "Token Mismatched";
	echo json_encode($response);
	
}else{
/* Check connection*/
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
	
}else{
	foreach ($data->weeks as $days) {

		$hours=$days->hours;
		$time=strtotime($days->day);
		$newformat = date('Y-m-d',$time);
		/*checking count if data is there or not */
		$checkweekexitquery="select count(*) as cnt from time_entry where user_id= '$user_id' and date='".$newformat."' and week_mode='1'";

		$countquery = mysql_query($checkweekexitquery);
		$data=mysql_fetch_assoc($countquery);
	
		$count=$data['cnt'];	 
	    $action="";
		if(intval($count) > 0){
			/*to change the already existed data*/
			$weekquery = "update time_entry set hours= '$hours' where user_id=$user_id and date = '".$newformat."' and week_mode=1";
			$action="Update Time Track";
		}else{
			/*to inserting a new data*/
			$weekquery = " insert time_entry (project_id, date, hours, week_mode,user_id) values ('$project_id','".$newformat."','".$hours."', 1,'$user_id')";
			 $action="insert Time Track";
		}
		
  	    mysql_query($weekquery)  or die("Error while selecting time_entry " . mysql_error());
		 /*Saving Session audit log when user enter data*/
  	    saveSessionUsersAudit($sessionid, $action, "Week Tab", "Date: ". $newformat .", Projectid: ".$project_id.", Hours: ".$hours.", UserID: ".$user_id);
	}
	$userquery = "select user_id,email_id,u_name,country,empno,role from users where user_id = '$user_id' ";
	$userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());
	if (!empty($userqueryexec)) {
		if (mysql_num_rows($userqueryexec) > 0) {
			$response["success"] = 1;
			$response["message"] = "Updated Successfully";
			
			$response["timetrackdetails"] = getTimeTrackDetails($isadmin, $user_id);
		} else {
			/*inserting userdetails */
			$insert_sql = "insert into users (user_id, email_id, u_name, country,empno, role) values ($user_id,'$email_id','$u_name','$country','$empno','user')";
			if (mysql_query($insert_sql)) {
				$userquery = "select user_id,email_id,u_name,role from users where user_id = '$user_id' ";
				$userqueryexec = mysql_query($userquery) or die("Error while selecting user information" . mysql_error());
				if (!empty($userqueryexec)) {
					if (mysql_num_rows($userqueryexec) > 0) {
						$response["success"]     = 1;
						$response["timetrackdetails"] = getTimeTrackDetails($isadmin, $user_id);

					} else {
						$response["error"]   = 0;
						$response["successage"] = "User details not found";

					}

				}
			} else {
				/* no userdetails found*/
				$response["success"] = 0;
				$response["message"] = "Failed to insert";
			}
		}
		echo json_encode($response);
	}


	 }
}
/*closing db connection*/
mysql_close($conn);
?>
