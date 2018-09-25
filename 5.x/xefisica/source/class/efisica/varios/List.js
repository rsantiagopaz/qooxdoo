qx.Class.define("efisica.varios.List",
{
  extend : qx.ui.mobile.list.List,

  construct : function(delegate)
  {
    this.base(arguments, delegate);
    
	this.addListener("longtap", function(e){
		e.stopPropagation();
		
		var originalTarget = e.getOriginalTarget();
		while (originalTarget.tagName != "LI") {
			originalTarget = originalTarget.parentNode;
		}
		
		var index = parseInt(originalTarget.getAttribute("data-row"), 10);
		
		this.fireDataEvent("contexttap", index);
	}, this);
  },


  members :
  {

  },
  
  events : 
  {
	"contexttap": "qx.event.type.Event"
  }
});