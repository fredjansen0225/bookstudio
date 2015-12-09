<?php
require_once 'libs/MysqliDb.php';
// require_once 'libs/dbObject.php';

// other init
date_default_timezone_set("UTC");
session_start();

define("DB_MYSQL", "mysql");
// define("DB_SQLITE", "sqlite");

$dbSource = DB_SQLITE;

$db = null;
$dbMysql = null;


//local config
$root_url = "http://localhost/3rdStreetAdrBooking";

//prod config
//$root_url = "http://54.67.78.76";




if(defined('DB_SQLITE'))
{
    $db_exists = file_exists("daypilot.sqlite");

    $db = new PDO('sqlite:daypilot.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    if (!$db_exists) {
        //create the database
        $db->exec("CREATE TABLE doctor (
        client_id   INTEGER       PRIMARY KEY AUTOINCREMENT NOT NULL,
        doctor_name VARCHAR (100) NOT NULL
        );");
        
        $db->exec("CREATE TABLE appointment (
        appointment_id              INTEGER       PRIMARY KEY AUTOINCREMENT NOT NULL,
        appointment_start           DATETIME      NOT NULL,
        appointment_end             DATETIME      NOT NULL,
        appointment_patient_name    VARCHAR (100),
        appointment_status          VARCHAR (100) DEFAULT ('free') NOT NULL,
        appointment_patient_session VARCHAR (100),
        client_id                   INTEGER       NOT NULL
        );");

        $items = array(
            array('name' => 'Doctor 1'),
            array('name' => 'Doctor 2'),        
            array('name' => 'Doctor 3'),        
            array('name' => 'Doctor 4'),        
            array('name' => 'Doctor 5'),        
        );
        $insert = "INSERT INTO [doctor] (doctor_name) VALUES (:name)";
        $stmt = $db->prepare($insert);
        $stmt->bindParam(':name', $name);
        foreach ($items as $m) {
          $name = $m['name'];
          $stmt->execute();
        }

    }
}
else
{

//    $localconf = Array (
//        'host' => 'localhost',
//        'username' => 'root',
//        'password' => 'mysql',
//        'db'=> '3rdStreetAdr',
//        'port' => 3306,
//        'prefix' => '',
//        'charset' => 'utf8');

    $prodconf = Array (
        'host' => 'db3rdstreetadr.cfnvfnqmhj5u.us-west-1.rds.amazonaws.com',
        'username' => 'a3rdstreetadr',
        'password' => 'dbv0nd3lp1rk',
        'db'=> 'a3rdstreetadr',
        'port' => 3306,
        'prefix' => '',
        'charset' => 'utf8');

    //test app for local
    $fbAppId = "391449467692451";
    $fbAppSecret = "8ac4f17de057d5732bf7fba8a52d090a";

    //product app for production

    //$fbAppId = "390114841159247";
    //$fbAppSecret = "2159765601f2aabe0f50d70698eda26f";


    $dbMysql = new MysqliDb ($prodconf);
//    $dbMysql = new MysqliDb ($localconf);
}


?>
