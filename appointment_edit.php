<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit Appointment</title>
    	<link type="text/css" rel="stylesheet" href="media/layout.css" />    
        <script src="js/jquery-1.11.2.min.js" type="text/javascript"></script>
        
    </head>
    <body>
        <script src="js/angular.min.js"></script>
        <script src="js/daypilot/daypilot-all.min.js" type="text/javascript"></script>

        <?php
            // check the input
            is_numeric($_GET['id']) or die("invalid URL");
            
            require_once '_db.php';
            
            $event;

            if(defined('DB_SQLITE'))
            {
                $stmt = $db->prepare('SELECT * FROM [appointment] WHERE appointment_id = :id');
                $stmt->bindParam(':id', $_GET['id']);
                $stmt->execute();
                $event = $stmt->fetch();
            }else
            {
                $dbMysql->where('appointment_id', $_GET['id']);
                $event = $dbMysql->get('appointment');
            }

        ?>
        
        <div ng-app="main" ng-controller="AppointmentEditCtrl" style="padding:10px">

            <h1>Edit Appointment Slot</h1>
            
            <div class="space">
                <button id="delete" ng-click="delete()">Delete</button>
            </div>

            <div>Start:</div>
            <div><input type="text" id="start" name="start" disabled ng-model="appointment.start" /></div>

            <div>End:</div>
            <div><input type="text" id="end" name="end" disabled  ng-model="appointment.end" /></div>

            <div class="space">
                <div>Doctor:</div>
                <div>
                    <select id="resource" name="resource" disabled ng-model="appointment.doctor">
                    <?php 

                        if(defined('DB_SQLITE'))
                        {
                            foreach($db->query('SELECT * FROM [doctor] ORDER BY [doctor_name]') as $item) {
                                $selected = "";
                                if ($event["doctor_id"] == $item["doctor_id"]) {
                                    $selected = " selected";
                                }
                                echo "<option value='".$item["doctor_id"]."'".$selected.">".$item["doctor_name"]."</option>";
                            }
                        }
                        else{
                            $dbMysql->orderBy('doctor_name','asc');
                            foreach($dbMysql->get('doctor') as $item) {
                                $selected = "";
                                if ($event["doctor_id"] == $item["doctor_id"]) {
                                    $selected = " selected";
                                }
                                echo "<option value='".$item["doctor_id"]."'".$selected.">".$item["doctor_name"]."</option>";
                            }
                        }
                    ?>
                    </select>
                </div>
            </div>

            <div class="space">
                <div>Status:</div>
                <div>
                    <select id="status" name="status" ng-model="appointment.status">
                        <option value="free">Free</option>
                        <option value="waiting">Waiting</option>
                        <option value="confirmed">Confirmed</option>
                    </select>
                </div>
            </div>

            <div>Name: </div>
            <div><input type="text" id="name" name="name" ng-model="appointment.name" ng-disabled="appointment.status === 'free'" /></div>
            
            <div class="space"><input type="submit" value="Save" ng-click="save()" /> <a href="" id="cancel" ng-click="cancel()">Cancel</a></div>
            
        </div>
        
        <script type="text/javascript">
            
        var app = angular.module('main', ['daypilot']).controller('AppointmentEditCtrl', function($scope, $timeout, $http) {
            $scope.appointment = {
                id: '<?php echo $event['appointment_id'] ?>',
                name: '<?php echo $event['appointment_patient_name'] ?>',
                doctor: '<?php echo $event['doctor_id'] ?>',
                status: '<?php echo $event['appointment_status'] ?>',
                start: '<?php print (new DateTime($event['appointment_start']))->format('d/M/y g:i A') ?>',
                end: '<?php print (new DateTime($event['appointment_end']))->format('d/M/y g:i A') ?>',
            };
            $scope.delete = function() {
                $http.post("backend_delete.php", $scope.appointment).success(function(data) {
                    DayPilot.Modal.close(data);
                });   
            };
            $scope.save = function() {
                $http.post("backend_update.php", $scope.appointment).success(function(data) {
                    DayPilot.Modal.close(data);
                });
            };
            $scope.cancel = function() {
                DayPilot.Modal.close();
            };
            
            $("#name").focus();
        });
           
        </script>
    </body>
</html>
