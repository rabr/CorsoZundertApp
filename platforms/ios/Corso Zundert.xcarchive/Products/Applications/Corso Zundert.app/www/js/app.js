// Wait for PhoneGap to load
//
document.addEventListener("deviceready", onDeviceReady, false);

// Update content when orientation changes
// var supportsOrientationChange = "onorientationchange" in window,
// orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";
// window.addEventListener(orientationEvent, function)
//
window.addEventListener('orientationchange', onOrientationChange);

/*
function attachPinch(wrapperID,imgID)
{
    var image = $(imgID);
    var wrap = $(wrapperID);

    var  width = image.width();
    var  height = image.height();
    var  newX = 0;
    var  newY = 0;
    var  offset = wrap.offset();
    
    $(imgID).hammer().on("pinch", function(event) {
        var photo = $(this);

        newWidth = photo.width() * event.gesture.scale;
        newHeight = photo.height() * event.gesture.scale;

        // Convert from screen to image coordinates
        var x = event.gesture.center.x;
        var y = event.gesture.center.y;
        x -= offset.left + newX;
        y -= offset.top + newY;

        newX += -x * (newWidth - width) / newWidth;
        newY += -y * (newHeight - height) / newHeight;

        photo.css('-webkit-transform', "scale3d("+event.gesture.scale+", "+event.gesture.scale+", 1)");      
        wrap.css('-webkit-transform', "translate3d("+newX+"px, "+newY+"px, 0)");

        width = newWidth;
        height = newHeight;
       
    });
   
   $(imgID).data("hammer").get("pinch").set({ enable: true });
}


function attachPinch(wrapperID,imgID)
{

   var MIN_SCALE = 1;
   var MAX_SCALE = 5;
   var scale = MIN_SCALE;

   var offsetX = 0;
   var offsetY = 0;

   var $image     = $(imgID);
   var $container = $(wrapperID);

   var areaWidth  = $container.width();
   var areaHeight = $container.height();
   
   $container.hammer().on("pinch", function(event) {

      var clientX = event.gesture.center.x - $container.offset().left;
      var clientY = event.gesture.center.y - $container.offset().top;

      var nextScale = Math.min(MAX_SCALE, Math.max(MIN_SCALE, scale * event.gesture.scale));

      var percentXInCurrentBox = clientX / areaWidth;
      var percentYInCurrentBox = clientY / areaHeight;

      var currentBoxWidth  = areaWidth / scale;
      var currentBoxHeight = areaHeight / scale;

      var nextBoxWidth  = areaWidth / nextScale;
      var nextBoxHeight = areaHeight / nextScale;

      var deltaX = (nextBoxWidth - currentBoxWidth) * (percentXInCurrentBox - 0.5);
      var deltaY = (nextBoxHeight - currentBoxHeight) * (percentYInCurrentBox - 0.5);

      var nextOffsetX = offsetX - deltaX;
      var nextOffsetY = offsetY - deltaY;

      $image.css({
         transform : 'scale(' + nextScale + ')',
         left      : -1 * nextOffsetX * nextScale,
         right     : nextOffsetX * nextScale,
         top       : -1 * nextOffsetY * nextScale,
         bottom    : nextOffsetY * nextScale
      });

      offsetX = nextOffsetX;
      offsetY = nextOffsetY;
      scale   = nextScale;
   });
   
   $container.data("hammer").get("pinch").set({ enable: true });

}
*/
/*
function attachPinch(imgID)
{
   var wrap = $('#waar');

   $(imgID).hammer().on("pinch", function(event) {
      var minWidth = window.innerWidth;
      var maxWidth = 3805;
      
      var photo = $(this);
      var width  = photo.width();
      var newWidth = width*event.gesture.scale
      
      if (newWidth>=minWidth && newWidth<=maxWidth) {
         photo.css('max-width','');
         photo.css('height','auto');
         photo.width(newWidth);
         wrap.width(newWidth);
         //photo.css('-webkit-transform', "scale3d("+event.gesture.scale+", "+event.gesture.scale+", 1)"); 
      }    
   });
   
   $(imgID).data("hammer").get("pinch").set({ enable: true });
}
*/

function attachPinch(imgID)
{
   var wrap = $('#waar');
   var pinching = false;
   var dist = 0;
   var prevDist = 0;
   
   $(imgID).on('touchstart', function(event) {
      if (event.originalEvent.touches.length == 2) {
         pinching = true;
         prevDist = 0;
      }
   });
   
   $(imgID).on('touchmove', function(event) {
   
      if (pinching) {
         var photo = $(this);
         var t = event.originalEvent.touches;
         var scale;
         var scrollT = $('#page1').scrollTop();
		   var scrollL = $('#page1').scrollLeft();
      
         dist = Math.sqrt(
            (t[0].pageX-t[1].pageX) * (t[0].pageX-t[1].pageX) +
            (t[0].pageY-t[1].pageY) * (t[0].pageY-t[1].pageY));
            
         if (prevDist!=0 && dist>50) scale = dist/prevDist;
         else scale = 1;
            
         var minWidth = window.innerWidth;
         var maxWidth = 3805;
      
         
         var width  = photo.width();
         var newWidth = width*scale;
         
         if (newWidth>=minWidth && newWidth<=maxWidth) {
            photo.css('max-width','');
            photo.css('height','auto');
            photo.width(newWidth);
            wrap.width(newWidth);
            
            $('#page1').scrollTop(scrollT*scale);
            $('#page1').scrollLeft(scrollL*scale);
            //photo.css('-webkit-transform', "scale3d("+event.gesture.scale+", "+event.gesture.scale+", 1)"); 
         }
         
         prevDist = dist;
      }
   });

   $(imgID).on("touchend", function(event) {
      if (pinching) pinching = false;
   });
   
}


//
// Define global variable to keep track of the status of affaires
//
var appStatus = {
    // 
    // CONSTANTS
    //
    supportedLangs : [ 'nl', 'en', 'fr' ],                  // supported languages
    defaultLang : 'en',                                     // default language in case phone language is not supported                     
    jaar : 2018,                                            // the current year
    apiBaseUrl : 'http://www.corsozundert.nl/api/1.0/',     // the url of the api to the database, incl version number
    maxRefreshRate : 20,                                     // maximum refresh rate in secs of API call (for uitslag), to limit server load
    extContent : {                                          // External content, either from own server via CZ-API or from social media
      optocht : {
         id   : 'optocht',
         live : true,
         imgOnline : true
      },
      uitslag : {
         page1 : {
            id   : 'livexmldoc',
            year : 2017,
            live : true
         },
         page2 : {
            id   : 'vorigjaarxml',
            year : 2016,
            live : false
         }
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
         title1  : '',
         page1   : '',
         title2  : 'App Info',
         page2   : '',
         title3  : 'Settings',
         page3   : ''
      },
      section1 : {
         enabled : false,
         title1  : 'programma en tijden',
         page1   : '#wat',
         title2  : 'maandag',
         page2   : '#maandag',
         title3  : 'TV Uitzendingen',
         page3   : '#uitzending'
      },  
      section2 : {
         enabled : true,
         title1  : 'deelnemers optocht 2018',
         page1   : '#wie'
      }, 
      section3 : {
         enabled : true,
         title1  : 'plattegrond',
         page1   : '#waar'
      },
      section4 : {
         enabled : true,
         title1  : 'uitslag 2017',
         page1   : '#live',
         title2  : 'uitslag 2016',
         page2   : '#vorigjaar' /*,
         title2  : 'jouw voorspelling 2014',
         page2   : '#voorspelling'*/
      },
      section5 : {
         enabled : true,
         title1  : 'facebook CorsoZundert',
         page1   : '#facebook',
         title2  : 'instagram #corsozundert',
         page2   : '#instagram' /*, 
         title3  : 'tweets #corsozundert',
         page3   : '#nieuws' */
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
   updateFunction: null,       
   activeTab : 1,              // the number of the active tab
   enablePTR : 0,              // enable pull-to-refresh (default off)
   plattegrond : {             // plattegrond specific status
      zoomLevel : 0            // 0 = zoomed in, 1 = zoomed out    
   },
   photoViewActive : false,
   location : {
      serverSideConfig  : null,
      reportingInterval : 10,          // constant, in seconds
      enabled           : false,
      latitude          : null,
      longitude         : null,
      lastTime          : 0,
      watchID           : null
   },
   //optochtLive : 1,           // have the optocht_volgorde live or fixed include. Note this var is overruled by a php variable transferred from index.php, see below

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
   
   // function to call to update all texts in case of a language change
   updateTexts : function() {
      this.setTexts();
      this.readOptochtVolgorde();
      this.readUitslag(appStatus.extContent.uitslag.page1);
      this.readUitslag(appStatus.extContent.uitslag.page2);
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
         
         // warning @ map
         $('#route').text(self.text.sentences.route);
         
         // tv broadcast schedule
         $('#oblive').text(self.text.sentences.oblive);
         $('#obsum').text(self.text.sentences.obsum);
         //$('#maxsum').text(self.text.sentences.maxsum);
         
         // update the titel (of the active page within the tab), easiest to use drawPagingIndicators for that
         self.drawPagingIndicators();
         
         // the app-info
         $('#app-info').html(self.text.info);
         $('#app-info #lang').off('click').on('click', function() {  // make sure to only bind once, otherwise strange things happen
            jQuery("#choose-lang").fadeToggle(400);
            appStatus.setLanguage(true, function() {
               appStatus.updateTexts();
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
      var apiurl = this.apiBaseUrl + '?optocht&jaar=' + this.jaar + '&taal=' + this.lang;
      var imgPathOnline = 'http://www.corsozundert.nl/uploads/images/archief/';
      var imgPathLocal = 'img/';   
      var imgPath = 'null';    
      var elem = '#' + this.extContent.optocht.id;
      var self = this; //closure for use inside callback function
      
      if (this.extContent.optocht.live) ovurl = apiurl;
      else ovurl = fileurl;
      
      if (this.extContent.optocht.imgOnline) imgPath = imgPathOnline;
      else imgPath = imgPathLocal;
      
      console.log('Attempting to load ' + ovurl);
      jQuery.getJSON(ovurl, function(data) {
         self.ovdata = data;
         console.log('loading text : ' + JSON.stringify(self.ovdata, null, 4));
      }).fail(function(jqXHR, status, error){
         alert('error loading optocht file: ' + status + ', ' + error);
      }).complete(function() { 
         var debug = "";
      
         // clear all existing content from optocht div
         jQuery(elem).empty();
         
         if (self.ovdata["data"].length == 0) {
            // data is empty so no optocht volgorde yet, we can do a count down
            var today = new Date();
            var bm = new Date(2018,5,9,20); // months count from 0!
            var timeDiff = Math.abs(bm.getTime() - today.getTime());
            var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
            
            var html = $('<p id="countdown">').append('Nog <span id="days">' + diffDays + '</span> dagen tot de optocht volgorde 2018 bekend is');
            
            $(elem).append(html);
            
         } else {
         
         // loop through all data items
         for (var key in self.ovdata["data"]) {
            var item = self.ovdata["data"][key];
            
            if (item["type"] == "wagen") {
               var w = item["wagen"];
               var b = item["buurtschap"];
               var v = b["vorigjaar"];
               var jaar = self.ovdata["jaar"];
               var foto_maquette1 = imgPath + 'wagens/' + jaar + '/' + jaar + '-' + b["afkorting"] + '-M00.jpg';
               var foto_maquette2 = imgPath + 'wagens/' + jaar + '/' + jaar + '-' + b["afkorting"] + '-M01.jpg';
               var foto_heraldiek = imgPathLocal + 'heraldieken/' + b["afkorting"] + '.gif';
               var ly = self.text.sentences["lastyear"];
               var lastyear;
               var titel;
               var wom = w["omschrijving"];
               
               // apparently there is a difference in interpretation of /r /n chars between reading directly form api versus from file. For the latter they need to actively be replaced by <br>. Do not understand why, but this works for now.
               if (!self.extContent.optocht.live) wom = wom.replace(/\r\n|\n|\r/g, '<br />');
               
               // if points of last year are negative we need another sentence
               if (v["punten"] > 0) lastyear = ly["part1"] + ' ' + b["naam"] + ' ' + ly["part2"] + ' ' + v["prijs"] + ' ' + ly["part3"] + ' "' + v["titel"] + '" ' + ly["part4"] + ' ' + v["punten"] + ' ' + ly["part5"] + '.';
               else lastyear = ly["part1-np"] + ' ' + b["naam"] + ' ' + ly["part2-np"] + ' "' + v["titel"] + '" ' + ly["part3-np"] + '.';
               
               if (w["startnummer"]!=null) titel = w["startnummer"] + '. ' + w["titel"];
               else titel = w["titel"];
               
               var html = $('<div class="optochtvolgorde">')
                  .append($('<div class="foto">').append('<img src="' + foto_maquette1 + '" alt="" />'))
                  .append($('<div class="text">') 
                     .append($('<h2>').append(titel))
                     .append($('<p>').append('<i>' + self.text.words["buurtschap"] + ': </i>' + b["naam"] + '<br>' + '<i>' + self.text.words["ontwerpers"] + ': </i>' + w["ontwerpers"] + '<br>'))
                  );
               
               $(elem).append(html);
               
               html = $('<div class="wageninfo">')
                  .append($('<div class="foto">').append('<img src="' + foto_maquette1 + '" alt="" />'))
                  /*.append($('<div class="foto">').append('<img src="' + foto_maquette2 + '" alt="" />'))*/
                  .append($('<div class="beschrijving">')
                     .append($('<p class="tekstwagen">').append(wom))
                     .append($('<div class="heraldiek">').append('<img src="' + foto_heraldiek + '" alt="" />'))
                     .append($('<div class="buurtschap">')
                        .append('<p class="titel">' + self.text.words["buurtschap"] + ' ' + b["naam"] + '</p>')
                        .append('<p class="tekst">' + b["omschrijving"] + '</p>')
                     )
                  )
                  .append($('<div id="clearfloat0"></div>'));
                  
               if (v["titel"]!=null) html.append('<p class="prijzen">' + lastyear + '</p>');
                  //.append('<p class="prijzen">Vorig jaar behaalde buurtschap ' + b["naam"] + ' met de wagen "' + v["titel"] + '" een ' + v["prijs"] + 'e plaats met ' + v["punten"] + ' punten.</p>');
               // !!! MAKE TRANSLATION OF ABOVE
               
               $(elem).append(html);   
               $(elem).append($('<div class="spacer">'));
               
               
               
            } else if (item["type"] == "korps") {
               var foto_korps = imgPath + 'korpsen/' + item["foto"];
               
               var html = $('<div class="korps">')
                  .append($('<div class="foto">').append('<img src="' + foto_korps + '" alt="" />'))
                  .append($('<div class="text">') 
                     .append($('<h2>').append(item["naam"]))
                     .append($('<p>').append(item["plaats"] + ', ' + item["land"]))
                  );
               
               $(elem).append(html);
               
               html = $('<div class="korpsinfo">')
                  .append($('<div class="foto">').append('<img src="' + foto_korps + '" alt="" />'))
                  .append($('<div class="beschrijving">')
                     .append($('<p class="omschrijving">').append(item["omschrijving"]))
                     .append($('<p>').append('<a href="' + item["link"] + '">' + item["link"] + '</a>'))
                  )
                  .append($('<div id="clearfloat0"></div>'));
               
               $(elem).append(html);   
               $(elem).append($('<div class="spacer">'));
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
         jQuery(".wageninfo .foto img").click(function() {
            appStatus.viewPhoto(this);
         });
         
         }
         
         //alert(debug); 
      });
   
   },
   
   readUitslag : function(content) {
      var year = content.year; // this.jaar;
      var usurl = null;
      var fileurl = 'res/uitslag-' + year + '-' + this.lang +'.json';
      var apiurl = this.apiBaseUrl + '?uitslag&jaar=' + year + '&taal=' + this.lang;
      var self = this; //closure for use inside callback function
      var imgPathOnline = 'http://www.corsozundert.nl/uploads/images/archief/wagens/' + year + '/';
      var imgPathLocal = 'img/wagens/' + year + '/';
      var imgPath = null;
      var elem = '#' + content.id;
      var now = Date.now()/1000;  // convert back to secs instead of msecs
      var refresh = true;
      
      if (content.live) {
         usurl = apiurl;
         imgPath = imgPathOnline;
      } else {
         usurl = fileurl;
         imgPath = imgPathLocal;
      }
      
      if (this.uitslag != null && content.live) {
         // there has been a previous API call to retrieve the uitslag
         // so check the time difference
         // NOTE: the local client clock and server clock can be (and are) out-of-sync, so there will always be a diff
         // need to think through further how to deal with that, for now just put d>client-server delta and test!!!
         var d = Math.abs(now - this.uitslag.timestamp)
         if (d < this.maxRefreshRate) refresh = false; //no need to refresh
      }
      
      if (!refresh) {
         //alert('wait a sec, hold your horses | ' + d);
      } else {
      
      console.log('Attempting to load ' + usurl);
      jQuery.getJSON(usurl, function(data) {
         self.uitslag = data;
         console.log('loading text : ' + JSON.stringify(self.uitslag, null, 4));
      }).fail(function(jqXHR, status, error){
         alert('error loading uitslag file: ' + status + ', ' + error);
      }).complete(function() { 
         var debug = "";
         
         // clear all existing content from optocht div
         jQuery(elem).empty();
         
         // loop through all data items
         for (var key in self.uitslag["data"]) {
            var item = self.uitslag["data"][key];
            
            if (item["titel"]!="") {
               var html = $('<div class="uitslagregel">')
                  .append($('<p class="prijs">').append(item["prijs"]));
                  /*
                  .append($('<div class="prijzen">')
                     .append($('<p class="punten">').append(item["punten"] + ' ' + self.text.words["punten"]))
                     .append($('<p class="ereprijs">').append(item["ereprijs"]))
                  )*/
               var wagen = $('<div class="wagen">');    
               
               if (item["foto"]!="") $(wagen).append($('<img src="' + imgPath + item["foto"] + '">'));
               
               $(wagen).append($('<p class="titel">').append(item["titel"]))
                       .append($('<p class="buurtschap">').append(item["buurtschap"])); // self.text.words["buurtschap"] + ' ' + 
               
               if (item["punten"]!=null && item["punten"]!="") $(wagen).append($('<p class="punten">').append(item["punten"] + ' ' + self.text.words["punten"]));
               
               $(html).append(wagen);
               $(elem).append(html);
               
            } else {
               var html = $('<div class="uitslagregel">')
                  .append($('<p class="prijs">').append(item["prijs"]))
                  .append($('<p class="onbekend">?</p>'));
               
               $(elem).append(html);
            }
         }
         
         jQuery(".wagen img").click(function() {
            appStatus.viewPhoto(this);
         });
      });
      
      }
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
    
            if (this.extContent.optocht.live) {
               if (to == 2) {
                  if (this.enablePTR) $('#page1').trigger('refresh');
                  else {
                     // enable the refresh button 
                     $('#refresh-button').css('visibility', 'visible');
                     // attach AJAX function
                     $('#refresh-button').on('click', function(){ 
                        $('#'+this.extContent.optocht.id).empty();  // clear the div, purely for visual feedback
                        self.readOptochtVolgorde(); 
                     });
            
                     this.readOptochtVolgorde();
                  }
               }
            }
    
            if (to == 3) {
               //enable the zoom button
               //$('#zoom-button').css('visibility', 'visible');
            }
            // each time button4 is clicked, refresh the live content
            if (to == 4) {
               if (this.extContent.uitslag.page1.live) {
                  if (this.enablePTR) $('#page1').trigger('refresh');
                  else {
                     // enable the refresh button 
                     $('#refresh-button').css('visibility', 'visible');
                     // attach AJAX function
                     $('#refresh-button').on('click', function(){ 
                        $('#'+this.extContent.uitslag.page1.id).empty();  // clear the div, purely for visual feedback
                        //appStatus.refreshContent(appStatus.extContent.uitslag); 
                        self.readUitslag(this.extContent.uitslag.page1);
                     });
                     this.readUitslag(this.extContent.uitslag.page1);
                  }
               }
               if (this.extContent.uitslag.page2.live) {
                  if (this.enablePTR) $('#page2').trigger('refresh');
                  else {
                     // enable the refresh button 
                     $('#refresh-button').css('visibility', 'visible');
                     // attach AJAX function
                     $('#refresh-button').on('click', function(){ 
                        $('#'+this.extContent.uitslag.page2).empty();  // clear the div, purely for visual feedback
                        //appStatus.refreshContent(appStatus.extContent.uitslag); 
                        self.readUitslag(this.extContent.uitslag.page2);
                     });
                     this.readUitslag(this.extContent.uitslag.page2);
                  }
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
   
   setupLocationServices: function() {
      var self = this; //closure for use inside callback functions
      
      // onSetupSuccess Callback
      var onSetupSuccess = function(position) {
         self.location.enabled   = true;
         self.location.latitude  = position.coords.latitude;
         self.location.longitude = position.coords.longitude;
         self.updateLocation(position);
      };
      // onWatchSuccess Callback 
      var onWatchSuccess = function(position) {
         if (self.location.enabled) {
            var updatedLatitude  = position.coords.latitude;
            var updatedLongitude = position.coords.longitude;
 
            if (updatedLatitude != self.location.latitude && updatedLongitude != self.location.longitude) {
               self.location.latitude  = updatedLatitude;
               self.location.longitude = updatedLongitude;
               self.updateLocation(position);
            }
         }
      };
      // onError Callback receives a PositionError object
      function onError(error) {
         self.location.enabled = false;
         /*
         alert('code: '    + error.code    + '\n' +
               'message: ' + error.message + '\n');
         */
      }
      
      navigator.geolocation.getCurrentPosition(onSetupSuccess, onError, { enableHighAccuracy: true });
      
      this.watchID = navigator.geolocation.watchPosition(onWatchSuccess, onError, { enableHighAccuracy: true });
      
   },
   
   updateLocation : function(position) {
      var self = this; //closure for use inside callback functions
      var timeDiff = Math.abs(this.location.lastTime - position.timestamp);
      
      if ( this.location.enabled && (timeDiff > this.location.reportingInterval*1000) ) {
         var getApiUrl = this.apiBaseUrl + 'location/?config';
         var postApiUrl = this.apiBaseUrl + 'location/';
         
         // first check whether tracking is requested
         jQuery.getJSON(getApiUrl, function(data) {
            self.serverSideConfig = data;
            console.log('loading: ' + JSON.stringify(self.serverSideConfig, null, 4));
         }).fail(function(jqXHR, status, error){
            alert('error loading location config: ' + status + ', ' + error);
         }).complete(function() { 
            
            //alert(JSON.stringify(self.serverSideConfig));  
            if (self.serverSideConfig.trackPhones == true) {
               // yes, tracking is requested, now post the location data
               //alert("track it!")     
         
               var obj = {
                  phoneid   : device.uuid,
                  coords    : position.coords,
                  timestamp : position.timestamp
               };
               //alert(JSON.stringify(obj));
         
               // save this timestamp
               self.location.lastTime = position.timestamp;
         
               jQuery.ajax({
                  type: "POST",
                  url: postApiUrl,
                  contentType: "application/json",
                  dataType: "json",
                  data: JSON.stringify(obj)
               });
               /*
               alert('Device  : '          + device.uuid                       + '\n' +
                     'Latitude: '          + position.coords.latitude          + '\n' +
                     'Longitude: '         + position.coords.longitude         + '\n' +
                     'Altitude: '          + position.coords.altitude          + '\n' +
                     'Accuracy: '          + position.coords.accuracy          + '\n' +
                     'Altitude Accuracy: ' + position.coords.altitudeAccuracy  + '\n' +
                     'Heading: '           + position.coords.heading           + '\n' +
                     'Speed: '             + position.coords.speed             + '\n' +
                     'Timestamp: '         + position.timestamp                + '\n');  */
            }
         });
      }
   },

   initTimeTable: function() {
      /*
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
      */
      
      jQuery("#locaties-zo").fadeIn(200);
      jQuery("#locaties-ma").fadeIn(200);
      jQuery("#page1").css('background','#ffffff');
      jQuery("#page2").css('background','#ffffff');
   },
   
   resetTimeTable: function() {
      /*
      jQuery("#locaties").css('visibility', 'hidden');
      jQuery("#button-zo").css('visibility', 'hidden');
      jQuery("#button-ma").css('visibility', 'hidden');
      jQuery("#page1").css('background','#ea5514');
      */
      jQuery("#locaties-zo").fadeOut(200);
      jQuery("#locaties-ma").fadeOut(200);
      jQuery("#page1").css('background','#ea5514');
      jQuery("#page2").css('background','#ea5514');
   },
   
   viewPhoto : function(e) {
      //alert(e.src);
      if (!appStatus.photoViewActive) {
         jQuery("#photoview").fadeToggle(200);
         // set width of _rotated_ image to then height of the parent div (#photoview)
         jQuery("#photoview img").width(jQuery("#photoview").height());
         jQuery("#photoview img").attr("src",e.src);
         appStatus.photoViewActive = true;
      
         // show the close icon
         jQuery("#closeview").fadeToggle(200);
         // attach (only) once the closing functionality
         jQuery("#closeview").one("click", function() {
            jQuery("#photoview").fadeToggle(200);
            jQuery("#closeview").fadeToggle(200);
            appStatus.photoViewActive = false;
         });
      }
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
      appStatus.updateTexts();
   });
   
   // use pull-to-refresh on iOS devices
   appStatus.enablePTR = (device.platform == 'iOS');
   //appStatus.optochtLive = ( jQuery('#phpOVLiveVar').val() != '0' ); // read the php variable set in index.php (note: the php var is returned as string!)
      
   //Open the App with indicated section
   appStatus.disableSections();
   appStatus.switchSection(appStatus.sections.opensWith);
   
   //Setup location services
   appStatus.setupLocationServices();
   
   attachPinch('#plattegrond');

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
  /*
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
  */
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
      if (appStatus.extContent.optocht.live) {
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
      if (appStatus.extContent.uitslag.page1.live) {
         jQuery('#page1').pullToRefresh({
              tabDiv: "#live",
              ajaxDiv: "#livexmldoc",
              callback: function() {
                    var def = $.Deferred();
                    
                    setTimeout(function() {
                        def.resolve();      
                    }, 2000); 
                    // since on highest level attached to 'page1' only 1 callback function, so refresh depending on active tab
                    if (appStatus.activeTab == 4) appStatus.readUitslag(appStatus.extContent.uitslag.page1); 

                    return def.promise();
                }
            });
      }
      // attach pull-to-refresh to uitslag tab
      if (appStatus.extContent.uitslag.page2.live) {
         jQuery('#page2').pullToRefresh({
              tabDiv: "#vorigjaar",
              ajaxDiv: "#vorigjaar",
              callback: function() {
                    var def = $.Deferred();
                    
                    setTimeout(function() {
                        def.resolve();      
                    }, 2000); 
                    // since on highest level attached to 'page1' only 1 callback function, so refresh depending on active tab
                    if (appStatus.activeTab == 4) appStatus.readUitslag(appStatus.extContent.uitslag.page2); 

                    return def.promise();
                }
            });
      }
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
