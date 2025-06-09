<?php


$db_pass = "";
$db_host = "localhost";
$db_username = "root";


try{



    $connect = new PDO("mysql:host=$db_host;", $db_username, $db_pass);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully";
}catch(PDOException $e){
    echo "Connection failed: ". $e->getMessage();
}




?>