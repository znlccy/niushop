(function($){
	$.fn.changeImg=function(options){
		
		var defalutes={
			'boxWidth':800,
			'changeLen':5,
			'changeSpend':300,
			'autoChange':true,
			'changeHandle':true,
			'autoTime':5000
		};
		
		var ops=$.extend(defalutes, options),
			$that=$(this);
		
		var _html='<a href="javascript:;" class="change-bnt prev-bnt">&nbsp;</a><a href="javascript:;" class="change-bnt next-bnt">&nbsp;</a>';
		
		
		var $changeBox=$that.find(".focus-img-con"),
			$changeUl=$changeBox.find("ul"),
			$changeLi=$changeBox.find("li"),
			$changePrev='',
			$changeNext='',
			_len=$changeLi.length,
			_timer='';
			
			
		$changeBox.width(ops.boxWidth);
		if(ops.changeHandle){
			$that.append(_html);
			$changePrev=$that.find(".prev-bnt");
			$changeNext=$that.find(".next-bnt");
		};
		
		
		var oWidth=$changeLi.eq(0).outerWidth(),
			bWidth=oWidth*_len,
			_handle=true;
		$changeUl.width(bWidth);
		
		
		$changePrev.on("click",function(){
			if(_handle){
				_handle=false;
				clearInterval(_timer);
				$changeUl.css('right','auto');
				for(var i=0; i<ops.changeLen; i++){
					var _li=$changeUl.find("li").eq(i).clone(true);
					$changeUl.append(_li);
				};
				$changeUl.stop().animate({
					'left':-oWidth*ops.changeLen
				},800,function(){
					for(var i=0; i<ops.changeLen; i++){
						$changeUl.find("li").eq(0).remove();
					};
					$changeUl.css('left',0);
					_handle=true;
				});
				autoPlay();
			};
		});
		
		var _len1=_len-1;
		$changeNext.on("click",function(){
			
			$changeUl.css('right',0);
			if(_handle){
				_handle=false;
				clearInterval(_timer);
				$changeUl.css('left','auto');
				for(var i=0; i<ops.changeLen; i++){
					var $_li=$changeUl.find("li").eq(_len1).clone(true);
					$changeUl.prepend($_li);
				};
				
				$changeUl.stop().animate({
					'right':-oWidth*(_len-ops.changeLen)
				},300,function(){
					for(var i=0; i<ops.changeLen; i++){
						$changeUl.find("li").eq(_len).remove();
					};
					_handle=true;
				});
				autoPlay();
			};
		});
		
		
		function autoPlay(){
			if(ops.autoChange){
				_timer=setInterval(function(){
					$changePrev.click();
				},ops.autoTime)
			}
		}
		autoPlay();
		
		
		return this;
	}
	
})(jQuery);
