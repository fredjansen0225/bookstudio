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
        <script src="js/daypilot/daypilot-all.min1.js" type="text/javascript"></script>

        <?php
            // check the input
            is_numeric($_GET['id']) or die("invalid URL");

            $editable = $_GET['editable'];
            
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
                $event = $dbMysql->getOne('appointment');
            }

        ?>
        
        <div ng-app="main" ng-controller="AppointmentEditCtrl" style="padding:10px">

            <h1>{{readOnly ? 'Booking Information' : 'Edit Booking Information' }}</h1>
            


            <div>Start:</div>
            <div><input type="text" id="start" name="start" disabled ng-model="appointment.start" /></div>

            <div>End:</div>
            <div><input type="text" id="end" name="end" disabled  ng-model="appointment.end" /></div>
            <div class="space">
                <div>Status:</div>
                <div>
                    <select id="status" name="status" ng-model="appointment.status" ng-disabled="readOnly">
                        <option value="hold">Hold</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="finished">Finished</option>
                    </select>
                </div>
            </div>

            <div>Name: </div>
            <div><input type="text" id="name" name="name" ng-model="appointment.name" ng-disabled="readOnly" /></div>

            <div class="space"><button id="delete" ng-click="delete()" ng-if="!readOnly" style="margin: 10px;">Delete</button> <input type="submit" value="Save" ng-click="save()" ng-if="!readOnly" style="margin: 10px;"/> <a href="" id="cancel" ng-click="cancel()"> {{readOnly ? 'Close' : 'Cancel'}}</a></div>

        </div>
        
        <script type="text/javascript">
            
        var app = angular.module('main', ['daypilot']).controller('AppointmentEditCtrl', function($scope, $timeout, $http) {
            $scope.readOnly = !(<?php echo $editable;?>);

            $scope.appointment = {
                id: '<?php echo $event['appointment_id'] ?>',
                name: '<?php echo $event['appointment_patient_name'] ?>',
//                doctor: '<?php //echo $event['client_id'] ?>//',
                doctor: '1',
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
