<?php

require("Base.php");

class class_Parametros extends class_Base
{
  public function method_buscar_paramed($params, $error) {
  	$p = $params[0];
  	
	$sql = "SELECT * FROM paramed WHERE descrip LIKE '%" . $p->texto . "%' ORDER BY descrip";
	return $this->toJson($sql);
  }
}

?>