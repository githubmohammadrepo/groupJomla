<?php
require_once('./connection.php');
class getCardInfo
{
  private $sql;
  protected $user_id;
  private $conn;
  public $getDataType = 'none';
  public $ids;
  private $imagePath = "http://www.fishopping.ir/images/com_hikashop/upload/";
  public function __construct($conn,$user_id,$p_ids)
  {
    $this->conn = $conn;
    $this->user_id = $user_id;
    $this->ids = implode(",",$p_ids);
  }


  public function getData()
  {
    //   return;
    if($this->getDataType=='getProduct'){
      
      $this->setSqlGetProductInfo();
      
      $arr_res = array();
      $result1 = $this->conn->query($this->sql);

      if ($result1->num_rows > 0) {
    
        // output data of each row
        for ($i = 0; $i < $result1->num_rows; $i++) {
          $row = $result1->fetch_assoc();
          $arr_res[$i] = $row;
          $arr_res[$i]['product_sort_price'] = $row['product_sort_price'] / 10;
          $arr_res[$i]['product_msrp'] = $row['product_msrp'] / 10;
          $arr_res[$i]['product_image'] = $this->imagePath . $row['product_image'];
        }
        
        // show result
        $jsonEncode = json_encode($arr_res, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
         print_r($jsonEncode);
         
      }else{
        // echo json_encode(['user_id'=>'hi user id ']);
      $this->failResponse();
    }
    
          
    }elseif($this->getDataType=='getBasket'){
      $this->setSqlGetBasketInfo();
      if (isset($this->user_id)) {
    
          $result = $this->conn->query($this->sql);
    
          if ($result->num_rows > 0) {
            // output data of each row
            //set status
            $dev_array[] = ['status' => 1, 'text' => 'found'];
            while ($row = $result->fetch_assoc()) {
              $dev_array[] = $row;
            }
            $jsonEncode = json_encode($dev_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
            echo $jsonEncode;
          } else {
            $this->failResponse();
          }
          $this->conn->close();
      } else {
        // $this->failResponse();
      }
    }else{
        // echo json_encode(['user_id'=>'hi user id ']);
      $this->failResponse();
    }
    
   
    
  }

  private function failResponse()
  {
    $dev_array[] = ['status' => 0, 'text' => 'notFount'];
    $jsonEncode = json_encode($dev_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    echo $jsonEncode;
  }

  // set user id to sql for get card info
  private function setSqlGetProductInfo(){
    $this->sql = "SELECT pish_hikashop_product.*, pish_hikashop_file.file_path as product_image".
    " FROM pish_hikashop_product".
    " LEFT JOIN pish_hikashop_file ON pish_hikashop_product.product_id=pish_hikashop_file.file_ref_id".
    " WHERE pish_hikashop_product.product_id IN ({$this->ids})".
    " GROUP BY pish_hikashop_product.product_id, pish_hikashop_file.file_path";
  }

  // set user id to sql for get all information product in basket
  private function setSqlGetBasketInfo()
  {
    $this->sql = "SELECT" .
      " newCart.*, pish_hikashop_cart_product.cart_product_id, pish_hikashop_cart_product.cart_id AS card_product_card_id, pish_hikashop_cart_product.product_id, pish_hikashop_cart_product.cart_product_quantity, pish_hikashop_cart_product.cart_product_ref_price" .
      " FROM " .
      " (" .
      " SELECT  cms_users.cms_user_id" .
      ", pish_hikashop_cart.cart_id" .
      ", pish_hikashop_cart.user_id AS cart_user_id" .
      ", pish_hikashop_cart.cart_name" .
      " FROM " .
      " (" .
      " SELECT  user_id AS cms_user_id" .
      " FROM hyperboo_db.pish_hikashop_user" .
      " WHERE user_cms_id =" . $this->user_id . " " .
      " ) AS cms_users" .
      " INNER JOIN pish_hikashop_cart" .
      " ON cms_users.cms_user_id = pish_hikashop_cart.user_id" .
      " ) as newCart" .
      " INNER JOIN pish_hikashop_cart_product" .
      " ON newCart.cart_id = pish_hikashop_cart_product.cart_id";
  }
}


// using class 
$json = file_get_contents('php://input');
$post = json_decode($json, true);
$user_id = $post["user_id"];
$p_ids = $post["p_ids"];

$getCard =new getCardInfo($conn,$user_id,$p_ids);
if($user_id){
  //فقط اطلاعات سبد خرید رو بگیر
 
  $getCard->getDataType='getBasket';
 
}
if($p_ids){
  
  // اطلاعات سبد خرید و جزئیات محصول روبگیر
  $getCard->getDataType='getProduct';
  
}
// using class




$getCard->getData();

