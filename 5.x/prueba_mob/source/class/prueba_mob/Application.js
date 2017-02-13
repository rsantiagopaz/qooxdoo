/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * This is the main application class of your custom application "prueba_mob"
 *
 * @asset(prueba_mob/*)
 */
qx.Class.define("prueba_mob.Application",
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



      
var routing = qx.core.Init.getApplication().getRouting();
      
var btnClose = new qx.ui.mobile.form.Button("Close Popup");

var popup = new qx.ui.mobile.dialog.Popup(btnClose);
popup.setModal(true);
popup.setTitle("A Popup");

btnClose.addListener("tap", function() {
  //routing.executeGet("/", {reverse:true});
	alert(qx.lang.Json.stringify(qx.application.Routing.__back, null, 2));
  routing.back();
  alert(qx.lang.Json.stringify(qx.application.Routing.__back, null, 2));
  //routing.executeGet("/", {reverse:true});
  //alert(routing.getState());
});


var page1 = new qx.ui.mobile.page.NavigationPage();
page1.setTitle("Page 1");
page1.addListener("initialize", function() {
  var button = new qx.ui.mobile.form.Button("Popup");
  page1.getContent().add(button);

  button.addListener("tap", function() {
  	alert(qx.lang.Json.stringify(qx.application.Routing.__back, null, 2));
    routing.executeGet("/popup");
    alert(qx.lang.Json.stringify(qx.application.Routing.__back, null, 2));
  });
});

var manager = new qx.ui.mobile.page.Manager();
manager.addDetail([page1]);
      
routing.onGet("/", page1.show, page1);
routing.onGet("/popup", popup.show, popup);

routing.init();
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      /*
      var login = new prueba_mob.page.Login();
      var overview = new prueba_mob.page.Overview();

      // Add the pages to the page manager.
      var manager = new qx.ui.mobile.page.Manager(false);
      manager.addDetail([
        login,
        overview
      ]);

      // Initialize the application routing
      this.getRouting().onGet("/", this._show, login);
      this.getRouting().onGet("/overview", this._show, overview);

      this.getRouting().init();
      */
      
      
    },


    /**
     * Default behaviour when a route matches. Displays the corresponding page on screen.
     * @param data {Map} the animation properties
     */
    _show : function(data) {
    	alert(qx.lang.Json.stringify(data.customData, null, 2));
      this.show(data.customData);
    }
  }
});
