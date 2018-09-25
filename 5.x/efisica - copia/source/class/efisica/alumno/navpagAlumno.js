/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * TODO: needs documentation
 */
qx.Class.define("efisica.alumno.navpagAlumno",
{
  extend : efisica.alumno.navpagTab,

  construct : function()
  {
    this.base(arguments);
    
    this.setTitle("alumno");
    
    this.application = qx.core.Init.getApplication();
  },


  members :
  {
    actualizar: function(id_alumno) {
    	if (id_alumno != null) this.id_alumno = id_alumno;
    	
    	this.application.popupCargando.mostrar();
    	
    	var p = {id_alumno: this.id_alumno};
    	
		var rpc = new qx.io.remote.Rpc("services/", "comp.Alumno");
		rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
			this.setTitle(resultado.datos.apellido + ", " + resultado.datos.nombre);
			
			this.formAlumno.id_alumno = this.id_alumno;
			this.formAlumno.controller.setModel(qx.data.marshal.Json.createModel(resultado.datos));
			this.lstActividad.setModel(new qx.data.Array(resultado.actividad));
			this.lstInscripcion.setModel(new qx.data.Array(resultado.inscripcion));
			this.lstPago.setModel(new qx.data.Array(resultado.pago));
			
			this.application.popupCargando.ocultar();
		}, this), "leer_alumno", p);
    },
    
    
    // overridden
    _initialize: function() {
	
	this.base(arguments);
      
	
	
	var application = this.application;
	var routing = application.getRouting();
	


	var formAlumno = this.formAlumno = new efisica.alumno.formAlumno();
	var rendererSingle = new qx.ui.mobile.form.renderer.Single(formAlumno);
	
	this.getContent().add(rendererSingle);
	this.tabButton1.setView(rendererSingle);
	
	
	
	
	

	
	var formTexto = new qx.ui.mobile.form.Form();
	
	var txtTexto = new qx.ui.mobile.form.TextArea("");
	txtTexto.setRequired(true);
	formTexto.add(txtTexto, null, null, "texto");
	
	var btnGuardarTexto = new qx.ui.mobile.form.Button("Guardar");
	btnGuardarTexto.addListener("tap", function(e){
		txtTexto.setValue(txtTexto.getValue().trim());
		
		if (formTexto.validate()) {
			var p = {};
			p.id_actividad = popupTexto.id_actividad;
			p.id_alumno = this.id_alumno;
			p.tipo = "T";
			p.texto = txtTexto.getValue();
			
			var rpc = new qx.io.remote.Rpc("services/", "comp.Alumno");
			rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
				window.history.back();
				
				this.actualizar();
			}, this), "agregar_documento", p);
		}
	}, this)
	formTexto.addButton(btnGuardarTexto);
	
	var containerScroll = new qx.ui.mobile.container.Scroll();
	containerScroll.add(new qx.ui.mobile.form.renderer.Single(formTexto));
	var popupTexto = new efisica.varios.popupModal(containerScroll);

		


    

	

	var btnEliminar = new qx.ui.mobile.list.renderer.Default().set({title: "Eliminar..."});
	btnEliminar.setUserData("model", "eliminar");
	var btnDocumento = new qx.ui.mobile.list.renderer.Default().set({title: "Nuevo documento..."});
	btnDocumento.setUserData("model", "documento");
	var btnTexto = new qx.ui.mobile.list.renderer.Default().set({title: "Nuevo texto..."});
	btnTexto.setUserData("model", "texto");
	
	var popupContextMenuActividad = new efisica.varios.popupContextMenu([btnEliminar, null, btnDocumento, btnTexto]);
	popupContextMenuActividad.addListener("aceptado", function(e){
		var data = e.getData();
		
		if (data.model == "eliminar") {
			qx.ui.mobile.dialog.Manager.getInstance().confirm("Eliminar actividad", "<br/>Desea eliminar la actividad seleccionada?<br/><br/>", function(index) {
				if (index==0) {
					var p = {};
					p.id_alumno = this.id_alumno;
					p.id_actividad = btnEliminar.getUserData("id_actividad");
					
					var rpc = new qx.io.remote.Rpc("services/", "comp.Alumno");
					rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
						this.actualizar();
					}, this), "eliminar_actividad", p);
				}
			}, this, ["Aceptar", "Cancelar"]);
		} else if (data.model == "texto") {
			formTexto.reset();
			popupTexto.setTitle("Nuevo texto");
			popupTexto.id_actividad = null;
	
			popupTexto.mostrar("alumnoTexto");
		}
	}, this);


	
	var fineUploaderOptions = {
	    // options
		button: btnDocumento._getContentElement(),
		autoUpload: true,
		request: {
			endpoint: 'services/php-traditional-server-master/endpoint.php'
		},
	    callbacks: {
	        onSubmit: function(id, name) {
	        	application.popupCargando.mostrarModal();
	        },
	        
	        onError: function(id, name, errorReason, xhr) {
	        	alert(qx.lang.Json.stringify({id: id, name: name, errorReason: errorReason}, null, 2));
	        },
	        
	        onComplete: qx.lang.Function.bind(function(id, name, responseJSON, xhr) {
	        	application.popupCargando.ocultarModal();
	        	
	        	if (responseJSON.success) {
	        		var p = {};
	        		p.id_alumno = this.id_alumno;
	        		p.tipo = "D";
	        		p.uuid = responseJSON.uuid;
	        		p.uploadName = responseJSON.uploadName;
	        		
					var rpc = new qx.io.remote.Rpc("services/", "comp.Alumno");
					rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
						application.popupCargando.ocultarModal();
						
						this.actualizar();
					}, this), "agregar_documento", p);
	        	} else {
	        		application.popupCargando.ocultarModal();
	        	}
	        }, this)
	    }
	};
	
	var fineUploader = new qq.FineUploaderBasic(fineUploaderOptions);

	
	

      

      
      
      
	var lstActividad = this.lstActividad = new efisica.varios.List({
		configureItem: function(item, data, row) {
			item.setTitle(data.texto);
			item.setSubtitle(data.fecha);
			if (data.tipo == "D") item.setImage("efisica/icon/text-plain.png");
			item.setSelectable(true);
		}
	});
	
	lstActividad.addListener("changeSelection", function(e) {
		var item = lstActividad.getModel().getItem(e.getData());
		
		if (item.tipo == "D") {
			window.open("services/documentos/alumno/" + this.id_alumno + "/" + item.texto);
		} else {
			var p = {id_actividad: item.id_actividad};
			
			var rpc = new qx.io.remote.Rpc("services/", "comp.Alumno");
			rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
				formTexto.reset();
				txtTexto.setValue(resultado);
				popupTexto.setTitle("Modificar texto");
      			popupTexto.id_actividad = item.id_actividad;
      			
				popupTexto.mostrar("alumnoTexto");
			}, this), "leer_actividad_texto", p);
		}
	}, this);
	
	lstActividad.addListener("contexttap", function(e) {
		var data = e.getData();
		var item = lstActividad.getModel().getItem(data);
		
		btnEliminar.setUserData("id_actividad", item.id_actividad);
		
		btnEliminar.setEnabled(true);
		popupContextMenuActividad.mostrar("contextMenu");
	}, this);
	
	

	
	this.getContent().add(lstActividad);
	this.tabButton2.setView(lstActividad);
	

	
	
	
	
	
	
	var btnAnularIns = new qx.ui.mobile.list.renderer.Default().set({title: "Anular inscripción..."});
	btnAnularIns.setUserData("model", "anular");
	var btnInscribir = new qx.ui.mobile.list.renderer.Default().set({title: "Inscribir..."});
	btnInscribir.setUserData("model", "inscribir");
	
	var popupContextMenuInscripcion = new efisica.varios.popupContextMenu([btnAnularIns, null, btnInscribir]);
	popupContextMenuInscripcion.addListener("aceptado", function(e){
		var data = e.getData();
		
		if (data.model == "inscribir") {
			var popupInscribir = new efisica.alumno.popupInscribir(this.id_alumno);
			popupInscribir.addListener("aceptado", function(e){
				this.actualizar();
			}, this);
			popupInscribir.mostrar("inscribir");
		}
	}, this);
	
	
	
	var lstInscripcion = this.lstInscripcion = new efisica.varios.List({
		configureItem: function(item, data, row) {
			item.setTitle(data.descrip);
			item.setSubtitle(data.subtitle);
			item.setSelectable(true);
		}
	});

	lstInscripcion.addListener("changeSelection", function(e) {
		var data = e.getData();
		var item = lstInscripcion.getModel().getItem(data);
		
		var p = {};
		p.id_inscripcion = item.id_inscripcion;
		
		var form = new qx.ui.mobile.form.Form();
		var txt = new qx.ui.mobile.form.NumberField("");
		form.add(txt, "Monto", function(value, item){
			if (value.trim()=="" || parseInt(value) <= 0) throw new qx.core.ValidationError("Validation Error", "Debe ingresar el monto");
		});
		var btn = new qx.ui.mobile.form.Button("Guardar");
		btn.addListener("tap", function(e){
			if (form.validate()) {
				p.monto = txt.getValue();
				
				var rpc = new qx.io.remote.Rpc("services/", "comp.Curso");
				rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
					window.history.back();
					
					this.actualizar();
				}, this), "grabar_pago", p);
			}
		}, this);
		form.addButton(btn);
	
		var popupModal = new efisica.varios.popupModal(new qx.ui.mobile.form.renderer.Single(form));
		
		
		var rpc = new qx.io.remote.Rpc("services/", "comp.Curso");
		rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
			txt.setValue(resultado.monto);
			popupModal.setTitle("Pago nro. " + resultado.cant_pagos);
			
			popupModal.mostrar("pagarCuota");
		}, this), "leer_pago", p);
		
	}, this);
	
	lstInscripcion.addListener("contexttap", function(e) {
		var data = e.getData();
		var item = lstInscripcion.getModel().getItem(data);
		
		btnAnularIns.setEnabled(true);
		popupContextMenuInscripcion.mostrar("contextMenu");
	}, this);
	
	this.getContent().add(lstInscripcion);
	this.tabButton3.setView(lstInscripcion);
	
	
	
	
	

	
	var lstPago = this.lstPago = new qx.ui.mobile.list.List({
		configureItem: function(item, data, row) {
			item.setTitle(data.descrip);
			item.setSubtitle(data.subtitle);
			item.setSelectable(true);
		}
	});
	
	this.getContent().add(lstPago);
	this.tabButton4.setView(lstPago);
	
	
	
	
	this.addListener("longtap", function(e){
		if (this.tabButton1.getView().isVisible()) {
			
		} else if (this.tabButton2.getView().isVisible()){
			btnEliminar.setEnabled(false);
			popupContextMenuActividad.mostrar("contextMenu");			
		} else if (this.tabButton3.getView().isVisible()){
			btnAnularIns.setEnabled(false);
			popupContextMenuInscripcion.mostrar("contextMenu");
		} else if (this.tabButton4.getView().isVisible()){
			
		}
	}, this)
	
    }
  }

});
