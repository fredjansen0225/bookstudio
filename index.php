<?php
require_once '_db.php';

if(!isset($_SESSION['fb_access_token']))
    header('Location: login.php');

?>
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

    <div ng-app="main" ng-controller="BookingCtrl" >

        <div style="float:left; width:160px">
            <daypilot-navigator id="navigator" daypilot-config="navigatorConfig" daypilot-events="events"></daypilot-navigator>
        </div>
        <div style="margin-left: 160px">
            <daypilot-calendar id="calendar" daypilot-config="calendarConfig" daypilot-events="events" ></daypilot-calendar>
        </div>
    </div>

    <script>
        var app = angular.module('main', ['daypilot']).controller('BookingCtrl', function($scope, $timeout, $http) {
            $scope.client = '<?php echo $_SESSION['client_id']; ?>';

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
                eventMoveHandling: "Disabled",
                eventResizeHandling: "Disabled",
                onBeforeEventRender: function(args) {
                    switch (args.data.tags.status) {
                        case "free":
                            args.data.barColor = "orange";
                            break;
                        case "waiting":
                            args.data.barColor = "#f41616";
                            break;
                        case "confirmed":
                            args.data.barColor = "green";  // red
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

                    modal.showUrl("appointment_edit.php?editable=false&id=" + args.e.id());
                },
            };

            $scope.$watch("client", function() {
                loadEvents();
            });

            function loadEvents(day) {

                var params = {
//                    doctor: $scope.client,
                    start: $scope.navigator.visibleStart(),
                    end: $scope.navigator.visibleEnd().toString()
                };

                $http.post("backend_events.php", params).success(function(data) {
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
