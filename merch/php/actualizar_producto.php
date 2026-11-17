<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clave_pro   = $_POST['clave_pro'];
    $nombre      = trim($_POST['nombre']);
    $prec        = $_POST['prec'];
    $prev        = $_POST['prev'];
    $categoria   = trim($_POST['categoria']);
    $descripcion = trim($_POST['descripcion']);

    $sql = "UPDATE productos SET nombre = ?, prec = ?, prev = ?, categoria = ?, descripcion = ? WHERE clave_pro = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sddsss", $nombre, $prec, $prev, $categoria, $descripcion, $clave_pro);

    if ($stmt->execute()) {
        echo "<script>alert('Producto actualizado correctamente'); window.location.href='../php/productos.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el producto'); window.location.href='../php/productos.php';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
