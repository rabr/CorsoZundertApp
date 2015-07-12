<?php
   //Setup global neosmart STREAM Object ($nss)
   include "../../neosmart-stream/setup.php";

   $nss->streamCSS();
   //$nss->includeFile('jquery.js');
   $nss->streamJS();
   $nss->show();
?>
