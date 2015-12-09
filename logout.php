<?php

session_start();
session_unset();
session_destroy();

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

    <script>

        // This is called with the results from from FB.getLoginStatus().
        function statusChangeCallback(response) {
            console.log('statusChangeCallback');
            console.log(response);
            // The response object is returned with a status field that lets the
            // app know the current login status of the person.
            // Full docs on the response object can be found in the documentation
            // for FB.getLoginStatus().
            if (response.status === 'connected') {
                // Logged into your app and Facebook.

            } else if (response.status === 'not_authorized') {
                // The person is logged into Facebook, but not your app.
                document.getElementById('status').innerHTML = 'Please log ' +
                    'into this app.';
            } else {
                // The person is not logged into Facebook, so we're not sure if
                // they are logged into this app or not.
                document.getElementById('status').innerHTML = 'Please log ' +
                    'into Facebook.';
            }
        }

        window.fbAsyncInit = function() {
            FB.init({
                appId      : '<?php echo $fbAppId ?>',
                cookie     : true,  // enable cookies to allow the server to access
                                    // the session
                xfbml      : true,  // parse social plugins on this page
                version    : 'v2.2' // use version 2.2
            });

            FB.getLoginStatus(function(response) {
                if (!response.session) {
                    window.location = "login.php";
                    return;
                }
                logOut();
            });

        };

        logOut = function() {
            FB.logout(function(response) {
                // user is now logged out

                console.log('logged out');
                console.log(response);

                window.location = "login.php";
            });
        };

        // Load the SDK asynchronously
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        // Here we run a very simple test of the Graph API after login is
        // successful.  See statusChangeCallback() for when this call is made.
    </script>
</body>
</html>

