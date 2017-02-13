qx.Class.define("controles.list.List",
{
	extend : qx.ui.form.List,
	construct : function (horizontal)
	{
		this.base(arguments, horizontal);
		
		this._listenerFocus = this.addListener("focus", function(e){
			if (this._contextMenu) this._contextMenu.restablecer();
		});
		
		this._listenerBlur = this.addListener("blur", function(e){
			if (this._contextMenu) this._contextMenu.desactivar();
		});

		this._listenerChangeContextMenu = this.addListener("changeContextMenu", function(e){
			this._contextMenu = e.getData();
		});

	},
	members : 
	{
		_listenerFocus: null,
		_listenerBlur: null,
		_listenerChangeContextMenu: null,
		_contextMenu: null
	},
	destruct : function ()
	{
		this.removeListenerById(this._listenerFocus);
		this.removeListenerById(this._listenerBlur);
		this.removeListenerById(this._listenerChangeContextMenu);
	}
});