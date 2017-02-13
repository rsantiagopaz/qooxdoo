qx.Class.define("componente.comp.Rutinas",
{
	statics:
	{
		validar_CUIT: function(cuit) {
			var multiplos = new Array(9);
	        multiplos[0] = 5;
	        multiplos[1] = 4;
	        multiplos[2] = 3;
	        multiplos[3] = 2;
	        multiplos[4] = 7;
	        multiplos[5] = 6;
	        multiplos[6] = 5;
	        multiplos[7] = 4;
	        multiplos[8] = 3;
	        multiplos[9] = 2;
	        var sumador = 0;
	       
	        if (cuit.length == 11) {
	            for(var i=0;i<((cuit.length)-1);i++) {
	                sumador = sumador + (cuit.charAt(i) * multiplos[i]);
	            }
	   
	            sumador = (11 - (sumador % 11)) % 11;
	   
	            if (cuit.charAt(10) != sumador) {
	                return false;
	            } else {
	                return true;
	            }
	        } else {
	            return false;
	        }
		}
	}

});