<?php

/*
 * UDT PARAMETERS
 */
$params['jaar'] = 2013;

// wat is de huidige taal?
$taal = "nederlands";  //$smarty->get_template_vars('page_lang');
$base_path_img = "img";


/*
 * FUNCTIONS
 */

function list_item_wagen($jaar,$foto_maquette, $nummer, $titel, $buurtschap, $buurtschap_afk, $buurtschap_om, $ontw_array, $taal, $omschrijving, $titel_vj, $punten_vj, $prijs_vj)
{
  global $base_path_img;  
  
  // vertalingen
  $buurtschap_lang = array("nederlands" => "Buurtschap",
                                            "engels" => "Hamlet",
                                            "duits" => "Nachbarschaftsverein", 
                                            "frans" => "Hameau");

  $ontwerpers_lang = array("nederlands" => "Ontwerpers",
                                            "engels" => "Designers",
                                            "duits" => "Designers", 
                                            "frans" => "Designers");
  $en_lang = array("nederlands" => "en",
                               "engels" => "and",
                               "duits" => "und", 
                               "frans" => "et");
  
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

   echo '<li>' . PHP_EOL;
   echo '   <div class="wagen">' . PHP_EOL;
   echo '      <p class="titel">' . $nummer . '. ' . $titel . '</p>' . PHP_EOL;
   echo '      <p class="buurtschap">' . $buurtschap . '</p>' . PHP_EOL;
   echo '   </div>' . PHP_EOL;
   echo '</li>' .PHP_EOL;

/*
  echo '<div class="optochtvolgorde">' . PHP_EOL;
  echo '  <div class="foto">' . PHP_EOL;
  // kijk of er al een foto beschikbaar is van de maquette
  echo '    <img src="' . $foto_maquette . '" alt="" />' . PHP_EOL;
  echo '  </div> <!-- einde foto -->' . PHP_EOL;
  
  echo '  <div class="text">' . PHP_EOL;
  echo '    <h2>';
  // als de start volgorde nog niet bekend is of startnummer 0 (buiten mededinging), 
  // laat dan het nummer niet zien
  if ($nummer != "" && $nummer > 0) echo $nummer . '. ';  
  echo $titel . '</h2>' . PHP_EOL;
  echo '    <p>';
  echo       '<i>' . $buurtschap_lang[$taal] . ': </i>';
  echo        $buurtschap;  
  echo       '<br>' . PHP_EOL;
  echo       '<i>' . $ontwerpers_lang[$taal] . ': </i>';
  echo        $namen_ontwerpers;
  echo       '<br>' . PHP_EOL;
  echo      '</p>' . PHP_EOL;
  echo '  </div> <!-- einde text -->' . PHP_EOL;
          
  echo '</div> <!-- einde item -->' . PHP_EOL;
  
  echo '<div class="wageninfo">' . PHP_EOL;
  echo '  <img class="foto" src="' . $foto_maquette . '" alt="" />' . PHP_EOL;
  echo '  <div class="beschrijving">' . PHP_EOL;
  echo '    <p class="tekstwagen">' . $omschrijving . '</p>' . PHP_EOL;
  echo '    <div class="heraldiek">' . PHP_EOL;
  echo '      <img src="' . $base_path_img . '/heraldieken/' . $buurtschap_afk . '.gif" alt="" />';
  echo '    </div>' . PHP_EOL;
  echo '    <div class="buurtschap">' . PHP_EOL;
  echo '      <p class="titel">Buurtschap ' . $buurtschap . '</p>' . PHP_EOL;
  echo '      <p class="tekst">' . $buurtschap_om . '</p>' . PHP_EOL;
  echo '    </div>' . PHP_EOL;
  echo '  </div>' . PHP_EOL;
  echo '  <div id="clearfloat0"></div>';
  echo '  <p class="prijzen">Vorig jaar behaalde buurtschap ' . $buurtschap . ' met de wagen "' . $titel_vj . '" een ' . $prijs_vj . 'e plaats met ' . $punten_vj . ' punten.</p>' . PHP_EOL;
  echo '</div>';
  
  //echo '<div id="clearfloat0"></div>';
  echo '<div class="spacer"></div>';
*/  
  
}

// ----------------------------------------------------------------------------------------
// Begin van de tag code
// ----------------------------------------------------------------------------------------

$testmode = $params['test'];

// eerst kijken of de parameter 'jaar' opgegeven is en klopt
if ( !is_int($params['jaar']) || $params['jaar']<1936) 
{
  echo '<h2>Error: geen geldig jaar opgegeven</h2><br>';
}
else
{

if ($testmode==1) echo '<b><i>optocht_volgorde test mode</i></b><br><br>';



if ($testmode==1) {
  echo "Jaar: " . $params['jaar'] . "<br>";
  echo "Taal: " . $taal . "<br><br>";
}

// eerst verbinden met de database
$conn = mysql_connect('localhost', "bloemencor_db", 'Bl0emen!');
      mysql_select_db ('bloemencor_db', $conn); 

// kijk of alle wagens van dit jaar al een startnummer hebben, 
// zo ja dan kan de volgorde gegeneerd en gepubliceerd worden
$sqlwrk = "Select wagens.jaar, wagens.startnummer
From wagens
Where wagens.jaar = " . $params['jaar'] . " And ISNULL(wagens.startnummer)";

$res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
$num_wagens_zonder_startnr = mysql_num_rows($res);
if ($testmode==1) echo "aantal wagens zonder startnummer: " . $num_wagens_zonder_startnr . "<br>";

if ($num_wagens_zonder_startnr>0 && $testmode!=1) {
  // de volgorde kan nog niet bekend gemaakt worden

  // haal de datum op waarop de volgorde bekend gemaakt worden
  $sqlwrk = "Select datum_volgorde_bekendmaking 
  From jaarinfo 
  Where jaar = " . $params['jaar'];

  $res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
  $row = mysql_fetch_array($res);

  if ($row["datum_volgorde_bekendmaking"] == "") die("Error: geen datum_volgorde_bekendmaking bekend voor jaar " . $params['jaar']);

  echo "De volgorde van de corso-optocht van " . $params['jaar'] . " wordt bekend gemaakt tijdens de jaarlijkse maquettepresentatie op " . $row["datum_volgorde_bekendmaking"] . " in het CultuurCentrum Zundert. Daarna is de optochtvolgorde hier online te vinden.";
}
else 
{
// ok, we kunnen een volgorde tonen
// start met het creeren van een div voor de optocht volgorde
//echo '<div id="optochtvolgorde">' . PHP_EOL;

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
  buurtschappen.naam,
  buurtschappen.afkorting,
  buurtschappen.omschrijving,
  wagens.startnummer
From wagens Inner Join buurtschappen On buurtschappen.id = wagens.`buurtschap-id`
Where wagens.jaar = " . $params['jaar'] . "
Order By wagens.startnummer";


$res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
while($row = mysql_fetch_array($res)) 
{

  // haalde de prijs en punten van vorig jaar op
  /*
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
  */
  
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
  switch($taal) {
    case 'nederlands' : $titel =$row["titel_nl"]; break;
    case 'engels' :        $titel =$row["titel_en"]; break;    
    case 'duits' :           $titel =$row["titel_du"]; break;    
    case 'frans' :           $titel =$row["titel_fr"]; break;
    default:                   $titel =$row["titel_nl"]; break;
  }

  list_item_wagen($params['jaar'],$foto_maq_volledig, $row["startnummer"], $titel, $row["naam"], $row["afkorting"], $row["omschrijving"], $ontw_ar, $taal, $row["omschrijving_nl"], $rowww["titel_nl"], $rowww["punten"], $rowww["prijs"]);
}

//einde van de div optochtvolgorde
//echo '</div>' . PHP_EOL;

}
}

?>
