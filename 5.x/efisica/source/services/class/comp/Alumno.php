<?php

require("Base.php");

class class_Alumno extends class_Base
{
  function __construct() {
    parent::__construct();
  }
  
  
  public function method_buscar_alumno($params, $error) {
  	$p = $params[0];

	$sql = "SELECT id_alumno, CONCAT(apellido, ', ', nombre) AS apenom, tipo_doc, nro_doc, domicilio, telefono FROM alumno WHERE apellido LIKE '%" . $p->descrip . "%' OR nombre LIKE '%" . $p->descrip . "%' ORDER BY apellido, nombre";
	return $this->toJson($sql);
  }



  public function method_leer_alumno($params, $error) {
  	$p = $params[0];
	$resultado = new stdClass;
	
	$sql = "SELECT * FROM alumno WHERE id_alumno=" . $p->id_alumno;
	$resultado->datos = $this->toJson($sql);
	$resultado->datos = $resultado->datos[0];
	
	$sql = "SELECT id_actividad, SUBSTRING(texto, 1, 128) AS texto, tipo, fecha FROM actividad WHERE id_alumno=" . $p->id_alumno . " ORDER BY fecha DESC";
	$resultado->actividad = $this->toJson($sql);
	
	$sql = "SELECT inscripcion.id_inscripcion, curso.descrip, CONCAT('Pagos: ', cant_pagos, ', Saldo: ', saldo) AS subtitle FROM inscripcion INNER JOIN curso USING(id_curso) WHERE id_alumno=" . $p->id_alumno;
	$resultado->inscripcion = $this->toJson($sql);
	
	$sql = "SELECT curso.descrip, CONCAT('Monto: ', pago.monto, ', ', pago.fecha) AS subtitle FROM (pago INNER JOIN inscripcion USING(id_inscripcion)) INNER JOIN curso USING(id_curso) WHERE inscripcion.id_alumno=" . $p->id_alumno . " ORDER BY pago.fecha DESC";
	$resultado->pago = $this->toJson($sql);
	
	return $resultado;
  }
  
  
  public function method_grabar_alumno($params, $error) {
  	$p = $params[0];
  	
  	$mysqli = $this->mysqli;
  	
  	$id_alumno = $p->id_alumno;
  	$set = $this->prepararCampos($p->model, "alumno");
  	
  	if (is_null($id_alumno)) {
		$sql = "INSERT alumno SET " . $set;
		$mysqli->query($sql);
		
		$id_alumno = (string) $mysqli->insert_id;
  	} else {
		$sql = "UPDATE alumno SET " . $set . " WHERE id_alumno=" . $id_alumno;
		$mysqli->query($sql);
  	}
  	
  	return $id_alumno;
  }
  
  
  public function method_agregar_documento($params, $error) {
  	$p = $params[0];
  	
  	$mysqli = $this->mysqli;
  	
	if ($p->tipo == "D") {
		if (! is_dir("documentos/alumno/" . $p->id_alumno)) mkdir("documentos/alumno/" . $p->id_alumno);
		rename("php-traditional-server-master/files/" . $p->uuid . "/" . $p->uploadName, "documentos/alumno/" . $p->id_alumno . "/" . $p->uploadName);
		rmdir("php-traditional-server-master/files/" . $p->uuid);
		
		$sql = "INSERT actividad SET id_alumno=" . $p->id_alumno . ", tipo='D', texto='" . $p->uploadName . "', fecha=NOW()";
		$mysqli->query($sql);
	} else {
		if (is_null($p->id_actividad)) {
			$sql = "INSERT actividad SET id_alumno=" . $p->id_alumno . ", tipo='T', texto='" . $p->texto . "', fecha=NOW()";
			$mysqli->query($sql);
		} else {
			$sql = "UPDATE actividad SET texto='" . $p->texto . "' WHERE id_actividad=" . $p->id_actividad;
			$mysqli->query($sql);
		}
	}
  }
  
  
  public function method_leer_actividad_texto($params, $error) {
  	$p = $params[0];
	
	$sql = "SELECT texto FROM actividad WHERE id_actividad=" . $p->id_actividad;
	$aux = $this->toJson($sql);
	
	return $aux[0]->texto;
  }
  
  
  public function method_eliminar_actividad($params, $error) {
  	$p = $params[0];
  	
  	$mysqli = $this->mysqli;
  	
  	$sql = "SELECT * FROM actividad WHERE id_actividad=" . $p->id_actividad;
  	$rs = $mysqli->query($sql);
  	$row = $rs->fetch_object();
  	
  	if ($row->tipo == "D") unlink("documentos/alumno/" . $p->id_alumno . "/" . $row->texto);
	
	$sql = "DELETE FROM actividad WHERE id_actividad=" . $p->id_actividad;
	$mysqli->query($sql);
  }
}
?>
