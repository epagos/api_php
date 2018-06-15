<?
$data = base64_decode($_POST['pdf']);
$conComprobante = file_put_contents(dirname(__FILE__).'/comprobante.pdf',$data);
?>
<html>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-2"></div>
      <div class="col-md-8">
        <p class="text-center"><img src="logo_login.png" height="80" /></p>
        <div class="alert alert-success" role="alert">
          <b>Proceso correcto</b><br>
          <?= $_POST["respuesta"]; ?><br>
          <? if ($conComprobante){ ?>
          <a href="/devel/post/tests/comprobante.pdf" class="alert-link" download>Descargar Comprobante</a>
          <? } ?>
        </div>
      </div>
      <div class="col-md-2"></div>
    </div>
    <div class="row">
      <div class="col-md-12 text-center">
        <a href="/tests/obtener_token.php">Volver</a>
      </div>
    </div>
  </div>
</body>
</html>