<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombres   = $_POST['nombres'];
    $edad      = $_POST['edad'];
    $telefono  = $_POST['telefono'];
    $correo    = $_POST['correo'];
    $direccion = $_POST['direccion'];

    $sql = "INSERT INTO clientes (nombres, edad, telefono, correo, direccion) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sisss", $nombres, $edad, $telefono, $correo, $direccion);

    if ($stmt->execute()) {
        echo "<script>alert('Cliente registrado correctamente'); window.location.href='../php/clientes.php';</script>";
    } else {
        echo "<script>alert('Error al registrar cliente'); window.location.href='../php/clientes.php';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
