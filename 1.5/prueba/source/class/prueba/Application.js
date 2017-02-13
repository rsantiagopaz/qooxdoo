/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/* ************************************************************************

#asset(prueba/*)

************************************************************************ */

/**
 * This is the main application class of your custom application "prueba"
 */
qx.Class.define("prueba.Application",
{
  extend : qx.application.Standalone,



  /*
  *****************************************************************************
     MEMBERS
  *****************************************************************************
  */

  members :
  {
    /**
     * This method contains the initial application code and gets called 
     * during startup of the application
     * 
     * @lint ignoreDeprecated(alert)
     */
    main : function()
    {
      // Call super class
      this.base(arguments);

      // Enable logging in debug variant
      if (qx.core.Environment.get("qx.debug"))
      {
        // support native logging capabilities, e.g. Firebug for Firefox
        qx.log.appender.Native;
        // support additional cross-browser console. Press F7 to toggle visibility
        qx.log.appender.Console;
      }

      /*
      -------------------------------------------------------------------------
        Below is your actual application code...
      -------------------------------------------------------------------------
      */



      // Document is the application root
var doc = this.getRoot();

var form = new qx.ui.form.Form();

var txtApellido = new qx.ui.form.TextField("");

var btnAceptar = new qx.ui.form.Button("Aceptar");
btnAceptar.addListener("execute", function(e){
	txtApellido.focus();		
	txtApellido.setValid(false);
	txtApellido.setInvalidMessage("este esta mal");
	
	var manager = qx.ui.tooltip.Manager.getInstance();
	manager.showToolTip(txtApellido);
});

doc.add(txtApellido, {left: 100, top: 100});
doc.add(btnAceptar, {left: 100, top: 300});


    }
  }
});
