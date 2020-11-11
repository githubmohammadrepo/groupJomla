<?php

$url = "http://hypertester.ir/serverHypernetShowUnion/Sendto20StoreAndNotify.php";
$card  = [
  'user_id'=>963
];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($card));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



    $output = curl_exec($ch);

    curl_close($ch);

    $contents = json_decode($output);
    var_dump($contents);