qx.Class.define("componente.comp.ui.ramon.menu.Menu",
{
	extend : qx.ui.menu.Menu,
	construct : function ()
	{
		this.base(arguments);
		
	},
	members : 
	{
		_arrayObjeto: null,
		
		memorizar: function(objeto)
		{
			if (!objeto) {
				this._arrayObjeto = [];
				this._memorizar(this);
			} else {
				var map;
				for (var i in objeto) {
					if (map = this._arrayObjeto[objeto[i].toString()]) {
						map.enabled = objeto[i].getEnabled();
					}
				}
			}
		},
		
		_memorizar: function(menu)
		{
			var map;
			var command;
			var nombre;
			var submenu;
			
			var child;
			var children = menu.getChildren();
			for (var i in children) {
				child = children[i];
				nombre = child.toString();
				if (nombre.substr(0, 17)=="qx.ui.menu.Button" || nombre.substr(0, 22)=="qx.ui.menu.RadioButton" || nombre.substr(0, 19)=="qx.ui.menu.CheckBox") {
					map = {};
					if (command = child.getCommand()) {
						map.objeto = command;
						map.enabled = command.getEnabled();
						this._arrayObjeto[command.toString()] = map;
					} else {
						map.objeto = child;
						map.enabled = child.getEnabled();
						this._arrayObjeto[nombre] = map;
					}
					if (submenu = child.getMenu()) {
						this._memorizar(submenu);
					}
				}
			}
		},
		
		
		memorizarEnabled: function(objeto, enabled)
		{
			var map;
			for (var i in objeto) {
				if (map = this._arrayObjeto[objeto[i].toString()]) {
					map.enabled = enabled;
				}
			}
		},
		
		
		restablecer: function()
		{
			for (var i in this._arrayObjeto) {
				this._arrayObjeto[i].objeto.setEnabled(this._arrayObjeto[i].enabled);
			}
		},
		
		desactivar: function()
		{
			for (var i in this._arrayObjeto) {
				this._arrayObjeto[i].objeto.setEnabled(false);
			}
		}
	},
	destruct : function ()
	{

	}
});