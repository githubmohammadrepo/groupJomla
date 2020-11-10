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
  private function setUserIdInSql()
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
$getCard = new getCardInfo($conn);
$getCard->getData();
