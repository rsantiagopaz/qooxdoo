/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * TODO: needs documentation
 */
qx.Class.define("efisica.varios.NavigationPage",
{
  extend : qx.ui.mobile.page.NavigationPage,

  construct : function()
  {
    this.base(arguments);
    
    this.routing = qx.core.Init.getApplication().getRouting();
    
	this.addListener("disappear", function(e){
		this.routing.remove(this._listenerGet);
	}, this);
    
  },

  members :
  {
  	routing: null,
  	_listenerGet: null,
	_listeners: [],
	
	registrarListener : function (objeto, listenerId)
	{
		this._listeners.push({objeto: objeto, listenerId: listenerId});
	},
  	
	mostrar : function(path)
	{
		this._listenerGet = this.routing.onGet(path, this.show, this);
	
		this.routing.executeGet(path);
	}
  },

  destruct : function ()
  {
  	for (var x in this._listeners) this._listeners[x].objeto.removeListenerById(this._listeners[x].listenerId);
  }
});
