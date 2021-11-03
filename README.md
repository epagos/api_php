# api_php
PHP - Ejemplos API

En la carpeta **/post** encontrará la forma de iniciar la petición y obtener su token usando la modalidad de redirección o con iframe respectivamente.
Además de un ejemplo de página de Error y Ok que recibirá el control luego de haberse procesado su solicitud.

El script **/post/redireccion/inicio.php** y **/post/iframe/inicio.php** a través de un POST HTTP envia las credenciales de login, 
obteniendo como respuesta de E-Pagos un código único denominado **token** que permitirá luego redireccionar al usuario a completar su pago.
La segunda parte del script realiza la redirección propiamente dicha para que el usuario realice el pago y luego retorne con la respuesta a sus URLs de Error u OK.

En la carpeta **/api** está el ejemplo de cómo consultar a través del Webservice los pagos realizados a través de la plataforma, 
así como iniciar solicitudes de pago y/o obtener las rendiciones generadas.

###Requerimientos:
 - PHP 5.6 o superior
 - Extensión SOAP en PHP

La carpeta **/quickstart** contiene un ejemplo con iframe de como implementar el botón de inicio rápido de pago usando solamente Javascript.