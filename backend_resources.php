<?php
require_once '_db.php';

if(defined('DB_SQLITE'))
{    
	$scheduler_clients = $db->query('SELECT * FROM [client] ORDER BY [client_name]');
}else{
	$dbMysql->orderBy('doctor_name','asc');
	$scheduler_clients = $dbMysql->get('client');
}

class Client {}

$result = array();

foreach($scheduler_clients as $client) {
  $r = new Client();
  $r->id = $client['client_id'];
  $r->name = $client['client_name'];
  $r->email = $client['client_name'];
  $result[] = $r;
}

header('Content-Type: application/json');
echo json_encode($result);

?>
