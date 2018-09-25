/* ************************************************************************

   Copyright:

   License:

   Authors:

************************************************************************ */

/**
 * TODO: needs documentation
 */
qx.Class.define("efisica.page.Overview",
{
  extend : qx.ui.mobile.page.NavigationPage,

  construct : function()
  {
    this.base(arguments);
    this.setTitle("Principal");
  },


  members :
  {
    // overridden
    _initialize : function()
    {
      this.base(arguments);
		
	var application = qx.core.Init.getApplication();
		
		var dataAlumnos;
		
		dataAlumnos = [
			{title: "Administrar", path: "alumnos"}
		];
		
		var listAlumnos = new qx.ui.mobile.list.List({
			configureItem: function(item, data, row) {
				item.setTitle(data.title);
				//item.setSubtitle(data.subtitle);
				item.setSelectable(true);
			}
		});
		
	
		listAlumnos.addListener("changeSelection", function(e) {
        	var path = dataAlumnos[e.getData()].path;
        	application.getRouting().executeGet("" + path);
		});
		
		listAlumnos.setModel(new qx.data.Array(dataAlumnos));
		
		var collapsibleAlumnos = new qx.ui.mobile.container.Collapsible("Alumnos");
		collapsibleAlumnos.add(listAlumnos);
		this.getContent().add(collapsibleAlumnos);
		
		var dataCursos = [
			{title: "Administrar", path: "cursos"}
		];
		
		var listCursos = new qx.ui.mobile.list.List({
			configureItem: function(item, data, row) {
				item.setTitle(data.title);
				//item.setSubtitle(data.subtitle);
				item.setSelectable(true);
			}
		});
		
		listCursos.setModel(new qx.data.Array(dataCursos));
		
		listCursos.addListener("changeSelection", function(e) {
        	var path = this.getModel().getItem(e.getData()).path;
        	application.getRouting().executeGet("" + path);
		});
		
		var collapsibleCursos = new qx.ui.mobile.container.Collapsible("Cursos");
		collapsibleCursos.add(listCursos);
		this.getContent().add(collapsibleCursos);

	
    }
  }
});
