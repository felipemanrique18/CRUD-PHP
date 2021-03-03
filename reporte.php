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
    
    $consultaSQL = "SELECT ae.*,e.id as id_estudiante,e.nombre as nombre_estudiante,e.identificacion as identificacion_estudiante,e.apellido as apellido_estudiante  FROM acudientes_estudiantes as ae LEFT JOIN estudiantes AS e ON ae.estudiantes_id = e.id";
    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute();

    $acudientes = $sentencia->fetchAll();

    } catch(PDOException $error) {
    $error= $error->getMessage();
    }
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
      <h2 class="mt-3">Todos los acudientes</h2>
      <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>ciudad</th>
            <th>telefono</th>
            <th>identificacion acudiente</th>
            <th>estudiante</th>
            <th>identificion estudiante</th>
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
                <td><?php echo escapar($fila["cuidad"]); ?></td>
                <td><?php echo escapar($fila["telefono"]); ?></td>
                <td><?php echo escapar($fila["identificacion"]); ?></td>
                <td><?php echo escapar($fila["nombre_estudiante"]); ?> <?php echo escapar($fila["apellido_estudiante"]); ?></td>
                <td><?php echo escapar($fila["identificacion_estudiante"]); ?></td>
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

<?php include "templates/footer.php"; ?>