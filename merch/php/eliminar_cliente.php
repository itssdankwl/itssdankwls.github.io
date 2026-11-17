<?php
include("conexion.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM clientes WHERE clave = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Cliente eliminado correctamente'); window.location.href='../clientes.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar cliente'); window.location.href='../clientes.php';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
