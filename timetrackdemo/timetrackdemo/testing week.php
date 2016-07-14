<?php
include_once "connection.php";


$user_id=$_POST['user_id'];
$wyear=$_POST['wyear'];
$wmonth=$_POST['wmonth'];
$hours=$_POST['hours'];
$project_id=$_POST['project_id'];
$dat='2015-04-15';
// Check connection
if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
}else{
    $checkweekexitquery="select count(*) from time_entry where user_id= '$user_id' and 'date'='$dat' and 'week_mode'='1'";//date ="77" and workset=1
    $countquery = mysql_query($checkweekexitquery) or die("Error in Selecting " . mysql_error());
    $data=mysql_fetch_assoc($countquery);
    $count=$data[0];
	echo "WWWW ".$count;
    if(intval($count) > 0){
        $weekquery = " update time_entry set hours= '$hours' where user_id=$user_id and date = '$dat' and week_mode=1";
    }else{
        $weekquery = " insert time_entry (project_id, date, hours, week_mode) value ('$project_id','$dat','$hours', 1)";
    }
     if (mysql_query($weekquery)) {
			$yearmonthquery="SELECT * FROM time_entry WHERE MONTH(date) = '$wmonth' AND YEAR(date) = '$wyear'";
			$yearmonthqueryexec = mysql_query($yearmonthquery) or die("Error while selecting user information" . mysql_error());//startttt
        if (!empty($yearmonthqueryexec)) {
            if (mysql_num_rows($yearmonthqueryexec) > 0) {
                $response["weekdetails"] = array();
                while ($row = mysql_fetch_array($yearmonthqueryexec)) {
                  //  $weekdetails              = array();
                    $weekdetailsarray[] = $row;
                }
					$response["success"] = 1;
					$response["message"] = "Inserted Successfully";
					$response["weekdetails"] = $weekarray;
					//mysql_fetch_assoc($result);
			}
		} else{
            $response["error"] = 1;
            $response["message"] = "failed to insert in weeks";
        }
        echo json_encode($response);
	}
	 }
mysql_close($conn);
?>