qx.Class.define("controles.combobox.ComboBoxAuto",
{
	extend : qx.ui.form.ComboBox,
	construct : function (url, serviceName, methodName, parametros)
	{
		this.base(arguments);
		
		var listenerId;
		var stringModoBuscar = null;
		
		this.parametros = parametros;

		listenerId = this.addListener("focus", function(e) {
			if (this._contextMenu) this._contextMenu.restablecer();
		});
		this._listeners.push({objeto: this, listenerId: listenerId});
		
		listenerId = this.addListener("blur", function(e) {
			if (this._contextMenu) this._contextMenu.desactivar();
			if (this._list.isSelectionEmpty()) this.setValue("");
		});
		this._listeners.push({objeto: this, listenerId: listenerId});

		listenerId = this.addListener("changeContextMenu", function(e) {
			this._contextMenu = e.getData();
		});
		this._listeners.push({objeto: this, listenerId: listenerId});
		
		this._list = this.getChildControl("list");
		this._textfield = this.getChildControl("textfield");
		
		listenerId = this._textfield.addListener("input", function (e) {
			var texto = e.getData().trim();
			if (stringModoBuscar == null) {
				this._list.resetSelection();
				if (texto.length == 0) {
					this.removeAll();
				} else if (texto.length != 3) {
					var find = new RegExp(texto, "i");
	
					for(var x in this._list.getChildren()) {
						if (this._list.getChildren()[x].getLabel().search(find) != -1) {
			    			this._list.getChildren()[x].setVisibility("visible");
						} else this._list.getChildren()[x].setVisibility("excluded");
					}
					this.open();
				} else {
					stringModoBuscar = texto;
					var contexto = this;
					var p = {};
					p.texto = texto;
					p.parametros = this.parametros;
					
					var rpc = new qx.io.remote.Rpc(url, serviceName);
					rpc.setTimeout(60000 * 1);
					rpc.callAsync(function(resultado, error, id) {
						var listItem;
						contexto.removeAll();
						for (var x in resultado) {
							listItem = new qx.ui.form.ListItem(resultado[x].label, null, resultado[x].model);
							listItem.setUserData("datos", resultado[x]);
							contexto.add(listItem);
						}
						
                        //var texto = txtBuscar.getValue().trim();
                        if (texto != stringModoBuscar) {
                                stringModoBuscar = null;
                                this.fireDataEvent("input", texto);
                        } else stringModoBuscar = null;
					}, methodName, p);
				}
			}
		}, this);
		this._listeners.push({objeto: this._textfield, listenerId: listenerId});
		


	},
	properties : 
	{
		parametros : {
			init : null
		}
	},
	members : 
	{
		_list: null,
		_textfield: null,
		_contextMenu: null,
		_listeners: []
	},
	destruct : function ()
	{
		for (var x in this._listeners) {
			this._listeners[x].objeto.removeListenerById(this._listeners[x].listenerId);
		}
	}
});