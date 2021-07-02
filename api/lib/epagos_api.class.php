<?php
const EPAGOS_ENTORNO_SANDBOX    = 0;
const EPAGOS_ENTORNO_PRODUCCION = 1;

class EPagos_Exception extends Exception {}

/**
 * Gestiona la API de EPagos
 * User: Alejandro Salgueiro
 * Date: 02/07/2021
 * @version 3.0
 */
class epagos_api {
  const DEBUG_ACTIVADO   = false;

  private $_id_organismo = null;
  private $_id_usuario   = null;

  private $_entorno      = EPAGOS_ENTORNO_SANDBOX;
  private $_cliente      = null;
  private $_debug        = [];

  private $_token        = '';

  /**
   * epagos_api constructor.
   * @param int $id_organismo Identificador del organismo
   * @param int $id_usuario Identificador del usuario
   * @throws EPagos_Exception
   */
  public function __construct($id_organismo, $id_usuario) {
    if ($id_organismo === ''){
      throw new EPagos_Exception('Debe indicar el ID de organismo recibido para la implementación');
    }
    if (!$id_usuario){
      throw new EPagos_Exception('Debe indicar el ID de usuario recibido para la implementación');
    }
    $this->_id_organismo = $id_organismo;
    $this->_id_usuario   = $id_usuario;
  }

  /**
   * Cambia el entorno a donde se envian las peticiones
   * @param int $entorno El entorno a consumir
   * @throws EPagos_Exception
   */
  public function set_entorno($entorno){
    if (!in_array($entorno, [1, 0])){
      throw new EPagos_Exception('Indique un entorno válido');
    }
    $this->_entorno = $entorno;
  }

  /**
   * Devuelve el último error producido
   * @return null|array
   */
  public function getDebug(){
    return $this->_debug;
  }

  /**
   * Genera un token para las credenciales especificadas
   * @param string $password El password del usuario
   * @param string $hash El hash del usuario
   * @return array
   * @throws EPagos_Exception
   * @throws SoapFault
   */
  public function obtener_token($password, $hash){
    if (!$password){
      throw new EPagos_Exception('Debe indicar el password recibido para la implementación');
    }
    if (!$hash){
      throw new EPagos_Exception('Debe indicar el hash recibido para la implementación');
    }

    $credenciales = array(
      'id_usuario'   => $this->_id_usuario,
      'id_organismo' => $this->_id_organismo,
      'password'     => $password,
      'hash'         => $hash
    );

    $this->_cliente = new SoapClient($this->get_url(), array(
      'soap_version'  => SOAP_1_1,
      'trace'         => true,
      'exceptions'    => false,
      'cache_wsdl'    => WSDL_CACHE_NONE
    ));

    if (is_soap_fault($this->_cliente)) {
      if (self::DEBUG_ACTIVADO){
        $this->_debug[] = 'obtener_token :: '.$this->_cliente->__getLastResponse();
      }
      throw new EPagos_Exception($this->_cliente->faultcode. ' - ' .$this->_cliente->faultstring);
    }

    $resultado = $this->_cliente->obtener_token($this->get_version(), $credenciales);

    if (self::DEBUG_ACTIVADO) {
      $this->_debug[] = 'obtener_token :: ' . $this->_cliente->__getLastResponse();
    }

    if (is_soap_fault($resultado)) {
      throw new EPagos_Exception($this->_cliente->faultcode. ' - ' .$this->_cliente->faultstring);
    }

    $this->_token = $resultado['token'];
    return $resultado;
  }

  /**
   * Devuelve la consulta de los pagos especificados
   * @param array $criterios Vector con los criterios de consulta
   * @return array
   * @throws EPagos_Exception
   */
  public function obtener_pagos($criterios = []){
    if (empty($criterios)){
      throw new EPagos_Exception('Debe indicar algún crtierio de búsqueda de los pagos');
    }

    if (!$this->_cliente){
      throw new EPagos_Exception('Debe invocar primero al obtener_token');
    }

    $credenciales = array(
      'id_organismo' => $this->_id_organismo,
      'token'        => $this->_token
    );

    $resultado = $this->_cliente->obtener_pagos($this->get_version(), $credenciales, $criterios);
    if (is_soap_fault($resultado)) {
      throw new EPagos_Exception($this->_cliente->faultcode. ' - ' .$this->_cliente->faultstring);
    }

    return $resultado;
  }

  /**
   * Devuelve la consulta de las devoluciones especificadas
   * @param array $criterios Vector con los criterios de consulta
   * @return array
   * @throws EPagos_Exception
   */
  public function obtener_devoluciones($criterios = []){
    if (empty($criterios)){
      throw new EPagos_Exception('Debe indicar algún crtierio de búsqueda de las devoluciones');
    }
    if (!$this->_cliente){
      throw new EPagos_Exception('Debe invocar primero al obtener_token');
    }

    $criterios['Estado'] = 'D';

    $credenciales = array(
      'id_organismo' => $this->_id_organismo,
      'token'        => $this->_token
    );

    $resultado = $this->_cliente->obtener_pagos($this->get_version(), $credenciales, $criterios);
    if (is_soap_fault($resultado)) {
      throw new EPagos_Exception($this->_cliente->faultcode. ' - ' .$this->_cliente->faultstring);
    }

    return $resultado;
  }

  /**
   * Devuelve la consulta de los medios de pago disponibles
   * @param array $criterios Vector con los criterios de consulta
   * @return array
   * @throws EPagos_Exception
   */
  public function obtener_entidades_pago($criterios = []){
    if (!$this->_cliente){
      throw new EPagos_Exception('Debe invocar primero al obtener_token');
    }

    $credenciales = array(
      'id_organismo' => $this->_id_organismo,
      'token'        => $this->_token
    );

    $resultado = $this->_cliente->obtener_entidades_pago($this->get_version(), $credenciales, $criterios);
    if (is_soap_fault($resultado)) {
      throw new EPagos_Exception($this->_cliente->faultcode. ' - ' .$this->_cliente->faultstring);
    }

    return $resultado;
  }

  /**
   * Genera un token por medio de un POST HTTP para las credenciales especificadas
   * @param string $password El password del usuario
   * @param string $hash El hash del usuario
   * @return object
   * @throws EPagos_Exception
   */
  public function obtener_token_post($password, $hash){
    if (!$password){
      throw new EPagos_Exception('Debe indicar el password recibido para la implementación');
    }
    if (!$hash){
      throw new EPagos_Exception('Debe indicar el hash recibido para la implementación');
    }

    $fields = [
      'id_usuario'   => $this->_id_usuario,
      'id_organismo' => $this->_id_organismo,
      'password'     => $password,
      'hash'         => $hash
    ];
    $post_field_string = http_build_query($fields, '', '&');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->get_url_token());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field_string);
    curl_setopt($ch, CURLOPT_POST, true);
    $response = curl_exec($ch);

    // control de error HTTP
    if ($response === FALSE) {
      throw new EPagos_Exception(curl_error($ch));
    }

    curl_close($ch);
    $resultado = json_decode($response);
    if ($resultado->token)
      $this->_token = $resultado->token;

    return $resultado;
  }

  /**
   * Realiza una solicitud de pago pero con un iframe con el POST que redirige al usuario
   * @param array $datos Vector con los parámetros del pago
   */
  public function solicitud_pago_post_iframe($datos){
    $datos['version']      = $this->get_version();
    $datos['operacion']    = 'op_pago';
    $datos['id_organismo'] = $this->_id_organismo;
    $datos['token']        = $this->_token;

    $s_fields = "";
    foreach ($datos as $field_key => $field_value){
      $s_fields .= "<input type='hidden' name='".$field_key."' value='".$field_value."' />";
    }

    exit("<html>
              <body>
                <form name='f' target='epagos_iframe' method='post' action='".$this->get_url_post()."'>".$s_fields."</form>
                <h1>Esta es su página web</h1>
                <p>Debajo coloca el iframe integrado a su web:</p>
                <iframe name='epagos_iframe' frameborder='0' src='#' style='width: 100%; height: 500px'></iframe>  
                <script type='text/javascript'>
                document.addEventListener(\"DOMContentLoaded\", function() { 
                  document.forms.f.submit();
                });
                </script>
              </body>
            </html>");
  }

  /**
   * Realiza una solicitud de pago a través del POST que redirige al usuario
   * @param array $datos Vector con los parámetros del pago
   */
  public function solicitud_pago_post($datos){
    $datos['version']      = $this->get_version();
    $datos['operacion']    = 'op_pago';
    $datos['id_organismo'] = $this->_id_organismo;
    $datos['token']        = $this->_token;

    $s_fields = "";
    foreach ($datos as $field_key => $field_value){
      $s_fields .= "<input type='hidden' name='".$field_key."' value='".$field_value."' />";
    }

    exit("<html>
              <body>
                <form name='f' method='post' action='".$this->get_url_post()."'>".$s_fields."</form>
                <script type='text/javascript'>document.forms.f.submit();</script>  
              </body>
            </html>");
  }

  /**
   * Genera una operación via API
   * @param array $operacion Vector con los datos de la operación
   * @param array $fp Vector con los datos de la forma de pago
   * @param string $convenio El número de convenio
   * @return array
   * @throws EPagos_Exception
   */
  public function solicitud_pago($operacion, $fp, $convenio=null){
    if (empty($operacion)){
      throw new EPagos_Exception('Debe indicar parámetros para iniciar un pago');
    }
    if (empty($fp)){
      throw new EPagos_Exception('Debe indicar los datos de la forma de pago para iniciar un pago');
    }
    if (!$this->_cliente){
      throw new EPagos_Exception('Debe invocar primero al obtener_token');
    }

    $credenciales = array(
      'id_organismo' => $this->_id_organismo,
      'token'        => $this->_token
    );

    $resultado = $this->_cliente->solicitud_pago(
      $this->get_version(),
      'op_pago',
      $credenciales,
      $operacion,
      $fp,
      $convenio
    );

    if (self::DEBUG_ACTIVADO) {
      $this->_debug[] = 'solicitud_pago :: ' . $this->_cliente->__getLastResponse();
    }

    if (is_soap_fault($resultado)) {
      throw new EPagos_Exception($resultado->faultcode. ' - ' .$resultado->faultstring);
    }

    return $resultado;
  }

  /**
   * Genera un lote de una o más operaciones via API
   * @param array $lote Vector con los datos de las operaciones a generar
   * @return array
   * @throws EPagos_Exception
   */
  public function solicitud_pago_lote($lote){
    if (empty($lote)){
      throw new EPagos_Exception('Debe indicar los parámetros para iniciar los pagos');
    }
    if (!$this->_cliente){
      throw new EPagos_Exception('Debe invocar primero al obtener_token');
    }

    $credenciales = array(
      'id_organismo' => $this->_id_organismo,
      'token'        => $this->_token
    );

    $resultado = $this->_cliente->solicitud_pago_lote($this->get_version(), 'op_pago', $credenciales, $lote);

    if (self::DEBUG_ACTIVADO) {
      $this->_debug[] = 'solicitud_pago_lote :: ' . $this->_cliente->__getLastResponse();
    }

    if (is_soap_fault($resultado)) {
      throw new EPagos_Exception($resultado->faultcode. ' - ' .$resultado->faultstring);
    }

    return $resultado;
  }

  /**
   * Obtener la lista de las rendiciones del sistema
   * @param array $criterios Vector con los criterios de consulta
   * @return array
   * @throws EPagos_Exception
   */
  public function obtener_rendiciones($criterios = []){
    if (empty($criterios)){
      throw new EPagos_Exception('Debe indicar algún crtierio de búsqueda de las rendiciones');
    }
    if (!$this->_cliente){
      throw new EPagos_Exception('Debe invocar primero al obtener_token');
    }

    $credenciales = array(
      'id_organismo' => $this->_id_organismo,
      'token'        => $this->_token
    );

    $resultado = $this->_cliente->obtener_rendiciones($this->get_version(), $credenciales, $criterios);
    if (is_soap_fault($resultado)) {
      throw new EPagos_Exception($resultado->faultcode. ' - ' .$resultado->faultstring);
    }

    return $resultado;
  }

  /**
   * Devuelve la versión actual de la API
   * @return string
   */
  public function get_version(){
    return '2.0';
  }

  /********************************** Métodos privados *******************************************/

  /**
   * Devuelve la URL a donde enviar las solicitudes de API
   * @return string
   */
  private function get_url(){
    if ($this->_entorno == EPAGOS_ENTORNO_PRODUCCION){
      return 'https://api.epagos.com.ar/wsdl/2.0/index.php?wsdl';
    } else {
      return 'https://sandbox.epagos.com.ar/wsdl/2.0/index.php?wsdl';
    }
  }

  /**
   * Devuelve la URL a donde obtener el token
   * @return string
   */
  private function get_url_token(){
    if ($this->_entorno == EPAGOS_ENTORNO_PRODUCCION) {
      return 'https://api.epagos.com.ar/post.php';
    } else {
      return 'https://sandbox.epagos.com.ar/post.php';
    }
  }

  /**
   * Devuelve la URL a donde enviar al usuario para completar la solicitud de pago
   * @return string
   */
  private function get_url_post(){
    if ($this->_entorno == EPAGOS_ENTORNO_PRODUCCION) {
      return 'https://post.epagos.com.ar';
    } else {
      return 'https://postsandbox.epagos.com.ar';
    }
  }
}