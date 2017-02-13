qx.Class.define("pediatra.comp.varios.tableParamed",
{
  extend : componente.comp.ui.ramon.table.Table,
  construct : function (readOnly)
  {
	var tableModel = new qx.ui.table.model.Simple();
	tableModel.setColumns(["Descripción", "Tipo", "Código"], ["descrip", "tipo", "codigo"]);

	var custom = {tableColumnModel : function(obj) {
		return new qx.ui.table.columnmodel.Resize(obj);
	}};
  	
	this.base(arguments, tableModel, custom);
	
	
	
	var application = qx.core.Init.getApplication();
	
	var popup = new pediatra.comp.varios.popupParamed();
	popup.addListener("aceptado", function(e){
		var data = e.getData();
		
		tableModel.addRowsAsMapArray([data], null, true);
	})
	
	
	if (! readOnly) {
		//Menu de contexto
		
		var commandAgregar = new qx.ui.command.Command("Insert");
		commandAgregar.addListener("execute", function(e){
			popup.placeToWidget(this);
			popup.show();
		}, this);
			
		var menu = new componente.comp.ui.ramon.menu.Menu();
		var btnAgregar = new qx.ui.menu.Button("Agregar parámetro...", null, commandAgregar);
	
		var commandEliminar = new qx.ui.command.Command("Del");
		commandEliminar.setEnabled(false);
		commandEliminar.addListener("execute", function(e){
	
		}, this);
			
		var btnEliminar = new qx.ui.menu.Button("Eliminar parámetro...", null, commandEliminar);
		
		menu.add(btnAgregar);
		menu.addSeparator();
		menu.add(btnEliminar);
		menu.memorizar();
	}
	
	
	
	this.setShowCellFocusIndicator(false);
	this.toggleColumnVisibilityButtonVisible();
	this.toggleStatusBarVisible();
	if (! readOnly) this.setContextMenu(menu);
	
	var tableColumnModel = this.getTableColumnModel();
	var resizeBehavior = tableColumnModel.getBehavior();
	resizeBehavior.set(0, {width:"60%", minWidth: 100});
	resizeBehavior.set(1, {width:"20%", minWidth: 100});
	resizeBehavior.set(2, {width:"20%", minWidth: 100});
	
	var selectionModel = this.getSelectionModel();
	selectionModel.setSelectionMode(qx.ui.table.selection.Model.SINGLE_SELECTION);
	selectionModel.addListener("changeSelection", function(){
		var bool = (! selectionModel.isSelectionEmpty());
		commandEliminar.setEnabled(bool);
		menu.memorizar([commandEliminar]);
	});

		
	},
	members : 
	{

	}
});