<?php
include_once "connection.php";

//$to =date('Y-m-d');
//$from=date('Y-m-d',strtotime($currentDate . “-3 days"));

$email_sql = "select user_id, email_id, role, count(*), curdate() from (select distinct u.user_id, u.email_id, u.role, te.date from italentnewdb.time_entry te left join italentnewdb.users u on u.user_id = te.user_id where te.date between subdate(curdate(), 3)and curdate() group by u.user_id, te.date) as tmcount group by user_id having count(*) < 3";

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
			$current_date = $row["curdate()"];
			//print_r($list_of_email);
			//print_r (explode(',',$list_of_email));
			echo $list_of_email;
			echo $current_date;
			//array_push($response["email_notify"], $email_notify);
			if(intval($count) < 3){
				$to = $list_of_email; 
				$subject = "Regarding Time track status";
				$body="<P>Hi, Please Update Your Daily Time track Status </p>";
				$body .="<br/>";
				$body .= '<a href="https://www.google.co.in/" target="_blank">Enter Your Status Here</a>';
				$body .="<br/>";
				$body .="Thanks,";
				$body .="<br/>";
				$body .="iTalent.";
				//$from = "hvuppala@italentcorp.com";
				//$headers = "From: hvuppala@italentcorp.com"; 
				$headers = "From: $from\n";
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
				$headers .= "Cc: hvuppala@italentcorp.com";
				// More headers
				
				$sent = mail($to, $subject, $body, $headers) ; 
				//echo "message sent";
				if($sent) 
					{print "Your mail was sent successfully"; }
				else 
					{print "We encountered an error sending your mail"; }
				}else{}
			}

				 $response["error"]   = 0;
				 $response["message"] = "User details not found";
									
			  }
			
mysql_close($conn);
?>