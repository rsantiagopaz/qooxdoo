qx.Class.define("componente.comp.ui.ramon.table.Table",
{
	extend : qx.ui.table.Table,
	construct : function (tableModel, custom)
	{
		this.base(arguments, tableModel, custom);
		
		this.toggleContextMenuFromDataCellsOnly();
		
		this._listeners = [];
		
		var listenerId;

		
		this._decorator_default = this._decorator_blur = this.getDecorator();
		
		this._decorator_focused = new qx.ui.decoration.Decorator();
		this._decorator_focused.setWidth(1, 1, 1, 1);
		this._decorator_focused.setStyle("solid", "solid", "solid", "solid");
		this._decorator_focused.setColor("border-focused", "border-focused", "border-focused", "border-focused");
		
		this._decorator_invalid = new qx.ui.decoration.Decorator();
		this._decorator_invalid.setWidth(1, 1, 1, 1);
		this._decorator_invalid.setStyle("solid", "solid", "solid", "solid");
		this._decorator_invalid.setColor("border-focused-invalid", "border-focused-invalid", "border-focused-invalid", "border-focused-invalid");
		
		this._decorator_invalid_focused = new qx.ui.decoration.Decorator();
		this._decorator_invalid_focused.setWidth(2, 2, 2, 2);
		this._decorator_invalid_focused.setStyle("solid", "solid", "solid", "solid");
		this._decorator_invalid_focused.setColor("border-focused-invalid", "border-focused-invalid", "border-focused-invalid", "border-focused-invalid");
		
		this._decorator_focus = this._decorator_focused;
		
		listenerId = this.addListener("changeTableModel", function(e){
			tableModel = e.getData();
		});
		this.registrarListener(this, listenerId);
		
		listenerId = this.addListener("focus", function(e){
			this.setDecorator(this._decorator_focus);
			if (this.modo=="especial") {
				if (tableModel.getRowCount() > 0) {
					var focusedRow = this.getFocusedRow();
					if (focusedRow != null) this.getSelectionModel().setSelectionInterval(focusedRow, focusedRow);
				}
			}
			if (this._contextMenu) this._contextMenu.restablecer();
		}, this);
		this.registrarListener(this, listenerId);
		
		listenerId = this.addListener("blur", function(e){
			if (this._decorator_blur == null) this.resetDecorator(); else this.setDecorator(this._decorator_blur);
			if (this.modo=="especial") this.resetSelection();
			if (this._contextMenu) this._contextMenu.desactivar();
		}, this);
		this.registrarListener(this, listenerId);

		listenerId = this.addListener("changeContextMenu", function(e){
			this._contextMenu = e.getData();
		}, this);
		this.registrarListener(this, listenerId);

	},
	properties :
	{
		valid: {init: true, check: "Boolean", apply: "_applyValid"}
	},
	members :
	{
		edicion: "edicion_horizontal",
		modo: "especial",
		
		_decorator_focus: null,
		_decorator_blur: null,
		_decorator_default: null,
		_decorator_focused: null,
		_decorator_invalid: null,
		_decorator_invalid_focused: null,
		
		
		_listeners: [],
		_contextMenu: null,
		
		_applyValid : function(value, old, name) {
			if (value) {
				this._decorator_focus = this._decorator_focused;
				this._decorator_blur = this._decorator_default;
			} else {
				this._decorator_focus = this._decorator_invalid_focused;
				this._decorator_blur = this._decorator_invalid;
			}
			
			if (this._decorator_blur == null) this.resetDecorator(); else this.setDecorator(this._decorator_blur);
		},
		
		registrarListener : function (objeto, listenerId)
		{
			this._listeners.push({objeto: objeto, listenerId: listenerId});
		},
		
		buscar: function(key, data, seleccionar, columna, resultado)
		{
			var rowData = null;
			var tableModel = this.getTableModel();
			var rowCount = tableModel.getRowCount();
			if (seleccionar==null) seleccionar=true;
			if (seleccionar) this.setFocusedCell();
			if (columna==null) columna=0;
			for (var x=0; x < rowCount; x++) {
				rowData = tableModel.getRowData(x);
				if (rowData[key] == data) {
					if (resultado != null) {
						resultado.indice = x;
						resultado.row = rowData;
					}
					
					if (seleccionar) this.setFocusedCell(columna, x, true);
					break;
				} else {
					rowData = null;
				}
			}
			return rowData;
		},
		
    _updateStatusBar : function()
    {
      if (this.getStatusBarVisible())
      {
        var text = this.getAdditionalStatusBarText();

        if (text) {
          this.getChildControl("statusbar").setValue(text);
        }
      }
    },
		
    _onKeyPress : function(evt)
    {
      if (!this.getEnabled()) {
        return;
      }

      // No editing mode
      var oldFocusedRow = this.getFocusedRow();
      var consumed = true;

      // Handle keys that are independent from the modifiers
      var identifier = evt.getKeyIdentifier();

      if (this.isEditing())
      {
        // Editing mode
        if (evt.getModifiers() == 0)
        {
          switch(identifier)
          {
			case "Enter":
			  this.stopEditing();
			  if (this.edicion=="edicion_vertical") {
			      var oldFocusedRow = this.getFocusedRow();
			      this.moveFocusedCell(0, 1);
			
			      if (this.getFocusedRow() != oldFocusedRow) {
			        consumed = this.startEditing();
			      }
			  } else if (this.edicion=="edicion_horizontal") {
			      var oldFocusedCol = this.getFocusedColumn();
			      this.moveFocusedCell(1, 0);
			
			      if (this.getFocusedColumn() != oldFocusedCol) {
			        consumed = this.startEditing();
			      }
			  } else if (this.edicion=="desplazamiento_vertical") {
			      var oldFocusedRow = this.getFocusedRow();
			      this.moveFocusedCell(0, 1);
			      
			  } else if (this.edicion=="desplazamiento_horizontal") {
			      var oldFocusedCol = this.getFocusedColumn();
			      this.moveFocusedCell(1, 0);

			  } else {
			  	
			  }
			
			  break;

            case "Escape":
              this.cancelEditing();
              this.focus();
              break;

            default:
              consumed = false;
              break;
          }
        }

      }
      else
      {
        // No editing mode
        if (evt.isCtrlPressed())
        {
          // Handle keys that depend on modifiers
          consumed = true;

          switch(identifier)
          {
            case "A": // Ctrl + A
              var rowCount = this.getTableModel().getRowCount();

              if (rowCount > 0) {
                this.getSelectionModel().setSelectionInterval(0, rowCount - 1);
              }

              break;

            default:
              consumed = false;
              break;
          }
        }
        else
        {
          // Handle keys that are independent from the modifiers
          switch(identifier)
          {
            case "Space":
              this.getSelectionManager().handleSelectKeyDown(this.getFocusedRow(), evt);
              break;

            case "F2":
            case "Enter":
              this.startEditing();
              consumed = true;
              break;

            case "Home":
              this.setFocusedCell(this.getFocusedColumn(), 0, true);
              break;

            case "End":
              var rowCount = this.getTableModel().getRowCount();
              this.setFocusedCell(this.getFocusedColumn(), rowCount - 1, true);
              break;

            case "Left":
              this.moveFocusedCell(-1, 0);
              break;

            case "Right":
              this.moveFocusedCell(1, 0);
              break;

            case "Up":
              this.moveFocusedCell(0, -1);
              break;

            case "Down":
              this.moveFocusedCell(0, 1);
              break;

            case "PageUp":
            case "PageDown":
              var scroller = this.getPaneScroller(0);
              var pane = scroller.getTablePane();
              var rowHeight = this.getRowHeight();
              var direction = (identifier == "PageUp") ? -1 : 1;
              rowCount = pane.getVisibleRowCount() - 1;
              scroller.setScrollY(scroller.getScrollY() + direction * rowCount * rowHeight);
              this.moveFocusedCell(0, direction * rowCount);
              break;

            default:
              consumed = false;
          }
        }
      }

      if (oldFocusedRow != this.getFocusedRow() &&
          this.getRowFocusChangeModifiesSelection())
      {
        // The focus moved -> Let the selection manager handle this event
        this.getSelectionManager().handleMoveKeyDown(this.getFocusedRow(), evt);
      }

      if (consumed)
      {
        evt.preventDefault();
        evt.stopPropagation();
      }
    }
	},
	destruct : function ()
	{
		for (var i in this._listeners) this._listeners[i].objeto.removeListenerById(this._listeners[i].listenerId);
	}
});