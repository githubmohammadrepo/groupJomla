<?php

include_once('connection.php');

$url = "http://hypertester.ir/serverHypernetShowUnion/getUserCardProducts.php";

  $p_ids = [49521,9744,53364,8798,9804,8548];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['p_ids'=>$p_ids]));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $output = curl_exec($ch);
  if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
  }
  curl_close($ch);
  
print_r(json_decode($output,true));
