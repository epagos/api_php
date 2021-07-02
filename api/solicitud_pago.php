<?php
/**
 * Ejemplo del método que genera un pago via API para su organismo
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
  // consulta la documentación para ver los parámetros requeridos
  //

  // datos de la operación
  $opc_generar_pdf = true;                 // TODO: desactivarlo acelerará el proceso de creación de la operación si no se usará la boleta de E-Pagos
  $opc_pdf         = true;                 // TODO: indica si desea obtener el pdf de la boleta generada
  $monto           = 2000;                 // TODO: cambiar por el importe a pagar
  $email_pagador   = 'pago@epagos.com.ar'; // TODO: cambiar por el email del pagador
  $tipo_doc        = 1;                    // TODO: el tipo de documento del pagador (ver anexo de la documetación)
  $nro_doc         = '12345678';           // TODO: el número de documento del pagador
  $cuit_doc        = '20123456780';        // TODO: el número de CUIT del pagador
  $id_fp           = 34;                   // TODO: cambiar por la forma de pago deseada

  $detalles       = [[                    // TODO: los detalles de como se compone la operación a pagar
    "id_item"       => 1,
    "desc_item"     => 'item 1',
    "monto_item"    => $monto / 2,
    "cantidad_item" => 1,
  ], [
    "id_item"       => 2,
    "desc_item"     => 'item 2',
    "monto_item"    => $monto / 2,
    "cantidad_item" => 1,
  ]];
  $pagador = [
    "nombre_pagador"          => '',
    "apellido_pagador"        => '',
    "fechanac_pagador"        => '',
    "email_pagador"           => $email_pagador,
    "identificacion_pagador"  => [
      "tipo_doc_pagador"      => $tipo_doc,
      "numero_doc_pagador"    => $nro_doc,
      "cuit_doc_pagador"      => $cuit_doc,
    ],
    "domicilio_pagador"       => [
      "calle_dom_pagador"     => '',
      "numero_dom_pagador"    => '',
      "adicional_dom_pagador" => '',
      "cp_dom_pagador"        => '',
      "ciudad_dom_pagador"    => '',
      "provincia_dom_pagador" => '',
      "pais_dom_pagador"      => '',
    ],
    "telefono_pagador"        => [
      "codigo_telef_pagador"  => '',
      "numero_telef_pagador"  => '',
    ],
  ];
  $operacion = [
    "id_moneda_operacion"     => '1',
    "monto_operacion"         => $monto,
    "detalle_operacion"       => $detalles,
    "pagador"                 => $pagador,
    "numero_operacion"        => '',
    "identificador_externo_2" => '',
    "identificador_externo_3" => '',
    "identificador_externo_4" => '',
    "opc_pdf"                 => $opc_pdf,
    "opc_fecha_vencimiento"   => '',
    "opc_generar_pdf"         => $opc_generar_pdf,
    "identificador_cliente"   => '',
    "url_boleta"              => ''
  ];

  // datos de la forma de pago
  $tarjeta = [
    "numero_tarjeta_fp"         => '',
    "banco_tarjeta_fp"          => 0,
    "vencimiento_tarjeta_fp"    => [
      "mes_vencimiento_tarjeta_fp"  => '',
      "anio_vencimiento_tarjeta_fp" => '',
    ],
    "codigo_seg_tarjeta_fp"     => '',
    "cuotas_tarjeta_fp"         => 1,
    "titular_tarjeta_fp"        => '',
    "identificacion_tarjeta_fp" => [
      "tipo_identificacion_tarjeta_fp"   => '',
      "numero_identificacion_tarjeta_fp" => '',
    ],
    "fechanac_tarjeta_fp"       => '',
    "direccion_tarjeta_fp"      => [
      "calle_direccion_tarjeta_fp"  => '',
      "numero_direccion_tarjeta_fp" => '',
    ],
  ];
  $fp = [[
    'id_fp'     => $id_fp,
    'monto_fp'  => $monto,
    'tarjeta'   => $tarjeta,
  ]];

  $pago = $epagos->solicitud_pago($operacion, $fp, CONVENIO);
  echo "Resultado: <b>solicitud_pago</b>";
  echo "<pre>";
  print_r($pago);
  echo "</pre>";

} catch (EPagos_Exception $e){
  echo "Error: ".$e->getMessage();
} /* try, catch */