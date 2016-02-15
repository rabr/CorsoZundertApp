<?php

//echo '<p>Dit komt van de server... ' . time() . ' :-)</p>';

/*
 * UDT PARAMETERS
 */
$params['jaar'] = 2015;

// wat is de huidige taal?
$taal = "nederlands";  //$smarty->get_template_vars('page_lang');
$base_path_img = "img";


function uitslag_item_wagen($jaar, $prijs, $wagenid, $titel, $buurtschap, $buurtschap_afk, $startnummer, $punten, $ereprijzen, $bijzonderheden, $taal)
{
  // $base_path_fotos = 'http://new.bloemencorsozundert.nl/' . 'uploads/images/archief/' . $jaar;
  $base_path_fotos = 'http://www.corsozundert.nl/' .'uploads/images/archief/wagens/' . $jaar;
  
  // vertalingen
  $buurtschap_lang = array("nederlands" => "Buurtschap",
                                            "engels" => "Hamlet",
                                            "duits" => "Nachbarschaftsverein", 
                                            "frans" => "Hameau");

  $punten_lang = array("nederlands" => "Punten",
                                            "engels" => "Points",
                                            "duits" => "Punkte", 
                                            "frans" => "Point");

/*
// NBRA: vervangen door nieuwe code incl. fotograaf:
// NIEUW (zoeken met glob functie naar alle files met JAAR-BS-W##-, met fotovolgnr/fotograaf als wildcard):
  $foto_wagen = sprintf("%d-%s-W%02d", $jaar, $buurtschap_afk, $startnummer);
  $search = $base_path_fotos . '/' . $foto_wagen . '-' . "*" . '.jpg';
  $thumbs = glob($search);
  $foto_url = $thumbs[0];
  if ($foto_url <> "") {$foto_file_exists = 1;}
// EINDE NIEUWE CODE NBRA
*/

  echo '<div class="uitslagregel">' . PHP_EOL;

  echo '  <p class="prijs">' . $prijs . '</p>' . PHP_EOL;
 
  echo '  <div class="prijzen">' . PHP_EOL;
  echo '    <p class="punten">' . $punten . ' punten</p>' . PHP_EOL;
  echo '    <p class="ereprijs">' . $ereprijzen . '</p>' . PHP_EOL;
  echo '  </div> <!-- einde prijzen -->' . PHP_EOL; 
            
  echo '  <div class="wagen">' . PHP_EOL;
  echo '    <p class="titel">' . $titel . '</p>' . PHP_EOL;
  echo '    <p class="buurtschap">Buurtschap ' . $buurtschap . '</p>' . PHP_EOL;
  echo '  </div> <!-- einde wagen -->' . PHP_EOL; 
  
  echo '</div> <!-- einde item -->' . PHP_EOL;
}

function uitslag_item_onbekend($prijs)
{
    echo '<div class="uitslagregel">' . PHP_EOL;
    echo '  <p class="prijs">' . $prijs . '</p>' . PHP_EOL;
    echo '  <p class="onbekend">?</p>' . PHP_EOL;
    echo '</div> <!-- einde item -->' . PHP_EOL;
}


// ----------------------------------------------------------------------------------------
// Begin van de tag code
// ----------------------------------------------------------------------------------------
$en_lang = array("nederlands" => "en",
                               "engels" => "and",
                               "duits" => "und", 
                               "frans" => "et");


// eerst verbinden met de database
$conn = mysql_connect('localhost', "bloemencor_db", 'Bl0emen!');
      mysql_select_db ('bloemencor_db', $conn); 
   
if (1) {      
  echo '<div id="uitslag">';
  //echo '<h1>UITSLAG</h1>';
  
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
      $prijs = $row["prijs"];
      
      if ($i<$prijs) {
          // er zijn nog een aantal lege plekken tussen deze wagen en de huidige regel
          // opvullen met 'onbekende' regels
          while ($i<$prijs) {
              uitslag_item_onbekend($i);
              $i++;
          }
      }
      // Nu deze uitslag regel
                
    // de juiste titel op basis van de huidige taal bepalen
    switch($taal) {
      case 'nederlands' : $titel =$row["titel_nl"]; break;
      case 'engels'     : $titel =$row["titel_en"]; break;    
      case 'duits'      : $titel =$row["titel_du"]; break;    
      case 'frans'      : $titel =$row["titel_fr"]; break;
      default:            $titel =$row["titel_nl"]; break;
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
    
    uitslag_item_wagen($params['jaar'], $row["prijs"], $row["id"], $titel, $row["naam"], $row["afkorting"], $row["startnummer"], $row["punten"],$ereprijzen, $row["bijzonderheden"], $taal);
    $i++;
  }
  
  // en vul de resterende lege plekken
  while ($i<=19) {
      uitslag_item_onbekend($i);
      $i++;
  }
  
  //einde van de div optochtvolgorde
  echo '</div>';
} 

?>
