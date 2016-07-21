<?PHP
  include_once "connection.php";
  $user_id = $_POST['user_id'];
  $user_id = str_replace("%00%00",'',$user_id);
  echo $user_id;
  $start_date = $_POST['start_date'];
  echo $start_date;
  $end_date = $_POST['end_date'];
  echo $end_date;
  $mode = $_POST['mode'];
  echo $mode;
  $username = $_POST['username'];
  echo $username;
  $isadmin =$_POST['isadmin'];
	if($mode == "week"){
		  // This is for week tab data downloading
		$weeklydata = "SELECT u.u_name,u.first_name,u.last_name,u.country,u.empno, t.user_id, t.hours, t.date, IFNULL( t.description,  '' ) description, t.updated_date, p.project_name, u.email_id FROM  `time_entry` t
		               JOIN projects p ON p.`project_id` = t.project_id
		               JOIN users u ON u.`user_id` = t.user_id
		               WHERE t.week_mode =1 AND (date BETWEEN '$start_date' AND '$end_date')";
		if($user_id == "alluser"){
		    //$weeklydata = $weeklydata;
		}elseif($user_id == "indianuser"){
		    $weeklydata = $weeklydata." And country='India' ORDER BY u.u_name, t.updated_date DESC";
			
		}elseif($user_id == "ususer"){
		    $weeklydata = $weeklydata." And country='USA' ORDER BY u.u_name, t.updated_date DESC";
		}else{
		    $weeklydata = "SELECT te.date,te.hours,IFNULL(te.description,'') as description, te.updated_date, u.user_id, u.email_id, p.project_name, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM time_entry te
		                   LEFT JOIN projects p ON p.`project_id` = te.project_id
		                   Join users u on u.user_id = te.user_id
		                   WHERE te. week_mode=1 AND (date BETWEEN '$start_date' AND '$end_date') AND te.user_id=$user_id";
	    }
	    filltimetrackdatatoArray($weeklydata, $mode, $username);

	    
	}else{ //if($mode == "day")
        // This is for day tab data downloading
		$timetrackquery = "SELECT t.user_id, t.hours, t.date, IFNULL( t.description,  '' ) description, t.updated_date, p.project_name, u.email_id, u.u_name,u.first_name,u.last_name, u.country, u.empno 
		FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id 
		WHERE ";
		//echo($isadmin);
		if ($isadmin == "false")
		{
			//echo "fdff user";
			//echo "WWWW ".$user_id." IIII ";
			$timetrackquery .=  " t.user_id ='$user_id' AND " ;
		}
		$timetrackquery .=  " (date BETWEEN '$start_date' AND '$end_date') ORDER BY t.updated_date DESC";
		//echo $timetrackquery;

		filltimetrackdatatoArray($timetrackquery,$mode,$username);
	  
	}
	function cleanData(&$str)
	{
		$str = preg_replace("/\t/", "\\t", $str);
		$str = preg_replace("/\r?\n/", "\\n", $str);
		if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
	 }

	function filltimetrackdatatoArray($QryStr,$mode,$username){
		//Filling data to array from DB to download excel file. Changed by Sunil July 15 2016
		$timetrackexec = mysql_query($QryStr)  or die("Error while dowload the Time track data " . mysql_error());
		
		$response["downloaddetails"]=array();
		while($row = mysql_fetch_array($timetrackexec))
		{
			  $timetrackdetails=array();
			  $timetrackdetails["Employee Number"]    = $row["empno"];
			  $timetrackdetails["First Name"] = $row["first_name"];
			  $timetrackdetails["Last Name"] = $row["last_name"];
			  $timetrackdetails["Country"]    = $row["country"];
			  $timetrackdetails["email"] = $row["email_id"];
			  $timetrackdetails["Project Name"] = $row["project_name"];
			  $timetrackdetails["Date"] = $row["date"];
			  $timetrackdetails["Hours"] = $row["hours"];
			  $timetrackdetails["Description"] = $row["description"];
			  $timetrackdetails["Updated Date"] = $row["updated_date"];
			  array_push($response["downloaddetails"],$timetrackdetails);
		} 
		$filename = "Timetrack_". $mode ."_ ". $username .".xls";
		//$filename = "Timetrack_$mode _$username.xls";
		header("Content-Disposition: attachment; filename=\"".$filename."\";");
		//header("Content-Type: application/vnd.ms-excel");
		header("Content-Type: application/csv");
		$flag = false;
		foreach($response["downloaddetails"] as $row) {
		   if(!$flag) {

		      echo implode("\t", array_keys($row)) . "\n";
  		      $flag = true;
		   }
		   array_walk($row, __NAMESPACE__ . '\cleanData');
		   echo implode("\t", array_values($row)) . "\n";
	   }
	}
    // var_dump($response);



   mysql_close($conn);
?>