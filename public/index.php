<?php
session_start();
// Verifica que el usuario haya iniciado sesión
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  // 1. Incluir el archivo de configuración (config.php)
  require('../config.php');

  // 2. Consulta a la base de datos para obtener las tareas del usuario
  $sql = "SELECT id, title, description, status FROM tasks WHERE user_id = $user_id";
  $result = $conn->query($sql);

  // 3. Verificar si se envió una solicitud de eliminación
  if (isset($_POST['delete_task']) && isset($_POST['task_id'])) {
    $taskId = $_POST['task_id'];

    // 4. Sanitización del ID de la tarea
    $taskId = $conn->real_escape_string($taskId);

    // 5. Consulta SQL para eliminar la tarea
    $sql = "DELETE FROM tasks WHERE id = $taskId AND user_id = $user_id";

    if ($conn->query($sql) === TRUE) {
      // Tarea eliminada exitosamente, recarga la página
      echo "<script>alert('Tarea eliminada exitosamente.'); window.location.href = 'index.php';</script>";
      exit;
    } else {
      // Error al eliminar la tarea
      echo "<script>alert('Error al eliminar la tarea: " . $conn->error . "');</script>";
    }
  }
} else {
  // Redirige al usuario a la página de inicio de sesión si no se ha logeado
  header("Location: login.php");
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
      <a class="navbar-brand" href="#">Lista de pendientes</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">Inicio</a>
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

  <div class="container">
    <div class="row mt-3 mb-3 text-center">
      <h1> Sistema de gestión de tareas</h1>
      <div class="col-md-12 text-end">
        <button type="button" class="btn btn-primary" style="width: auto;" data-bs-toggle="modal" data-bs-target="#exampleModal">
          Crear nueva tarea
        </button>
      </div>
    </div>

    <div class="row  table-responsive">
      <!-- Tabla -->
      <table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Titulo</th>
            <th scope="col">Descripción</th>
            <th scope="col">Estado</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (isset($result) && $result->num_rows > 0) {
            $counter = 1; // contador
            while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<th scope='row'>" . $counter . "</th>";
              echo "<td>" . $row["title"] . "</td>";
              echo "<td>" . $row["description"] . "</td>";
              echo "<td>";
              if ($row["status"] == 'pending') echo 'Pendiente';
              if ($row["status"] == 'in_progress') echo 'En progreso';
              if ($row["status"] == 'completed') echo 'Completado';
              echo "</td>";
              echo "<td>
              <a href='edit.php?id=" . $row["id"] . "'>Modificar</a> | 
              <a href='#' onclick='confirmDelete(" . $row["id"] . ")'>Eliminar</a> 
              </td>";
              echo "</tr>";
              $counter++;
            }
          } else {
            echo "<tr style='text-align:center'><td colspan='5'>No hay tareas pendientes</td></tr>";
          }
          ?>
        </tbody>
      </table>
      <!-- Tabla -->

      <!-- Modal -->
      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar nueva tarea</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="../src/Controller/TaskController.php/addTask" method="POST">

                <label class="form-label" for="title">Titulo</label>
                <input type="text" name="title" id="title" class="form-control" required />

                <label class="form-label" for="description">Descripción</label>
                <input type="text" name="description" id="description" class="form-control" required />

                <label class="form-label" for="status">Estado:</label>
                <select class="form-control" id="status" name="status">
                  <option value="pending">Pendiente</option>
                  <option value="in_progress">En progreso</option>
                  <option value="completed">Completado</option>
                </select>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            </form>
          </div>
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

  <script>
    // confirmar eliminación
    function confirmDelete(taskId) {
      if (confirm("¿Estás seguro de que deseas eliminar esta tarea?")) {
        // Formulario para enviar la solicitud de eliminación
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = ''; // Envía a index.php

        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_task';
        input.value = '1';

        var input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'task_id';
        input2.value = taskId;

        form.appendChild(input);
        form.appendChild(input2);
        document.body.appendChild(form);
        form.submit();
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>