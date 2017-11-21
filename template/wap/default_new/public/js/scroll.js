/**
 * 滚动图js插件
 * 
 */
// JavaScript Document
    //entra92-45
    (function(global){
        var g=[[]],d='http://static.gtimg.com',m={},blankjs="",grays={},mdebug = location.href.indexOf("mdebug=1")!=-1;
        m.alias = {};
        var a={},p={},c=[];
        g["1"]=["\/js\/version\/201403\/lazyload.201403071104.js","\/js\/version\/201306\/loadscript.201306251923.js","\/js\/version\/201309\/url.201309101641.js","\/js\/version\/201405\/wd.scroll.201405261106.js","\/js\/version\/201402\/zepto.201402121740.js"];
        g["0"]=["\/js\/version\/201405\/wd.shop.global.201405261106.js"];
        c = [['lazyload',1,0],['loadscript',1,1],['url',1,2],['wd.scroll',1,3],['wd.shop.global',0,0],['zepto',1,4]];
        if(typeof(JSON)!="undefined" && window.localStorage){
            for(var key in localStorage){
                if(/^_m_/.test(key)){
                    var store = JSON.parse(localStorage.getItem(key));
                    var i = key.substr(3);
                    if((new Date()).getTime()>store.cacheTime){
                        continue;
                    }
                    var _m=getModuleMap(i);
                    if(!_m || _m.group==0){continue;}
                    var curPath=g[_m.group][_m.groupid];
                    if((d+curPath)==store.path){
                        g[0].push(curPath);
                        g[_m.group][_m.groupid]=blankjs;
                        c[_m.index]=[_m.id,0,g[0].length-1];
                    }
                }
            }
        }
        function getModuleMap(id){
            for(var i=0;i<c.length;i++){
                if(c[i][0]==id){
                    return {"id":id,"index":i,"group":c[i][1],"groupid":c[i][2]};
                }
            }
            return "";
        }
        function getCombUrl(list){
            var a=[];
            for(var i=0;i<list.length;i++){
                if(list[i]){
                    a.push(list[i]);
                }
            }
            return a.length>1?(d+"/c/="+a.join(",")):(d+a[0]);
        }
        for(var i=0;i<c.length;i++){
            var surl=d+g[c[i][1]][c[i][2]];
            var furl=getCombUrl(g[c[i][1]]);//鍚堝苟鍖呭湴鍧
            a[c[i][0]]=surl;
            p[c[i][0]]=(c[i][1]==0)?surl:furl;
        }
        m.alias=mdebug?a:p;
        m.moduleURI=a;
        m.vars = {
            jquery : 'zepto'
        };
        global._moduleConfig = m;
        function gray(m){
        }
    })(this);
	
	if(typeof JSON!=="object"){JSON={};}(function(){function f(n){return n<10?"0"+n:n;}if(typeof Date.prototype.toJSON!=="function"){Date.prototype.toJSON=function(key){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+f(this.getUTCMonth()+1)+"-"+f(this.getUTCDate())+"T"+f(this.getUTCHours())+":"+f(this.getUTCMinutes())+":"+f(this.getUTCSeconds())+"Z":null;};String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(key){return this.valueOf();};}var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={"\b":"\\b","\t":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},rep;function quote(string){escapable.lastIndex=0;return escapable.test(string)?'"'+string.replace(escapable,function(a){var c=meta[a];return typeof c==="string"?c:"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4);})+'"':'"'+string+'"';}function str(key,holder){var i,k,v,length,mind=gap,partial,value=holder[key];if(value&&typeof value==="object"&&typeof value.toJSON==="function"){value=value.toJSON(key);}if(typeof rep==="function"){value=rep.call(holder,key,value);}switch(typeof value){case"string":return quote(value);case"number":return isFinite(value)?String(value):"null";case"boolean":case"null":return String(value);case"object":if(!value){return"null";}gap+=indent;partial=[];if(Object.prototype.toString.apply(value)==="[object Array]"){length=value.length;for(i=0;i<length;i+=1){partial[i]=str(i,value)||"null";}v=partial.length===0?"[]":gap?"[\n"+gap+partial.join(",\n"+gap)+"\n"+mind+"]":"["+partial.join(",")+"]";gap=mind;return v;}if(rep&&typeof rep==="object"){length=rep.length;for(i=0;i<length;i+=1){if(typeof rep[i]==="string"){k=rep[i];v=str(k,value);if(v){partial.push(quote(k)+(gap?": ":":")+v);}}}}else{for(k in value){if(Object.prototype.hasOwnProperty.call(value,k)){v=str(k,value);if(v){partial.push(quote(k)+(gap?": ":":")+v);}}}}v=partial.length===0?"{}":gap?"{\n"+gap+partial.join(",\n"+gap)+"\n"+mind+"}":"{"+partial.join(",")+"}";gap=mind;return v;}}if(typeof JSON.stringify!=="function"){JSON.stringify=function(value,replacer,space){var i;gap="";indent="";if(typeof space==="number"){for(i=0;i<space;i+=1){indent+=" ";}}else{if(typeof space==="string"){indent=space;}}rep=replacer;if(replacer&&typeof replacer!=="function"&&(typeof replacer!=="object"||typeof replacer.length!=="number")){throw new Error("JSON.stringify");}return str("",{"":value});};}if(typeof JSON.parse!=="function"){JSON.parse=function(text,reviver){var j;function walk(holder,key){var k,v,value=holder[key];if(value&&typeof value==="object"){for(k in value){if(Object.prototype.hasOwnProperty.call(value,k)){v=walk(value,k);if(v!==undefined){value[k]=v;}else{delete value[k];}}}}return reviver.call(holder,key,value);}text=String(text);cx.lastIndex=0;if(cx.test(text)){text=text.replace(cx,function(a){return"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4);});}if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,""))){j=eval("("+text+")");return typeof reviver==="function"?walk({"":j},""):j;}throw new SyntaxError("JSON.parse");};}}());var modulejs,require,define;(function(global){var mod,cfg,_modulejs,_define,_require;var version="1.0.2";var isCache=true;var cacheTime=60*60*24*30*1000;cfg={debug:location.href.indexOf("mdebug=1")!=-1?true:false,alias:{},cssAlias:{},moduleURI:{},vars:{},uris:{},modules:{},callback:[],actModules:{},cacheLoad:{},cacheDel:{},deps:{},events:{},timing:{}};_modulejs.config=config;config(global["_moduleConfig"]?global._moduleConfig:cfg);global["_moduleConfig"]=cfg;require=_require;define=_define;modulejs=_modulejs;on("module_ready",function(id,fromeCache){cfg.callback=cleanArray(cfg.callback);var init=cfg.callback,l=init.length;for(var i=0;i<l;i++){if(init[i]&&checkDeps(init[i].dependencies)){var cb=init[i].factory;var deps=init[i].dependencies;var mods=[];var allDeps=[];var m;for(var j=0;j<deps.length;j++){var m=_require(deps[j]);allDeps.push(m.dependencies);mods.push(m);}
    debug("callback_is_run=start",cb.toString(),cfg.callback);init[i]=null;cb.apply(null,mods);cfg.debug&&emit("callback_is_run",cb.toString().replace(/[\r\n]/g,""));}}});on("module_require",function(id){cfg.actModules[id]=cfg.actModules[id]?(cfg.actModules[id]+1):1;});on("module_cacheDel",function(id){cfg.cacheDel[id]=1;});on("cache_load",function(m){});on("module_loss_alias",function(id){console.error("module_loss_alias:"+id);});var cacheNow=new Date();_loadCache();cfg.timing['loadcache']=new Date()-cacheNow;function _loadCache(){if(!_useCache()){return false;}
    localStorage.removeItem("_modules");for(var key in localStorage){if(/^_m_/.test(key)){var store=JSON.parse(localStorage.getItem(key));var i=key.substr(3);var now=(new Date()).getTime();var oneDay=24*3600*1000;if(cfg.alias[i]){if(_getModuleURI(i)!=store.path||store.deps.join(",").indexOf("{")>-1){emit("module_cacheDel",i);localStorage.removeItem(key);continue;}}else{if(now>store.cacheTime){emit("module_cacheDel",i);localStorage.removeItem(key);continue;}}
        if(store.cacheTime-now<oneDay){store.cacheTime=(new Date()).getTime()+cacheTime;try{localStorage.removeItem(key);localStorage.setItem(key,JSON.stringify(store));}catch(e){}}
        cfg.cacheLoad[store.id]=store;emit("cache_load",store);}}}
    function _getModuleURI(id){return cfg.moduleURI[id]?cfg.moduleURI[id]:cfg.alias[id];}
    function _useCache(id,deps,factory){if(typeof(JSON)=="undefined"){return false;}
        if(!isCache){return false;}
        if(!(JSON&&window.localStorage)){return false;}
        if(cfg.debug){return false;}
        var agent=navigator.userAgent.toLowerCase();if(agent.indexOf("msie")>0){var m=agent.match(/msie\s([\d\.]+);/i);if(m&&m.length>=2&&parseInt(m[1])<=6){return false;}}
        if(id&&id.indexOf("_")==0){return false;}
        if(factory&&factory.toString().indexOf("_cacheThisModule_")<0){return false;}
        return true;}
    function _cacheModule(id,deps,factory){var key="_m_"+id;var _t=localStorage.getItem(key);var ms=_t?JSON.parse(_t):{};ms={"id":id,"deps":deps,"factory":factory.toString(),"path":_getModuleURI(id),"cacheTime":(new Date()).getTime()+cacheTime};try{localStorage.removeItem(key);localStorage.setItem(key,JSON.stringify(ms));emit("module_cached",id);}catch(e){}}
    function _define(id,deps,factory){if(arguments.length===2){factory=deps;deps=null;}
        deps=isType("Array",deps)?deps:(deps?[deps]:[]);if(isType("Function",factory)){var _deps=parseDependencies(factory.toString());}
        _useCache(id,deps,factory)&&_cacheModule(id,deps,factory);deps=mergeArray(deps,_deps);var mod=new Module(id);mod.dependencies=deps||[];mod.factory=factory;if(id=="_init"){cfg.callback.push(mod);}else{cfg.modules[id]=mod;}
        emit("module_ready",id);}
    function _require(id){id=parseVars(id);var module=cfg.modules[id];emit("module_require",id);if(!module){emit("module_error",id);return null}
        if(module.exports){return module.exports;}
        var now=new Date();var factory=module.factory;var exports=isType("Function",factory)?factory(require,module.exports={},module):factory;module.exports=exports===undefined?module.exports:exports;cfg.timing[id]=new Date()-now;return module.exports;}
    _require.async=_modulejs;_require.css=function(path){path=cfg.cssAlias&&cfg.cssAlias[path]?cfg.cssAlias[path]:path;if(!path){return;}
        var l;if(!window["_loadCss"]||window["_loadCss"].indexOf(path)<0){l=document.createElement('link');l.setAttribute('type','text/css');l.setAttribute('rel','stylesheet');l.setAttribute('href',path);l.setAttribute("id","loadCss"+Math.random());document.getElementsByTagName("head")[0].appendChild(l);window["_loadCss"]?(window["_loadCss"]+="|"+path):(window["_loadCss"]="|"+path);}
        l&&(typeof callback=="function")&&(l.onload=callback);return true;}
    function _modulejs(deps,factory){_define("_init",deps,factory);}
    function checkDeps(deps){var list={},flag=true;for(var i=0;i<deps.length;i++)list[deps[i]]=1;getDesps(deps,list);for(var i in list){if(!cfg.modules[i]){loadModule(i);flag=false;}}
        return flag;function getDesps(deps,list){for(var i=0;i<deps.length;i++){if(!list[deps[i]]){list[deps[i]]=1;}
            if(cfg.modules[deps[i]]&&list[deps[i]]!=2){list[deps[i]]=2;getDesps(cfg.modules[deps[i]].dependencies,list);}}}}
    function parseVars(id){var VARS_RE=/{([^{]+)}/g
        var vars=cfg.vars
        if(vars&&id.indexOf("{")>-1){id=id.replace(VARS_RE,function(m,key){return isType("String",vars[key])?vars[key]:key})}
        return id}
    function mergeObject(a,b){for(var i in b){if(!b.hasOwnProperty(i)){continue;}
        if(!a[i]){a[i]=b[i];}else if(Object.prototype.toString.call(b[i])=="[object Object]"){mergeObject(a[i],b[i]);}else{a[i]=b[i];}}
        return a;}
    function config(obj){return cfg=mergeObject(cfg,obj);}
    function Module(id){this.id=id;this.dependencies=[];this.exports=null;this.uri="";}
    function loadModule(id){var m;if(m=cfg.cacheLoad[id]){return _define(m.id,m.deps,eval("a = "+m.factory));}
        if(cfg.modules[id]){emit("module_ready",id);return;}
        var url=cfg.alias[id]?cfg.alias[id]:"";if(!url){emit("module_loss_alias",id);return;}
        if(cfg.uris[url]){return;}
        cfg.uris[url]=1;var head=document.getElementsByTagName("head")[0]||document.documentElement;var baseElement=head.getElementsByTagName("base")[0];var node=document.createElement("script");node.charset="utf-8";node.async=true;node.src=url;var handler=function(){var callee=arguments.callee
            cfg.timing[callee.uri]=new Date()-callee.time;};handler.time=new Date();handler.uri=url;node.onload=handler;baseElement?head.insertBefore(node,baseElement):head.appendChild(node);cfg.debug&&emit("file_loading",url);}
    function on(name,cb){var cbs=cfg.events[name];if(!cbs){cbs=cfg.events[name]=[];}
        cbs.push(cb);}
    function emit(name,evt){debug(name,evt);if(!cfg.events[name]||cfg.events[name].length==0){return;}
        for(var i=0,l=cfg.events[name].length;i<l;i++){cfg.events[name][i](evt);}
        if(name==='error'){delete cfg.events[name];}}
    function cleanArray(a){var n=[];for(var i=0;i<a.length;i++){a[i]&&n.push(a[i]);}
        return n;}
    function mergeArray(a,b){for(var i=0;i<b.length;i++){((","+a+",").indexOf(","+b[i]+",")<0)&&a.push(b[i]);}
        return a;}
    function isType(type,obj){return Object.prototype.toString.call(obj)==="[object "+type+"]"}
    function parseDependencies(code){var commentRegExp=/(\/\*([\s\S]*?)\*\/|([^:]|^)\/\/(.*)$)/mg;var cjsRequireRegExp=/[^.]\s*require\s*\(\s*["']([^'"\s]+)["']\s*\)/g;var ret=[];code.replace(commentRegExp,"").replace(cjsRequireRegExp,function(match,dep){dep&&ret.push(parseVars(dep));})
        return ret;}
    function debug(){if(!cfg.debug){return true;}
        var a=[],l=arguments.length;for(var i=0;i<l;i++){a.push(arguments[i]);}
        try{console.log.apply(console,a);}catch(e){}}}(this));
		
		var _speedMarkModule = new Date();
    modulejs("wd.shop.global", function(m){
        m.init();
    });
	
	
	