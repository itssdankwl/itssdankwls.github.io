<?php
$servidor = "localhost";
$usuario = "root";
$password = ""; // Pon tu contraseña si tienes una configurada
$base_datos = "merch";

$conexion = new mysqli($servidor, $usuario, $password, $base_datos, 3307);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>
