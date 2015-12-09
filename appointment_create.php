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

            $start = $_GET['start'];
            $end = $_GET['end'];
            $client_id = $_GET['client_id'];
            $scale = $_GET['scale'];
//            require_once '_db.php';
        ?>
        
        <div ng-app="main" ng-controller="AppointmentEditCtrl" style="padding:10px">

            <h1>Create Appointment Slot</h1>

            <div>Start:</div>
            <div><input type="text" id="start" name="start" disabled ng-model="appointment.start" /></div>

            <div>End:</div>
            <div><input type="text" id="end" name="end" disabled  ng-model="appointment.end" /></div>

            <div class="space">
                <div>Status:</div>
                <div><input type="text" id="status" name="status" disabled  ng-model="appointment.status" /></div>
            </div>

            <div>Name: </div>
            <div><input type="text" id="name" name="name" ng-model="appointment.name" /></div>
            
            <div class="space"><input type="submit" value="Save" ng-click="save()" /> <a href="" id="cancel" ng-click="cancel()">Cancel</a></div>
            
        </div>
        
        <script type="text/javascript">
            
        var app = angular.module('main', ['daypilot']).controller('AppointmentEditCtrl', function($scope, $timeout, $http) {

            $scope.appointment = {
                start : '<?php echo $start ?>',
                end : '<?php echo $end; ?>',
                client_id : '<?php echo $client_id; ?>',
                scale : '<?php echo $scale; ?>',
                status : 'waiting',
                name : ''
            }

            $scope.save = function() {
                $http.post("backend_create.php", $scope.appointment).success(function(data) {
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
