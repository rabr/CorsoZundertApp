<?php
   //Setup global neosmart STREAM Object ($nss)
   include "../../neosmart-stream/setup.php";
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>Your Website</title>
    </head>
    <body>
<?php
   $nss->streamCSS();
   $nss->includeFile('jquery.js');
   $nss->streamJS();
   $nss->show();
?>
    </body>
</html>
