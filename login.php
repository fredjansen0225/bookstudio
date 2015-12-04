<?php
require_once '_db.php';
require_once __DIR__ . '/Facebook/autoload.php';


$fb = new Facebook\Facebook([
    'app_id' => '{app-id}',
    'app_secret' => '{app-secret}',
    'default_graph_version' => 'v2.2',
]);





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
    <script src="js/daypilot/daypilot-all.min1.js"></script>

<?php require_once '_header.php'; ?>

    <div class="main">
    </div>
    <div class="clear">
    </div>
</body>
</html>

