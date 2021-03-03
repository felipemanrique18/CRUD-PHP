<?php
include 'funciones.php';

$config = include 'config.php';
csrf();

if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  die();
}
$resultado = [
  'error' => false,
  'mensaje' => ''
];

if (!isset($_GET['id'])) {
  $resultado['error'] = true;
  $resultado['mensaje'] = 'El acudiente no existe';
}

if (isset($_POST['submit'])) {
  try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $id = $_GET['id'];
    $consultaSQL = "SELECT ae.*,e.id as id_estudiante,e.nombre as nombre_estudiante FROM acudientes_estudiantes as ae LEFT JOIN estudiantes AS e ON ae.estudiantes_id = e.id WHERE ae.id =" . $id;

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute();
    $acudiente = $sentencia->fetch(PDO::FETCH_ASSOC);
    
    $consultaSQL = "SELECT * FROM acudientes_estudiantes WHERE identificacion =".$_POST['identificacion'];
    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute();
    $acudiente_exis = $sentencia->fetch(PDO::FETCH_ASSOC);

    if (is_countable($acudiente_exis)>0 && $_POST['identificacion']!=$acudiente['identificacion']) {
      $resultado['error'] = true;
      $resultado['mensaje'] = 'El numero de identificacion ya existe';
    }else{
      $acudiente = [
        "id"=> $_GET['id'],
        "nombre" => $_POST['nombre'],
        "apellido" => $_POST['apellido'],
        "identificacion" => $_POST['identificacion'],
        "direccion" => $_POST['direccion'],
        "cuidad" => $_POST['cuidad'],
        "telefono" => $_POST['telefono'],
        "estudiantes_id" => $_POST['estudiante'],
        
      ];
      $consultaSQL = "UPDATE acudientes_estudiantes SET
        nombres = :nombre,
        apellidos = :apellido,
        identificacion=:identificacion,
        direccion=:direccion,
        cuidad=:cuidad,
        telefono=:telefono,
        estudiantes_id=:estudiantes_id
        WHERE id = :id";
    
        $consulta = $conexion->prepare($consultaSQL);
        $consulta->execute($acudiente);
        header('Location: index.php');
    }
    

  } catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
  }
}

try {
  $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
  $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);
    
  $id = $_GET['id'];
  $consultaSQL = "SELECT ae.*,e.id as id_estudiante,e.nombre as nombre_estudiante FROM acudientes_estudiantes as ae LEFT JOIN estudiantes AS e ON ae.estudiantes_id = e.id WHERE ae.id =" . $id;

  $sentencia = $conexion->prepare($consultaSQL);
  $sentencia->execute();

  $acudiente = $sentencia->fetch(PDO::FETCH_ASSOC);

  if (!$acudiente) {
    $resultado['error'] = true;
    $resultado['mensaje'] = 'No se ha encontrado el acudiente';
  }

} catch(PDOException $error) {
  $resultado['error'] = true;
  $resultado['mensaje'] = $error->getMessage();
}
?>

<?php require "templates/header.php"; ?>

<?php
if ($resultado['error']) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
          <?= $resultado['mensaje'] ?>
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($_POST['submit']) && !$resultado['error']) {
  ?>
  <div class="container mt-2">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-success" role="alert">
          El acudiente ha sido actualizado correctamente
        </div>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php
if (isset($acudiente) && $acudiente) {
  ?>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="mt-4">Editando el acudiente <?= escapar($acudiente['nombres']) . ' ' . escapar($acudiente['apellidos'])  ?></h2>
        <hr>
        <form method="post">
          <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
          <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="<?= escapar($acudiente['nombres']) ?>" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido" value="<?= escapar($acudiente['apellidos']) ?>" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="identificacion">identificacion</label>
            <input type="number" name="identificacion"
            id="identificacion" class="form-control" value="<?= escapar($acudiente['identificacion']) ?>" required>
          </div>
          <div class="form-group">
            <label for="direccion">direccion</label>
            <input type="text" name="direccion" id="direccion" value="<?= escapar($acudiente['direccion']) ?>" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="cuidad">cuidad</label>
            <input type="text" name="cuidad" id="cuidad" value="<?= escapar($acudiente['cuidad']) ?>" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="telefono">telefono</label>
            <input type="number" name="telefono" id="telefono" value="<?= escapar($acudiente['telefono']) ?>" class="form-control" required>
          </div>
          <div class="form-group">
              <label for="estudiante">estudiante</label>
              <select name="estudiante" id="estudiante" class="form-control">
                  <option value="<?php echo escapar($acudiente["id_estudiante"]); ?>"><?php echo escapar($acudiente["nombre_estudiante"]); ?></option>
                  
                  <?php
                      if ($estudiantes && $sentencia->rowCount() > 0) {
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
            <input type="submit" name="submit" class="btn btn-primary" value="Actualizar">
            <a class="btn btn-primary" href="index.php">Regresar al inicio</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php
}
?>

<?php require "templates/footer.php"; ?>