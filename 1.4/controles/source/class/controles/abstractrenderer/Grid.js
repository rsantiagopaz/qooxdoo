qx.Class.define("controles.abstractrenderer.Grid",
{
  extend : qx.ui.form.renderer.AbstractRenderer,


  construct : function(form, rowCount, columnCount, grupo)
  {
    var layout = new qx.ui.layout.Grid();
    layout.setSpacing(6);
    
    this.grupo = grupo;
    
    this.rowCount = rowCount;
    this.columnCount = columnCount;
    for (var i = 0; i < columnCount; i++) {
    	layout.setColumnWidth(i, 20);
    	layout.setColumnAlign(i, "left", "middle");
    }
	
    this._setLayout(layout);

    this.base(arguments, form);
  },


  members :
  {
  	tabIndex: 0,
  	grupo: null,
  	rowCount: null,
  	columnCount: null,
    _row : 0,
    _buttonRow : null,

    /**
     * Add a group of form items with the corresponding names. The names are
     * displayed as label.
     * The title is optional and is used as grouping for the given form
     * items.
     *
     * @param items {qx.ui.core.Widget[]} An array of form items to render.
     * @param names {String[]} An array of names for the form items.
     * @param title {String?} A title of the group you are adding.
     */
    addItems : function(items, names, title, options) {
      // add the header
	/*
      if (title != null) {
        this._add(
          this._createHeader(title), {row: this._row, column: 0, colSpan: 4}
        );
        this._row++;
      }
	*/

      // add the items
      for (var i = 0; i < items.length; i++) {
      	var widget;
		var layout = this.getLayout();
		if ((options[i] != null && this.grupo == null) || (options[i] != null && options[i].grupo == this.grupo)) {
			this.tabIndex = this.tabIndex + 1;
	        var label = this._createLabel(names[i], items[i]);
	        if (options[i].label == null) {
	        	layout.setColumnWidth(options[i].item.column - 1, null);
	        	widget = layout.getCellWidget(options[i].item.row, options[i].item.column - 1);
	        	if (widget != null) this._remove(widget);
	        	this._add(label, {row: options[i].item.row, column: options[i].item.column - 1});
	        } else {
	        	if (options[i].label.colSpan != null) {
	        		for (var x = options[i].label.column + 1; x < options[i].label.column + options[i].label.colSpan; x++) {
	        			widget = layout.getCellWidget(options[i].label.row, x);
	        			if (widget != null) this._remove(widget);
	        		}
	        	}
	        	if (options[i].label.rowSpan != null) {
	        		for (var x = options[i].label.row + 1; x < options[i].label.row + options[i].label.rowSpan; x++) {
	        			widget = layout.getCellWidget(x, options[i].label.column);
	        			if (widget != null) this._remove(widget);
	        		}
	        	}
	        	widget = layout.getCellWidget(options[i].label.row, options[i].label.column);
        		if (widget != null) this._remove(widget);
	        	this._add(label, options[i].label);
	        }
	        
	        var item = items[i];
	        label.setBuddy(item);
	
	        this._connectVisibility(item, label);
	        
	        if (options[i].enabled != null) item.setEnabled(options[i].enabled);
	        
        	if (options[i].item.colSpan != null) {
        		for (var x = options[i].item.column + 1; x < options[i].item.column + options[i].item.colSpan; x++) {
        			widget = layout.getCellWidget(options[i].item.row, x);
        			if (widget != null) this._remove(widget);
        		}
        	}
        	if (options[i].item.rowSpan != null) {
        		for (var x = options[i].item.row + 1; x < options[i].item.row + options[i].item.rowSpan; x++) {
        			widget = layout.getCellWidget(x, options[i].item.column);
        			if (widget != null) this._remove(widget);
        		}
        	}
        	widget = layout.getCellWidget(options[i].item.row, options[i].item.column);
    		if (widget != null) this._remove(widget);
    		if (options[i].tabIndex != null) this.tabIndex = options[i].tabIndex;
    		item.setTabIndex(this.tabIndex);
	        this._add(item, options[i].item);
	        
	        
			
	        // store the names for translation
	        if (qx.core.Environment.get("qx.dynlocale")) {
	          this._names.push({name: names[i], label: label, item: items[i]});
	        }
		}
      }

    },

    /**
     * Adds a button the form renderer. All buttons will be added in a
     * single row at the bottom of the form.
     *
     * @param button {qx.ui.form.Button} The button to add.
     */
    addButton : function(button) {
      if (this._buttonRow == null) {
        // create button row
        this._buttonRow = new qx.ui.container.Composite();
        this._buttonRow.setMarginTop(5);
        var hbox = new qx.ui.layout.HBox();
        hbox.setAlignX("right");
        hbox.setSpacing(5);
        this._buttonRow.setLayout(hbox);
        // add the button row
        this._add(this._buttonRow, {row: this._row, column: 0, colSpan: 4});
        // increase the row
        this._row++;
      }

      // add the button
      this._buttonRow.add(button);
    },


    /**
     * Returns the set layout for configuration.
     *
     * @return {qx.ui.layout.Grid} The grid layout of the widget.
     */
    getLayout : function() {
      return this._getLayout();
    },
    
    getChildren : function() {
      return this._getChildren();
    },


    /**
     * Creates a label for the given form item.
     *
     * @param name {String} The content of the label without the
     *   trailing * and :
     * @param item {qx.ui.core.Widget} The item, which has the required state.
     * @return {qx.ui.basic.Label} The label for the given item.
     */
    _createLabel : function(name, item) {
      var label = new qx.ui.basic.Label(this._createLabelText(name, item));
      label.setRich(true);
      return label;
    },


    /**
     * Creates a header label for the form groups.
     *
     * @param title {String} Creates a header label.
     * @return {qx.ui.basic.Label} The header for the form groups.
     */
    _createHeader : function(title) {
      var header = new qx.ui.basic.Label(title);
      header.setFont("bold");
      if (this._row != 0) {
        header.setMarginTop(10);
      }
      return header;
    }
  },


  /*
  *****************************************************************************
     DESTRUCTOR
  *****************************************************************************
  */
  destruct : function() {
    // first, remove all buttons from the botton row because they
    // should not be disposed
    if (this._buttonRow) {
      this._buttonRow.removeAll();
      this._disposeObjects("_buttonRow");
    }
  }
});
