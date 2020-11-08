<?php
require_once('./connection.php');
class getUserLocation
{
  private $sql;
  protected $user_id;
  private $conn;
  public function __construct($conn)
  {
    $this->conn = $conn;
    
  }


  public function getData($user_id)
  {
    $this->user_id = $user_id;

    $this->setUserIdInSql();
    if (($this->user_id)) {

      $result = $this->conn->query($this->sql);

      if ($result->num_rows > 0) {
        // output data of each row

        //set status
        $dev_array[] = ['status' => 1, 'text' => 'found'];
        while ($row = $result->fetch_assoc()) {
          $dev_array[] = $row;
        }
        $jsonEncode = json_encode($dev_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return $jsonEncode;

      } else {
        return $this->failResponse();
      }
      return $this->conn->close();
    } else {
      return $this->failResponse();
    }
  }

  private function failResponse()
  {
    $dev_array[] = ['status' => 0, 'text' => 'notFount'];
    $jsonEncode = json_encode($dev_array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    return $jsonEncode;
  }

  // set user id to sql
    private function setUserIdInSql(){
      $this->sql ="SELECT `user_id`,`latitude`,`longitude` FROM `pish_phocamaps_marker_customer` ".
      " WHERE user_id =".$this->user_id.  ";";
    }
}



// using class

