<?php
require_once '_db.php';

if(defined('DB_SQLITE'))
{    
	$scheduler_doctors = $db->query('SELECT * FROM [doctor] ORDER BY [doctor_name]');
}else{
	$dbMysql->orderBy('doctor_name','asc');
	$scheduler_doctors = $dbMysql->get('doctor');
}

class Resource {}

$result = array();

foreach($scheduler_doctors as $doctor) {
  $r = new Resource();
  $r->id = $doctor['doctor_id'];
  $r->name = $doctor['doctor_name'];
  $result[] = $r;
}

header('Content-Type: application/json');
echo json_encode($result);

?>
