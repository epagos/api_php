<?php
/**
 * Ejemplo del método que obtiene las operaciones pagadas que fueron devueltas para su organismo
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
  //
  $criterios = ["FechaNovedadAcreditacionDesde" => '2020-09-01',"FechaNovedadAcreditacionHasta" => '2020-09-20'];
  echo "</pre>";
  $pagos = $epagos->obtener_devoluciones($criterios);
  echo "Resultado: <b>obtener_devoluciones</b>";
  echo "<pre>";
  print_r($pagos);
  echo "</pre>";

} catch (EPagos_Exception $e){
  echo "Error: ".$e->getMessage();
} /* try, catch */