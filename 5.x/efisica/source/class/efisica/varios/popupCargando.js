qx.Class.define("efisica.varios.popupCargando",
{
  extend : qx.ui.mobile.dialog.Popup,

  construct : function(widget, anchor)
  {
    this.base(arguments, widget, anchor);
  },


  members :
  {
	contador : 0,

	mostrar : function()
	{
		this.contador = this.contador + 1;
		this.show();
	},
	
	mostrarModal : function()
	{
		this.setModal(true);
		this.show();
	},

	ocultar : function()
	{
		this.contador = this.contador - 1;
		if (this.contador == 0) this.hide();
	},
	
	ocultarModal : function()
	{
		this.hide();
		this.setModal(false);
	}
  }
});