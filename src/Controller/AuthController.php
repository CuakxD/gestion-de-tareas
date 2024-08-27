<?php
// Obtiene la URI solicitada
$requestUri = $_SERVER['REQUEST_URI'];

// Verifica la URL para login
if ($requestUri === '/to-do-list/src/Controller/AuthController.php/login') {
  if (isset($_POST['loginName']) && isset($_POST['loginPassword'])) {
    // Recibe los parametros
    $username = $_POST['loginName'];
    $password = $_POST['loginPassword'];

    // Llama a la función login()
    login($username, $password);
  } else {
    // Los parámetros no fueron enviados, muestra un mensaje de error
    echo "<script>alert('Error: Faltan datos de inicio de sesión.'); window.location.href = '../../../public/login.php';</script>"; // Redirige a login.php 
  }
} elseif ($requestUri === '/to-do-list/src/Controller/AuthController.php/register') {   // Verifica la URL para el registro
  if (isset($_POST['registerUsername']) && isset($_POST['registerPassword'])) {
    // Recibe los parametros
    $username = $_POST['registerUsername'];
    $password = $_POST['registerPassword'];
    $repeatPassword = $_POST['registerRepeatPassword'];

    // Verifica que password y el password repetido sean el mismo
    if ($password !== $repeatPassword) {
      echo "<script>alert('Error: Las contraseñas no coinciden.'); window.location.href = '../../../public/login.php';</script>";
      exit;
    }

    // Llama a la función register()
    register($username, $password);
  } else {
    // Los parámetros del registro no fueron enviados, muestra un mensaje de error
    echo "<script>alert('Error: Complete los datos para el registro.'); window.location.href = '../../../public/login.php';</script>"; // Redirige a login.php 
  }
} else {
  http_response_code(404); // Envía el encabezado HTTP 404 Not Found
  exit; // Detiene la ejecución del script, el servidor mostrará su mensaje de error 404
}

function login($username, $password)
{
  // Conexion a la BD
  require('../../config.php');
  // 1. Sanitización de datos (evita inyección SQL)
  $username = $conn->real_escape_string($username);

  // 2. Consulta a la base de datos para obtener el hash de la contraseña almacenada
  $sql = "SELECT id, password FROM users WHERE username = '$username'";
  $result = $conn->query($sql);

  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $hashedPassword = $row['password'];

    // 3. Verificación de la contraseña usando password_verify
    if (password_verify($password, $hashedPassword)) {
      // Inicio de sesión exitoso
      session_start();
      // Almacena el ID del usuario en la sesión
      $_SESSION['user_id'] = $row['id'];
      // Redirige al usuario a index.php
      header("Location: ../../../public/index.php");
      exit;
    } else {
      // Usuario o contraseña incorrecta
      echo "<script>alert('Error: Usuario o contraseña incorrectos.'); window.location.href = '../../../public/login.php';</script>";
      exit;
    }
  } else {
    // Usuario no encontrado
    echo "<script>alert('Error: Usuario o contraseña incorrectos.'); window.location.href = '../../../public/login.php';</script>";
    exit;
  }
  // Cierra la conexión a la base de datos
  $conn->close();
}

function register($username, $password)
{
  // Conexion a la BD
  require('../../config.php');

  // 1. Sanitización de datos (evita inyección SQL)
  $username = $conn->real_escape_string($username);

  // 2. Hash de la contraseña 
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // 3. Consulta a la base de datos para verificar si el usuario ya existe
  $sql = "SELECT id FROM users WHERE username = '$username'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // El usuario ya existe
    echo "<script>alert('Error: El usuario ya está registrado.'); window.location.href = '../../../public/login.php';</script>";
    exit;
  }

  // 4. Inserción en la base de datos
  $sql = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";

  if ($conn->query($sql) === TRUE) {
    // Registro exitoso
    echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href = '../../../public/login.php';</script>";
    exit;
  } else {
    // Error en la inserción
    echo "<script>alert('Error al registrar el usuario: " . $conn->error . "'); window.location.href = '../../../public/login.php';</script>";
    exit;
  }
  // Cierra la conexión a la base de datos
  $conn->close();
}
