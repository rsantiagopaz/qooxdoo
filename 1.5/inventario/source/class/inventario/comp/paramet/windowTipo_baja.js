qx.Class.define("inventario.comp.paramet.windowTipo_baja",
{
	extend : controles.window.Window,
	construct : function (appMain)
	{
		this.base(arguments);

	this.set({
		caption: "Tipo de baja",
		width: 400,
		height: 400,
		showMinimize: false,
		showMaximize: false
	});
	this.setLayout(new qx.ui.layout.Canvas());
	
	this.addListenerOnce("appear", function(){
		tbl.focus();
	});
		
		
	var commandAgregar = new qx.ui.core.Command("Insert");
	commandAgregar.addListener("execute", function(e){
		btnAceptar.setEnabled(true);
		tableModel.addRowsAsMapArray([{id_tipo_baja: "0", descrip: "Nuevo tipo baja", alta: true, modificado: false, eliminado: false}], null, true);
		tbl.setFocusedCell(0, tableModel.getRowCount()-1, true);
		tbl.startEditing();
	});
	var commandEditar = new qx.ui.core.Command("F2");
	commandEditar.setEnabled(false);
	commandEditar.addListener("execute", function(e){
		tbl.startEditing();
	});
	
	
	var menu = new controles.menu.Menu();
	var btnAgregar = new qx.ui.menu.Button("Agregar tipo", null, commandAgregar);
	var btnCambiar = new qx.ui.menu.Button("Editar", null, commandEditar);
	menu.add(btnAgregar);
	menu.addSeparator();
	menu.add(btnCambiar);
	menu.memorizar();

		

		
		//Tabla

		var tableModel = new qx.ui.table.model.Filtered();
		tableModel.setColumns(["Descripción"], ["descrip"]);
		tableModel.setEditable(true);

		var custom = {tableColumnModel : function(obj) {
			return new qx.ui.table.columnmodel.Resize(obj);
		}};
		
		var tbl = new controles.table.Table(tableModel, custom);
		tbl.getSelectionModel().setSelectionMode(qx.ui.table.selection.Model.SINGLE_SELECTION);
		tbl.setShowCellFocusIndicator(true);
		tbl.toggleColumnVisibilityButtonVisible();
		tbl.toggleStatusBarVisible();
		
		var tableColumnModel = tbl.getTableColumnModel();
		var resizeBehavior = tableColumnModel.getBehavior();
		//resizeBehavior.set(0, {width:"60%", minWidth:100});
		//resizeBehavior.set(1, {width:"20%", minWidth:100});
		//resizeBehavior.set(2, {width:"20%", minWidth:100});
		tableColumnModel.getCellEditorFactory(0).setValidationFunction(function(newValue, oldValue){
			newValue = newValue.trim();
			if (newValue=="" || newValue.indexOf("*") > -1) return oldValue; else return newValue;
		});
		
		var selectionModel = tbl.getSelectionModel();
		selectionModel.addListener("changeSelection", function(){
			var aux = (selectionModel.getSelectedCount() > 0);
			commandEditar.setEnabled(aux);
			menu.memorizar([commandEditar]);
		});
		
		
		
		
		

		tbl.setContextMenu(menu);

		
		
		this.add(tbl, {left: 0, top: 0, right: 0, bottom: 35});
		
		tbl.addListener("dataEdited", function(e){
			var focusedRow = tbl.getFocusedRow();
			var original = tableModel.getRowData(focusedRow);
			if (original.id_motivo=="1") {
				tableModel.setValueById("descrip", focusedRow, original.descrip);
			} else {
				var actual = tableModel.getRowDataAsMap(focusedRow);
				original.descrip = actual.descrip;
				original.modificado = true;
				tableModel.setRowsAsMapArray([original], focusedRow, true);
				btnAceptar.setEnabled(true);
			}
		}, this);
		
		
		
		
		

		var commandEsc = new qx.ui.core.Command("Esc");
		this.registrarCommand(commandEsc);
		commandEsc.addListener("execute", function(e){
			if (!tbl.isEditing()) btnCancelar.fireEvent("execute");
		});
		
		var btnAceptar = new qx.ui.form.Button("Aceptar");
		btnAceptar.setEnabled(false);
		btnAceptar.addListener("execute", function(e){
			var rowData;
			var enviar = false;
			var cambios = {altas: [], modificados: []};
			for (var i=0; i < tableModel.getRowCount(); i++) {
				rowData = tableModel.getRowData(i);
				if (rowData.alta) {
					cambios.altas.push(rowData);
					enviar = true;
				} else if (rowData.modificado) {
					cambios.modificados.push(rowData);
					enviar = true;
				}
				for (var x=0; x < tableModel.getRowCount(); x++) {
					if (x != i && rowData.descrip.toUpperCase()==tableModel.getValueById("descrip", x).toUpperCase()) {
						tbl.setFocusedCell(0, x, true);
						dialog.Dialog.alert("No puede haber tipos duplicados.", function(){tbl.focus();});
						return;
					}
				}
			}
			if (enviar) {
				var p = {};
				p.cambios = cambios;
				var rpc = new qx.io.remote.Rpc("services/", "comp.Paramet");
				try {
					var resultado = rpc.callSync("escribir_tipo_baja", p);
				} catch (ex) {
					alert("Sync exception: " + ex);
				}
			}
			btnCancelar.fireEvent("execute");
		}, this);

		var btnCancelar = new qx.ui.form.Button("Cancelar");
		btnCancelar.addListener("execute", function(e){
			this.destroy();
		}, this);
		
		this.add(btnAceptar, {left: 80, bottom: 0});
		this.add(btnCancelar, {left: 220, bottom: 0});
		

		
		var rpc = new qx.io.remote.Rpc("services/", "comp.Paramet");
		try {
			var resultado = rpc.callSync("leer_tipo_baja");
		} catch (ex) {
			alert("Sync exception: " + ex);
		}

		tableModel.setDataAsMapArray(resultado, true);
		if (tableModel.getRowCount() > 0) tbl.setFocusedCell(0, 0, true);
		


	
		
		
	},
	members : 
	{

	}
});