<?php

require("Base.php");

class class_Viaticos extends class_Base
{
  function __construct() {
    parent::__construct();
  }
  
  
  public function method_validar_viatico($params, $error) {
  	$p = $params[0];
  	
  	$resultado = array();
  	
  	
  	$fecha_desde = substr($p->fecha_desde2, 0, 10) . " " . $p->hora_desde2;
  	$fecha_hasta = substr($p->fecha_hasta2, 0, 10) . " " . $p->hora_hasta2;
  	$sql = "SELECT id_viatico FROM viatico WHERE id_viatico <> '" . $p->id_viatico . "' AND id_personal='" . $p->id_personal . "' AND GREATEST(ADDTIME(fecha_desde2, hora_desde2), '" . $fecha_desde . "') - LEAST(ADDTIME(fecha_hasta2, hora_hasta2), '" . $fecha_hasta . "') < 0";
	$rs = mysql_query($sql);
	if (mysql_num_rows($rs) > 0) {
		$item = new stdClass();
		$item->descrip = "intervalo";
		$item->message = " El titular ya tiene un viático asignado que está en conflicto con este intervalo definido ";
		$resultado[] = $item;
	}
	

  	$sql = "SELECT SUM(subtotal_viatico2) AS total, id_personal FROM viatico WHERE id_viatico <> '" . $p->id_viatico . "' AND id_personal='" . $p->id_personal . "' AND YEAR(fecha_desde2)=" . substr($fecha_desde, 0, 4) . " AND MONTH(fecha_desde2)=" . substr($fecha_desde, 5, 2) . " GROUP BY id_personal";
	$rs = mysql_query($sql);
	if (mysql_num_rows($rs) > 0) {
		$row = mysql_fetch_object($rs);
		$total = (float) $row->total;
	} else {
		$total = 0;
	}
	$sql = "SELECT porc_tope_cargo FROM paramet WHERE id_paramet=1";
	$rs = mysql_query($sql);
	$row = mysql_fetch_object($rs);
	$porc_tope_cargo = (float) $row->porc_tope_cargo;
	if ($total + $p->subtotal_viatico2 > $p->codigo_002per * $porc_tope_cargo / 100) {
		$item = new stdClass();
		$item->descrip = "porc_tope_cargo";
		$item->message = "Con este importe de viático $" . $p->subtotal_viatico2 . " se supera tope mensual";
		$resultado[] = $item;
	}

	

	$sql = "SELECT documentacion_id FROM salud1.001_documentaciones WHERE documentacion_id='" . $p->documentacion_id . "'";
	$rs = mysql_query($sql);
	$bool1 = (mysql_num_rows($rs)==0);
	$sql = "SELECT organismo_area_de_id, organismo_area_para_id FROM salud1.001_documentaciones_seguimientos WHERE documentacion_id='" . $p->documentacion_id . "' ORDER BY seguimiento_id_orden DESC LIMIT 1";
	$rs = mysql_query($sql);
	$row = mysql_fetch_object($rs);
	$bool2 = ($row->organismo_area_de_id != $p->organismo_area_id || trim($row->organismo_area_para_id) != "");
	if ($bool1 || $bool2) {
		$item = new stdClass();
		$item->descrip = "documentacion_id";
		$item->message = " Asunto inválido ";
		$resultado[] = $item;
	}

	
	
	return $resultado;
  }


  public function method_estado_viatico($params, $error) {
  	$p = $params[0];

	$sql = "UPDATE viatico SET estado = '" . $p->estado . "', json = '" . $p->json . "' WHERE id_viatico='" . $p->id_viatico . "'";
	mysql_query($sql);
  }
  
  
  public function method_rendir_viatico($params, $error) {
  	$p = $params[0];
  	$set = $this->prepararCampos($p->model, "viatico");

	$sql = "UPDATE viatico SET " . $set . " WHERE id_viatico='" . $p->model->id_viatico . "'";
	mysql_query($sql);
  }
    
  
  public function method_leer_viatico($params, $error) {
  	$p = $params[0];
  	
  	$id_viatico = $p;

	$resultado = new stdClass();
	$sql = "SELECT * FROM viatico WHERE id_viatico='" . $id_viatico . "'";
	$resultado->viatico = $this->toJson($sql);
	$resultado->viatico = $resultado->viatico[0];

	$resultado->viatico->nro_viatico = (int) $resultado->viatico->nro_viatico;
	$resultado->viatico->fuera_provincia = (bool) $resultado->viatico->fuera_provincia;
	$resultado->viatico->cant_dias_viatico1 = (int) $resultado->viatico->cant_dias_viatico1;
	$resultado->viatico->monto_diario_viatico = (float) $resultado->viatico->monto_diario_viatico;
	$resultado->viatico->adicional_viatico1 = (float) $resultado->viatico->adicional_viatico1;
	$resultado->viatico->subtotal_viatico1 = (float) $resultado->viatico->subtotal_viatico1;
	$resultado->viatico->combustible1 = (float) $resultado->viatico->combustible1;
	$resultado->viatico->pasajes1 = (float) $resultado->viatico->pasajes1;
	$resultado->viatico->otros_gastos1 = (float) $resultado->viatico->otros_gastos1;
	$resultado->viatico->cant_dias_alojam1 = (int) $resultado->viatico->cant_dias_alojam1;
	$resultado->viatico->monto_diario_alojam = (float) $resultado->viatico->monto_diario_alojam;
	$resultado->viatico->subtotal_alojam1 = (float) $resultado->viatico->subtotal_alojam1;
	$resultado->viatico->con_funcionario = (bool) $resultado->viatico->con_funcionario;
	$resultado->viatico->nro_cheque = (int) $resultado->viatico->nro_cheque;
	$resultado->viatico->subtotal_viatico2 = (float) $resultado->viatico->subtotal_viatico2;
	$resultado->viatico->subtotal_alojam2 = (float) $resultado->viatico->subtotal_alojam2;
	$resultado->viatico->combustible2 = (float) $resultado->viatico->combustible2;
	$resultado->viatico->pasajes2 = (float) $resultado->viatico->pasajes2;
	$resultado->viatico->otros_gastos2 = (float) $resultado->viatico->otros_gastos2;
	$resultado->viatico->importe_total = (float) $resultado->viatico->importe_total;
	
	
	$sql = "SELECT CONCAT(organismo_area, ' (', organismo, ')') AS label, organismo_area_id AS model FROM salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id) WHERE organismo_area_id='" . $resultado->viatico->organismo_area_id . "'";
	$resultado->cboOrganismoArea = $this->toJson($sql);
	$resultado->cboOrganismoArea = $resultado->cboOrganismoArea[0];
	
	$sql = "SELECT CONCAT(TRIM(apenom), ' (', dni, ')') AS label, id_personal AS model, codigo_002, funcionario FROM salud1._personal WHERE id_personal='" . $resultado->viatico->id_personal . "'";
	$opciones = array("funcionario"=>"bool");
	$resultado->cboPersonal = $this->toJson($sql, $opciones);
	$resultado->cboPersonal = $resultado->cboPersonal[0];
	
	$sql = "SELECT descrip AS label, id_motivo AS model FROM motivo WHERE id_motivo='" . $resultado->viatico->id_motivo . "'";
	$resultado->cboMotivo = $this->toJson($sql);
	$resultado->cboMotivo = $resultado->cboMotivo[0];
	
	$sql = "SELECT CONCAT(organismo_area, ' (', organismo, ')') AS label, organismo_area_id AS model FROM salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id) WHERE organismo_area_id='" . $resultado->viatico->organismo_area_id_origen . "'";
	$resultado->cboOrganismoAreaOrigen = $this->toJson($sql);
	$resultado->cboOrganismoAreaOrigen = $resultado->cboOrganismoAreaOrigen[0];
	
	//SELECT CONCAT(NRO_PAT, '  ', MARCA) AS label, COD_VEHICULO AS model FROM 017.vehiculos WHERE COD_VEHICULO='9'
	$sql = "SELECT CONCAT(NRO_PAT, '  ', MARCA) AS label, COD_VEHICULO AS model FROM `017`.vehiculos WHERE COD_VEHICULO = '" . $resultado->viatico->COD_VEHICULO . "'";
	$resultado->cboVehiculo = $this->toJson($sql);
	$resultado->cboVehiculo = $resultado->cboVehiculo[0];
	
	$sql = "SELECT CONCAT(TRIM(apenom), ' (', dni, ')') AS label, id_personal AS model, codigo_002, funcionario FROM salud1._personal WHERE id_personal='" . $resultado->viatico->id_funcionario . "'";
	$opciones = array("funcionario"=>"bool");
	$resultado->cboFuncionario = $this->toJson($sql, $opciones);
	$resultado->cboFuncionario = $resultado->cboFuncionario[0];
	
	/*
	$sql = "SELECT descrip AS label, id_cta_cte AS model FROM cta_cte WHERE id_cta_cte='" . $resultado->viatico->id_cta_cte . "'";
	$resultado->cboCtaCte = $this->toJson($sql);
	$resultado->cboCtaCte = $resultado->cboCtaCte[0];
	*/
	
	$sql = "SELECT CONCAT(salud1._localidades.localidad, ' (', salud1._departamentos.departamento, ')') AS label, viatico_localidad.localidad_id AS model FROM (viatico_localidad INNER JOIN salud1._localidades USING(localidad_id)) INNER JOIN salud1._departamentos USING(departamento_id) WHERE viatico_localidad.id_viatico='" . $id_viatico . "'";
	$resultado->localidad = $this->toJson($sql);
	
	return $resultado;
  }

  public function method_leer_viaticos($params, $error) {
  	$p = $params[0];
  	
  	$resultado = array();
  	
  	$tipo_descrip = "CASE tipo_viatico WHEN 'A' THEN 'Anticipo' WHEN 'R' THEN 'Reintegro' ELSE 'R.Mensual' END AS tipo_descrip";
  	$estado_descrip = "CASE estado WHEN 'E' THEN 'Emitido' WHEN 'L' THEN 'Liquidado' WHEN 'R' THEN 'Rendido' WHEN 'C' THEN 'Cerrado' ELSE 'Anulado' END AS estado_descrip";
	//$sql = "SELECT id_viatico, documentacion_id, organismo_area, apenom, importe_total, tipo_viatico, estado, " . $tipo_descrip . ", " . $estado_descrip . " FROM (viatico INNER JOIN salud1._organismos_areas ON viatico.organismo_area_id_origen = salud1._organismos_areas.organismo_area_id) INNER JOIN salud1._personal USING(id_personal) WHERE viatico.organismo_area_id='" . $p->organismo_area_id . "' AND _personal.apenom LIKE '" . $p->filtrar . "%'";
	$sql = "SELECT id_viatico, documentacion_id, organismo_area_id, organismo_area_id_origen, apenom, importe_total, viatico.json, tipo_viatico, estado, " . $tipo_descrip . ", " . $estado_descrip . " FROM viatico INNER JOIN salud1._personal USING(id_personal) WHERE _personal.apenom LIKE '" . $p->filtrar . "%' ORDER BY documentacion_id";
	$rs = mysql_query($sql);
	while ($row = mysql_fetch_object($rs)) {
		$row->importe_total = (float) $row->importe_total;
		
		$sql = "SELECT CONCAT(organismo_area, ' (', organismo, ')') AS label, organismo_area_id AS model FROM salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id) WHERE organismo_area_id='" . $row->organismo_area_id . "'";
		$rsAux = mysql_query($sql);
		$rowAux = mysql_fetch_object($rsAux);
		$row->organismo_area = $rowAux->label;
		
		$sql = "SELECT CONCAT(organismo_area, ' (', organismo, ')') AS label, organismo_area_id AS model FROM salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id) WHERE organismo_area_id='" . $row->organismo_area_id_origen . "'";
		$rsAux = mysql_query($sql);
		$rowAux = mysql_fetch_object($rsAux);
		$row->organismo_area_origen = $rowAux->label;
	
		$resultado[] = $row;
	}

	return $resultado;
  }


  public function method_autocompletarVehiculo($params, $error) {
  	$p = $params[0];
  	
	$sql = "SELECT CONCAT(NRO_PAT, '  ', MARCA) AS label, COD_VEHICULO AS model FROM `017`.vehiculos WHERE NRO_PAT LIKE '%" . $p . "%' ORDER BY label";
	return $this->toJson(mysql_query($sql));
  }
  
  
  public function method_autocompletarLocalidad($params, $error) {
  	$p = $params[0];
  	//$rs = mysql_query("SELECT localidad_id AS id, CONCAT(localidad, ' (', departamento, ')') AS descrip FROM salud1._localidades INNER JOIN salud1._departamentos USING(departamento_id) WHERE " . (($_REQUEST['id']==null) ? "localidad LIKE '%" . $_REQUEST['descrip'] . "%'" : "localidad_id=" . $_REQUEST['id']));
	$sql = "SELECT CONCAT(localidad, ' (', departamento, ')') AS label, localidad_id AS model FROM salud1._localidades INNER JOIN salud1._departamentos USING(departamento_id) WHERE localidad LIKE '%" . $p . "%' ORDER BY label";
	return $this->toJson($sql);
  }


  public function method_autocompletarCtaCte($params, $error) {
  	$p = $params[0];
	$sql = "SELECT descrip AS label, id_cta_cte AS model FROM cta_cte WHERE descrip LIKE '%" . $p . "%' ORDER BY label";
	return $this->toJson($sql);
  }
  
  
  public function method_autocompletarMotivo($params, $error) {
  	$p = $params[0];
	$sql = "SELECT descrip AS label, id_motivo AS model FROM motivo WHERE descrip LIKE '%" . $p . "%' ORDER BY label";
	return $this->toJson($sql);
  }
  

  public function method_autocompletarPersonal($params, $error) {
  	$p = $params[0];
  	
  	$opciones = array("funcionario"=>"bool");
  	
	$sql = "SELECT CONCAT(TRIM(apenom), ' (', dni, ')') AS label, id_personal AS model, codigo_002, funcionario FROM salud1._personal WHERE apenom LIKE '%" . $p . "%' ORDER BY label";
	return $this->toJson($sql, $opciones);
  }
  
  
  public function method_autocompletarFuncionario($params, $error) {
  	$p = $params[0];
  	set_time_limit(120);
  	
  	$opciones = array("funcionario"=>"bool");
  	
	$sql = "SELECT CONCAT(TRIM(apenom), ' (', dni, ')') AS label, id_personal AS model, codigo_002, funcionario FROM salud1._personal WHERE apenom LIKE '%" . $p . "%' AND funcionario ORDER BY label";
	return $this->toJson($sql, $opciones);
  }
  
  
  public function method_autocompletarOrganismoArea($params, $error) {
  	$p = $params[0];
	$sql = "SELECT CONCAT(organismo_area, ' (', organismo, ')') AS label, organismo_area_id AS model FROM salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id) WHERE organismo_area LIKE '%" . $p . "%' ORDER BY label";
	return $this->toJson($sql);
  }
  
  
  public function method_alta_modifica_viatico($params, $error) {
  	$p = $params[0];
  	
	if ($p->model->con_funcionario && $p->model->estado!="R") {
		$sql = "UPDATE salud1._personal SET codigo_002='" . $p->model->codigo_002fun . "' WHERE id_personal = '" . $p->model->id_funcionario . "'";
		mysql_query($sql);
	} else {
		$p->model->id_funcionario = null;
	}
	
	if ($p->model->estado!="R") {
		$sql = "UPDATE salud1._personal SET codigo_002='" . $p->model->codigo_002per . "' WHERE id_personal = '" . $p->model->id_personal . "'";
		mysql_query($sql);
	}
	
	$set = $this->prepararCampos($p->model, "viatico");
	$id_viatico = $p->model->id_viatico;
	if ($id_viatico=="0") {
		$sql = "INSERT viatico SET " . $set . ", fecha_tramite=NOW()";
		mysql_query($sql);
		$id_viatico = mysql_insert_id();
		if ($p->model->tipo_viatico=="A") {
			$sql = "UPDATE paramet SET nro_viatico='" . $p->model->nro_viatico . "' WHERE id_paramet = 1";
			mysql_query($sql);
		}
	} else {
		if ($p->model->estado!="R") {
			$sql="DELETE FROM viatico_localidad WHERE id_viatico='" . $id_viatico . "'";
			mysql_query($sql);
		}
		
		$sql = "UPDATE viatico SET " . $set . " WHERE id_viatico='" . $id_viatico . "'";
		mysql_query($sql);
	}
	
	if ($p->model->estado!="R") {
		foreach ($p->localidad as $item) {
			$sql = "INSERT viatico_localidad SET id_viatico='" . $id_viatico . "', localidad_id='" . $item . "'";
			mysql_query($sql);
		}
	}
	
	return $id_viatico;
  }
  
  
  public function method_leer_cta_cte($params, $error) {
	$sql = "SELECT * FROM cta_cte ORDER BY descrip";
	return $this->toJson($sql);
  }
  
  
  public function method_leer_motivos($params, $error) {
  	$p = $params[0];

	$sql = "SELECT * FROM motivo ORDER BY descrip";
	return $this->toJson($sql);
  }
  
  
  public function method_escribir_motivo($params, $error) {
  	$p = $params[0];
  	
  	$cambios = $p->cambios;
  	
	mysql_query("START TRANSACTION");
	
	foreach ($cambios->altas as $item) {
		$sql="INSERT INTO motivo SET descrip='" . $item->descrip . "'";
		mysql_query($sql);
		if (mysql_errno()) break;
	}
	if (! mysql_errno()) {
		foreach ($cambios->modificados as $item) {
			$sql="UPDATE motivo SET descrip='" . $item->descrip . "' WHERE id_motivo='" . $item->id_motivo . "'";
			mysql_query($sql);
			if (mysql_errno()) break;
		}	
	}
	if (mysql_errno()) {
		mysql_query("ROLLBACK");
		return mysql_error();
	} else {
		mysql_query("COMMIT");
	}
  }

  
  public function method_leer_paramet($params, $error) {
  	$p = $params[0];

	$sql = "SELECT * FROM paramet";
	$resultado = $this->toJson($sql);
	foreach($resultado[0] as $key => $value) {
		$resultado[0]->$key = (float) $value;
	}
	
	return $resultado;
  }
  
  
  public function method_escribir_paramet($params, $error) {
  	$p = $params[0];

	$set = $this->prepararCampos($p);
	$sql = "UPDATE paramet SET " . $set . " WHERE id_paramet=1";
	mysql_query($sql);
  }
  
  
  public function method_escribir_cta_cte($params, $error) {
  	$p = $params[0];
  	
  	$cambios = $p->cambios;
  	
  	try {
		mysql_query("START TRANSACTION");
		
		foreach ($cambios->altas as $item) {
			$sql="INSERT INTO cta_cte SET descrip='" . $item->descrip . "'";
			mysql_query($sql);
		}
	
		foreach ($cambios->modificados as $item) {
			$sql="UPDATE cta_cte SET descrip='" . $item->descrip . "' WHERE id_cta_cte='" . $item->id_cta_cte . "'";
			mysql_query($sql);
		}	
	
		mysql_query("COMMIT");
	
	} catch (Exception $e) {
		mysql_query("ROLLBACK");
	}
  }
  
  public function method_leer_personal($params, $error) {
  	$p = $params[0];
  	
  	$opciones = array("codigo_002"=>"float", "funcionario"=>"bool");
  	
	$sql = "SELECT id_personal, CONCAT(TRIM(apenom), ' (', dni, ')') AS descrip, codigo_002, funcionario FROM salud1._personal WHERE apenom LIKE '%" . $p->descrip . "%' ORDER BY descrip";
	return $this->toJson($sql, $opciones);
  }
  
  
  public function method_escribir_personal($params, $error) {
  	$p = $params[0];
  	
	$sql = "UPDATE salud1._personal SET codigo_002=" . $p->codigo_002 . ", funcionario=" . (($p->funcionario) ? "TRUE" : "FALSE") . " WHERE id_personal=" . $p->id_personal;
	mysql_query($sql);
  }
}

?>
