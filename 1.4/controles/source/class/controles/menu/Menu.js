qx.Class.define("controles.menu.Menu",
{
	extend : qx.ui.menu.Menu,
	construct : function ()
	{
		this.base(arguments);
		
		this._arrayCommand = [];
	},
	members : 
	{
		_arrayCommand: null,
		
		memorizar: function(objeto)
		{
			var map;
			if (!objeto) {
				this._arrayCommand = [];
				var a = this.getChildren();
				for (var i in a) {
					var nombre = a[i].toString();
					if (nombre.substr(0, 17)=="qx.ui.menu.Button" || nombre.substr(0, 22)=="qx.ui.menu.RadioButton") {
						map = {};
						var c;
						if (c = a[i].getCommand()) {
							map.objeto = c;
							map.enabled = c.getEnabled();
							this._arrayCommand[c.toString()] = map;
						} else {
							map.objeto = a[i];
							map.enabled = a[i].getEnabled();
							this._arrayCommand[nombre] = map;
						}
					}
				}
			} else {
				for (var i = 0; i < objeto.length; i++) {
					if (map = this._arrayCommand[objeto[i].toString()]) {
						map.enabled = objeto[i].getEnabled();
					}
				}
			}
		},
		
		restablecer: function()
		{
			for (var i in this._arrayCommand) {
				if (this._arrayCommand[i].objeto!=null) {
					this._arrayCommand[i].objeto.setEnabled(this._arrayCommand[i].enabled);
				}
			}
		},
		
		desactivar: function()
		{
			for (var i in this._arrayCommand) {
				if (this._arrayCommand[i].objeto!=null) {
					this._arrayCommand[i].objeto.setEnabled(false);
				}
			}
		}
	},
	destruct : function ()
	{

	}
});