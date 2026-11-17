
<?php
include("conexion.php");

$resultados = [];
$busqueda_realizada = false;

if (isset($_GET['buscar']) && trim($_GET['buscar']) !== '') {

    $busqueda_realizada = true;
    $buscar = trim($_GET['buscar']);
    $termino = "%" . $buscar . "%";

    $sql = "SELECT * FROM ventas WHERE nombres LIKE ? OR nombre LIKE ? OR CAST(id AS CHAR) = ? OR clave = ? OR clave_pro = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "sssss",
        $termino,
        $termino,
        $buscar,
        $buscar,
        $buscar
    );

    $stmt->execute();
    $res = $stmt->get_result();

    while ($fila = $res->fetch_assoc()) {
        $resultados[] = $fila;
    }

    $stmt->close();
}

$venta_editar = null;

if (isset($_GET['editar_id'])) {

    $id = (int)$_GET['editar_id'];

    $stmt = $conexion->prepare(
        "SELECT * FROM ventas WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $venta_editar = $stmt->get_result()->fetch_assoc();

    $stmt->close();
}

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Ventas - MERCH</title>

    <link rel="stylesheet" href="../css/clientes.css">
</head>

<body>

<!-- ENCABEZADO -->

<div class="encabezado">

    <div class="izq-header">
        <a href="../html/menu.html" class="btn-regresar">← Volver al Menú</a>
        <h2>MERCH</h2>
    </div>

    <a href="../html/sesion.html" class="cerrar">Cerrar Sesión</a>

</div>


<!-- ==================================================
     REGISTRAR NUEVA VENTA
================================================== -->

<div class="seccion-panel">

    <h3>Registrar Nueva Venta</h3>

    <form action="registrar_venta.php" method="POST"
      class="form-horizontal"
      onsubmit="return validarVenta('');">

        <!-- BUSCAR CLIENTE -->

        <div class="input-group">

            <label for="buscar_cli">Buscar Cliente (Clave o Nombre)</label>

            <input
                type="text"
                id="buscar_cli"
                placeholder="Escribe la clave o nombre..."
                onchange="recuperarDatos(
                    'cliente',
                    this.value,
                    'clave',
                    'nombres'
                )"
                required
            >

        </div>

        <div class="input-group" style="max-width:110px;">

            <label for="clave">Clave Cli.</label>

            <input
                type="text"
                name="clave"
                id="clave"
                readonly
                required
                style="background-color:#ededed;"
            >

        </div>

        <div class="input-group">

            <label for="nombres">Nombre Cliente</label>

            <input
                type="text"
                name="nombres"
                id="nombres"
                readonly
                required
                style="background-color:#ededed;"
            >

        </div>


        <!-- BUSCAR PRODUCTO -->

        <div class="input-group">

            <label for="buscar_pro">Buscar Producto (Clave o Nombre)</label>

            <input
                type="text"
                id="buscar_pro"
                placeholder="Escribe la clave o nombre..."
                onchange="recuperarDatos(
                    'producto',
                    this.value,
                    'clave_pro',
                    'nombre_pro',
                    'total'
                )"
                required
            >

        </div>

        <div class="input-group" style="max-width:120px;">

            <label for="clave_pro">Clave Pro.</label>

            <input
                type="text"
                name="clave_pro"
                id="clave_pro"
                readonly
                required
                style="background-color:#ededed;"
            >

        </div>

        <div class="input-group">

            <label for="nombre_pro">Nombre Producto</label>

            <input
                type="text"
                name="nombre"
                id="nombre_pro"
                readonly
                required
                style="background-color:#ededed;"
            >

        </div>


        <!-- TOTAL -->

        <div class="input-group" style="max-width:140px;">

            <label for="total">Total ($)</label>

            <input
                type="number"
                step="0.01"
                min="0.01"
                name="total"
                id="total"
                required
            >

        </div>

        <button type="submit" class="btn-registrar">
            Registrar
        </button>

    </form>

</div>


<!-- ==================================================
     BUSCAR HISTORIAL
================================================== -->

<div class="seccion-panel">

    <h3>Buscar Registro de Ventas</h3>

    <form action="ventas.php" method="GET" class="form-horizontal">

        <div class="input-group">

            <label for="buscar">Buscar por ID, Cliente o Producto</label>

            <input
                type="text"
                name="buscar"
                id="buscar"
                placeholder="Ej. Juan, Playera o 1"
                value="<?php echo e($_GET['buscar'] ?? ''); ?>"
                required
            >

        </div>

        <button type="submit" class="btn-buscar">
            Buscar
        </button>

    </form>


    <?php if ($busqueda_realizada): ?>

        <?php if (count($resultados) > 0): ?>

            <table class="tabla-resultados">

                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Clave Cli.</th>
                        <th>Cliente</th>
                        <th>Clave Pro.</th>
                        <th>Producto</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($resultados as $venta): ?>

                        <tr>

                            <td><strong><?php echo (int)$venta['id']; ?></strong></td>

                            <td><?php echo e($venta['clave']); ?></td>

                            <td><?php echo e($venta['nombres']); ?></td>

                            <td><?php echo e($venta['clave_pro']); ?></td>

                            <td><?php echo e($venta['nombre']); ?></td>

                            <td>
                                $<?php echo number_format((float)$venta['total'], 2); ?>
                            </td>

                            <td class="acciones-celda">

                                <a
                                    href="ventas.php?buscar=<?php echo urlencode($buscar); ?>&editar_id=<?php echo (int)$venta['id']; ?>"
                                    class="btn-accion btn-modificar"
                                    style="text-decoration:none;"
                                >
                                    Modificar
                                </a>

                                <a
                                    href="eliminar_venta.php?id=<?php echo (int)$venta['id']; ?>"
                                    class="btn-accion btn-eliminar"
                                    style="text-decoration:none;"
                                    onclick="return confirm('¿Estás seguro de eliminar esta venta?');"
                                >
                                    Eliminar
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p style="margin-top:20px;color:var(--text-muted);font-weight:600;">
                No se encontraron ventas registradas.
            </p>

        <?php endif; ?>

    <?php endif; ?>

</div>


<!-- ==================================================
     MODIFICAR VENTA
================================================== -->

<?php if ($venta_editar): ?>

<div class="seccion-panel panel-modificar">

    <h3>Modificar Venta (ID: <?php echo (int)$venta_editar['id']; ?>)</h3>

    <form action="actualizar_venta.php" method="POST"
      class="form-horizontal"
      onsubmit="return validarVenta('_edit');">

        <input type="hidden" name="id" value="<?php echo (int)$venta_editar['id']; ?>">

        <!-- CLIENTE -->

        <div class="input-group">

            <label for="buscar_cli_edit">Buscar / Cambiar Cliente</label>

            <input
                type="text"
                id="buscar_cli_edit"
                placeholder="Escribe la clave o nombre..."
                onchange="recuperarDatos(
                    'cliente',
                    this.value,
                    'edit_clave',
                    'edit_nombres'
                )"
            >

        </div>

        <div class="input-group" style="max-width:110px;">

            <label for="edit_clave">Clave Cli.</label>

            <input
                type="text"
                name="clave"
                id="edit_clave"
                value="<?php echo e($venta_editar['clave']); ?>"
                readonly
                required
                style="background-color:#ededed;"
            >

        </div>

        <div class="input-group">

            <label for="edit_nombres">Nombre Cliente</label>

            <input
                type="text"
                name="nombres"
                id="edit_nombres"
                value="<?php echo e($venta_editar['nombres']); ?>"
                readonly
                required
                style="background-color:#ededed;"
            >

        </div>


        <!-- PRODUCTO -->

        <div class="input-group">

            <label for="buscar_pro_edit">Buscar / Cambiar Producto</label>

            <input
                type="text"
                id="buscar_pro_edit"
                placeholder="Escribe la clave o nombre..."
                onchange="recuperarDatos(
                    'producto',
                    this.value,
                    'edit_clave_pro',
                    'edit_nombre_pro',
                    'edit_total'
                )"
            >

        </div>

        <div class="input-group" style="max-width:120px;">

            <label for="edit_clave_pro">Clave Pro.</label>

            <input
                type="text"
                name="clave_pro"
                id="edit_clave_pro"
                value="<?php echo e($venta_editar['clave_pro']); ?>"
                readonly
                required
                style="background-color:#ededed;"
            >

        </div>

        <div class="input-group">

            <label for="edit_nombre_pro">Nombre Producto</label>

            <input
                type="text"
                name="nombre"
                id="edit_nombre_pro"
                value="<?php echo e($venta_editar['nombre']); ?>"
                readonly
                required
                style="background-color:#ededed;"
            >

        </div>


        <!-- TOTAL -->

        <div class="input-group" style="max-width:140px;">

            <label for="edit_total">Total ($)</label>

            <input
                type="number"
                step="0.01"
                min="0.01"
                name="total"
                id="edit_total"
                value="<?php echo e($venta_editar['total']); ?>"
                required
            >

        </div>

        <button type="submit" class="btn-registrar">
            Guardar Cambios
        </button>

        <a
            href="ventas.php"
            class="btn-accion btn-eliminar"
            style="text-decoration:none;padding:12px 18px;"
        >
            Cancelar
        </a>

    </form>

</div>

<?php endif; ?>


<!-- ==================================================
     JAVASCRIPT
================================================== -->

<script>

// Recuperar cliente o producto desde PHP


async function recuperarDatos(
    tipo,
    valor,
    campoClave,
    campoNombre,
    campoTotal = null
) {

    valor = valor.trim();

    if (valor === "") return;

    const claveInput = document.getElementById(campoClave);
    const nombreInput = document.getElementById(campoNombre);

    const totalInput = campoTotal
        ? document.getElementById(campoTotal)
        : null;

    claveInput.value = "";
    nombreInput.value = "";

    if (totalInput) {
        totalInput.value = "";
    }

    try {

        const respuesta = await fetch(
            `buscar_autocompletar.php?tipo=${encodeURIComponent(tipo)}&buscar=${encodeURIComponent(valor)}`
        );

        const data = await respuesta.json();

        if (!respuesta.ok) {
            throw new Error(data.mensaje || "Error en PHP");
        }

        if (data.exito) {

            claveInput.value = data.clave;
            nombreInput.value = data.nombre;

            if (totalInput && data.precio !== undefined) {
                totalInput.value = data.precio;
            }

        } else {

            alert(data.mensaje || "No se encontraron resultados.");

        }

    } catch (error) {

        console.error("Error:", error);

        alert("Error al recuperar datos: " + error.message);

    }
}



// Validar que los datos recuperados existan

function validarVenta(sufijo) {

    let clave, nombres, clavePro, nombrePro, total;

    if (sufijo === '_edit') {

        clave = document.getElementById('edit_clave').value;
        nombres = document.getElementById('edit_nombres').value;
        clavePro = document.getElementById('edit_clave_pro').value;
        nombrePro = document.getElementById('edit_nombre_pro').value;
        total = parseFloat(document.getElementById('edit_total').value);

    } else {

        clave = document.getElementById('clave').value;
        nombres = document.getElementById('nombres').value;
        clavePro = document.getElementById('clave_pro').value;
        nombrePro = document.getElementById('nombre_pro').value;
        total = parseFloat(document.getElementById('total').value);

    }

    if (!clave || !nombres) {
        alert("Debes seleccionar un cliente válido.");
        return false;
    }

    if (!clavePro || !nombrePro) {
        alert("Debes seleccionar un producto válido.");
        return false;
    }

    if (!Number.isFinite(total) || total <= 0) {
        alert("Ingresa un total válido.");
        return false;
    }

    return true;
}

</script>

</body>
</html>
