<?php
    require_once("connection.php");

    $object = new stdClass();

    class SendTo20Store
    {
        private $user_id;
        private $orders;
        private $dev_array; //array
        private $get_array; //array
        private $hika_user_id;
        private $conn;
        private $vendor_id;
        private $products;
        public $last_id;
        public $row;

        public function __construct($conn, $orders, $user_id)
        {
            foreach ($orders as $order) {
                $this->vendor_id = $order["vendor_id"];
                $this->products = $order["products"];
            }
            $this->conn = $conn;

            // set hikashop user_id
            $this->getHikashopUserId($user_id);

            //get last order id
          $this->row =$this->getLastOrderTableId();
        }
        /**
         * get hikashop user id
         */
        private function getHikashopUserId($user_id)
        {
            $sql = "SELECT `user_id` FROM pish_hikashop_user WHERE user_cms_id=$user_id LIMIT 1";
            $rows = $this->SelectAction($sql);
            $this->hika_user_id = $rows['user_id'];
        }

        /**
         * select process
         */
        private function SelectAction($sql)
        {
            $result = $this->conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row;
            } else {
                return [];
            }
        }

        /**
         * transaction insert process
         */
        public function insertTransaction()
        {
            
            /* Tell mysqli to throw an exception if an error occurs */
            /* Start transaction */
            $this->conn->autocommit(FALSE);

            $this->conn->begin_transaction();
            
            try {
                /* Insert order table */
                
                    $time = time();
                    $sqlSellOrderType = "INSERT INTO pish_hikashop_order (order_user_id, order_status, order_id, order_created, order_modified, order_vendor_id)
                    VALUES($this->hika_user_id, 'sell', $this->last_id, $time, $time,0)";
                    mysqli_query($this->conn,$sqlSellOrderType);
                // insert order product table
                foreach ($this->products as $product) {

                    $product_id = $product["product_id"];
                    $cart_product_quantity = $product["quantity"];
                    $product_name = $product["product_name"];
                    $product_price = $product["product_price"];
                    $product_code = 'product_' . $product["product_id"];
        
                    //if i==0 save all product
                    $sql = "INSERT INTO pish_hikashop_order_product (order_id, product_id, order_product_quantity, order_product_name, order_product_code,
                order_product_price) VALUES ($this->last_id, $product_id, $cart_product_quantity, '$product_name', '$product_code', $product_price)";
            
                     mysqli_query($this->conn,$sql);
                        
                        
                     
                }
            
                // ($this->insertOrderTable());
                // $this->insertOrderProductTable();
                
                /* If code reaches this point without errors then commit the data in the database */
                /* If code reaches this point without errors then commit the data in the database */
                mysqli_commit($this->conn);
                return true;
            } catch (mysqli_sql_exception $exception) {
                
                mysqli_rollback($this->conn);
                // throw $exception;
                return false;
            }
        }

        /***
         * insert into order table
         */
        public function insertOrderTable()
        {   $this->last_id++;
            $time = time();
            $sqlSellOrderType = "INSERT INTO pish_hikashop_order (order_user_id, order_status, order_id, order_created, order_modified, order_vendor_id)
            VALUES($this->hika_user_id, 'sell', $this->last_id, $time, $time,0)";
            mysqli_query($this->conn,$sqlSellOrderType);
        }

        /***
         * find last order table id
         */
        public function getLastOrderTableId()
        {
            $sql = "SELECT * FROM pish_hikashop_order ORDER BY order_id DESC LIMIT 1"; //have error
            $rows = $this->conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
            
                $this->last_id = $rows['order_id'];
            } else {
                $this->last_id = 0;
            }
            return $rows;
        }

        /***
         * insert into order_product
         */
        public function insertOrderProductTable()
        {
            
            foreach ($this->products as $product) {

                $product_id = $product["product_id"];
                $cart_product_quantity = $product["quantity"];
                $product_name = $product["product_name"];
                $product_price = $product["product_price"];
                $product_code = 'product_' . $product["product_id"];

                //if i==0 save all product
                $sql = "INSERT INTO pish_hikashop_order_product (order_id, product_id, order_product_quantity, order_product_name, order_product_code,
                order_product_price) VALUES ($this->last_id, $product_id, $cart_product_quantity, '$product_name', '$product_code', ".($product_price ? $product_price : 0).")";
                
                ($this->conn->query($sql));
            }

        }
    }

    //using class

    $card = Array
    (
        'user_id' => 963,
        'orders' => Array
            (
                Array
                    (
                        'vendor_id' => Array
                            (
                                Array
                                    (
                                    'id' => 107518
                                    ),

                                Array
                                    (
                                        'id' => 129307
                                    ),

                                Array
                                    (
                                        'id' => 129308
                                    ),

                                Array
                                    (
                                        'id' => 129306
                                    ),

                                Array
                                    (
                                        'id' => 128141
                                    )

                            ),

                        'products' => Array
                            (
                                Array
                                    (
                                        'product_id' => 52235,
                                        'quantity' => 1,
                                        'product_name' => ' جرم گیر 4000 گرمی سورمه ای اکتیو',
                                        'product_price' => 0
                                    ),

                                Array
                                    (
                                        'product_id' => 51966,
                                        'quantity' => 1,
                                        'product_name' =>' جرم گیر 4000 گرمی سورمه ای اکتیو',
                                        'product_price' => 0,
                                    ),

                                Array
                                    (
                                        'product_id' => 50661,
                                        'quantity' => 1,
                                        'product_name' =>' جرم گیر 4000 گرمی سورمه ای اکتیو',
                                        'product_price' => 0
                                    )

                            )

                    )

            )

    );

    //other code
    $json = file_get_contents('php://input');
    $post = json_decode($json, true);
    // $post = $card;
    $user_id = $post["user_id"];
    $orders = $post["orders"];


    $sendStore = new SendTo20Store($conn, $orders, $user_id);
    // $result = $sendStore->getLastOrderTableId();
    $sendStore->insertOrderTable();
    $sendStore->insertOrderProductTable();
    print_r(json_encode(['name'=>$row]));
    // // if($sendStore->insertOrderProductTable()){
    // //     echo json_encode(['response'=>'yes']);
    // // }else{
    //     // }
  