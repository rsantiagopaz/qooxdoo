qx.Class.define("controles.table.TableMonFilaColumna",
{
	extend : qx.ui.table.Table,
	construct : function (tableModel, custom)
	{
		this.base(arguments, tableModel, custom);

		
		var decoration = new qx.ui.decoration.Single(1, "solid", "border-focused");
		
		this.addListener("mousemove", function(e){
			var pageX = e.getDocumentLeft();
			var pageY = e.getDocumentTop();
			var sc = this.getTablePaneScrollerAtPageX(pageX);
			this.fila = sc._getRowForPagePos(pageX, pageY);
			this.columna = sc._getColumnForPageX(pageX);
		}, this);

		this._listenerChangeTableModel = this.addListener("changeTableModel", function(e){
			tableModel = e.getData();
		});
		
		this._listenerFocus = this.addListener("focus", function(e){
			this.setDecorator(decoration);
			if (this.modo=="especial") {
				if (tableModel.getRowCount() > 0) {
					var focusedRow = this.getFocusedRow();
					this.getSelectionModel().setSelectionInterval(focusedRow, focusedRow);
				}
			}
			if (this._contextMenu) this._contextMenu.restablecer();
		});
		
		this._listenerBlur = this.addListener("blur", function(e){
			this.resetDecorator();
			if (this.modo=="especial") this.resetSelection();
			if (this._contextMenu) this._contextMenu.desactivar();
		});

		this._listenerChangeContextMenu = this.addListener("changeContextMenu", function(e){
			this._contextMenu = e.getData();
		});

	},
	members : 
	{
		edicion: "edicion_horizontal",
		modo: "especial",
		
		_listenerChangeTableModel: null,
		_listenerFocus: null,
		_listenerBlur: null,
		_listenerChangeContextMenu: null,
		_contextMenu: null,
		columna: null,
		fila: null,
		
		buscar: function(key, data, seleccionar, columna)
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
					if (seleccionar) this.setFocusedCell(columna, x, true);
					return rowData;
				} else rowData = null;
			}
			return rowData;
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
		this.removeListenerById(this._listenerChangeTableModel);
		this.removeListenerById(this._listenerFocus);
		this.removeListenerById(this._listenerBlur);
		this.removeListenerById(this._listenerChangeContextMenu);
	}
});