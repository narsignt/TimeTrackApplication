<?PHP
  include_once "connection.php";
  $user_id = $_GET['user_id'];
  $fromdate = $_GET['fromdate'];
  $todate = $_GET['todate'];
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
                        $userdetails["last_name"] = $row["last_name"];
                        $userdetails["country"] = $row["country"];
			$userdetails["empno"] = $row["empno"];
                        $userdetails["role"] = $row["role"];
                        $userrole = $row["role"];
                        $userid = $row["user_id"];
                        array_push($response["userdetails"],$userdetails);
                    }
   if(strcmp($userrole, "Admin") !==0)
                    {
                        $timetrackquery = "SELECT t . * , p.project_name, u.email_id, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.user_id ='$userid' AND t.week_mode =0 AND (date BETWEEN '$fromdate' AND '$todate') ORDER BY t.updated_date DESC";
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
                            $timetrackdetails["first_name"] = $row["first_name"];
                            $timetrackdetails["last_name"] = $row["last_name"];
                            $timetrackdetails["country"]    = $row["country"];
			    $timetrackdetails["empno"]    = $row["empno"];
                            array_push($response["timetrackdetails"],$timetrackdetails);
                        }

                    }else{
							$timetrackquery ="SELECT t . * , p.project_name, u.email_id, u.u_name,u.first_name,u.last_name, u.country, u.empno FROM  `time_entry` t LEFT JOIN projects p ON t.`project_id` = p.project_id LEFT JOIN users u ON t.`user_id` = u.user_id WHERE t.week_mode =0 AND (date BETWEEN '$fromdate' AND '$todate')ORDER BY t.updated_date DESC";
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
                             $timetrackdetails["first_name"] = $row["first_name"];
                             $timetrackdetails["last_name"] = $row["last_name"];
                           $timetrackdetails["country"]= $row["country"];
			   $timetrackdetails["empno"]    = $row["empno"];
                            array_push($response["timetrackdetails"],$timetrackdetails);
                         }
						 // filename for download
  $filename = "website_data_" . date('Ymd') . ".csv";

  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: text/csv");

  $out = fopen("php://output", 'w');

  $flag = false;
  foreach($response["timetrackdetails"] as $row) {
    if(!$flag) {
      // display field/column names as first row
      fputcsv($out, array_keys($row), ',', '"');
      $flag = true;
    }
    array_walk($row, __NAMESPACE__ . '\cleanData');
    fputcsv($out, array_values($row), ',', '"');
  }

  fclose($out);
  exit;
					}
				}else{
                      $response["error"] = 0;
                      $response["message"] = "User details not found";
                      //echo json_encode($response);
                }

            }
 function cleanData(&$str)
  {
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }

  // filename for download
  $filename = "website_data_" . date('Ymd') . ".csv";

  header("Content-Disposition: attachment; filename=\"$filename\"");
  header("Content-Type: text/csv");

  $out = fopen("php://output", 'w');

  $flag = false;
  foreach($response["timetrackdetails"] as $row) {
    if(!$flag) {
      // display field/column names as first row
      fputcsv($out, array_keys($row), ',', '"');
      $flag = true;
    }
    array_walk($row, __NAMESPACE__ . '\cleanData');
    fputcsv($out, array_values($row), ',', '"');
  }

  fclose($out);
  exit;
 mysql_close($conn);
?>