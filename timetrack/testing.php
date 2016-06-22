<?php
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, 'https://italent.jiveon.com/api/core/v3/people/2277'); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
$user="npanta@itaentcorp.com";
$pass="Q!w2e3r4";
$post_data = $user.":".$pass;
curl_setopt($ch, CURLOPT_USERPWD, $post_data);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
$data = curl_exec($ch); 
curl_close($ch); 
//echo $data;
$search = "throw 'allowIllegalResourceCall is false.';";
$response = str_replace($search, '', $data);
$response1 = json_encode($response);
$obj = json_decode($response, true);
$code ="message";
echo $obj;

echo "chinni..";
echo $obj -> code;
echo "chinni.. 1";
 echo $response ->$code;
 echo "chinni..2";
 echo $obj["code"];
 echo "chinni..3";
 echo $obj[code];

 ?>