{source}

 

 

 

<?php

 $session = JFactory::getSession();

$current_user = JFactory::getUser();

$user_id = $current_user->id;

 

error_reporting(E_ALL);

ini_set('error_reporting', E_ALL);

ini_set('display_errors', 1);

 

// start get card info

$fields = ['user_id' =>$user_id];

$user_id = json_encode($fields);

$basket = array();

$cardSaved = false;

$foundStore = 0;

// $session = JFactory::getSession();

$session = array();

// $url = 'http://localhost/ss/getcartinfo.php';

$url = 'http://hypertester.ir/serverHypernetShowUnion/m_getcartinfo.php';

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

 

// echo $result;

$result = (json_decode($result));

foreach ($result as $key => $value) {

  if ($key == 0) {

    //show status

  } else {

    $basket = $result;

  }

}

// end get card info

 

$user_id = json_encode(['user_id' =>$user_id]);

 

// set default user_id to 0;

// $user_id = 963;

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

  $userLocationUrl = 'http://hypertester.ir/serverHypernetShowUnion/getUserLocation.php';

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $userLocationUrl);

  curl_setopt($ch, CURLOPT_POSTFIELDS, $user_id);

  // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

 

  $result = curl_exec($ch);

  if (curl_errno($ch)) {

    $error_msg = curl_error($ch);

    print_r($error_msg);

  }

  curl_close($ch);

 

  // echo $result;

  $userLocationResult = (json_decode($result,true));

  foreach ($userLocationResult as $key => $value) {

    if ($key == 0) {

    } else {

 

      $post['lat'] = $value['latitude'];

      $post['lng'] = $value['longitude'];

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

        if ($k == 0) {

        } else {

          

          $product_id = $basket[$k]->product_id;

 

          $quantity = $basket[$k]->cart_product_quantity;

 

          $product_name = $basket[$k]->product_name;

 

          $product_price = $basket[$k]->cart_product_ref_price;

 

          $products[] = [

 

            'product_id' => $product_id,

 

            'quantity' => $quantity,

 

            'product_name' => $product_name,

 

            'product_price' => $product_price,

 

          ];

        }

      }

 

      $foundStore = 1;

 

      $card = [

 

        'user_id' => $user_id,

 

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

 

      // $session->set('store_basket', null);

      /**

       * clear basket or cart

       */

 

      //  start clear basket

 

      // end claer basket

 

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

 

 

 

 

 

<!-- last step -->

<!-- additional files for show infos -->

<?php

 

 

// Create connection

$imagePath = "http://www.fishopping.ir/images/com_hikashop/upload/";

 

$p_ids = [];

 

// $session = JFactory::getSession();

// $basket = $session->get('store_basket');

 

if ($basket) {

  for ($i = 0; $i < count($basket); $i++) {

    if ($i != 0) {

      $p_ids[] = $basket[$i]->product_id;

    }

  }

 

  //query

 

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

  

 

  $arr_res =(json_decode($output,true));

  // $arr_res[$i]['product_image'] = $imagePath . $row['product_image'];

 

 

}

 

if ($cardSaved) {

 

?>

 

  <div style="text-align: center; background-color: #eee; padding: 10px; margin-bottom: 10px; color: green; font-size: 16px; font-weight: bold;">

 

    <p>سبد خرید با موفقیت برای فروشگاه های نزدیک شما ارسال شد. </p>

 

  </div>

 

<?php

 

}

 

if ($basket) {

?>

 

  <form method="post" style="text-align: left;">

 

    <input type="hidden" id="lat" name="lat" value="">

 

    <input type="hidden" id="lng" name="lng" value="">

 

    <button type="submit" name"send_card">ارسال سبد خرید</button>

 

  </form>

 

 

 

  <?php

 

  if ($foundStore == -1) {

 

  ?>

 

    <div style="text-align: center; background-color: #eee; padding: 10px; margin-bottom: 10px; color: red; font-size: 16px; font-weight: bold;">

 

      <p>فروشگاهی در نزدیکی شما پیدا نشد.</p>

 

    </div>

 

  <?php

 

  }

 

  ?>

 

 

 

  <?php

  for ($i = 0; $i < count($arr_res); $i++) {

  ?>

 

    <div class="prodBox" style="flex: 30%;text-decoration: none;margin: 5px;padding: 0px;border: none;background-color: white;padding: 5px;border: 1px solid #eeeeee;min-height: 100px;">

 

      <div style="display: flex; flex-direction: row;justify-content: flex-start;">

        <img src="<?= $arr_res[$i]["product_image"] ?>" style="max-width:100px;max-height:140px;margin-right: 10px;margin-left: 10px;" />

        <div style="display: flex; flex-direction: column;justify-content: center;">

          <div style="margin-bottom: 5px;">

            <p style="display: inline;">نام محصول:</p>

            <p style="display: inline;font-weight: bold"><?= $arr_res[$i]["product_name"] ?></p>

          </div>

          <div style="margin-bottom: 5px;display: none;">

            <p style="display: inline;">نام برند:</p>

            <p style="display: inline;font-weight: bold"><?= $arr_res[$i]["category_name"] ?></p>

          </div>

          <div style="margin-bottom: 5px;">

            <p style="display: inline;">قیمت واحد:</p>

            <p style="display: inline;font-weight: bold"><?= $arr_res[$i]["product_sort_price"] ?></p>

          </div>

 

          <div style="margin-bottom: 5px;">

            <p style="display: inline;">تعداد:</p>

            <p style="display: inline;font-weight: bold"><?= $basket[$i]->product_counting_unit ?? "" ?></p>

          </div>

 

          <div style="margin-bottom: 5px;">

            <form method="post">

 

              <input type="hidden" name="pid" value="<?= $arr_res[$i]['product_id'] ?>">

 

              <button style="background-color: red;color: white;" type="submit">حذف</button>

 

            </form>

          </div>

        </div>

      </div>

 

    </div>

 

  <?php

  }

} else {

  ?>

 

  <div>

    <p style="width: 100%; background-color: #eee; padding: 10px; text-align: center;">

      سبد خرید خالی می باشد.

    </p>

  </div>

 

<?php

}

?> 

{/source}