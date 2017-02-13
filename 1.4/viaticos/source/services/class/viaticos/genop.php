<?php
//session_start();

//if (($_SESSION['usuario'] == "") OR ($_SESSION['password'] == "") OR ($_SESSION['oficina'] == ""))
//{
/*	echo "<script>parent.location='index.php';</script>"; */
//} else {
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Generacion de la Orden de Pago</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="en-us" />

	<script type="text/javascript" src="javascripts/prototype.js"> </script>
	<script type="text/javascript" src="javascripts/window.js"> </script>
	<script type="text/javascript" src="javascripts/window_ext.js"> </script>
	<script type="text/javascript" src="rutinas.js"></script>
	<script type="text/javascript" src="genop.js"></script>

	<link href="themes/default.css" rel="stylesheet" type="text/css" >	 </link>
	<link href="themes/mac_os_x.css" rel="stylesheet" type="text/css" >	 </link>
	<link href="themes/alert.css" rel="stylesheet" type="text/css" >	 </link>
	<link href="themes/lighting.css" rel="stylesheet" type="text/css" >	 </link>
	
	<style>
	#DialogConfirm .myButtonClass {
		width:70px;
	}
	</style>
</head>
<body>
<table id="tblMain" border="1" cellpadding=0 cellspacing=0 width=750 height=1% align="center">
<tr>
	<td align="center">
		<form name="frmOP" onsubmit="return false;">
		<table border="0" cellpadding=0 cellspacing=2 width=99% style="font-size:10; font-weight: bold;"  bgcolor="#e4fbf3">

		<tr><td colspan="6" align="center" class="titulo1" ><big>ORDEN DE PAGO</big></td></tr>

		<tr>
			<td>Nro OP:</td>
			<td>
			<input id="txtNroop1" name="txtNroop1" type="text" value="" tabindex="1" autocomplete="off" maxlength="2" onkeypress="return ValidarTecla(event, '0123456789');" onblur="this.value=Trim(this.value);" style="width:20px" /> -
			<input id="txtNroop2" name="txtNroop2" type="text" value="" tabindex="2" autocomplete="off" maxlength="7" onkeypress="return ValidarTecla(event, '0123456789');" onblur="this.value=(isNaN(parseInt(this.value)) || parseInt(this.value)==0 ? '0' : Trim(String(parseInt(this.value))));" style="width:60px" /> -
			<input id="txtNroop3" name="txtNroop3" type="text" value="" tabindex="3" autocomplete="off" maxlength="4" onkeypress="return ValidarTecla(event, '0123456789');" onblur="this.value=(isNaN(parseInt(this.value)) || parseInt(this.value)==0 ? '0' : Trim(String(parseInt(this.value))));" style="width:40px" />
			<input id="btnVer" name="btnVer" type="button" value="Ver" tabindex="4" />
			</td>
			<td>Monto ($): <input readonly="true" id="txtMontoop" name="txtMontoop" type="text" value="" onkeypress="return ValidarTecla(event, '0123456789.');" onblur="this.value=(isNaN(parseFloat(this.value)) ? '' : FNumero(this.value, false));" tabindex="5" autocomplete="off" maxlength="15" /></td>
			<td>Fecha (dd/mm/aaaa): <input readonly="true" id="txtFecha" name="txtFecha" type="text" value="" onkeypress="return ValidarTecla(event, '0123456789/');" onblur="this.value=(! ValidarFecha(this.value) ? '' : ValidarFecha(this.value));" tabindex="6" autocomplete="off" maxlength="12" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td align="left"><input id="btnBuscarOP" name="btnBuscarOP" type="button" value="Buscar..." tabindex="7" /></td>
		</tr>
		<tr><td>&nbsp;</td></tr>

		<tr><td colspan="6" align="center" class="titulo1" ><big>EXPEDIENTE PARA EL PAGO</big></td></tr>
		
		<tr>
			<td>Nº Asunto:</td>
			<td><input disabled="true" id="txtNroasunto1" name="txtNroasunto" type="text" value="" tabindex="8" autocomplete="off" maxlength="12" /></td>
			<td colspan="4">Iniciador:<label id="lblIniciador1" style="font-size:12; font-weight: normal; text-decoration:underline" /></td>
		</tr>
		<tr>
			<td>Asunto:</td>
			<td colspan="5" style="font-size:12; font-weight: normal;"><div id="lblAsunto1" style="text-decoration:underline" /></td>
		</tr>

		<tr><td>&nbsp;</td></tr>

		<tr><td colspan="6" align="center" class="titulo1" ><big>EXPEDIENTE DONDE SE LO AFECTO</big></td></tr>
		<tr><td><input name="chkNocontrolar" type="checkbox" id="chkNocontrolar" disabled="true" tabindex="10"> No controlar</input></td></tr>
		<tr>
			<td>Nº Asunto:</td>
			<td><input disabled="true" id="txtNroasunto2" name="txtNroasunto" type="text" value="" tabindex="10" autocomplete="off" maxlength="12" /></td>

			<td colspan="4">Iniciador: <label id="lblIniciador2" style="font-size:12; font-weight: normal; text-decoration:underline" /></td>
		</tr>
		<tr>
			<td>Asunto:</td>
			<td colspan="5" style="font-size:12; font-weight: normal;"><div id="lblAsunto2" style="text-decoration:underline" /></td>
		</tr>

		<tr><td>&nbsp;</td></tr>

		<tr><td colspan="6" align="center" class="titulo1" ><big>DESTINATARIO DEL PAGO</big></td></tr>

		<tr>
			<td>Tipo:</td>
			<td>
				<select size="1" name="cboDestinatario" id="cboDestinatario" type="text" tabindex="13" style="width:120px">
					<option value="1">Proveedor</option>
					<option value="2">Subsidio</option>
					<option value="3">Otros</option>
				</select>
				<input id="btnNuevo" name="btnNuevo" type="button" value="Nuevo..." disabled="true" tabindex="14" />
			</td>
			<td align="center" colspan="2">Resultados:</td>
		</tr>
		<tr>
			<td>Buscar:</td>
			<td>
			<input disabled="true" id="txtBuscar" name="txtBuscar" type="text" value="" tabindex="15" autocomplete="off" maxlength="50" />
            </td>
			<td rowspan="3" colspan="3"><select id="lstBuscar" name="lstBuscar" tabindex="18" size="6" style="width:450px" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2">
			<input name="optBuscar" type="radio" id="optBuscar" value="1" tabindex="16" checked="true">por descripción</input>
			<input name="optBuscar" type="radio" id="optBuscar" value="2" tabindex="17">por CUIT/DNI</input>		
			</td>
		</tr>
		<tr>
			<td>Monto ($):</td>
			<td>
			<input disabled="true" id="txtMonto" name="txtMonto" type="text" value="" onkeypress="return ValidarTecla(event, '0123456789.');" onblur="this.value=(isNaN(parseFloat(this.value)) ? '' : FNumero(this.value, false));" tabindex="19" autocomplete="off" maxlength="15" />
            </td>
		</tr>
		

		<tr><td colspan="6" align="center" class="titulo1" ><big>&nbsp;</big></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td colspan="2" align="center"><input id="btnAgregar" name="btnAgregar" type="button" value="Agregar item" disabled="true" tabindex="20" /></td>
			<td colspan="1" align="center"><input id="btnInicializar" name="btnInicializar" type="button" value="Inicializar formulario" tabindex="21" /></td>
			<td colspan="2" align="center"><input id="btnTramiteInterno" name="btnTramiteInterno" type="button" value="Nueva OP p/Tramite Interno" tabindex="22" /></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>&nbsp;</td></tr>
		</table>
		</form>
	</td>
</tr>
<tr>
	<td>
	<div id="divPagos" />
	</td>
</tr>
</table>
</body>
</html>

<?php
//}
?>