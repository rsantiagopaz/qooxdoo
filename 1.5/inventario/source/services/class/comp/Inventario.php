<?php

require("Base.php");

class class_Inventario extends class_Base
{
  function __construct() {
    parent::__construct();
  }
  
  
  public function method_leer_bienes($params, $error) {
  	$p = $params[0];
  	
  	$resultado = array();

	$sql = "(SELECT bien.*, tipo_bien.descrip AS tipo_bien_descrip FROM bien INNER JOIN tipo_bien USING(id_tipo_bien) WHERE organismo_area_id='" . $p->organismo_area_id . "' AND estado LIKE '%" . $p->estado . "%' AND bien.descrip LIKE '%" . $p->buscar . "%')";
	$sql.= " UNION DISTINCT (SELECT bien.*, tipo_bien.descrip AS tipo_bien_descrip FROM bien INNER JOIN tipo_bien USING(id_tipo_bien) WHERE organismo_area_id='" . $p->organismo_area_id . "' AND estado LIKE '%" . $p->estado . "%' AND codigo_barra='" . $p->buscar . "')";
	$sql.= " UNION DISTINCT (SELECT bien.*, tipo_bien.descrip AS tipo_bien_descrip FROM bien INNER JOIN tipo_bien USING(id_tipo_bien) WHERE organismo_area_id='" . $p->organismo_area_id . "' AND estado LIKE '%" . $p->estado . "%' AND codigo_qr='" . $p->buscar . "')";
	$sql.= " ORDER BY descrip";
	$rs = mysql_query($sql);
	
	while ($rowBien = mysql_fetch_object($rs)) {
		//$sql = "SELECT movimiento.id_organismo_area_servicio_destino, CONCAT(_organismos_areas.organismo_area, ' - ', _servicios.denominacion) AS destino FROM ((movimiento INNER JOIN salud1._organismos_areas_servicios ON movimiento.id_organismo_area_servicio_destino=_organismos_areas_servicios.id_organismo_area_servicio) INNER JOIN salud1._organismos_areas ON _organismos_areas_servicios.id_organismo_area=_organismos_areas.organismo_area_id) INNER JOIN salud1._servicios USING(id_servicio) WHERE movimiento.id_bien=" . $rowBien->id_bien . " ORDER BY id_movimiento DESC LIMIT 1";
		$sql = "SELECT movimiento.id_organismo_area_servicio_destino, _servicios.denominacion AS destino FROM ((movimiento INNER JOIN salud1._organismos_areas_servicios ON movimiento.id_organismo_area_servicio_destino=_organismos_areas_servicios.id_organismo_area_servicio) INNER JOIN salud1._organismos_areas ON _organismos_areas_servicios.id_organismo_area=_organismos_areas.organismo_area_id) INNER JOIN salud1._servicios USING(id_servicio) WHERE movimiento.id_bien=" . $rowBien->id_bien . " ORDER BY id_movimiento DESC LIMIT 1";
		$rsAux = mysql_query($sql);
		$rowAux = mysql_fetch_object($rsAux);
		$rowBien->id_organismo_area_servicio_destino = $rowAux->id_organismo_area_servicio_destino;
		$rowBien->destino = $rowAux->destino;
		$rowBien->estado_descrip = ($rowBien->estado=="1") ? "Exist." : "Baja";
		
		$resultado[] = $rowBien;
	}
	
	return $resultado;
  }
  
  
  public function method_alta_modifica_bien($params, $error) {
  	$p = $params[0];
  	
  	$resultado = null;
  	
	$set = $this->prepararCampos($p->model, "bien");
	$resultado = $p->model->id_bien;
	if ($resultado=="0") {
		$resultado = array();
		for ($i=0; $i < $p->model->cantidad; $i++) {
			$sql = "INSERT bien SET " . $set . ", nro_serie='" . $p->nro_serie[$i]->nro_serie . "', fecha_alta=NOW()";
			mysql_query($sql);
			$insert_id = mysql_insert_id();
			$resultado[] = $insert_id;
			
			$sql = "UPDATE bien SET codigo_barra = CONCAT(id_tipo_bien, id_bien), codigo_qr = CONCAT(id_bien, ' - ', descrip, ' - ', id_oas_usuario_alta) WHERE id_bien=" . $insert_id;
			mysql_query($sql);
			
			
			$sql = "INSERT movimiento SET id_bien=" . $insert_id . ", tipo_movimiento='A', fecha_movimiento=NOW(), id_organismo_area_servicio_destino='" . $p->model->id_organismo_area_servicio_destino . "', id_oas_usuario_movimiento='" . $p->model->id_oas_usuario_alta . "'";
			mysql_query($sql);
		}
	} else {
		$sql = "UPDATE bien SET " . $set . " WHERE id_bien=" . $resultado;
		mysql_query($sql);
		
		$sql = "UPDATE bien SET codigo_barra = CONCAT(id_tipo_bien, id_bien), codigo_qr = CONCAT(id_bien, ' - ', descrip, ' - ', id_oas_usuario_alta) WHERE id_bien=" . $resultado;
		mysql_query($sql);
	}

	return $resultado;
  }
  
  
  public function method_baja_bien($params, $error) {
  	$p = $params[0];
  	
  	$resultado = null;
  	
	$set = $this->prepararCampos($p->model, "bien");
	$resultado = $p->model->id_bien;

	$sql = "UPDATE bien SET " . $set . ", fecha_baja=NOW() WHERE id_bien='" . $resultado . "'";
	mysql_query($sql);
	
	$sql = "SELECT id_organismo_area_servicio_destino FROM movimiento WHERE id_bien=" . $resultado . " ORDER BY id_movimiento DESC LIMIT 1";
	$rsAux = mysql_query($sql);
	$rowAux = mysql_fetch_object($rsAux);

	$sql = "INSERT movimiento SET id_bien=" . $resultado . ", tipo_movimiento='B', fecha_movimiento=NOW(), id_organismo_area_servicio_origen=" . $rowAux->id_organismo_area_servicio_destino . ", id_oas_usuario_movimiento='" . $p->model->id_oas_usuario_baja . "'";
	mysql_query($sql);

	return $resultado;
  }
  
  
  public function method_leer_bien($params, $error) {
  	$p = $params[0];
  	
	$resultado = new stdClass();
	$sql = "SELECT * FROM bien WHERE id_bien='" . $p->id_bien . "'";
	$resultado->model = $this->toJson($sql);
	$resultado->model = $resultado->model[0];
	
	return $resultado;
  }
  
  
  public function method_alta_movimiento($params, $error) {
	$p = $params[0];
	
	$sql = "INSERT movimiento SET id_bien=" . $p->id_bien . ", tipo_movimiento='P', fecha_movimiento=NOW(), id_organismo_area_servicio_origen='" . $p->id_organismo_area_servicio_origen . "', id_organismo_area_servicio_destino='" . $p->id_organismo_area_servicio_destino . "', id_oas_usuario_movimiento='" . $p->id_oas_usuario_movimiento . "'";
	mysql_query($sql);
  }
  
  
  public function method_autocompletarOAS($params, $error) {
  	$p = $params[0];
	//$sql = "SELECT descrip AS label, id_motivo AS model FROM motivo WHERE descrip LIKE '%" . $p . "%' ORDER BY label";
	//$sql = "SELECT CONCAT(_organismos_areas.organismo_area, ' - ', _servicios.denominacion) AS label, _organismos_areas_servicios.id_organismo_area_servicio AS model FROM (salud1._organismos_areas_servicios INNER JOIN salud1._organismos_areas ON _organismos_areas_servicios.id_organismo_area=_organismos_areas.organismo_area_id) INNER JOIN salud1._servicios USING(id_servicio) WHERE _organismos_areas.organismo_area_id='" . $p->parametros->organismo_area_id . "' AND _organismos_areas.organismo_area LIKE '%" . $p->texto . "%' ORDER BY label";
	$sql = "SELECT CONCAT(_organismos_areas.organismo_area, ' - ', _servicios.denominacion) AS label, _organismos_areas_servicios.id_organismo_area_servicio AS model FROM (salud1._organismos_areas_servicios INNER JOIN salud1._organismos_areas ON _organismos_areas_servicios.id_organismo_area=_organismos_areas.organismo_area_id) INNER JOIN salud1._servicios USING(id_servicio) WHERE _organismos_areas.organismo_area_id='" . $p->parametros->organismo_area_id . "' AND (_organismos_areas.organismo_area LIKE '%" . $p->texto . "%' OR _servicios.denominacion LIKE '%" . $p->texto . "%') ORDER BY label";
	return $this->toJson($sql);
  }
  
  
  public function method_leer_parametros_inicio($params, $error) {
  	$resultado = new stdClass;
  	
	$resultado->tipo_bien = $this->toJson("SELECT * FROM tipo_bien ORDER BY descrip");
	$resultado->tipo_alta = $this->toJson("SELECT * FROM tipo_alta ORDER BY descrip");
	$resultado->tipo_baja = $this->toJson("SELECT * FROM tipo_baja ORDER BY descrip");

	return $resultado;
  }

}

?>
