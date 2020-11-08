<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<?php


error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

$session = JFactory::getSession();

$basket = $session->get("store_basket");

$current_user = JFactory::getUser();

$foundStore = 0;

$cardSaved = false;

//update basket session

if (isset($_POST) && isset($_POST["pid"])) {

  $newBasket = [];

  if ($basket) {

    $found = false;

    for ($i = 0; $i < count($basket); $i++) {

      if ($basket[$i]["product_id"] != $_POST["pid"]) {

        $newBasket[] = $basket[$i];
      }
    }
  }

  $session->set('store_basket', $newBasket);
}

//get nearest stores

if (isset($_POST) && isset($_POST["lat"]) && isset($_POST["lng"])) {

  $url = "http://hypernetshow.com/serverHypernetShowUnion/SelectNearestShop.php";

  $post = [
    'lat' => $_POST["lat"],
    'lng' => $_POST["lng"],
  ];

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

$servername = "localhost";
$username = "hypernet_user";
$password = "sghl@1362";
$db_name = "hypernet_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $db_name);

$imagePath = "http://www.fishopping.ir/images/com_hikashop/upload/";

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
mysqli_set_charset($conn, "utf8");

$p_ids = [];

$session = JFactory::getSession();
$basket = $session->get('store_basket');

if ($basket) {
  for ($i = 0; $i < count($basket); $i++) {

    $p_ids[] = $basket[$i]['product_id'];
  }

  //query

  $ids = implode(",", $p_ids);
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

  ////

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
            <p style="display: inline;font-weight: bold"><?= $basket[$i]['count'] ?></p>
          </div>

          <div style="margin-bottom: 5px;">
            <form method="post">

              <input type="hidden" name="pid" value="<?= $arr_res[$i]["product_id"] ?>">

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

<script>
  function setPosition(position) {

    console.log(position);

    document.getElementById("lat").value = position.latitude;

    document.getElementById("lng").value = position.longitude;
  }

  function do_something(coords) {
    console.log(coords);
    // Do something with the coords here
  }

  navigator.geolocation.getCurrentPosition(
    function(position) {
      setPosition(position.coords);
    },
    function(failure) {
      $.getJSON('https://ipinfo.io/geo', function(response) {
        var loc = response.loc.split(',');
        var coords = {
          latitude: loc[0],
          longitude: loc[1]
        };
        console.log(coords)
        setPosition(coords);
      });
    }
  );
</script>