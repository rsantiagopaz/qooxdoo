<?php
session_start();

include ('rutinas.php');
include ('auditoria.php');
include ('conexion.php');

switch ($_REQUEST['rutina']) {
case 'buscarOP': {

//	if ($_REQUEST['tipo']=='1') $rs = mysql_query("SELECT razon_social, cuit, monto_dest_as_op, nro_asunto_afectacion, nro_op from (((razones_sociales INNER JOIN proveedores ON razones_sociales.cod_proveedor=proveedores.cod_proveedor) INNER JOIN destinatarios_as_op ON razones_sociales.cod_razon_social=destinatarios_as_op.ID_razon_social) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE razon_social LIKE '%" . $_REQUEST['descrip'] . "%' ORDER BY razon_social"); print mysql_error();
	if ($_REQUEST['tipo']=='1') {
		$sql="(SELECT razon_social AS descrip, cuit AS cuitdni, monto_dest_as_op, nro_asunto_pago, nro_asunto_afectacion, ordenes_de_pago.ID_orden_pago, nro_op from (((razones_sociales INNER JOIN proveedores ON razones_sociales.cod_proveedor=proveedores.cod_proveedor) INNER JOIN destinatarios_as_op ON razones_sociales.cod_razon_social=destinatarios_as_op.ID_razon_social) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE razon_social LIKE '%" . $_REQUEST['descrip'] . "%')";
		$sql.=" UNION ALL ";
		$sql.="(SELECT nombre AS descrip, cuit AS cuitdni, monto_dest_as_op, nro_asunto_pago, nro_asunto_afectacion, ordenes_de_pago.ID_orden_pago, nro_op from (((ongs INNER JOIN beneficiarios ON ongs.ID_ong=beneficiarios.ID_ong) INNER JOIN destinatarios_as_op ON beneficiarios.ID_beneficiario=destinatarios_as_op.ID_beneficiario) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE nombre LIKE '%" . $_REQUEST['descrip'] . "%')";
		$sql.=" UNION ALL ";
		$sql.="(SELECT nombre AS descrip, dni AS cuitdni, monto_dest_as_op, nro_asunto_pago, nro_asunto_afectacion, ordenes_de_pago.ID_orden_pago, nro_op from (((pacientes INNER JOIN beneficiarios ON pacientes.ID_paciente=beneficiarios.ID_paciente) INNER JOIN destinatarios_as_op ON beneficiarios.ID_beneficiario=destinatarios_as_op.ID_beneficiario) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE nombre LIKE '%" . $_REQUEST['descrip'] . "%')";
		$sql.=" UNION ALL ";


		$sql.="(SELECT descrip, '', monto_dest_as_op, nro_asunto_pago, nro_asunto_afectacion, ordenes_de_pago.ID_orden_pago, nro_op from ((beneficiarios INNER JOIN destinatarios_as_op ON beneficiarios.ID_beneficiario=destinatarios_as_op.ID_beneficiario) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE ID_tipo_destinat=3 AND descrip LIKE '%" . $_REQUEST['descrip'] . "%')";


		
		$sql.=" ORDER BY descrip";
	}
//	if ($_REQUEST['tipo']=='2') $rs = mysql_query("SELECT razon_social, cuit, monto_dest_as_op, nro_asunto_afectacion, nro_op from (((razones_sociales INNER JOIN proveedores ON razones_sociales.cod_proveedor=proveedores.cod_proveedor) INNER JOIN destinatarios_as_op ON razones_sociales.cod_razon_social=destinatarios_as_op.ID_razon_social) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE cuit LIKE '%" . $_REQUEST['descrip'] . "%' ORDER BY cuit"); print mysql_error();
	if ($_REQUEST['tipo']=='2') {
		$sql="(SELECT razon_social AS descrip, cuit AS cuitdni, monto_dest_as_op, nro_asunto_pago, nro_asunto_afectacion, ordenes_de_pago.ID_orden_pago, nro_op from (((razones_sociales INNER JOIN proveedores ON razones_sociales.cod_proveedor=proveedores.cod_proveedor) INNER JOIN destinatarios_as_op ON razones_sociales.cod_razon_social=destinatarios_as_op.ID_razon_social) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE cuit LIKE '%" . $_REQUEST['descrip'] . "%')";
		$sql.=" UNION ALL ";
		$sql.="(SELECT nombre AS descrip, cuit AS cuitdni, monto_dest_as_op, nro_asunto_pago, nro_asunto_afectacion, ordenes_de_pago.ID_orden_pago, nro_op from (((ongs INNER JOIN beneficiarios ON ongs.ID_ong=beneficiarios.ID_ong) INNER JOIN destinatarios_as_op ON beneficiarios.ID_beneficiario=destinatarios_as_op.ID_beneficiario) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE cuit LIKE '%" . $_REQUEST['descrip'] . "%')";
		$sql.=" UNION ALL ";
		$sql.="(SELECT nombre AS descrip, dni AS cuitdni, monto_dest_as_op, nro_asunto_pago, nro_asunto_afectacion, ordenes_de_pago.ID_orden_pago, nro_op from (((pacientes INNER JOIN beneficiarios ON pacientes.ID_paciente=beneficiarios.ID_paciente) INNER JOIN destinatarios_as_op ON beneficiarios.ID_beneficiario=destinatarios_as_op.ID_beneficiario) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE dni LIKE '%" . $_REQUEST['descrip'] . "%')";
		$sql.=" ORDER BY cuitdni";
	}
//	if ($_REQUEST['tipo']=='3') $rs = mysql_query("SELECT razon_social, cuit, monto_dest_as_op, nro_asunto_afectacion, nro_op from (((razones_sociales INNER JOIN proveedores ON razones_sociales.cod_proveedor=proveedores.cod_proveedor) INNER JOIN destinatarios_as_op ON razones_sociales.cod_razon_social=destinatarios_as_op.ID_razon_social) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE nro_asunto_afectacion LIKE '%" . $_REQUEST['descrip'] . "%' ORDER BY nro_asunto_afectacion"); print mysql_error();
	if ($_REQUEST['tipo']=='3') {
		$sql="(SELECT razon_social AS descrip, cuit AS cuitdni, monto_dest_as_op, nro_asunto_pago, nro_asunto_afectacion, ordenes_de_pago.ID_orden_pago, nro_op from (((razones_sociales INNER JOIN proveedores ON razones_sociales.cod_proveedor=proveedores.cod_proveedor) INNER JOIN destinatarios_as_op ON razones_sociales.cod_razon_social=destinatarios_as_op.ID_razon_social) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE nro_asunto_pago LIKE '%" . $_REQUEST['descrip'] . "%')";
		$sql.=" UNION ALL ";
		$sql.="(SELECT nombre AS descrip, cuit AS cuitdni, monto_dest_as_op, nro_asunto_pago, nro_asunto_afectacion, ordenes_de_pago.ID_orden_pago, nro_op from (((ongs INNER JOIN beneficiarios ON ongs.ID_ong=beneficiarios.ID_ong) INNER JOIN destinatarios_as_op ON beneficiarios.ID_beneficiario=destinatarios_as_op.ID_beneficiario) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE nro_asunto_pago LIKE '%" . $_REQUEST['descrip'] . "%')";
		$sql.=" UNION ALL ";
		$sql.="(SELECT nombre AS descrip, dni AS cuitdni, monto_dest_as_op, nro_asunto_pago, nro_asunto_afectacion, ordenes_de_pago.ID_orden_pago, nro_op from (((pacientes INNER JOIN beneficiarios ON pacientes.ID_paciente=beneficiarios.ID_paciente) INNER JOIN destinatarios_as_op ON beneficiarios.ID_beneficiario=destinatarios_as_op.ID_beneficiario) INNER JOIN asuntos_op ON destinatarios_as_op.ID_asunto_op=asuntos_op.ID_asunto_op) INNER JOIN ordenes_de_pago ON asuntos_op.ID_orden_pago=ordenes_de_pago.ID_orden_pago WHERE nro_asunto_pago LIKE '%" . $_REQUEST['descrip'] . "%')";
		$sql.=" ORDER BY nro_asunto_pago";
	}

	$rs = mysql_query($sql); print mysql_error();

	$op='<table border="1" cellpadding=0 cellspacing=2 width=100% style="font-size:10; font-weight: bold;"  bgcolor="#ffffff">';
	$op.='<tr align="center" style="font-size:12; font-weight: bold;" bgcolor="#e4fbf3"><td>Destinatario</td><td>CUIT/DNI</td><td>N� asunto</td><td>Monto</td><td>N� OP</td><td>&nbsp;</td></tr>';

	while ($row = mysql_fetch_array($rs)) {
//		$op.='<tr><td>' . $row['descrip'] . '</td><td>' . $row['cuitdni'] . '</td><td>' . $row['nro_asunto_afectacion'] . '</td>' . FormatearCeldaNumerica(number_format($row['monto_dest_as_op'], 2), ' align="right"', ' style="color : #ea0000;" align="right"', '$ ', '') . '<td>' . $row['nro_op'] . '</td><td align="center"><input type="button" value="Seleccionar" onclick="document.frmOP.txtNroop.value=' . "'" . $row['nro_op'] . "'" . '; Windows.close(' . "'" . 'VentanaBuscarOP' . "'" . '); document.frmOP.btnVer.focus()" /></td></tr>';
		$op.='<tr><td>' . $row['descrip'] . '</td><td>' . $row['cuitdni'] . '&nbsp;</td><td>' . $row['nro_asunto_pago'] . '&nbsp;</td>' . FormatearCeldaNumerica(number_format($row['monto_dest_as_op'], 2), ' align="right"', ' style="color : #ea0000;" align="right"', '$ ', '') . '<td>' . $row['nro_op'] . '</td><td align="center"><input type="button" id="btnSeleccionar" name="btnSeleccionar" value="Seleccionar" onclick="VentanaBuscarOP_btnSeleccionar_click(' . $row['ID_orden_pago'] . ", '" . $row['nro_op'] . "', '" . $row['nro_asunto_pago'] . "', '" . $row['nro_asunto_afectacion'] . "'" . ');" /></td></tr>';
	}
	$op.='</table>';

	header('Content-Type: text/html; charset=iso-8859-1');
	echo $op;

break;
}



case 'ventana_buscarOP': {

header('Content-Type: text/html; charset=iso-8859-1');
?>
<form name="frmBuscarOP" onsubmit="return false;">
<table border="0" cellpadding="0" cellspacing="2" width="99%" align="center" style="font-size:10; font-weight: bold;"  bgcolor="#e4fbf3">
<tr>
	<td>Buscar:&nbsp;&nbsp;<input id="txtBuscarOP" name="txtBuscarOP" type="text" value="" onkeyup="txtBuscarOP_keyup();" onblur="this.value=Trim(this.value);" autocomplete="off" maxlength="50" />&nbsp;&nbsp; por: &nbsp;&nbsp;
		<select size="1" name="cboBuscarOP" id="cboBuscarOP" type="text" onchange="txtBuscarOP_keyup();" style="width:120px">
			<option value="1">Destinatario</option>
			<option value="2">CUIT/DNI</option>
			<option value="3">N� asunto</option>
		</select>
	</td>
</tr>
<tr><td colspan="6" align="center" bgcolor="#8cc4ac" style="color : #800000;"><big>&nbsp;</big></td></tr>
<tr><td colspan="6"><div id="divBuscarOP">&nbsp;</div></td></tr>
</table>
</form>
<?php


break;
}



case 'agregar_beneficiario': {

mysql_db_query("$base", "START TRANSACTION");

if ($_REQUEST['particular']=='0') {
	$auxSQL = "INSERT INTO beneficiarios VALUES (0, 'T', 0, 0, '" . $_REQUEST['descrip'] . "')";
	mysql_db_query("$base", $auxSQL);
} else if ($_REQUEST['particular']=='true') {
	$auxSQL = "INSERT INTO pacientes VALUES (0, '" . $_REQUEST['descrip'] . "', '" . $_REQUEST['cuit'] . "', '" . $_REQUEST['domicilio'] . "')";
	mysql_db_query("$base", $auxSQL);
	if (! mysql_errno()) {
		$auxSQL = "INSERT INTO beneficiarios VALUES (0, 'P', " . mysql_insert_id() . ", 0, '')";
		mysql_db_query("$base", $auxSQL);
	}	
} else {
	$auxSQL = "INSERT INTO ongs VALUES (0, '" . $_REQUEST['descrip'] . "', '" . $_REQUEST['cuit'] . "', '" . $_REQUEST['domicilio'] . "')";
	mysql_db_query("$base", $auxSQL);
	if (! mysql_errno()) {
		$auxSQL = "INSERT INTO beneficiarios VALUES (0, 'O', 0, " . mysql_insert_id() . ", '')";
		mysql_db_query("$base", $auxSQL);
	}
}

if (! mysql_errno()) {
	mysql_db_query("$base", "COMMIT");
} else {
	print mysql_error();
	mysql_db_query("$base", "ROLLBACK");
	print mysql_error();
}

break;
}



case 'ventana_nuevo_partic': {

header('Content-Type: text/html; charset=iso-8859-1');
?>
<form name="frmNB" onsubmit="return false;">
<table border="0" cellpadding="0" cellspacing="2" width="99%" align="center" style="font-size:10; font-weight: bold;"  bgcolor="#e4fbf3">
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td><td>Nombre: </td><td><input id="txtDescrip" name="txtDescrip" type="text" value="" onblur="this.value=Trim(this.value);" autocomplete="off" maxlength="50" style="width:300px" /></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2" align="center"><input id="btnAceptar" name="btnAceptar" type="button" onclick="VentanaNuevoPartic_btnAceptar_click();" value="Aceptar" /></td>
		<td colspan="2" align="center"><input id="btnCancelar" name="btnCancelar" type="button" onclick="Windows.close('VentanaNuevoBenef');" value="Cancelar" /></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>
</form>
<?php

break;
}



case 'ventana_nuevo_benef': {

header('Content-Type: text/html; charset=iso-8859-1');
?>
<form name="frmNB" onsubmit="return false;">
<table border="0" cellpadding="0" cellspacing="2" width="99%" align="center" style="font-size:10; font-weight: bold;"  bgcolor="#e4fbf3">
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td><td>Nombre: </td><td><input id="txtDescrip" name="txtDescrip" type="text" value="" onblur="this.value=Trim(this.value);" autocomplete="off" maxlength="50" style="width:300px" /></td></tr>
	<tr><td>&nbsp;</td><td>DNI/CUIT: </td><td><input id="txtCuit" name="txtCuit" type="text" value="" onkeypress="return ValidarTecla(event, '0123456789');" onblur="this.value=Trim(this.value);" autocomplete="off" maxlength="15" /></td></tr>
	<tr><td>&nbsp;</td><td>Domicilio: </td><td><input id="txtDomicilio" name="txtDomicilio" type="text" value="" onblur="this.value=Trim(this.value);" autocomplete="off" maxlength="50" style="width:300px" /></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>
		<input name="optTipo" type="radio" id="optTipo" value="1" checked="true"> Particular</input>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input name="optTipo" type="radio" id="optTipo" value="2"> O.N.G.</input>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2" align="center"><input id="btnAceptar" name="btnAceptar" type="button" onclick="VentanaNuevoBenef_btnAceptar_click();" value="Aceptar" /></td>
		<td colspan="2" align="center"><input id="btnCancelar" name="btnCancelar" type="button" onclick="Windows.close('VentanaNuevoBenef');" value="Cancelar" /></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>
</form>
<?php

break;
}



case 'pagar_item': {

$xml = DOMDocument::loadXML($_REQUEST['xml']);
$rs = XMLtoArray($xml->getElementsByTagName('retenciones_realizadas'));

mysql_db_query("$base", "START TRANSACTION");

$auxSQL = "INSERT INTO movimientos VALUES (0, '" . ($_REQUEST['cheque']=='true' ? '1' : '6') . "', '" . $_REQUEST['nro_cheque'] . "', " . $_REQUEST['cod_cta_cte'] . ", " . $_REQUEST['ID_dest_as_op'] . ", " . $_REQUEST['monto'] . ", '', 'N', '" . date('Y-m-d') . "', '" . date('Y-m-d') . "', 0, '0000-00-00')";
//$auxSQL = "INSERT INTO cheques VALUES (0, '" . $_REQUEST['nro_cheque'] . "', " . $_REQUEST['cod_cta_cte'] . ", " . $_REQUEST['monto'] . ", '', 'N', '" . date('Y-m-d') . "', '')";
mysql_db_query("$base", $auxSQL);
if (! mysql_errno()) {
	$auxSQL="UPDATE destinatarios_as_op SET ID_movimiento=" . mysql_insert_id() . " WHERE ID_dest_as_op=" . $_REQUEST['ID_dest_as_op'];
	mysql_db_query("$base", $auxSQL);
	if (! mysql_errno()) {
		for ($i = 0; $i < count($rs['retenciones_realizadas']); $i++) {
			if ($rs['retenciones_realizadas'][$i]['tipo_rendicion']=='I') {
				$auxSQL = "INSERT INTO movimientos VALUES (0, '1', '" . $rs['retenciones_realizadas'][$i]['nro_cheque'] . "', " . $_REQUEST['cod_cta_cte'] . ", " . $_REQUEST['ID_dest_as_op'] . ", " . $rs['retenciones_realizadas'][$i]['importe'] . ", '', 'N', '" . date('Y-m-d') . "', '" . date('Y-m-d') . "', 0, '0000-00-00')";
				//$auxSQL = "INSERT INTO cheques VALUES (0, '" . $rs['retenciones_realizadas'][$i]['nro_cheque'] . "', " . $_REQUEST['cod_cta_cte'] . ", " . $rs['retenciones_realizadas'][$i]['importe'] . ", '', 'N', '" . date('Y-m-d') . "', '')";
				mysql_db_query("$base", $auxSQL);
			}
			if (! mysql_errno()) {
				$auxSQL = "INSERT INTO retenciones_realizadas VALUES (0, " . $rs['retenciones_realizadas'][$i]['cod_tipo_retencion'] . ", " . $_REQUEST['ID_dest_as_op'] . ", '" . $rs['retenciones_realizadas'][$i]['tipo_rendicion'] . "', " . $rs['retenciones_realizadas'][$i]['porcentaje'] . ", " . $rs['retenciones_realizadas'][$i]['monto_fijo'] . ", " . $rs['retenciones_realizadas'][$i]['monto_desde'] . ", '" . $rs['retenciones_realizadas'][$i]['modo_aplicacion'] . "', " . $rs['retenciones_realizadas'][$i]['importe'] . ", 0, " . ($rs['retenciones_realizadas'][$i]['tipo_rendicion']=='I' ? mysql_insert_id() : "0") . ", 0)";
				mysql_db_query("$base", $auxSQL);
				if (mysql_errno()) break;
			} else break;
		}
	}
}
if (! mysql_errno()) {
	mysql_db_query("$base", "COMMIT");
//	header('Content-Type: text/html; charset=iso-8859-1');
//	echo MostrarItems($_REQUEST['ID_orden_pago']);
	
	
	$xml='<?xml version="1.0" encoding="ISO-8859-1" ?>';
	$xml.="<row>";
	$xml.=MostrarItems($_REQUEST['ID_orden_pago']);
	$xml.="</row>";
	
	header('Content-Type: text/xml');
	echo $xml;
} else {
	print mysql_error();
	mysql_db_query("$base", "ROLLBACK");
	print mysql_error();
}

break;
}



case 'ventana_pagar_item': {

$rsDestinatarios_as_op = mysql_query("SELECT * FROM destinatarios_as_op WHERE ID_dest_as_op=" . $_REQUEST['ID_dest_as_op']); print mysql_error();
$rowDestinatarios_as_op = mysql_fetch_array($rsDestinatarios_as_op);

$rsTipo_retenciones = mysql_query("SELECT tipo_retenciones.* FROM tipo_retenciones, razon_social_retencion WHERE razon_social_retencion.cod_razon_social = '" . $rowDestinatarios_as_op['ID_razon_social'] . "' AND razon_social_retencion.cod_tipo_retencion = tipo_retenciones.cod_tipo_retencion"); print mysql_error();
$rsCuentas_corrientes = mysql_query("SELECT cod_cta_cte, nro_cta_cte, nombre FROM cuentas_corrientes INNER JOIN bancos ON cuentas_corrientes.cod_banco=bancos.cod_banco ORDER BY nombre, nro_cta_cte"); print mysql_error();

$lblMontoFinal=(float) $rowDestinatarios_as_op['monto_dest_as_op'];
while ($row = mysql_fetch_array($rsTipo_retenciones)) {
	$activado=(bool)($rowDestinatarios_as_op['monto_dest_as_op'] >= $row['monto_desde']);
	if ($activado) {
		$importe=($row['modo_aplicacion'] == 'F' ? $row['monto_fijo'] : ($rowDestinatarios_as_op['monto_dest_as_op'] * $row['porcentaje']) / 100);
		$lblMontoFinal=$lblMontoFinal-$importe;
	}
}
if (mysql_num_rows($rsTipo_retenciones) > 0) mysql_data_seek($rsTipo_retenciones, 0);


header('Content-Type: text/html; charset=iso-8859-1');
?>
<form name="frmPI" onsubmit="return false;">
<table border="0" cellpadding="0" cellspacing="2" width="99%" align="center" style="font-size:10; font-weight: bold;"  bgcolor="#e4fbf3">
	<tr><td colspan="6" align="center" bgcolor="#8cc4ac" style="color : #800000;"><big>REGISTRACI�N DE PAGO</big></td></tr>
	<tr>
		<td>Monto neto ($): </td><td style="font-size:12; font-weight: normal; text-decoration:underline"><?php echo number_format($rowDestinatarios_as_op['monto_dest_as_op'], 2); ?></td>
		<td>Monto final ($): </td>
		<td style="font-size:12; font-weight: normal;"><div id="lblMontoFinal" style="text-decoration:underline"><?php echo number_format($lblMontoFinal, 2); ?></div></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>Cta. Cte.: </td>
		<td>
			<select size="1" name="cboCtacte" id="cboCtacte" type="text" style="width:200px" tabindex="1">
<?php
			while ($row = mysql_fetch_array($rsCuentas_corrientes)) {
				echo '<option value="' . $row[cod_cta_cte] . '">' . $row[nombre] . ' - ' . $row[nro_cta_cte] . '</option>';
			}
?>
			</select>
		</td>
		<td><input name="optTipo" type="radio" id="optTipo" value="1" checked="true" tabindex="2">N� cheque: </input></td>
		<td>
		<input id="txtNrocheque" name="txtNrocheque" type="text" value="" tabindex="4" onblur="this.value=Trim(this.value);" autocomplete="off" maxlength="50" />
		</td>
	</tr>
	<tr><td>&nbsp;</td><td>&nbsp;</td><td><input name="optTipo" type="radio" id="optTipo" value="2" tabindex="3">N� deb.aut.: </input></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td colspan="4">
		<table border="1" cellpadding=0 cellspacing=2 width=100% style="font-size:10; font-weight: bold;"  bgcolor="#ffffff">
		<tr><td colspan="4" align="center" bgcolor="#8cc4ac" style="color : #800000;"><big>RETENCIONES</big></td></tr>
		<tr align="center" style="font-size:12; font-weight: bold;" bgcolor="#e4fbf3"><td>Descripci�n</td><td>Importe</td><td>N� cheque</td></tr>
<?php
			while ($row = mysql_fetch_array($rsTipo_retenciones)) {
				$cod_tipo_retencion=$row['cod_tipo_retencion'];
				$activado=(bool)($rowDestinatarios_as_op['monto_dest_as_op'] >= $row['monto_desde']);
				$importe=round(($row['modo_aplicacion'] == 'F' ? $row['monto_fijo'] : ($rowDestinatarios_as_op['monto_dest_as_op'] * $row['porcentaje']) / 100), 2);
				echo '<tr id="tr' . $cod_tipo_retencion . '"' . ($activado ? ' bgcolor="#FFFFFF"' : ' bgcolor="#CCCCCC"') . '>';
				echo '<td>';

				echo '<input type="checkbox" name="chk" id="chk' . $cod_tipo_retencion . '" value="' . $cod_tipo_retencion . '" onclick="chk_click(this);" ' . ($activado ? 'checked="true" ' : '') . '/>&nbsp;&nbsp;' . $row['descripcion'];
				echo '<input name="hidTipo_rendicion" id="hidTipo_rendicion' . $cod_tipo_retencion . '" type="hidden" value="' . $row['tipo_rendicion'] . '" />';
				echo '<input name="hidPorcentaje" id="hidPorcentaje' . $cod_tipo_retencion . '" type="hidden" value="' . $row['porcentaje'] . '" />';
				echo '<input name="hidMonto_fijo" id="hidMonto_fijo' . $cod_tipo_retencion . '" type="hidden" value="' . $row['monto_fijo'] . '" />';
				echo '<input name="hidMonto_desde" id="hidMonto_desde' . $cod_tipo_retencion . '" type="hidden" value="' . $row['monto_desde'] . '" />';
				echo '<input name="hidModo_aplicacion" id="hidModo_aplicacion' . $cod_tipo_retencion . '" type="hidden" value="' . $row['modo_aplicacion'] . '" />';
				echo '<input name="hidImporte" id="hidImporte' . $cod_tipo_retencion . '" type="hidden" value="' . $importe . '" />';

				echo  '</td>';
				echo '<td align="right"><div id="lbl' . $cod_tipo_retencion . '">$ ' . number_format($importe, 2) . '</div></td>';
				echo '<td>' . (($row['tipo_rendicion'] == 'I') ? '<input ' . ($activado ? '' : 'disabled="true"') . ' name="txt" id="txt' . $cod_tipo_retencion . '" type="text" value="" onblur="this.value=Trim(this.value);" autocomplete="off" />' : '&nbsp;') . '</td>';
				echo '</tr>';
			}
?>
		</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td colspan="2" align="center"><input id="btnAceptar" name="btnAceptar" type="button" onclick="VentanaPagarItem_btnAceptar_click();" value="Aceptar" /></td>
		<td colspan="2" align="center"><input id="btnCancelar" name="btnCancelar" type="button" onclick="Windows.close('VentanaPagarItem');" value="Cancelar" /></td>
	</tr>
	<tr><td>&nbsp;</td></tr>
</table>
</form>
<?php

break;
}


case 'eliminar_item': {

mysql_db_query("$base", "START TRANSACTION");
if (! mysql_errno()) {
	$auxSQL="UPDATE asuntos_op SET monto_as_op=monto_as_op - " . $_REQUEST['monto_dest_as_op'] . " WHERE ID_asunto_op=" . $_REQUEST['ID_asunto_op'];
	mysql_db_query("$base", $auxSQL);
	if (! mysql_errno()) {
		$auxSQL="DELETE FROM destinatarios_as_op WHERE ID_dest_as_op=" . $_REQUEST['ID_dest_as_op'];
		mysql_db_query("$base", $auxSQL);
		if (! mysql_errno()) {
			$auxSQL="DELETE FROM movimientos WHERE ID_dest_as_op=" . $_REQUEST['ID_dest_as_op'];
			mysql_db_query("$base", $auxSQL);
			if (! mysql_errno()) {
				$auxSQL="DELETE FROM retenciones_realizadas WHERE ID_dest_as_op=" . $_REQUEST['ID_dest_as_op'];
				mysql_db_query("$base", $auxSQL);
				if (! mysql_errno()) {
					mysql_db_query("$base", "COMMIT");
				}
			}
		}
	}
}
if (! mysql_errno()) {
	$xml='<?xml version="1.0" encoding="ISO-8859-1" ?>';
	$xml.="<row>";
	$xml.=MostrarItems($_REQUEST['ID_orden_pago']);
	$xml.="</row>";

	header('Content-Type: text/xml');
	echo $xml;
} else {
	print mysql_error();
	mysql_db_query("$base", "ROLLBACK");
	print mysql_error();
}

break;
}



case 'agregar_item': {

mysql_db_query("$base", "START TRANSACTION");

if ($_REQUEST['ID_orden_pago'] == "0") {
	$auxSQL = "INSERT INTO ordenes_de_pago VALUES (0, '" . $_REQUEST['nro_op'] . "', '" . $_REQUEST['fecha'] . "', " . $_REQUEST['monto_op'] . ", 0)";
	mysql_db_query("$base", $auxSQL);

	if (! mysql_errno()) $_REQUEST['ID_orden_pago']=mysql_insert_id();
}

if (! mysql_errno()) {
	$rs = mysql_query("SELECT ID_asunto_op FROM asuntos_op WHERE nro_asunto_pago='" . $_REQUEST['nro_asunto_pago'] . "' AND nro_asunto_afectacion='" . $_REQUEST['nro_asunto_afectacion'] . "' AND ID_orden_pago=" . $_REQUEST['ID_orden_pago']); print mysql_error();
	if (mysql_num_rows($rs)>0) {
		$row = mysql_fetch_array($rs);
		$ID_asunto_op = $row['ID_asunto_op'];
	} else {
		$auxSQL = "INSERT INTO asuntos_op VALUES (0, '" . $_REQUEST['nro_asunto_pago'] . "', '" . $_REQUEST['nro_asunto_afectacion'] . "', " . $_REQUEST['ID_orden_pago'] . ", 0, 0)";
		mysql_db_query("$base", $auxSQL);
		if (! mysql_errno()) $ID_asunto_op = mysql_insert_id();
	}
	
	if (! mysql_errno()) {
		$auxSQL = "INSERT INTO destinatarios_as_op VALUES (0, " . $ID_asunto_op . ", " . $_REQUEST['ID_tipo_destinat'] . ", " . $_REQUEST['cod_razon_social'] . ", " . $_REQUEST['ID_beneficiario'] . ", " . $_REQUEST['monto_dest_as_op'] . ", 0)";
		mysql_db_query("$base", $auxSQL);
		if (! mysql_errno()) {
			$auxSQL="UPDATE asuntos_op SET monto_as_op=monto_as_op + " . $_REQUEST['monto_dest_as_op'] . " WHERE ID_asunto_op=" . $ID_asunto_op;
			mysql_db_query("$base", $auxSQL);
		}
	}
}

if (! mysql_errno()) {
	mysql_db_query("$base", "COMMIT");
	
	$xml='<?xml version="1.0" encoding="ISO-8859-1" ?>';
	$xml.="<row>";
	$xml.=MostrarItems($_REQUEST['ID_orden_pago']);
	$xml.="<ID_orden_pago>" . $_REQUEST['ID_orden_pago'] . "</ID_orden_pago>";
	$xml.="</row>";
	
	header('Content-Type: text/xml');
	echo $xml;
	
//	header('Content-Type: text/html; charset=iso-8859-1');
//	echo MostrarItems($_REQUEST['ID_orden_pago']);

} else {
	print mysql_error();
	mysql_db_query("$base", "ROLLBACK");
}

break;
}



case 'traer_destinatarios': {

if ($_REQUEST['tipo_destinat']==1) {
	if ($_REQUEST['por_descrip']=='true') {
		$rs = mysql_query("SELECT cod_razon_social as ID, CONCAT(razon_social, ' (' , cuit, ')') as descrip FROM razones_sociales INNER JOIN proveedores ON razones_sociales.cod_proveedor=proveedores.cod_proveedor WHERE razon_social LIKE '%" . $_REQUEST['descrip'] . "%' ORDER BY razon_social"); print mysql_error();
	} else {
		$rs = mysql_query("SELECT cod_razon_social as ID, CONCAT('(' , cuit, ') ', razon_social) as descrip FROM razones_sociales INNER JOIN proveedores ON razones_sociales.cod_proveedor=proveedores.cod_proveedor WHERE cuit LIKE '%" . $_REQUEST['descrip'] . "%' ORDER BY cuit"); print mysql_error();
	}
} else if ($_REQUEST['tipo_destinat']==2) {
	if ($_REQUEST['por_descrip']=='true') {
		$rs = mysql_query("(SELECT ID_beneficiario as ID, CONCAT(ongs.nombre, ' (', ongs.cuit, ')') as descrip FROM beneficiarios INNER JOIN ongs ON beneficiarios.ID_ong=ongs.ID_ong WHERE ongs.nombre LIKE '%" . $_REQUEST['descrip'] . "%') UNION ALL (SELECT ID_beneficiario as ID, CONCAT(pacientes.nombre, ' (', pacientes.dni, ')') as descrip FROM beneficiarios INNER JOIN pacientes ON beneficiarios.ID_paciente=pacientes.ID_paciente WHERE pacientes.nombre LIKE '%" . $_REQUEST['descrip'] . "%') ORDER BY descrip"); print mysql_error();
	} else {
		$rs = mysql_query("(SELECT ID_beneficiario as ID, CONCAT('(', ongs.cuit, ') ', ongs.nombre) as descrip FROM beneficiarios INNER JOIN ongs ON beneficiarios.ID_ong=ongs.ID_ong WHERE ongs.cuit LIKE '%" . $_REQUEST['descrip'] . "%') UNION ALL (SELECT ID_beneficiario as ID, CONCAT('(', pacientes.dni, ') ', pacientes.nombre) as descrip FROM beneficiarios INNER JOIN pacientes ON beneficiarios.ID_paciente=pacientes.ID_paciente WHERE pacientes.dni LIKE '%" . $_REQUEST['descrip'] . "%') ORDER BY descrip"); print mysql_error();	
	}
} else {
	$rs = mysql_query("SELECT ID_beneficiario as ID, descrip FROM beneficiarios WHERE descrip LIKE '%" . $_REQUEST['descrip'] . "%' ORDER BY descrip"); print mysql_error();
}

if (mysql_num_rows($rs)>0) {
	$xml='<?xml version="1.0" encoding="ISO-8859-1" ?>';
	$xml.="<destinatarios>";
	while ($row = mysql_fetch_array($rs)) {
		$xml.="<row_destinatario>";
		$xml.="<ID>" . $row['ID'] . "</ID>";
		$xml.="<descrip>" . $row['descrip'] . "</descrip>";
		$xml.="</row_destinatario>";
	}
	$xml.="</destinatarios>";
	
	header('Content-Type: text/xml');
	echo $xml;

} else {
	header('Content-Type: text/html');
	echo "false";
}

break;
}


case 'asunto1': {
include('conexsalud1.php');

$rs = mysql_query("SELECT documentacion_tmp_iniciador, documentacion_tmp_asunto FROM salud1.001_documentaciones WHERE documentacion_id='" . $_REQUEST['nro_asunto'] . "'"); print mysql_error();

if (mysql_num_rows($rs)>0) {
	$row = mysql_fetch_array($rs);

	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
	echo "<row>";
	echo "<iniciador>" . $row['documentacion_tmp_iniciador'] . "</iniciador>";
	echo "<asunto>" . $row['documentacion_tmp_asunto'] . "</asunto>";
	echo "</row>";
} else {
	header('Content-Type: text/html');
	echo "false";
}

break;
}


case 'asunto2': {

$rs = mysql_query("SELECT nro_asunto FROM afectaciones WHERE nro_asunto='" . $_REQUEST['nro_asunto'] . "'"); print mysql_error();
if (mysql_num_rows($rs)>0) {
	include('conexsalud1.php');
	$rs = mysql_query("SELECT documentacion_tmp_iniciador, documentacion_tmp_asunto FROM salud1.001_documentaciones WHERE documentacion_id='" . $_REQUEST['nro_asunto'] . "'"); print mysql_error();
	
	if (mysql_num_rows($rs)>0) {
		$row = mysql_fetch_array($rs);
	
		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
		echo "<row>";
		echo "<iniciador>" . $row['documentacion_tmp_iniciador'] . "</iniciador>";
		echo "<asunto>" . $row['documentacion_tmp_asunto'] . "</asunto>";
		echo "</row>";
	} else {
		header('Content-Type: text/html');
		echo "false";
	}
} else {
	header('Content-Type: text/html');
	echo "false";
}

break;
}


case 'ver': {

if ($_REQUEST['ID_orden_pago']=='0') {
	$rsOP = mysql_query("SELECT * FROM ordenes_de_pago WHERE nro_op='" . $_REQUEST['nro_op'] . "'"); print mysql_error();
} else {
	$rsOP = mysql_query("SELECT * FROM ordenes_de_pago WHERE ID_orden_pago='" . $_REQUEST['ID_orden_pago'] . "'"); print mysql_error();
}
if (mysql_num_rows($rsOP)>0) {
	$rowOP = mysql_fetch_array($rsOP);
	
	$xml='<?xml version="1.0" encoding="ISO-8859-1" ?>';
	$xml.="<row>";
	$xml.=MostrarItems($rowOP['ID_orden_pago']);
	$xml.="<idop>" . $rowOP['ID_orden_pago'] . "</idop>";
	$xml.="<monto_op>" . $rowOP['monto_op'] . "</monto_op>";
	$xml.="<fecha>" . DMYYYY($rowOP['fecha']) . "</fecha>";
	$xml.="</row>";
	
	header('Content-Type: text/xml');
	echo $xml;

} else {
	header('Content-Type: text/html');
	echo "false";
}

break;
}

}



function MostrarItems($ID_orden_pago) {
	$rsDest = mysql_query("SELECT destinatarios_as_op.ID_dest_as_op, destinatarios_as_op.ID_asunto_op, nro_asunto_afectacion, ID_tipo_destinat, ID_beneficiario, razon_social, fecha_pago, monto_dest_as_op, movimientos.ID_movimiento, nro_cheque, conciliado FROM ((asuntos_op INNER JOIN destinatarios_as_op ON asuntos_op.ID_asunto_op=destinatarios_as_op.ID_asunto_op) LEFT JOIN razones_sociales ON destinatarios_as_op.ID_razon_social=razones_sociales.cod_razon_social) LEFT JOIN movimientos ON destinatarios_as_op.ID_movimiento=movimientos.ID_movimiento WHERE ID_orden_pago=" . $ID_orden_pago); print mysql_error();

//	$rsDest = mysql_query("SELECT ID_dest_as_op, destinatarios_as_op.ID_asunto_op, nro_asunto_afectacion, ID_tipo_destinat, ID_beneficiario, razon_social, fecha_pago, monto_dest_as_op, destinatarios_as_op.cod_cheque, nro_cheque, conciliado FROM ((asuntos_op INNER JOIN destinatarios_as_op ON asuntos_op.ID_asunto_op=destinatarios_as_op.ID_asunto_op) LEFT JOIN razones_sociales ON destinatarios_as_op.ID_razon_social=razones_sociales.cod_razon_social) LEFT JOIN cheques ON destinatarios_as_op.cod_cheque=cheques.cod_cheque WHERE ID_orden_pago=" . $ID_orden_pago); print mysql_error();

	$pagos='<table border="1" cellpadding=0 cellspacing=2 width=100% style="font-size:10; font-weight: bold;"  bgcolor="#ffffff">';
	$pagos.='<tr><td colspan="7" align="center" bgcolor="#8cc4ac" style="color : #800000;"><big>ITEMS</big></td></tr>';
	$pagos.='<tr align="center" style="font-size:12; font-weight: bold;" bgcolor="#e4fbf3"><td>N� Asunto</td><td>Destinatario</td><td>Monto</td><td>Fecha pago</td><td>N� cheque</td><td>P</td><td>E</td></tr>';

	$suma_items=0;
	while ($rowDest = mysql_fetch_array($rsDest)) {
		if ($rowDest['ID_tipo_destinat']==2 || $rowDest['ID_tipo_destinat']==3) {
			$rsBenef = mysql_query("SELECT tipo_beneficiario, beneficiarios.descrip as particular, pacientes.nombre AS paciente, ongs.nombre AS ong FROM (beneficiarios LEFT JOIN pacientes ON beneficiarios.ID_paciente=pacientes.ID_paciente) LEFT JOIN ongs ON beneficiarios.ID_ong=ongs.ID_ong WHERE ID_beneficiario=" . $rowDest['ID_beneficiario']); print mysql_error();
			$rowBenef = mysql_fetch_array($rsBenef);
		}
		$pagos.='<tr>';
		$pagos.='<td>' . $rowDest['nro_asunto_afectacion'] . '&nbsp;</td>';
		$pagos.='<td>' . ($rowDest['ID_tipo_destinat']==1 ? $rowDest['razon_social'] : ($rowBenef['tipo_beneficiario']=='P' ? $rowBenef['paciente'] : ($rowBenef['tipo_beneficiario']=='0' ? $rowBenef['ong'] : $rowBenef['particular']))) . '&nbsp;</td>';
		$suma_items+=(float) $rowDest['monto_dest_as_op'];
		$pagos.=FormatearCeldaNumerica(number_format((float) $rowDest['monto_dest_as_op'], 2), ' align="right"', ' style="color : #ea0000;" align="right"', '$ ', '');
		if ($rowDest['conciliado'] == "S" || $rowDest['conciliado'] == "N") {
			$pagos.='<td align="center">' . DMYYYY($rowDest['fecha_pago']) . '&nbsp;</td>';
			$pagos.='<td align="center">' . $rowDest['nro_cheque'] . '&nbsp;</td>';
			$pagos.='<td>&nbsp;</td>';
			if ($rowDest['conciliado'] == "S") {
				$pagos.='<td>&nbsp;</td>';
			} else {
				$pagos.='<td align="center"><a onclick="EliminarItem(' . $rowDest['ID_dest_as_op'] . ', ' . $rowDest['ID_asunto_op'] . ', ' . $rowDest['monto_dest_as_op'] . ');"><img src="imagenes/borrar.png" border="0" alt="Eliminar modificacion" style="cursor : pointer;" /></a></td>';
			}
		} else {
			$pagos.='<td>&nbsp;</td>';
			$pagos.='<td>&nbsp;</td>';
			$pagos.='<td align="center"><a onclick="VentanaPagarItem(' . $rowDest['ID_dest_as_op'] . ');"><img src="imagenes/pagar.jpg" border="0" alt="Confirmar modificacion" style="cursor : pointer;" /></a></td>';
			$pagos.='<td align="center"><a onclick="EliminarItem(' . $rowDest['ID_dest_as_op'] . ', ' . $rowDest['ID_asunto_op'] . ', ' . $rowDest['monto_dest_as_op'] . ');"><img src="imagenes/borrar.png" border="0" alt="Eliminar modificacion" style="cursor : pointer;" /></a></td>';	
		}
		$pagos.='</tr>';
	}
	$pagos.='</table>';

	return "<items>" . htmlspecialchars($pagos) . "</items><suma_items>" . $suma_items . "</suma_items>";
}

?>
