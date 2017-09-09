<?php

/*
 * Corso Zundert Location API to store phone locations in online database
 *
 * version: 1.0
 *
 */
 
 
/*

DESIGN
------

Each running App performs a HTTP POST request at fixed intervals to report its location
Phones are identified by a UUID, and thereby data is anonymized

CONSIDERATIONS
--------------

1. SCALABILITY
a. nr of requests per sec
assume at peak max 4000 active Apps, reporting each 5 secs, more or less uniformly 
distributed -> 800 requests per sec
With reporing each 30 secs -> ~130 requests per sec

b. amount of data that needs to handled per sec

c. processing time per request
To limit processing time and required (parralel) processing power at the server, DB
queries to store incoming data should be as limited as possible -> flat table

2. SECURITY
a. Insertion of wrong data -> move to HTTPS!!!?? does that establish mutual trust??
b. DoS attack on server via this interface

3. DATA FORMAT
a. location object

b. database design
options: 1. one big flat table, 2. table per device + table of devices
option 2 simplifies data analysis, but assumption is that data analysis will be done 
offline and popups will be generated on the fly (so not requiring online data analysis).
option 1 limit processing time, so seems good enough.
 
4. CONFIGURATION
a. Include option to remotely turn off tracking, per UUID. In case of scalability or
other issues, or to simply ignore certain phones to not pollute the DB.
Since we use a RESTfull interface from App towards cloud, the App needs to first perform
a GET request to check whether reporting is needed.

OPEN POINTS
-----------
- Ask legal rep to draft a privacy / terms & conditions text for in the App
- Do we need to check scalability with hosting provider (fxw)?

*/


/* START OF API CODE */

// CONSTANTS
$DEBUG_LOG = true;


// First connect to the DB
$conn = mysql_connect('localhost', "bloemencor_db", 'Bl0emen!');
mysql_select_db ('bloemencor_location', $conn); 
mysql_query('SET CHARACTER SET utf8');

// Check whether there is a GET request for configuration
if ($_SERVER["REQUEST_METHOD"] == "GET") {
   if ($DEBUG_LOG) file_put_contents( 'debug_post.log', "[GET]", FILE_APPEND);

   // return the configuration setting as json
   $conf = array();
   
   $sqlwrk = "SELECT * FROM config LIMIT 1";
   $res = mysql_query($sqlwrk);
   if ($res == FALSE) { 
      if ($DEBUG_LOG) file_put_contents( 'debug_post.log', mysql_error().$sqlwrk, FILE_APPEND);
      exit();
   }
   
   $conf = mysql_fetch_array($res);  // should only be 1 row as is this special config table
   
   echo json_encode($conf);
   
   if ($DEBUG_LOG) file_put_contents( 'debug_post.log', json_encode($conf), FILE_APPEND);

} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // No, proceed with handling the POST request, if any
   if ($DEBUG_LOG) file_put_contents( 'debug_post.log', "[POST]", FILE_APPEND);
   

   $input_str = file_get_contents('php://input');
   $data = json_decode($input_str, true);
   
   if ($DEBUG_LOG) file_put_contents( 'debug_post.log', $input_str, FILE_APPEND);
   
   if ($data["phoneid"]!="") {
      
      //check whether we have seen this phone before
      $sqlwrk = "SELECT * FROM phones WHERE phones.uuid = '" . $data["phoneid"] ."'";
      $res = mysql_query($sqlwrk); 
      if ($res == FALSE) { 
         if ($DEBUG_LOG) file_put_contents( 'debug_post.log', mysql_error().$sqlwrk , FILE_APPEND);
         exit();
      }
      
      if (mysql_num_rows($res) == 0) {
         // No we have not seen this phone before
         // Create a table entry for it
         $sqlwrk = "INSERT INTO phones (uuid, track) VALUES('" . $data['phoneid'] . "', 1)";
         $res = mysql_query($sqlwrk);
         if ($res == FALSE) { 
            if ($DEBUG_LOG) file_put_contents( 'debug_post.log', mysql_error().$sqlwrk, FILE_APPEND);
            exit();
         }
      }
      
      //now store the location
      //both heading and speed can be null, so we need to handle this correctly for the insert string
      if ($data ['coords']['heading'] == null) $data ['coords']['heading'] = 0;  // physically 0 is not the correct value here, but 'null' cannot be used
      if ($data ['coords']['speed'] == null) $data ['coords']['speed'] = 0;
      
      $sqlwrk = "INSERT INTO tracks (phoneID, latitude, longitude, accuracy, heading, speed)
                 VALUES ('" . $data['phoneid'] . "', " . $data['coords']['latitude'] . ", " . $data['coords']['longitude'] . ", " .
                         $data['coords']['accuracy'] . ", " . $data ['coords']['heading'] . ", " . 
                         $data['coords']['speed'] . ")";
      $res = mysql_query($sqlwrk);
      if ($res == FALSE) { 
         if ($DEBUG_LOG) file_put_contents( 'debug_post.log', mysql_error().$sqlwrk, FILE_APPEND);
         exit();
      }
      
      if ($DEBUG_LOG) file_put_contents( 'debug_post.log', "[SUCCESS]", FILE_APPEND);
   }
}

mysql_close($conn);

?>