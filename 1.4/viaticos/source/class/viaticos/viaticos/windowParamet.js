qx.Class.define("viaticos.viaticos.windowParamet",
{
	extend : controles.window.Window,
	construct : function (appMain)
	{
		this.base(arguments);

	this.set({
		caption: "Parámetros",
		width: 380,
		height: 300,
		showMinimize: false,
		showMaximize: false
	});
	this.setLayout(new qx.ui.layout.Canvas());
	
	this.addListenerOnce("appear", function(){
		//tbl.focus();
	});
	
	var numberformatMonto = new qx.util.format.NumberFormat("en").set({groupingUsed: false, maximumFractionDigits: 2, minimumFractionDigits: 2});
		
	var spinner;
	var modelForm = null;
	var form = new qx.ui.form.Form();
	
	spinner = new qx.ui.form.Spinner(0, 0, 10000);
	spinner.setNumberFormat(numberformatMonto);
	spinner.setWidth(90);
	form.add(spinner, "Viat.diario dp", null, "diario_dp");
	
	spinner = new qx.ui.form.Spinner(0, 0, 10000);
	spinner.setNumberFormat(numberformatMonto);
	spinner.setWidth(90);
	form.add(spinner, "Viat.diario fp", null, "diario_fp");
	
	spinner = new qx.ui.form.Spinner(0, 0, 10000);
	spinner.setNumberFormat(numberformatMonto);
	form.add(spinner, "Viat. 1/2 dp", null, "diario_12_dp");
	
	spinner = new qx.ui.form.Spinner(0, 0, 10000);
	spinner.setNumberFormat(numberformatMonto);
	form.add(spinner, "Viat. 1/2 fp", null, "diario_12_fp");
	
	spinner = new qx.ui.form.Spinner(0, 0, 10000);
	spinner.setNumberFormat(numberformatMonto);
	form.add(spinner, "Viat. 3/4 dp", null, "diario_34_dp");
	
	spinner = new qx.ui.form.Spinner(0, 0, 10000);
	spinner.setNumberFormat(numberformatMonto);
	form.add(spinner, "Viat. 3/4 fp", null, "diario_34_fp");
	
	spinner = new qx.ui.form.Spinner(0, 0, 10000);
	spinner.setNumberFormat(numberformatMonto);
	form.add(spinner, "Aloj.emp.dp", null, "alojam_emp_dp");
	
	spinner = new qx.ui.form.Spinner(0, 0, 10000);
	spinner.setNumberFormat(numberformatMonto);
	form.add(spinner, "Aloj.emp.fp", null, "alojam_emp_fp");

	spinner = new qx.ui.form.Spinner(0, 0, 10000);
	spinner.setNumberFormat(numberformatMonto);
	form.add(spinner, "Aloj.fun.dp", null, "alojam_func_dp");
	
	spinner = new qx.ui.form.Spinner(0, 0, 10000);
	spinner.setNumberFormat(numberformatMonto);
	form.add(spinner, "Aloj.fun.fp", null, "alojam_func_fp");
	
	spinner = new qx.ui.form.Spinner(0, 0, 200);
	spinner.setNumberFormat(numberformatMonto);
	form.add(spinner, "Porc.fun. %", null, "porc_func");
	
	spinner = new qx.ui.form.Spinner(0, 0, 200);
	spinner.setNumberFormat(numberformatMonto);
	form.add(spinner, "Porc.tope %", null, "porc_tope_cargo");
	

	var formView = new qx.ui.form.renderer.Double(form);
	this.add(formView, {left: 10, top: 10})

	var controllerForm = new qx.data.controller.Form(null, form);
	
	

	var commandEsc = new qx.ui.core.Command("Esc");
	this.registrarCommand(commandEsc);
	commandEsc.addListener("execute", function(e){
		btnCancelar.fireEvent("execute");
	});
	
	var btnAceptar = new qx.ui.form.Button("Aceptar");
	btnAceptar.addListener("execute", function(e){
		var model = qx.util.Serializer.toNativeObject(modelForm);
		var rpc = new qx.io.remote.Rpc("services/", "viaticos.Viaticos");
		try {
			var resultado = rpc.callSync("escribir_paramet", model);
		} catch (ex) {
			alert("Sync exception: " + ex);
		}

		btnCancelar.fireEvent("execute");
	}, this);

	var btnCancelar = new qx.ui.form.Button("Cancelar");
	btnCancelar.addListener("execute", function(e){
		this.destroy();
	}, this);
	
	this.add(btnAceptar, {left: 90, bottom: 5});
	this.add(btnCancelar, {left: 230, bottom: 5});
	

	var rpc = new qx.io.remote.Rpc("services/", "viaticos.Viaticos");
	try {
		var resultado = rpc.callSync("leer_paramet");
	} catch (ex) {
		alert("Sync exception: " + ex);
	}
	
	modelForm = qx.data.marshal.Json.createModel(resultado[0]);
	controllerForm.setModel(modelForm);
		
		
	},
	members : 
	{

	}
});