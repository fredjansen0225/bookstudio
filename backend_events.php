<?php
require_once '_db.php';

$json = file_get_contents('php://input');
$params = json_decode($json);
    
if(defined('DB_SQLITE'))
{
	$stmt = $db->prepare('SELECT * FROM [appointment] WHERE NOT ((appointment_end <= :start) OR (appointment_start >= :end))');
	$stmt->bindParam(':start', $params->start);
	$stmt->bindParam(':end', $params->end);
	$stmt->execute();
	$result = $stmt->fetchAll();

}else{
	$data = Array($params->start, $params->end);
    $result = $dbMysql->rawQuery("SELECT * FROM appointment WHERE NOT ((appointment_end <= ?) OR (appointment_start >= ?))",$data);
}



class Event {}
class Tags {}
$events = array();

foreach($result as $row) {
  $e = new Event();
  $e->id = $row['appointment_id'];
  $e->text = $row['appointment_patient_name'] ?: "";
  $e->start = $row['appointment_start'];
  $e->end = $row['appointment_end'];

  $e->resource = $row['client_id'];
  $e->tags = new Tags();
  $e->tags->status = $row['appointment_status'];

  // custom datas
//  $e->html = substr($e->start,11,5) . " ~ " . substr($e->end,11,5);
  if($e->resource != $_SESSION['id']) {
    $e->backColor = 'lightgrey';
    $e->readOnly = true;

  }else
  {
    $e->readOnly = false;
    $e->photo = $_SESSION['photo'];
  }

  switch($e->tags->status)
  {

    case "confirmed":
      $e->barColor = "green";
      break;
    case "finished":
      $e->barColor = "red";
      break;
    case "hold":
      $e->barColor = "orange";
      break;
    default:
      $e->barColor = "orange";
      break;
  }

  $events[] = $e;
}

header('Content-Type: application/json');
echo json_encode($events);

?>
