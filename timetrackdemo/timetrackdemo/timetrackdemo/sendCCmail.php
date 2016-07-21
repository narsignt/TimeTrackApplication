<?php
include_once "connection.php";


$from_date=date('Y-m-d',strtotime($current_date . '-7 days'));
echo "log run@";
date_default_timezone_set('America/Dawson_Creek');
echo date(DATE_RFC2822)."<br/>";

	$email_sql = "Select user_id, email_id, role,u_name,  0 as trackcount, curdate() from users where user_id not in (select distinct u.user_id 
                  from time_entry te left join users u on u.user_id = te.user_id where te.date between subdate(curdate(), 7) and subdate(curdate(), 3)
                  group by user_id having count(*) < 5)
				  union
				  select user_id, email_id, role,u_name, trackcount, curdate() from (select distinct u.user_id, u.email_id, u.role, u.u_name, te.date,  COUNT( * ) AS trackcount 
                  from time_entry te left join users u on u.user_id = te.user_id where te.date between subdate(curdate(), 7) and subdate(curdate(), 3) group by user_id having count(*) < 5) as tmcount";

$result = mysql_query($email_sql);

if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    die($message);
	
}else {
	//echo $result;
		while ($row = mysql_fetch_assoc($result)) {		
			$list_of_email =  $row["email_id"];
			$list_of_role = $row["role"];
			$list_of_uname = $row["u_name"];
			$count=$row["trackcount"];   
			//echo $from_date;
			$current_date = $row["curdate()"];
			//echo $current_date;
				if(intval($count) < 5){
					$to =$list_of_email; 
					echo $to. '</br>';
					$subject = "Time Tracker update - Reminder";
					$body="<html><header></header><body><p>Hello $list_of_uname,</p>";
					$body .="<br/>";
					$body .="<P>Please update your Hours in the Time Tracker for the week starting $from_date  </p>";
					$body .="<br/>";
					$body .= '<a href="https://italent.jiveon.com/groups/time-track-demo" target="_blank">Click here to enter your hours.</a>';
					$body .="<br/>";
					$body .="<br/>";
					$body .="<P>Thanks,</P>";
					$body .="<P>iTalent Corporation.</P>";
					$body .="</body></html>";
					
					$headers = "MIME-Version: 1.0" . "\r\n";
			        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					
					$headers .= 'From: <no-reply@italentcorp.com>' . "\r\n";
					$headers .= "Cc: timetracker@italentcorp.com"."\r\n";
					$sent = mail($to, $subject, $body, $headers) ; 
				echo $sent. '</br>';
				//return false;
				
				
				}
			}

				 $response["error"]   = 0;
				 $response["message"] = "User details not found";
									
	 }
			
mysql_close($conn);
 ?>


