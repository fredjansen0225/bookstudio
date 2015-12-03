<?php
// require_once 'libs/MysqliDb.php';
// require_once 'libs/dbObject.php';

$db_exists = file_exists("daypilot.sqlite");

$db = new PDO('sqlite:daypilot.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// other init
date_default_timezone_set("UTC");
session_start();

if (!$db_exists) {
    //create the database
    $db->exec("CREATE TABLE doctor (
    doctor_id   INTEGER       PRIMARY KEY AUTOINCREMENT NOT NULL,
    doctor_name VARCHAR (100) NOT NULL
    );");
    
    $db->exec("CREATE TABLE appointment (
    appointment_id              INTEGER       PRIMARY KEY AUTOINCREMENT NOT NULL,
    appointment_start           DATETIME      NOT NULL,
    appointment_end             DATETIME      NOT NULL,
    appointment_patient_name    VARCHAR (100),
    appointment_status          VARCHAR (100) DEFAULT ('free') NOT NULL,
    appointment_patient_session VARCHAR (100),
    doctor_id                   INTEGER       NOT NULL
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


// $dbMysql = new MysqliDb (Array (
//                 'host' => 'localhost',
//                 'username' => 'root', 
//                 'password' => '',
//                 'db'=> '3rdStreetAdr',
//                 'port' => 3306,
//                 'prefix' => 'my_',
//                 'charset' => 'utf8'));

// $doctor_tb_creation = "CREATE TABLE `3rdStreetAdr`.`doctor` ( `doctor_id` INT NOT NULL AUTO_INCREMENT , `doctor_name` VARCHAR(100) NOT NULL , PRIMARY KEY (`doctor_id`)) ENGINE = InnoDB;";
// $appointment_tb_creation = "CREATE TABLE `3rdStreetAdr`.`appointment` ( `id` INT NOT NULL AUTO_INCREMENT , `start` DATETIME NOT NULL , `end` DATETIME NOT NULL , `patient_name` VARCHAR(100) NOT NULL , `status` VARCHAR(100) NOT NULL DEFAULT 'free' , `patient_session` VARCHAR(100) NOT NULL , `doctor_id` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";







?>
