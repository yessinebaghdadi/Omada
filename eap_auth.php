<?php
$controllerIP = '197.13.10.162';
$controllerPort = '8043';
$controllerID = '35d07bdb43a76fb05c263e52f09c9aae';

// Time duration in ms for which the client will be authorized on the network

$seconds = 3600000;

// Username/password of operator (created in Hotspot Manager => Operators)

$username = 'Relead';
$password = 'releadpassword';

########### The code below this line does not need to be modified ###########

$clientMac = $_SESSION["clientMac"];
$apMac = $_SESSION["apMac"];
$ssidName = $_SESSION["ssidName"];
$t = $_SESSION["t"];
$radioId = $_SESSION["radioId"];
$site = $_SESSION["site"];

$curl = curl_init();

$postData = [
  "name" => $username,
  "password" => $password
];

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://'.$controllerIP.':'.$controllerPort.'/'.$controllerID.'/api/v2/hotspot/login',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_COOKIEFILE => '',
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode($postData),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

if ($response !== false) {
  $json = json_decode($response, true);
  $csrfToken = $json['result']['token'];
}
else {
  die("Error: check with your network administrator");
}

$postData2 = [
  "clientMac" => $clientMac,
  "apMac" => $apMac,
  'ssidName' => $ssidName,
  'radioId' => $radioId,
  'authType' => 4,
  'time' => $seconds
];

$url = 'https://'.$controllerIP.':'.$controllerPort.'/'.$controllerID.'/api/v2/hotspot/extPortal/auth';

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode($postData2),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Csrf-Token: ' . $csrfToken,
  ),
));

$res = curl_exec($curl);

curl_close($curl);

if ($res !== false) {
  $json = json_decode($res, true);
  $code = $json['errorCode'];
  if ($code == "0") {
    // echo "You are now authorized on the WiFi network";
  } else {
    die("Error: check with your network administrator");
  }
}
else {
  die("Error: check with your network administrator");
}

?>