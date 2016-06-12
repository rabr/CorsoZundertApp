// Wait for PhoneGap to load
//
document.addEventListener("deviceready", onDeviceReady, false);

// Update content when orientation changes
// var supportsOrientationChange = "onorientationchange" in window,
// orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";
// window.addEventListener(orientationEvent, function)
//
window.addEventListener('orientationchange', onOrientationChange);


//
// Define global variable to keep track of the status of affaires
//
var appStatus = {
    // 
    // CONSTANTS
    //
    //supportedLangs : [ 'nl', 'en', 'fr' ],      // supported languages
    //defaultLang : 'en',                         // default language in case phone language is not supported
    supportedLangs : [ 'nl' ],      
    defaultLang : 'nl',                         
    jaar : 2016,                                // the current year
    extContent : {                              // External content
      optocht : {
         id  : 'optocht',
         url : 'http://www.corsozundert.nl/api/?optocht&jaar=2016&taal=nl'
      },
      uitslag : {
         id  : 'livexmldoc',
         url : 'http://www.corsozundert.nl/api/?uitslag&jaar=2015&taal=nl'
      },
      twitter : {
         id  : 'livexmlnieuws',
         url : 'http://www.corsozundert.nl/app/php/twitterdb_live.php'
      },
      facebook : {
         id  : 'facebookxml',
         url : 'http://www.corsozundert.nl/app/php/facebook.html'
      },
      instagram : {
         id  : 'instagramxml',
         url : 'http://www.corsozundert.nl/app/php/instagram.php'
      }
    },
    // Define the sections and pages in the app
    sections : {
      section0 : {
         enabled : true,
         title1  : ''
      },
      section1 : {
         enabled : false,
         title1  : 'programma en tijden',
         page1   : '#wat'
      },  
      section2 : {
         enabled : true,
         title1  : 'deelnemers optocht 2016',
         page1   : '#wie'
      }, 
      section3 : {
         enabled : true,
         title1  : 'plattegrond',
         page1   : '#waar'
      },
      section4 : {
         enabled : true,
         title1  : 'uitslag 2015',
         page1   : '#live' /*,
         title2  : 'uitslag 2014',
         page2   : '#vorigjaar',
         title2  : 'jouw voorspelling 2014',
         page2   : '#voorspelling'*/
      },
      section5 : {
         enabled : true,
         title1  : 'facebook CorsoZundert',
         page1   : '#facebook' /*,
         title2  : 'instagram #corsozundert',
         page2   : '#instagram', 
         title2  : 'tweets #corsozundert',
         page2   : '#nieuws'*/
         
      },
      maxPages  : 3,
      opensWith : 0             // indicates with which section should the App open, 0 means none-> bg image shown
    },
    
    //
    // VARIABLES
    //
    langSetting : 'auto',       // user setting of the language: default 'auto' (same as phone, or 'en' in case not supported), or override (stored in local storage) to supported languages
    lang : null,                // the actual language used
    text : null,                // the json text object containing the texts in the specific language
    ovdata : null,              // the json object containing the optocht volgorde data
    uitslag : null,             // the json object containing the uitslag data
    activeTab : 1,              // the number of the active tab
    enablePTR : 0,              // enable pull-to-refresh (default off)
    plattegrond : {             // plattegrond specific status
        zoomLevel : 0           // 0 = zoomed in, 1 = zoomed out    
    },
    optochtLive : 1,           // have the optocht_volgorde live or fixed include. Note this var is overruled by a php variable transferred from index.php, see below

   //
   // FUNCTIONS
   //

   // Switch pages, fade animation
   page : function(toPages) {
      var fromPage = $(".sections .current");
   
      for (var i=0; i<toPages.length; i++) {
         var toPage = $(toPages[i]);
    
         if(toPage.hasClass("current")) {
            return;
         };
         toPage.addClass("current fade in").one("webkitAnimationEnd", function(){
            fromPage.removeClass("current fade out");
            toPage.removeClass("fade in")
         });
      }
   
      fromPage.addClass("fade out");
   },
   
   // Draw little indicators indicating the active page
   drawPagingIndicators : function () {
      // Figure out the number of active pages
      var pages = jQuery('#canvas').children('.snap').filter(function() {
                     return $(this).css('visibility') == 'visible';
                  });
      var numPages = pages.length;
               
      if (numPages>1) {
         // create the right amount of indicators, highlight the first
         // first delete all child nodes
         jQuery('#box').empty();
         // set the appropriate width
         jQuery('#box').width(numPages*12);
      
         // find out what is the active page by checking the scroll position
         var scrollingEl = jQuery('#canvas');
         var matchingEl = null;

         pages.each(function() {
            var snappingEl = jQuery(this);
                //dist = snappingEl.offset().left - scrollingEl.scrollLeft();

            //if (dist == 0) matchingEl = snappingEl;
            if (snappingEl.offset().left == 0) matchingEl = snappingEl;
         });
      
         if (matchingEl) {
            activePage = matchingEl.attr('id').slice(-1);
            // now set the title ...
            var sec = 'section'+this.activeTab;
            var tit = 'title'+activePage;
            if (this.sections[sec][tit]!='') {
               $('#titel').text(this.sections[sec][tit]);
               $('#titel').css({ 'visibility' : 'visible'});
            } else {
               $('#titel').css({ 'visibility' : 'hidden'});
            }
      
            // ...and add the indicators
            for(i=1; i<=numPages; i++) {
               if (i==activePage) jQuery('#box').append('<img src="img/layout/icon_paging.png">');
               else jQuery('#box').append('<img src="img/layout/icon_paging_gray.png">');
            }
         }

      } else {
         // else no need to draw indicators for a single page
         // just make sure all indicators are removed by deleting all child nodes
         var sec = 'section'+this.activeTab;
         if (this.sections[sec].title1!='') {
            $('#titel').text(this.sections[sec].title1);
            $('#titel').css({ 'visibility' : 'visible'});
         } else {
            $('#titel').css({ 'visibility' : 'hidden'});
         }
         jQuery('#box').empty();
      }  
   },
   
   // set the language based on user choice
   // if forceChoice is true the selection screen is activated otherwise the function just process the language settings in the object
   // the callback will be called when all information has been gathered
   setLanguage : function(forceChoice, callback) {
      var self = this;  //closure for use inside callback function
      
      var langHelper = function(lang,cb) {
         self.lang = lang;
         window.localStorage.setItem("lang", lang);   // store as override in local storage
         if (typeof cb === "function") cb();
      };
      
      var autoHelper = function(cb) {
         window.localStorage.removeItem("lang");     // make sure the override is cleared
         // get preferred language from phone settings
         navigator.globalization.getPreferredLanguage(
            function (language) {
               console.log('language: ' + language.value + '\n'); 
               // check if this language is supported 
               var l = language.value.substring(0,2);
               var i = jQuery.inArray(l,self.supportedLangs);
               
               if (i > -1) self.lang = l;             // found in array so it is supported so use it
               else self.lang = self.defaultLang;     // not found so use the default language in case phone lang not supported
               
               if (typeof cb === "function") cb();
            },
            function () {alert('Error getting language\n');}
         );
      };
      
      if (forceChoice) {
         // user wants to choose again, so let the user choose...and store as override in local storage in case not auto
         // make sure to only bind once, otherwise strange things happen
         jQuery("#flag-nl").off('click').on('click', function() { langHelper("nl", callback); });
         jQuery("#flag-en").off('click').on('click', function() { langHelper("en", callback); });
         jQuery("#flag-fr").off('click').on('click', function() { langHelper("fr", callback); });
         jQuery("#flag-auto").off('click').on('click', function() { autoHelper(callback); });
         
      } else {
         
         if (this.langSetting == 'auto') autoHelper(callback);
         else {
            // no need to store override since we are running this from the (default) object settings
            if (typeof callback === "function") callback();
         }
      }
   },
   
   // set all texts as defined in the language file
   setTexts : function() {
      // first load the language file
      var texturl = 'res/text-' + this.lang + '.json';
      var self = this;  //closure for use inside callback function
      
      console.log('Attempting to load ' + texturl);
      jQuery.getJSON(texturl, function(data) {
         self.text = data;
         console.log('loading text : ' + JSON.stringify(self.text, null, 4));
      }).fail(function(jqXHR, status, error){
         alert('error loading language file: ' + status + ', ' + error);
      }).complete(function() { 
         //alert("complete"); 
         
         // merge all section related text into the section object
         $.extend( true, self.sections, self.text.sections );
         // now set the texts of the different items
         // the 5 buttons
         $('#button1').text(self.sections.section1.button);
         $('#button2').text(self.sections.section2.button);
         $('#button3').text(self.sections.section3.button);
         $('#button4').text(self.sections.section4.button);
         $('#button5').text(self.sections.section5.button);
         
         // update the timetable buttons
         $('#button-zo').text(self.text.buttons.zondag);
         $('#button-ma').text(self.text.buttons.maandag);
         
         // update the titel (of the active page within the tab), easiest to use drawPagingIndicators for that
         self.drawPagingIndicators();
         
         // the app-info
         $('#app-info').html(self.text.info);
         $('#app-info #lang').off('click').on('click', function() {  // make sure to only bind once, otherwise strange things happen
            jQuery("#choose-lang").fadeToggle(400);
            appStatus.setLanguage(true, function() {
               appStatus.setTexts();
               appStatus.readOptochtVolgorde();
               jQuery("#choose-lang").fadeToggle(400);
               jQuery("#info-button").trigger('click');  // close the app-info
            });
         });
      });
      /*
      .success(function() { alert("second success"); })
      .error(function() { alert("error"); })
      */
   },
   
   // Load optochtvolgorde, either static from file or live from server
   readOptochtVolgorde: function() {
      var ovurl = null;
      var fileurl = 'res/optochtvolgorde-' + this.lang +'.json';
      var apiurl = 'http://www.corsozundert.nl/api/?optocht&jaar=' + this.jaar + '&taal=' + this.lang;
      var imgPath = 'img';   // !!! MAKE PATH DEPENDENT ON LIVE OR STATIC, NOW ONLY STATIC -> MAYBE OK AS DOES SAVE BANDWIDTH AND IMAGES DON'T CHANGE USUALLY
      var self = this; //closure for use inside callback function
      
      if (this.optochtLive) ovurl = apiurl;
      else ovurl = fileurl;
      
      console.log('Attempting to load ' + ovurl);
      jQuery.getJSON(ovurl, function(data) {
         self.ovdata = data;
         console.log('loading text : ' + JSON.stringify(self.ovdata, null, 4));
      }).fail(function(jqXHR, status, error){
         alert('error loading optocht file: ' + status + ', ' + error);
      }).complete(function() { 
         var debug = "";
      
         // clear all existing content from optocht div
         jQuery("#optocht").empty();
         
         // loop through all data items
         for (var key in self.ovdata["data"]) {
            var item = self.ovdata["data"][key];
            
            if (item["type"] == "wagen") {
               var w = item["wagen"];
               var b = item["buurtschap"];
               var v = b["vorigjaar"];
               var foto_maquette1 = imgPath + '/wagens/' + self.ovdata["jaar"] + '-' + b["afkorting"] + '-M00.jpg';
               var foto_maquette2 = imgPath + '/wagens/' + self.ovdata["jaar"] + '-' + b["afkorting"] + '-M01.jpg';
               var foto_heraldiek = imgPath + '/heraldieken/' + b["afkorting"] + '.gif';
               var ly = self.text.sentences["lastyear"];
               var lastyear = ly["part1"] + ' ' + b["naam"] + ' ' + ly["part2"] + ' "' + v["titel"] + '" ' + ly["part3"] + ' ' + v["prijs"] + ly["part4"] + ' ' + v["punten"] + ' ' + ly["part5"];
               var titel;
               
               if (w["startnummer"]!=null) titel = w["startnummer"] + '. ' + w["titel"];
               else titel = w["titel"];
               
               var html = $('<div class="optochtvolgorde">')
                  .append($('<div class="foto">').append('<img src="' + foto_maquette1 + '" alt="" />'))
                  .append($('<div class="text">') 
                     .append($('<h2>').append(titel))
                     .append($('<p>').append('<i>' + self.text.words["buurtschap"] + ': </i>' + b["naam"] + '<br>' + '<i>' + self.text.words["ontwerpers"] + ': </i>' + w["ontwerpers"] + '<br>'))
                  );
               
               $('#optocht').append(html);
               
               html = $('<div class="wageninfo">')
                  .append($('<div class="foto">').append('<img src="' + foto_maquette1 + '" alt="" />'))
                  /*.append($('<div class="foto">').append('<img src="' + foto_maquette2 + '" alt="" />'))*/
                  .append($('<div class="beschrijving">')
                     .append($('<p class="tekstwagen">').append(w["omschrijving"]))
                     .append($('<div class="heraldiek">').append('<img src="' + foto_heraldiek + '" alt="" />'))
                     .append($('<div class="buurtschap">')
                        .append('<p class="titel">Buurtschap ' + b["naam"] + '</p>')
                        .append('<p class="tekst">' + b["omschrijving"] + '</p>')
                     )
                  )
                  .append($('<div id="clearfloat0"></div>'));
                  
               if (v["titel"]!=null) html.append('<p class="prijzen">' + lastyear + '</p>');
                  //.append('<p class="prijzen">Vorig jaar behaalde buurtschap ' + b["naam"] + ' met de wagen "' + v["titel"] + '" een ' + v["prijs"] + 'e plaats met ' + v["punten"] + ' punten.</p>');
               // !!! MAKE TRANSLATION OF ABOVE
               
               $('#optocht').append(html);   
               $('#optocht').append($('<div class="spacer">'));
               
               
               
            } else if (item["type"] == "korps") {
               var foto_korps = imgPath + '/korpsen/' + item["foto"];
               
               var html = $('<div class="korps">')
                  .append($('<div class="foto">').append('<img src="' + foto_korps + '" alt="" />'))
                  .append($('<div class="text">') 
                     .append($('<h2>').append(item["naam"]))
                     .append($('<p>').append(item["plaats"] + ', ' + item["land"]))
                  );
               
               $('#optocht').append(html);
               
               html = $('<div class="korpsinfo">')
                  .append($('<div class="foto">').append('<img src="' + foto_korps + '" alt="" />'))
                  .append($('<div class="beschrijving">')
                     .append($('<p class="omschrijving">').append(item["omschrijving"]))
                     .append($('<p>').append('<a href="' + item["link"] + '">' + item["link"] + '</a>'))
                  )
                  .append($('<div id="clearfloat0"></div>'));
               
               $('#optocht').append(html);   
               $('#optocht').append($('<div class="spacer">'));
            }
            
            debug = debug + item["type"] + ", ";
         }
         
         // attach on click behavior to the newly added items
         jQuery(".optochtvolgorde").click(function()
         {
            jQuery(this).next(".wageninfo").slideToggle(200);
            if (jQuery(this).css("background-image").search("open") != -1) newBGImg = "url(img/layout/close.png)";
            else newBGImg = "url(img/layout/open.png)";
            jQuery(this).css("background-image",newBGImg);
         });
         jQuery(".korps").click(function()
         {
            jQuery(this).next(".korpsinfo").slideToggle(200);
            if (jQuery(this).css("background-image").search('open') != -1) newBGImg = "url(img/layout/close.png)";
            else newBGImg = "url(img/layout/open.png)";
            jQuery(this).css("background-image",newBGImg);
         });
         
         //alert(debug); 
      });
   
   },
   
   readUitslag : function() {
      var year = 2015; // this.jaar;
      var apiurl = 'http://www.corsozundert.nl/api/?uitslag&jaar=' + year + '&taal=' + this.lang;
      var self = this; //closure for use inside callback function
      var imgPath = 'http://www.corsozundert.nl/uploads/images/archief/wagens/' + year + '/';
      
      console.log('Attempting to load ' + apiurl);
      jQuery.getJSON(apiurl, function(data) {
         self.uitslag = data;
         console.log('loading text : ' + JSON.stringify(self.uitslag, null, 4));
      }).fail(function(jqXHR, status, error){
         alert('error loading uitslag file: ' + status + ', ' + error);
      }).complete(function() { 
         var debug = "";
         
         // clear all existing content from optocht div
         jQuery("#livexmldoc").empty();
         
         // loop through all data items
         for (var key in self.uitslag["data"]) {
            var item = self.uitslag["data"][key];
            
            var html = $('<div class="uitslagregel">')
               .append($('<p class="prijs">').append(item["prijs"]))
               /*
               .append($('<div class="prijzen">')
                  .append($('<p class="punten">').append(item["punten"] + ' ' + self.text.words["punten"]))
                  .append($('<p class="ereprijs">').append(item["ereprijs"]))
               )*/
               .append($('<div class="wagen">')
                  .append($('<img src="' + imgPath + item["foto"] + '">'))
                  .append($('<p class="titel">').append(item["titel"]))
                  .append($('<p class="buurtschap">').append(item["buurtschap"])) // self.text.words["buurtschap"] + ' ' + 
                  .append($('<p class="punten">').append(item["punten"] + ' ' + self.text.words["punten"]))
               );
               
            $('#livexmldoc').append(html);
         }
      });
   },
   
   // Reload external content
   refreshContent : function(content) {
      var xmlhttp;
    
      if (window.XMLHttpRequest) {
         // code for IE7+, Firefox, Chrome, Opera, Safari
         xmlhttp=new XMLHttpRequest();
      } else {
         // code for IE6, IE5
         xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
      
      xmlhttp.divID = content.id;
      xmlhttp.serverUrl = content.url;
      
      xmlhttp.onreadystatechange=function() {
         if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            var divElem = document.getElementById(xmlhttp.divID);
            divElem.innerHTML=xmlhttp.responseText;
            // execute scripts if there are embedded in the page
            var arr = divElem.getElementsByTagName('script');
            for (var n=0; n<arr.length; n++) {
               if (arr[n].innerHTML != "" && arr[n].type!="text/x-jquery-tmpl") eval(arr[n].innerHTML); //run script inside div
               if (arr[n].src != "") jQuery.getScript(arr[n].src); // load and execute the external script
            }
            // open urls in an in-app browser
            var elem = '#' + xmlhttp.divID + ' a';
            jQuery(elem).on('click', function(e){
               e.preventDefault();
               var url = e.currentTarget.href;
               window.open(encodeURI(url), '_blank', 'location=yes');
            });
        }
      }
      xmlhttp.open("GET",xmlhttp.serverUrl,true);
      xmlhttp.send();
   },
   // Update the button icons for all sections that have been disabled
   disableSections : function() {
      for (i=1; i<=5; i++) {
         var s = "section" + i;
         var button = "#button" + i;
         if (!this.sections[s].enabled) $(button).addClass("disable");
      }
   },
   // Switch to another section, 'to' indicates number of the section
   switchSection : function(to) {
      var self = this; //closure for use inside callback function
      //first check whether the section number is valid
      //NOW HARDCODED BY SHOULD BE REPLACED BY AUTOMATIC DETERMINATION OF TOTAL NUMBER OF SECTIONS
      if ( (to>=1) && (to<=5) ) {   
         // check which button was pressed and get the new section to go to 
         //var button = e.currentTarget.id;
         var button = "#button" + to;
         var nextSection = "section" + to;
         
         if (this.sections[nextSection].enabled) {
            // This section is enabled
            this.activeTab = to;
         
            // reset all pages but the first
            for (var p=2; p<=this.sections.maxPages; p++) $("#page"+p).css( { 'visibility' : 'hidden', 'z-index' : '-1', 'left' : '0%' } );
            // get all pages in the section
            var pages=[];
            for(var key in this.sections[nextSection]) {
               if (key.indexOf("page")!=-1) pages.push(this.sections[nextSection][key]);
            }
            // restore all pages but the first
            for (var p=2; p<=pages.length; p++) {
               var l = (p-1)*100 + '%';
               $("#page"+p).css( { 'visibility' : 'visible', 'z-index' : '2', 'left' : l } );
            }
            // fade in the new pages, fade out the old
            this.page(pages);

            // make the pressed button the new current button
            $("#tab-bar .current").removeClass("current");
            $(button).addClass("current");
    
            // reset the scroll position
            $("#canvas").scrollLeft(0);  // horizontal scrolling goes via canvas
            for (p=1; p<=this.sections.maxPages; p++) $("#page"+p).scrollTop(0); //vertical scrolling goes via the pages
            // draw the paging indicators + title
            this.drawPagingIndicators();
    
    
            // disable the refresh and zoom button by default
            $('#refresh-button').css('visibility', 'hidden');
            $('#zoom-button').css('visibility', 'hidden');
    
            if (to == 1) this.initTimeTable();
            else this.resetTimeTable();
    
            if (this.optochtLive) {
               if (to == 2) {
                  if (this.enablePTR) $('#page1').trigger('refresh');
                  else {
                     // enable the refresh button 
                     $('#refresh-button').css('visibility', 'visible');
                     // attach AJAX function
                     $('#refresh-button').on('click', function(){ 
                        $('#optocht').empty();  // clear the div, purely for visual feedback
                        self.readOptochtVolgorde(); 
                     });
            
                     this.readOptochtVolgorde();
                  }
               }
            }
    
            if (to == 3) {
               //enable the zoom button
               $('#zoom-button').css('visibility', 'visible');
            }
            // each time button4 is clicked, refresh the live content
            if (to == 4) {
               if (this.enablePTR) $('#page1').trigger('refresh');
               else {
                  // enable the refresh button 
                  $('#refresh-button').css('visibility', 'visible');
                  // attach AJAX function
                  $('#refresh-button').on('click', function(){ 
                     $('#livexmldoc').empty();  // clear the div, purely for visual feedback
                     //appStatus.refreshContent(appStatus.extContent.uitslag); 
                     self.readUitslag();
                  });
            
                  //appStatus.refreshContent(appStatus.extContent.uitslag);
                  this.readUitslag();
               }
            }
    
            // each time button5 is clicked, refresh the live content
            if (to == 5) {
               if (this.enablePTR) {
                  $('#page1').trigger('refresh');
                  $('#page2').trigger('refresh');
                  $('#page3').trigger('refresh');
               } else {
                  // enable the refresh button
                  $('#refresh-button').css('visibility', 'visible');
                  // attach AJAX function
                  $('#refresh-button').on('click', function(){ 
                     $('#livexmlnieuws').empty();  // clear the div, purely for visual feedback
                     self.refreshContent(self.extContent.facebook);
                     self.refreshContent(self.extContent.instagram);
                     self.refreshContent(self.extContent.twitter); 
                  });
            
                  this.refreshContent(this.extContent.facebook);
                  this.refreshContent(this.extContent.instagram);
                  this.refreshContent(this.extContent.twitter);
               }
            }
         
         } 
         
      } else if (to==0) {
         // Section 0 is special, it just show a background image
         this.activeTab = to;
         
         // reset all pages but the first
         for (var p=2; p<=this.sections.maxPages; p++) $("#page"+p).css( { 'visibility' : 'hidden', 'z-index' : '-1', 'left' : '0%' } );
            
         this.drawPagingIndicators();
      }
      
   },
   /*
   initTimeTable: function() {
      var le = jQuery("#locaties");
      var tze = jQuery("#tijden-zo");
      var tme = jQuery("#tijden-ma");
      var bze = jQuery("#button-zo");
      var bme = jQuery("#button-ma");
      var p1e = jQuery("#page1");
      
      var teTop = p1e.height()*0.0677;
      var bzeWidth = bze.width();
            
      // set all static elements of the timetable to visible
      le.css('visibility', 'visible');
      bze.css('visibility', 'visible');
      bme.css('visibility', 'visible');
      bme.css({left: bzeWidth + 'px'});
      
      // scale and position the times on zondag
      tze.height( p1e.height()*0.9323);
      tze.parent().css({position: 'relative', });
      tze.css({top: teTop + 'px', left: 0, position:'absolute'});
      
      // scale and position the times on maandag
      tme.height( p1e.height()*0.9323);
      tme.parent().css({position: 'relative', });
      tme.css({top: teTop + 'px', left: 0, position:'absolute'});
      
      jQuery("#page1").css('background','#ffffff');
      
      bze.on('click', function(e){
         //alert('ZONDAG');
         jQuery("#wat #tijden-ma").css('visibility', 'hidden');
         jQuery("#wat #tijden-zo").css('visibility', 'visible');
         jQuery("#button-zo").attr("src","img/timetable/button-zo-active.png");
         jQuery("#button-ma").attr("src","img/timetable/button-ma.png");
      });
      
      bme.on('click', function(e){
         //alert('MAANDAG');
         jQuery("#wat #tijden-zo").css('visibility', 'hidden');
         jQuery("#wat #tijden-ma").css('visibility', 'visible');
         jQuery("#button-zo").attr("src","img/timetable/button-zo.png");
         jQuery("#button-ma").attr("src","img/timetable/button-ma-active.png");
      });
   },*/
   
   initTimeTable: function() {
      var le = jQuery("#locaties");
      var tze = jQuery("#tijden-zo");
      var tme = jQuery("#tijden-ma");
      var bze = jQuery("#button-zo");
      var bme = jQuery("#button-ma");
      var p1e = jQuery("#page1");
      
      var teTop = p1e.height()*0.0677;
      var bzeWidth  = bze.width();
      var bzeHeight = bze.height();
            
      // set all static elements of the timetable to visible
      le.css('visibility', 'visible');
      bze.css('visibility', 'visible');
      bme.css('visibility', 'visible');
      bme.css({left: bzeWidth + 'px'});
      
      // vertically align text in span via line-height (line-height cannot be done via %, so just best set it via js
      bze.css('line-height' , bzeHeight + 'px');
      bme.css('line-height' , bzeHeight + 'px');
      
      // scale and position the times on zondag
      tze.height( p1e.height()*0.9323);
      tze.parent().css({position: 'relative', });
      tze.css({top: teTop + 'px', left: 0, position:'absolute'});
      
      // scale and position the times on maandag
      tme.height( p1e.height()*0.9323);
      tme.parent().css({position: 'relative', });
      tme.css({top: teTop + 'px', left: 0, position:'absolute'});
      
      jQuery("#page1").css('background','#ffffff');
      
      bze.on('click', function(e){
         //alert('ZONDAG');
         jQuery("#wat #tijden-ma").css('visibility', 'hidden');
         jQuery("#wat #tijden-zo").css('visibility', 'visible');
         jQuery("#button-zo").css('background-color','#ea5514');
         jQuery("#button-ma").css('background-color','#3c3c3c');
      });
      
      bme.on('click', function(e){
         //alert('MAANDAG');
         jQuery("#wat #tijden-zo").css('visibility', 'hidden');
         jQuery("#wat #tijden-ma").css('visibility', 'visible');
         jQuery("#button-zo").css('background-color','#3c3c3c');
         jQuery("#button-ma").css('background-color','#ea5514');
      });
   },
   
   resetTimeTable: function() {
      jQuery("#locaties").css('visibility', 'hidden');
      jQuery("#button-zo").css('visibility', 'hidden');
      jQuery("#button-ma").css('visibility', 'hidden');
      jQuery("#page1").css('background','#ea5514');
   }

};


/* TO DO
7. alle functions onderdeel maken van appStatus object
8. facebook one time login, evt. op aparte help pagina (links van default openings pagina)
9. instagram website: generieke refresh functie maken
10. titel per page ipv per section
*/



//
// PhoneGap is ready
//
function onDeviceReady() {
   //
   // Init
   //
   
   // fix bug in cordova that doesn't fix orientation on iPad, so do it manually via a special plugin
   if (typeof screen.lockOrientation != "undefined") screen.lockOrientation('portrait');
   
   appStatus.setLanguage(false, function() {
      appStatus.setTexts();
      appStatus.readOptochtVolgorde();
      appStatus.readUitslag();
   });
   
   // use pull-to-refresh on iOS devices
   appStatus.enablePTR = (device.platform == 'iOS');
   //appStatus.optochtLive = ( jQuery('#phpOVLiveVar').val() != '0' ); // read the php variable set in index.php (note: the php var is returned as string!)
      
   //Open the App with indicated section
   appStatus.disableSections();
   appStatus.switchSection(appStatus.sections.opensWith);

   //
   // Menu
   //
   jQuery('#tab-bar a').on('click', function(e){
      e.preventDefault();
      // check which button was pressed and get the new section to go to 
      var button = e.currentTarget.id;
      //var nextSection = button.replace("button","section");
      var nextSection = button.substr(button.length - 1);
      //alert(nextSection);
    
      appStatus.switchSection(nextSection);
    
    /*
    appStatus.activeTab = button.replace("button","");
    // reset all pages but the first
    for (var p=2; p<=appStatus.sections.maxPages; p++) $("#page"+p).css( { 'visibility' : 'hidden', 'z-index' : '-1', 'left' : '0%' } );
    // get all pages in the section
    var pages=[];
    for(var key in appStatus.sections[nextSection]) {
      if (key.indexOf("page")!=-1) pages.push(appStatus.sections[nextSection][key]);
    }
    // restore all pages but the first
    for (var p=2; p<=pages.length; p++) {
      var l = (p-1)*100 + '%';
      $("#page"+p).css( { 'visibility' : 'visible', 'z-index' : '2', 'left' : l } );
    }
    // fade in the new pages, fade out the old
    appStatus.page(pages);

    // make the pressed button the new current button
    $("#tab-bar .current").removeClass("current");
    $(e.currentTarget).addClass("current");
    // reset the scroll position
    $("#canvas").scrollLeft(0);  // horizontal scrolling goes via canvas
    for (p=1; p<=appStatus.sections.maxPages; p++) $("#page"+p).scrollTop(0); //vertical scrolling goes via the pages
    // draw the paging indicators + title
    appStatus.drawPagingIndicators();
    
    
    // disable the refresh and zoom button by default
    $('#refresh-button').css('visibility', 'hidden');
    $('#zoom-button').css('visibility', 'hidden');
    
    if (button == "button1") appStatus.initTimeTable();
    else appStatus.resetTimeTable();
    
    if (appStatus.optochtLive) {
      if (button == "button2") {
        if (appStatus.enablePTR) $('#page1').trigger('refresh');
        else {
            // enable the refresh button 
            $('#refresh-button').css('visibility', 'visible');
            // attach AJAX function
            $('#refresh-button').on('click', function(){ 
              $('#optocht').empty();  // clear the div, purely for visual feedback
              appStatus.refreshContent(appStatus.extContent.optocht); 
            });
            
            appStatus.refreshContent(appStatus.extContent.optocht);
        }
      }
    }
    
    if (button == "button3") {
        //enable the zoom button
        $('#zoom-button').css('visibility', 'visible');
    }
    // each time button4 is clicked, refresh the live content
    if (button == "button4") {
        if (appStatus.enablePTR) $('#page1').trigger('refresh');
        else {
            // enable the refresh button 
            $('#refresh-button').css('visibility', 'visible');
            // attach AJAX function
            $('#refresh-button').on('click', function(){ 
              $('#livexmldoc').empty();  // clear the div, purely for visual feedback
              //appStatus.refreshContent(appStatus.extContent.uitslag); 
              appStatus.readUitslag();
            });
            
            //appStatus.refreshContent(appStatus.extContent.uitslag);
            appStatus.readUitslag();
        }
    }
    
    // each time button5 is clicked, refresh the live content
    if (button == "button5") {
        if (appStatus.enablePTR) {
            $('#page1').trigger('refresh');
            $('#page2').trigger('refresh');
            $('#page3').trigger('refresh');
        } else {
            // enable the refresh button
            $('#refresh-button').css('visibility', 'visible');
            // attach AJAX function
            $('#refresh-button').on('click', function(){ 
              $('#livexmlnieuws').empty();  // clear the div, purely for visual feedback
              appStatus.refreshContent(appStatus.extContent.facebook);
              appStatus.refreshContent(appStatus.extContent.instagram);
              appStatus.refreshContent(appStatus.extContent.twitter); 
            });
            
            appStatus.refreshContent(appStatus.extContent.facebook);
            appStatus.refreshContent(appStatus.extContent.instagram);
            appStatus.refreshContent(appStatus.extContent.twitter);
        }
        //
    }
    */
   });
  
  // 
  // Correctly setup everything that depends on the orientation
  //
  onOrientationChange(); 
  
  //
  // Put bg image of programma tab at bottom of the page
  //
  /*
  image_height = 275*(window.innerWidth/320.0); // HACK since image appears not be loaded yet, so no info yet on actual size
  top_position = window.innerHeight - image_height - jQuery('#tab-bar a').height();
  position_str = top_position + "px";
  jQuery("#bgimage").css("top",position_str);
  */
  
  //
  // Collapse (toggle) div layer
  //
  jQuery(".wageninfo").hide();
  jQuery(".korpsinfo").hide();
  jQuery(".proginfo").hide();
  jQuery("#app-info").hide();
  jQuery("#overlay").hide();
  //toggle the componenet with class msg_body
  jQuery("#info-button").click(function()
  {
      jQuery("#overlay").fadeToggle(400);
      jQuery("#app-info").slideToggle(200);
  });
  jQuery("#zoom-button").click(function()
  {
      var default_width = 1218; // the default width of the plattegrond
      
      if (appStatus.plattegrond.zoomLevel == 0) {
          jQuery("#plattegrond").width(window.innerWidth);
          jQuery("#waar").width(window.innerWidth);
          newBGImg = "url(img/layout/icon_zoom_in.png)";
          appStatus.plattegrond.zoomLevel = 1;
      } else {
          jQuery("#plattegrond").width(default_width);
          jQuery("#waar").width(default_width);
          newBGImg = "url(img/layout/icon_zoom_out.png)";
          appStatus.plattegrond.zoomLevel = 0;
      }
      jQuery(this).css("background-image",newBGImg);
  });
  jQuery(".optochtvolgorde").click(function()
  {
      jQuery(this).next(".wageninfo").slideToggle(200);
      if (jQuery(this).css("background-image").search('open') != -1) newBGImg = "url(img/layout/close.png)";
      else newBGImg = "url(img/layout/open.png)";
      jQuery(this).css("background-image",newBGImg);
  });
  jQuery(".korps").click(function()
  {
      jQuery(this).next(".korpsinfo").slideToggle(200);
      if (jQuery(this).css("background-image").search('open') != -1) newBGImg = "url(img/layout/close.png)";
      else newBGImg = "url(img/layout/open.png)";
      jQuery(this).css("background-image",newBGImg);
  });
  /*
  jQuery(".wageninfo .foto").click(function()
  {
      jQuery("#overlay").fadeIn(400);
      jQuery(this).css("z-index",3);
      jQuery(this).animate({ width: "1000px", top: "200px" }, 200);
  });
  */
  jQuery(".progitem").click(function()
  {
      jQuery(this).next(".proginfo").slideToggle(200);
      if (jQuery(this).css("background-image").search('open') != -1) newBGImg = "url(img/layout/close.png)";
      else newBGImg = "url(img/layout/open.png)";
      jQuery(this).css("background-image",newBGImg);
  });
  
  // 
  // open urls in an in-app browser
  //
  jQuery('#app-info a, #wie a').on('click', function(e){
		e.preventDefault();
      var url = e.currentTarget.href;
      window.open(encodeURI(url), '_blank', 'location=yes');
  });
  

  //
  // Pull to refresh code
  //
  if (appStatus.enablePTR) {
      // attach pull-to-refresh to optocht tab
      if (appStatus.optochtLive) {
      jQuery('#page1').pullToRefresh({
              tabDiv: "#wie",
              ajaxDiv: "#optocht",
              callback: function() {
                    var def = $.Deferred();
                    
                    setTimeout(function() {
                        def.resolve();      
                    }, 2000); 
                    // since on highest level attached to 'page1' only 1 callback function, so refresh depending on active tab
                    if (appStatus.activeTab == 2) appStatus.readOptochtVolgorde();

                    return def.promise();
                }
            });
      }
      // attach pull-to-refresh to uitslag tab
      jQuery('#page1').pullToRefresh({
              tabDiv: "#live",
              ajaxDiv: "#livexmldoc",
              callback: function() {
                    var def = $.Deferred();
                    
                    setTimeout(function() {
                        def.resolve();      
                    }, 2000); 
                    // since on highest level attached to 'page1' only 1 callback function, so refresh depending on active tab
                    if (appStatus.activeTab == 4) appStatus.readUitslag(); //appStatus.refreshContent(appStatus.extContent.uitslag);

                    return def.promise();
                }
            });
      // attach pull-to-refresh to facebook page            
      jQuery('#page1').pullToRefresh({
              tabDiv: "#facebook",
              ajaxDiv: "#facebookxml",
              callback: function() {
                    var def = $.Deferred();
                    
                    setTimeout(function() {
                        def.resolve();      
                    }, 2000); 
                    // since on highest level attached to 'page1' only 1 callback function, so refresh depending on active tab
                    if (appStatus.activeTab == 5) appStatus.refreshContent(appStatus.extContent.facebook);

                    return def.promise();
                }
            });
      // attach pull-to-refresh to instagram page
      jQuery('#page2').pullToRefresh({
              tabDiv: "#instagram",
              ajaxDiv: "#instagramxml",
              callback: function() {
                    var def = $.Deferred();
                    
                    setTimeout(function() {
                        def.resolve();      
                    }, 2000); 
                    // since on highest level attached to 'page1' only 1 callback function, so refresh depending on active tab
                    if (appStatus.activeTab == 5) appStatus.refreshContent(appStatus.extContent.instagram);

                    return def.promise();
                }
            });
      // attach pull-to-refresh to twitter page
      jQuery('#page3').pullToRefresh({
              tabDiv: "#nieuws",
              ajaxDiv: "#livexmlnieuws",
              callback: function() {
                    var def = $.Deferred();
                    
                    setTimeout(function() {
                        def.resolve();      
                    }, 2000); 
                    // since on highest level attached to 'page1' only 1 callback function, so refresh depending on active tab
                    if (appStatus.activeTab == 5) appStatus.refreshContent(appStatus.extContent.twitter);

                    return def.promise();
                }
            });

   }
     
   //
   // Setup Paging
   //
   jQuery('#canvas').paging({
            'snaps' : '.snap',
            'offset' : 0,
            'duration' : 50,
            'easing' : 'swing',
            'debug' : false,
	         'debugText' : function(txt){
	                           jQuery('#titel').text(txt);
	                        },
	         'snapComplete' : function(){
	                           appStatus.drawPagingIndicators();
	                           //alert('complete');
	                        }
   });

   //
   // Setup sortable voorspelling
   //
   /*
   jQuery('#sortable').sortable({ axis: "y" });
   jQuery('#sortable').on( "taphold", function( event ) { 
      alert('tap'); 
   } );
   //jQuery('#sortable').disableSelection();
   // Refresh list to the end of sort to have a correct display
   jQuery('#sortable').on( "sortstop", function(event, ui) {
      jQuery('#sortable').listview('refresh');
   });
   */
   
   
}


function onOrientationChange()
{
    /*
    switch(window.orientation) 
    {  
      case -90:
      case 90:
        alert('landscape');
        break; 
      default:
        alert('portrait');
        break; 
    }
    */
    
    // reset scroll position to top left (amongst others because page snapping is incorrect after rotation)
    jQuery('#canvas').scrollTop(0);
    jQuery('#canvas').scrollLeft(0);

    // if zoomed out to match width of screen update needed
    if (appStatus.plattegrond.zoomLevel == 1) {
        // set the plattegrond and tab to the new width
        jQuery("#plattegrond").width(window.innerWidth);
        jQuery("#waar").width(window.innerWidth);
    }
    
    // set wageninfo images and text to correct width
    var preferredImageWidth = 320.0;
    var newImageWidth, newTextWidth;
    var padding = parseInt(jQuery(".wageninfo").css("padding-left"));
    
    // find out ratio between screen width and preferred image width
    var ratio = preferredImageWidth/window.innerWidth;
    if (ratio <= 0.68) {
        // wide screen so image and text side-by-side, take the preferred image width but maximum half of the screen
        newImageWidth = Math.min(preferredImageWidth, window.innerWidth/2);
        newTextWidth = window.innerWidth - newImageWidth - (2*padding + 5); // take padding into account (both sides) + 5 additional for beschrijving
        jQuery(".wageninfo .beschrijving").css("padding-left","5px");
    } else {
        // small screen so image as wide as screen, text below
        newImageWidth = window.innerWidth - 2*padding; // take padding into account (both sides)
        newTextWidth = window.innerWidth - 2*padding; // take padding into account (both sides)
        jQuery(".wageninfo .beschrijving").css("padding-left","0px");
    }
    // adapt the widths of the elements
    jQuery(".wageninfo .foto").width(newImageWidth);
    jQuery(".wageninfo .beschrijving").width(newTextWidth);
    
    // adapt timetable width
    jQuery("#timetable #tijden").width( window.innerWidth - jQuery("#timetable #locaties").width() );
    
    //alert('width: ' + window.innerWidth);
}
