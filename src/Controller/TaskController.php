<?php
// Obtiene la URI solicitada
$requestUri = $_SERVER['REQUEST_URI'];

// Verifica la URL para agregar nueva tarea
if ($requestUri === '/to-do-list/src/Controller/TaskController.php/addTask') {
  if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['status'])) {
    // Los parámetros fueron enviados
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];

    // Llama a la función addTask()
    addTask($title, $description, $status);
  } else {
    // Los parámetros no fueron enviados, muestra un mensaje de error
    echo "<script>alert('Error: Faltan datos para crear la tarea.'); window.location.href = '../../../public/index.php';</script>"; // Redirige a index.php 
  }
} elseif ($requestUri === '/to-do-list/src/Controller/TaskController.php/editTask') {
  //Validacion de los parametros
  if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['status'])) {
    // Obtiene los datos del formulario de edición
    $taskId = $_POST['task_id'];
    $newTitle = $_POST['title'];
    $newDescription = $_POST['description'];
    $newStatus = $_POST['status'];

    // Llama a la función editTask()
    editTask($taskId, $newTitle, $newDescription, $newStatus);
  } else {
    // Los parámetros no fueron enviados, muestra un mensaje de error
    echo "<script>alert('Error: Faltan datos para editar la tarea.'); window.location.href = '../../../public/index.php';</script>"; // Redirige a index.php 
  }
} else {
  http_response_code(404); // Envía el encabezado HTTP 404 Not Found
  exit;
}

function addTask($title, $description, $status)
{
  require('../../config.php');

  // 1. Sanitización de datos (evita inyección SQL)
  $title = $conn->real_escape_string($title);
  $description = $conn->real_escape_string($description);

  // 2. Obtén el ID del usuario de la sesión
  session_start();
  $userId = $_SESSION['user_id'];

  // 3. Inserción en la base de datos
  $sql = "INSERT INTO tasks (title, description, status, user_id) 
          VALUES ('$title', '$description', '$status', $userId)";

  if ($conn->query($sql) === TRUE) {
    // Tarea agregada exitosamente
    echo "<script>alert('Tarea agregada exitosamente.'); window.location.href = '../../../public/index.php';</script>";
    exit;
  } else {
    // Error en la inserción
    echo "<script>alert('Error al agregar la tarea: " . $conn->error . "'); window.location.href = '../../../public/index.php';</script>";
    exit;
  }
  // Cierra la conexión a la base de datos
  $conn->close();
}

function editTask($taskId, $newTitle, $newDescription, $newStatus)
{
  // 1. Conexion a la bd.
  require('../../config.php');

  // 2. Sanitización de datos
  $taskId = $conn->real_escape_string($taskId);
  $newTitle = $conn->real_escape_string($newTitle);
  $newDescription = $conn->real_escape_string($newDescription);

  // 3. Obtén el ID del usuario de la sesión
  session_start();
  $userId = $_SESSION['user_id'];

  // 4. Actualiza la tarea en la base de datos
  $sql = "UPDATE tasks 
            SET title = '$newTitle', description = '$newDescription', status = '$newStatus' 
            WHERE id = $taskId AND user_id = $userId";

  if ($conn->query($sql) === TRUE) {
    // Tarea actualizada exitosamente
    echo "<script>alert('Tarea actualizada exitosamente.'); window.location.href = '../../../public/index.php';</script>";
    exit;
  } else {
    // Error al actualizar la tarea
    echo "<script>alert('Error al actualizar la tarea: " . $conn->error . "'); window.location.href = '../../../public/index.php';</script>";
    exit;
  }

  $conn->close();
}
