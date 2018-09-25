qx.Class.define("efisica.varios.popupModal",
{
  extend : qx.ui.mobile.dialog.Popup,

  construct : function(widget, anchor)
  {
    this.base(arguments, widget, anchor);
    
	this.setModal(true);
	this.setHideOnBlockerTap(true);
	
	this.routing = qx.core.Init.getApplication().getRouting();
	
	var blocker = qx.ui.mobile.core.Blocker.getInstance();
	var listenerTap;
	
	this.addListener("appear", function(e){
		listenerTap = blocker.addListenerOnce("tap", function(e){
			window.history.back();
		});
	});
	
	this.addListener("disappear", function(e){
		blocker.removeListenerById(listenerTap);
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
	},
	
    // overridden
    _updatePosition : function() {
      var parentHeight = qx.ui.mobile.dialog.Popup.ROOT.getHeight();
      var listScrollerHeight = parseInt(parentHeight, 10) * 0.75;
      listScrollerHeight = parseInt(listScrollerHeight,10);

      /*
      if (this.getVisibleListItems() !== null) {
        var newListScrollerHeight = this.__selectionList.getListItemHeight() * this.getVisibleListItems();
        listScrollerHeight = Math.min(newListScrollerHeight, listScrollerHeight);
      }
      */

      qx.bom.element.Style.set(this._getChildren()[0].getContainerElement(), "maxHeight", listScrollerHeight + "px");

      this.base(arguments);
    }
  },

  destruct : function ()
  {
  	for (var x in this._listeners) this._listeners[x].objeto.removeListenerById(this._listeners[x].listenerId);
  }
});