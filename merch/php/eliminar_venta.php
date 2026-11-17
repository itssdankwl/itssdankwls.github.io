
<?php

include("conexion.php");

// Verificar que se recibió el ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ventas.php?error=id_invalido");
    exit;
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    header("Location: ventas.php?error=id_invalido");
    exit;
}

// Preparar eliminación
$sql = "DELETE FROM ventas WHERE id = ?";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    $eliminada = $stmt->affected_rows;

    $stmt->close();

    if ($eliminada > 0) {
        header("Location: ventas.php?mensaje=eliminada");
    } else {
        header("Location: ventas.php?error=no_encontrada");
    }

    exit;

} else {

    $stmt->close();

    header("Location: ventas.php?error=eliminacion");
    exit;

}

?>
