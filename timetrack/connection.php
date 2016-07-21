<?php
global $conn;
   $conn=mysql_connect ("dtimetracker.db.12054818.hostedresource.com", "dtimetracker", "Q!w2e3r4") or die ('System cannot connect to the database because: ' . mysql_error());
   mysql_select_db ("dtimetracker");
   if($conn<0)
   echo "connection could not be established"; 

?>