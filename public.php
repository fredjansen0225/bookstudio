<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>3rdStreetAdr Mixing</title>
        
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
        <script src="js/daypilot/daypilot-all.min1.js"></script>
        
        <?php require_once '_header.php'; ?>
                
        <div class="main">
            
            <?php require_once '_navigation.php'; ?>
                
            <div ng-app="main" ng-controller="DemoCtrl" >

                <div style="float:left; width:160px">
                    <daypilot-navigator id="navigator" daypilot-config="navigatorConfig" daypilot-events="events"></daypilot-navigator>
                </div>
                <div style="margin-left: 160px">
                    <div class="space">Available time slots:</div>
                    <daypilot-calendar id="calendar" daypilot-config="calendarConfig" daypilot-events="events" ></daypilot-calendar>
                </div>
                
            </div>

            <script>
                var app = angular.module('main', ['daypilot']).controller('DemoCtrl', function($scope, $timeout, $http) {
                    
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
                    scale: "Manual",
                    timeRangeSelectedHandling: "Disabled",
                    eventMoveHandling: "Disabled",
                    eventResizeHandling: "Disabled",
//                     timeHeaders : [ { groupBy: "Month" }, { groupBy: "Day", format: "dddd d" }],
                    onBeforeEventRender: function(args) {
                        switch (args.data.tags.status) {
                            case "free":
                                args.data.barColor = "green";
                                args.data.html = "Available";
                                args.data.toolTip = "Click to request this time slot";
                                break;
                            case "waiting":
                                args.data.barColor = "orange";
                                args.data.html = "Your appointment, waiting for confirmation";
                                break;
                            case "confirmed":
                                args.data.barColor = "#f41616";  // red            
                                args.data.html = "Your appointment, confirmed";
                                break;                            
                        }
                    },
                    onEventClick: function(args) {
                        
                        if (args.e.tag("status") !== "free") {
                            $scope.calendar.message("You can only request a new appointment in a free slot.");
                            return;
                        }
                        
                        var modal = new DayPilot.Modal({
                            onClosed: function(args) {
                                if (args.result) {  // args.result is empty when modal is closed without submitting
                                    loadEvents();
                                }
                            }
                        });

                        modal.showUrl("appointment_request.php?id=" + args.e.id());
                    }
                };

                $timeout(function() {
                    loadEvents();
                });
                
                
                function loadEvents(day) {
                    
                    var start = $scope.navigator.visibleStart() > new DayPilot.Date() ? $scope.navigator.visibleStart() : new DayPilot.Date();
                    
                    var params = {
                        start: start.toString(),
                        end: $scope.navigator.visibleEnd().toString()
                    };
                    
                    $http.post("backend_events_free.php", params).success(function(data) {
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
