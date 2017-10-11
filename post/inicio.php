<?php
/**
 * Probador de la generación de Token para la página de POST
 * Pasos:
 *   1 - Generar el token
 *   2 - Invocar al formulario de POST con ese token y los otros parámetros necesarios,
 *       puede invocarse al formulario intermedio de tests/inicio.php para completar
 *       los otros parámetros desde una interfaz más cómoda.
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include("../api/epagos_api.class.php");

define("ID_ORGANISMO", ""); //TODO: ccmpletar con el ID de organismo proporcionado
define("ID_USUARIO",   ""); //TODO: ccmpletar con el ID de usuario proporcionado
define("PASSWORD",     ""); //TODO: ccmpletar con el password proporcionado
define("HASH",         ""); //TODO: ccmpletar con el hash proporcionado

//TODO: reemplazar por su URL para el caso de pago correcto (no implica acreditado)
define("URL_OK",       "https://postsandbox.epagos.com.ar/tests/ok.php");
//TODO: reemplazar por su URL para el caso de pago con errores
define("URL_ERROR",    "https://postsandbox.epagos.com.ar/tests/error.php");

$epagos = new epagos_api(ID_ORGANISMO, ID_USUARIO);
//
// el SDK soporta dos entornos:
// Testing    -> EPAGOS_ENTORNO_SANDBOX
// Producción -> EPAGOS_ENTORNO_PRODUCCIOM
//
$epagos->set_entorno(EPAGOS_ENTORNO_SANDBOX);
$respuesta = $epagos->obtener_token_post(PASSWORD, HASH);
// control de error en la respuesta
if (empty($respuesta->token)){
  echo 'Error: <b>obtener_token_post</b>: ';
  echo "<pre>";
  print_r($respuesta);
  echo '</pre>';
  exit;
}

$token = $respuesta->token;


//TODO: personalizar con sus valores y detalles a enviar, esto es opcional, puede no enviarlo
$detalle_op = urlencode(json_encode(
  [
    [
      'id_item'       => '1',
      'desc_item'     => 'Descripcion 1',
      'monto_item'    => '120',
      'cantidad_item' => '1'
    ],
    [
      'id_item'       => '2',
      'desc_item'     => 'Descripcion 2',
      'monto_item'    => '120',
      'cantidad_item' => '3'
    ],
  ]
));
$datos_post = [
  'numero_operacion'    => '1',
  'id_moneda_operacion' => '1',
  'monto_operacion'     => 480,   // TODO: reemplazar por el importe de la operación
  'detalle_operacion'   => $detalle_op,
  //
  // En el caso de que se desee mostrar el usuario el detalle en la pantalla de pago, incluir:
  //  'detalle_operacion_visible' => 1,
  //
  'ok_url'              => URL_OK,     //TODO: reemplazar por sus URL
  'error_url'           => URL_ERROR,  //TODO: reemplazar por sus URL
  //
  // Usos avanzados
  //
  // - Para restringir que solo se vean determinados medios de pago:
  // $datos_post['fp_permitidas'] = serialize([1, 2, 3, 9, 10, 11]);
  //
  // - Para excluir determinados medios de pago:
  // $datos_post['fp_excluidas'] = serialize([1, 2, 3]);
  //
];
$epagos->solicitud_pago_post($datos_post);