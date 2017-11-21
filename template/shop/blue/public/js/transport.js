var Transport =
{
  filename : "transport.js",

  debugging :
  {
    isDebugging : 0,
    debuggingMode : 0,
    linefeed : "",
    containerId : 0
  },

  debug : function (isDebugging, debuggingMode)
  {
    this.debugging =
    {
      "isDebugging" : isDebugging,
      "debuggingMode" : debuggingMode,
      "linefeed" : debuggingMode ? "<br />" : "\n",
      "containerId" : "dubugging-container" + new Date().getTime()
    };
  },

  onComplete : function ()
  {
  },

  onRunning : function ()
  {
  },


  run : function (url, params, callback, transferMode, responseType, asyn, quiet)
  {
    params = this.parseParams(params);
    transferMode = typeof(transferMode) === "string"
    && transferMode.toUpperCase() === "GET"
    ? "GET"
    : "POST";

    if (transferMode === "GET")
    {
      var d = new Date();

      url += params ? (url.indexOf("?") === - 1 ? "?" : "&") + params : "";
      url = encodeURI(url) + (url.indexOf("?") === - 1 ? "?" : "&") + d.getTime() + d.getMilliseconds();
      params = null;
    }

    responseType = typeof(responseType) === "string" && ((responseType = responseType.toUpperCase()) === "JSON" || responseType === "XML") ? responseType : "TEXT";
    asyn = asyn === false ? false : true;

    var xhr = this.createXMLHttpRequest();

    try
    {
      var self = this;

      if (typeof(self.onRunning) === "function" && !quiet)
      {
        self.onRunning();
      }

      xhr.open(transferMode, url, asyn);

      if (transferMode === "POST")
      {
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      }

      if (asyn)
      {
        xhr.onreadystatechange = function ()
        {
          if (xhr.readyState == 4)
          {
            switch ( xhr.status )
            {
              case 0:
              case 200: 

                if (typeof(self.onComplete) === "function")
                {
                  self.onComplete();
                }

                if (typeof(callback) === "function")
                {
                  callback.call(self, self.parseResult(responseType, xhr), xhr.responseText);
                }
              break;

              case 304:
              break;

              case 400:
                 alert("XmlHttpRequest status: [400] Bad Request");
              break;

              case 404: // Not Found
                alert("XmlHttpRequest status: [404] \nThe requested URL "+url+" was not found on this server.");
              break;

              case 409: // Conflict
                /*
                 * Perhaps your JavaScript request attempted to
                 * update a Database record
                 * but failed due to a conflict
                 * (eg: a field that must be unique)
                 */
              break;

              case 503: // Service Unavailable
                /*
                 * A resource that this request relies upon
                 * is currently unavailable
                 * (eg: a file is locked by another process)
                 */
                 alert("XmlHttpRequest status: [503] Service Unavailable");
              break;

              default:
                alert("XmlHttpRequest status: [" + xhr.status + "] Unknow status.");
            }

            xhr = null;
          }
        }
        if (xhr != null) xhr.send(params);
      }
      else
      {
        if (typeof(self.onRunning) === "function")
        {
          self.onRunning();
        }

        xhr.send(params);

        var result = self.parseResult(responseType, xhr);
        //xhr = null;

        if (typeof(self.onComplete) === "function")
        {
          self.onComplete();
        }
        if (typeof(callback) === "function")
        {
          callback.call(self, result, xhr.responseText);
        }

        return result;
      }
    }
    catch (ex)
    {
      if (typeof(self.onComplete) === "function")
      {
        self.onComplete();
      }

      alert(this.filename + "/run() error:" + ex.description);
    }
  },

  displayDebuggingInfo : function (info, type)
  {
    if ( ! this.debugging.debuggingMode)
    {
      alert(info);
    }
    else
    {

      var id = this.debugging.containerId;
      if ( ! document.getElementById(id))
      {
        div = document.createElement("DIV");
        div.id = id;
        div.style.position = "absolute";
        div.style.width = "98%";
        div.style.border = "1px solid #f00";
        div.style.backgroundColor = "#eef";
        var pageYOffset = document.body.scrollTop
        || window.pageYOffset
        || 0;
        div.style.top = document.body.clientHeight * 0.6
        + pageYOffset
        + "px";
        document.body.appendChild(div);
        div.innerHTML = "<div></div>"
        + "<hr style='height:1px;border:1px dashed red;'>"
        + "<div></div>";
      }

      var subDivs = div.getElementsByTagName("DIV");
      if (type === "param")
      {
        subDivs[0].innerHTML = info;
      }
      else
      {
        subDivs[1].innerHTML = info;
      }
    }
  },

  createXMLHttpRequest : function ()
  {
    var xhr = null;

    if (window.ActiveXObject)
    {
      var versions = ['Microsoft.XMLHTTP', 'MSXML6.XMLHTTP', 'MSXML5.XMLHTTP', 'MSXML4.XMLHTTP', 'MSXML3.XMLHTTP', 'MSXML2.XMLHTTP', 'MSXML.XMLHTTP'];

      for (var i = 0; i < versions.length; i ++ )
      {
        try
        {
          xhr = new ActiveXObject(versions[i]);
          break;
        }
        catch (ex)
        {
          continue;
        }
      }
    }
    else
    {
      xhr = new XMLHttpRequest();
    }

    return xhr;
  },


  onXMLHttpRequestError : function (xhr, url)
  {
    throw "URL: " + url + "\n"
    +  "readyState: " + xhr.readyState + "\n"
    + "state: " + xhr.status + "\n"
    + "headers: " + xhr.getAllResponseHeaders();
  },


  parseParams : function (params)
  {
    var legalParams = "";
    params = params ? params : "";

    if (typeof(params) === "string")
    {
      legalParams = params;
    }
    else if (typeof(params) === "object")
    {
      try
      {
        legalParams = "JSON=" + $.toJSON(params);
      }
      catch (ex)
      {
        alert("Can't stringify JSON!");
        return false;
      }
    }
    else
    {
      alert("Invalid parameters!");
      return false;
    }

    if (this.debugging.isDebugging)
    {
      var lf = this.debugging.linefeed,
      info = "[Original Parameters]" + lf + params + lf + lf
      + "[Parsed Parameters]" + lf + legalParams;

      this.displayDebuggingInfo(info, "param");
    }

    return legalParams;
  },


  preFilter : function (result)
  {
    return result.replace(/\xEF\xBB\xBF/g, "");
  },


  parseResult : function (responseType, xhr)
  {
    var result = null;

    switch (responseType)
    {
      case "JSON" :
        result = this.preFilter(xhr.responseText);
        try
        {
			if($ && $.evalJSON){
				result = $.evalJSON(result);
			}else{
				result = $.parseJSON(result);
			}
        }
        catch (ex)
        {
			console.log(ex);
          throw this.filename + "/parseResult() error: can't parse to JSON.\n\n" + xhr.responseText;
        }
        break;
      case "XML" :
        result = xhr.responseXML;
        break;
      case "TEXT" :
        result = this.preFilter(xhr.responseText);
        break;
      default :
        throw this.filename + "/parseResult() error: unknown response type:" + responseType;
    }

    if (this.debugging.isDebugging)
    {
      var lf = this.debugging.linefeed,
      info = "[Response Result of " + responseType + " Format]" + lf
      + result;

      if (responseType === "JSON")
      {
        info = "[Response Result of TEXT Format]" + lf
        + xhr.responseText + lf + lf
        + info;
      }

      this.displayDebuggingInfo(info, "result");
    }

    return result;
  }
};

var Ajax = Transport;
Ajax.call = Transport.run;


Ajax.onRunning  = showLoader;
Ajax.onComplete = hideLoader;

function showLoader()
{

  document.getElementsByTagName('body').item(0).style.cursor = "wait";

  if (top.frames['header-frame'] && top.frames['header-frame'].document.getElementById("load-div"))
  { 
    top.frames['header-frame'].document.getElementById("load-div").style.display = "block";

  }
  else
  { 
    var obj = document.getElementById('loader');

    if ( ! obj && typeof(process_request) != 'undefined')
    {
      obj = document.createElement("DIV");
      obj.id = "loader";
      obj.innerHTML = process_request;

      document.body.appendChild(obj);
    }
  }
}

function hideLoader()
{
  document.getElementsByTagName('body').item(0).style.cursor = "auto";
  if (top.frames['header-frame'] && top.frames['header-frame'].document.getElementById("load-div"))
  {
    setTimeout(function(){top.frames['header-frame'].document.getElementById("load-div").style.display = "none"}, 10);
  }
  else
  {
    try
    {
      var obj = document.getElementById("loader");
      obj.style.display = 'none';
      document.body.removeChild(obj);
    }
    catch (ex)
    {}
  }
}