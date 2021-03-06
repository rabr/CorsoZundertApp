<!doctype html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=5.0">
<meta name="apple-mobile-web-app-capable" content="yes" />
  <title>Corso Zundert</title>
  
  <link rel="stylesheet" href="style.css">
  
  <script type="text/javascript" src="cordova.js"></script>
  <script src="js/jquery-1.8.2.min.js"></script>
  <script src="js/jquery.plugin.pullToRefresh.modified.js"></script>
  <script src="js/jquery.easing.min.js"></script>
  <script src="js/jquery.paging.js"></script>
  <script src="js/jquery.tmpl.min.js"></script>
  <script src="js/jquery.facebookWall.js"></script>
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
  Versie: 2.4.0</p>
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

<img id="button-zo" src="img/timetable/button-zo-active.png">
<img id="button-ma" src="img/timetable/button-ma.png">
<img id="locaties" src="img/timetable/timetable-locaties.png">

<div id="page1" class="overthrow snap">
    <div id="main-content">
        <div class="sections">
            
            <div id="wat">              
                  <img id="tijden-zo" src="img/timetable/timetable-tijden-zo.png">
                  <img id="tijden-ma" src="img/timetable/timetable-tijden-ma.png">
                  <!--
                  <p class="big1">Optocht</p> 
                  <p class="big2">Zondag 6 September 2015</p>
                  <p class="big1">Tentoonstelling</p>
                  <p class="big2">Maandag 7 September 2015</p>
                  <p class="big1">Feestprogramma volgt</p>
                  -->
            </div>
            
            <div id="wie">
               <div id="optocht">
               <!--
                  <?php $OVLIVE=0; if (!$OVLIVE) include 'php/optocht_volgorde_embedded.php'; ?>
                  <input type="hidden" id="phpOVLiveVar" value="<?php echo $OVLIVE; ?>">
               -->
               </div>
            </div>
    
            <div id="waar">
               <!--<p id="testot">Overthrow support: </p>-->
               <img id="plattegrond" src="img/plattegrond_2015.png"></img>
            </div>

            <div id="live">
                  <!--<button type="button" onclick="refreshUitslag()">Uitslag verversen</button>-->
                  <div id="livexmldoc"></div>  
            </div>
            
            <div id="facebook">
               <div id="facebookxml"></div>
		      </div>
    
        </div>
    </div>
</div>


<div id="page2" class="overthrow snap"> 
   <div class="sections">
      <div id="feestprogramma">
      </div>
      <div id="vorigjaar">
         <!-- <?php include 'php/uitslag_vorig.php'; ?> -->
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
      <div id="nieuws">
         <div id="livexmlnieuws"></div>
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


<script id="feedTpl" type="text/x-jquery-tmpl">
<li>
	<div class="status">
	   <div class="header">
	     <img class="profile" src="${from.picture}">
		  <h2><a href="http://www.facebook.com/profile.php?id=${from.id}" target="_blank">${from.name}</a></h2>
		  <p class="meta">${created_time}</p>
		</div>
		<p class="message">{{html message}}</p>
		{{if type == "photo"}}
		<img class="picture" src="${pictureurl}" />
		{{/if}}
		{{if type == "link" }}
			<div class="attachment">
				{{if picture}}
					<img src="${picture}" />
				{{/if}}
				<div class="attachment-data">
					<p class="name"><a href="${link}" target="_blank">${name}</a></p>
					<p class="caption">${caption}</p>
					<p class="description">${description}</p>
				</div>
			</div>
		{{/if}}
	</div>
	
</li>
</script>
    

<!--
<script type="text/javascript">
  os = document.getElementById("testot");
  os.innerHTML = os.innerHTML + overthrow.support;
</script>
-->

</body>
</html>
