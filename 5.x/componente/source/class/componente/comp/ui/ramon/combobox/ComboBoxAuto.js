qx.Class.define("componente.comp.ui.ramon.combobox.ComboBoxAuto",
{
	extend : qx.ui.form.ComboBox,
	construct : function (rpcParametros, phpParametros, caracteres)
	{
		this.base(arguments);
		
		var listenerId;
		var stringModoBuscar = null;
		
		this.phpParametros = phpParametros;
		this._listeners = [];
		
		if (caracteres==null) caracteres = 3;

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
		
		listenerId = this._textfield.addListener("input", function(e) {
			var texto = e.getData().trim();
			
			var timer = qx.util.TimerManager.getInstance();
			// check for the old listener
			if (this.timerId != null) {
				// stop the old one
				timer.stop(this.timerId);
				if (this.rpc != null) this.rpc.abort(this.opaqueCallRef);
				this.timerId = null;
			}
			
			this.timerId = timer.start(function(userData, timerId) {
				this._list.resetSelection();
				if (texto.length == 0) {
					this.removeAll();
					this.close();
				} else if (texto.length >= caracteres) {
					var p = {};
					p.texto = texto;
					p.parametros = this.phpParametros;
					
					this.rpc = new componente.comp.io.ramon.rpc.Rpc(rpcParametros.url, rpcParametros.serviceName);
					this.rpc.setTimeout(60000 * 1);
					this.rpc.addListener("completed", qx.lang.Function.bind(function(e){
						var resultado = e.getData().result;
						
						//alert(qx.lang.Json.stringify(resultado, null, 2));
						
						var listItem;
						this.removeAll();
						for (var x in resultado) {
							listItem = new qx.ui.form.ListItem(resultado[x].label, null, resultado[x].model);
							listItem.setUserData("datos", resultado[x]);
							this.add(listItem);
						}
						
						this.timerId = null;
						this.rpc = null;

                        this.open();
					}, this));

					this.opaqueCallRef = this.rpc.callAsyncListeners(true, rpcParametros.methodName, p);
				}
			}, null, this, null, 200);
		}, this);
		this._listeners.push({objeto: this._textfield, listenerId: listenerId});
		


	},
	properties : 
	{
		phpParametros : {
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