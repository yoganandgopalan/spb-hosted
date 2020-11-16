<?php
include('config.php');
$ch_1 = curl_init();
curl_setopt($ch_1, CURLOPT_URL, $apiEndPoint."/v1/oauth2/token");
curl_setopt($ch_1, CURLOPT_HEADER, false);
curl_setopt($ch_1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch_1, CURLOPT_POST, true);
curl_setopt($ch_1, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch_1, CURLOPT_USERPWD, $clientId.":".$secret);
curl_setopt($ch_1, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

$result_1 = curl_exec($ch_1);

if(empty($result_1))die("Error: No response.");
else
{
    $json_1 = json_decode($result_1);
	$access_token = $json_1->access_token;
    //print_r($json_1->access_token);
}

curl_close($ch_1);


$data_1 = '{
}';

$ch_2 = curl_init();
curl_setopt($ch_2, CURLOPT_URL, $apiEndPoint."/v2/checkout/orders/".$_GET['order_id']."/capture");
curl_setopt($ch_2, CURLOPT_POST, true);
curl_setopt($ch_2, CURLOPT_POSTFIELDS, $data_2); 
curl_setopt($ch_2, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: Bearer ".$access_token)
);

curl_setopt($ch_2, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch_2, CURLOPT_RETURNTRANSFER, TRUE);

$result_2 = curl_exec($ch_2); 

if(empty($result_2))die("Error: No response.");
else
{

	$json_2 = json_decode($result_2);
	print_r($json_2);
}
curl_close($ch_2);
?>