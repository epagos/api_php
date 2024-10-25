<?php
/**
 * Ejemplo del método que obtiene los pagos realizados para su organismo
 */

const ID_ORGANISMO = ""; //TODO: ccmpletar con el ID de organismo proporcionado
const ID_USUARIO   = ""; //TODO: ccmpletar con el ID de usuario proporcionado
const PASSWORD     = ""; //TODO: ccmpletar con el password proporcionado
const HASH         = ""; //TODO: ccmpletar con el hash proporcionado

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
  $fecha_desde = '2023-06-28';
  $fecha_hasta = '2023-06-29';

  $criterios = ["Fecha_desde" => $fecha_desde, "Fecha_hasta" => $fecha_hasta];
  echo "</pre>";
  $pagos = $epagos->obtener_pagos_adicionales($criterios);
  echo "Resultado: <b>obtener_pagos_adicionales</b>";
  echo "<pre>";
  print_r($pagos);
  echo "</pre>";

} catch (EPagos_Exception $e){
  echo "Error: ".$e->getMessage();
} /* try, catch */