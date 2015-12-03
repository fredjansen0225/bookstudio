<?php
require_once '_db.php';

$json = file_get_contents('php://input');
$params = json_decode($json);

if(defined('DB_SQLITE'))
{
	$stmt = $db->prepare("UPDATE appointment SET appointment_patient_name = :name, appointment_status = :status WHERE appointment_id = :id");
	$stmt->bindParam(':id', $params->id);
	$stmt->bindParam(':name', $params->name);
	$stmt->bindParam(':status', $params->status);
	$stmt->execute();
}else{
	$data = Array('appointment_patient_name' => $params->name, 'appointment_status' => $params->status);
	$dbMysql->where('appointment_id',$params->id);
	$dbMysql->update('appointment', $data);
}

class Result {}
$response = new Result();
$response->result = 'OK';
$response->message = 'Update successful';

header('Content-Type: application/json');
echo json_encode($response);

?>
