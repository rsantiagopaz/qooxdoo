<?php
session_start();

require('conexion.php');

$link = mysql_connect("$servidor", "$usuario", "$password");
mysql_select_db("$base", $link);
mysql_query("SET NAMES 'utf8'");


switch ($_REQUEST['rutina']) {
case 'imprimir_total_motivos': {
	
$sql = "SELECT * FROM motivo ORDER BY descrip";
$rsMotivo = mysql_query($sql);

	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Total por motivos</title>
	</head>
	<body>
	<table border="0" cellpadding=0 cellspacing=0 width=750 height=1% align="center">
	<tr><td align="center" colspan="6"><big>TOTAL POR MOTIVOS	<?php echo $_REQUEST['desde'] . " - " . $_REQUEST['hasta']  ?></big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<?php
	while ($rowMotivo = mysql_fetch_object($rsMotivo)) {
		$sql = "SELECT COUNT(id_viatico) AS cantidad, SUM(subtotal_viatico2) AS subtotal_viatico2, SUM(pasajes2) AS pasajes2, SUM(combustible2) AS combustible2, SUM(subtotal_alojam2) AS subtotal_alojam2, SUM(otros_gastos2) AS otros_gastos2, id_motivo FROM viatico WHERE id_motivo='" . $rowMotivo->id_motivo . "' AND (fecha_desde2 BETWEEN '" . $_REQUEST['desde'] . "' AND '" . $_REQUEST['hasta'] . "') GROUP BY id_motivo";
		$rsViatico = mysql_query($sql);
		$rowViatico = mysql_fetch_object($rsViatico);
	?>
	<tr><td><big><?php echo $rowMotivo->descrip ?></big></td></tr>
	<tr><td>Cantidad viaticos: <?php echo $rowViatico->cantidad ?></td><td align="right">TOTAL: <?php echo "$ " . number_format((float) $rowViatico->subtotal_viatico2 + (float) $rowViatico->pasajes2 + (float) $rowViatico->combustible2 + (float) $rowViatico->subtotal_alojam2 + (float) $rowViatico->otros_gastos2, 2) ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<?php
	}
	?>
	</table>
	</body>
	</html>
	<?php
		
break;
}


case 'imprimir_total_dependencia': {

$sql = "SELECT COUNT(id_viatico) AS cantidad, SUM(subtotal_viatico2) AS subtotal_viatico2, SUM(pasajes2) AS pasajes2, SUM(combustible2) AS combustible2, SUM(subtotal_alojam2) AS subtotal_alojam2, SUM(otros_gastos2) AS otros_gastos2, TRUE as grupo FROM viatico WHERE organismo_area_id_origen='" . $_REQUEST['organismo_area_id'] . "' AND (fecha_desde2 BETWEEN '" . $_REQUEST['desde'] . "' AND '" . $_REQUEST['hasta'] . "') GROUP BY grupo";
$rsViatico = mysql_query($sql);
$rowViatico = mysql_fetch_object($rsViatico);

$sql = "SELECT CONCAT(organismo_area, ' (', organismo, ')') AS label, organismo_area_id AS model FROM salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id) WHERE organismo_area_id='" . $_REQUEST['organismo_area_id'] . "'";
$rsOrganismo = mysql_query($sql);
$rowOrganismo = mysql_fetch_object($rsOrganismo);

	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Total por dependencia</title>
	</head>
	<body>
	<table border="0" cellpadding=0 cellspacing=0 width=750 height=1% align="center">
	<tr><td align="center" colspan="6"><big>TOTAL POR DEPENDENCIA	<?php echo $_REQUEST['desde'] . " - " . $_REQUEST['hasta']  ?></big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos</big></td></tr>
	<tr><td colspan="6">Dependencia: <?php echo $rowOrganismo->label ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Cantidad viaticos: <?php echo $rowViatico->cantidad ?></td><td align="right">TOTAL: <?php echo "$ " . number_format((float) $rowViatico->subtotal_viatico2 + (float) $rowViatico->pasajes2 + (float) $rowViatico->combustible2 + (float) $rowViatico->subtotal_alojam2 + (float) $rowViatico->otros_gastos2, 2) ?></td></tr>
	</table>
	</body>
	</html>
	<?php
		
break;
}


case 'imprimir_historial_empleado': {

$sql = "SELECT * FROM viatico WHERE id_personal='" . $_REQUEST['id_personal'] . "' AND (fecha_desde2 BETWEEN '" . $_REQUEST['desde'] . "' AND '" . $_REQUEST['hasta'] . "')";
$rsViatico = mysql_query($sql);

$sql = "SELECT _personal.apenom, _personal.dni FROM salud1._personal WHERE id_personal='" . $_REQUEST['id_personal'] . "'";
$rsPersonal = mysql_query($sql);
$rowPersonal = mysql_fetch_object($rsPersonal)

	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Historial de empleado</title>
	</head>
	<body>
	<table border="0" cellpadding=0 cellspacing=0 width=750 height=1% align="center">
	<tr><td align="center" colspan="6"><big>HISTORIAL DE EMPLEADO <?php echo $_REQUEST['desde'] . " - " . $_REQUEST['hasta']  ?></big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos de la persona</big></td></tr>
	<tr><td>Ape. y nom.: <?php echo trim($rowPersonal->apenom) ?></td><td>Nro.doc.: <?php echo $rowPersonal->dni ?></td></tr>
	<?php
	while ($rowViatico = mysql_fetch_object($rsViatico)) {
	?>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>Salida: <?php echo $rowViatico->fecha_desde2 . " " . substr($rowViatico->hora_desde2, 0, 5) ?></td>
			<td>Regreso: <?php echo $rowViatico->fecha_hasta2 . " " . substr($rowViatico->hora_hasta2, 0, 5) ?></td>
			<td align="right">TOTAL: <?php echo "$ " . number_format((float) $rowViatico->subtotal_viatico2 + (float) $rowViatico->pasajes2 + (float) $rowViatico->combustible2 + (float) $rowViatico->subtotal_alojam2 + (float) $rowViatico->otros_gastos2, 2) ?></td>
		</tr>
		<tr><td>Estado: <?php
			if ($rowViatico->estado=="E") echo "Emitido";
			if ($rowViatico->estado=="L") echo "Liquidado";
			if ($rowViatico->estado=="R") echo "Rendido";
			if ($rowViatico->estado=="C") echo "Cerrado";
			if ($rowViatico->estado=="A") echo "Anulado";
		?></td></tr>
		<tr><td><big>Lugar/es de destino</big></td></tr>
		<?php
		$sql = "SELECT CONCAT(localidad, ' (', departamento, ')') AS lugar FROM (salud1._localidades INNER JOIN salud1._departamentos USING(departamento_id)) INNER JOIN viatico_localidad USING(localidad_id) WHERE viatico_localidad.id_viatico='" . $rowViatico->id_viatico . "'";
		$rsLocalidad = mysql_query($sql);
		while ($rowLocalidad = mysql_fetch_object($rsLocalidad)) {
			echo "<tr><td>" . $rowLocalidad->lugar . "</td></tr>";
		}
		?>
		<tr><td>&nbsp;</td></tr>
	<?php
	}
	?>
	</table>
	</body>
	</html>
	<?php
		
break;
}

	
case 'imprimir_rendicion': {
	
$sql = "SELECT viatico.*, _personal.apenom, _personal.dni, motivo.descrip AS motivo FROM (viatico INNER JOIN salud1._personal USING(id_personal)) INNER JOIN motivo USING(id_motivo) WHERE id_viatico='" . $_REQUEST['id_viatico'] . "'";
$rsViatico = mysql_query($sql);
$rowViatico = mysql_fetch_object($rsViatico);

$sql = "SELECT CONCAT(organismo_area, ' (', organismo, ')') AS label, organismo_area_id AS model FROM salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id) WHERE organismo_area_id='" . $rowViatico->organismo_area_id . "'";
$rsOrganismo = mysql_query($sql);
$rowOrganismo = mysql_fetch_object($rsOrganismo);

$sql = "SELECT CONCAT(localidad, ' (', departamento, ')') AS lugar FROM (salud1._localidades INNER JOIN salud1._departamentos USING(departamento_id)) INNER JOIN viatico_localidad USING(localidad_id) WHERE viatico_localidad.id_viatico='" . $rowViatico->id_viatico . "'";
$rsLocalidad = mysql_query($sql);


	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Impresión de comprobante</title>
	</head>
	<body>
	<table border="0" cellpadding=0 cellspacing=0 width=750 height=1% align="center">
	<tr><td align="center" colspan="6"><big>RENDICION DE ANTICIPO</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos del trámite</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Fecha: <?php echo $rowViatico->fecha_tramite ?></td></tr>
	<?php
	if ($rowViatico->tipo_viatico=="A") {
		?>
		<tr><td>Asunto: <?php echo $rowViatico->documentacion_id ?></td><td>Nro. anticipo: <?php echo $rowViatico->nro_viatico ?></td></tr>		
		<?php
	} else {
		?>
		<tr><td>Asunto: <?php echo $rowViatico->documentacion_id ?></td></tr>
		<?php
	}
	?>

	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos de la persona</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Ape. y nom.: <?php echo trim($rowViatico->apenom) ?></td><td>Nro.doc.: <?php echo $rowViatico->dni ?></td></tr>
	<tr><td colspan="6">Dependencia: <?php echo $rowOrganismo->label ?></td></tr>
	<tr><td>Motivo: <?php echo $rowViatico->motivo ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Fecha de servicio</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Salida: <?php echo $rowViatico->fecha_desde2 . " " . substr($rowViatico->hora_desde2, 0, 5) ?></td><td>Regreso: <?php echo $rowViatico->fecha_hasta2 . " " . substr($rowViatico->hora_hasta2, 0, 5) ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos del rendición</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	</table>
	<table border="1" rules="rows" cellpadding=0 cellspacing=0 width=650 height=1% align="center">
	<tr align="right"><td>&nbsp;</td><td>&nbsp;</td><td>Recibido</td><td>Rendido</td></tr>
	<tr><td>Viáticos:</td><td><?php echo $rowViatico->cant_dias_viatico1 . " dia/s a $ " . $rowViatico->monto_diario_viatico . " diarios + $ " . $rowViatico->adicional_viatico1 . " adic." ?></td><td align="right"><?php echo "$ " . $rowViatico->subtotal_viatico1 ?></td><td align="right"><?php echo "$ " . $rowViatico->subtotal_viatico2 ?></td></tr>
	<tr><td>Pasajes:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->pasajes1 ?></td><td align="right"><?php echo "$ " . $rowViatico->pasajes2 ?></td></tr>
	<tr><td>Combustible:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->combustible1 ?></td><td align="right"><?php echo "$ " . $rowViatico->combustible2 ?></td></tr>
	<tr><td>Alojamiento:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->subtotal_alojam1 ?></td><td align="right"><?php echo "$ " . $rowViatico->subtotal_alojam2 ?></td></tr>
	<tr><td>Otros:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->otros_gastos1 ?></td><td align="right"><?php echo "$ " . $rowViatico->otros_gastos2 ?></td></tr>
	<tr>
		<td>&nbsp;</td>
		<td>TOTAL:</td>
		<?php
		$importe_aux = (float) $rowViatico->subtotal_viatico1 + (float) $rowViatico->pasajes1 + (float) $rowViatico->combustible1 + (float) $rowViatico->subtotal_alojam1 + (float) $rowViatico->otros_gastos1
		?>
		<td align="right"><?php echo "$ " . number_format((float) $rowViatico->subtotal_viatico1 + (float) $rowViatico->pasajes1 + (float) $rowViatico->combustible1 + (float) $rowViatico->subtotal_alojam1 + (float) $rowViatico->otros_gastos1, 2) ?></td>
		<td align="right"><?php echo "$ " . number_format((float) $rowViatico->subtotal_viatico2 + (float) $rowViatico->pasajes2 + (float) $rowViatico->combustible2 + (float) $rowViatico->subtotal_alojam2 + (float) $rowViatico->otros_gastos2, 2) ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>DIFERENCIA: $ <?php echo number_format((float) $rowViatico->importe_total - $importe_aux, 2); ?></td>
	</tr>
	</table>
	
	<table border="0" cellpadding=0 cellspacing=0 width=750 height=1% align="center">
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr align="center"><td>......................</td><td>......................</td><td>......................</td></tr>
	<tr align="center"><td>Solicitante</td><td>Responsable</td><td>Operador</td></tr>
	</table>
	</body>
	</html>
	<?php

break;
}
	
	
case 'imprimir_viatico': {
	
$sql = "SELECT viatico.*, _personal.apenom, _personal.dni, motivo.descrip AS motivo FROM (viatico INNER JOIN salud1._personal USING(id_personal)) INNER JOIN motivo USING(id_motivo) WHERE id_viatico='" . $_REQUEST['id_viatico'] . "'";
$rsViatico = mysql_query($sql);
$rowViatico = mysql_fetch_object($rsViatico);

$sql = "SELECT CONCAT(organismo_area, ' (', organismo, ')') AS label, organismo_area_id AS model FROM salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id) WHERE organismo_area_id='" . $rowViatico->organismo_area_id . "'";
$rsOrganismo = mysql_query($sql);
$rowOrganismo = mysql_fetch_object($rsOrganismo);

$sql = "SELECT CONCAT(localidad, ' (', departamento, ')') AS lugar FROM (salud1._localidades INNER JOIN salud1._departamentos USING(departamento_id)) INNER JOIN viatico_localidad USING(localidad_id) WHERE viatico_localidad.id_viatico='" . $rowViatico->id_viatico . "'";
$rsLocalidad = mysql_query($sql);

if ($rowViatico->tipo_viatico=="A") {
	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Impresión de comprobante</title>
	</head>
	<body>
	<table border="0" cellpadding=0 cellspacing=0 width=750 height=1% align="center">
	<tr><td align="center" colspan="6"><big>ANTICIPO</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos del trámite</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Fecha: <?php echo $rowViatico->fecha_tramite ?></td></tr>
	<tr><td>Asunto: <?php echo $rowViatico->documentacion_id ?></td><td>Nro. anticipo: <?php echo $rowViatico->nro_viatico ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos de la persona</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Ape. y nom.: <?php echo trim($rowViatico->apenom) ?></td><td>Nro.doc.: <?php echo $rowViatico->dni ?></td></tr>
	<tr><td colspan="6">Dependencia: <?php echo $rowOrganismo->label ?></td></tr>
	<tr><td>Motivo: <?php echo $rowViatico->motivo ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Lugar/es de destino</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<?php
	while ($rowLocalidad = mysql_fetch_object($rsLocalidad)) {
		echo "<tr><td>" . $rowLocalidad->lugar . "</td></tr>";
	}
	?>
	<tr><td>Salida: <?php echo $rowViatico->fecha_desde2 . " " . substr($rowViatico->hora_desde2, 0, 5) ?></td><td>Regreso: <?php echo $rowViatico->fecha_hasta2 . " " . substr($rowViatico->hora_hasta2, 0, 5) ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos del viático</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	</table>
	<table border="1" rules="rows" cellpadding=0 cellspacing=0 width=650 height=1% align="center">
	<tr><td>Viáticos:</td><td><?php echo $rowViatico->cant_dias_viatico2 . " dia/s a $ " . $rowViatico->monto_diario_viatico . " diarios + $ " . $rowViatico->adicional_viatico2 . " adic." ?></td><td align="right"><?php echo "$ " . $rowViatico->subtotal_viatico2 ?></td></tr>
	<tr><td>Pasajes:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->pasajes2 ?></td></tr>
	<tr><td>Combustible:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->combustible2 ?></td></tr>
	<tr><td>Alojamiento:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->subtotal_alojam2 ?></td></tr>
	<tr><td>Otros:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->otros_gastos2 ?></td></tr>
	<tr><td>&nbsp;</td><td>TOTAL:</td><td align="right"><?php echo "$ " . number_format((float) $rowViatico->subtotal_viatico2 + (float) $rowViatico->pasajes2 + (float) $rowViatico->combustible2 + (float) $rowViatico->subtotal_alojam2 + (float) $rowViatico->otros_gastos2, 2) ?></td></tr>
	</table>
	
	<table border="0" cellpadding=0 cellspacing=0 width=750 height=1% align="center">
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr align="center"><td>......................</td><td>......................</td><td>......................</td></tr>
	<tr align="center"><td>Solicitante</td><td>Responsable</td><td>Operador</td></tr>
	</table>
	</body>
	</html>
	<?php
} else if ($rowViatico->tipo_viatico=="R") {
	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Impresión de comprobante</title>
	</head>
	<body>
	<table border="0" cellpadding=0 cellspacing=0 width=750 height=1% align="center">
	<tr><td align="center" colspan="6"><big>REINTEGRO</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos del trámite</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Fecha: <?php echo $rowViatico->fecha_tramite ?></td></tr>
	<tr><td>Asunto: <?php echo $rowViatico->documentacion_id ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos de la persona</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Ape. y nom.: <?php echo trim($rowViatico->apenom) ?></td><td>Nro.doc.: <?php echo $rowViatico->dni ?></td></tr>
	<tr><td colspan="6">Dependencia: <?php echo $rowOrganismo->label ?></td></tr>
	<tr><td>Motivo: <?php echo $rowViatico->motivo ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Lugar/es de destino</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<?php
	while ($rowLocalidad = mysql_fetch_object($rsLocalidad)) {
		echo "<tr><td>" . $rowLocalidad->lugar . "</td></tr>";
	}
	?>
	<tr><td>Salida: <?php echo $rowViatico->fecha_desde2 . " " . substr($rowViatico->hora_desde2, 0, 5) ?></td><td>Regreso: <?php echo $rowViatico->fecha_hasta2 . " " . substr($rowViatico->hora_hasta2, 0, 5) ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><big>Datos del viático</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	</table>
	<table border="1" rules="rows" cellpadding=0 cellspacing=0 width=650 height=1% align="center">
	<tr><td>Viáticos:</td><td><?php echo $rowViatico->cant_dias_viatico2 . " dia/s a $ " . $rowViatico->monto_diario_viatico . " diarios + $ " . $rowViatico->adicional_viatico2 . " adic." ?></td><td align="right"><?php echo "$ " . $rowViatico->subtotal_viatico2 ?></td></tr>
	<tr><td>Pasajes:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->pasajes2 ?></td></tr>
	<tr><td>Combustible:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->combustible2 ?></td></tr>
	<tr><td>Alojamiento:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->subtotal_alojam2 ?></td></tr>
	<tr><td>Otros:</td><td>&nbsp;</td><td align="right"><?php echo "$ " . $rowViatico->otros_gastos2 ?></td></tr>
	<tr><td>&nbsp;</td><td>TOTAL:</td><td align="right"><?php echo "$ " . number_format((float) $rowViatico->subtotal_viatico2 + (float) $rowViatico->pasajes2 + (float) $rowViatico->combustible2 + (float) $rowViatico->subtotal_alojam2 + (float) $rowViatico->otros_gastos2, 2) ?></td></tr>
	</table>
	
	<table border="0" cellpadding=0 cellspacing=0 width=750 height=1% align="center">
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr align="center"><td>......................</td><td>......................</td><td>......................</td></tr>
	<tr align="center"><td>Solicitante</td><td>Responsable</td><td>Operador</td></tr>
	</table>
	</body>
	</html>
	<?php
}

break;
}


}

?>