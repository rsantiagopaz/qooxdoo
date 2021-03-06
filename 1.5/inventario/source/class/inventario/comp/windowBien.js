qx.Class.define("inventario.comp.windowBien",
{
	extend : controles.window.Window,
	construct : function (appMain, id_bien)
	{
	this.base(arguments);
	
	this.set({
		width: 580,
		height: 450,
		showMinimize: false,
		showMaximize: false
	});
	
	this.setLayout(new qx.ui.layout.Canvas());
	this.setResizable(false, false, false, false);

	this.addListenerOnce("appear", function(e){
		tbl.setFocusedCell(0, 0, true);
		txtDescrip.focus();
	});
	
	
	
	var model;
	


	//Tabla

	var tableModel = new qx.ui.table.model.Simple();
	tableModel.setColumns(["Nros.serie"], ["nro_serie"]);
	tableModel.setEditable(true);

	var custom = {tableColumnModel : function(obj) {
		return new qx.ui.table.columnmodel.Resize(obj);
	}};
	
	var tbl = new controles.table.Table(tableModel, custom);
	//tblTotales.toggleShowCellFocusIndicator();
	tbl.setHeight(224);
	tbl.setWidth(150);
	tbl.getSelectionModel().setSelectionMode(qx.ui.table.selection.Model.SINGLE_SELECTION);
	tbl.setShowCellFocusIndicator(false);
	tbl.toggleColumnVisibilityButtonVisible();
	tbl.toggleStatusBarVisible();
	tbl.edicion = "edicion_vertical";
	
	tbl.addListener("dataEdited", function(e){
		this.debug("dataEdited");
		var data = e.getData();
		data.value = qx.lang.String.trim(data.value);
		tableModel.setValueById("nro_serie", data.row, data.value);
	});
	
	this.add(tbl, {left: 400, top: 0});
	

	
	var form = new qx.ui.form.Form();
	
	var txtDescrip = new qx.ui.form.TextField("");
	txtDescrip.setRequired(true);
	txtDescrip.setWidth(300);
	txtDescrip.addListener("blur", function(e){
		txtDescrip.setValue(txtDescrip.getValue().trim());
	});
	form.add(txtDescrip, "Descripción", null, "descrip");
	
	var txtNroexp = new qx.ui.form.TextField("");
	txtNroexp.setRequired(true);
	txtNroexp.addListener("blur", function(e){
		txtNroexp.setValue(txtNroexp.getValue().trim());
	});	
	form.add(txtNroexp, "Nro.exped.", null, "nro_expediente");
	
	var slbTipo_bien = new qx.ui.form.SelectBox();
	slbTipo_bien.setRequired(true);
	form.add(slbTipo_bien, "Tipo bien", null, "id_tipo_bien");
	
	var slbTipo_alta = new qx.ui.form.SelectBox();
	slbTipo_alta.setRequired(true);
	form.add(slbTipo_alta, "Tipo alta", null, "id_tipo_alta");
	
	var txtProveedor = new qx.ui.form.TextField("");
	form.add(txtProveedor, "Proveedor", null, "proveedor");
	
	var txtNro_remito = new qx.ui.form.TextField("");
	form.add(txtNro_remito, "Nro.remito", null, "nro_remito");
	
	var txtCuit = new qx.ui.form.TextField("");
	form.add(txtCuit, "CUIT", null, "cuit");
	
	var txtObserva_alta = new qx.ui.form.TextArea("");
	form.add(txtObserva_alta, "Observaciones", null, "observa_alta");
	
	var spnCantidad = new qx.ui.form.Spinner();
	spnCantidad.setWidth(50);
	spnCantidad.addListener("changeValue", function(e){
		var rowCount = tableModel.getRowCount();
		var cantidad = e.getData();
		if (cantidad >= rowCount) {
			for (var i=rowCount; i < cantidad; i++) {
				tableModel.addRowsAsMapArray([{nro_serie: ""}], null, true);
			}
		} else {
			tbl.setFocusedCell(0, 0, true);
			for (var i=rowCount; i > cantidad; --i) {
				tableModel.removeRows(i - 1, 1);
			}
		}
	});
	spnCantidad.setMinimum(1);
	spnCantidad.setMaximum(100);
	form.add(spnCantidad, "Cantidad", null, "cantidad");

	
	var formView = new qx.ui.form.renderer.Single(form);
	this.add(formView, {left: 0, top: 0})
	
	
	
	

	
	
	
	

	
	var rpc = new qx.io.remote.Rpc("services/", "comp.Inventario");
	try {
		var resultado = rpc.callSync("leer_parametros_inicio");
	} catch (ex) {
		alert("Sync exception: " + ex);
	}
	
	for (var x in resultado.tipo_bien) {
		slbTipo_bien.add(new qx.ui.form.ListItem(resultado.tipo_bien[x].descrip, null, resultado.tipo_bien[x].id_tipo_bien));
	}
	for (var x in resultado.tipo_alta) {
		slbTipo_alta.add(new qx.ui.form.ListItem(resultado.tipo_alta[x].descrip, null, resultado.tipo_alta[x].id_tipo_alta));
	}
	
	
	var controller = new qx.data.controller.Form(null, form);
	
	
	
	if (id_bien=="0") {
		this.setCaption("Alta de bien");
		model = controller.createModel(true);
	} else {
		this.setCaption("Modificación de bien");
		
		var p = {};
		p.id_bien = id_bien;
		var rpc = new qx.io.remote.Rpc("services/", "comp.Inventario");
		try {
			var resultado = rpc.callSync("leer_bien", p);
		} catch (ex) {
			alert("Sync exception: " + ex);
		}
		
		model = qx.data.marshal.Json.createModel(resultado.model);
		controller.setModel(model);
	}
	

	
	var btnAceptar = new qx.ui.form.Button("Aceptar");
	btnAceptar.addListener("execute", function(e){
		if (form.validate()) {
			var p = {};
			p.model = qx.util.Serializer.toNativeObject(model);
			p.model.id_bien = id_bien;
			if (id_bien="0") {
				p.model.id_oas_usuario_alta = appMain.rowOrganismo_area.id_oas_usuario;
				p.model.organismo_area_id = appMain.rowOrganismo_area.organismo_area_id;
				p.model.id_organismo_area_servicio_destino = appMain.rowOrganismo_area.id_organismo_area_servicio_destino;
			}
			p.model.estado = 1;
			p.nro_serie = tableModel.getDataAsMapArray();
			
			//alert(qx.lang.Json.stringify(p, null, 2));
			
			var rpc = new qx.io.remote.Rpc("services/", "comp.Inventario");
			try {
				var resultado = rpc.callSync("alta_modifica_bien", p);
			} catch (ex) {
				alert("Sync exception: " + ex);
			}
			
			this.fireDataEvent("aceptado", resultado);
			btnCancelar.execute();
		} else {
			var items = form.getItems();
			for (var item in items) {
				if (!items[item].isValid()) {
					items[item].focus();
					break;
				}
			}
		}
	}, this);
	this.add(btnAceptar, {left: 100, bottom: 0});
	
	var btnCancelar = new qx.ui.form.Button("Cancelar");
	btnCancelar.addListener("execute", function(e){
		this.destroy();
	}, this);
	this.add(btnCancelar, {left: 200, bottom: 0});
	
	
	txtDescrip.setTabIndex(1);
	txtNroexp.setTabIndex(2);
	slbTipo_bien.setTabIndex(3);
	slbTipo_alta.setTabIndex(4);
	txtProveedor.setTabIndex(5);
	txtNro_remito.setTabIndex(6);
	txtCuit.setTabIndex(7);
	txtObserva_alta.setTabIndex(8);
	spnCantidad.setTabIndex(9);
	tbl.setTabIndex(10);


	},
	members : 
	{

	},
	events : 
	{
		"aceptado": "qx.event.type.Event"
	}
});