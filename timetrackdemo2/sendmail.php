<?php
include_once "connection.php";

//$to =date('Y-m-d');
$from_date=date('Y-m-d',strtotime($current_date . '-7 days'));
//$from=date('Y-m-d',strtotime($currentDate . “-3 days"));

/*$email_sql = "select user_id, email_id, role, count(*), curdate() from (select distinct u.user_id, u.email_id, u.role, te.date 
from italentnewdb.time_entry te left join italentnewdb.users u on u.user_id = te.user_id where te.date between subdate(curdate(), 7)and curdate() group by u.user_id, te.date) as tmcount group by user_id having count(*) < 5";*/
	
	$email_sql = "select user_id, email_id, role, 0 as trackcount, curdate() from users where user_id not in (select distinct u.user_id 
from italentnewdb.time_entry te left join italentnewdb.users u on u.user_id = te.user_id where te.date between subdate(curdate(), 7) and curdate() group by user_id, date having count(*) < 7)

union
select user_id, email_id, role, count(*) as trackcount, curdate() from (select distinct u.user_id, u.email_id, u.role, te.date 
from italentnewdb.time_entry te left join italentnewdb.users u on u.user_id = te.user_id where te.date between subdate(curdate(), 7) and curdate() group by user_id, date having count(*) < 7) as tmcount";
print $email_sql;
$result = mysql_query($email_sql);

if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    die($message);
	
}else {
	
		while ($row = mysql_fetch_assoc($result)) {
			//$email_notify  = array();
			//$email_notify["user_id"]   = $row["user_id"];
			//echo $row["user_id"];
			$list_of_email =  $row["email_id"];
			$list_of_role = $row["role"];
			
			echo $from_date;
			$current_date = $row["curdate()"];
			//print_r($list_of_email);
			//print_r (explode(',',$list_of_email));
			//echo $list_of_email;
			echo $current_date;
			//array_push($response["email_notify"], $email_notify);
				if(intval($count) < 5){
					$to = "$list_of_email"; 
					echo $to. '</br>';
					$subject = "Test Mail (please Ignore)";
					$body="<P>Hi, Please Update Your Daily Time track Status at week $from_date  </p>";
					$body .="<br/>";
					$body .= '<a href="https://italent.jiveon.com/groups/time-track-demo" target="_blank">Enter Your Status Here</a>';
					$body .="<br/>";
					$body .="<br/>";
					$body .="Thanks,";
					$body .="<br/>";
					$body .="iTalent.";
					$headers = "From: $from\n";
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					//$headers .= "Cc: hr@italentcorp.com";
					$sent = mail($to, $subject, $body, $headers) ; 
				echo $sent. '</br>';
				
				
				}
			}

				 $response["error"]   = 0;
				 $response["message"] = "User details not found";
									
	 }
			
mysql_close($conn);
?>