<?php
    include 'funciones.php';

    $error = false;
    $config = include 'config.php';
    csrf();

    if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die();
    }

    try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);
    
    if (isset($_POST['numero_busqueda'])) {
        if ($_POST['tipo_busqueda']=='ced') {
            $consultaSQL = "SELECT ae.*,e.id as id_estudiante,e.nombre as nombre_estudiante,e.identificacion as identificacion_estudiante  FROM acudientes_estudiantes as ae LEFT JOIN estudiantes AS e ON ae.estudiantes_id = e.id WHERE ae.identificacion LIKE '%" . $_POST['numero_busqueda'] . "%'";
        }else{
            $consultaSQL = "SELECT ae.*,e.id as id_estudiante,e.nombre as nombre_estudiante,e.identificacion as identificacion_estudiante  FROM acudientes_estudiantes as ae LEFT JOIN estudiantes AS e ON ae.estudiantes_id = e.id WHERE e.identificacion LIKE '%" . $_POST['numero_busqueda'] . "%'";
        }
    } else {
        $consultaSQL = "SELECT ae.*,e.id as id_estudiante,e.nombre as nombre_estudiante,e.identificacion as identificacion_estudiante  FROM acudientes_estudiantes as ae LEFT JOIN estudiantes AS e ON ae.estudiantes_id = e.id";
    }
    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute();

    $acudientes = $sentencia->fetchAll();

    } catch(PDOException $error) {
    $error= $error->getMessage();
    }
$titulo = isset($_POST['numero_busqueda']) ? 'Lista de Acudientes (' . $_POST['numero_busqueda'] . ')' : 'Lista de Acudientes';
?>

<?php include "templates/header.php"; ?>

<?php
if ($error) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $error ?>
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
      <a href="crear.php"  class="btn btn-primary mt-4">Crear acompañante</a>
      <a href="reporte.php"  class="btn btn-primary mt-4 float-right">Reporte</a>
      <hr>
      <h2 class="mt-3">Buscar</h2>
      <form method="post" class="form-inline">

        <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
        <div class="form-group mr-3">
            <select name="tipo_busqueda" id="tipo_busqueda" class="form-control">
              <option value="ced">Cedula del acudiente</option>
              <option value="registro">identificacion del estudiante</option>
            </select>
            <input type="text" id="numero_busqueda" name="numero_busqueda" placeholder="Numero..." class="form-control">
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Ver resultados</button>
      </form>
      
    </div>
  </div>
</div>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="mt-3"><?= $titulo ?></h2>
        <div class="table-responsive" >
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>identificacion acudiente</th>
                    <th>estudiante</th>
                    <th>identificion estudiante</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($acudientes && $sentencia->rowCount() > 0) {
                    foreach ($acudientes as $fila) {
                    ?>
                    <tr>
                        <td><?php echo escapar($fila["id"]); ?></td>
                        <td><?php echo escapar($fila["nombres"]); ?></td>
                        <td><?php echo escapar($fila["apellidos"]); ?></td>
                        <td><?php echo escapar($fila["identificacion"]); ?></td>
                        <td><?php echo escapar($fila["nombre_estudiante"]); ?></td>
                        <td><?php echo escapar($fila["identificacion_estudiante"]); ?></td>
                        <td>
                            <a href="<?= 'borrar.php?id=' . escapar($fila["id"]) ?>">🗑️Borrar</a>
                            <a href="<?= 'editar.php?id=' . escapar($fila["id"]) ?>">✏️Editar</a>
                        </td>
                    </tr>
                    <?php
                    }
                }
                ?>
                <tbody>
            </table>
        </div>
    </div>
  </div>
</div>

<?php include "templates/footer.php"; ?>