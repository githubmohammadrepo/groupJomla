<?php
include_once('./getUserLocation.php');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

// set default user_id to 0;
$user_id = 963;
//get nearest stores
/**
 * start get nearest shops
 */
if (isset($_POST) && isset($_POST["lat"]) && isset($_POST["lng"])) {

  $post = [
    'lat' => 0,
    'lng' => 0,
  ];
  // $url = "http://hypernetshow.com/serverHypernetShowUnion/SelectNearestShop.php";

  // start get lat and lng location current user.
  // using class
  $getCard =new getUserLocation($conn);
  $userLocationResult = json_decode($getCard->getData($user_id),true);
  foreach ($userLocationResult as $key => $value) { 
    if($key ==0){

    }else{
      
      $post['lat']= $value['latitude'];
      $post['lng']= $value['longitude'];
    }
  }

  // end get lat and lng location current user. **completed** 


  // start select nearest shop
  $url = "http://hypertester.ir/serverHypernetShowUnion/SelectNearestShop.php";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $output = curl_exec($ch);
  if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
  }
  curl_close($ch);
  $contents = json_decode($output, true);

  $foundStore = -1;

  // end select nearest shop

  if ($contents && count($contents) > 0) {

    if ($contents[0]['id'] == "notok") {

      $foundStore = -1;
    } else {

      $ids = [];

      for ($j = 0; $j < count($contents); $j++) {

        $ids[] = ['id' => $contents[$j]['id']];
      }

      $products = [];

      for ($k = 0; $k < count($basket); $k++) {

        $product_id = $basket[$k]['product_id'];

        $quantity = $basket[$k]['count'];

        $product_name = $basket[$k]['product_name'];

        $product_price = $basket[$k]['product_price'];

        $products[] = [

          'product_id' => $product_id,

          'quantity' => $quantity,

          'product_name' => $product_name,

          'product_price' => $product_price,

        ];
      }

      $foundStore = 1;

      $card = [

        'user_id' => $current_user->id,

        'orders' => [[

          'vendor_id' => $ids,

          'products' => $products,

        ]],

      ];

      //////////  save card  ///////////////

      $url = "http://hypernetshow.com/serverHypernetShowUnion/Sendto20Store.php";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($card));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $output = curl_exec($ch);
      curl_close($ch);
      $contents = json_decode($output, true);

      //clear session

      $session->set('store_basket', null);

      $basket = null;

      $cardSaved = true;

      ///////////////////////////////////////

    }
  } else {

    $foundStore = -1;
  }
}
/**
 * end get nearest shops
 */

?>

<form method="post" style="text-align: left;">

<input type="hidden" id="lat" name="lat" value="">

<input type="hidden" id="lng" name="lng" value="">

<button type="submit" name"send_card">ارسال سبد خرید</button>

</form>
