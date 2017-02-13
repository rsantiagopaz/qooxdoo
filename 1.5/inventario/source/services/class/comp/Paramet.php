<?php

require("Base.php");

class class_Paramet extends class_Base
{
  function __construct() {
    parent::__construct();
  }
  
  public function method_leer_tipo_alta($params, $error) {
  	$p = $params[0];

	$sql = "SELECT * FROM tipo_alta ORDER BY descrip";
	return $this->toJson($sql);
  }
  
  
  public function method_escribir_tipo_alta($params, $error) {
  	$p = $params[0];
  	
  	$cambios = $p->cambios;
  	
	mysql_query("START TRANSACTION");
	
	foreach ($cambios->altas as $item) {
		$sql="INSERT INTO tipo_alta SET descrip='" . $item->descrip . "'";
		mysql_query($sql);
		if (mysql_errno()) break;
	}
	if (! mysql_errno()) {
		foreach ($cambios->modificados as $item) {
			$sql="UPDATE tipo_alta SET descrip='" . $item->descrip . "' WHERE id_tipo_alta='" . $item->id_tipo_alta . "'";
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
  
  
  public function method_leer_tipo_baja($params, $error) {
  	$p = $params[0];

	$sql = "SELECT * FROM tipo_baja ORDER BY descrip";
	return $this->toJson($sql);
  }
  
  
  public function method_escribir_tipo_baja($params, $error) {
  	$p = $params[0];
  	
  	$cambios = $p->cambios;
  	
	mysql_query("START TRANSACTION");
	
	foreach ($cambios->altas as $item) {
		$sql="INSERT INTO tipo_baja SET descrip='" . $item->descrip . "'";
		mysql_query($sql);
		if (mysql_errno()) break;
	}
	if (! mysql_errno()) {
		foreach ($cambios->modificados as $item) {
			$sql="UPDATE tipo_baja SET descrip='" . $item->descrip . "' WHERE id_tipo_baja='" . $item->id_tipo_baja . "'";
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
  
  
  public function method_leer_tipo_bien($params, $error) {
  	$p = $params[0];

	$sql = "SELECT * FROM tipo_bien ORDER BY descrip";
	return $this->toJson($sql);
  }
  
  
  public function method_escribir_tipo_bien($params, $error) {
  	$p = $params[0];
  	
  	$cambios = $p->cambios;
  	
	mysql_query("START TRANSACTION");
	
	foreach ($cambios->altas as $item) {
		$sql="INSERT INTO tipo_bien SET descrip='" . $item->descrip . "'";
		mysql_query($sql);
		if (mysql_errno()) break;
	}
	if (! mysql_errno()) {
		foreach ($cambios->modificados as $item) {
			$sql="UPDATE tipo_bien SET descrip='" . $item->descrip . "' WHERE id_tipo_bien='" . $item->id_tipo_bien . "'";
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

}

?>
