<?php
require_once '_db.php';

$json = file_get_contents('php://input');
$params = json_decode($json);


if(defined('DB_SQLITE'))
{
	$stmt = $db->prepare("UPDATE appointment SET appointment_start = :start, appointment_end = :end WHERE appointment_id = :id");
	$stmt->bindParam(':id', $params->e->id);
	$stmt->bindParam(':start', $params->newStart);
	$stmt->bindParam(':end', $params->newEnd);
	$stmt->execute();
}else{
	$data = Array('appointment_start' => $params->newStart,
					'appointment_end' => $params->newEnd);
	$dbMysql->where('appointment_id',$params->e->id);
	$dbMysql->update('appointment',$data);
}

class Result {}

$response = new Result();
$response->result = 'OK';
$response->message = 'Update successful';

header('Content-Type: application/json');
echo json_encode($response);

?>
