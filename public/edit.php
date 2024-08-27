<?php
session_start();
// Verifica que el usuario haya iniciado sesión
if (isset($_SESSION['user_id'])) {
  if (isset($_GET['id'])) {
    $taskId = $_GET['id'];

    // Sanitización del ID de la tarea (importante para prevenir inyección SQL)
    $taskId = filter_var($taskId, FILTER_SANITIZE_NUMBER_INT);
    //Conexión de la BD
    require('../config.php');

    // Consulta a la base de datos para obtener los datos de la tarea
    $sql = "SELECT title, description, status FROM tasks WHERE id = $taskId AND user_id = {$_SESSION['user_id']}";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
      $row = $result->fetch_assoc();
      $title = $row['title'];
      $description = $row['description'];
      $status = $row['status'];
    } else {
      // No se encuentra la tarea o no pertenece al usuario
      echo "<script>alert('Tarea no encontrada o no tienes permiso para editarla.'); window.location.href = 'index.php';</script>";
      exit;
    }

    $conn->close();
  } else {
    // No se proporcionó el ID de la tarea
    echo "<script>alert('No se especificó la tarea a editar.'); window.location.href = 'index.php';</script>";
  }
} else {
  // No se inicio sesión
  echo "<script>alert('Primero debes de iniciar sesióon.'); window.location.href = 'login.php';</script>"; // Redirige al usuario a la página de inicio de sesión
  exit;
}

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lista de tareas </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
  <!-- Barra de navegacion -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Lista de pendientes</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="index.php">Inicio</a>
          </li>
        </ul>
        <span class="navbar-text">
          <div class="row float-end">
            <a style="width: auto;" href="logout.php" class="btn btn-danger text-white">Desconectarse</a>
          </div>
        </span>
      </div>
    </div>
  </nav>
  <!-- Barra de navegacion -->

  <div class="container pb-3">
    <div class="row">
      <div class="col-sm-6 offset-sm-3">
        <form action="../src/Controller/TaskController.php/editTask" method="POST">
          <div class="text-center">
            <h2>Modificar Tarea</h2>
          </div>
          <!-- Llenando los campos del edit -->
          <input type="hidden" name="task_id" value="<?php echo $taskId; ?>"> <label class="form-label" for="title">Titulo</label>
          <input type="text" name="title" id="title" class="form-control" value="<?php echo $title; ?>" required />

          <label class="form-label" for="description">Descripción</label>
          <input type="text" name="description" id="description" class="form-control"  
            value="<?php echo $description; ?>" required />

          <label class="form-label" for="status">Estado:</label>
          <select class="form-control mb-3" id="status" name="status">
            <option value="pending" <?php if ($status == 'pending') echo 'selected'; ?>>Pendiente</option>
            <option value="in_progress" <?php if ($status == 'in_progress') echo 'selected'; ?>>En progreso</option>
            <option value="completed" <?php if ($status == 'completed') echo 'selected'; ?>>Completado</option>
          </select>

          <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal" onclick="window.location.href='index.php'">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
      </div>
    </div>
  </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white text-center py-2">
    <p>
      &copy; Copyright 2024. Todos los derechos reservados. |
      <a href="https://cuakxd.com/JonathanCordero/" target="_blank" class="text-white">Desarrollado por Jonathan Cordero</a>
    </p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>