<?php
/**
 * Created by PhpStorm.
 * User: androiddev
 * Date: 7/17/17
 * Time: 10:47 PM
 */
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "hyperboo_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


mysqli_set_charset($conn,"utf8");
?>