define("util/draw",["jquery"],function(e){var t,n;return t=function(e){function t(e){return Math.PI/180*e}function n(e,t){var n=[0,0],r=["M"+n.join(","),"L"+[n[0],n[1]+e].join(","),"L"+[n[0]+e/2,n[1]+e/2].join(","),"L"+n.join(",")],i=n[1]+e/2;t[0].setAttribute('refX',n[0]),t[0].setAttribute('refY',i),t[0].setAttribute('markerWidth',2*i),t[0].setAttribute('markerHeight',2*i),t.find("path").attr({d:r.join(" ")})}function r(r){r=e.extend({radius:0,margin:0,sAngle:0,eAngle:0,arrowSize:2.5,arrowObj:"#x-pushfresh-arrow",pathObj:".x-pullfresh-path"},r);var i=r.radius+r.margin+r.radius*Math.sin(t(r.eAngle)),s=r.radius+r.margin-r.radius*Math.cos(t(r.eAngle)),o=r.radius+r.margin+r.radius*Math.sin(t(r.sAngle)),u=r.radius+r.margin-r.radius*Math.cos(t(r.sAngle)),a=[["M"+o,u].join(",")];a.push([["A"+r.radius,r.radius].join(","),0,[r.eAngle-r.sAngle>180?1:0,1].join(","),[i,s].join(",")].join(" ")),e(r.pathObj).attr("d",a.join(" ")),n(r.arrowSize,e(r.arrowObj))}return{drawArc:r}}(e),n=function(e){function t(e,t,n,r,i,s,o,u,a){typeof t=="string"&&(t=parseInt(t,10)),typeof n=="string"&&(n=parseInt(n,10)),typeof r=="string"&&(r=parseInt(r,10)),typeof i=="string"&&(i=parseInt(i,10)),s=s!==undefined?s:console.log,o=o!==undefined?o:1,u=u!==undefined?u:Math.PI/8,a=a!==undefined?a:10;var f=typeof s!="function"?console.log:s,l,c,h,p,d,v,m,g;l=Math.atan2(i-n,r-t),c=Math.abs(a/Math.cos(u)),o&1&&(h=l+Math.PI+u,d=r+Math.cos(h)*c,v=i+Math.sin(h)*c,p=l+Math.PI-u,m=r+Math.cos(p)*c,g=i+Math.sin(p)*c,f(e,d,v,r,i,m,g,s)),o&2&&(h=l+u,d=t+Math.cos(h)*c,v=n+Math.sin(h)*c,p=l-u,m=t+Math.cos(p)*c,g=n+Math.sin(p)*c,f(e,d,v,t,n,m,g,s))}function n(e,n,r,i,s,o,u,a,f,l,c){a=a!==undefined?a:console.log,f=f!==undefined?f:1,l=l!==undefined?l:Math.PI/8,c=c!==undefined?c:10;var h,p,d,v,m,g=typeof a!="function"?console.log:a;g(e,n,r,i,s,o,u),f&1&&(h=Math.cos(s)*i+n,p=Math.sin(s)*i+r,d=Math.atan2(n-h,p-r),u?(v=h+10*Math.cos(d),m=p+10*Math.sin(d)):(v=h-10*Math.cos(d),m=p-10*Math.sin(d)),t(e,h,p,v,m,a,2,l,c)),f&2&&(h=Math.cos(o)*i+n,p=Math.sin(o)*i+r,d=Math.atan2(n-h,p-r),u?(v=h-10*Math.cos(d),m=p-10*Math.sin(d)):(v=h+10*Math.cos(d),m=p+10*Math.sin(d)),t(e,h,p,v,m,a,2,l,c))}return{drawArrow:t,drawArcedArrow:n}}(e),{SVGUtil:t,CanvasUtil:n}})
,define("h5/pullrefresh",["jquery", "util/draw", "http://cdn.bootcss.com/blueimp-md5/2.6.0/js/md5.min.js"],function(e, t, md5) {
	var animations={SHORTCUTS:{a:'animate',an:'attributeName',at:'animateTransform',c:'circle',da:'stroke-dasharray',os:'stroke-dashoffset',f:'fill',lc:'stroke-linecap',rc:'repeatCount',sw:'stroke-width',t:'transform',v:'values'},setSvgAttribute:function(ele,k,v){ele.setAttribute(this.SHORTCUTS[k]||k,v)},easeInOutCubic:function(t,c){t/=c/2;if(t<1)return 1/2*t*t*t;t-=2;return 1/2*(t*t*t+2)},android:function(ele){var t=this;t.stoped=false;var rIndex=0;var rotateCircle=0;var startTime;var svgEle=ele.querySelector('g');var circleEle=ele.querySelector('circle');var bgcolor=['#FF4136','#0074D9','#FF851B','#B10DC9','#FFDC00','#2ECC40','#FF851B'];function run(){if(t.stoped)return;var v=t.easeInOutCubic(Date.now()-startTime,650);var scaleX=1;var translateX=0;var dasharray=(188-(58*v));var dashoffset=(182-(182*v));if(rIndex%2){scaleX=-1;translateX=-64;dasharray=(128-(-58*v));dashoffset=(182*v)}var rotateLine=[0,-101,-90,-11,-180,79,-270,-191][rIndex];t.setSvgAttribute(circleEle,'da',Math.max(Math.min(dasharray,188),128));t.setSvgAttribute(circleEle,'os',Math.max(Math.min(dashoffset,182),0));t.setSvgAttribute(circleEle,'t','scale('+scaleX+',1) translate('+translateX+',0) rotate('+rotateLine+',32,32)');rotateCircle+=4.1;if(rotateCircle>359)rotateCircle=0;t.setSvgAttribute(svgEle,'t','rotate('+rotateCircle+',32,32)');ele.style.stroke=bgcolor[rIndex];if(v>=1){rIndex++;if(rIndex>7)rIndex=0;startTime=Date.now()}window.requestAnimationFrame(run)}animations.run=function(){t.stoped=false;startTime=Date.now();run()}},run:function(){},stop:function(){this.stoped=true}};
	var pullrefresh = {
		options: {refresh:1,wrapper:".x-pullfresh-wrapper",canvas:".x-pullfresh-canvas",svg:".x-pullfresh-svg",container:'.x-pullfresh-container',circle:{originX:16,originY:16,radius:12},arrow:{angle:90,lineLength:3},moveOffset:50,moveRate:2.5},
		$container: null,
		init: function(opt) {var t=this;t.options=e.extend(!0,t.options,opt||{});t.addPullTip(),t.wrapper=e(t.options.wrapper),t.svg=e(t.options.svg),t.beginPos=0,t.currPos=0,t.endEvents="webkitTransitionEnd transitionend",t.addEventListener()},
		drawRotate: function(e,t){var n=this,r=0,i=e*360;n.svg.length?n.drawRotateSVG(r,i,t):n.canvas.length&&n.drawRotateCanvas(r,i,t)},
		drawRotateSVG: function(e,n,r){var i=this,s=i.options;t.SVGUtil.drawArc({margin:8,radius:s.circle.radius,sAngle:e+35,eAngle:n+35,arrowSize:s.arrow.lineLength*r*2.5,arrowObj:i.svg.find("#x-pullfresh-arrow"),pathObj:i.svg.find(".x-pullfresh-path")})},
		onTouchStart: function(e){var t=this;t.beginPos=e.touches[0].pageY,t.isFull=!1,t.currPos=0},
		onTouchMove: function(e){var t=this,n=e.touches[0].pageY-t.beginPos,r;t.isFull=!1;if(window.scrollY===0){if(n>0){e.preventDefault();e.stopPropagation()}if(n>30){t.currPos=n*.2+n*((t.options.moveOffset-t.currPos)/t.options.moveOffset)/10,r=Math.floor(t.currPos*1e3/t.options.moveOffset)/1e3;if(r>1||r<0)r=1,t.isFull=!0;r>=.9&&(t.isFull=!0),t.drawRotate(r>.9?.9:r,r),n=t.options.moveOffset*t.options.moveRate*r,t.wrapper.css({"-webkit-transition":"","-webkit-transform":"translate3d(0,"+n+"px,0)",transform:"translate3d(0,"+n+"px,0)",opacity:1})}}},
		onTouchEnd: function(e){this.isFull?this.onLoad(1,!0):this.hideLoading()},
		onScroll: function(){},
		showLoading: function(isRefresh){var t=this;t.isLoading=true;if(isRefresh){t.wrapper.attr('style','transform: translate3d(0px, 125px, 0px);opacity: 1').html('<div class="x-pullfresh-loading"><svg id="pullrefresh_loading"viewBox="0 0 64 64"style="stroke:#4b8bf4;fill:none;width:40px;height:40px;"><g><circle stroke-width="4"r="20"cx="32"cy="32"></circle></g></svg></div>');animations.android(t.wrapper.find('svg')[0]);animations.run();}else{t.upTip.addClass('pullfresh-loading')}},
		hideLoading: function(){var t=this,$loading=t.wrapper.find('.x-pullfresh-loading');t.isLoading=false;t.upTip.removeClass('pullfresh-loading');$loading.css('-webkit-transform','scale(0,0)');setTimeout(function(){if(!t.isLoading){animations.stop();t.wrapper.find('.x-pullfresh-loading').css('-webkit-transform','');t.wrapper.attr('style','').html('<div class="x-pullfresh-loading"><svg class="x-pullfresh-svg"><marker id="x-pullfresh-arrow" orient="auto" markerUnits="userSpaceOnUse"><path/></marker><path class="x-pullfresh-path" marker-end="url(#x-pullfresh-arrow)"fill="none"/></svg></div>');t.svg=t.wrapper.find('.x-pullfresh-svg')}},1000);},
		addPullTip: function(){e('body').append('<div class="x-pullfresh-wrapper"></div>');e('.x-pullfresh-more').html('<div class="pullfresh-up"><div class="loader"><span></span><span></span><span></span><span></span></div><div class="pullfresh-label">没有更多数据了</div></div>');this.upTip=e('.x-pullfresh-more')},
		handleEvent: function(e){switch(e.type){case'scroll':this.onScroll(e);break;case'touchstart':this.onTouchStart(e);break;case'touchmove':this.onTouchMove(e);break;case'touchend':this.onTouchEnd(e);break}},
		addEventListener: function(){if(this.options.refresh){var ele = document.body.querySelector('.container>.content');ele.addEventListener("touchstart",this,!1),ele.addEventListener("touchmove",this,!1),ele.addEventListener("touchend",this,!1)}window.addEventListener('scroll',this,!1)},
		onLoad: function(page){var t=this;t.isLoading=true;t.showLoading(page==1);setTimeout(function(){t.isLoading=false;t.hideLoading()},2000)},
		timer:0,
		triggerTop: 0,
		getTriggerTop: function(){return this.$container.offset().top + this.$container.outerHeight() - document.documentElement.clientHeight - 200},
		setMore: function(hasMore,page){var t=this;t.hideLoading();if(hasMore){t.upTip.removeClass('no-more');window.clearInterval(t.timer);t.triggerTop=t.getTriggerTop();t.timer=setInterval(function(){t.triggerTop=t.getTriggerTop()},3000)}else{t.upTip.addClass('no-more');window.clearInterval(t.timer)}},
		isLoading: false,
		lastCache: {},
		id: md5(window.location.host+window.location.pathname),
		info: null,
		localInfo: function(guid, data){
			var t = this;
			
			if(!t.info){
				t.info = t.getCache(t.id);
				if(!t.info){
					t.info = {key: '', items: {}};
				}
			}
			
			// 读取数据
			if(!guid){
				return t.info;
			}
			
			// 设置数据
			var cache = t.info.items[guid];
			if(data == undefined){
				return !cache ? {t: 0, p: 1} : cache;
			}else if(data == null){
				delete t.info.items[guid];
				t.setCache(t.id, t.info);
			}else{
				t.info.items[guid] = data;
				t.setCache(t.id, t.info);
			}
		},
		doRefresh: function(ajax){
			var t = this;
			if(t.$container == null){t.localInfo();t.init({refresh: typeof ajax.refresh == 'undefined' ? 1 : ajax.refresh})}
			if(!ajax || ajax === true) return this.onLoad(1, ajax === true);
			t.$container = $(ajax.container ? ajax.container : 'body');
			
			// 保存上次信息
			if(t.lastCache.guid){
				t.info.key = ajax.cacheKey;
				t.localInfo(t.lastCache.guid, {t: t.lastCache.scrollTop, p: t.lastCache.page});
			}
			t.lastCache = {scrollTop: document.body.scrollTop};

			var request = null, guid = null;
			if(ajax.cacheKey){
				guid = t.getGuid(ajax.url, ajax.data);
				t.info.key = ajax.cacheKey;
				t.localInfo(t.lastCache.guid, {t: t.lastCache.scrollTop, p: t.lastCache.page});
			}
			
			document.body.scrollTop = ajax.scrollTop ? ajax.scrollTop : 0;
			t.setMore(typeof ajax.hasMore == 'undefined' ? true : ajax.hasMore);
			
			t.onLoad = function(page, isRefresh){
				if(t.isLoading && request){
					request.abort();
				}
				
				t.showLoading(page == 1);
				
				if(ajax.data.size){
					ajax.data.offset = (page - 1) * ajax.data.size;
				}else{
					ajax.data.page = page;
				}
				
				if(ajax.cacheKey){
					if(page == 1){
						guid = t.getGuid(ajax.url, ajax.data);
					}
					
					if(isRefresh === true){
						t.clearCache(guid);
					}else{
						var a = t.getCache(guid+'_'+page);
						if(a){
							ajax.page = page;
							ajax.hasMore = ajax.success.apply(ajax.container, [a, page, ajax.data.size]);
							t.setMore(ajax.hasMore, page);
							
							t.lastCache.guid = guid;
							t.lastCache.key = ajax.cacheKey;
							t.lastCache.page = page;
							t.info.key = ajax.cacheKey;
							t.localInfo(guid, {t: document.body.scrollTop, p: page});
							return;
						}
					}
				}
				
				if(ajax.debug){
					setTimeout(function(){
						ajax.page = page;
						ajax.hasMore = ajax.success.apply(ajax.container, [[], page, ajax.data.size]);
						t.setMore(ajax.hasMore, page);
						
						if(ajax.cacheKey){
							t.setCache(guid + '_' + page, a);
							t.lastCache.guid = guid;
							t.lastCache.key = ajax.cacheKey;
							t.lastCache.page = page;
							t.info.key = ajax.cacheKey;
							t.localInfo(guid, {t: document.body.scrollTop, p: page});
						}
						t.hideLoading();
					}, 3000);
				}else{
					request = $.ajax({
						url: ajax.url,
						data: ajax.data,
						dataType: ajax.dataType,
					    ifModified: true,
					    timeout: 8000,
						success: function(a){
							ajax.page = page;
							ajax.hasMore = ajax.success.apply(ajax.container, [a, page, ajax.data.size]);
							t.setMore(ajax.hasMore, page);
							
							if(ajax.cacheKey){
								t.setCache(guid + '_' + page, a);
								t.lastCache.guid = guid;
								t.lastCache.key = ajax.cacheKey;
								t.lastCache.page = page;
								t.info.key = ajax.cacheKey;
								t.localInfo(guid, {t: document.body.scrollTop, p: page});
							}
						},
						error: function(a, b, c){
							if(typeof ajax.error == 'function'){
								ajax.error(a, b, c);
							}
						},
						complete: function(a, b, c){
							t.hideLoading();
							if(typeof ajax.error == 'function'){
								ajax.error(a, b, c);
							}
						}
					});
				}
			}
			
			var data = {p: ajax.page ? ajax.page : 1, t: ajax.scrollTop};
			if(ajax.cacheKey){
				data = t.localInfo(guid);
			}
			for(var i=0; i<data.p; i++){
				t.onLoad(i+1);
				document.body.scrollTop = data.t;
			}
			
			var setHistory = function(){
				t.info.key = ajax.cacheKey;
				t.localInfo(t.lastCache.guid, {t: document.body.scrollTop, p: ajax.page});
			}
			$(window).unbind('beforeunload', setHistory);
			
			if(ajax.cacheKey){
				$(window).bind('beforeunload', setHistory);
			}

			t.onScroll = function(e){
				t.lastCache.scrollTop = ajax.scrollTop = document.body.scrollTop;
				if(ajax.hasMore && !t.isLoading && document.body.scrollTop > t.triggerTop){
					t.onLoad(ajax.page+1)
				}
			}
			
			return ajax;
		},
		parseURL: function(url){var a=document.createElement('a');a.href=url;return{href:url,protocol:a.protocol.replace(':',''),host:a.hostname,port:a.port,query:a.search,params:(function(){var ret={},seg=a.search.replace(/^\?/,'').split('&'),len=seg.length,i=0,s;for(;i<len;i++){if(!seg[i]){continue}s=seg[i].split('=');ret[s[0]]=s[1]}return ret})(),file:(a.pathname.match(/\/([^\/?#]+)$/i)||[,''])[1],hash:a.hash.replace('#',''),path:a.pathname.replace(/^([^\/])/,'/$1'),relative:(a.href.match(/tps?:\/\/[^\/]+(.+)/)||[,''])[1]}},
		getGuid: function(url, data){
			var urlData = this.parseURL(url);
			if(data){
				for(var field in data){
					urlData.params[field] = data[field];
				}
			}
			
			var params = '';
			for(var field in urlData.params){
				if(field == 'page' || field == 'offset' || field == 'size'){
					continue;
				}
				params += (params == '' ? '?' : '&') + field + '=' + urlData.params[field];
			}
			
			url = urlData.protocol + '://'+urlData.host+urlData.path + params;
			return md5(url);
		},
		setCache: function(key, value){
			sessionStorage.setItem(key, JSON.stringify(value));
		},
		getCache: function(key){
			var json = sessionStorage.getItem(key);
			if(json){
				return eval("("+json+")");
			}
			return;
		},
		clearCache: function(guid){
			var t = this, data = t.localInfo(guid);
			if(data){
				t.localInfo(guid, null);
				for(var p=1; p<=data.p; p++){
					sessionStorage.removeItem(guid+'_'+p)
				}
			}
		}
	};
	
	pullrefresh.localInfo();
	return pullrefresh;
})