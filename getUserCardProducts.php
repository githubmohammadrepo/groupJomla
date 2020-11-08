<?php
include_once('connection.php');
// Create connection
$imagePath = "http://www.fishopping.ir/images/com_hikashop/upload/";

$p_ids = [];

$json = file_get_contents('php://input');
$post = json_decode($json, true);
$p_ids = $post["p_ids"];
echo $p_ids;
  //query

  $ids = implode(",", $p_ids);
  echo "({$ids})";
  $sql1 = "SELECT pish_hikashop_product.*, pish_hikashop_file.file_path as product_image
FROM pish_hikashop_product
LEFT JOIN pish_hikashop_file ON pish_hikashop_product.product_id=pish_hikashop_file.file_ref_id
WHERE pish_hikashop_product.product_id IN ({$ids})
GROUP BY pish_hikashop_product.product_id, pish_hikashop_file.file_path";
  $result1 = $conn->query($sql1);

  $arr_res = array();

  if ($result1->num_rows > 0) {

    // output data of each row
    for ($i = 0; $i < $result1->num_rows; $i++) {
      $row = $result1->fetch_assoc();
      $arr_res[$i] = $row;
      $arr_res[$i]['product_sort_price'] = $row['product_sort_price'] / 10;
      $arr_res[$i]['product_msrp'] = $row['product_msrp'] / 10;
      $arr_res[$i]['product_image'] = $imagePath . $row['product_image'];
    }
  }

  // show result
  $jsonEncode = json_encode($arr_res, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

  echo $jsonEncode;
