<?php
    require_once '_db.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>3rdStreetAdr Mixing</title>
        
        <!-- demo stylesheet -->
    	<link type="text/css" rel="stylesheet" href="media/layout.css" />
        <link type="text/css" rel="stylesheet" href="media/custom.css" />
        
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

                    $scope.client = '<?php echo $_SESSION['id']; ?>';
                    console.log("client_id:" + $scope.client);

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
                    // timeRangeSelectedHandling: "Disabled",
                    allowEventOverlap : false,
                    allowMultiSelect : false,
                    dayBeginsHour : 7,
                    dayEndsHour: 21,
                    businessBeginsHour : 8,
                    businessEndsHour: 20,
                    headerDateFormat : 'ddd M/d',
                    headerHeight : 42,
                    loadingLabelText : 'Loading...',

                    onTimeRangeSelected: function(args) {

                        var dp = $scope.calendar;

                        var msg = validateAction(args,'add');
                        if(msg)
                        {
                            $scope.calendar.message(msg);
                            dp.clearSelection();
                        }
                        else
                        {

                            var params = {
                                start: args.start.addMinutes(-30).toString(),             // 30 mins buffer time
                                end: args.end.toString(),
                                resource: $scope.client,
                                scale: 'hours'
                            };

                            var modal = new DayPilot.Modal({
                                onClosed: function(args) {
                                    if (args.result) {  // args.result is empty when modal is closed without submitting

                                        loadEvents();
                                    }
                                    dp.clearSelection();
                                }
                            });

                            modal.showUrl("appointment_create.php?start=" + params.start + "&end=" + params.end + "&client_id=" + params.resource + "&scale=hours");
                        }
                    },

                    onEventMove: function(args){
                        var msg = validateAction(args,'move');
                        if(msg)
                        {
                            $scope.calendar.message(msg);
                            args.preventDefault();
                        }
                        //console.log(args);
                    },
                    onEventMoved: function(args) {
                        console.log(args);

                        $http.post("backend_move.php", args).success(function(data) {
                            $scope.calendar.message(data.message);
                        }); 
                    },

                    onEventResize: function(args){
                        var msg = validateAction(args,'resize');
                        if(msg)
                        {
                            $scope.calendar.message(msg);
                            args.preventDefault();
                        }
                    },

                    onEventResized: function(args) {
                        console.log(args);

                        $http.post("backend_move.php", args).success(function(data) {
                            $scope.calendar.message(data.message);
                        }); 
                    },

                    onBeforeEventRender: function(args) {
                        //console.log(args);

                        var profilePhoto = args.data.photo ? "<img class='profile_photo' src='" + args.data.photo + "' /> <br/>" : "",
                            text = "<b>" + args.data.text + "</b> <br/>",
                            time = args.data.start.toString().substring(11,16) + " ~ " + args.data.end.toString().substring(11,16);

                        args.e.html = profilePhoto + text + time;

                        if(args.data.end < new DayPilot.Date())
                            args.data.barColor = "red"; // red

//                        if(args.e.resource != $scope.client)
//                            args.data.backColor = "lightgrey";
//
//                        switch (args.data.tags.status) {
//                            case "finished":
//                                args.data.barColor = "red"; // red
//                                break;
//                            case "hold":
//                                args.data.barColor = "orange";
//                                break;
//                            case "confirmed":
//                                args.data.barColor = "green";
//                                break;
//                        }

                    },

                    onEventClick: function(args) {
                        var msg = validateAction(args,'click');
                        var writable = "true";
                        if(msg)
                        {
                            if(msg == 'readonly')
                                writable = "false";
                            else
                            {
                                $scope.calendar.message(msg);
                                return;
                            }
                        }

                        {
                            var modal = new DayPilot.Modal({
                                onClosed: function(args) {
                                    if (args.result) {  // args.result is empty when modal is closed without submitting
                                        loadEvents();
                                    }
                                }
                            });

                            modal.showUrl("appointment_edit.php?editable=" + writable + "&id=" + args.e.id());
                        }
                    }
                };
                
                $scope.$watch("doctor", function() {
                    loadEvents();
                });

               function loadEvents(day) {
                    
                    var params = {
                        client_id: $scope.client,
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

                function validateAction(args,action)
                {
                    var msg=false;
                    var now = new DayPilot.Date();

                    switch (action)
                    {

                        case 'add':
                            //TODO: check user role whether it's admin or not
                            if(false)
                                break;

                            if(args.start < now)
                                msg = "You can't book in times passed already.";

                            break;
                        case 'move':
                            //TODO: check user role whether it's admin or not
                            if(false)
                                break;

                            if(args.newStart < now)
                                msg = "Booking Start time is already passed.";
                            if ((args.newEnd.getHours() + args.newEnd.getMinutes()/60) > $scope.calendar.businessEndsHour)
                                msg = "You can book only in bussiness hours";
                            if(args.e.data.start < now)
                                msg = "Can't edit past booking";
                            if (args.e.data.readOnly)
                                msg = "You can't edit other's booking";

                            break;
                        case 'resize':
                            //TODO: check user role whether it's admin or not
                            if(false)
                                break;

                            if (args.e.readOnly)
                                msg = "You can't edit other's booking";
                            if (args.newEnd.getHours() > $scope.calendar.businessEndsHour)
                                msg = "You can book only in bussiness hours";

                            break;
                        case 'click':
                            //TODO: check user role whether it's admin or not
                            if(false)
                                break;
                            if(args.e.data.start < now)
                                msg = "readonly";
                            if (args.e.data.readOnly)
                                msg = "You can't edit other's booking";

                            break;
                    }

                    return msg;
                }
            });
            </script>
        </div>
        <div class="clear">
        </div>
    </body>    
</html>
