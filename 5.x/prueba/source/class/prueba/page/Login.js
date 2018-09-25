/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * TODO: needs documentation
 */
qx.Class.define("prueba.page.Login",
{
  extend : qx.ui.mobile.page.NavigationPage,

  construct : function()
  {
    this.base(arguments);
    this.setTitle("Login");
    
    //this.__preloadThemes();
  },
  
  
  statics :
  {
    THEMES: [{
      "name": "Indigo",
      "css": "resource/mobileshowcase/css/indigo.css"
    }, {
      "name": "Flat",
      "css": "resource/mobileshowcase/css/flat.css"
    }]
  },


  members :
  {
    __form: null,
    
    
    __preloadThemes : function() {
    	alert("sdf");
      for(var i = 0; i < this.self(arguments).THEMES.length; i++) {
        var cssResource = this.self(arguments).THEMES[i].css;
        var cssURI = qx.util.ResourceManager.getInstance().toUri(cssResource);

        var req = new qx.bom.request.Xhr();

        req.open("GET", cssURI);
        req.send();
      }
    },


    // overridden
    _initialize: function() {
      this.base(arguments);

      // Username
      var user = new qx.ui.mobile.form.TextField();
      user.setRequired(true);

      // Password
      var pwd = new qx.ui.mobile.form.PasswordField();
      pwd.setRequired(true);

      // Login Button
      var loginButton = new qx.ui.mobile.form.Button("Login");
      loginButton.addListener("tap", this._onButtonTap, this);

      var loginForm = this.__form = new qx.ui.mobile.form.Form();
      loginForm.add(user, "Username");
      loginForm.add(pwd, "Password");

      // Use form renderer
      this.getContent().add(new qx.ui.mobile.form.renderer.Single(loginForm));
      this.getContent().add(loginButton);
    },


    /**
     * Event handler for <code>tap</code> on the login button.
     */
    _onButtonTap: function() {
      // use form validation
      if (this.__form.validate()) {
        qx.core.Init.getApplication().getRouting().executeGet("/overview");
      }
    }
  }

});
