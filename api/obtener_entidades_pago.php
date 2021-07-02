<?php
/**
 * Ejemplo del método que obtiene las entidades de pago disponibles para su organismo
 */

const ID_ORGANISMO = ""; //TODO: ccmpletar con el ID de organismo proporcionado
const ID_USUARIO   = ""; //TODO: ccmpletar con el ID de usuario proporcionado
const PASSWORD     = ""; //TODO: ccmpletar con el password proporcionado
const HASH         = ""; //TODO: ccmpletar con el hash proporcionado
const CONVENIO     = ""; //TODO: completar con el convenio proporcionado

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
  $criterios = [];
  $entidades = $epagos->obtener_entidades_pago($criterios);
  echo "Resultado: <b>obtener_entidades_pago</b>";
  echo "<pre>";
  print_r($entidades);

} catch (EPagos_Exception $e){
  echo "Error: ".$e->getMessage();
} /* try, catch */