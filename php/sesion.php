<?php
include('../conexion.php');
session_start();

if (isset($_SESSION['usuario'])) {
    echo'<script>
       alert("Para ingresar a esta pagina, no debe tener una sesion activa");
       window.location.href = "/Municipalidad-Necochea-Caps/index.php";
       </script>';
}

if(!isset($_POST['usuario'])){ //VERIFICACION DE SESION Y ENTRADA POR URL
    header('Location: /Municipalidad-Necochea-Caps/index.php');
}
else
{
    $email = $_POST['usuario'];
    $contrasenia = $_POST['contrasenia'];
    $contra_encriptada = md5($contrasenia); //ENCRIPTACION DE CONTRASEÑA DE FORMULARIO
    $sql="SELECT * FROM usuarios where usuario = '".$usuario."'"; //CONSULTAMOS SI EXISTE EL USUARIO
    $resultado = mysqli_query($conexion,$sql);

    if(mysqli_num_rows($resultado)>0)
    { //SI EXISTE LA CUENTA
        $datos=mysqli_fetch_assoc($resultado);
        if ($contra_encriptada==$datos['contrasenia'])
        { //SI LAS CONTRASEÑAS COINCIDEN
         $_SESSION['usuario']=$datos['nombre']; //INICIAMOS SESION
         $_SESSION['administrador']=$datos['administrador'];
        //verificar si es admin
            if ($datos['administrador'] == 1) {
            header("Location: ../admin/administrador.php");
            exit();
            } 
        }
        else
        {
            //si las contraseñas no coinciden
            echo'<script>
            alert("contraseña incorrecta");
            </script>';
        }
    }
    else {
        //no existe la cuenta
        echo'<script>
            alert("Usuario ingresado no existe);
           window.location.href = "form_sesion.php";
            </script>';
    }
}
?>
