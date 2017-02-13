qx.Class.define("componente.comp.ui.ramon.popup.Popup",
{
	extend : qx.ui.popup.Popup,
	construct : function ()
	{
		this.base(arguments);
		
		var listenerId;
		this._commands = [];
		this._listeners = [];
		
		listenerId = this.addListener("activate", function(e){
			for (var i in this._commands) this._commands[i].setEnabled(true);
		}, this);
		this.registrarListener(this, listenerId);
		
		listenerId = this.addListener("deactivate", function(e){
			for (var i in this._commands) this._commands[i].setEnabled(false);
		}, this);
		this.registrarListener(this, listenerId);
	},
	members : 
	{
		_commands: null,
		_listeners: null,
		
		registrarCommand : function (c)
		{
			this._commands.push(c);
		},

		registrarListener : function (objeto, listenerId)
		{
			this._listeners.push({"objeto": objeto, "listenerId": listenerId});
		}
	},

	destruct : function ()
	{
		for (var i in this._listeners) this._listeners[i].objeto.removeListenerById(this._listeners[i].listenerId);
		for (var i in this._commands) this._commands[i].dispose();
	}
});