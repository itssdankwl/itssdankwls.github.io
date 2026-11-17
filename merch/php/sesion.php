<?php   
session_start();

$usuario = "daniel";
$contrasena = "12345";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $us = $_POST['us'];
    $pass = $_POST['pass'];

    if ($us === $usuario && $pass === $contrasena) {
        $_SESSION['usuario'] = $us;
        header("Location: ../html/menu.html");
        exit();
    } else {
        echo "<script>
        alert('Usuario o contraseña incorrectos');
        window.location.href = '../html/sesion.html';
        </script>";
    }
}

?>