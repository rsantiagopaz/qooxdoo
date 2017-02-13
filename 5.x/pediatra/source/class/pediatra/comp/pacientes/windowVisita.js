qx.Class.define("pediatra.comp.pacientes.windowVisita",
{
	extend : componente.comp.ui.ramon.window.Window,
	construct : function (rowPaciente, id_visita)
	{
		this.base(arguments);

	this.set({
		caption: "Visita",
		width: 800,
		height: 650,
		showMinimize: false,
		showMaximize: true
	});
	this.setLayout(new qx.ui.layout.Canvas());
		
	this.addListenerOnce("appear", function(){
		dtfFecha.focus();
	});
	
	
	var sharedErrorTooltip = qx.ui.tooltip.Manager.getInstance().getSharedErrorTooltip();
		

	
	var form = new qx.ui.form.Form();
	
	var dtfFecha = new qx.ui.form.DateField();
	dtfFecha.setRequired(true);
	dtfFecha.addListener("focusin", function(e) {
		window.setTimeout(function(){
			dtfFecha.getChildControl("textfield").selectAllText();
		}, 0);
	});
	form.add(dtfFecha, "Fecha", null, "fecha", null, {item: {row: 0, column: 1, colSpan: 3}});
	
	var txtMotivo = new qx.ui.form.TextArea("");
	form.add(txtMotivo, "Motivo", null, "motivo", null, {item: {row: 1, column: 1, colSpan: 5}});
	
	var txtAntecedentes = new qx.ui.form.TextArea("");
	form.add(txtAntecedentes, "Antecedentes", null, "antecedentes", null, {item: {row: 1, column: 7, colSpan: 5}});
	
	var txtPer_enc = new qx.ui.form.Spinner(0, 0, 100);
	txtPer_enc.setRequired(true);
	txtPer_enc.getChildControl("upbutton").setVisibility("excluded");
	txtPer_enc.getChildControl("downbutton").setVisibility("excluded");
	form.add(txtPer_enc, "Per.enc.", null, "per_enc", null, {item: {row: 2, column: 1, colSpan: 2}});
	
	var txtTalla = new qx.ui.form.Spinner(0, 0, 100);
	txtTalla.setRequired(true);
	txtTalla.getChildControl("upbutton").setVisibility("excluded");
	txtTalla.getChildControl("downbutton").setVisibility("excluded");
	form.add(txtTalla, "Talla", null, "talla", null, {item: {row: 2, column: 4, colSpan: 2}});

	var txtPeso = new qx.ui.form.Spinner(0, 0, 100);
	txtPeso.setRequired(true);
	txtPeso.getChildControl("upbutton").setVisibility("excluded");
	txtPeso.getChildControl("downbutton").setVisibility("excluded");
	form.add(txtPeso, "Peso", null, "peso", null, {item: {row: 2, column: 7, colSpan: 2}});

	var txtPresion = new qx.ui.form.Spinner(0, 0, 100);
	txtPresion.setRequired(true);
	txtPresion.getChildControl("upbutton").setVisibility("excluded");
	txtPresion.getChildControl("downbutton").setVisibility("excluded");
	form.add(txtPresion, "Presión", null, "presion", null, {item: {row: 2, column: 10, colSpan: 2}});
	
	
	var txtTexto = new qx.ui.form.TextArea("");
	form.add(txtTexto, "Texto", null, "texto", null, {item: {row: 3, column: 1, colSpan: 11}});
	
	
	var controllerForm = new qx.data.controller.Form(null, form);
		
	
	//var formView = new qx.ui.form.renderer.Single(form);
	var formView = new componente.comp.ui.ramon.abstractrenderer.Grid(form, 50, 50);
	
	this.add(formView, {left: 0, top: 0, right: 0});
	
	
	
	
	var tblParamed = new pediatra.comp.varios.tableParamed();
	var tableModelParamed = tblParamed.getTableModel();
	this.add(tblParamed, {left: 0, top: 300, right: 0, bottom: 40});
	
	
	
	
	var modelForm;
	if (id_visita == "0") {
		this.setCaption("Nueva visita - " + rowPaciente.apenom);
		
		modelForm = controllerForm.createModel(true);
		modelForm = qx.util.Serializer.toNativeObject(modelForm);
		
		modelForm.fecha = new Date;
		modelForm.id_visita = id_visita;
		modelForm.id_paciente = rowPaciente.id_paciente;
		modelForm = qx.data.marshal.Json.createModel(modelForm, true);
	} else {
		this.setCaption("Modificar visita - " + rowPaciente.apenom);
		
		var p = {};
		p.id_visita = id_visita;
		
		var rpc = new componente.comp.io.ramon.rpc.Rpc("services/", "comp.Pacientes");
		try {
			var resultado = rpc.callSync("leer_visita", p);
		} catch (ex) {
			alert("Sync exception: " + ex);
		}
		
		modelForm = qx.data.marshal.Json.createModel(resultado.visita, true);
		tableModelParamed.setDataAsMapArray(resultado.paramed, true);
	}
	controllerForm.setModel(modelForm);

	
	
	var btnAceptar = new qx.ui.form.Button("Aceptar");
	btnAceptar.addListener("execute", function(e){
		tblParamed.setValid(true);
		
		if (form.validate()) {
			//if (tableModelParamed.getRowCount() == 0) {
			if (false) {
				tblParamed.setValid(false);
				tblParamed.focus();
				
				sharedErrorTooltip.setLabel("Debe agregar algún parámetro");
				sharedErrorTooltip.placeToWidget(tblParamed);
				sharedErrorTooltip.show();
			} else {
				var p = {};
				p.model = qx.util.Serializer.toNativeObject(controllerForm.getModel());
				p.paramed = tableModelParamed.getDataAsMapArray();
				
				alert(qx.lang.Json.stringify(p, null, 2));
				
				var rpc = new componente.comp.io.ramon.rpc.Rpc("services/", "comp.Pacientes");
				rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id) {
					btnCancelar.execute();
					
					this.fireDataEvent("aceptado", resultado);
				}, this), "alta_modifica_visita", p);
			}
		} else {
			form.getValidationManager().getInvalidFormItems()[0].focus();
		}
		
		
		
		/*
		txtDescrip.setValid(true);
		
		if (txtDescrip.getValue()=="") {
			txtDescrip.setValid(false);
			txtDescrip.focus();
		} else if (tableModelItems.getRowCount()==0) {
			dialog.Dialog.warning("Debe agregar algún item a la tabla de items", function(e){cboProducto.focus();});
		} else {
			var p = {};
			p.id_ingreso_lugar = cboLugar.getModelSelection().getItem(0);
			p.descrip = txtDescrip.getValue();
			p.items = tableModelItems.getDataAsMapArray();
			
			var rpc = new qx.io.remote.Rpc("services/", "comp.Parametros");
			try {
				var resultado = rpc.callSync("grabar_ingreso", p);
			} catch (ex) {
				alert("Sync exception: " + ex);
			}
			
			btnCancelar.execute();
		}
		*/
	}, this);
	
	var btnCancelar = new qx.ui.form.Button("Cancelar");
	btnCancelar.addListener("execute", function(e){
		this.destroy();
	}, this);
	
	this.add(btnAceptar, {left: "35%", bottom: 0});
	this.add(btnCancelar, {right: "35%", bottom: 0});

	

		
		
	},
	members : 
	{

	}
});