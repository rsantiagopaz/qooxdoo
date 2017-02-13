/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * TODO: needs documentation
 */
qx.Class.define("efisica.curso.formCurso",
{
  extend : qx.ui.mobile.form.Form,

  construct : function()
  {
    this.base(arguments);
    
    var resetter = this.resetter = new qx.ui.mobile.form.Resetter();
    
	var txtDescrip = new qx.ui.mobile.form.TextField("");
	txtDescrip.setRequired(true);
	txtDescrip.addListener("blur", function(e){
		txtDescrip.setValue(txtDescrip.getValue().trim());
	})
	this.add(txtDescrip, "Descripción", null, "descrip");
	resetter.add(txtDescrip);
	
	var txtCantCuotas = new qx.ui.mobile.form.NumberField("0");
	txtCantCuotas.setRequired(true);
    txtCantCuotas.setMaximum(150);
    txtCantCuotas.setMinimum(0);
	txtCantCuotas.addListener("changeValue", function(e){
		txtTotal.setValue(txtMontoCuota.getValue() * txtCantCuotas.getValue());
	});
	this.add(txtCantCuotas, "Cant.cuotas", function(value, item){
		if (parseInt(value) <= 0) throw new qx.core.ValidationError("Validation Error", "Debe ingresar un valor");
	}, "cant_cuotas");
	resetter.add(txtCantCuotas);
	
	var txtMontoCuota = new qx.ui.mobile.form.NumberField("0");
	txtMontoCuota.setRequired(true);
    txtMontoCuota.setMaximum(100000);
    txtMontoCuota.setMinimum(0);
	txtMontoCuota.addListener("changeValue", function(e){
		txtTotal.setValue(txtMontoCuota.getValue() * txtCantCuotas.getValue());
	});
	this.add(txtMontoCuota, "Monto cuota", function(value, item){
		if (parseInt(value) <= 0) throw new qx.core.ValidationError("Validation Error", "Debe ingresar un valor");
	}, "monto_cuota");
	resetter.add(txtMontoCuota);
	
	var txtTotal = new qx.ui.mobile.form.NumberField("0");
	txtTotal.setReadOnly(true);
	this.add(txtTotal, "Total", null, "total");
	resetter.add(txtTotal);
	
	var btnGuardar = new qx.ui.mobile.form.Button("Guardar");
	btnGuardar.addListener("tap", function(e){
		if (this.validate()) {
			var model = controller.createModel();
			model = qx.util.Serializer.toNativeObject(model);
			
			var p = {};
			p.id_curso = this.id_curso;
			p.model = model;
			
			var rpc = new qx.io.remote.Rpc("services/", "comp.Curso");
			rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
				this.fireDataEvent("aceptado");
			}, this), "grabar_curso", p);
		}
	}, this);
	this.addButton(btnGuardar);
	
	var controller = this.controller = new qx.data.controller.Form(null, this);
    
  },

  
  members :
  {
	id_curso: null
  },
  
  events : 
  {
	"aceptado": "qx.event.type.Event"
  }
});
