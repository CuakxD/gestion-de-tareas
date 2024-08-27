<?php
// Configuración de la base de datos (reemplaza con tus datos)
$db_host = 'localhost';
$db_name = 'to-do-list';
$db_user = 'phpmyadmin';
$db_password = '12345';

// Conexión a la base de datos
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
