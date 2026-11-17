<?php
include("../php/conexion.php");

// Lógica para BUSCAR productos
$resultados = [];
$busqueda_realizada = false;

if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))) {
    $busqueda_realizada = true;
    $termino = "%" . trim($_GET['buscar']) . "%";
    $clave_exacta = trim($_GET['buscar']);

    // Busca por nombre o por la clave exacta del producto
    $sql = "SELECT * FROM productos WHERE nombre LIKE ? OR clave_pro = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $termino, $clave_exacta);
    $stmt->execute();
    $res = $stmt->get_result();
    
    while ($fila = $res->fetch_assoc()) {
        $resultados[] = $fila;
    }
    $stmt->close();
}

// Lógica para cargar datos si se presionó "Modificar"
$producto_editar = null;
if (isset($_GET['editar_clave'])) {
    $clave_editar = $_GET['editar_clave'];
    $sql_edit = "SELECT * FROM productos WHERE clave_pro = ?";
    $stmt_edit = $conexion->prepare($sql_edit);
    $stmt_edit->bind_param("s", $clave_editar);
    $stmt_edit->execute();
    $res_edit = $stmt_edit->get_result();
    $producto_editar = $res_edit->fetch_assoc();
    $stmt_edit->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - MERCH</title>
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

    <!-- 1. APARTADO DE REGISTRAR PRODUCTOS -->
    <div class="seccion-panel">
        <h3>Registrar Nuevo Producto</h3>
        <form action="registrar_producto.php" method="POST" class="form-horizontal">
            <div class="input-group" style="max-width: 150px;">
                <label for="clave_pro">Clave Producto</label>
                <input type="text" name="clave_pro" id="clave_pro" required>
            </div>
            <div class="input-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" required>
            </div>
            <div class="input-group" style="max-width: 130px;">
                <label for="prec">Precio Compra</label>
                <input type="number" step="0.01" name="prec" id="prec" required>
            </div>
            <div class="input-group" style="max-width: 130px;">
                <label for="prev">Precio Venta</label>
                <input type="number" step="0.01" name="prev" id="prev" required>
            </div>
            <div class="input-group">
                <label for="categoria">Categoría</label>
                <input type="text" name="categoria" id="categoria" required>
            </div>
            <div class="input-group" style="flex: 2; min-width: 250px;">
                <label for="descripcion">Descripción</label>
                <input type="text" name="descripcion" id="descripcion" required>
            </div>
            <button type="submit" class="btn-registrar">Registrar</button>
        </form>
    </div>

    <!-- 2. APARTADO DE BUSCAR PRODUCTOS -->
    <div class="seccion-panel">
        <h3>Buscar Producto</h3>
        <form action="productos.php" method="GET" class="form-horizontal">
            <div class="input-group">
                <label for="buscar">Buscar por Nombre o Clave</label>
                <input type="text" name="buscar" id="buscar" placeholder="Ej. Playera Billie o P01" value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>" required>
            </div>
            <button type="submit" class="btn-buscar">Buscar</button>
        </form>

        <!-- TABLA DE RESULTADOS -->
        <?php if ($busqueda_realizada): ?>
            <?php if (count($resultados) > 0): ?>
                <table class="tabla-resultados">
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Nombre</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $producto): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($producto['clave_pro']); ?></strong></td>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td>$<?php echo number_format($producto['prec'], 2); ?></td>
                                <td>$<?php echo number_format($producto['prev'], 2); ?></td>
                                <td><?php echo htmlspecialchars($producto['categoria']); ?></td>
                                <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                                <td class="acciones-celda">
                                    <a href="productos.php?buscar=<?php echo urlencode($_GET['buscar']); ?>&editar_clave=<?php echo urlencode($producto['clave_pro']); ?>" class="btn-accion btn-modificar" style="text-decoration:none;">Modificar</a>
                                    <a href="php/eliminar_producto.php?clave=<?php echo urlencode($producto['clave_pro']); ?>" class="btn-accion btn-eliminar" style="text-decoration:none;" onclick="return confirm('¿Estás seguro de eliminar este producto?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="margin-top: 20px; color: var(--text-muted); font-weight: 600;">No se encontraron productos con ese criterio.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- 3. PANEL DINÁMICO PARA MODIFICAR -->
    <?php if ($producto_editar): ?>
        <div class="seccion-panel panel-modificar">
            <h3>Modificar Producto (Clave: <?php echo htmlspecialchars($producto_editar['clave_pro']); ?>)</h3>
            <form action="actualizar_producto.php" method="POST" class="form-horizontal">
                <!-- Llave primaria oculta para el WHERE -->
                <input type="hidden" name="clave_pro" value="<?php echo htmlspecialchars($producto_editar['clave_pro']); ?>">
                
                <div class="input-group">
                    <label for="edit_nombre">Nombre</label>
                    <input type="text" name="nombre" id="edit_nombre" value="<?php echo htmlspecialchars($producto_editar['nombre']); ?>" required>
                </div>
                <div class="input-group" style="max-width: 130px;">
                    <label for="edit_prec">Precio Compra</label>
                    <input type="number" step="0.01" name="prec" id="edit_prec" value="<?php echo $producto_editar['prec']; ?>" required>
                </div>
                <div class="input-group" style="max-width: 130px;">
                    <label for="edit_prev">Precio Venta</label>
                    <input type="number" step="0.01" name="prev" id="edit_prev" value="<?php echo $producto_editar['prev']; ?>" required>
                </div>
                <div class="input-group">
                    <label for="edit_categoria">Categoría</label>
                    <input type="text" name="categoria" id="edit_categoria" value="<?php echo htmlspecialchars($producto_editar['categoria']); ?>" required>
                </div>
                <div class="input-group" style="flex: 2; min-width: 250px;">
                    <label for="edit_descripcion">Descripción</label>
                    <input type="text" name="descripcion" id="edit_descripcion" value="<?php echo htmlspecialchars($producto_editar['descripcion']); ?>" required>
                </div>
                <button type="submit" class="btn-registrar">Guardar Cambios</button>
                <a href="productos.php" class="btn-accion btn-eliminar" style="text-decoration:none; padding: 12px 18px;">Cancelar</a>
            </form>
        </div>
    <?php endif; ?>

</body>
</html>
