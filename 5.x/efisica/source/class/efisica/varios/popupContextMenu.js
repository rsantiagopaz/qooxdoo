qx.Class.define("efisica.varios.popupContextMenu",
{
  extend : efisica.varios.popupModal,

  construct : function(widgets, anchor)
  {
   	//this.base(arguments, null, anchor);
   	
	var composite = new qx.ui.mobile.container.Composite(new qx.ui.mobile.layout.VBox());
	composite.addCssClass("list");
	//this.add(composite);
	this.base(arguments, composite, anchor);
   	
	var functionWidget_tap = qx.lang.Function.bind(function(e) {
		window.history.back();

		window.setTimeout(function(){
			this.fireDataEvent("aceptado", {model: e.getCurrentTarget().getUserData("model")});
		}.bind(this), 1);
	}, this);

	var widget, listenerTap;
	for (var x in widgets) {
		widget = widgets[x];
		if (widget == null) {
			composite.add(new qx.ui.mobile.form.Title());
		} else if (widget.getUserData("model") == null) {
			
		} else {
			composite.add(widget);
			listenerTap = widget.addListener("tap", functionWidget_tap);
			this.registrarListener(widget, listenerTap);
		}
	}
  },


  members :
  {

  },
  
  events : 
  {
	"aceptado": "qx.event.type.Event"
  }
});