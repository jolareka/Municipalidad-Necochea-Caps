<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="sesion.php" method="post">
        <div class="contenedorlogin">
        <input class="contenedorlogin" type="text" name="usuario" id="usuario" placeholder="">
        <label class="contenedorlogin" for="usuario">Usuario</label>
        <input class="contenedorlogin" type="text" name="contrasenia" id="contrasenia" placeholder="">
        <label class="contenedorlogin" for="contrasenia">Contraseña</label>
    </div>
    <input type="submit" value="Iniciar Sesion"class="login">
    <input type="reset" value="Cancelar" class="login">
    </form>
</body>
</html>