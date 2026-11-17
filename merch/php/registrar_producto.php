<?php
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clave_pro   = trim($_POST['clave_pro']);
    $nombre      = trim($_POST['nombre']);
    $prec        = $_POST['prec'];
    $prev        = $_POST['prev'];
    $categoria   = trim($_POST['categoria']);
    $descripcion = trim($_POST['descripcion']);

    $sql = "INSERT INTO productos (clave_pro, nombre, prec, prev, categoria, descripcion) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssddss", $clave_pro, $nombre, $prec, $prev, $categoria, $descripcion);

    if ($stmt->execute()) {
        echo "<script>alert('Producto registrado correctamente'); window.location.href='../php/productos.php';</script>";
    } else {
        echo "<script>alert('Error: La clave de producto ya existe o los datos son incorrectos.'); window.location.href='../php/productos.php';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>
