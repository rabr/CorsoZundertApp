<?php

/*
 * UDT PARAMETERS
 */
$params['jaar'] = 2015;
$params['test'] = 0;

// wat is de huidige taal?
$taal = "en";  //$smarty->get_template_vars('page_lang');
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
  //haal laatste komma weg  (altijd, bugfix door Niels, bij ��n ontwerper bleef komma staan)
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
  
  
  $foto_maquette2 = substr($foto_maquette,0,-5) . '1.jpg';

  // override zolang de maquette foto's er nog niet zijn
  //$foto_maquette = $base_path_img . '/wagens/unknown.jpg';

  echo '<div class="optochtvolgorde">' . PHP_EOL;
  echo '  <div class="foto">' . PHP_EOL;
  // kijk of er al een foto beschikbaar is van de maquette
  echo '    <img src="' . $foto_maquette . '" alt="" />' . PHP_EOL;
  echo '  </div> <!-- einde foto -->' . PHP_EOL;
  
  /*
  echo '  <div class="heraldiek">' . PHP_EOL;
  echo '    <img src="' . $base_path_img . '/heraldieken/' . $buurtschap_afk . '.gif" alt="" />';
  echo '  </div> <!-- einde heraldiek -->' . PHP_EOL;
  */
  
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
  echo '  <img class="foto" src="' . $foto_maquette2 . '" alt="" />' . PHP_EOL;
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
  if ($prijs_vj!="") { // alleen vermelden er inderdaad vorig jaar een prijs behaald is
    echo '  <p class="prijzen">Vorig jaar behaalde buurtschap ' . $buurtschap . ' met de wagen "' . $titel_vj . '" een ' . $prijs_vj . 'e plaats met ' . $punten_vj . ' punten.</p>' . PHP_EOL;
  }
  echo '</div>';
  
  //echo '<div id="clearfloat0"></div>';
  echo '<div class="spacer"></div>';
  
  
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
  
  
  
  echo '<div class="korps">' . PHP_EOL;
  echo '  <div class="foto">' . PHP_EOL;
  //echo '    <img src="' . $base_path_img . '/wagens/blank.gif" alt=""/>' . PHP_EOL;
  echo '    <img src="' . $base_path_img . '/korpsen/' . $foto . '" alt=""/>' . PHP_EOL;
  echo '  </div> <!-- einde foto -->' . PHP_EOL;
  /*
  echo '  <div class="heraldiek">' . PHP_EOL;
  echo '    <img src="' . $base_path_img . '/heraldieken/korpsen.gif" alt="" />' . PHP_EOL;
  echo '  </div> <!-- einde heraldiek -->' . PHP_EOL;
  */         
  echo '  <div class="text">' . PHP_EOL;
  echo '    <h2>' . $naam . '</h2>' . PHP_EOL;
  echo '    <p>';
  echo        $plaats . ', ' . $land;
  echo '    </p>' . PHP_EOL;
  /*
  echo '    <p>';
  echo        '<a href=' . $link . '> ' . $link . '</a>';
  echo '    </p>' . PHP_EOL;
  */
  echo '  </div> <!-- einde text -->' . PHP_EOL;
  
  echo '</div> <!-- einde item -->' . PHP_EOL;
  
  echo '<div class="korpsinfo">' . PHP_EOL;
  echo '  <img class="foto" src="' . $base_path_img . '/korpsen/' . $foto . '" alt=""/>' . PHP_EOL;
  echo '  <div class="beschrijving">' . PHP_EOL;
  echo '    <p class="omschrijving">' . $omschrijving . '</p>' . PHP_EOL;
  echo '    <p><a href="' . $link . '">' . $link . '</a></p>' . PHP_EOL;
  echo '  </div>' . PHP_EOL;
  echo '  <div id="clearfloat0"></div>';
  echo '</div>';

  
  echo '<div class="spacer"></div>';
  //echo '<div id="clearfloat0"></div>';
}


// ----------------------------------------------------------------------------------------
// Begin van de tag code
// ----------------------------------------------------------------------------------------

$ovdata = array();
$ovdata["jaar"] = $params['jaar'];
$ovdata["taal"] = $taal;
$ovdata["data"] = array();

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

mysql_query('SET CHARACTER SET utf8');

// kijk of alle wagens van dit jaar al een startnummer hebben, 
// zo ja dan kan de volgorde gegeneerd en gepubliceerd worden
$sqlwrk = "Select wagens.jaar, wagens.startnummer
From wagens
Where wagens.jaar = " . $params['jaar'] . " And ISNULL(wagens.startnummer)";

$res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
$num_wagens_zonder_startnr = mysql_num_rows($res);
if ($testmode==1) echo "aantal wagens zonder startnummer: " . $num_wagens_zonder_startnr . "<br>";

/* NIET NODIG MEER OM DEZE CHECK TE DOEN
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
*/
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
  wagens.omschrijving_en,
  wagens.omschrijving_du,
  wagens.omschrijving_fr,
  buurtschappen.naam,
  buurtschappen.afkorting,
  buurtschappen.omschrijving,
  wagens.startnummer
From wagens Inner Join buurtschappen On buurtschappen.id = wagens.`buurtschap-id`
Where wagens.jaar = " . $params['jaar'] . "
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
    $ovdata["data"][] = volgorde_item_korps($roww["naam"], $roww["foto"], $roww["plaats"], $roww["land"], $roww["link"], $roww["omschrijving"]);
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
  
  // haalde de prijs en punten van vorig jaar op
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
  
/* OUD!!!!
  // als foto_maquette nog leeg is dan willen we niet dat er alleen het pad komt staan (voor file_exist)
  if ($row["foto_maquette"] != "") 
  {
// Niels: fotonaam genereren via jaar-buurtcode-M01.jpg ipv uit database halen?
    $foto_maq_volledig = 'uploads/images/archief/' . $params['jaar'] . '/' . $row["foto_maquette"];
  }
  else
  {
    $foto_maq_volledig = '';
  }
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
    case 'nl' : $titel =$row["titel_nl"]; $omschrijving =$row["omschrijving_nl"]; break;
    case 'en' : $titel =$row["titel_en"]; $omschrijving =$row["omschrijving_en"]; break;    
    case 'du' : $titel =$row["titel_du"]; $omschrijving =$row["omschrijving_du"]; break;    
    case 'fr' : $titel =$row["titel_fr"]; $omschrijving =$row["omschrijving_fr"]; break;
    default: $titel =$row["titel_nl"]; $omschrijving =$row["omschrijving_nl"]; break;
  }
  
  $ovdata["data"][] = volgorde_item_wagen($params['jaar'],$foto_maq_volledig, $row["startnummer"], $titel, $row["naam"], $row["afkorting"], $row["omschrijving"], $ontw_ar, $taal, $omschrijving, $rowww["titel_nl"], $rowww["punten"], $rowww["prijs"]);
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


echo json_encode($ovdata);

//einde van de div optochtvolgorde
//echo '</div>' . PHP_EOL;

// nu nog de fotografen van de maquette foto's bedanken....
$sqlwrk = "Select fotografen_volgorde_bekendmaking 
From jaarinfo 
Where jaar = " . $params['jaar'];

$res = mysql_query($sqlwrk) or die (mysql_error().$sqlwrk);
$row = mysql_fetch_array($res);

if ($row["fotografen_volgorde_bekendmaking"] != "") echo "<p>Met dank aan " . utf8_encode($row["fotografen_volgorde_bekendmaking"]) . " voor de foto's.</p>" . PHP_EOL;
/*}*/
}

?>
