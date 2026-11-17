<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id        = $_POST['id'];
    $nombres   = $_POST['nombres'];
    $edad      = $_POST['edad'];
    $telefono  = $_POST['telefono'];
    $correo    = $_POST['correo'];
    $direccion = $_POST['direccion'];

    $sql = "UPDATE clientes SET nombres = ?, edad = ?, telefono = ?, correo = ?, direccion = ? WHERE clave = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sisssi", $nombres, $edad, $telefono, $correo, $direccion, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Datos del cliente actualizados'); window.location.href='../php/clientes.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar'); window.location.href='../php/clientes.php';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
