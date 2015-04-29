/*! Copyright (c) 2009 Brandon Aaron (http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 *
 * Version: 3.0.2
 * 
 * Requires: 1.2.2+
 */

(function($) {

	var types = ['DOMMouseScroll', 'mousewheel'];

	$.event.special.mousewheel = {
		setup: function() {
			if ( this.addEventListener )
				for ( var i=types.length; i; )
					this.addEventListener( types[--i], handler, false );
			else
				this.onmousewheel = handler;
		},
		
		teardown: function() {
			if ( this.removeEventListener )
				for ( var i=types.length; i; )
					this.removeEventListener( types[--i], handler, false );
			else
				this.onmousewheel = null;
		}
	};

	$.fn.extend({
		mousewheel: function(fn) {
			return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
		},
		
		unmousewheel: function(fn) {
			return this.unbind("mousewheel", fn);
		}
	});


	function handler(e) {
		var args = [].slice.call( arguments, 1 ), delta = 0 /*, returnValue = true*/;
		
		e = $.event.fix(e || window.event);
		e.type = "mousewheel";
		
		if ( e.wheelDelta ) delta = e.wheelDelta/120;
		if ( e.detail     ) delta = -e.detail/3;
		
		// Add events and delta to the front of the arguments
		args.unshift(e, delta);

		return $.event.handle.apply(this, args);
	};
	
})(jQuery);