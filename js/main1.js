/*  Syntax:elena

 *   oName = OBJECTS
 *   aName = ARRAY
 *   functionName = FUNCTION
 *   variable_name = VARIABLE
 *   CONST_NAME = CONST
 */
/**|-->|><|<--||-->|><|<--||-->|><|<--||-->|><|<--||-->|><|<--||-->|><|<--|**/
/* Vamos a usar el método fetch
 *  para consumir la API PUBLICA
 *   del sistema de precios del dólar.
 */

const API_URL = "https://www.dolarsi.com/api/api.php?type=valoresprincipales";
var aCompras = document.getElementsByClassName('compravalor');
var aVentas = document.getElementsByClassName('ventavalor');
var aAgencias = document.getElementsByClassName('agenciavalor');

window.onload = actualizarCotizaciones();
/*Con esta funcion que es inicializada al comienzo del codigo, cuando la pagina
se encuente cargada vendra a buscar el actualizador de cotizaciones y cargaré el HTML 
con los datos*/


function actualizarCotizaciones() {
    let aDataCompras = [], aDataVentas = [], aDataAgencias = [];
    for (let i = 0; i < aVentas.length; i++) {
        aVentas[i].innerHTML = "Cargando...";
        aAgencias[i].innerHTML = "Cargando...";
        aCompras[i].innerHTML = "Cargando...";
    }
  fetch(API_URL)
    .then((response) => response.json())
    .then((data) => {
        for (const item of data) {
            if (item.casa.nombre.includes('Dolar') && !item.casa.compra.includes('No Cotiza')) {
                aDataCompras.push(item.casa.compra);
                aDataVentas.push(item.casa.venta);
                aDataAgencias.push(item.casa.agencia);
            }
        }
        for (let i = 0; i < aVentas.length; i++) {
            aVentas[i].innerHTML = '$' + aDataVentas[i];
            aAgencias[i].innerHTML = 'Agencia ' + aDataAgencias[i];
            aCompras[i].innerHTML = '$' + aDataCompras[i];
        }
})}


/*Con la siguiente funcion descargamos el archivo Json completo tal y como nos lo entrega la API */
function descargarCotizaciones() {
  fetch(API_URL)
    .then((response) => response.json())
    .then((data) => {
      var json = JSON.stringify(data);
      var blob = new Blob([json], { type: "application/json" });
      var url = URL.createObjectURL(blob);
      var a = document.createElement("a");
      a.href = url;
      a.download = "cotizaciones.json";
      document.body.appendChild(a);
      a.click();
    });
}
