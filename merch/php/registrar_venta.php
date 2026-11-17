
<?php

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../ventas.php");
    exit;
}

$clave = trim($_POST['clave'] ?? '');
$clave_pro = trim($_POST['clave_pro'] ?? '');
$total = $_POST['total'] ?? '';

if ($clave === '' || $clave_pro === '' ||
    !is_numeric($total) || (float)$total <= 0) {
    die("Datos de venta inválidos.");
}


// ==========================================
// VERIFICAR CLIENTE
// ==========================================

$sql = "SELECT clave, nombres FROM clientes WHERE clave = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $clave);
$stmt->execute();

$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cliente) {
    die("El cliente seleccionado no existe.");
}


// ==========================================
// VERIFICAR PRODUCTO
// ==========================================

$sql = "SELECT clave_pro, nombre FROM productos WHERE clave_pro = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $clave_pro);
$stmt->execute();

$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$producto) {
    die("El producto seleccionado no existe.");
}


// ==========================================
// REGISTRAR VENTA
// ==========================================

$nombres = $cliente['nombres'];
$nombre = $producto['nombre'];
$total = (float)$total;

$sql = "INSERT INTO ventas
        (clave, nombres, clave_pro, nombre, total)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "ssssd",
    $clave,
    $nombres,
    $clave_pro,
    $nombre,
    $total
);

if ($stmt->execute()) {

    $stmt->close();

    header("Location: ../php/ventas.php?mensaje=registrada");
    exit;

} else {

    die("Error al registrar la venta: " . $stmt->error);

}

?>
