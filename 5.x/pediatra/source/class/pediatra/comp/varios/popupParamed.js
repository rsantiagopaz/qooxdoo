qx.Class.define("pediatra.comp.varios.popupParamed",
{
  extend : componente.comp.ui.ramon.popup.Popup,
  construct : function ()
  {
	this.base(arguments);
	
	this.set({
		width: 700,
		height: 200
	});
	
	this.setLayout(new qx.ui.layout.Canvas());
	
	this.addListener("appear", function(e){
		txtBuscar.focus();
	});
	
	
	
	var application = qx.core.Init.getApplication();
	
	
	
	var commandEsc = new qx.ui.command.Command("Esc");
	commandEsc.addListener("execute", function(e){
		this.hide();
	}, this);
	this.registrarCommand(commandEsc);
		
	var commandSeleccionar = new qx.ui.command.Command("Enter");
	commandSeleccionar.setEnabled(false);
	commandSeleccionar.addListener("execute", function(e){
		var rowData = tableModel.getRowDataAsMap(tbl.getFocusedRow());
		this.fireDataEvent("aceptado", rowData);
		commandEsc.execute();
	}, this);
		
	var menu = new componente.comp.ui.ramon.menu.Menu();
	menu.addListener("appear", function(e){
		menu.setZIndex(this.getZIndex() + 1);
		this.setAutoHide(false);
	}, this);
	menu.addListener("disappear", function(e){
		this.setAutoHide(true);
	}, this);
	
	var btnSeleccionar = new qx.ui.menu.Button("Seleccionar", null, commandSeleccionar);
	menu.add(btnSeleccionar);
	menu.memorizar();

		
	var txtBuscar = new qx.ui.form.TextField("");
	txtBuscar.setPlaceholder("Buscar");
	txtBuscar.setLiveUpdate(true);
	txtBuscar.addListener("changeValue", function(e){
		var data = e.getData().trim();
		if (data == "") {
			tableModel.setDataAsMapArray([], true);
		} else if (data.length >= 3) {
			var p = {};
			p.texto = data;
			var rpc = new qx.io.remote.Rpc("services/", "comp.Parametros");
			rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id) {
				tbl.setFocusedCell();
				tableModel.setDataAsMapArray(resultado, true);
				if (resultado.length > 0) tbl.setFocusedCell(0, 0, true);
			}, this), "buscar_paramed", p);
		}
	});
	txtBuscar.addListener("keypress", function(e){
		if (e.getKeyIdentifier()=="Down" && tableModel.getRowCount() > 0) tbl.focus();
	});
	
	this.add(txtBuscar, {left: 5, top: 5, right: 5});
		
		
		//Tabla

		var tableModel = new qx.ui.table.model.Simple();
		tableModel.setColumns(["Descripción", "Tipo", "Código"], ["descrip", "tipo", "codigo"]);

		var custom = {tableColumnModel : function(obj) {
			return new qx.ui.table.columnmodel.Resize(obj);
		}};
		
		var tbl = new componente.comp.ui.ramon.table.Table(tableModel, custom);
		tbl.toggleShowCellFocusIndicator();
		tbl.toggleColumnVisibilityButtonVisible();
		tbl.toggleStatusBarVisible();
		tbl.setContextMenu(menu);
		
		tbl.addListener("cellDbltap", function(e){
			commandSeleccionar.execute();
		});
		
		var selectionModel = tbl.getSelectionModel();
		selectionModel.setSelectionMode(qx.ui.table.selection.Model.SINGLE_SELECTION);
		selectionModel.addListener("changeSelection", function(){
			var bool = (! selectionModel.isSelectionEmpty());
			menu.memorizarEnabled([commandSeleccionar], bool);
		});
		
		var tableColumnModel = tbl.getTableColumnModel();
		var resizeBehavior = tableColumnModel.getBehavior();
		resizeBehavior.set(0, {width:"60%", minWidth:100});
		resizeBehavior.set(1, {width:"20%", minWidth:100});
		resizeBehavior.set(2, {width:"20%", minWidth:100});

		
		
		this.add(tbl, {left: 5, top: 35, right: 5, bottom: 5});
		
		
	},
	members : 
	{

	},
	events : 
	{
		"seleccionado": "qx.event.type.Event"
	}
});