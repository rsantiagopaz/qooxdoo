qx.Class.define("pediatra.comp.pacientes.pagePaciente",
{
	extend : qx.ui.tabview.Page,
	construct : function (rowPaciente)
	{
	this.base(arguments);

	this.setLabel(" " + rowPaciente.apenom + " ");
	this.setLayout(new qx.ui.layout.Canvas());
	this.toggleShowCloseButton();
	
	this.addListenerOnce("appear", function(e){
		tblVisita.focus();
		txtMotivo.setBackgroundColor("#ffffc0");
	});
	
	var application = qx.core.Init.getApplication();
	
	
	var functionActualizarVisitas = function(id_visita) {
		var p = {};
		p.id_paciente = rowPaciente.id_paciente;
		
		var rpc = new componente.comp.io.ramon.rpc.Rpc("services/", "comp.Pacientes");
		rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id) {
			for (var x = 0; x < resultado.length; x++) {
				resultado[x].index = x + 1;
			}
			
			//alert(qx.lang.Json.stringify(resultado, null, 2));
			
			tableModelVisita.setDataAsMapArray(resultado, true);
			
			if (id_visita != null) tblVisita.buscar("id_visita", id_visita); else tblVisita.buscar("index", 1);
		}, this), "leer_visitas", p);		
	};
	
	
	var rowDataVisita;
	
	var stack = new qx.ui.container.Stack();
	
	
	
	var composite = new qx.ui.container.Composite(new qx.ui.layout.Canvas());
	
	
	
	//Menu de contexto
	
	var commandNuevaVisita = new qx.ui.command.Command("Insert");
	commandNuevaVisita.addListener("execute", function(e){
		var win = new pediatra.comp.pacientes.windowVisita(rowPaciente, "0");
		win.addListener("aceptado", function(e){
			var data = e.getData();
			
			functionActualizarVisitas(data);
		})
		application.getRoot().add(win);
		win.center();
		win.open();
	}, this);
		
	var menuVisita = new componente.comp.ui.ramon.menu.Menu();
	var btnNuevaVisita = new qx.ui.menu.Button("Nueva visita...", null, commandNuevaVisita);

	var commandModificarVisita = new qx.ui.command.Command("Enter");
	commandModificarVisita.setEnabled(false);
	commandModificarVisita.addListener("execute", function(e){
		var win = new pediatra.comp.pacientes.windowVisita(rowPaciente, rowDataVisita.id_visita);
		win.addListener("aceptado", function(e){
			var data = e.getData();
			
			functionActualizarVisitas(data);
		})
		application.getRoot().add(win);
		win.center();
		win.open();
	}, this);
		
	var btnModificarVisita = new qx.ui.menu.Button("Modificar visita...", null, commandModificarVisita);
	
	menuVisita.add(btnNuevaVisita);
	menuVisita.add(btnModificarVisita);
	menuVisita.memorizar();
	
	
	
	//Tabla
	var tableModelVisita = new qx.ui.table.model.Simple();
	tableModelVisita.setColumns(["#", "Motivo", "Antecedentes", "Fecha", "Per.enc.", "Talla", "Peso", "Presión"], ["index", "motivo", "antecedentes", "fecha", "per_enc", "talla", "peso", "presion"]);

	var custom = {tableColumnModel : function(obj) {
		return new qx.ui.table.columnmodel.Resize(obj);
	}};
	
	var tblVisita = new componente.comp.ui.ramon.table.Table(tableModelVisita, custom);
	tblVisita.setShowCellFocusIndicator(false);
	tblVisita.toggleColumnVisibilityButtonVisible();
	tblVisita.toggleStatusBarVisible();
	tblVisita.setContextMenu(menuVisita);
	tblVisita.addListener("cellDbltap", function(e){
		commandModificarVisita.execute();
	})
	
	var tableColumnModel = tblVisita.getTableColumnModel();
	var resizeBehavior = tableColumnModel.getBehavior();
	//resizeBehavior.set(0, {width:"80%", minWidth: 100});
	//resizeBehavior.set(1, {width:"20%", minWidth: 100});
	
	var selectionModel = tblVisita.getSelectionModel();
	selectionModel.setSelectionMode(qx.ui.table.selection.Model.SINGLE_SELECTION);
	selectionModel.addListener("changeSelection", function(){
		var selectionEmpty = selectionModel.isSelectionEmpty();
		commandModificarVisita.setEnabled(! selectionEmpty);
		menuVisita.memorizar([commandModificarVisita]);
		
		if (! selectionEmpty) {
			rowDataVisita = tableModelVisita.getRowDataAsMap(tblVisita.getFocusedRow());
			var p = {};
			p.id_visita = rowDataVisita.id_visita;
			
			var rpc = new componente.comp.io.ramon.rpc.Rpc("services/", "comp.Pacientes");
			rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id) {
				txtMotivo.setValue(resultado.visita.motivo);
				txtAntecedentes.setValue(resultado.visita.antecedentes);
				txtTexto.setValue(resultado.visita.texto);
				
				tableModelParamed.setDataAsMapArray(resultado.paramed, true);
			}, this), "leer_visita", p);
		}
	});

	composite.add(tblVisita, {left: 0, top: 0, right: 0, bottom: "50.5%"});
	
	
	
	
	
	
	
	
	
	var txtMotivo = new qx.ui.form.TextArea("");
	txtMotivo.setReadOnly(true);
	txtMotivo.setDecorator("main");
	txtMotivo.setBackgroundColor("#ffffc0");
	composite.add(txtMotivo, {left: 0, top: "50%", right: "50%", bottom: "33%"});
	
	var txtAntecedentes = new qx.ui.form.TextArea();
	txtAntecedentes.setReadOnly(true);
	txtAntecedentes.setDecorator("main");
	txtAntecedentes.setBackgroundColor("#ffffc0");
	composite.add(txtAntecedentes, {left: 0, top: "66%", right: "50%", bottom: "16%"});
	
	var txtTexto = new qx.ui.form.TextArea();
	txtTexto.setReadOnly(true);
	txtTexto.setDecorator("main");
	txtTexto.setBackgroundColor("#ffffc0");
	composite.add(txtTexto, {left: "50%", top: "50%", right: 0, bottom: "16%"});
	
	
	
	
	
	
	
	
	var tblParamed = new pediatra.comp.varios.tableParamed(true);
	var tableModelParamed = tblParamed.getTableModel();

	composite.add(tblParamed, {left: 0, top: "83%", right: 0, bottom: 0});
	

	stack.add(composite);
	this.add(stack, {left: 0, top: 32, right: 0, bottom: 5});
	
	
	tableModelParamed.setDataAsMapArray([{descrip: "Zitromax", tipo: "Medicamento", codigo: ""}, {descrip: "Torax frente de pie", tipo: "Radiografía", codigo: ""}], true)
	
	
	
	composite = new qx.ui.container.Composite(new qx.ui.layout.Canvas());
	stack.add(composite);
	
	composite = new qx.ui.container.Composite(new qx.ui.layout.Canvas());
	stack.add(composite);
	
	composite = new qx.ui.container.Composite(new qx.ui.layout.Canvas());
	stack.add(composite);
	
	composite = new qx.ui.container.Composite(new qx.ui.layout.Canvas());
	stack.add(composite);
	
	
	var toolbarMain = new qx.ui.toolbar.ToolBar();

	var rdg = new qx.ui.form.RadioGroup();
	rdg.addListener("changeSelection", function(e){
		stack.setSelection([stack.getChildren()[e.getData()[0].getModel()]]);
	})
	var button;
	
	button = new qx.ui.toolbar.RadioButton(" Visitas ");
	button.setModel(0);
	rdg.add(button);
	toolbarMain.add(button);
	
	button = new qx.ui.toolbar.RadioButton(" Vacunación ");
	button.setModel(1);
	rdg.add(button);
	toolbarMain.add(button);
	
	button = new qx.ui.toolbar.RadioButton(" Gráfico de evolución ");
	button.setModel(2);
	rdg.add(button);
	toolbarMain.add(button);
	
	button = new qx.ui.toolbar.RadioButton(" Documentos ");
	button.setModel(3);
	rdg.add(button);
	toolbarMain.add(button);
	
	button = new qx.ui.toolbar.RadioButton(" Datos fijos ");
	button.setModel(4);
	rdg.add(button);
	toolbarMain.add(button);
	
	this.add(toolbarMain, {left: 0, top: 0});


	functionActualizarVisitas();

		
	},
	members : 
	{

	}
});