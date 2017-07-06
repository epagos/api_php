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

$id_organismo = ID_ORGANISMO;
$credenciales = array(
  'id_usuario'   => ID_USUARIO,
  'id_organismo' => $id_organismo,
  'password'     => PASSWORD,
  'hash'         => HASH
);
$wsdl = 'https://sandbox.epagos.com.ar/wsdl/index.php?wsdl';

$cliente = new SoapClient($wsdl, array(
                                   "soap_version" => SOAP_1_1,
                                   "trace" => true,
                                   "exceptions" => false,
                                   "cache_wsdl" => WSDL_CACHE_NONE,
                                 ));
if (is_soap_fault($cliente)) {
  echo '<h2>Constructor error</h2>';
  echo $cliente->faultcode." - ".$cliente->faultstring;
  exit();
}

$resultado = $cliente->obtener_token('1.0', $credenciales);
if (is_soap_fault($resultado)) {
  echo 'Error: <b>generar_token</b>: ';
  echo "<pre>";
  echo $resultado->faultcode." - ".$resultado->faultstring;
  echo $cliente->__getLastResponseHeaders();
  echo $cliente->__getLastResponse();
  echo "</pre>";
  exit;
}

if (!$resultado["token"]){
  echo 'Error: <b>generar_token</b>: ';
  echo "<pre>";
  print_r($resultado);
  echo "</pre>";
  exit;
}

// datos de las credenciales
$credenciales = array(
  "id_organismo" => $id_organismo,
  "token"        => $resultado["token"],
);

// datos de la operación
$pagos = array(
  'Estado' => 'A', // estado del pago
  'FechaPagoDesde' => "2017-05-01", //fecha de realización (desde) 
  'FechaPagoHasta' => "2017-06-01", //fecha de realización (hasta)
);

$resultado = $cliente->obtener_pagos('1.0', $credenciales, $pagos);
if (is_soap_fault($resultado)) {
  echo 'Error: <b>obtener_pagos</b>: ';
  echo "<pre>";
  echo $resultado->faultcode." - ".$resultado->faultstring;
  echo $cliente->__getLastResponseHeaders();
  echo $cliente->__getLastResponse();
  echo "</pre>";
  exit;
}

echo "Resultado: <b>obtener_pagos</b>";
echo "<pre>";
print_r($resultado);
echo "</pre>";
?>