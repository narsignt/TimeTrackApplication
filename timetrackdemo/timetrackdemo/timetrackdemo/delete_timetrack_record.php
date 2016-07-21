<?php
include "connection.php";
include "audi_session.php";
include_once "timetrackCommonFunc.php";
$track_id=$_REQUEST['track_id'];
$user_id=$_POST['user_id'];
$isadmin=$_POST['isadmin'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$sessionid = $_REQUEST['sessionid'];
$userrole  = $_REQUEST['userrole'];

if (!$conn) {
    die("Connection failed: " . mysql_connect_error());
}else{
    $del_query = "delete from time_entry where id = $track_id;";
    // Added for audit log by sunil
    $trackDetails= getTrackDetials($track_id);

    if (mysql_query($del_query)) {
        // Added for audit log by sunil
        if ($trackDetails !='') {
            saveSessionUsersAudit($sessionid,"Deleted Track details","Day Tab", $trackDetails);
        }
        $userqueryexec = getSelectedUserDetails($user_id);
        if(!empty($userqueryexec))
            {
                if(mysql_num_rows($userqueryexec)>0)
                {
     
                    $response["userdetails"]= getUserDetailsArray($userqueryexec, $userrole);

                    if($isadmin === "false"){                      
                    
                        $response["weekdetailsarray"]=getTimeTrackWeekDetails($user_id,$start_date,$end_date);
                        $response["timetrackdetails"] = getTimeTrackDetails($isadmin, $user_id);
                    }else{
                                        
                        $weekdetailsarray[] = $row;
                        $weekdetailsarray["success"] = 1;
                        $weekdetailsarray["message"] = "Admin user";
                        $response["timetrackdetails"] = getTimeTrackDetails($isadmin, $user_id);
                    }
					
                    
                }else{
                      $response["error"] = 0;
                      $response["message"] = "User details not found";
                      //echo json_encode($response);
                }
            }else{
				$response["error"] = 1;
				$response["message"] = "User record not found in db. Please, Redirect to home page for login.";
			}
    }else{
        $response["error"] = 1;
        $response["message"] = "failed to delete in timetrack record";
    }
    echo json_encode($response);
}
mysql_close($conn);
?>
