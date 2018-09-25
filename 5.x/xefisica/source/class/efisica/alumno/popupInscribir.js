qx.Class.define("efisica.alumno.popupInscribir",
{
  extend : efisica.varios.popupModal,

  construct : function(id_alumno)
  {
    this.base(arguments, null, null);
    
    this.setTitle("Inscribir");
    
	var lst = this.__selectionList = new qx.ui.mobile.list.List({
		configureItem: function(item, data, row) {
			item.setTitle(data.descrip);
			//item.setSubtitle(data.fecha);
			item.setSelectable(true);
		}
	});
	
	
	lst.addListener("changeSelection", function(e) {
		var data = e.getData();
		var item = lst.getModel().getItem(data);
		
		var p = {};
		p.id_alumno = id_alumno;
		p.id_curso = item.id_curso;
		
		var rpc = new qx.io.remote.Rpc("services/", "comp.Curso");
		rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
			window.history.back();
			
			window.setTimeout(function(){
				this.fireDataEvent("aceptado", item.id_curso);
			}.bind(this), 1);			
		}, this), "inscribir", p);
	}, this);
	
	
	
	var p = {};
	p.id_alumno = id_alumno;
	
	var rpc = new qx.io.remote.Rpc("services/", "comp.Curso");
	rpc.callAsync(qx.lang.Function.bind(function(resultado, error, id){
		lst.setModel(new qx.data.Array(resultado));
	}, this), "leer_cursos_inscripcion", p);
	
	var containerScroll = new qx.ui.mobile.container.Scroll();
	containerScroll.add(lst);
	this.add(containerScroll);
      
  },

  members :
  {

  },
  
  events : 
  {
	"aceptado": "qx.event.type.Event"
  }
});