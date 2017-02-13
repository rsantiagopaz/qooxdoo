qx.Class.define("controles.selectbox.SelectBox",
{
	extend : qx.ui.form.SelectBox,
	construct : function ()
	{
		this.base(arguments);
		
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