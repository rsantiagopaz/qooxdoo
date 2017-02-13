/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * This is the main application class of your custom application "pediatra"
 *
 * @asset(pediatra/*)
 */
qx.Class.define("pediatra.Application",
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

var mapPagePaciente = {};

var tabviewMain = this.tabviewMain = new qx.ui.tabview.TabView();
doc.add(tabviewMain, {left: 0, top: 30, right: 0, bottom: 0});


var popupPacientes = new pediatra.comp.pacientes.popupPacientes();
popupPacientes.addListener("seleccionado", function(e){
	var data = e.getData();
	var page = mapPagePaciente[data.id_paciente];
	if (page == null) {
		var page = new pediatra.comp.pacientes.pagePaciente(data);
		page.addListenerOnce("close", function(e){
			delete mapPagePaciente[data.id_paciente];
		});
		mapPagePaciente[data.id_paciente] = page;
		tabviewMain.add(page);
		tabviewMain.setSelection([page]);
	} else {
		tabviewMain.setSelection([page]);
	}
});

//var popupMensajes = new pediatra.comp.popupMensajes();
//var popupNotas = new pediatra.comp.popupNotas();

var tabview = new qx.ui.tabview.TabView();
tabview.setMinHeight(0);
tabview.setHeight(29);

var page;
page = new qx.ui.tabview.Page("Pacientes");
page.getChildControl("button").addListener("execute", function(e){
	popupPacientes.show();
})
tabview.add(page);
page = new qx.ui.tabview.Page("Mensajes, citas, recordatorios");
page.getChildControl("button").addListener("execute", function(e){
	//popupMensajes.show();
})
tabview.add(page);
var page = new qx.ui.tabview.Page("Notas");
page.getChildControl("button").addListener("execute", function(e){
	//popupNotas.show();
})
tabview.add(page);

doc.add(tabview, {left: 0, right: 0, bottom: 0});






var mnuArchivo = new qx.ui.menu.Menu();
var btnAcercaDe = new qx.ui.menu.Button("Acerca de...");
btnAcercaDe.addListener("execute", function(){

});
mnuArchivo.add(btnAcercaDe);


  
var mnuEdicion = new qx.ui.menu.Menu();
var mnuVer = new qx.ui.menu.Menu();

var commandPacientes = new qx.ui.command.Command("Control+P");
commandPacientes.addListener("execute", function(e){
	tabview.getChildren()[0].getChildControl("button").execute();
});
var btnPacientes = new qx.ui.menu.Button("Pacientes", null, commandPacientes);
mnuVer.add(btnPacientes);

var commandMensajes = new qx.ui.command.Command("Control+M");
commandMensajes.addListener("execute", function(e){
	tabview.getChildren()[1].getChildControl("button").execute();
});
var btnMensajes = new qx.ui.menu.Button("Mensajes", null, commandMensajes);
mnuVer.add(btnMensajes);

var commandNotas = new qx.ui.command.Command("Control+N");
commandNotas.addListener("execute", function(e){
	tabview.getChildren()[2].getChildControl("button").execute();
});
var btnNotas = new qx.ui.menu.Button("Notas", null, commandNotas);
mnuVer.add(btnNotas);





var mnubtnArchivo = new qx.ui.toolbar.MenuButton('Archivo');
var mnubtnEdicion = new qx.ui.toolbar.MenuButton('Edición');
var mnubtnVer = new qx.ui.toolbar.MenuButton('Ver');

mnubtnVer.setMenu(mnuVer);
  

var toolbarMain = new qx.ui.toolbar.ToolBar();
toolbarMain.add(mnubtnArchivo);
toolbarMain.add(mnubtnEdicion);
toolbarMain.add(mnubtnVer);

doc.add(toolbarMain, {left: 5, top: 0, right: 5});


	
	
    }
  }
});
