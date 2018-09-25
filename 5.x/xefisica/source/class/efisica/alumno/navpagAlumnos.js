/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * TODO: needs documentation
 */
qx.Class.define("efisica.alumno.navpagAlumnos",
{
  extend : qx.ui.mobile.page.NavigationPage,

  construct : function()
  {
    this.base(arguments);
    this.setTitle("Administrar alumnos");
    
	this.addListenerOnce("appear", function(e){
		this.txtBuscar.fireDataEvent("changeValue", " ");
	}, this);
    
  },


  members :
  {
    // overridden
    _initialize: function() {
      this.base(arguments);
      
      
      this.getContent().setLayoutProperties({flex:3});
      
      var application = qx.core.Init.getApplication();
      
      application.navpagNuevoAlumno.form.addListener("aceptado", function(e){
      	txtBuscar.fireDataEvent("changeValue", txtBuscar.getValue());
      });
      
      

	var txtBuscar = this.txtBuscar = new qx.ui.mobile.form.TextField("");
	txtBuscar.setPlaceholder("Buscar...");
	txtBuscar.setLiveUpdate(true);
	
	txtBuscar.addListener("changeValue", function(e){
		var texto = e.getData().trim();
		
		var p;
		if (texto.length >= 3) p = {descrip: texto}; else p = {descrip: ""};
		
		if (p != null) {
			application.popupCargando.mostrar();
			
	        var timer = qx.util.TimerManager.getInstance();
	        // check for the old listener
	        if (this.timerId != null) {
	          // stop the old one
	          timer.stop(this.timerId);
	          if (this.rpc != null) this.rpc.abort(this.reference);
	          this.timerId = null;
	          
	          application.popupCargando.ocultar();
	        }
	        // start a new listener to update the controller
			this.timerId = timer.start(function(userData, timerId) {
				this.rpc = new qx.io.remote.Rpc("services/", "comp.Alumno");
				this.rpc.addListener("completed", qx.lang.Function.bind(function(e){
					var resultado = e.getData().result;
					listAlumnos.setModel(new qx.data.Array(resultado));

					this.rpc = null;
					this.timerId = null;
					
					application.popupCargando.ocultar();
				}, this));
				
				this.reference = this.rpc.callAsyncListeners(true, "buscar_alumno", p);
			}, null, this, null, 200);
		}
	});
      

	var composite = new qx.ui.mobile.container.Composite();
	composite.addCssClass("group");
	composite.add(txtBuscar);
	this.addAfterNavigationBar(composite);
	
	
	var btnEliminar = new qx.ui.mobile.list.renderer.Default().set({title: "Eliminar..."});
	btnEliminar.setUserData("model", "eliminar");
	var btnNuevo = new qx.ui.mobile.list.renderer.Default().set({title: "Nuevo..."});
	btnNuevo.setUserData("model", "nuevo");
	
	var popupContextMenu = new efisica.varios.popupContextMenu([btnEliminar, null, btnNuevo]);
	popupContextMenu.addListener("aceptado", function(e){
		var data = e.getData();
		
		if (data.model == "nuevo") {
			//var formAlumno = this.formAlumno = new efisica.alumno.formAlumno();
			//var rendererSingle = new qx.ui.mobile.form.renderer.Single(formAlumno);
			//var s = new qx.ui.mobile.container.Scroll();
			//s.add(rendererSingle);
			//var popupNuevo = new efisica.varios.popupModal(s);
			//popupNuevo.setTitle("Nuevo alumno");
			//popupNuevo.mostrar("nuevoAlumno");
			
			//var p = new qx.ui.mobile.page.NavigationPage();
			//p.add(rendererSingle);
			//p.show();
			//application.navpagNuevoAlumno.form.reset();
			application.navpagNuevoAlumno.mostrar("nuevoAlumno");
			
		} else if (data.model == "eliminar") {
			
		}
	}, this);
      
    
	var listAlumnos = new efisica.varios.List({
		configureItem: function(item, data, row) {
			item.setTitle(data.apenom);
			item.setSubtitle(((data.nro_doc == null) ? "" : data.tipo_doc + " " + data.nro_doc + ",") + data.domicilio + ", " + data.telefono);
			item.setSelectable(true);
		}
	});
	
	listAlumnos.addListener("changeSelection", function(e) {
		application.popupCargando.mostrar();
		
		var item = this.getModel().getItem(e.getData());

       	application.getRouting().executeGet("alumno");
       	application.navpagAlumno.actualizar(item.id_alumno);
       	
       	application.popupCargando.ocultar();
	});
	
	listAlumnos.addListener("contexttap", function(e) {
		var data = e.getData();
		var item = listAlumnos.getModel().getItem(data);
		
		btnEliminar.setEnabled(true);
		popupContextMenu.mostrar("contextMenu");
	}, this);
	
	this.getContent().add(listAlumnos);
	
	this.addListener("longtap", function(e){
		btnEliminar.setEnabled(false);
		popupContextMenu.mostrar("contextMenu");

	}, this)
	
    }
  }

});
