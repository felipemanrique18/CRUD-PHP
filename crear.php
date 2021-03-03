<?php

    include 'funciones.php';
    $config = include 'config.php';
    csrf();

    if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die();
    }
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);
    
    try {
        $consultaSQL = "SELECT * FROM estudiantes";
        $sentencia = $conexion->prepare($consultaSQL);
        $sentencia->execute();
        $estudiantes = $sentencia->fetchAll();
    } catch(PDOException $error) {
        $error= $error->getMessage();
    }

    if (isset($_POST['submit'])) {
        $resultado = [
            'error' => false,
            'mensaje' => 'El acompañante ' . escapar($_POST['nombres']) . ' ha sido agregado con éxito'
        ];
        try {
            $acudiente = array(
            "nombres"   => $_POST['nombres'],
            "apellidos" => $_POST['apellidos'],
            "identificacion" => $_POST['identificacion'],
            "direccion" => $_POST['direccion'],
            "cuidad" => $_POST['cuidad'],
            "telefono" => $_POST['telefono'],
            "estudiantes_id" => $_POST['estudiante'],
            );

            $consultaSQL = "SELECT * FROM acudientes_estudiantes WHERE identificacion =".$_POST['identificacion'];
            $sentencia = $conexion->prepare($consultaSQL);
            $sentencia->execute();
            $acudiente_exis = $sentencia->fetch(PDO::FETCH_ASSOC);

            if (is_countable($acudiente_exis)>0) {
              $resultado['error'] = true;
              $resultado['mensaje'] = 'El numero de identificacion ya existe';
            }else{
              $consultaSQL = "INSERT INTO acudientes_estudiantes (nombres, apellidos, identificacion, direccion,cuidad,telefono,estudiantes_id) values (:" . implode(", :", array_keys($acudiente)) . ")";
              $sentencia = $conexion->prepare($consultaSQL);
              $sentencia->execute($acudiente);
              header('Location: index.php');
            }
        } catch(PDOException $error) {
            $resultado['error'] = true;
            $resultado['mensaje'] = $error->getMessage();
        }
    }
?>

<?php include 'templates/header.php'; ?>

<?php
if (isset($resultado)) {
  ?>
  <div class="container mt-3">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-<?= $resultado['error'] ? 'danger' : 'success' ?>" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mt-4">Crear un Acudiente</h2>
      <hr>
      <form method="post">
        <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
        <div class="form-group">
          <label for="nombres">Nombre</label>
          <input type="text" name="nombres" id="nombres" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="apellidos">Apellido</label>
          <input type="text" name="apellidos" id="apellidos" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="identificacion">identificacion</label>
          <input type="number" name="identificacion"
           id="identificacion" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="direccion">direccion</label>
          <input type="text" name="direccion" id="direccion" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="cuidad">cuidad</label>
          <input type="text" name="cuidad" id="cuidad" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="telefono">telefono</label>
          <input type="number" name="telefono" id="telefono" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="estudiante">estudiante</label>
            <select name="estudiante" id="estudiante" class="form-control">
                <option value="">Selecciona un estudiante</option>

                <?php
                    if ($estudiantes) {
                        foreach ($estudiantes as $fila) {
                        ?>
                            <option value="<?php echo escapar($fila["id"]); ?>"><?php echo escapar($fila["nombre"]); ?></option>
                        <?php
                        }
                    }
                ?>
          </select>
        </div>
        <div class="form-group">
          <input type="submit" name="submit" class="btn btn-primary" value="Enviar">
          <a class="btn btn-primary" href="index.php">Regresar al inicio</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'templates/footer.php'; ?>