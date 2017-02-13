# api_php
PHP - Ejemplos API

En la carpeta **/post** encontrará la forma de iniciar la petición y obtener su token. Además de un ejemplo de página de Error y Ok que recibirá el control luego de haberse procesado su solicitud.

El script **/post/obtener_token.php** a través de un POST HTTP envia las credenciales de login, obteniendo como respuesta de Epagos un código único denominado **token** que permitirá luego redireccionar al usuario a completar su pago.

La segunda parte del script **/post/obtener_token.php** realiza la redirección propiamente dicha para que el usuario realice el pago y luego retorne con la respuesta a sus URLs de Error u OK.
