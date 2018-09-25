<?php
class class_Base
{
	protected $mysqli;
	protected $rowParamet;
	
	function __construct() {
		require('Conexion.php');
		
		$this->mysqli = new mysqli("$servidor", "$usuario", "$password", "$base");
		$this->mysqli->query("SET NAMES 'utf8'");
		
		//$this->method_leer_paramet(null, null);
	}
	
	
  public function method_leer_paramet($params, $error) {
	$sql = "SELECT * FROM paramet";
	$rsParamet = $this->mysqli->query($sql);
	//$this->rowParamet = mysql_fetch_object($rsParamet);
	//$this->rowParamet->nro_sucursal = (int) $this->rowParamet->nro_sucursal;
	//$this->rowParamet->nro_remito = (int) $this->rowParamet->nro_remito;

	return $this->rowParamet;
  }
  
  
  public function toJson($paramet, &$opciones = null) {
	if (is_string($paramet)) {
		$cadena = strtoupper(substr(trim($paramet), 0, 6));
		if ($cadena=="INSERT" || $cadena=="SELECT") {
			$paramet = $this->mysqli->query($paramet);
			if ($this->mysqli->errno > 0) {
				return $this->mysqli->errno . " " . $this->mysqli->error . "\n";
			} else if ($cadena=="INSERT"){ 
				//$nodo=$xml->addChild("insert_id", mysql_insert_id());
			} else {
				return $this->toJson($paramet, $opciones);
			}
		}
	} else if (is_a($paramet, "MySQLi_Result")) {
		$result = $paramet;
		$rows = array();
		if (is_null($opciones)) {
			while ($row = $result->fetch_object()) {
				$rows[] = $row;
			}
		} else {
			while ($row = $result->fetch_object()) {
				foreach($opciones as $key => $value) {
					if ($value=="int") {
						$row->$key = (int) $row->$key;
					} else if ($value=="float") {
						$row->$key = (float) $row->$key;
					} else if ($value=="bool") {
						$row->$key = (bool) $row->$key;
					} else {
						$value($row, $key);
					}
				}

				$rows[] = $row;
			}
		}
		return $rows;
	}
  }
  

  public function prepararCampos(&$model, $tabla = null) {
  	static $campos = array();
	$set = array();
	$chequear = false;
	if (!is_null($tabla)) {
		$chequear = true;
		if (is_null($campos[$tabla])) {
			$campos[$tabla] = array();
			$rs = $this->mysqli->query("SHOW COLUMNS FROM " . $tabla);
			while ($row = $rs->fetch_assoc()) {
				$campos[$tabla][$row['Field']] = true;
			}
		}
	}
	foreach($model as $key => $value) {
		if ($chequear) {
			if (!is_null($campos[$tabla][$key])) {
				$set[] = $key . "=" . ((is_null($value)) ? "NULL" : "'" . $value . "'");
			}			
		} else {
			$set[] = $key . "=" . ((is_null($value)) ? "NULL" : "'" . $value . "'");
		}
	}
	return implode(", ", $set);
  }
}

?>