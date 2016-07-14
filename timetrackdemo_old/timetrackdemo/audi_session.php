<?php 
include "connection.php";


function saveAuditSessionLogin($user_id, $UserName)
{
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
                      
                }
            }else{
                     $response["error"] = 1;
                     $response["message"] = "Returns empty session audit";
                    
                }

            $response["success"] = 1;
            $response["message"] = "Inserted Successfully";
          
        }else{
            $response["error"] = 1;
            $response["message"] = "Failed to insert in session audit";
            
        }

 }


function updateAuditSessionLogout()
{
     $sessionquery = "update Session_Users set LogoutTime = now() where Session_Id=$Session_Id";
      if (mysql_query($sessionquery)) {
           
            $response["success"] = 1;
            $response["message"] = "Updated Session Logout Time Successfully";
        }else{
            $response["error"] = 1;
            $response["message"] = "Failed to insert in session audit";
         }
}

function saveSessionUsersAudit($Session_Id, $ActionTaken, $AccessArea, $performed_data)
{
	
	$sessionquery = "insert into Session_Users_Audit (Session_Id, Time_Stamp, ActionTaken,  AccessArea, performed_data ) values ($Session_Id, now(), '$ActionTaken', '$AccessArea','$performed_data')";
	
	if (mysql_query($sessionquery)) {
		$response["success"] = 1;
	    $response["message"] = "Inserted Session Users Audit Successfully";
	   
	}else{
	    $response["error"] = 1;
	    $response["message"] = "Failed to insert in Session Users Audit";
	}

}
//Getting track details and saving into Session_Users_Audit before delete the record
function getTrackDetials($trackid)
{
	$selTimeTrack="select user_id, date, project_id, description, updated_date from time_entry where id = $trackid";
 
    $TimeEntryLogexec = mysql_query($selTimeTrack) or die("Error while selecting track details information" . mysql_error());
    if(!empty($TimeEntryLogexec)){
        if(mysql_num_rows($TimeEntryLogexec)>0){
             while ($row = mysql_fetch_array($TimeEntryLogexec)) {
                $strLogDetails=  "User ID: ".$row["user_id"] .", date: ".$row["date"] .", Project ID: ".$row["project_id"] .", Description: ".$row["description"] .", Updated  Date: ".$row["updated_date"];
                   
                }
            }else{
                $response["message"] = "Problem in fetching data.";
            }
    }else{
        $response["message"] = "No Records found ";
    }

 
    return $strLogDetails;
}

?>