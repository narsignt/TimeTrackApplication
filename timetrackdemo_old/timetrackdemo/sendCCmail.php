 <?php
include_once "connection.php";

//$to =date('Y-m-d');
$from_date=date('Y-m-d',strtotime($current_date . '-7 days'));
//$from=date('Y-m-d',strtotime($currentDate . “-3 days"));

/*$email_sql = "select user_id, email_id, role, count(*), curdate() from (select distinct u.user_id, u.email_id, u.role, te.date 
from time_entry te left join users u on u.user_id = te.user_id where te.date between subdate(curdate(), 7)and curdate() group by u.user_id, te.date) as tmcount group by user_id having count(*) < 5";*/
	
	$email_sql = "select user_id, email_id, role, 0 as trackcount, curdate() from users where user_id not in (select distinct u.user_id 
from time_entry te left join users u on u.user_id = te.user_id where te.date between subdate(curdate(), 7) and curdate() group by user_id, date having count(*) < 7)

union
select user_id, email_id, role, count(*) as trackcount, curdate() from (select distinct u.user_id, u.email_id, u.role, te.date 
from time_entry te left join users u on u.user_id = te.user_id where te.date between subdate(curdate(), 7) and curdate() group by user_id, date having count(*) < 7) as tmcount";
//print $email_sql;
$result = mysql_query($email_sql);

if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    die($message);
	
}else {
	
		while ($row = mysql_fetch_assoc($result)) {
			$list_of_email =  $row["email_id"];
			$list_of_role = $row["role"];
			
			echo $from_date;
			$current_date = $row["curdate()"];
			echo $current_date;
				if(intval($count) < 5){
					$to = "$list_of_email"; 
					echo $to. '</br>';
					$subject = "Time Tracker update - Reminder";
					$body="<html><header></header><body><P>Hi,</p>";
					$body.="<p>  Please update your efforts in the Time Tracker for the week starting $from_date </p>"
					$body .="<br/>";
					$body .= '<a href="https://italent.jiveon.com/groups/time-track-demo" target="_blank">Click here to enter your effort.</a>';
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
				
				
				}
			}

				 $response["error"]   = 0;
				 $response["message"] = "User details not found";
									
	 }
			
mysql_close($conn);
 ?>

