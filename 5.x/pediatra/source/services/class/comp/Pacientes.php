<?php

require("Base.php");

class class_Pacientes extends class_Base
{
  function __construct() {
    parent::__construct();
  }
  
  
  public function method_buscar_paciente($params, $error) {
  	$p = $params[0];
  	
	$sql = "SELECT id_paciente, CONCAT(apellido, ', ', nombre) AS apenom, nro_doc, domicilio FROM paciente";
	if (is_numeric($p->texto)) {
		$sql.= " WHERE nro_doc LIKE '%" . $p->texto . "%'";
	} else {
		$sql.= " WHERE apellido LIKE '%" . $p->texto . "%' OR nombre LIKE '%" . $p->texto . "%'";
	}
	$sql.= " ORDER BY apenom";
	
	return $this->toJson($sql);
  }
  
  
  public function method_alta_modifica_visita($params, $error) {
	$p = $params[0];

	$id_visita = $p->model->id_visita;

	$set = $this->prepararCampos($p->model, "visita");
	
	mysql_query("START TRANSACTION");

	if ($id_visita == "0") {
		$sql = "INSERT visita SET " . $set;
		mysql_query($sql);
		$id_visita = mysql_insert_id();
	} else {
		$sql = "UPDATE visita SET " . $set . " WHERE id_visita=" . $id_visita;
		mysql_query($sql);

		$sql = "DELETE FROM visita_paramed WHERE id_visita=" . $id_visita;
		mysql_query($sql);
	}
	
	foreach ($p->paramed as $item) {
		$sql = "INSERT visita_paramed SET id_visita=" . $id_visita . ", id_paramed=" . $item->id_paramed;
		mysql_query($sql);
	}
	
	mysql_query("COMMIT");
	
	return $id_visita;
  }
  
  
  public function method_leer_visitas($params, $error) {
  	$p = $params[0];
  	
  	$opciones = new stdClass;
  	$opciones->per_enc = "float";
  	$opciones->talla = "float";
  	$opciones->peso = "float";
  	
	$sql = "SELECT * FROM visita WHERE id_paciente=" . $p->id_paciente;
	return $this->toJson($sql, $opciones);
  }
  
  
  public function method_leer_visita($params, $error) {
  	$p = $params[0];
  	
  	$resultado = new stdClass;
  	
  	$opciones = new stdClass;
  	$opciones->per_enc = "float";
  	$opciones->talla = "float";
  	$opciones->peso = "float";
  	
	$sql = "SELECT * FROM visita WHERE id_visita=" . $p->id_visita;
	$resultado->visita = $this->toJson($sql, $opciones);
	$resultado->visita = $resultado->visita[0];
	
	
	
	$sql = "SELECT * FROM visita_paramed WHERE id_visita=" . $p->id_visita;
	$resultado->paramed = $this->toJson($sql);
	
	return $resultado;
  }
}

?>