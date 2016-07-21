<?php
include_once "connection.php";
include "audi_session.php";
require "timetrackCommonFunc.php";
//paramenters
//get username and password
//Input values from JS file front end
$user_id   = $_GET['user_id'];
$email_id = $_GET['uname'];
$u_name = $_GET['UserName'];
$empno = $_GET['EmpNumber'];
$country = $_GET['Country'];
$givenName=$_GET['givenName'];
$familyName=$_GET['familyName'];

//decoding input values from JS
$user_id   = base64_decode($user_id);
$email_id = base64_decode($email_id);
$u_name = base64_decode($u_name);
$empno = base64_decode($empno);
$country = base64_decode($country);
$givenName=base64_decode($givenName);
$familyName=base64_decode($familyName);
$isadmin = "false";
//Jive authentication checking for API call respons
$url =  $jiveHostName. $user_id;
$ch  = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


$obj;
curl_setopt($ch, CURLOPT_USERPWD, $post_data);
$data = curl_exec($ch);
curl_close($ch);
if ($data != strip_tags($data)) {
   $flag = true;
}else{
	$search   = "throw 'allowIllegalResourceCall is false.';";
	$jiveApiResponse = str_replace($search, '', $data);
	$obj = json_decode($jiveApiResponse, true);
}
//Jive authentication checking for API call respons


if (($obj[error][status] != 404) && ($flag != true)) {
    //check connection
    //Session audit instering when user login
    saveAuditSessionLogin($user_id,$u_name);
    //Session audit log
    saveSessionUsersAudit($Session_Id, 'Logged in', 'Home Page','');
    if ($conn) {
        //echo "connecion---".$conn ;
        // updating user deatils ($empno, $country)
        updateUserDetails($empno, $country, $user_id);

        // Geting user details 
        $userqueryexec = getSelectedUserDetails($user_id);
        if (!empty($userqueryexec)) {     
            if (mysql_num_rows($userqueryexec) > 0) {
                $response["success"]     = 1;
                $response["sessionid"] = $Session_Id;
                $response["userdetails"] = array();
                while ($row = mysql_fetch_array($userqueryexec)) {
                    $userrole                 = $row["role"];
                    $userid                   = $row["user_id"];
                    //echo "row details".$row["user_id"]."; ".$row["email_id"]."; ".$row["role"].";<br/>";
                    //echo "role ".strcmp($row["role"], "Admin")."<br/>";
                    if(strcmp($row["role"], "Admin") != 0){
                        //This id got normal user
                        $userdetails              = array();
                        $userdetails["user_id"] = $row["user_id"];
                        $userdetails["email_id"] = $row["email_id"];
                        $userdetails["u_name"] = $row["u_name"];
                        $userdetails["first_name"]=$row["first_name"];
                        $userdetails["last_name"]=$row["last_name"];
						$userdetails["country"] = $row["country"];
						$userdetails["empno"] = $row["empno"];
                        $userdetails["role"] = $row["role"];
                        $userrole = $row["role"];
                        $userid   = $row["user_id"];
                        $isadmin = "false"; 

                        array_push($response["userdetails"], $userdetails);
                        //echo "user role1 : ".$userrole. "<br/>";
                    }else{
                        $isadmin = "true"; 

                        $adminquery = "(select user_id,email_id,u_name,first_name,last_name,country,empno,role from users where user_id = '$user_id' ) union (select user_id,email_id,u_name,first_name,last_name,country,empno,role from users where user_id != '$user_id')";
                        $adminqueryexec = mysql_query($adminquery) or die("Error while selecting admin u information" . mysql_error());
                        while ($row = mysql_fetch_array($adminqueryexec)) {
                            $userdetails              = array();
                            $userdetails["user_id"]   = $row["user_id"];
                            $userdetails["email_id"] = $row["email_id"];
                            $userdetails["u_name"] = $row["u_name"];
                            $userdetails["first_name"]=$row["first_name"];
                            $userdetails["last_name"]=$row["last_name"];
							$userdetails["country"] = $row["country"];
							$userdetails["empno"] = $row["empno"];
                            $userdetails["role"]  = $row["role"];
                            array_push($response["userdetails"], $userdetails);
                            //echo "user role XXXX: ".$row["role"];
                        }
                    }

                }
                //echo "User Role: ".$userrole."<br/>";
                $response["projectdetails"]= getProjectList();
                $response["timetrackdetails"] = getTimeTrackDetails($isadmin,$userid);
            } else {
                $insert_sql = "insert into users (user_id, email_id, u_name,first_name,last_name,country,empno,role) values ($user_id,'$email_id','$u_name','$givenName','$familyName','$country',$empno,'user')";
                if (mysql_query($insert_sql)) {

    				//$userdetfound =false;
                   //Filling user list into array after insert the user for refresh
                    $response["userdetails"] = fillUserDetails($user_id, $userdetfound, $userrole);

                    $response["timetrackdetails"] = getTimeTrackDetails($isadmin, $user_id);
                    if ($userdetfound=true){
                        
                    }else{
                        $response["error"]   = 0;
                        $response["message"] = "User details not found";
                    }
    				
				    $response["projectdetails"]= getProjectList();
				
                } else {
                    // no userdetails found
                    $response["success"] = 0;
                    $response["message"] = "Failed to insert";
                }
            }
            echo json_encode($response);
        }
    } else {
        die("Connection failed: " . mysql_connect_error());
    }
    mysql_close($conn);
}else if(($obj[error][status] == 404) ){
    echo json_encode($jiveApiResponse);
}else{
    $jsonErrorMsg = array('error' => 'Bad request');
    echo json_encode($jsonErrorMsg);
}




?>
