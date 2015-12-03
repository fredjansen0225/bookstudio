<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        date_default_timezone_set("UTC");


        
        
        function date_modify($date, $diff) {
            $date = clone $date;
            $date->modify($diff);
            return $date;
        }
        ?>
    </body>
</html>
