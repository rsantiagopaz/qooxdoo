/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * TODO: needs documentation
 */
qx.Class.define("efisica.alumno.formAlumno",
{
  extend : qx.ui.mobile.form.Form,

  construct : function()
  {
    this.base(arguments);
    
	var txtApellido = new qx.ui.mobile.form.TextField("");
	txtApellido.setRequired(true);
	//this.add(txtApellido, "Apellido", function(value, item){
	//	var valid = value.trim() != "";
	//	if(! valid) item.setInvalidMessage("Este campo es obligatorio");
	//return valid;
	//}, "apellido");
	this.add(txtApellido, "Apellido", null, "apellido");
	
	var txtNombre = new qx.ui.mobile.form.TextField("");
	txtNombre.setRequired(true);
	//this.add(txtNombre, "Nombre", function(value, item){
	//	var valid = value.trim() != "";
	//	if(! valid) item.setInvalidMessage("Este campo es obligatorio");
	//	return valid;
	//}, "nombre");
	this.add(txtNombre, "Nombre", null, "nombre");
	
	var slbTipodoc = new qx.ui.mobile.form.SelectBox();
	slbTipodoc.setDialogTitle("Tipo doc.");
	slbTipodoc.setModel(new qx.data.Array(["dni", "cuit", "cédula", "lib.cív."]));
	slbTipodoc.setSelection(0);
	this.add(slbTipodoc, "Tipo doc.", null, "tipo_doc");
	
	var txtNrodoc = new qx.ui.mobile.form.NumberField();
	txtNrodoc.setMinimum(0);
	this.add(txtNrodoc, "Nro.doc.", null, "nro_doc");
	
	var txtDomicilio = new qx.ui.mobile.form.TextField("");
	this.add(txtDomicilio, "Domicilio", null, "domicilio");
	
	var txtTelefono = new qx.ui.mobile.form.TextArea("");
	this.add(txtTelefono, "Cel./Tel./E-mail", null, "telefono");
	
	var txtF_nacimiento = new qx.ui.mobile.form.TextField("");
	this.add(txtF_nacimiento, "Fecha nac.", null, "f_nacimiento");
	
	var btnGuardar = new qx.ui.mobile.form.Button("Guardar");
	btnGuardar.addListener("tap", function(e){
		txtApellido.setValue(txtApellido.getValue().trim());
		txtNombre.setValue(txtNombre.getValue().trim());
		
		if (this.validate()) {
			var model = controller.createModel();
			model = qx.util.Serializer.toNativeObject(model);
			
			if (model.nro_doc == "" || model.nro_doc == "0") model.nro_doc = null;
			if (model.f_nacimiento == "") model.f_nacimiento = null;
			
			var p = {};
			p.id_alumno = this.id_alumno;
			p.model = model;
			
			var rpc = new qx.io.remote.Rpc("services/", "comp.Alumno");
			rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
				this.fireDataEvent("aceptado");
			}, this), "grabar_alumno", p);
		}
	}, this);
	this.addButton(btnGuardar);
	
	var controller = this.controller = new qx.data.controller.Form(null, this);
    
  },

  
  members :
  {
	id_alumno: null
  },
  
  events : 
  {
	"aceptado": "qx.event.type.Event"
  }
});
