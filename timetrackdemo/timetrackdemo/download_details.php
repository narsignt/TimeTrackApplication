<?PHP
  include_once "connection.php";
  // namespace Chirp;
  $user_id = $_GET['user_id'];
  $start_date = $_GET['start_date'];
  $end_date = $_GET['end_date'];
  $mode = $_GET['mode'];
  $username = $_GET['username'];
  if($mode == "week"){
	$weeklydata = "SELECT u.u_name,u.first_name,u.last_name,u.country,u.empno, t.user_id, t.hours, t.date, IFNULL( t.description,  '' ) description, t.updated_date, p.project_name, u.email_id FROM  `time_entry` t
LEFT JOIN projects p ON p.`project_id` = t.project_id
LEFT JOIN users u ON u.`user_id` = t.user_id
WHERE t.week_mode =1 AND (date BETWEEN '$start_date' AND '$end_date')";
//echo $user_id;
    if($user_id == "alluser"){
        $weeklydata = $weeklydata;
		//$UserName=''
    }elseif($user_id == "indianuser"){
        $weeklydata = $weeklydata." And country='India' ORDER BY u.u_name, t.updated_date DESC";
		
    }elseif($user_id == "ususer"){
        $weeklydata = $weeklydata." And country='USA' ORDER BY u.u_name, t.updated_date DESC";
		//echo "USA User";
    }else{
        $weeklydata = "SELECT te.date,te.hours,IFNULL(te.description,'') as description, te.updated_date, u.user_id, u.email_id, p.project_name, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM time_entry te
        LEFT JOIN projects p ON p.`project_id` = te.project_id
Join users u on u.user_id = te.user_id
WHERE te. week_mode=1 AND (date BETWEEN '$start_date' AND '$end_date') AND te.user_id=$user_id";
    }
	//echo $weeklydata;
    $weeklydataexec = mysql_query($weeklydata) or die("Error while selecting user information" . mysql_error());//startttt
    //echo "on $weekquery";
          // if(mysql_num_rows($weeklydataexec) == 0){
			  // echo '<script language="javascript">';
			 // echo 'alert("No Data")';
			 // echo '</script>';	
		 // }
       
            //echo "yearmonthquery";
            //$response["weekdetailsarray"] = array();
            while ($row = mysql_fetch_array($weeklydataexec)) {
                
// $response["success"] = 1;
            // $response["message"] = "Records found successfully";
            $response["downloaddetails"] = array();
            while ($row = mysql_fetch_array($weeklydataexec)) {
                                                $weekdetailsarray              = array();
												$weekdetailsarray["EmployeeNumber"]=$row["empno"];
												$weekdetailsarray["FirstName"] = $row["first_name"];
												$weekdetailsarray["LastName"] = $row["last_name"];
												$weekdetailsarray["email"] = $row["email_id"];
												$weekdetailsarray["Country"] =$row["country"];
												$weekdetailsarray["ProjectName"]  = $row["project_name"];
												$weekdetailsarray["Hours"] = $row["hours"];
                                                $weekdetailsarray["Date"] = $row["date"];
                                                array_push($response["downloaddetails"], $weekdetailsarray);
												//echo $response;
                                              // echo $response["downloaddetails"];
            }
			
				}
  }
  if($mode == "day"){
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
                        $userdetails["LastName"] = $row["last_name"];
                        $userdetails["country"] = $row["country"];
			$userdetails["empno"] = $row["empno"];
                        $userdetails["role"] = $row["role"];
                        $userrole = $row["role"];
                        $userid = $row["user_id"];
                        array_push($response["userdetails"],$userdetails);
						
                    }
   if(strcmp($userrole, "Admin") !==0)
                    {
                        $timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$userid' AND t.week_mode =0 AND (date BETWEEN '$start_date' AND '$end_date') ORDER BY t.updated_date DESC";
						$timetrackexec = mysql_query($timetrackquery)  or die("Error while selecting time_entry " . mysql_error());
                         // if(mysql_num_rows($timetrackexec) == 0){
							 // echo '<script language="javascript">';
							 // echo 'alert("No Data")';
							 // echo '</script>';
							 
							 // }
						$response["downloaddetails"]=array();
                        while($row = mysql_fetch_array($timetrackexec))
                        {
                            $timetrackdetails=array();
                            $timetrackdetails["EmployeeNumber"]    = $row["empno"];
							$timetrackdetails["FirstName"] = $row["first_name"];
							 $timetrackdetails["LastName"] = $row["last_name"];
							  $timetrackdetails["Country"]    = $row["country"];
							  $timetrackdetails["email"] = $row["email_id"];
							  $timetrackdetails["ProjectName"] = $row["project_name"];
							  $timetrackdetails["Date"] = $row["date"];
							   $timetrackdetails["Hours"] = $row["hours"];
							   $timetrackdetails["description"] = $row["description"];
							    $timetrackdetails["UpdatedDate"] = $row["updated_date"];
								array_push($response["downloaddetails"],$timetrackdetails);
							//echo $response;
                        }

                    }else{
							$timetrackquery ="SELECT t . * , p.project_name, u.email_id, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.week_mode =0 AND (date BETWEEN '$start_date' AND '$end_date')ORDER BY t.updated_date DESC";
						 $timetrackexec = mysql_query($timetrackquery)  or die("Error while selecting time_entry " . mysql_error());
                           if(mysql_num_rows($timetrackexec) == 0)
                           {
							   echo "<script type='text/javascript'>alert('No Records');</script>";
						   }
						 $response["downloaddetails"]=array();
                         while($row = mysql_fetch_array($timetrackexec))
                         {
                            $timetrackdetails=array();
                            $timetrackdetails["EmployeeNumber"]    = $row["empno"];
							$timetrackdetails["FirstName"] = $row["first_name"];
							 $timetrackdetails["LastName"] = $row["last_name"];
							  $timetrackdetails["Country"]    = $row["country"];
							  $timetrackdetails["email"] = $row["email_id"];
							  $timetrackdetails["ProjectName"] = $row["project_name"];
							  $timetrackdetails["Date"] = $row["date"];
							   $timetrackdetails["Hours"] = $row["hours"];
							   $timetrackdetails["description"] = $row["description"];
							    $timetrackdetails["UpdatedDate"] = $row["updated_date"];
                            array_push($response["downloaddetails"],$timetrackdetails);
							//echo $response;
                         }
					}
				}else{
                      $response["error"] = 0;
                      $response["message"] = "User details not found";
                      //echo json_encode($response);
                }

            }
  }
    function cleanData(&$str)
    {
      $str = preg_replace("/\t/", "\\t", $str);
      $str = preg_replace("/\r?\n/", "\\n", $str);
      if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    }

 
    $filename = "Timetrack_ $mode _ $username .xls";

    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Type: application/vnd.ms-excel");

    $flag = false;
    foreach($response["downloaddetails"] as $row) {
    if(!$flag) {
   
       echo implode("\t", array_keys($row)) . "\n";
        $flag = true;
      }
     array_walk($row, __NAMESPACE__ . '\cleanData');
     echo implode("\t", array_values($row)) . "\n";
    }

 mysql_close($conn);
?>