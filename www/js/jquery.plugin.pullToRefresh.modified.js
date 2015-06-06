/*!
* jquery.plugin.pullToRefresh.js
* version 1.0
* author: Damien Antipa
* https://github.com/dantipa/pull-to-refresh-js
*/
(function( $ ){

	$.fn.pullToRefresh = function( options ) {

		var isTouch = !!('ontouchstart' in window),
			cfg = $.extend(true, {
			  message: {
				pull: 'Sleep naar beneden',            // RB: changed text
				release: 'Laat los',
				loading: 'Laden'
				}
			}, options),
			html = '<div class="pull-to-refresh">' +
				'<div class="icon"></div>' +
				'<div class="message">' +
					'<i class="arrow"></i>' +
					'<i class="spinner"></i>' +
					'<span class="pull">' + cfg.message.pull + '</span>' +
					'<span class="release">' + cfg.message.release + '</span>' +
					'<span class="loading">' + cfg.message.loading + '</span>' +
				  '</div>' +
				'</div>';



		return this.each(function() {
			if (!isTouch) {
				//return;
			}
			
			//$(this).find('#live').prepend(html);       // RB: added, to append live tab (not all)
			$(this).find(cfg.tabDiv).prepend(html);     // RB: added, to append nieuws tab
			
			var e = $(this),                           // RB: changed from e = $(this).prepend(html)
			    //content = e.find('#livexmldoc'),       // RB: changed div name
				content = e.find(cfg.ajaxDiv),
				ptr = e.find('.pull-to-refresh'),
				arrow = e.find('.arrow'),
				spinner = e.find('.spinner'),
				pull = e.find('.pull'),
				release = e.find('.release'),
				loading = e.find('.loading'),           
				ptrTotalHeight = ptr.outerHeight(),    // RB: added to including padding
				ptrHeight = ptr.height(),
				arrowDelay = ptrTotalHeight / 3 * 2,
				isActivated = false,
				isLoading = false;

			content.on('touchstart', function (ev) {
				if (e.scrollTop() === 0) { // fix scrolling
					e.scrollTop(1);
				}
				
			}).on('touchmove', function (ev) {
				var top = e.scrollTop(),
					deg = 180 - (top < -ptrTotalHeight ? 180 : // degrees to move for the arrow (starts at 180¡ and decreases)
						  (top < -arrowDelay ? Math.round(180 / (ptrTotalHeight - arrowDelay) * (-top - arrowDelay)) // RB: changed to ptrTotalHeight, also row above
						  : 0));

				if (isLoading) { // if is already loading -> do nothing
					return true;
				}
				
				arrow.show();
				arrow.css('transform', 'rotate('+ deg + 'deg)'); // move arrow

				spinner.hide();

				if (-top > ptrTotalHeight) { // release state, RB: changed to ptrTotalHeight
					release.css('opacity', 1);
					pull.css('opacity', 0);
					loading.css('opacity', 0);

					isActivated = true;
				} else if (top > -ptrTotalHeight) { // pull state, RB: changed to ptrTotalHeight
					release.css('opacity', 0);
					loading.css('opacity', 0);
					pull.css('opacity', 1);

					isActivated = false;
				}
				
			}).on('touchend', function(ev) {
				var top = e.scrollTop();
				
				//RB: debug: document.getElementById("livexmldoc").innerHTML= top + '<br>' + document.getElementById("livexmldoc").innerHTML;

				if (isActivated) { // loading state
					isLoading = true;
					isActivated = false;

					release.css('opacity', 0);;
					pull.css('opacity', 0);
					loading.css('opacity', 1);
					arrow.hide();
					spinner.show();

					ptr.css('position', 'static');
					
					cfg.callback().done(function() {
						ptr.animate({
							height: 10
						}, 'fast', 'linear', function () {
							ptr.css({
								position: 'absolute',
								height: ptrHeight
							});
							isLoading = false;
						});
					});
					
				}
			});
			
			// RB: Added custom event to have same 'loading' appearance when button is pressed
			e.on('refresh', function(ev) {
			    	isLoading = true;
					isActivated = false;

					release.css('opacity', 0);;
					pull.css('opacity', 0);
					loading.css('opacity', 1);
					arrow.hide();
					spinner.show();

					ptr.css('position', 'static');
					
					cfg.callback().done(function() {
						ptr.animate({
							height: 10
						}, 'fast', 'linear', function () {
							ptr.css({
								position: 'absolute',
								height: ptrHeight
							});
							isLoading = false;
						});
					});
					
			});
		});

	};
})( jQuery );
