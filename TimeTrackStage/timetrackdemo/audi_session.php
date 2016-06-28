<?php 
include "connection.php";


function saveAuditSessionLogin($user_id, $UserName)
{
    //echo "saveAuditSessionLogin";
    // if (!$conn) {
    //             die("Connection failed: " . mysql_connect_error());
    // }else{
         $sessionquery = "insert into Session_Users (UserName, user_id, LoginTime ) values ('$UserName', $user_id, now())";
          if (mysql_query($sessionquery)) {
                $lastrecord = "SELECT Session_Id FROM Session_Users where user_id = $user_id ORDER BY Session_Id desc LIMIT 1";
                $result = mysql_query($lastrecord) or die("Error in Selecting " . mysql_error());
                if (!empty($result)) {
                    if (mysql_num_rows($result) > 0) {
                         while($row =mysql_fetch_assoc($result))
                            {
                            	$GLOBALS['Session_Id']=$row['Session_Id'];
                            }

                    }else{
                         $response["error"] = 1;
                         $response["message"] = "There is no record count for session audit";
                          echo "No row count";
                    }
                }else{
                         $response["error"] = 1;
                         $response["message"] = "Returns empty session audit";
                         //echo "Returns empty session audit";
                    }

                $response["success"] = 1;
                $response["message"] = "Inserted Successfully";
                //mysql_free_result($result);
                //$session_id=$_SESSION['Session_Id'];
            }else{
                $response["error"] = 1;
                $response["message"] = "Failed to insert in session audit";
                //echo "failed to insert in session audi";
            }

            //echo json_encode($response);
       
        //}
           // echo "Session ID---". $_SESSION['Session_Id'] ;
        echo $Session_Id;
}

function updateAuditSessionLogout($Session_Id)
{
     $sessionquery = "update Session_Users set LogoutTime = now() where Session_Id=$Session_Id";
      if (mysql_query($sessionquery)) {
           
            $response["success"] = 1;
            $response["message"] = "Updated Session Logout Time Successfully";
            //mysql_free_result($result);
            //$session_id=$_SESSION['Session_Id'];
        }else{
            $response["error"] = 1;
            $response["message"] = "Failed to insert in session audit";
            //echo "failed to insert in session audi";
        }
}

function saveSessionUsersAudit($Session_Id, $ActionTaken, $AccessArea)
{
	$sessionquery = "insert into Session_Users_Audit (Session_Id, Time_Stamp, ActionTaken,  AccessArea ) values ($Session_Id, now(), '$ActionTaken', '$AccessArea')";
	if (mysql_query($sessionquery)) {
	    $response["success"] = 1;
	    $response["message"] = "Inserted Session Users Audit Successfully";
	    //mysql_free_result($result);
	    //$session_id=$_SESSION['Session_Id'];
	}else{
	    $response["error"] = 1;
	    $response["message"] = "Failed to insert in Session Users Audit";
	    //echo "failed to insert in session audi";
	}

}

?>