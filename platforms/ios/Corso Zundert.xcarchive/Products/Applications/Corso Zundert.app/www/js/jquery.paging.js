/*!
* jquery.paging.js
* version 1.0
* author: Ralph Braspenning
* https://...
*
* <description>
*
* <workings>
*
*
* <known bugs>
* - with debug on the debug print can cause strange behavior during inertial scrolling:
*   at a new touchstart the scroll pos jumps to the 'old' touchstart position
*
*/
(function( $ ) {

   $.fn.paging = function( options ) {
   
      var settings = $.extend( {
            'snaps' : '*',
            'offset' : 0,
            'duration' : 200,
            'easing' : 'swing',
        }, options);
   
      // Status vars keeping track of touch direction
      var scrollT, scrollL, startX, startY, initialTouchDir, enabled=0;
      var // The last several Y values are kept here
			 lastTops = [],
		
			 // The last several X values are kept here
			 lastLefts = [],
				
			 // lastDown will be true if the last scroll direction was down, false if it was up
			 lastDown,
				
			 // lastRight will be true if the last scroll direction was right, false if it was left
			 lastRight,
				
			 // For a new gesture, or change in direction, reset the values from last scroll
			 resetVertTracking = function(){
			   lastTops = [];
			   lastDown = null;
			 },
				
			 resetHorTracking = function(){
			   lastLefts = [];
			   lastRight = null;
			 };
      
      return this.each(function() {
      
         var elem = $(this);
         
         elem.on('touchstart', function(e) {

            enabled = elem.children(settings.snaps).filter(function() {
                  return $(this).css('visibility') == 'visible';
                }).length > 1;
            
            if (enabled) {
               scrollT = elem.scrollTop();
		         scrollL = elem.scrollLeft();
		         startY = e.originalEvent.touches[ 0 ].pageY;
		         startX = e.originalEvent.touches[ 0 ].pageX;
		
		         // Reset the distance and direction tracking
		         initialTouchDir = '';
		         resetVertTracking();
		         resetHorTracking();
		      } else {
		         if (settings.debug) settings.debugText('paging disabled');
		      }
		      
         });
         
         elem.on('touchmove', function(e) {
            if (enabled) {
               var ty = scrollT + startY - e.originalEvent.touches[ 0 ].pageY,
			          tx = scrollL + startX - e.originalEvent.touches[ 0 ].pageX,
			          dy = Math.abs(ty-scrollT),
			          dx = Math.abs(tx-scrollL),
			          sl = elem.scrollLeft() - scrollL,
			          down = ty >= ( lastTops.length ? lastTops[ 0 ] : 0 ),
			          right = tx >= ( lastLefts.length ? lastLefts[ 0 ] : 0 );
			 
		         if (initialTouchDir == '') initialTouchDir = ( (dx>5 && dy<10) ? 'page' : 'scroll' ); 
		         // monitor the actual scrollLeft: if initialTouchDir = scroll, we are not supposed to scroll horizontally
		         // ...but this can/might happen in case the page (within 'this') is not scrollable, in which case the
		         // default scrolling events are sent reach 'this' and it starts scrolling horizontally
		         if (initialTouchDir == 'scroll' && sl!=0 ) initialTouchDir = 'page'; // reset to paging since we are scrolling hor.
		
		         // If down and lastDown are inequal, the y scroll has changed direction. Reset tracking.
		         if( lastDown && down !== lastDown ){
		            resetVertTracking();
		         }
							
		         // If right and lastRight are inequal, the x scroll has changed direction. Reset tracking.
		         if( lastRight && right !== lastRight ){
		            resetHorTracking();
		         }
							
		         // remember the last direction in which we were headed
		         lastDown = down;
		         lastRight = right;		
		
		         if (initialTouchDir == 'page') {					
			         e.preventDefault();  // prevent vertical scrolling of the pages while making a side swipe
		            //elem.scrollTop(ty);
		            elem.scrollLeft(tx);
		         }
		
		         lastTops.unshift( ty );
		         lastLefts.unshift( tx );
						
		         if( lastTops.length > 3 ){
		            lastTops.pop();
		         }
		         if( lastLefts.length > 3 ){
		            lastLefts.pop();
	            }
               var pages = elem.children(settings.snaps).filter(function() {
                              return $(this).css('visibility') == 'visible';
                           }).length;
               if (settings.debug) settings.debugText("canvas scroll: " + dx + ',' + dy + '|' + initialTouchDir + '|' + pages);
            }       
         });
         
         elem.on('touchend', function(e) {
            if (enabled && initialTouchDir == 'page') {
               var searchDir = (lastLefts[0] - lastLefts[1] >= 0 ? 1 : -1);
               var scrollingEl = this;
               var matchingEl = null, matchingDist = 1000000;

               jQuery(scrollingEl).find(settings.snaps).each(function() {
                  var snappingEl = this,
                      dist = searchDir * (snappingEl['offsetLeft'] - scrollingEl['scrollLeft']);

                  if (dist > 0 && dist < matchingDist) {
                     matchingEl = snappingEl;
                     matchingDist = dist;
                  }
               });
         
               if (matchingEl) {
                  var endScroll = matchingEl['offsetLeft'], // + settings.offset,
                      animateProp = {};
                  animateProp['scrollLeft'] = endScroll;
                  if (jQuery(scrollingEl)['scrollLeft']() != endScroll) {
                     jQuery(scrollingEl).animate(animateProp, settings.duration, settings.easing, settings.snapComplete);
                  }
            
                  if (settings.debug) settings.debugText("snap to " + matchingEl['id'] + "|" + searchDir);
               } else if (settings.debug) settings.debugText("no matching element");
         
            }
         });      
      
      });
   
   };

})( jQuery );
