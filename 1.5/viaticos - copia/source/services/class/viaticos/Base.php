<?php
class class_Base
{
	function __construct() {
		require('conexion.php');
		
		$link = mysql_connect("$servidor", "$usuario", "$password");
		mysql_select_db("$base", $link);
		mysql_query("SET NAMES 'utf8'");
		set_time_limit(120);
	}
	
  public function auditoria() {
  	
  }
	
  public function toJson($paramet, &$opciones = null) {
	if (is_string($paramet)) {
		$cadena = strtoupper(substr(trim($paramet), 0, 6));
		if ($cadena=="INSERT" || $cadena=="SELECT") {
			$paramet = @mysql_query($paramet);
			if (mysql_errno() > 0) {
				return mysql_errno() . " " . mysql_error() . "\n";
			} else if ($cadena=="INSERT"){ 
				//$nodo=$xml->addChild("insert_id", mysql_insert_id());
			} else {
				return $this->toJson($paramet, $opciones);
			}
		}
	} else if (is_resource($paramet)) {
		$rows = array();
		if (is_null($opciones)) {
			while ($row = mysql_fetch_object($paramet)) {
				$rows[] = $row;
			}
		} else {
			while ($row = mysql_fetch_object($paramet)) {
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
			$rs = mysql_query("SHOW COLUMNS FROM " . $tabla);
			while ($row = mysql_fetch_assoc($rs)) {
				$aux = new stdClass;
				$aux->Null = $row['Null'];
				$campos[$tabla][$row['Field']] = $aux;
			}
		}
	}
	foreach($model as $key => $value) {
		if ($chequear) {
			if (!is_null($campos[$tabla][$key])) {
				if (is_null($value)) {
					if ($campos[$tabla][$key]->Null=="YES") $set[] = $key . "=NULL";
				} else {
					$set[] = $key . "='" . $value . "'";
				}
			}			
		} else {
			if (is_null($value)) {
				$set[] = $key . "=NULL";
			} else {
				$set[] = $key . "='" . $value . "'";
			}
		}
	}
	return implode(", ", $set);
  }
  
  
  public function getRealIP() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
	return $_SERVER['REMOTE_ADDR'];
  }
}

?>