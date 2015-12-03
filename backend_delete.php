<?php
require_once '_db.php';

$json = file_get_contents('php://input');
$params = json_decode($json);

class Result {}

$stmt = $db->prepare("DELETE FROM appointment WHERE appointment_id = :id");
$stmt->bindParam(':id', $params->id);
$stmt->execute();

// $dbMysql->where('id',$prepare->id);
// $dbMysql->delete('appointment');

$response = new Result();
$response->result = 'OK';
$response->message = 'Update successful';

header('Content-Type: application/json');
echo json_encode($response);

?>
