/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * TODO: needs documentation
 */
qx.Class.define("efisica.curso.navpagCursos",
{
  extend : qx.ui.mobile.page.NavigationPage,

  construct : function()
  {
    this.base(arguments);
    this.setTitle("Administrar cursos");
    
  },


  members :
  {
    // overridden
    _initialize: function() {
      this.base(arguments);
      
      this.addListenerOnce("appear", function(e){
      	actualizar();
      });
      
	var application = qx.core.Init.getApplication();
	var routing = application.getRouting();

	
	var actualizar = function(e){
		application.popupCargando.mostrar();
		
		var rpc = new qx.io.remote.Rpc("services/", "comp.Curso");
		rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
			lstCurso.setModel(new qx.data.Array(resultado));
			
			application.popupCargando.ocultar();
		}, this), "leer_cursos");
	};
	
	
	
	var btnModificar = new qx.ui.mobile.list.renderer.Default().set({title: "Modificar..."});
	btnModificar.setUserData("model", "modificar");
	var btnEliminar = new qx.ui.mobile.list.renderer.Default().set({title: "Eliminar..."});
	btnEliminar.setUserData("model", "eliminar");
	var btnNuevo = new qx.ui.mobile.list.renderer.Default().set({title: "Nuevo..."});
	btnNuevo.setUserData("model", "nuevo");

	var popupContextMenu = new efisica.varios.popupContextMenu([btnModificar, btnEliminar, null, btnNuevo]);
	popupContextMenu.addListener("aceptado", function(e){
		var data = e.getData();
		
		if (data.model == "modificar") {
			application.popupCargando.mostrar();
			
			popupCurso.setTitle("Modificar curso");
			
	    	var p = {id_curso: formCurso.id_curso};
	    	
			var rpc = new qx.io.remote.Rpc("services/", "comp.Curso");
			rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
				formCurso.controller.setModel(qx.data.marshal.Json.createModel(resultado));
				
				application.popupCargando.ocultar();
				popupCurso.mostrar("curso");
			}, this), "leer_curso", p);
			
		} else if (data.model == "eliminar") {

		} else if (data.model == "nuevo") {
			formCurso.id_curso = null;
			formCurso.resetter.reset();
			
			popupCurso.setTitle("Nuevo curso");
			popupCurso.mostrar("curso");
		}
	});
	
	
	

	
	
	var formCurso = new efisica.curso.formCurso();
	formCurso.addListener("aceptado", function(e){
		window.history.back();
		actualizar();
	});
	
	var popupCurso = new efisica.varios.popupModal(new qx.ui.mobile.form.renderer.Single(formCurso));

	
      

	var lstCurso = new efisica.varios.List({
		configureItem: function(item, data, row) {
			item.setTitle(data.descrip);
			item.setSubtitle(data.subtitle);
			item.setSelectable(true);
		}
	});
	
	lstCurso.addListener("changeSelection", function(e) {
		application.popupCargando.mostrar();
		
		var item = this.getModel().getItem(e.getData());

       	//application.getRouting().executeGet("/alumnos/alumno");
       	//application.navpagAlumno.actualizar(item.id_alumno);
       	
       	application.popupCargando.ocultar();
	});
	
	lstCurso.addListener("contexttap", function(e) {
		var data = e.getData();
		formCurso.id_curso = lstCurso.getModel().getItem(data).id_curso;
		
		btnModificar.setEnabled(true);
		btnEliminar.setEnabled(true);
		popupContextMenu.mostrar("contextMenu");
	});
	
	this.getContent().add(lstCurso);
	
	this.addListener("longtap", function(e){
		btnModificar.setEnabled(false);
		btnEliminar.setEnabled(false);
		popupContextMenu.mostrar("contextMenu");
	})
	

	
    }
  }

});
