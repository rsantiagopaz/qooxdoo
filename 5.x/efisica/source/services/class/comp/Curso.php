<?php

require("Base.php");

class class_Curso extends class_Base
{
  function __construct() {
    parent::__construct();
  }
  
  
  public function method_leer_cursos($params, $error) {
  	$p = $params[0];

	$sql = "SELECT id_curso, descrip, CONCAT('cuota $', monto_cuota, ', ', cant_cuotas, ' cuotas, ', cant_inscriptos, ' inscriptos') AS subtitle FROM curso ORDER BY id_curso DESC";
	return $this->toJson($sql);
  }
  
  
  public function method_leer_curso($params, $error) {
  	$p = $params[0];

	$opciones = new stdClass;
	$opciones->cant_cuotas = "int";
	$opciones->monto_cuota = "float";
	$opciones->total = "float";
	
	$sql = "SELECT * FROM curso WHERE id_curso=" . $p->id_curso;
	$aux = $this->toJson($sql, $opciones);
	return $aux[0];
  }

  
  public function method_grabar_curso($params, $error) {
  	$p = $params[0];
  	
  	$mysqli = $this->mysqli;
  	
  	$id_curso = $p->id_curso;
  	$set = $this->prepararCampos($p->model, "curso");
  	
  	if (is_null($id_curso)) {
		$sql = "INSERT curso SET " . $set . ", cant_inscriptos=0";
		$mysqli->query($sql);
		
		$id_curso = (string) $mysqli->insert_id;
  	} else {
		$sql = "UPDATE curso SET " . $set . " WHERE id_curso=" . $id_curso;
		$mysqli->query($sql);
  	}
  	
  	return $id_curso;
  }
  
  
  public function method_leer_cursos_inscripcion($params, $error) {
  	$p = $params[0];
	
	$sql = "SELECT curso.id_curso, curso.descrip";
	$sql.= " FROM curso";
	$sql.= " WHERE curso.id_curso NOT IN (SELECT curso.id_curso FROM (curso INNER JOIN inscripcion USING(id_curso)) INNER JOIN alumno USING(id_alumno) WHERE inscripcion.id_alumno=" .  $p->id_alumno . ")";
	$sql.= " ORDER BY descrip";
	
	return $this->toJson($sql);
  }
  
  
  public function method_inscribir($params, $error) {
  	$p = $params[0];
  	
  	$mysqli = $this->mysqli;
  	
  	$sql = "SELECT * FROM curso WHERE id_curso=" . $p->id_curso;
  	$rs = $mysqli->query($sql);
  	$rowCurso = $rs->fetch_object();
	
	$sql = "INSERT inscripcion SET id_alumno=" . $p->id_alumno . ", id_curso=" . $p->id_curso . ", cant_pagos=0, saldo=" . $rowCurso->total . ", fecha=NOW()";
	$mysqli->query($sql);
	
  	$sql = "UPDATE curso SET cant_inscriptos=cant_inscriptos + 1 WHERE id_curso=" . $p->id_curso;
  	$mysqli->query($sql);
  }
  
  
  public function method_grabar_pago($params, $error) {
  	$p = $params[0];
  	
  	$mysqli = $this->mysqli;
  	
	$sql = "INSERT pago SET id_inscripcion=" . $p->id_inscripcion . ", monto=" . $p->monto . ", fecha=NOW()";
	$mysqli->query($sql);
	
  	$sql = "UPDATE inscripcion SET cant_pagos=cant_pagos + 1, saldo=saldo - " . $p->monto . " WHERE id_inscripcion=" . $p->id_inscripcion;
  	$mysqli->query($sql);
  }
  
  
  public function method_leer_pago($params, $error) {
  	$p = $params[0];
  	
  	$mysqli = $this->mysqli;
	
	$sql = "SELECT * FROM inscripcion WHERE id_inscripcion=" . $p->id_inscripcion;
  	$rs = $mysqli->query($sql);
  	$rowInscripcion = $rs->fetch_object();
  	$rowInscripcion->saldo = (float) $rowInscripcion->saldo;
  	
	$sql = "SELECT * FROM curso WHERE id_curso=" . $rowInscripcion->id_curso;
  	$rs = $mysqli->query($sql);
  	$rowCurso = $rs->fetch_object();
  	$rowCurso->monto_cuota = (float) $rowCurso->monto_cuota;
  	
  	$resultado = new stdClass;
  	$resultado->cant_pagos = $rowInscripcion->cant_pagos + 1;
  	$resultado->monto = ($rowInscripcion->saldo >= $rowCurso->monto_cuota) ? $rowCurso->monto_cuota : $rowInscripcion->saldo;

	return $resultado;
  }
}
?>
