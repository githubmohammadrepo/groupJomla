<!-- {source} -->
<?php




//  $session = JFactory::getSession();

// $current_user = JFactory::getUser();

//  $user_id = $current_user->id;
$user_id = 963;

error_reporting(E_ALL);

ini_set('error_reporting', E_ALL);

ini_set('display_errors', 1);



// start get card info

$fields = ['user_id' => $user_id];

$user_id = json_encode($fields);

$basket = array();

$cardSaved = false;

$foundStore = 0;

// $session = JFactory::getSession();

$session = array();

// $url = 'http://localhost/ss/getcartinfo.php';


function getCardInformations($user_id, &$basket)
{
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

  // var_dump($result);

  foreach ($result as $key => $value) {

    if ($key == 0) {

      //show status

    } else {

      $basket[$key - 1] = $value;
    }
  }
}

getCardInformations($user_id, $basket);
// end get card info



//call after $basket returned or ready
function getShowBasketProduct($basket,&$p_ids)
{
  for ($i = 0; $i < count($basket); $i++) {

    if (true) {

      //if ($i != 0) {

      $p_ids[] = $basket[$i]->product_id;
    }
  }

  $url = "http://hypertester.ir/serverHypernetShowUnion/m_getcartinfo.php";

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);

  curl_setopt($ch, CURLOPT_POST, 1);

  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['p_ids' => $p_ids]));

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



  $output = curl_exec($ch);

  if (curl_errno($ch)) {

    $error_msg = curl_error($ch);
  }

  curl_close($ch);





  $array_res = (json_decode($output, true));

  //set or combine two array  basket and product_basket
  foreach ($basket as $key => $value) {

    foreach ($array_res as $k => $v) {

      if ($value->product_id == $v['product_id']) {

        $array_res[$k]['quantity'] = $value->cart_product_quantity;
      }
    }
  }

  return $array_res;
}
// call show products
$arr_res = getShowBasketProduct($basket,$p_ids);
// var_dump($arr_res[0]['product_name']);


// set default user_id to 0;

// $user_id = 963;

//get nearest stores

/**

 * start get nearest shops

 */
function getCurrentUserLocation($user_id, &$post)
{

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


  $userLocationResult = (json_decode($result, true));
  foreach ($userLocationResult as $key => $value) {

    if ($key == 0) {
    } else {



      $post['lat'] = $value['latitude'];

      $post['lng'] = $value['longitude'];
    }
  }
}

//define function select neares shop and create cart for user
function selectNearestShop($post, &$foundStore, $basket, $arr_res, $user_id)
{
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
      return;
    } else {

      $ids = [];

      for ($j = 0; $j < count($contents); $j++) {



        $ids[] = ['id' => $contents[$j]['id']];
      }

      $products = [];

      for ($k = 0; $k < count($basket); $k++) {

        if (true) {

          //        } else {

          $product_id = $basket[$k]->product_id;



          $quantity = $basket[$k]->cart_product_quantity;


          // var_dump($basket[$k]);
          $product_name = $arr_res[0]['product_name'];



          $product_price = $basket[$k]->cart_product_ref_price;

          $products[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,

            'product_name' => $product_name,

            'product_price' => $product_price,

          ];
        }
      }
      // var_dump($products);

      $foundStore = 1;

      $card = [

        'user_id' => json_decode($user_id)->user_id,

        'orders' => [[

          'vendor_id' => $ids,

          'products' => $products,

        ]],

      ];
      return $card;
    }
  } else {

    $foundStore = -1;
    return;
  }
}


if (isset($_POST) && isset($_POST["lat"]) && isset($_POST["lng"])) {



  $post = [

    'lat' => 0,

    'lng' => 0,

  ];

  // $url = "http://hypernetshow.com/serverHypernetShowUnion/SelectNearestShop.php";



  // start get lat and lng location current user.

  // using class
  getCurrentUserLocation($user_id, $post);







  // end get lat and lng location current user. **completed** 




  // start select nearest shop
  $card = selectNearestShop($post, $foundStore, $basket, $arr_res, $user_id);

  //////////  save card  ///////////////
  function sentUserCartTo20Store(&$basket, $card, &$cardSaved)
  {
    $url = "http://hypertester.ir/serverHypernetShowUnion/Sendto20StoreAndNotify.php";


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($card));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


    $output = curl_exec($ch);

    curl_close($ch);

    // var_dump($output);
    $contents = json_decode($output);
    if ($contents->response == 'notok') {
      //card does not saved

    } else {
      //card saved
      //clear session



      // $session->set('store_basket', null);

      /**

       * clear basket or cart

       */



      //  start clear basket



      // end claer basket



      $basket = null;



      $cardSaved = true;
    }
  }

  if ($card && count($card)) {

    sentUserCartTo20Store($basket, $card, $cardSaved);


    ///////////////////////////////////////



  }else{
    echo 'hi';
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





  // var_dump($p_ids);

  //query



  // var_dump($arr_res);





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

  //var_dump($arr_res);



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

            <p style="display: inline;font-weight: bold"><?= $arr_res[$i]['product_sort_price'] ?></p>

          </div>



          <div style="margin-bottom: 5px;">

            <p style="display: inline;">تعداد:</p>

            <p style="display: inline;font-weight: bold"><?= $arr_res[$i]['quantity'] ?></p>

          </div>



          <div style="margin-bottom: 5px;">

            <form method="post">



              <input type="hidden" name="pid" value="<?= $arr_res[$i]['product_id']  ?>">



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

<!-- {/source} -->