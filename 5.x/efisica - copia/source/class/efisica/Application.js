/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * This is the main application class of your custom application "efisica"
 *
 * @asset(efisica/*)
 */
qx.Class.define("efisica.Application",
{
  extend : qx.application.Mobile,


  members :
  {

    /**
     * This method contains the initial application code and gets called
     * during startup of the application
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
        // support additional cross-browser console.
        // Trigger a "longtap" event on the navigation bar for opening it.
        qx.log.appender.Console;
      }

      /*
      -------------------------------------------------------------------------
        Below is your actual application code...
        Remove or edit the following code to create your application.
      -------------------------------------------------------------------------
      */

      
      this.getRoot().setFontScale(1.5);
      
      
      
      
      
      var popupCargando = this.popupCargando = new efisica.varios.popupCargando(new qx.ui.mobile.dialog.BusyIndicator("Cargando..."));
      

      // Create the pages
      var overview = new efisica.page.Overview();
      var navpagAlumnos = new efisica.alumno.navpagAlumnos();
      var navpagAlumno = this.navpagAlumno = new efisica.alumno.navpagAlumno();

	var formAlumno = new efisica.alumno.formAlumno();
	formAlumno.redefineResetter();
	formAlumno.addListener("aceptado", function(e){
		window.history.back();
	})
      var navpagNuevoAlumno = this.navpagNuevoAlumno = new efisica.varios.NavigationPage();
      navpagNuevoAlumno.form = formAlumno;
      navpagNuevoAlumno.addListener("initialize", function(e){
      	this.setTitle("Nuevo alumno");

		this.getContent().add(new qx.ui.mobile.form.renderer.Single(this.form));
      });
      
      
      var navpagCursos = new efisica.curso.navpagCursos();
      
      //var list = new elpintao_mob.comp.List();
      //var form = new elpintao_mob.comp.Form();
      //var recibir_pedidos = new elpintao_mob.comp.npgRecibirPedidos(true);

      // Add the pages to the page manager
      var manager = this.manager = new qx.ui.mobile.page.Manager();
      manager.setHideMasterButtonCaption("Ocultar");
      manager.addMaster([overview]);
	manager.addDetail([
		navpagAlumnos,
		navpagAlumno,
		navpagNuevoAlumno,
		navpagCursos
        //recibir_pedidos,
        //list,
        //form
	]);

      // Initialize the navigation
      var routing = this.routing = this.getRouting();

      if (qx.core.Environment.get("device.type") == "tablet" || qx.core.Environment.get("device.type") == "desktop") {
        routing.onGet("/.*", this._show, overview);
        routing.onGet("/", this._show, navpagAlumnos);
      }

      routing.onGet("/", this._show, overview);
      routing.onGet("alumnos", this._show, navpagAlumnos);
      routing.onGet("alumno", this._show, navpagAlumno);
      routing.onGet("cursos", this._show, navpagCursos);
      //routing.onGet("/recibir_pedidos", this._show, recibir_pedidos);
      //routing.onGet("/form", this._show, form);
      //routing.onGet("/list", this._show, list);


      routing.init();
    },


    /**
     * Default behaviour when a route matches. Displays the corresponding page on screen.
     * @param data {Map} the animation properties
     */
    _show : function(data) {
      this.show(data.customData);
    }
  }
});
