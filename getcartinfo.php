<?php
require_once('./connection.php');
class getCardInfo
{
  private $sql;
  protected $user_id;
  private $conn;
  public function __construct($conn)
  {
    $this->conn = $conn;
    
  }


  public function getData()
  {
    $json = file_get_contents('php://input');
    $post = json_decode($json, true);
    $this->user_id = $post["user_id"];

    $this->setUserIdInSql();
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
      $this->failResponse();
    }
  }

  private function failResponse()
  {
    $dev_array[] = ['status' => 0, 'text' => 'notFount'];
    $jsonEncode = json_encode($dev_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    echo $jsonEncode;
  }

  // set user id to sql
  private function setUserIdInSql(){
    $this->sql ="SELECT Aow.*,pish_hikashop_product.product_name as product_name FROM".
    " (SELECT".
    " pish_hikashop_cart_product.cart_product_id,".
    " pish_hikashop_cart_product.cart_id as card_product_card_id,".
    " pish_hikashop_cart_product.product_id,".
    " pish_hikashop_cart_product.cart_product_quantity,".
    " pish_hikashop_cart_product.cart_product_ref_price,".
    " pish_hikashop_cart.cart_id as cart_cart_id,".
    " pish_hikashop_cart.cart_name".
    " ".
    " FROM pish_hikashop_cart_product".
    " inner join".
    " pish_hikashop_cart".
    " ".
    " ON pish_hikashop_cart_product.cart_id = pish_hikashop_cart.cart_id".
    " ".
    " WHERE  pish_hikashop_cart.user_id =".$this->user_id.") as Aow".
    " left join  pish_hikashop_product".
    " on Aow.product_id = pish_hikashop_product.product_id";
  }
}



// using class
$getCard =new getCardInfo($conn);
$getCard->getData();

