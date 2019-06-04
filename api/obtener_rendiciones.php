<?php
/**
 * Ejemplo de uso del método que obtiene las rendiciones a su organismo
 */

define("ID_ORGANISMO", ""); //TODO: ccmpletar con el ID de organismo proporcionado
define("ID_USUARIO",   ""); //TODO: ccmpletar con el ID de usuario proporcionado
define("PASSWORD",     ""); //TODO: ccmpletar con el password proporcionado
define("HASH",         ""); //TODO: ccmpletar con el hash proporcionado

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require 'lib/epagos_api.class.php';

try {
  $epagos = new epagos_api(ID_ORGANISMO, ID_USUARIO);

  //
  // el SDK soporta dos entornos:
  // Testing    -> EPAGOS_ENTORNO_SANDBOX
  // Producción -> EPAGOS_ENTORNO_PRODUCCION
  //
  $epagos->set_entorno(EPAGOS_ENTORNO_SANDBOX);
  $ret = $epagos->obtener_token(PASSWORD, HASH);
  if (!$ret["token"]) {
    echo 'Error: <b>generar_token</b>: ';
    echo "<pre>";
    print_r($ret);
    echo "</pre>";
    exit;
  }

  //
  // consulta la documentación para ver los criterios de búsqueda posibles
  // las fechas son generalmente en el rango de un día
  //
  $criterios = ["Fecha_desde" => "2016-06-03", "Fecha_hasta" => "2016-06-04"];
  echo "</pre>";
  $rendiciones = $epagos->obtener_rendiciones($criterios);
  echo "Resultado: <b>obtener_rendiciones</b>";
  echo "<pre>";
  print_r($rendiciones);
  echo "</pre>";

} catch (EPagos_Exception $e){
  echo "Error: ".$e->getMessage();
} /* try, catch */