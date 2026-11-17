<?php
include("../php/conexion.php");

// Lógica para BUSCAR clientes
$resultados = [];
$busqueda_realizada = false;

if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))) {
    $busqueda_realizada = true;
    $termino = "%" . trim($_GET['buscar']) . "%";
    $id_exacto = trim($_GET['buscar']);

    $sql = "SELECT * FROM clientes WHERE nombres LIKE ? OR clave = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $termino, $id_exacto);
    $stmt->execute();
    $res = $stmt->get_result();
    
    while ($fila = $res->fetch_assoc()) {
        $resultados[] = $fila;
    }
    $stmt->close();
}

// Lógica para cargar datos si se presionó "Modificar"
$cliente_editar = null;
if (isset($_GET['editar_id'])) {
    $id_editar = intval($_GET['editar_id']);
    $sql_edit = "SELECT * FROM clientes WHERE clave = ?";
    $stmt_edit = $conexion->prepare($sql_edit);
    $stmt_edit->bind_param("i", $id_editar);
    $stmt_edit->execute();
    $res_edit = $stmt_edit->get_result();
    $cliente_editar = $res_edit->fetch_assoc();
    $stmt_edit->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - MERCH</title>
    <!-- Asegúrate de que la ruta a tu CSS sea la correcta -->
    <link rel="stylesheet" href="../css/clientes.css">
</head>
<body>

        <!-- ENCABEZADO -->
    <div class="encabezado">
        <div class="izq-header">
            <a href="../html/menu.html" class="btn-regresar">← Volver al Menú</a>
            <h2>MERCH</h2>
        </div>
        <a href="html/sesion.html" class="cerrar">Cerrar Sesión</a>
    </div>


    <!-- 1. APARTADO DE REGISTRAR CLIENTES -->
    <div class="seccion-panel">
        <h3>Registrar Nuevo Cliente</h3>
        <form action="registrar_cliente.php" method="POST" class="form-horizontal">
            <div class="input-group">
                <label for="nombres">Nombre Completo</label>
                <input type="text" name="nombres" id="nombres" required>
            </div>
            <div class="input-group" style="max-width: 100px;">
                <label for="edad">Edad</label>
                <input type="number" name="edad" id="edad" required>
            </div>
            <div class="input-group">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" required>
            </div>
            <div class="input-group">
                <label for="correo">Correo Electrónico</label>
                <input type="email" name="correo" id="correo" required>
            </div>
            <div class="input-group">
                <label for="direccion">Dirección</label>
                <input type="text" name="direccion" id="direccion" required>
            </div>
            <button type="submit" class="btn-registrar">Registrar</button>
        </form>
    </div>

    <!-- 2. APARTADO DE BUSCAR CLIENTES -->
    <div class="seccion-panel">
        <h3>Buscar Cliente</h3>
        <form action="clientes.php" method="GET" class="form-horizontal">
            <div class="input-group">
                <label for="buscar">Buscar por Nombre o ID</label>
                <input type="text" name="buscar" id="buscar" placeholder="Ej. Juan Pérez o 1" value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>" required>
            </div>
            <button type="submit" class="btn-buscar">Buscar</button>
        </form>

        <!-- TABLA DE RESULTADOS -->
        <?php if ($busqueda_realizada): ?>
            <?php if (count($resultados) > 0): ?>
                <table class="tabla-resultados">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $cliente): ?>
                            <tr>
                                <td><?php echo $cliente['clave']; ?></td>
                                <td><?php echo htmlspecialchars($cliente['nombres']); ?></td>
                                <td><?php echo $cliente['edad']; ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['correo']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['direccion']); ?></td>
                                <td class="acciones-celda">
                                    <!-- Botón Modificar: Recarga pasando el ID a editar -->
                                    <a href="clientes.php?buscar=<?php echo urlencode($_GET['buscar']); ?>&editar_id=<?php echo $cliente['clave']; ?>" class="btn-accion btn-modificar" style="text-decoration:none;">Modificar</a>
                                    
                                    <!-- Botón Eliminar -->
                                    <a href="php/eliminar_cliente.php?id=<?php echo $cliente['clave']; ?>" class="btn-accion btn-eliminar" style="text-decoration:none;" onclick="return confirm('¿Estás seguro de eliminar este cliente?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="margin-top: 20px; color: var(--text-muted); font-weight: 600;">No se encontraron clientes con ese dato.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- 3. PANEL DINÁMICO PARA MODIFICAR (Solo aparece al dar clic en Modificar) -->
    <?php if ($cliente_editar): ?>
        <div class="seccion-panel panel-modificar" id="panel-editar">
            <h3>Modificar Datos del Cliente (ID: <?php echo $cliente_editar['clave']; ?>)</h3>
            <form action="actualizar_cliente.php" method="POST" class="form-horizontal">
                <!-- Campo oculto con el ID -->
                <input type="hidden" name="id" value="<?php echo $cliente_editar['clave']; ?>">
                
                <div class="input-group">
                    <label for="edit_nombres">Nombre Completo</label>
                    <input type="text" name="nombres" id="edit_nombres" value="<?php echo htmlspecialchars($cliente_editar['nombres']); ?>" required>
                </div>
                <div class="input-group" style="max-width: 100px;">
                    <label for="edit_edad">Edad</label>
                    <input type="number" name="edad" id="edit_edad" value="<?php echo $cliente_editar['edad']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="edit_telefono">Teléfono</label>
                    <input type="text" name="telefono" id="edit_telefono" value="<?php echo htmlspecialchars($cliente_editar['telefono']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="edit_correo">Correo Electrónico</label>
                    <input type="email" name="correo" id="edit_correo" value="<?php echo htmlspecialchars($cliente_editar['correo']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="edit_direccion">Dirección</label>
                    <input type="text" name="direccion" id="edit_direccion" value="<?php echo htmlspecialchars($cliente_editar['direccion']); ?>" required>
                </div>
                <button type="submit" class="btn-registrar">Guardar Cambios</button>
                <a href="clientes.php" class="btn-accion btn-eliminar" style="text-decoration:none; padding: 12px 18px;">Cancelar</a>
            </form>
        </div>
    <?php endif; ?>

</body>
</html>
