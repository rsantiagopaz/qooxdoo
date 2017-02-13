<?php
session_start();

require("Base.php");

class class_Vehiculo extends class_Base
{
  function __construct() {
    parent::__construct();
  }
  
  
  public function calcular_estados($id_movimiento) {
  	$sql = "SELECT id_entsal FROM movimiento WHERE id_movimiento=" . $id_movimiento;
  	$rs = mysql_query($sql);
  	$row = mysql_fetch_object($rs);
  	$id_entsal = $row->id_entsal;
  	
  	$sql = "SELECT id_vehiculo, estado FROM entsal WHERE id_entsal=" . $id_entsal;
  	$rs = mysql_query($sql);
  	$row = mysql_fetch_object($rs);
  	$id_vehiculo = $row->id_vehiculo;
  	$estado = $row->estado;
  	

  	if ($estado != "S") {
  		$sql = "SELECT id_movimiento FROM movimiento WHERE id_entsal=" . $id_entsal . " AND estado='E'";
  		$rs = mysql_query($sql);
  		$estado = ((mysql_num_rows($rs) > 0) ? "T" : "E");
  	}
  	
  	$sql = "SELECT id_movimiento FROM movimiento WHERE id_entsal=" . $id_entsal . " AND ISNULL(documentacion_id)";
  	$rs = mysql_query($sql);
  	$asunto = ((mysql_num_rows($rs) > 0) ? "TRUE" : "FALSE");
  	
  	$sql = "UPDATE entsal SET estado='" . $estado . "', asunto=" . $asunto . " WHERE id_entsal=" . $id_entsal;
  	mysql_query($sql);
  	

  	
  	$sql = "UPDATE vehiculo SET estado='" . $estado . "' WHERE id_vehiculo=" . $id_vehiculo;
  	mysql_query($sql);
  }
  
  
  public function calcular_totales($id_movimiento) {
  	
  	$sql = "SELECT id_movimiento, SUM(total) AS total FROM reparacion WHERE id_movimiento=" . $id_movimiento . " GROUP BY id_movimiento";
  	$rs = mysql_query($sql);
  	$row = mysql_fetch_object($rs);
  	
  	$sql = "UPDATE movimiento SET total=" . $row->total . " WHERE id_movimiento=" . $id_movimiento;
  	mysql_query($sql);
  	

  	
  	
  	$sql = "SELECT id_entsal FROM movimiento WHERE id_movimiento=" . $id_movimiento . " LIMIT 1";
  	$rs = mysql_query($sql);
  	$row = mysql_fetch_object($rs);
  	
  	$id_entsal = $row->id_entsal;

  	$sql = "SELECT id_entsal, SUM(total) AS total FROM movimiento WHERE id_entsal=" . $id_entsal . " GROUP BY id_entsal";
  	$rs = mysql_query($sql);
  	$row = mysql_fetch_object($rs);
  	
  	$sql = "UPDATE entsal SET total=" . $row->total . " WHERE id_entsal=" . $id_entsal;
  	mysql_query($sql);
  	
  	

  	
  	
  	$sql = "SELECT id_vehiculo FROM entsal WHERE id_entsal=" . $id_entsal . " LIMIT 1";
  	$rs = mysql_query($sql);
  	$row = mysql_fetch_object($rs);
  	
  	$id_vehiculo = $row->id_vehiculo;
  	
  	$sql = "SELECT id_vehiculo, SUM(total) AS total FROM entsal WHERE id_vehiculo=" . $id_vehiculo . " GROUP BY id_vehiculo";
  	$rs = mysql_query($sql);
  	$row = mysql_fetch_object($rs);
  	
  	$sql = "UPDATE vehiculo SET total=" . $row->total . " WHERE id_vehiculo=" . $id_vehiculo;
  	mysql_query($sql);
  }
  
  
  public function method_leer_reparacion($params, $error) {
  	$p = $params[0];

  	$opciones = new stdClass;
  	$opciones->costo = "float";
  	$opciones->cantidad = "int";
  	$opciones->total = "float";
  	
	$sql = "SELECT reparacion.*, tipo_reparacion.descrip AS reparacion FROM reparacion INNER JOIN tipo_reparacion USING(id_tipo_reparacion) WHERE id_movimiento=" . $p->id_movimiento;
	
	return $this->toJson($sql, $opciones);
  }
  
  
  public function method_salida_taller($params, $error) {
  	$p = $params[0];
  	
  	mysql_query("START TRANSACTION");
  	
  	$sql = "UPDATE movimiento SET f_sal=NOW(), id_usuario_sal='" . $_SESSION['usuario'] . "', estado='S' WHERE id_movimiento=" . $p->id_movimiento;
  	mysql_query($sql);
  	
  	foreach ($p->model as $item) {
  		$set = $this->prepararCampos($item, "reparacion");
  		
  		$sql = "INSERT reparacion SET " . $set;
  		mysql_query($sql);  		
  	}
  	
  	$this->calcular_totales($p->id_movimiento);
  	$this->calcular_estados($p->id_movimiento);
  	
  	mysql_query("COMMIT");
  }
  
  
  public function method_entrada_taller($params, $error) {
	$p = $params[0];

	mysql_query("START TRANSACTION");

	$sql = "UPDATE vehiculo SET estado='T' WHERE id_vehiculo=" . $p->id_vehiculo;
	mysql_query($sql);

	$sql = "INSERT movimiento SET id_entsal=" . $p->id_entsal . ", cod_razon_social=" . $p->cod_razon_social . ", observa='" . $p->observa . "', f_ent=NOW(), id_usuario_ent='" . $_SESSION['usuario'] . "', estado='E'";
	mysql_query($sql);
	$insert_id = mysql_insert_id();

	$this->calcular_estados($insert_id);

	mysql_query("COMMIT");

	return $insert_id;
  }
  
  
  public function method_leer_movimiento($params, $error) {
  	$p = $params[0];
  	
  	$opciones = new stdClass;
  	$opciones->total = "float";
  	
	$sql = "SELECT movimiento.*, razones_sociales.razon_social AS taller FROM movimiento INNER JOIN `019`.razones_sociales USING(cod_razon_social) WHERE id_entsal=" . $p->id_entsal . " ORDER BY f_ent DESC";
	
	return $this->toJson($sql, $opciones);
  }
  
  
  public function method_entrada_vehiculo($params, $error) {
  	$p = $params[0];
  	
  	mysql_query("START TRANSACTION");
  	
  	$sql = "UPDATE vehiculo SET estado='E' WHERE id_vehiculo=" . $p->id_vehiculo;
  	mysql_query($sql);
  	
  	$sql = "INSERT entsal SET id_vehiculo=" . $p->id_vehiculo . ", observa='" . $p->observa . "', f_ent=NOW(), id_usuario_ent='" . $_SESSION['usuario'] . "', resp_ent='" . $p->resp_ent . "', asunto=FALSE, estado='E'";
  	mysql_query($sql);
  	$insert_id = mysql_insert_id();
  	
  	mysql_query("COMMIT");
  	
  	return $insert_id;
  }
  
  
  public function method_salida_vehiculo($params, $error) {
  	$p = $params[0];
  	
  	mysql_query("START TRANSACTION");
  	
  	$sql = "UPDATE vehiculo SET estado='S' WHERE id_vehiculo=" . $p->id_vehiculo;
  	mysql_query($sql);

  	$sql = "UPDATE entsal SET f_sal=NOW(), id_usuario_sal='" . $_SESSION['usuario'] . "', resp_sal='" . $p->resp_sal . "', estado='S' WHERE id_entsal=" . $p->id_entsal;
  	mysql_query($sql);
  	
  	mysql_query("COMMIT");
  }
  
  
  public function method_leer_vehiculo($params, $error) {
  	$p = $params[0];
  	
  	$resultado = new stdClass;
  	
	function functionAux1(&$row, $key) {
		$row->total = (float) $row->total;
		
		$sql = "SELECT";
		$sql.= "  CONCAT(_organismos_areas.organismo_area, ' (', CASE WHEN _organismos_areas.organismo_area_tipo_id='E' THEN _departamentos.departamento ELSE _organismos.organismo END, ')') AS label";
		$sql.= " FROM (salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id)) LEFT JOIN salud1._departamentos ON _organismos_areas.organismo_areas_id_departamento=_departamentos.codigo_indec";
		$sql.= " WHERE _organismos_areas.organismo_area_id='" . $row->organismo_area_id . "'";
		
		$rsDependencia = mysql_query($sql);
		if (mysql_num_rows($rsDependencia) > 0) {
			$rowDependencia = mysql_fetch_object($rsDependencia);
			$row->dependencia = $rowDependencia->label;
		} else {
			$row->dependencia = "";
		}
	};
  	
  	$opciones = new stdClass;
  	$opciones->functionAux = functionAux1;
  	
  	$sql = "SELECT vehiculo.*, tipo_vehiculo.descrip AS tipo FROM vehiculo INNER JOIN tipo_vehiculo USING(id_tipo_vehiculo) WHERE id_vehiculo=" . $p->id_vehiculo;
  	$resultado = $this->toJson($sql, $opciones);
  	$resultado = $resultado[0];

	return $resultado;
  }
  
  
  public function method_leer_entsal($params, $error) {
  	$p = $params[0];
  	
  	$opciones = new stdClass;
  	$opciones->total = "float";
  	
	$sql = "SELECT * FROM entsal WHERE id_vehiculo=" . $p->id_vehiculo . " ORDER BY f_ent DESC";
	
	return $this->toJson($sql, $opciones);
  }
  
  
  public function method_alta_modifica_vehiculo($params, $error) {
  	$p = $params[0];
  	
  	$sql = "SELECT id_vehiculo FROM vehiculo WHERE nro_patente='" . $p->model->nro_patente . "' AND id_vehiculo <> " . $p->model->id_vehiculo;
  	$rs = mysql_query($sql);
  	if (mysql_num_rows($rs) > 0) {
  		$error->SetError(0, "duplicado");
  		return $error;
  	} else {
		$set = $this->prepararCampos($p->model, "vehiculo");
	  		
		if ($p->model->id_vehiculo == "0") {
	  		$sql = "INSERT vehiculo SET " . $set . ", total=0, estado='S'";
	  		mysql_query($sql);		
		} else {
	  		$sql = "UPDATE vehiculo SET " . $set . " WHERE id_vehiculo=" . $p->model->id_vehiculo;
	  		mysql_query($sql);
		}
  	}
  }
  
  
  public function method_leer_gral($params, $error) {
  	
  	$resultado = new stdClass;
  	$resultado->gral = array();
  	
  	$ent = 0;
  	$tal = 0;
  	$asu = 0;
 	
	$sql = "SELECT id_entsal, CONCAT(nro_patente, '  ', marca) AS vehiculo, f_ent, f_sal, asunto, entsal.estado FROM entsal INNER JOIN vehiculo USING(id_vehiculo) WHERE entsal.estado <> 'S' OR entsal.asunto ORDER BY f_ent DESC";
	$rs = mysql_query($sql);
	while ($row = mysql_fetch_object($rs)) {
		$resultado->gral[] = $row;
		
		if ($row->estado == 'E') $ent+= 1;
		if ($row->estado == 'T') $tal+= 1;
		if ($row->asunto == '1') $asu+= 1;
	}
	
	$resultado->statusBarText = "Entrada: " . $ent . ", Taller: " . $tal . ", En tramite: " . $asu;
	
	return $resultado;
  }
  
  
  public function method_asignar_asunto($params, $error) {
  	$p = $params[0];

	$sql = "SELECT documentacion_id FROM salud1.001_documentaciones WHERE documentacion_id='" . $p->documentacion_id . "'";
	$rs = mysql_query($sql);
	if (mysql_num_rows($rs) == 0) {
  		$error->SetError(0, "documentacion_id");
  		return $error;
	} else {
		mysql_query("START TRANSACTION");
		
		$sql = "UPDATE movimiento SET documentacion_id='" . $p->documentacion_id . "' WHERE id_movimiento=" . $p->id_movimiento;
		mysql_query($sql);
		
		$this->calcular_estados($p->id_movimiento);
		
		mysql_query("COMMIT");
	}
  }
  
  
  public function method_autocompletarVehiculo($params, $error) {
  	$p = $params[0];
  	
	$sql = "SELECT CONCAT(nro_patente, '  ', marca) AS label, id_vehiculo AS model FROM vehiculo WHERE nro_patente LIKE '%" . $p->texto . "%' ORDER BY label";
	return $this->toJson($sql);
  }
  
  
  public function method_autocompletarVehiculoCompleto($params, $error) {
  	$p = $params[0];
  	
	function functionAux(&$row, $key) {
		unset($row->total);
		unset($row->estado);
		
		$resultado = new stdClass;
		
		$resultado->model = $row->id_vehiculo;
		$resultado->label = $row->nro_patente . "  " . $row->marca;
		
		$resultado->vehiculo = $row;

		
		$sql = "SELECT";
		$sql.= "  CONCAT(_organismos_areas.organismo_area, ' (', CASE WHEN _organismos_areas.organismo_area_tipo_id='E' THEN _departamentos.departamento ELSE _organismos.organismo END, ')') AS label";
		$sql.= "  , _organismos_areas.organismo_area_id AS model";
		$sql.= " FROM (salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id)) LEFT JOIN salud1._departamentos ON _organismos_areas.organismo_areas_id_departamento=_departamentos.codigo_indec";
		$sql.= " WHERE _organismos_areas.organismo_area_id='" . $row->organismo_area_id . "'";
		
		$rsDependencia = mysql_query($sql);
		if (mysql_num_rows($rsDependencia) > 0) $resultado->cboDependencia = mysql_fetch_object($rsDependencia);

		return $resultado;
	};
  	
  	$opciones = new stdClass;
  	$opciones->functionAux = functionAux;
  	
	$sql = "SELECT * FROM vehiculo WHERE nro_patente LIKE '%" . $p->texto . "%' ORDER BY nro_patente, marca";
	return $this->toJson($sql, $opciones);
  }
  
  
  public function method_autocompletarTipo_vehiculo($params, $error) {
  	$p = $params[0];
  	
	$sql = "SELECT descrip AS label, id_tipo_vehiculo AS model FROM tipo_vehiculo WHERE descrip LIKE '%" . $p->texto . "%' ORDER BY label";
	return $this->toJson($sql);
  }
  
  
  public function method_autocompletarDependencia($params, $error) {
  	$p = $params[0];
  	
	$sql = " (SELECT _organismos_areas.organismo_area_id AS model, CONCAT(_organismos_areas.organismo_area, ' (', _departamentos.departamento, ')') AS label FROM salud1._organismos_areas INNER JOIN salud1._departamentos ON _organismos_areas.organismo_areas_id_departamento=_departamentos.codigo_indec WHERE _organismos_areas.organismo_area_tipo_id='E' AND _departamentos.provincia_id=21 AND _organismos_areas.organismo_area LIKE '%" . $p->texto . "%')";
	$sql.= " UNION DISTINCT";
	$sql.= " (SELECT _organismos_areas.organismo_area_id AS model, CONCAT(_organismos_areas.organismo_area, ' (', _organismos.organismo, ')') AS label FROM salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id) WHERE _organismos_areas.organismo_area_tipo_id<>'E' AND (_organismos_areas.organismo_id='33' OR _organismos_areas.organismo_id='54') AND _organismos_areas.organismo_area LIKE '%" . $p->texto . "%')";
	$sql.= " ORDER BY label";
	return $this->toJson(mysql_query($sql));
  }
}

?>