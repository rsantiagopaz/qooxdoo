qx.Class.define("componente.comp.ui.ramon.window.Window",
{
	extend : qx.ui.window.Window,
	construct : function ()
	{
		this.base(arguments);
		
		this._commands = {};
		this._listeners = [];
		
		var listenerId;
		
		listenerId = this.addListener("activate", function(e){
			for (var i in this._commands) this._commands[i].command.setEnabled(this._commands[i].enabled);
		}, this);
		this.registrarListener(this, listenerId);
		
		listenerId = this.addListener("deactivate", function(e){
			for (var i in this._commands) this._commands[i].command.setEnabled(false);
		}, this);
		this.registrarListener(this, listenerId);
		
	},
	members : 
	{
		_commands: {},
		_listeners: [],
		
		memorizarEnabled: function(command, enabled)
		{
			var map;
			for (var i in command) {
				if (map = this._commands[command[i].toString()]) {
					map.enabled = enabled;
				}
			}
		},
		
		registrarCommand : function (comm)
		{
			this._commands[comm.toString()] = {command: comm, enabled: comm.getEnabled()};
		},

		registrarListener : function (objeto, listenerId)
		{
			this._listeners.push({objeto: objeto, listenerId: listenerId});
		}
	},

	destruct : function ()
	{
		for (var i in this._listeners) this._listeners[i].objeto.removeListenerById(this._listeners[i].listenerId);
		for (var i in this._commands) this._commands[i].command.dispose();
	}
});