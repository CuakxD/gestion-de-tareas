<?php
session_start(); // Inicia o reanuda la sesión

// Destruye todas las variables de sesión
session_unset();

// Destruye la sesión
session_destroy();

// Redirige al usuario a la página de inicio de sesión
header("Location: login.php");
exit;
