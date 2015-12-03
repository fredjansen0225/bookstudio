<?php
    require_once '_db.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>AngularJS Doctor Appointment Scheduling Tutorial</title>
        
        <!-- demo stylesheet -->
    	<link type="text/css" rel="stylesheet" href="media/layout.css" />    
        
        <style type="text/css">
            #calendar .calendar_default_event_bar, #calendar .calendar_default_event_bar_inner {
                width: 10px;
            }

            #calendar .calendar_default_event_inner {
                padding-left: 12px;
            }                        
        </style>

    </head>
    <body>
        <script src="js/jquery-1.11.2.min.js"></script>
        <script src="js/angular.min.js"></script>
        <script src="js/daypilot/daypilot-all.min.js"></script>
        
        <?php require_once '_header.php'; ?>
                
        <div class="main">
            
            <?php require_once '_navigation.php'; ?>
                
            <div ng-app="main" ng-controller="DemoCtrl" >

                <div style="float:left; width:160px">
                    <daypilot-navigator id="navigator" daypilot-config="navigatorConfig" daypilot-events="events"></daypilot-navigator>
                </div>
                <div style="margin-left: 160px">
                    
                    <div class="space">
                        <select id="doctor" name="doctor" ng-model="doctor">
                        <?php 
                            if(defined('DB_SQLITE'))
                            {
                                foreach($db->query('SELECT * FROM [doctor] ORDER BY [doctor_name]') as $item) {
                                    echo "<option value='".$item["doctor_id"]."'>".$item["doctor_name"]."</option>";
                                }
                            }else
                            {
                                $dbMysql->orderBy('doctor_name','asc');
                                foreach($dbMysql->get('doctor') as $item) {
                                    echo "<option value='".$item["doctor_id"]."'>".$item["doctor_name"]."</option>";
                                }
                            }
                        ?>
                        </select>
                    </div>
                    <daypilot-calendar id="calendar" daypilot-config="calendarConfig" daypilot-events="events" ></daypilot-calendar>
                </div>
            </div>

            <script>
                var app = angular.module('main', ['daypilot']).controller('DemoCtrl', function($scope, $timeout, $http) {
                    
                $scope.doctor = '<?php echo defined(DB_SQLITE) ? $db->query('SELECT * FROM [doctor] ORDER BY [doctor_name]')->fetch()['doctor_id'] : $dbMysql->rawQuery('SELECT * FROM doctor ORDER BY doctor_name')['doctor_id']; ?>';
                $scope.doctor = '2';

                $scope.navigatorConfig = {
                    selectMode: "week",
                    showMonths: 3,
                    skipMonths: 3,
                    onTimeRangeSelected: function(args) {     
                        loadEvents(args.start.firstDayOfWeek(), args.start.addDays(7));
                    }
                };
                
                $scope.calendarConfig = {
                    viewType: "Week",
                    timeRangeSelectedHandling: "Disabled",
                    onEventMoved: function(args) {
                        $http.post("backend_move.php", args).success(function(data) {
                            $scope.calendar.message(data.message);
                        }); 
                    },
                    onEventResized: function(args) {
                        $http.post("backend_move.php", args).success(function(data) {
                            $scope.calendar.message(data.message);
                        }); 
                    },
                    onBeforeEventRender: function(args) {
                        switch (args.data.tags.status) {
                            case "free":
                                args.data.barColor = "green";
                                break;
                            case "waiting":
                                args.data.barColor = "orange";
                                break;
                            case "confirmed":
                                args.data.barColor = "#f41616";  // red            
                                break;                            
                        }
                    },
                    onEventClick: function(args) {
                        var modal = new DayPilot.Modal({
                            onClosed: function(args) {
                                if (args.result) {  // args.result is empty when modal is closed without submitting
                                    loadEvents();
                                }
                            }
                        });

                        modal.showUrl("appointment_edit.php?id=" + args.e.id());
                    }
                };
                
                $scope.$watch("doctor", function() {
                    loadEvents();
                });

               function loadEvents(day) {
                    
                    var params = {
                        doctor: $scope.doctor,
                        start: $scope.navigator.visibleStart(),
                        end: $scope.navigator.visibleEnd().toString()
                    };
                    
                    $http.post("backend_events_doctor.php", params).success(function(data) {
                        if (day) {
                            $scope.calendarConfig.startDate = day;
                        }
                        $scope.events = data;
                    });   
                }
            });

            </script>

        </div>
        <div class="clear">
        </div>
    </body>    
</html>
