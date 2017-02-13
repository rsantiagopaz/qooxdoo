<?php
session_start();

require("Conexion.php");

set_time_limit(0);

$link1 = mysql_connect($servidor, $usuario, $password);
mysql_select_db($base, $link1);
mysql_query("SET NAMES 'utf8'", $link1);

switch ($_REQUEST['rutina'])
{
case "salida_vehiculo" : {

	$sql = "SELECT * FROM entsal INNER JOIN vehiculo USING(id_vehiculo) WHERE id_entsal=" . $_REQUEST['id_entsal'];
	$rsEntsal = mysql_query($sql);
	$rowEntsal = mysql_fetch_object($rsEntsal);
	
	$sql = "SELECT";
	$sql.= "  CONCAT(_organismos_areas.organismo_area, ' (', CASE WHEN _organismos_areas.organismo_area_tipo_id='E' THEN _departamentos.departamento ELSE _organismos.organismo END, ')') AS label";
	$sql.= " FROM (salud1._organismos_areas INNER JOIN salud1._organismos USING(organismo_id)) LEFT JOIN salud1._departamentos ON _organismos_areas.organismo_areas_id_departamento=_departamentos.codigo_indec";
	$sql.= " WHERE _organismos_areas.organismo_area_id='" . $rowEntsal->organismo_area_id . "'";
	
	$rsDependencia = mysql_query($sql);
	if (mysql_num_rows($rsDependencia) > 0) {
		$rowDependencia = mysql_fetch_object($rsDependencia);
		$rowEntsal->dependencia = $rowDependencia->label;
	} else {
		$rowEntsal->dependencia = "";
	}
	
	
	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title>Comprobante</title>
	</head>
	<body>
	<input type="submit" value="Imprimir" onClick="window.print();"/>
	<table border="0" cellpadding="0" cellspacing="0" width="800" align="center">
	<tr><td align="center" colspan="6"><big><b>Parque Automotor</b></big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td align="center" colspan="6"><big><b>Ministerio de Salud y Desarrollo Social</b></big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><b>Salida de vehiculo: <?php echo $rowEntsal->nro_patente . "  " . $rowEntsal->marca; ?></b></td><td>Fecha: <?php echo $rowEntsal->f_sal; ?></td></tr>
	<tr><td colspan="20">Dependencia: <?php echo $rowEntsal->dependencia; ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Usuario: <?php echo $_SESSION['usuario']; ?></td><td>Responsable: <?php echo $rowEntsal->resp_sal; ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	
	<?php
	$sql = "SELECT movimiento.*, razones_sociales.razon_social AS taller FROM movimiento INNER JOIN `019`.razones_sociales USING(cod_razon_social) WHERE id_entsal=" . $rowEntsal->id_entsal . " ORDER BY f_ent DESC";
	$rsMovimiento = mysql_query($sql);
	
	while ($rowMovimiento = mysql_fetch_object($rsMovimiento)) {
		?>
		<tr><td colspan="20"><?php echo $rowMovimiento->taller; ?></td></tr>
		<tr><td colspan="20">
		<table border="1" rules="all" cellpadding="1" cellspacing="0" width="99%" align="center">
		<tr><th>Tipo reparación</th><th align="right">Costo</th><th align="right">Cant.</th><th align="right">Total</th></tr>
		<?php
		//$sql = "SELECT * FROM reparacion WHERE id_movimiento=" . $rowMovimiento->id_movimiento;
		$sql = "SELECT reparacion.*, tipo_reparacion.descrip AS tipo_reparacion FROM reparacion INNER JOIN tipo_reparacion USING(id_tipo_reparacion) WHERE id_movimiento=" . $rowMovimiento->id_movimiento;
		$rsReparacion = mysql_query($sql);
		
		while ($rowReparacion = mysql_fetch_object($rsReparacion)) {
			?>
			<tr>
			<td><?php echo $rowReparacion->tipo_reparacion; ?></td>
			<td align="right"><?php echo number_format($rowReparacion->costo, 2, ",", "."); ?></td>
			<td align="right"><?php echo $rowReparacion->cantidad; ?></td>
			<td align="right"><?php echo number_format($rowReparacion->total, 2, ",", "."); ?></td>
			</tr>
			<?php
		}
		?>
		</table>
		<tr><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td></tr>
		<?php
	}
	?>

	<tr><td>_____________________________</td><td>_____________________________</td></tr>
	<tr><td>Firma usuario</td><td>Firma responsable</td></tr>
	</td></tr>
	</table>
	</body>
	</html>
	<?php
	
break;
}


case "recibo" : {
	
	$sql = "SELECT cliente.*, usuario.descrip AS usuario, pago.importe, pago.tipo_pago FROM ((pago INNER JOIN usuario USING(id_usuario)) INNER JOIN operacion_cliente USING(id_operacion)) INNER JOIN cliente USING(id_cliente) WHERE pago.id_pago=" . $_REQUEST['id_pago'];
	$rs = mysql_query($sql);
	$row = mysql_fetch_object($rs);
	$apenom = $row->apellido . ", " . $row->nombre . " - " . $row->dni . " - " . $row->cuit;

	$tipo_pago = array();
	$tipo_pago["E"] = "Efectivo";
	$tipo_pago["C"] = "Tarjeta crédito";
	$tipo_pago["D"] = "Tarjeta débito";
	$tipo_pago["Q"] = "Cheque";
	$tipo_pago["T"] = "Transferencia";
	
?>

<table cellpadding="5" cellspacing="0" width="700px" border="1" >
<tr align="center">
	<td>
	<table width="100%" border="0">
	<tr>
		<td width="33%">
			<table width="100%" border="0" style="font-size:14; font-weight: bold;" align="center">
				<tr>
				<td rowspan="3"><img src="logo.png" border="0"></td>
				<td><b>Almundo</b></td>
				</tr>
				<!-- 
				<tr style="font-size:8; font-weight:normal;"><td>DirecciÃ³n: Rivadavia NÂº 1018</td></tr>
				<tr style="font-size:8; font-weight:normal;"><td>Tel: 0385 421-8866/ 0385 424-1917</td></tr>
				 -->
				<tr style="font-size:8; font-weight:normal;"><td>Mitre 372</td><td>C.P. 4200</td></tr>
				<tr style="font-size:8; font-weight:normal;"><td>(0385) 4221527</td><td>mario.avila@almundo.com</td></tr>
			</table>
			</td>
		<td width="33%" align="center"><table align="center" cellpadding="5" border="1"><tr><td align="center"><big>RECIBO DE PAGO</big><br /><font style="font-size:8;">Sin firma y sello no posee validez.</font></td></tr></table></td>
		<td width="33%" align="right">Fecha: <?php echo date('Y-m-d'); ?> <br /><br /> <?php echo $config->sucursal; ?> - 00000000<?php echo $_REQUEST["id_ctacte_pago"]; ?> <br /> Usuario: <?php echo $row->usuario; ?></td>
	</tr>
	<tr><td colspan="3"><hr></td></tr>
	<tr><td colspan="3" align="center"><b><u>Cliente</u>:</b> <?php echo $apenom; ?><br /><br />
		Recibimos del mismo la suma de: <b>$<?php echo number_format((float) $row->importe, 2, ',', '.'); ?></b> 
		</td>
	</tr>
	<tr><td colspan="3"><hr></td></tr>
	<tr><td colspan="3">
	<b><u>FORMA DE PAGO</u>:</b> 
		<?php echo $tipo_pago[$row->tipo_pago] . ": $" . number_format((float) $row->importe, 2, ',', '.'); ?>
	</td></tr>
	<tr><td colspan="3"><hr></td></tr>
	<!--
	<tr><td align="right">SALDO ANTERIOR: </td><td align="right">$<?php echo number_format($SALDO_ANTERIOR, 2, ',', '.'); ?></td><td></td></tr>
	<tr><td align="right">PAGO A CUENTA: </td><td align="right">$<?php echo number_format($rPAGO->monto, 2, ',', '.'); ?></td><td></td></tr>
	<tr><td></td><td><hr /></td><td></td></tr>
	<tr><td align="right">SALDO ACTUAL: </td><td align="right">$<?php echo number_format($SALDO, 2, ',', '.'); ?></td><td></td></tr>
	-->
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td colspan="3" align="right"><br />____________________________<br /> Firma y Sello &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
	</table>
	</td>
	</tr>
</table>
<script>
//window.print();
</script>		

<?php

	
break;
}
	
	
case "comprobante_entrega" : {
	
	$sql = "SELECT entrega_lugar.descrip AS lugar, entrega.descrip, entrega.fecha FROM entrega INNER JOIN entrega_lugar USING(id_entrega_lugar) WHERE id_entrega=" . $_REQUEST['id_entrega'];
	$rsEntrega = mysql_query($sql);
	$rowEntrega = mysql_fetch_object($rsEntrega);

	$sql = "SELECT producto.descrip, stock.lote, stock.f_vencimiento, entrega_item.cantidad FROM ((entrega INNER JOIN entrega_item USING(id_entrega)) INNER JOIN stock USING(id_stock)) INNER JOIN producto ON stock.id_producto = producto.id_producto WHERE entrega.id_entrega=" . $_REQUEST['id_entrega'] . " ORDER BY descrip, f_vencimiento";
	$rsEntrega_item = mysql_query($sql);
	
	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title>Comprobante de entrega</title>
	</head>
	<body>
	<table border="0" width="700" align="center">
	<tr><td align="center" colspan="2"><big>COMPROBANTE DE ENTREGA</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Destino: </td><td><?php echo $rowEntrega->lugar; ?></td></tr>
	<tr><td>Descripción: </td><td><?php echo $rowEntrega->descrip; ?></td></tr>
	<tr><td>Fecha: </td><td><?php echo $rowEntrega->fecha; ?></td></tr>
	<tr><td>&nbsp;</td></tr>
	
	<tr><td colspan="20">
	<table border="1" rules="all" width="100%" align="center">
	<tr><th>Producto</th><th>Lote</th><th>F.vencimiento</th><th align="right">Cantidad</th></tr>
	<?php
	while ($rowEntrega_item = mysql_fetch_object($rsEntrega_item)) {
		?>
		<tr><td><?php echo $rowEntrega_item->descrip; ?></td><td><?php echo $rowEntrega_item->lote; ?></td><td><?php echo $rowEntrega_item->f_vencimiento; ?></td><td align="right"><?php echo $rowEntrega_item->cantidad; ?></td></tr>
		<?php
	}
	?>
	</table>
	</td></tr>
	</table>
	</body>
	</html>
	<?php
	
break;
}
	
	
case "consumo_producto" : {

	$sql = "SELECT producto.id_producto, producto.descrip, SUM(entrega_item.cantidad) AS cantidad FROM (entrega INNER JOIN entrega_item USING(id_entrega)) INNER JOIN producto USING(id_producto) WHERE entrega.fecha BETWEEN '" . $_REQUEST['desde'] . "' AND '" . $_REQUEST['hasta'] . "' GROUP BY id_producto ORDER BY descrip";
	$rs = mysql_query($sql);
	
	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title>Listado Consumo x Producto</title>
	</head>
	<body>
	<table border="0" width="700" align="center">
	<tr><td align="center" colspan="6"><big><?php echo date('Y-m-d') ?> - LISTADO CONSUMO x PRODUCTO</big></td></tr>
	<tr><td align="center" colspan="6"><big>(período <?php echo substr($_REQUEST['desde'], 0, 10) . " / " . substr($_REQUEST['hasta'], 0, 10) ?>)</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	
	<tr><td colspan="20">
	<table border="1" rules="all" width="100%" align="center">
	<tr><th>Producto</th><th align="right">Cantidad</th></tr>
	<?php
	while ($row = mysql_fetch_array($rs)) {
		?>
		<tr><td><?php echo $row['descrip']; ?></td><td align="right"><?php echo $row['cantidad']; ?></td></tr>
		<?php
	}
	?>
	</table>
	</td></tr>
	</table>
	</body>
	</html>
	<?php
	
break;
}


case "stock" : {

	$sql = "SELECT producto.*, SUM(stock.stock) AS cantidad FROM producto LEFT JOIN stock USING(id_producto) GROUP BY id_producto ORDER BY descrip";
	$rs = mysql_query($sql);
	
	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title>Listado de Stock</title>
	</head>
	<body>
	<table border="0" width="700" align="center">
	<tr><td align="center" colspan="6"><big><?php echo date('Y-m-d') ?> - LISTADO DE STOCK</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	
	<tr><td colspan="20">
	<table border="1" rules="all" width="100%" align="center">
	<tr><th>Producto</th><th align="right">Pto.reposición</th><th align="right">Stock</th></tr>
	<?php
	while ($row = mysql_fetch_array($rs)) {
		?>
		<tr><td><?php echo $row['descrip']; ?></td><td align="right"><?php echo $row['pto_reposicion']; ?></td><td align="right"><?php echo (($row['cantidad'] == null) ? 0: $row['cantidad']); ?></td></tr>
		<?php
	}
	?>
	</table>
	</td></tr>
	</table>
	</body>
	</html>
	<?php
	
break;
}


case "producto_falta" : {

	$sql = "SELECT producto.*, SUM(stock.stock) AS cantidad FROM producto LEFT JOIN stock USING(id_producto) GROUP BY id_producto HAVING cantidad <= pto_reposicion OR ISNULL(cantidad) ORDER BY descrip";
	$rs = mysql_query($sql);
	
	?>
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<title>Listado Producto en Falta</title>
	</head>
	<body>
	<table border="0" width="700" align="center">
	<tr><td align="center" colspan="6"><big><?php echo date('Y-m-d') ?> - LISTADO PRODUCTO EN FALTA</big></td></tr>
	<tr><td>&nbsp;</td></tr>
	
	<tr><td colspan="20">
	<table border="1" rules="all" width="100%" align="center">
	<tr><th>Producto</th><th align="right">Pto.reposición</th><th align="right">Stock</th></tr>
	<?php
	while ($row = mysql_fetch_array($rs)) {
		?>
		<tr><td><?php echo $row['descrip']; ?></td><td align="right"><?php echo $row['pto_reposicion']; ?></td><td align="right"><?php echo (($row['cantidad'] == null) ? 0: $row['cantidad']); ?></td></tr>
		<?php
	}
	?>
	</table>
	</td></tr>
	</table>
	</body>
	</html>
	<?php
	
break;
}

}

?>