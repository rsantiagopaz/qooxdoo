/**
 * @ignore(qx.core.ServerSettings.*)
*/

qx.Class.define("componente.comp.io.ramon.rpc.Rpc",
{
	extend : qx.io.remote.Rpc,
	construct : function (url, serviceName)
	{
		this.base(arguments, url, serviceName);
		
		qx.io.remote.Rpc.CONVERT_DATES = true;
		qx.io.remote.Rpc.RESPONSE_JSON = true;
	},
	members : 
	{

		/**
		 * Internal RPC call method
		 *
		 * @lint ignoreDeprecated(eval)
		 *
		 * @param args {Array}
		 *   array of arguments
		 *
		 * @param callType {Integer}
		 *   0 = sync,
		 *   1 = async with handler,
		 *   2 = async event listeners
		 *
		 * @param refreshSession {Boolean}
		 *   whether a new session should be requested
		 *
		 * @return {var} the method call reference.
		 * @throws {Error} An error.
		 */
		_callInternal : function(args, callType, refreshSession)
		{
		  var self = this;
		  var offset = (callType == 0 ? 0 : 1);
		  var whichMethod = (refreshSession ? "refreshSession" : args[offset]);
		  var handler = args[0];
		  var argsArray = [];
		  var eventTarget = this;
		  var protocol = this.getProtocol();
		
		  for (var i=offset+1; i<args.length; ++i)
		  {
		    argsArray.push(args[i]);
		  }
		
		  var req = this.createRequest();
		
		  // Get any additional out-of-band data to be sent to the server
		  var serverData = this.getServerData();
		
		  // Create the request object
		  var rpcData = this.createRpcData(req.getSequenceNumber(),
		                                   whichMethod,
		                                   argsArray,
		                                   serverData);
		
		  req.setCrossDomain(this.getCrossDomain());
		
		  if (this.getUsername())
		  {
		    req.setUseBasicHttpAuth(this.getUseBasicHttpAuth());
		    req.setUsername(this.getUsername());
		    req.setPassword(this.getPassword());
		  }
		
		  req.setTimeout(this.getTimeout());
		  var ex = null;
		  var id = null;
		  var result = null;
		  var response = null;
		
		  var handleRequestFinished = function(eventType, eventTarget)
		  {
		    switch(callType)
		    {
		      case 0: // sync
		        break;
		
		      case 1: // async with handler function
		        handler(result, ex, id);
		        break;
		
		      case 2: // async with event listeners
		        // Dispatch the event to our listeners.
		        if (!ex)
		        {
		          eventTarget.fireDataEvent(eventType, response);
		        }
		        else
		        {
		          // Add the id to the exception
		          ex.id = id;
		
		          if (args[0])      // coalesce
		          {
		            // They requested that we coalesce all failure types to
		            // "failed"
		            eventTarget.fireDataEvent("failed", ex);
		          }
		          else
		          {
		            // No coalese so use original event type
		            eventTarget.fireDataEvent(eventType, ex);
		          }
		        }
		    }
		  };
		
		  var addToStringToObject = function(obj)
		  {
		    if (protocol == "qx1")
		    {
		      obj.toString = function()
		      {
		        switch(obj.origin)
		        {
		          case qx.io.remote.Rpc.origin.server:
		            return "Server error " + obj.code + ": " + obj.message;
		
		          case qx.io.remote.Rpc.origin.application:
		            return "Application error " + obj.code + ": " + obj.message;
		
		          case qx.io.remote.Rpc.origin.transport:
		            return "Transport error " + obj.code + ": " + obj.message;
		
		          case qx.io.remote.Rpc.origin.local:
		            return "Local error " + obj.code + ": " + obj.message;
		
		          default:
		            return ("UNEXPECTED origin " + obj.origin +
		                    " error " + obj.code + ": " + obj.message);
		        }
		      };
		    }
		    else // protocol == "2.0"
		    {
		      obj.toString = function()
		      {
		        var             ret;
		
		        ret =  "Error " + obj.code + ": " + obj.message;
		        if (obj.data)
		        {
		          ret += " (" + obj.data + ")";
		        }
		
		        return ret;
		      };
		    }
		  };
		
		  var makeException = function(origin, code, message)
		  {
		    var ex = new Object();
		
		    if (protocol == "qx1")
		    {
		      ex.origin = origin;
		    }
		    ex.code = code;
		    ex.message = message;
		    addToStringToObject(ex);
		
		    return ex;
		  };
		
		  req.addListener("failed", function(evt)
		  {
		    var code = evt.getStatusCode();
		    ex = makeException(qx.io.remote.Rpc.origin.transport,
		                       code,
		                       qx.io.remote.Exchange.statusCodeToString(code));
		    id = this.getSequenceNumber();
		    handleRequestFinished("failed", eventTarget);
		  });
		
		  req.addListener("timeout", function(evt)
		  {
		    this.debug("TIMEOUT OCCURRED");
		    ex = makeException(qx.io.remote.Rpc.origin.local,
		                       qx.io.remote.Rpc.localError.timeout,
		                       "Local time-out expired for "+ whichMethod);
		    id = this.getSequenceNumber();
		    handleRequestFinished("timeout", eventTarget);
		  });
		
		  req.addListener("aborted", function(evt)
		  {
		    ex = makeException(qx.io.remote.Rpc.origin.local,
		                       qx.io.remote.Rpc.localError.abort,
		                       "Aborted " + whichMethod);
		    id = this.getSequenceNumber();
		    handleRequestFinished("aborted", eventTarget);
		  });
		
		  req.addListener("completed", function(evt)
		  {
		    response = evt.getContent();
		
		    // Parse. Skip when response is already an object
		    // because the script transport was used.
		    if (!qx.lang.Type.isObject(response)) {
		
		      // Handle converted dates
		      if (self._isConvertDates()) {

		        // Parse as JSON and revive date literals
		        if (self._isResponseJson()) {
		          response = qx.lang.Json.parse(response, function(key, value) {
		            if (value && typeof value === "string") {
					/*
		              if (value.indexOf("new Date(Date.UTC(") >= 0) {
		                var m = value.match(/new Date\(Date.UTC\((\d+),(\d+),(\d+),(\d+),(\d+),(\d+),(\d+)\)\)/);
		                return new Date(Date.UTC(m[1],m[2],m[3],m[4],m[5],m[6],m[7]));
		              }
					*/
		            	
						if (value.substr(4, 1) == "-" && value.substr(7, 1) == "-" && (value.length == 10 || value.length == 19)) {
							var m = [];
							m[1] = parseInt(value.substr(0, 4));
							m[2] = parseInt(value.substr(5, 2)) - 1;
							m[3] = parseInt(value.substr(8, 2));
							if (value.length == 19) {
								m[4] = parseInt(value.substr(11, 2));
								m[5] = parseInt(value.substr(14, 2));
								m[6] = parseInt(value.substr(17, 2));
							} else {
								m[4] = 0;
								m[5] = 0;
								m[6] = 0;
							}
							
							return new Date(m[1],m[2],m[3],m[4],m[5],m[6]);
						}
		            }
		            return value;
		          });
		
		        // Eval
		        } else {
		          response = response && response.length > 0 ? eval('(' + response + ')') : null;
		        }
		
		      // No special date handling required, JSON assumed
		      } else {
		        response = qx.lang.Json.parse(response);
		      }
		    }
		
		    id = response["id"];
		
		    if (id != this.getSequenceNumber())
		    {
		      this.warn("Received id (" + id + ") does not match requested id " +
		                "(" + this.getSequenceNumber() + ")!");
		    }
		
		    // Determine if an error was returned. Assume no error, initially.
		    var eventType = "completed";
		    var exTest = response["error"];
		
		    if (exTest != null)
		    {
		      // There was an error
		      result = null;
		      addToStringToObject(exTest);
		      ex = exTest;
		
		      // Change the event type
		      eventType = "failed";
		    }
		    else
		    {
		      result = response["result"];
		
		      if (refreshSession)
		      {
		        result = eval("(" + result + ")");
		        var newSuffix = qx.core.ServerSettings.serverPathSuffix;
		
		        if (self.__currentServerSuffix != newSuffix)
		        {
		          self.__previousServerSuffix = self.__currentServerSuffix;
		          self.__currentServerSuffix = newSuffix;
		        }
		
		        self.setUrl(self.fixUrl(self.getUrl()));
		      }
		    }
		
		    handleRequestFinished(eventType, eventTarget);
		  });
		
		  // Provide a replacer when convert dates is enabled
		  var replacer = null;
		  if (this._isConvertDates()) {
		    replacer = function(key, value) {
		      // The value passed in is of type string, because the Date's
		      // toJson gets applied before. Get value from containing object.
		      value = this[key];
		
		      if (qx.lang.Type.isDate(value)) {
				/*
		        var dateParams =
		          value.getUTCFullYear() + "," +
		          value.getUTCMonth() + "," +
		          value.getUTCDate() + "," +
		          value.getUTCHours() + "," +
		          value.getUTCMinutes() + "," +
		          value.getUTCSeconds() + "," +
		          value.getUTCMilliseconds();
		        return "new Date(Date.UTC(" + dateParams + "))";
				*/
		      	
		        /*
		        var dateParams =
		          value.getUTCFullYear() + "-" +
		          qx.lang.String.pad((value.getUTCMonth() + 1).toString(), 2, "0") + "-" +
		          qx.lang.String.pad(value.getUTCDate().toString(), 2, "0") + " " +
		          qx.lang.String.pad(value.getUTCHours().toString(), 2, "0") + ":" +
		          qx.lang.String.pad(value.getUTCMinutes().toString(), 2, "0") + ":" +
		          qx.lang.String.pad(value.getUTCSeconds().toString(), 2, "0");
		          */
		          
		        var dateParams =
		          value.getFullYear() + "-" +
		          qx.lang.String.pad((value.getMonth() + 1).toString(), 2, "0") + "-" +
		          qx.lang.String.pad(value.getDate().toString(), 2, "0") + " " +
		          qx.lang.String.pad(value.getHours().toString(), 2, "0") + ":" +
		          qx.lang.String.pad(value.getMinutes().toString(), 2, "0") + ":" +
		          qx.lang.String.pad(value.getSeconds().toString(), 2, "0");
		        return dateParams;
		      }
		      return value;
		    };
		  }
		
		  req.setData(qx.lang.Json.stringify(rpcData, replacer));
		  req.setAsynchronous(callType > 0);
		
		  if (req.getCrossDomain())
		  {
		    // Our choice here has no effect anyway.  This is purely informational.
		    req.setRequestHeader("Content-Type",
		                         "application/x-www-form-urlencoded");
		  }
		  else
		  {
		    // When not cross-domain, set type to text/json
		    req.setRequestHeader("Content-Type", "application/json");
		  }
		
		  // Do not parse as JSON. Later done conditionally.
		  req.setParseJson(false);
		
		  req.send();
		
		  if (callType == 0)
		  {
		    if (ex != null)
		    {
		      var error = new Error(ex.toString());
		      error.rpcdetails = ex;
		      throw error;
		    }
		
		    return result;
		  }
		  else
		  {
		    return req;
		  }
		}
		
	}
});