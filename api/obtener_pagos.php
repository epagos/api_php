<?php
/**
 * Prueba el método que obtiene los pagos realizados
 */

define("ID_ORGANISMO", ""); //TODO: ccmpletar con el ID de organismo proporcionado
define("ID_USUARIO",   ""); //TODO: ccmpletar con el ID de usuario proporcionado
define("PASSWORD",     ""); //TODO: ccmpletar con el password proporcionado
define("HASH",         ""); //TODO: ccmpletar con el hash proporcionado

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require "epagos_api.class.php";
$epagos = new epagos_api(ID_ORGANISMO, ID_USUARIO);
//
// el SDK soporta dos entornos:
// Testing    -> EPAGOS_ENTORNO_SANDBOX
// Producción -> EPAGOS_ENTORNO_PRODUCCION
//
try {
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
  $criterios = [];
  $entidades = $epagos->obtener_entidades_pago($criterios);
  echo "Resultado: <b>obtener_entidades_pago</b>";
  echo "<pre>";
  print_r($entidades);

  //
  // consulta la documentación para ver los criterios de búsqueda posibles
  //
  $criterios = ["Estado" => "A"];
  echo "</pre>";
  $pagos = $epagos->obtener_pagos($criterios);
  echo "Resultado: <b>obtener_pagos</b>";
  echo "<pre>";
  print_r($pagos);
  echo "</pre>";

} catch (EPagos_Exception $e){
  echo "Error: ".$e->getMessage();
} /* try, catch */