<?php

require("Base.php");

class class_Productos extends class_Base
{
  function __construct() {
    parent::__construct();
  }
  
  public function method_arreglar_archivos($params, $error) {
  	$p = $params[0];
  	
  	foreach ($p->nodos as $item) {
		$sql = "SELECT";
		$sql.=" producto.descrip, fabrica.descrip AS fabrica, producto_item.id_producto_item, color.descrip AS color";
		$sql.=" FROM (producto INNER JOIN fabrica USING(id_fabrica)) INNER JOIN (producto_item INNER JOIN color USING(id_color)) USING(id_producto)";
		$sql.=" WHERE id_arbol=" . $item->id_arbol;
		$rs = mysql_query($sql);
		while ($reg = mysql_fetch_object($rs)) {
			$sql = "UPDATE producto_item SET busqueda = '" . $item->clasificacion . "|1|" . $reg->descrip . "|2|" . $reg->fabrica . "|3|" . $reg->color . "' WHERE id_producto_item='" . $reg->id_producto_item . "'";
			mysql_query($sql);
		}
	}


  }
  
  public function method_mover_nodo($params, $error) {
  	$p = $params[0];
  	
  	mysql_query("START TRANSACTION");
  	
	$sql = "UPDATE arbol SET cant_hijos = cant_hijos - 1 WHERE id_arbol='" . $p->id_padre_original . "'";
	mysql_query($sql);
	$sql = "UPDATE arbol SET cant_hijos = cant_hijos + 1 WHERE id_arbol='" . $p->id_padre . "'";
	mysql_query($sql);
	$sql = "UPDATE arbol SET id_padre = '" . $p->id_padre . "' WHERE id_arbol='" . $p->id_arbol . "'";
	mysql_query($sql);
	foreach ($p->clasificacion as $item) {
		$sql = "UPDATE (producto INNER JOIN producto_item USING (id_producto)) SET busqueda = CONCAT('" . $item->clasificacion . "', SUBSTR(busqueda, INSTR(busqueda, '|1|'))) WHERE id_arbol='" . $item->id_arbol . "'";
		mysql_query($sql);
	}
	mysql_query("COMMIT");

  }
  
  public function method_mover_productos($params, $error) {
  	$p = $params[0];
  	
  	mysql_query("START TRANSACTION");
  	
	$sql = "UPDATE arbol SET cant_productos = cant_productos - 1 WHERE id_arbol='" . $p->id_arbol_actual . "'";
	mysql_query($sql);
	$sql = "UPDATE arbol SET cant_productos = cant_productos + 1 WHERE id_arbol='" . $p->id_arbol . "'";
	mysql_query($sql);
	$sql = "UPDATE producto SET id_arbol='" . $p->id_arbol . "' WHERE id_producto='" . $p->id_producto . "'";
	mysql_query($sql);
	$sql = "UPDATE producto_item SET busqueda = CONCAT('" . $p->clasificacion . "', SUBSTR(busqueda, INSTR(busqueda, '|1|'))) WHERE id_producto='" . $p->id_producto . "'";
	mysql_query($sql);
	
	mysql_query("COMMIT");

  }

  public function method_grabar_precios($params, $error) {
  	$p = $params[0];
        
    mysql_query("START TRANSACTION");
    
	$sql = "UPDATE producto SET serializer='" . $p->serializer . "' WHERE id_producto='" . $p->id_producto . "'";
	mysql_query($sql);
    
    foreach ($p->producto_item as $item) {
            $set = $this->prepararCampos($item, "producto_item");
            $sql = "UPDATE producto_item SET " . $set . " WHERE id_producto_item='" . $item->id_producto_item . "'";
            mysql_query($sql);
            if (mysql_errno()) break;
    }
    
    mysql_query("COMMIT");
  }


  public function method_leer_precios($params, $error) {
  	$p = $params[0];
  	
	$resultado = "";
	$resultado->producto_item = array();
	
	$sql = "SELECT";
	$sql.=" producto.*, fabrica.descrip AS fabrica";
	$sql.=" FROM producto INNER JOIN fabrica USING (id_fabrica)";
	$sql.=" WHERE id_producto=" . $p->id_producto;
	$rs = mysql_query($sql);
	$producto = mysql_fetch_object($rs);
	$producto->desc_fabrica = (float) $producto->desc_fabrica;
	$producto->iva = (float) $producto->iva;
	$producto->fabrica = $producto->fabrica . " (" . $producto->desc_fabrica . "% descuento)";

	$resultado->producto = $producto;
	

	$sql = "SELECT";
	$sql.=" producto_item.*, unidad.descrip AS unidad, color.descrip AS color";
	$sql.=" FROM (producto_item INNER JOIN unidad USING (id_unidad)) INNER JOIN color USING (id_color)";
	$sql.=" WHERE id_producto=" . $p->id_producto;
	$sql.=" ORDER BY color, unidad, producto_item.capacidad";
	$rs = mysql_query($sql);
	while ($reg = mysql_fetch_object($rs)) {
		$reg->capacidad = (float) $reg->capacidad;
		$reg->duracion = (float) $reg->duracion;
		$reg->precio_lista = (float) $reg->precio_lista;
		$reg->remarc_final = (float) $reg->remarc_final;
		$reg->remarc_mayorista = (float) $reg->remarc_mayorista;
		$reg->desc_final = (float) $reg->desc_final;
		$reg->desc_mayorista = (float) $reg->desc_mayorista;
		$reg->bonif_final = (float) $reg->bonif_final;
		$reg->bonif_mayorista = (float) $reg->bonif_mayorista;
		$reg->comision_vendedor = (float) $reg->comision_vendedor;
		
		$resultado->producto_item[] = $reg;
		

/*
		$plmasiva = $reg->precio_lista + ($reg->precio_lista * $model->iva / 100);
		$pcf = $plmasiva + ($plmasiva * $reg->remarc_final / 100);
		$pcfcd = $pcf - ($pcf * $reg->desc_final /100);
		$pmay = $plmasiva + ($plmasiva * $reg->remarc_mayorista / 100);
		$pmaycd = $pmay - ($pmay * (float)$reg->desc_mayorista /100);
		$costo = $plmasiva - ($plmasiva * (float)$model->desc_fabrica / 100);
		
		$calculo = "";
		$calculo->item = $contador;
		$calculo->plmasiva = (float) number_format($plmasiva, 2);
		$calculo->costo = (float) number_format($costo, 2);
		$calculo->pcf = (float) number_format($pcf, 2);
		$calculo->pcfcd = (float) number_format($pcfcd, 2);
		$calculo->utilcf = (float) number_format($pcfcd - $costo, 2);
		$calculo->pmay = (float) number_format($pmay, 2);
		$calculo->pmaycd = (float) number_format($pmaycd, 2);
		$calculo->utilmay = (float) number_format($pmaycd - $costo, 2);
		$calculo->comision = (float) number_format($pcfcd * $reg->comision_vendedor /100, 2);
		
		$resultado->calculo[] = $calculo;
*/
	}
	
	return $resultado;
  }


  public function method_agregar_nodo($params, $error) {
  	$p = $params[0];
  	
	$sql="UPDATE arbol SET cant_hijos = cant_hijos + 1 WHERE id_arbol='" . $p->id_padre . "'";
	mysql_query($sql);
	$sql = "INSERT INTO arbol SET id_padre='" . $p->id_padre . "', descrip='Nuevo nodo', cant_hijos=0, cant_productos=0";
	mysql_query($sql);
	return mysql_insert_id();
  }


  public function method_leer_producto($params, $error) {
  	$p = $params[0];
  	
	$todos = is_null($p->id_producto);
	
	if ($todos) {
		$resultado = array();
		
		if (is_null($p->cod_interno)) {
			$WHERE = " WHERE TRUE";
	    	$buscar = explode(" ", $p->descrip);
	    	foreach ($buscar as $palabra) {
    			$WHERE .= " AND producto_item.busqueda LIKE '%" . $palabra . "%' ";
	    	}			
		} else {
			$WHERE = " WHERE producto_item.cod_interno LIKE '%" . $p->cod_interno . "%' ";
		}
		

		
		$sql = "SELECT";
		$sql.=" producto.descrip";
		$sql.=", producto.iva";
		$sql.=", producto.desc_fabrica";
		$sql.=", fabrica.descrip AS fabrica";
		$sql.=", moneda.simbolo AS moneda";
		$sql.=", producto_item.*";
		$sql.=", unidad.descrip AS unidad";
		$sql.=", color.descrip AS color";
		$sql.=" FROM ((producto INNER JOIN moneda USING(id_moneda)) INNER JOIN fabrica USING(id_fabrica))";
		$sql.=" INNER JOIN ((producto_item INNER JOIN color USING(id_color)) INNER JOIN unidad USING(id_unidad)) USING(id_producto)";
		if (is_null($p->id_arbol)) {
			//$sql.=" WHERE producto_item.busqueda LIKE '%" . $p->descrip . "%'";
			$sql.= $WHERE;
		} else {
			$sql.=" WHERE id_arbol=" . $p->id_arbol . " AND producto.descrip LIKE '%" . $p->descrip . "%'";
		}
		//$sql.=" ORDER BY producto_item.capacidad";
		$rs = mysql_query($sql);
		while ($reg = mysql_fetch_object($rs)) {
			$reg->capacidad = (float) $reg->capacidad;
			
			$plmasiva = $reg->precio_lista + ($reg->precio_lista * $reg->iva / 100);
			$costo = $plmasiva - ($plmasiva * (float)$reg->desc_fabrica / 100);
			
//			$pcf = $plmasiva - ($plmasiva * $reg->desc_final / 100);
//			$pcf = $pcf - ($pcf * $reg->bonif_final / 100);
//			$pcfcd = $pcf - ($pcf * $reg->desc_final /100);
//			$pcfcd = $costo + ($costo * $reg->remarc_final /100);
//			$pcfcd = $pcf + ($plmasiva * $reg->remarc_final /100);
			$pcf = $plmasiva + ($plmasiva * $reg->remarc_final / 100);
			$pcf = $pcf - ($pcf * $reg->desc_final / 100);
			$pcfcd = $pcf - ($pcf * $reg->bonif_final / 100);
			
//			$pmay = $plmasiva - ($plmasiva * $reg->desc_mayorista / 100);
//			$pmay = $pmay - ($pmay * $reg->bonif_mayorista / 100);
//			$pmaycd = $pmay - ($pmay * (float)$reg->desc_mayorista /100);
//			$pmaycd = $costo + ($costo * $reg->remarc_mayorista /100);
//			$pmaycd = $pmay + ($plmasiva * $reg->remarc_mayorista /100);
			$pmay = $plmasiva + ($plmasiva * $reg->remarc_mayorista / 100);
			$pmay = $pmay - ($pmay * $reg->desc_mayorista / 100);
			$pmaycd = $pmay - ($pmay * $reg->bonif_mayorista / 100);
			
			/*
			$reg->plmasiva = number_format($plmasiva, 2);
			$reg->costo = number_format($costo, 2);
			$reg->pcf = number_format($pcf, 2);
			$reg->pcfcd = number_format($pcfcd, 2);
			$reg->utilcf = number_format($pcfcd - $costo, 2);
			$reg->pmay = number_format($pmay, 2);
			$reg->pmaycd = number_format($pmaycd, 2);
			$reg->utilmay = number_format($pmaycd - $costo, 2);
			$reg->comision = number_format($pcfcd * $reg->comision_vendedor /100, 2);
			*/
			
			$reg->plmasiva = $plmasiva;
			$reg->costo = $costo;
			$reg->pcf = $pcf;
			$reg->pcfcd = $pcfcd;
			$reg->utilcf = $pcfcd - $costo;
			$reg->pmay = $pmay;
			$reg->pmaycd = $pmaycd;
			$reg->utilmay = $pmaycd - $costo;
			$reg->comision = $pcfcd * $reg->comision_vendedor /100;
			
			$resultado[] = $reg;
		}
	} else {
		$resultado = "";
		$resultado->items = array();
		
		$sql = "SELECT";
		$sql.=" *";
		$sql.=" FROM producto";
		$sql.=" WHERE id_producto=" . $p->id_producto;
		$rs = mysql_query($sql);
		$reg = mysql_fetch_object($rs);
		$reg->desc_fabrica = (float) $reg->desc_fabrica;
		$reg->iva = (float) $reg->iva;
	
		$resultado->model = $reg;
		
	
		$sql = "SELECT";
		$sql.=" *";
		$sql.=" FROM producto_item";
		$sql.=" WHERE id_producto=" . $p->id_producto;
		$rs = mysql_query($sql);
		while ($reg = mysql_fetch_object($rs)) {
			$reg->capacidad = (float) $reg->capacidad;
			$reg->duracion = (float) $reg->duracion;
			$reg->precio_lista = (float) $reg->precio_lista;
			$reg->remarc_final = (float) $reg->remarc_final;
			$reg->remarc_mayorista = (float) $reg->remarc_mayorista;
			$reg->desc_final = (float) $reg->desc_final;
			$reg->desc_mayorista = (float) $reg->desc_mayorista;
			$reg->comision_vendedor = (float) $reg->comision_vendedor;
			
			$resultado->items[] = $reg;
		}
	}
	
	return $resultado;
  }
  
  public function method_alta_modifica_producto($params, $error) {
  	$p = $params[0];
  	
	$model = $p->model;
	$items = $p->items;
	
	$set = $this->prepararCampos($model);
	
	mysql_query("START TRANSACTION");
	
	if ($model->id_producto == "0") {
		$sql="UPDATE arbol SET cant_productos = cant_productos + 1 WHERE id_arbol='" . $model->id_arbol . "'";
		mysql_query($sql);
		$sql = "INSERT producto SET " . $set . ", serializer='{\"agrupar\":false,\"colores\":{}}'";
		mysql_query($sql);
		$id_producto = mysql_insert_id();
	} else {
		$id_producto = $model->id_producto;
		$sql = "UPDATE producto SET " . $set . " WHERE id_producto='" . $model->id_producto . "'";
		mysql_query($sql);
	}
	
	foreach ($items->altas as $item) {
		$item->id_producto = $id_producto;
		$set = $this->prepararCampos($item);
		$sql = "INSERT producto_item SET " . $set;
		mysql_query($sql);
		if (mysql_errno()) break;
	}
	if (! mysql_errno()) {
		foreach ($items->modificados as $item) {
			$set = $this->prepararCampos($item);
			$sql = "UPDATE producto_item SET " . $set . " WHERE id_producto_item='" . $item->id_producto_item . "'";
			mysql_query($sql);
			if (mysql_errno()) break;
		}
		if (! mysql_errno()) {
			foreach ($items->eliminados as $item) {
				$sql="DELETE FROM producto_item WHERE id_producto_item='" . $item . "'";
				mysql_query($sql);
				if (mysql_errno()) break;
			}
		}
	}
	if (mysql_errno()) {
		mysql_query("ROLLBACK");
		return mysql_error();
	} else {
		mysql_query("COMMIT");
		return $id_producto;
	}
  }

  public function method_eliminar_nodo($params, $error) {
  	$p = $params[0];
  	
	$sql="UPDATE arbol SET cant_hijos = cant_hijos - 1 WHERE id_arbol='" . $p->id_padre . "'";
	mysql_query($sql);
	$sql="DELETE FROM arbol WHERE id_arbol=" . $p->id_arbol;
	mysql_query($sql);
  }

  public function method_modificar_nodo($params, $error) {
  	$p = $params[0];
  	
	$sql="UPDATE arbol SET descrip='" . $p->descrip . "' WHERE id_arbol='" . $p->id_arbol . "'";
	mysql_query($sql);
  }

  public function method_eliminar_producto($params, $error) {
  	$p = $params[0];
	
	$sql="UPDATE arbol SET cant_productos = cant_productos - 1 WHERE id_arbol='" . $p->id_arbol . "'";
	mysql_query($sql);
	$sql="DELETE FROM producto WHERE id_producto='" . $p->id_producto . "'";
	mysql_query($sql);
	$sql="DELETE FROM producto_item WHERE id_producto='" . $p->id_producto . "'";
	mysql_query($sql);
  }

  public function method_buscar_productos($params, $error) {
  	$p = $params[0];
	$resultado = array();
	
	$sql="SELECT producto_item.id_producto_item, fabrica.descrip AS fabrica, producto.descrip AS producto, producto.iva, producto_item.capacidad, producto_item.precio_lista, producto_item.stock, color.descrip AS color, unidad.descrip AS unidad, producto_item.id_unidad";
	$sql.=" FROM ((((producto INNER JOIN fabrica USING(id_fabrica)) INNER JOIN producto_item USING (id_producto)) INNER JOIN color USING (id_color)) INNER JOIN unidad USING (id_unidad))";
	
	if (is_null($p->cod_interno)) {
		$WHERE = " WHERE TRUE";
    	$buscar = explode(" ", $p->descrip);
    	foreach ($buscar as $palabra) {
   			$WHERE .= " AND producto_item.busqueda LIKE '%" . $palabra . "%' ";
    	}
    	$sql.= $WHERE;
	} else {
		$sql.=" WHERE producto_item.cod_interno LIKE '%" . $p->cod_interno . "%'";
	}
	if ($p->id_fabrica != "1") {
		$sql.="		AND producto.id_fabrica='" . $p->id_fabrica . "'";
	}
	//$sql.=" ORDER BY producto.descrip, producto_item.id_producto_item";
	$sql.=" ORDER BY fabrica, producto, color, unidad, capacidad";
	$rs = mysql_query($sql);
	while ($reg = mysql_fetch_object($rs)) {
		$reg->capacidad = (float) $reg->capacidad;
		$reg->stock = (float) $reg->stock;
		$reg->cantidad = 0;
		$reg->recibido = 0;
		$reg->precio_lista = (float) $reg->precio_lista;
		$reg->plmasiva = $reg->precio_lista + ($reg->precio_lista * (float) $reg->iva / 100);
		$resultado[] = $reg;
	}
	return $resultado;
  }
}
?>
