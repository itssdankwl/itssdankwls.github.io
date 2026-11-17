
<?php

include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../ventas.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);

$clave = trim($_POST['clave'] ?? '');
$clave_pro = trim($_POST['clave_pro'] ?? '');
$total = $_POST['total'] ?? '';

if (
    $id <= 0 ||
    $clave === '' ||
    $clave_pro === '' ||
    !is_numeric($total) ||
    (float)$total <= 0
) {
    die("Los datos proporcionados no son válidos.");
}


// ==========================================
// VERIFICAR CLIENTE
// ==========================================

$sql = "SELECT clave, nombres FROM cliente WHERE clave = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $clave);
$stmt->execute();

$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cliente) {
    die("El cliente no existe.");
}


// ==========================================
// VERIFICAR PRODUCTO
// ==========================================

$sql = "SELECT clave_pro, nombre FROM producto WHERE clave_pro = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $clave_pro);
$stmt->execute();

$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$producto) {
    die("El producto no existe.");
}


// ==========================================
// ACTUALIZAR VENTA
// ==========================================

$nombres = $cliente['nombres'];
$nombre = $producto['nombre'];
$total = (float)$total;

$sql = "UPDATE ventas SET
        clave = ?,
        nombres = ?,
        clave_pro = ?,
        nombre = ?,
        total = ?
        WHERE id = ?";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "ssssdi",
    $clave,
    $nombres,
    $clave_pro,
    $nombre,
    $total,
    $id
);

if ($stmt->execute()) {

    $stmt->close();

    header("Location: ../ventas.php?mensaje=actualizada");
    exit;

} else {

    die("Error al actualizar la venta: " . $stmt->error);

}

?>
