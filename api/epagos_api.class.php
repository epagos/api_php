<?php
define("EPAGOS_ENTORNO_SANDBOX",    0);
define("EPAGOS_ENTORNO_PRODUCCIOM", 1);

class EPagos_Exception extends Exception {}

/**
 * Gestiona la API de EPagos
 * User: Alejandro Salgueiro
 * Date: 11/10/2017
 * @version 1.0
 */
class epagos_api {
  private $_id_organismo = null;
  private $_id_usuario   = null;

  private $_entorno      = EPAGOS_ENTORNO_SANDBOX;
  private $_cliente      = null;

  private $_token        = "";

  /**
   * epagos_api constructor.
   * @param int $id_organismo Identificador del organismo
   * @param int $id_usuario Identificador del usuario
   * @throws EPagos_Exception
   */
  public function __construct($id_organismo, $id_usuario) {
    if ($id_organismo === ""){
      throw new EPagos_Exception("Debe indicar el ID de organismo recibido para la implementación");
    }
    if (!$id_usuario){
      throw new EPagos_Exception("Debe indicar el ID de usuario recibido para la implementación");
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
    if (!in_array($entorno, [1,0])){
      throw new EPagos_Exception("Indique un entorno válido");
    }
    $this->_entorno = $entorno;
  }

  /**
   * Genera un token para las credenciales especificadas
   * @param string $password El password del usuario
   * @param string $hash El hash del usuario
   * @return array
   * @throws EPagos_Exception
   */
  public function obtener_token($password, $hash){
    if (!$password){
      throw new EPagos_Exception("Debe indicar el password recibido para la implementación");
    }
    if (!$hash){
      throw new EPagos_Exception("Debe indicar el hash recibido para la implementación");
    }

    $credenciales = array(
      'id_usuario'   => $this->_id_usuario,
      'id_organismo' => $this->_id_organismo,
      'password'     => $password,
      'hash'         => $hash
    );

    $this->_cliente = new SoapClient($this->get_url(), array(
      "soap_version" => SOAP_1_1,
      "trace" => true,
      "exceptions" => false,
      "cache_wsdl" => WSDL_CACHE_NONE,
    ));
    if (is_soap_fault($this->_cliente)) {
      throw new EPagos_Exception($this->_cliente->faultcode." - ".$this->_cliente->faultstring);
    }

    $resultado = $this->_cliente->obtener_token($this->get_version(), $credenciales);
    if (is_soap_fault($resultado)) {
      throw new EPagos_Exception($this->_cliente->faultcode." - ".$this->_cliente->faultstring);
    }

    $this->_token = $resultado["token"];
    return $resultado;
  }

  /**
   * Devuelve la consulta de los pagos especificados
   * @param array $criterios Vector con los criterios de consulta
   * @return array
   * @throws EPagos_Exception
   */
  public function obtener_pagos($criterios = []){
    if (count($criterios) == 0){
      throw new EPagos_Exception("Debe indicar algún crtierio de búsqueda de los pagos");
    }

    $credenciales = array(
      "id_organismo" => $this->_id_organismo,
      "token"        => $this->_token,
    );

    $resultado = $this->_cliente->obtener_pagos($this->get_version(), $credenciales, $criterios);
    if (is_soap_fault($resultado)) {
      throw new EPagos_Exception($this->_cliente->faultcode." - ".$this->_cliente->faultstring);
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
    $credenciales = array(
      "id_organismo" => $this->_id_organismo,
      "token"        => $this->_token,
    );

    $resultado = $this->_cliente->obtener_entidades_pago($this->get_version(), $credenciales, $criterios);
    if (is_soap_fault($resultado)) {
      throw new EPagos_Exception($this->_cliente->faultcode." - ".$this->_cliente->faultstring);
    }

    return $resultado;
  }

  /**
   * Devuelve la versión actual de la API
   * @return string
   */
  public function get_version(){
    return '1.0';
  }

  /**
   * Devuelve la URL a donde enviar las solicitudes de API
   * @return string
   */
  private function get_url(){
    if ($this->_entorno == EPAGOS_ENTORNO_PRODUCCIOM)
      return 'https://api.epagos.com.ar/wsdl/index.php?wsdl';
    else
      return 'https://sandbox.epagos.com.ar/wsdl/index.php?wsdl';
  }
}