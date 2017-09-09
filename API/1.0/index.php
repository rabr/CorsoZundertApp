<?php

/*
 * Corso Zundert API to the online database
 * used by App
 *
 * version: 1.0
 *
 */

$base_path_img   = "img";

/*
 * FUNCTIONS
 */

function volgorde_item_wagen($jaar,$foto_maquette, $nummer, $titel, $buurtschap, $buurtschap_afk, $buurtschap_om, $ontw_array, $taal, $omschrijving, $titel_vj, $punten_vj, $prijs_vj)
{
  global $base_path_img;  
  $obj = array();
  
  // vertalingen
  $buurtschap_lang = array("nl" => "Buurtschap",
                           "en" => "Hamlet",
                           "du" => "Nachbarschaftsverein", 
                           "fre" => "Hameau");

  $ontwerpers_lang = array("nl" => "Ontwerpers",
                           "en" => "Designers",
                           "du" => "Designers", 
                           "fr" => "Designers");
  $en_lang = array("nl" => "en",
                   "en" => "and",
                   "du" => "und", 
                   "fr" => "et");
  

  
  
  //creeer string met namen v.d. ontwerpers
  $num_ontw = count($ontw_array);
  // er is altijd 1 ontwerpers
  $namen_ontwerpers = $ontw_array[0]["naam"] . ', ';
  //nu de rest tot de een na laatste, komma gescheiden
  for ($i=1; $i<$num_ontw-1; $i++)
  {
    $namen_ontwerpers = $namen_ontwerpers . $ontw_array[$i]["naam"] . ', ';
  }
  //haal laatste komma weg  (altijd, bugfix door Niels, bij ŽŽn ontwerper bleef komma staan)
  $namen_ontwerpers = substr($namen_ontwerpers, 0, -2);  
  // en de laatste toevoegen met 'en' ipv komma
  if ($num_ontw>1) {
    $namen_ontwerpers .= " " . $en_lang[$taal] . " " . $ontw_array[$num_ontw-1]["naam"];
  } 
  
  //vul het object
  $obj["type"] = "wagen";
  $obj["wagen"] = array();
  $obj["wagen"]["startnummer"] = $nummer;
  $obj["wagen"]["titel"] = $titel;
  $obj["wagen"]["omschrijving"] = $omschrijving;
  $obj["wagen"]["ontwerpers"] = $namen_ontwerpers;
  $obj["buurtschap"] = array();
  $obj["buurtschap"]["naam"] = $buurtschap;
  $obj["buurtschap"]["afkorting"] = $buurtschap_afk;
  $obj["buurtschap"]["omschrijving"] = $buurtschap_om;
  $obj["buurtschap"]["vorigjaar"] = array();
  $obj["buurtschap"]["vorigjaar"]["titel"] = $titel_vj;
  $obj["buurtschap"]["vorigjaar"]["prijs"] = $prijs_vj;
  $obj["buurtschap"]["vorigjaar"]["punten"] = $punten_vj;
  
  return $obj;
}

function volgorde_item_korps($naam, $foto, $plaats, $land, $link, $omschrijving)
{
  global $base_path_img; 
  $obj = array();
  
  $obj["type"] = "korps";
  $obj["naam"] = $naam;
  $obj["plaats"] = $plaats;
  $obj["land"] = $land;
  $obj["link"] = $link;
  $obj["omschrijving"] = $omschrijving;
  $obj["foto"] = $foto;
  
  return $obj;
}


// ----------------------------------------------------------------------------------------
// Begin API code
// 
// URL format: ?{optocht,uitslag}&jaar=[int]&taal={nl,en,fr}
// ----------------------------------------------------------------------------------------


/* Start with parsing the url parameters */
//print_r($_GET);

$params = array();

//print_r($_GET);

//check which request
if (isset($_GET["optocht"])) $params["type"]="optocht";
else if (isset($_GET["uitslag"])) $params["type"]="uitslag";
else throw new Exception('Onbekend type request');

if (is_numeric($_GET["jaar"]) && $_GET["jaar"]>1936) $params["jaar"] = $_GET["jaar"];
else $params["jaar"] = 2015;

switch ($_GET["taal"]) {
   case "en" : $params["taal"] = "en"; break;
   case "fr" : $params["taal"] = "fr"; break;
   default   : $params["taal"] = "nl"; break;
}

if (is_bool($_GET["test"])) $params["test"] = $_GET["test"];
else $params["test"] = false;


/* Start filling the object...*/

$ovdata = array();
$ovdata["jaar"] = $params['jaar'];
$ovdata["taal"] = $params["taal"];
$ovdata["timestamp"] = time();
$ovdata["data"] = array();

$testmode = $params['test'];


if ($testmode==1) echo '<b><i>optocht_volgorde test mode</i></b><br><br>';



if ($testmode==1) {
  echo "Jaar: " . $params['jaar'] . "<br>";
  echo "Taal: " . $params["taal"] . "<br><br>";
}

// eerst verbinden met de database
$conn = mysql_connect('localhost', "bloemencor_db", 'Bl0emen!');
      mysql_select_db ('bloemencor_db', $conn); 

mysql_query('SET CHARACTER SET utf8');


if ($params["type"]=="optocht") {

   // kijk of alle wagens van dit jaar al een startnummer hebben, 
   // zo ja dan kan de volgorde gegeneerd en gepubliceerd worden
   $sqlwrk = "Select wagens.jaar, wagens.startnummer
   From wagens
   Where wagens.jaar = " . $params['jaar'] . " And ISNULL(wagens.startnummer)";

   $res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
   $num_wagens_zonder_startnr = mysql_num_rows($res);
   if ($testmode==1) echo "aantal wagens zonder startnummer: " . $num_wagens_zonder_startnr . "<br>";

   $wagenid = $_GET["wagenid"];

   $sqlwrk = "Select wagens.jaar,
      wagens.id,
      wagens.`buurtschap-id`,
      wagens.titel_nl,
      wagens.titel_en,
      wagens.titel_du,
      wagens.titel_fr,  
      wagens.foto_maquette,
      wagens.omschrijving_nl,
      wagens.omschrijving_en,
      wagens.omschrijving_du,
      wagens.omschrijving_fr,
      buurtschappen.naam,
      buurtschappen.afkorting,
      buurtschappen.omschrijving,
      wagens.startnummer
      From wagens Inner Join buurtschappen On buurtschappen.id = wagens.`buurtschap-id`
      Where wagens.jaar = " . $params['jaar'] . " AND wagens.zichtbaar = 1
      Order By COALESCE(wagens.startnummer, ~0)";


   $res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
   
   while($row = mysql_fetch_array($res)) 
   {
      $sqlwrkk = "Select `jaarinfo-korpsen`.*,
         `jaarinfo-korpsen`.`voor_wagen-id`,
         korpsen.*,
         `jaarinfo-korpsen`.`korps-id`
         From `jaarinfo-korpsen` Inner Join
         korpsen On `jaarinfo-korpsen`.`korps-id` = korpsen.id
         Where `jaarinfo-korpsen`.jaar = " . $params['jaar'] . " And `jaarinfo-korpsen`.`voor_wagen-id` = '".  $row["id"] . "'";

      $ress = mysql_query($sqlwrkk) or die (mysql_error().$sqlwrkk);
      while($roww = mysql_fetch_array($ress)) 
      {
         // de juiste titel op basis van de huidige taal bepalen
         switch($params["taal"]) {
            case 'nl' : $omschrijving =$roww["omschrijving"]; break;
            case 'en' : $omschrijving =$roww["omschrijving_en"]; break;    
            //case 'du' : $omschrijving =$roww["omschrijving_du"]; break;    
            case 'fr' : $omschrijving =$roww["omschrijving_fr"]; break;
            default: $omschrijving =$roww["omschrijving"]; break;
         }
         
         $ovdata["data"][] = volgorde_item_korps($roww["naam"], $roww["foto"], $roww["plaats"], $roww["land"], $roww["link"], $omschrijving);
      }

      $sqlwrkk = "Select `wagen-ontwerpers`.`ontwerper-id`,
         ontwerpers.naam
         From `wagen-ontwerpers` 
         Inner Join ontwerpers On ontwerpers.id = `wagen-ontwerpers`.`ontwerper-id`
         Where `wagen-ontwerpers`.`wagen-id` = ' " . $row["id"] . " ' ";

      $ress = mysql_query($sqlwrkk) or die (mysql_error().$sqlwrkk);
      $num_ontwerpers =  mysql_num_rows ($ress);
      $ontw_ar = array();
      for ($i=0; $i<$num_ontwerpers; $i++)
      {
         $roww = mysql_fetch_array($ress);
         $ontw_ar[$i]["naam"] = $roww["naam"];
         $ontw_ar[$i]["id"] = $roww["ontwerper-id"];
      }
  
      // haal de de prijs en punten van vorig jaar op
      $sqlwrkkk = "Select wagens.jaar,
         wagens.id,
         wagens.`buurtschap-id`,
         wagens.titel_nl,
         wagens.titel_en,
         wagens.titel_du,
         wagens.titel_fr,    
         wagens.punten,
         wagens.prijs
         From wagens
         Where wagens.jaar = " . ($params['jaar']-1) . " AND wagens.`buurtschap-id` = " . $row["buurtschap-id"];
      $resss = mysql_query($sqlwrkkk) or die (mysql_error().$sqlwrkkk);
      $rowww = mysql_fetch_array($resss);
      
      // haal de buurtschap omschrijvingen op
      $sqlwrkkkk = "Select buurtschappen.*
         From buurtschappen
         Where buurtschappen.id = " . $row["buurtschap-id"];
      $ressss = mysql_query($sqlwrkkkk) or die (mysql_error().$sqlwrkkkk);
      $rowwww = mysql_fetch_array($ressss);

  
      if ($row['startnummer'] != '') {
         // Er is al een startnummer, dus de maquette fotonaam is nu met startnummer
         $startnum = $row['startnummer'];
      } else {
         // Er is nog geen startnummer bekend (voor bekendmaking) dus gebruik 00
         $startnum = 0;
      }
      // TEMP HACK OMDAT UPLOAD EN AUTOMATISCHE RENAMING NOG NIET WERKT
      $startnum = 0;
  
      $foto_maq = sprintf("%d-%s-M%02d.jpg", $params['jaar'], $row['afkorting'], $startnum);
      $foto_maq_volledig = $base_path_img . '/wagens/' . $foto_maq;
  
      // de juiste titel op basis van de huidige taal bepalen
      switch($params["taal"]) {
         case 'nl' : $titel =$row["titel_nl"]; $omschrijving =$row["omschrijving_nl"]; $omschr_buurt =$rowwww["omschrijving"]; $titel_vj =$rowww["titel_nl"]; break;
         case 'en' : $titel =$row["titel_en"]; $omschrijving =$row["omschrijving_en"]; $omschr_buurt =$rowwww["omschrijving_en"]; $titel_vj =$rowww["titel_en"]; break;    
         case 'du' : $titel =$row["titel_du"]; $omschrijving =$row["omschrijving_du"]; //$omschr_buurt =$rowwww["omschrijving_du"]; $titel_vj =$rowww["titel_du"]; break;    
         case 'fr' : $titel =$row["titel_fr"]; $omschrijving =$row["omschrijving_fr"]; $omschr_buurt =$rowwww["omschrijving_fr"]; $titel_vj =$rowww["titel_fr"]; break;
         default: $titel =$row["titel_nl"]; $omschrijving =$row["omschrijving_nl"]; $omschr_buurt =$rowwww["omschrijving"]; $titel_vj =$rowww["titel_nl"]; break;
      }
  
      $ovdata["data"][] = volgorde_item_wagen($params['jaar'],$foto_maq_volledig, $row["startnummer"], $titel, $row["naam"], $row["afkorting"], $omschr_buurt, $ontw_ar, $params["taal"], $omschrijving, $titel_vj, $rowww["punten"], $rowww["prijs"]);
   }

   // Er kan een korps het corso afsluiten. In dat geval is 'voor-wagen_id' null.
   $sqlwrkk = "Select `jaarinfo-korpsen`.*,
      `jaarinfo-korpsen`.`voor_wagen-id`,
      korpsen.*,
      `jaarinfo-korpsen`.`korps-id`
      From `jaarinfo-korpsen` Inner Join
      korpsen On `jaarinfo-korpsen`.`korps-id` = korpsen.id
      Where `jaarinfo-korpsen`.jaar = " . $params['jaar'] . " And `jaarinfo-korpsen`.`voor_wagen-id` is null";

   $ress = mysql_query($sqlwrkk) or die (mysql_error().$sqlwrkk);
   while($roww = mysql_fetch_array($ress)) 
   {
      $ovdata["data"][] = volgorde_item_korps($roww["naam"], $roww["foto"], $roww["plaats"], $roww["land"], $roww["link"], $roww["omschrijving"]);
   }

   
   
   
} else if ($params["type"]=="uitslag") {

   // haal het aantal wagen dat deelneemt in het jaar op
   $sqlwrk = "Select wagens.*
   From wagens
   Where wagens.jaar = " . $params['jaar'];
   $res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
   
   $ovdata["deelnemers"] = mysql_num_rows($res);
  
   // haal de wagens op, gesorteerd op prijs, wagens zonder prijs zijn nog niet bekend
   // wagens met negatieve punten hebben om een of andere reden niet mee kunnen rijden,
   // de reden hiervoor staat dan vermeld in het veld 'bijzonderheden'
   $sqlwrk = "Select wagens.*,
   wagens.jaar,
   wagens.punten,
   buurtschappen.naam,
   buurtschappen.afkorting
   From wagens Inner Join buurtschappen On buurtschappen.id = wagens.`buurtschap-id`
   Where wagens.jaar = " . $params['jaar'] . " AND wagens.prijs > 0
   Order By wagens.prijs ASC";
  
   // display de wagens
   $res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
   
   $i = 1;
   while($row = mysql_fetch_array($res)) 
   {
      $obj = array();
      
      $prijs = $row["prijs"];
      
      if ($i<$prijs) {
          // er zijn nog een aantal lege plekken tussen deze wagen en de huidige regel
          // opvullen met 'onbekende' regels
          while ($i<$prijs) {
            //uitslag_item_onbekend($i);
            $obj["prijs"] = $i;
            $obj["punten"] = "";
            $obj["ereprijs"] = "";
            $obj["titel"] = "";
            $obj["buurtschap"] = "";
            $obj["foto"] = "";
      
            $ovdata["data"][] = $obj;
      
            $i++;
          }
      }
      // Nu deze uitslag regel
      
                
      // de juiste titel op basis van de huidige taal bepalen
      switch($params["taal"]) {
         case 'nl' : $titel =$row["titel_nl"]; break;
         case 'en' : $titel =$row["titel_en"]; break;    
         case 'du' : $titel =$row["titel_du"]; break;    
         case 'fr' : $titel =$row["titel_fr"]; break;
         default   : $titel =$row["titel_nl"]; break;
      }
    
      // check of deze wagen nog ereprijzen gewonnen heeft
      // een inner join op de oorspronkelijk query werkt niet omdat dat alleen de wagens met ereprijzen
      // teruggeeft en niet alle wagens...dus we zullen het apart op moeten zoeken
      // LET OP: er kunnen meerdere ereprijzen voor 1 wagen zijn.
      $sqlwrk2 = "Select `wagen-ereprijzen`.*
      From `wagen-ereprijzen`
      Where `wagen-ereprijzen`.`wagen-id` = " . $row["id"];
      $res2 = mysql_query($sqlwrk2) or die (mysql_error().$sqlwrk2);
      $ereprijzen = "";
      while($row2 = mysql_fetch_array($res2)) $ereprijzen = $ereprijzen . $row2["ereprijs"] . ", ";
      if ($ereprijzen != "") $ereprijzen = substr($ereprijzen, 0, -2);
    
      //foto_url is only filename as the path is constant so no need to return that for each object
      //the base path will be provided as a separate item in the returned json object
      $foto_wagen = sprintf("%d-%s-W%02d", $params['jaar'], $row["afkorting"], $row["startnummer"]);
      $search = '../../uploads/images/archief/wagens/' . $params['jaar'] . '/' . $foto_wagen . '-' . "*" . '.jpg';
      $thumbs = glob($search);
      $foto_url = basename($thumbs[0]);
    
      $obj["prijs"] = $prijs;
      $obj["punten"] = $row["punten"];
      $obj["ereprijs"] = $ereprijzen;
      $obj["titel"] = $titel;
      $obj["buurtschap"] = $row["naam"];
      $obj["foto"] = $foto_url;
      
      $ovdata["data"][] = $obj;
    
      //uitslag_item_wagen($params['jaar'], $row["prijs"], $row["id"], $titel, $row["naam"], $row["afkorting"], $row["startnummer"], $row["punten"],$ereprijzen, $row["bijzonderheden"], $taal);
      $i++;
   }
  
   // Check of er nog wagens zijn die uitgevallen zijn (punten < 0)
   $sqlwrk = "Select wagens.*,
   wagens.jaar,
   wagens.punten,
   buurtschappen.naam,
   buurtschappen.afkorting
   From wagens Inner Join buurtschappen On buurtschappen.id = wagens.`buurtschap-id`
   Where wagens.jaar = " . $params['jaar'] . " AND wagens.punten < 0";
  
   // display de wagens
   $res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
   
   while($row = mysql_fetch_array($res)) 
   {
      // de juiste titel op basis van de huidige taal bepalen
      switch($params["taal"]) {
         case 'nl' : $titel =$row["titel_nl"]; break;
         case 'en' : $titel =$row["titel_en"]; break;    
         case 'du' : $titel =$row["titel_du"]; break;    
         case 'fr' : $titel =$row["titel_fr"]; break;
         default   : $titel =$row["titel_nl"]; break;
      }
    
      // check of deze wagen nog ereprijzen gewonnen heeft
      // een inner join op de oorspronkelijk query werkt niet omdat dat alleen de wagens met ereprijzen
      // teruggeeft en niet alle wagens...dus we zullen het apart op moeten zoeken
      // LET OP: er kunnen meerdere ereprijzen voor 1 wagen zijn.
      $sqlwrk2 = "Select `wagen-ereprijzen`.*
      From `wagen-ereprijzen`
      Where `wagen-ereprijzen`.`wagen-id` = " . $row["id"];
      $res2 = mysql_query($sqlwrk2) or die (mysql_error().$sqlwrk2);
      $ereprijzen = "";
      while($row2 = mysql_fetch_array($res2)) $ereprijzen = $ereprijzen . $row2["ereprijs"] . ", ";
      if ($ereprijzen != "") $ereprijzen = substr($ereprijzen, 0, -2);
    
      //foto_url is only filename as the path is constant so no need to return that for each object
      //the base path will be provided as a separate item in the returned json object
      $foto_wagen = sprintf("%d-%s-W%02d", $params['jaar'], $row["afkorting"], $row["startnummer"]);
      $search = '../../uploads/images/archief/wagens/' . $params['jaar'] . '/' . $foto_wagen . '-' . "*" . '.jpg';
      $thumbs = glob($search);
      $foto_url = basename($thumbs[0]);
    
      $obj["prijs"] = "";
      $obj["ereprijs"] = $ereprijzen;
      $obj["titel"] = $titel;
      $obj["buurtschap"] = $row["naam"];
      $obj["foto"] = $foto_url;
      
      switch($params["taal"]) {
         case 'nl' : $obj["punten"] = "geen"; break;
         case 'en' : $obj["punten"] = "no";  break;    
         case 'du' : $obj["punten"] = "kein";  break;    
         case 'fr' : $obj["punten"] = "aucun";  break;
         default   : $obj["punten"] = "geen";  break;
      }
      
      $ovdata["data"][] = $obj;
    
      //uitslag_item_wagen($params['jaar'], $row["prijs"], $row["id"], $titel, $row["naam"], $row["afkorting"], $row["startnummer"], $row["punten"],$ereprijzen, $row["bijzonderheden"], $taal);
      $i++;   
   }
  
  // en vul de resterende lege plekken
  while ($i<=$ovdata["deelnemers"]) {
      //uitslag_item_onbekend($i);
      $obj["prijs"] = $i;
      $obj["punten"] = "";
      $obj["ereprijs"] = "";
      $obj["titel"] = "";
      $obj["buurtschap"] = "";
      $obj["foto"] = "";
      
      $ovdata["data"][] = $obj;
      $i++;
  }    
}

//now return the json object
echo json_encode($ovdata);

//and finally close the connection
mysql_close($conn);

?>
