<?php

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

$fields =['user_id'=>0];
$user_id = json_encode($fields);
// $session = JFactory::getSession();
$session = array();
$url = 'http://localhost/ss/m_getcartinfo.php';
// start get card info
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $user_id);
// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
if (curl_errno($ch)) {
  $error_msg = curl_error($ch);
  print_r($error_msg);
}
curl_close($ch);

echo $result;