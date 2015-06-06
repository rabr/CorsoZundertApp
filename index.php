<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=5.0">
<meta name="apple-mobile-web-app-capable" content="yes" />
  <title>Corso Zundert</title>
  
  <link rel="stylesheet" href="css/style.css">
  
  <script type="text/javascript" charset="utf-8" src="phonegap.js"></script>
  <script src="js/jquery-1.8.2.min.js"></script>
  <!--
  <script src="js/jquery-ui-1.10.4.custom.min.js"></script>
  <script src="js/jquery.mobile.custom.min.js"></script>
  <script src="js/jquery.ui.touch-punch.js"></script>
  -->
  <script src="js/jquery.plugin.pullToRefresh.modified.js"></script>
  <script src="js/jquery.easing.min.js"></script>
  <script src="js/jquery.paging.js"></script>
  <script src="js/overthrow.js"></script>
  <script src="js/app.js"></script>
    
</head>
<body>


<header>
    <h1>CORSO ZUNDERT</h1>
    <h2 id="titel">programma en tijden</h2>
</header>

<div id="info-button"></div>
<div id="zoom-button"></div>
<div id="refresh-button"></div>
<div id="app-info">
<p><b>INFO</b><br>Deze app wordt uitgebracht door <b>Stichting Bloemencorso Zundert</b>. Mail je feedback naar 
  <a href="mailto:app@corsozundert.nl">app@corsozundert.nl</a><br>
  Versie: 2.2.1</p>
  <p>
  WEB | <a href="http://www.corsozundert.nl" rel="external" target="_new">CORSOZUNDERT.NL</a><br>
  FACEBOOK | <a href="http://www.facebook.com/corsozundert" rel="external" target="_new">FACEBOOK.COM/CORSOZUNDERT</a><br>
  TWITTER | #CORSOZUNDERT<br>
  INSTAGRAM | #CORSOZUNDERT
  </p>
  <p><b>HELP</b><br>
  Swipe naar links en rechts om tussen sub-pagina's te wisselen.
  Klik op een balk bij <i>optocht</i> om hem uit te klappen voor meer informatie. Gebruik de zoom button (+/-) om
  de <i>plattegrond</i> in of uit te zoomen. Voor <i>uitslag & nieuws</i> is een internet verbinding nodig. Je kunt deze 
  pagina's verversen door ze naar beneden te slepen bij iOS en bij Android door te klikken op de refresh button.
  </p>
  
</div>

<div id="overlay"></div>

<div id="canvas" > 

<img id="locaties" src="img/timetable/timetable-locaties.png">

<div id="page1" class="overthrow snap">
    <div id="main-content">
        <div class="sections">
            
            <div id="wat">   
               
                  <img id="tijden" src="img/timetable/timetable-tijden.png">

               <!--
               <img id="bgimage" src="img/layout/programma_bg.jpg">
               <div id="prog">
               <h2 class="progitem">ZATERDAG 11 JANUARI 2014 | VAANDELFEEST</h2>
               <div class="proginfo">
               	<p>Zaterdag 11 januari viert buurtschap Laer-Akkermolen nog eenmaal haar overwinning in het corso van 2013. En dat
               		doet zij uiteraard geheel in stijl en met de hele corsogemeenschap. Traditiegetrouw wordt de winnaar van het corso
               		niet alleen gehuldigd met een wisselbeker, maar ook met een blijvende herinnering in de vorm van een ere-vaandel.<br> 
         				Deze vaandeluitreiking is een feestje voor alle corsobouwers- en liefhebbers.
               		De buurtschap zal rond 19:30 bij het CultuurCentrum Zundert arriveren om het vaandel op spectaculaire wijze te onthullen.
               		De dresscode voor die avond is Goud&Fout!</p>
               </div>
               <div class="spacer"></div>
               <h2 class="progitem">ZATERDAG 21 JUNI 2014 | VOLGORDE BEKENDMAKING</h2>
               <div class="proginfo">
   					<p>Het weekend van de Aardbeienfeesten in Zundert staat altijd bol van de corso-activiteiten. Het gehele weekend
                     is er een tentoonstelling in het CultuurCentrum Zundert van de twintig maquettes van het komende corso. 
                     Zaterdag zijn de maquettes te bekijken van 14:00 tot 23:00 uur.
                     Op zaterdagavond wordt om 20:00 de stoetvolgorde bekendgemaakt en de aftrap van het bouwseizoen gegeven.<p>            	 
               </div>
               <div class="spacer"></div>
               
               <h2 class="progitem">ZONDAG 22 JUNI 2014 | MAQUETTE PRESENTATIE</h2>	
               <div class="proginfo">
               	<p>Op zondag tijdens de Aardbeienfeesten kunt u in het CultuurCentrum Zundert van 10:00 tot 19:00 uur de twintig
               	   maquettes van het komende corso bewonderen en speculeren over hoe de uitslag in 2014 er uit zal gaan zien.<p>   
               </div>
               <div class="spacer"></div>
               <h2 class="progitem">ZONDAG 7 SEPTEMBER 2014 | OPTOCHT</h2>
               <div class="proginfo">
                 <p>De corsostoet op zondag vormt het hoogtepunt van het bloemencorso. Maar daarnaast is er in Zundert nog veel
                    meer te beleven. Dweilbandjes, levende standbeelden en diverse jaarmarkten maken van het corso een afwisselend
                    dagje uit. Ook kunt u na afloop alle wagens nog eens rustig bekijken op het tentoonstellingsterrein. Of breng
                    een bezoekje aan de Zonnewende; een feestelijk plein met een ruim terras en optredens van diverse bands.</p>
                 <h3>BELANGRIJKE TIJDEN</h3>
                 <table>
                    <tbody>
                    <tr><td class="tijd">09.00</td><td class="programma">Jaarmarkt Eikenlaan/Beukenlaan en sfeerpleinen vol vertier</td></tr>
                    <tr><td class="tijd">13.30</td><td class="programma">Aanvang optocht</td></tr>
                    <tr><td class="tijd">14:00</td><td class="programma">Eerste doorkomst op de Markt.</td></tr>
                    <tr><td class="tijd">16.30</td><td class="programma">Tweede doorkomst op de Markt met 1 wagen die mag stoppen en de 'Zundertse jubel' mag laten zien</td></tr>
                    <tr><td class="tijd">18.00</td><td class="programma">Tentoonstellingsterrein geopend tot 23:00 met verlichte wagens.</td></tr>
                    </tbody>
                 </table>
               </div>
               <div class="spacer"></div>
               <h2 class="progitem">ZONDAG 1 SEPTEMBER 2014 | FEESTPROGRAMMA</h2>
               <div class="proginfo">
                 <table>
                    <tbody>
                    <tr><td class="lokatie" colspan="2">FEESTHAL TENTOONSTELLINGSTERREIN</td></tr>
                    <tr><td class="tijd2">18.00-20.30u</td><td class="programma">DJ Jozke</td></tr>
                    <tr><td class="tijd2">20.30-21.00u</td><td class="programma">DJ Twannie</td></tr>
                    <tr><td class="tijd2">21.00-22.10u</td><td class="programma">BitsNBeats</td></tr>
                    <tr><td class="tijd2">22.10-22.40u</td><td class="programma">DJ Twannie</td></tr>
                    <tr><td class="tijd2">22.40-00.00u</td><td class="programma">BitsNBeats</td></tr>
                    <tr><td class="tijd2">00.00-01.00u</td><td class="programma">DJ Twannie</td></tr>
                    <tr><td class="lokatie" colspan="2">ZONNEWENDE</td></tr>
                    <tr><td class="tijd2">14.00-19.00u</td><td class="programma">Disco 4 You</td></tr>
                    <tr><td class="tijd2">16:00-18:00u</td><td class="programma">Prijsuitreiking op groot scherm!</td></tr>
                    <tr><td class="tijd2">19:00-20:15u</td><td class="programma">Van Brenghe</td></tr>
                    <tr><td class="tijd2">20:15-21:00u</td><td class="programma">Disco 4 You</td></tr>
                    <tr><td class="tijd2">21.00-22.30u</td><td class="programma">Cookies and Cream</td></tr>
                    <tr><td class="tijd2">22.30-23.00u</td><td class="programma">Disco 4 You</td></tr>
                    <tr><td class="tijd2">23.00-23.30u</td><td class="programma">De Alpenzusjes</td></tr>
                    <tr><td class="tijd2">23.30-01.00u</td><td class="programma">Disco 4 You</td></tr>
                    <tr><td class="lokatie" colspan="2">CAFE DEN BELS</td></tr>
                    <tr><td class="tijd2">19.00u</td><td class="programma">Once Upon a Time</td></tr>
                    <tr><td class="tijd2">23:00u</td><td class="programma">Canvas</td></tr>
                    <tr><td class="lokatie" colspan="2">CAFE/ZAAL VICTORIA</td></tr>
                    <tr><td class="tijd2"> </td><td class="programma">"Action Music" drive-in show</td></tr>
                    <tr><td class="lokatie" colspan="2">EETCAFE DEN SOETE INVAL</td></tr>
                    <tr><td class="tijd2"> </td><td class="programma">Double D Drive in Show</td></tr>
                    <tr><td class="tijd2">na de optocht</td><td class="programma">Roger van Meer</td></tr>
                    <tr><td class="lokatie" colspan="2">CAFE DE OSSEKOP</td></tr>
                    <tr><td class="tijd2">vanaf 12.00u</td><td class="programma">Disco Frans van Gorp</td></tr>
                    <tr><td class="lokatie" colspan="2">NASSAUPLEIN</td></tr>
                    <tr><td class="tijd2">14:00-16:00u</td><td class="programma">DJ Frans</td></tr>
                    <tr><td class="tijd2">16:00-18:00u</td><td class="programma">Prijsuitreiking op groot scherm!</td></tr>
                    <tr><td class="tijd2">16:30-18:00u</td><td class="programma">Kompact</td></tr>
                    <tr><td class="tijd2">17:30-18:30u</td><td class="programma">DJ Frans</td></tr>
                    <tr><td class="tijd2">18:30-20:00u</td><td class="programma">Kompact</td></tr>
                    <tr><td class="tijd2">20:00-21:00u</td><td class="programma">DJ Frans</td></tr>
                    </tbody>
                 </table>
               </div>
               <div class="spacer"></div>
               <h2 class="progitem">MAANDAG 8 SEPTEMBER 2014 | TENTOONSTELLING</h2>
               <div class="proginfo">
                 <table>
                    <tbody>
                    <tr><td class="tijd">9.00</td><td class="programma">Tentoonstellingsterrein geopend, met gratis rondleidingen door corso-gidsen.</td></tr>
                    <tr><td class="tijd">11.00</td><td class="programma">Eerste presentatieronde waarin alle wagens zich tonen in vol ornaat met geluid, figuratie en beweging, tot 13.00 uur. Ook start een doorlopend muziekprogramma van tientallen artiesten op de verschillende podia in Zundert.</td></tr>
                    <tr><td class="tijd">12.00</td><td class="programma">Aanvang jaarmarkt in de Molenstraat.</td></tr>
                    <tr><td class="tijd">14.30</td><td class="programma">Tweede presentatieronde, tot 16.30 uur.</td></tr>
                    <tr><td class="tijd">18:30</td><td class="programma">Derde en laatste presentatieronde tot 20.30 uur, waarna de corsobouwers feestelijk afscheid nemen van de wagens.</td></tr>
                    </tbody>
                 </table>            
               </div>
               <div class="spacer"></div>
               <h2 class="progitem">MAANDAG 2 SEPTEMBER 2013 | FEESTPROGRAMMA</h2>
               <div class="proginfo">
                 <table>
                    <tbody>
                    <tr><td class="lokatie" colspan="2">FEESTHAL TENTOONSTELLINGSTERREIN</td></tr>
                    <tr><td class="tijd2">19.30-20.30u</td><td class="programma">DJ Jozke</td></tr>
                    <tr><td class="tijd2">20.30-22.45u</td><td class="programma">Live Request</td></tr>
                    <tr><td class="tijd2">22.45-23.15u</td><td class="programma">Johnny Gold</td></tr>
                    <tr><td class="tijd2">23.15-23.30u</td><td class="programma">Live Request</td></tr>
                    <tr><td class="tijd2">23.30-23.45u</td><td class="programma">DJ Stoffel</td></tr>
                    <tr><td class="tijd2">23.45-00.30u</td><td class="programma">Live Request</td></tr>
                    <tr><td class="lokatie" colspan="2">ZONNEWENDE</td></tr>
                    <tr><td class="tijd2">omlijsting</td><td class="programma">Disco 4 You</td></tr>
                    <tr><td class="tijd2">(14.30-18.30)</td><td class="programma">ZUNDERTSE MIDDAG:</td></tr>
                    <tr><td class="tijd2">14.30-15.15u</td><td class="programma">Unanime</td></tr>
                    <tr><td class="tijd2">15.15-16.00u</td><td class="programma">Strawberry Sailor Singers</td></tr>
                    <tr><td class="tijd2">16.00-17.00u</td><td class="programma">BigBand BuZz</td></tr>
                    <tr><td class="tijd2">17.15-17.45u</td><td class="programma">Randy Watzeels</td></tr>
                    <tr><td class="tijd2">18.00-18.30u</td><td class="programma">Jolanda Zoomer</td></tr>
                    <tr><td class="tijd2">20.00-21.30u</td><td class="programma">De Rustige 5</td></tr>
                    <tr><td class="tijd2">21.30-22.00u</td><td class="programma">Corsong top 3<br>Uitreiking 'Rode Lantaarn'<br>Uitreiking 'Ostaayen-troffee'<br>Shirtwissel voorzitters</td></tr>
                    <tr><td class="tijd2">22.00-22.30u</td><td class="programma">Jeroen van Zelst</td></tr>
                    <tr><td class="tijd2">22.30-00.30u</td><td class="programma">Drievoud</td></tr>
                    <tr><td class="tijd2">00:30-01:00u</td><td class="programma">Centrale Afsluiting Corso 2013!</td></tr>
                    <tr><td class="lokatie" colspan="2">CAFE DEN BELS</td></tr>
                    <tr><td class="tijd2">08:00-11:00u</td><td class="programma">PROJECT HANGOVER mmv DJ ZOLAV</td></tr>
                    <tr><td class="tijd2">17.00u</td><td class="programma">DJ ZOLAV</td></tr>
                    <tr><td class="tijd2">20.00u</td><td class="programma">Dr Dani&euml;ls</td></tr>
                    <tr><td class="tijd2">22.00u</td><td class="programma">Band Marginal<br>Fragment</td></tr>
                    <tr><td class="lokatie" colspan="2">CAFE/ZAAL VICTORIA</td></tr>
                    <tr><td class="tijd2"> </td><td class="programma">"Action Music" drive-in show</td></tr>
                    <tr><td class="lokatie" colspan="2">EETCAFE DEN SOETE INVAL</td></tr>
                    <tr><td class="tijd2">14.00-01.00u</td><td class="programma">Optredens van o.a. Johnny Gold, Bobby Prins, Anita, Jeroen Spierenburg, Roger van Meer, Yoeri L'Abee, Johan Linssen, Collin, Randy Watzeels, Tommy Lips, Karin Welsing, Marco den Hollander, Corsong Winnaars 2013</td></tr>
                    <tr><td class="lokatie" colspan="2">CAFE DE OSSEKOP</td></tr>
                    <tr><td class="tijd2">10:00-11:30u</td><td class="programma">Ontbijt</td></tr>
                    <tr><td class="tijd2">na 11:30</td><td class="programma">Disco Frans van Gorp</td></tr>
                    </tbody>
                 </table>
               </div>
               <div class="spacer"></div>
               
               <h2 class="progitem">DE CORSOMUNT</h2>
               <div class="proginfo">
               <p>
               Vanaf dit jaar is er 1 gezamelijke corsomunt waarmee betaalt kan worden bij veel horeca
               gelegenheden tijdens het corso. De verkoopprijs van de corsomunt bedraagt &euro;2,-. Het muntsysteem kent twee 
               soorten munten: breekmunten van 1 consumptie, welke gebroken de helft van zijn waarde heeft en munten van 10 
               consumpties. De munten zijn geldig van zondag 1 september 2013 tot en met kindercorso, zondag 15 september 2013.
               De deelnemers en verkooppunten zijn te herkennen aan de volgende posters:
               </p>
               <img class="poster" src="img/poster_corsomunt_accept.jpg"> 
               <img class="poster" src="img/poster_corsomunt_verkoop.jpg">
               <div id="clearfloat0"></div>
               <p>
               Bij de volgende ondernemers kunt u terecht met de corsomunt:
               Auberge van Gogh<br>
               Bij de Nonnen<br>
               Cafe de Kèrel<br>
               Cafe de Ossekop<br>
               Cafe den Bels<br>
               Cafe Neefs<br>
               Cafe van T<br>
               Cafe zaal Victoria<br>
               Cafe-Zaal het wapen van Zundert<br>
               CultuurCentrum Zundert<br>
               Den Soete Inval<br>
               Eetcafe Den Hoek<br>
               Eethuis Jochems<br>
               Hotel Eetcafe de Roskam<br>
               Il Bambino<br>
               In de Wandelgangen<br>
               Koebrugge evenement catering<br>
               Restaurant de Wissel<br>
               Shoarma Grillroom Taba<br>
               Stichting Bloemencorso Zundert<br>
               Tennishal de Leeuwerik<br>
               </p>
               </div>
               <div class="spacer"></div>
               </div>
               -->
            </div>
            
            <div id="wie">
              <?php include 'php/optocht_volgorde.php'; ?>
            </div>
    
            <div id="waar">
               <!--<p id="testot">Overthrow support: </p>-->
               <img id="plattegrond" src="img/plattegrond_2014.jpg"></img>
            </div>

            <div id="live">
                  <!--<button type="button" onclick="refreshUitslag()">Uitslag verversen</button>-->
                  <div id="livexmldoc"></div>  
            </div>
            
            <div id="nieuws">
                  <div id="livexmlnieuws"></div>
            </div>
            
        </div>
    </div>
</div>


<div id="page2" class="overthrow snap"> 
   <div class="sections">
      <div id="feestprogramma">
      </div>
      <div id="vorigjaar">
         <?php include 'php/uitslag_vorig.php'; ?>
      </div>
      <!--
      <div id="voorspelling">
         <div id="ranking">
            <p class="prijs">1</p><p class="prijs">2</p><p class="prijs">3</p><p class="prijs">4</p><p class="prijs">5</p>
            <p class="prijs">6</p><p class="prijs">7</p><p class="prijs">8</p><p class="prijs">9</p><p class="prijs">10</p>
            <p class="prijs">11</p><p class="prijs">12</p><p class="prijs">13</p><p class="prijs">14</p><p class="prijs">15</p>
            <p class="prijs">16</p><p class="prijs">17</p><p class="prijs">18</p><p class="prijs">19</p><p class="prijs">20</p>
         </div>
         <ul id="sortable">
            <?php include 'php/voorspelling.php'; ?>
         </ul>
      </div>
      -->
      <div id="instagram">
         <div id="instagramxml"></div>
      </div>
   </div>
</div>

<div id="page3" class="overthrow snap">
   <div class="sections">
      <div id="facebook">
         <div id="facebookxml"></div>
      </div>
   </div>
</div>

</div>


<footer>
<div id="indicators"> <div id="box"></div> </div>
<ul id="tab-bar">
    <li>
        <a id="button1" href="#wat">programma</a>
    </li>
    <li>
        <a id="button2" href="#wie">optocht</a>
    </li>
    <li>
        <a id="button3" href="#waar">plattegrond</a>
    </li>
    <li>
        <a id="button4" href="#live">uitslag</a>
    </li>
    <li>
        <a id="button5" href="#nieuws">nieuws</a>
    </li>
</ul>
</footer>


    

<!--
<script type="text/javascript">
  os = document.getElementById("testot");
  os.innerHTML = os.innerHTML + overthrow.support;
</script>
-->

</body>
</html>
