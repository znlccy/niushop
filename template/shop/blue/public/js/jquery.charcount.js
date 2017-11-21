 
(function($) {

	$.fn.charCount = function(options){
	  
		// default configuration properties
		var defaults = {	
			allowed: 140,		
			warning: 25,
			css: 'counter',
			counterElement: 'span',
			counterContainerID:'',
			cssWarning: 'warning',
			cssExceeded: 'exceeded',
			firstCounterText: '',
			endCounterText: '',
			errorCounterText: '',
			errortype: 'positive'	// positive or negative
		}; 
		var options = $.extend(defaults, options); 
		
		function calculate(obj){
			var count = $(obj).val().length;
			var counterText = options.firstCounterText;
			var _css = '';
			containerObj = $("#"+options.counterContainerID);
			var available = options.allowed - count;
			if(available <= options.warning && available >= 0){
				_css = options.cssWarning;
			}
			if(available < 0){
				if (options.errortype == 'positive')available = -available;
				counterText = options.errorCounterText;
				_css = options.cssExceeded;
			} else {
				counterText = options.firstCounterText;
			}
			$(containerObj).children().html(counterText +'<em class="'+ _css +'">'+ available +'</em>'+ options.endCounterText);
		};
		this.each(function() {
			$("#"+options.counterContainerID).append('<'+ options.counterElement +' class="' + options.css + '"></'+ options.counterElement +'>');
			calculate(this);
			$(this).keyup(function(){calculate(this)});
			$(this).change(function(){calculate(this)});
			$(this).focus(function(){calculate(this)});
		});
	};

})(jQuery);
