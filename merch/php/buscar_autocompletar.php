
<?php

include("conexion.php");

header("Content-Type: application/json; charset=UTF-8");

// Evitar que los errores PHP contaminen la respuesta JSON
ini_set('display_errors', '0');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {

    if (!isset($_GET['tipo']) || !isset($_GET['buscar'])) {
        throw new Exception("Faltan datos de búsqueda.");
    }

    $tipo = $_GET['tipo'];
    $buscar = trim($_GET['buscar']);

    if ($buscar === '') {
        throw new Exception("El campo de búsqueda está vacío.");
    }

    // ==========================================
    // BUSCAR CLIENTE
    // ==========================================

    if ($tipo === "cliente") {

        $sql = "SELECT clave, nombres
                FROM clientes
                WHERE clave = ?
                OR nombres LIKE ?
                LIMIT 1";

        $stmt = $conexion->prepare($sql);

        $termino = "%" . $buscar . "%";

        $stmt->bind_param("ss", $buscar, $termino);

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($fila = $resultado->fetch_assoc()) {

            $respuesta = [
                "exito" => true,
                "clave" => $fila['clave'],
                "nombre" => $fila['nombres']
            ];

        } else {

            $respuesta = [
                "exito" => false,
                "mensaje" => "No se encontró el cliente."
            ];

        }

        $stmt->close();

    }

    // ==========================================
    // BUSCAR PRODUCTO
    // ==========================================

    elseif ($tipo === "producto") {

        $sql = "SELECT clave_pro, nombre, prev
                FROM productos
                WHERE clave_pro = ?
                OR nombre LIKE ?
                LIMIT 1";

        $stmt = $conexion->prepare($sql);

        $termino = "%" . $buscar . "%";

        $stmt->bind_param("ss", $buscar, $termino);

        $stmt->execute();

        $resultado = $stmt->get_result();

        if ($fila = $resultado->fetch_assoc()) {

            $respuesta = [
                "exito" => true,
                "clave" => $fila['clave_pro'],
                "nombre" => $fila['nombre'],
                "precio" => $fila['prev']
            ];

        } else {

            $respuesta = [
                "exito" => false,
                "mensaje" => "No se encontró el producto."
            ];

        }

        $stmt->close();

    }

    else {

        throw new Exception("Tipo de búsqueda no válido.");

    }

    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {

    error_log("Error en buscar_autocompletar.php: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "exito" => false,
        "mensaje" => "Error en la consulta. Revisa los nombres de las columnas y tablas."
    ], JSON_UNESCAPED_UNICODE);

}

?>
