<?php
include("conexion.php");

if (isset($_GET['clave'])) {
    $clave_pro = $_GET['clave'];

    $sql = "DELETE FROM productos WHERE clave_pro = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $clave_pro);

    if ($stmt->execute()) {
        echo "<script>alert('Producto eliminado correctamente'); window.location.href='../php/productos.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el producto'); window.location.href='../php/productos.php';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
