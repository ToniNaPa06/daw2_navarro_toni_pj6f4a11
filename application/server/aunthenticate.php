<?php
/**
 * owner: Toni Navarro
 */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Method Not Allowed. Only POST requests are allowed."]);
    exit;
}

// 1. Indicamos al navegador que la respuesta es JSON
header('Content-Type: application/json');

// 2. Ruta absoluta hasta users.json
$rutaArchivo = __DIR__ . '/../../users/users.json';

// Comprobamos si el archivo existe
if (!file_exists($rutaArchivo)) {
    echo json_encode([
        "success" => false,
        "message" => "No se encuentra el archivo en la ruta: " . $rutaArchivo
    ]);
    exit;
}

$username = ($_POST["username"] ?? "");
$password = ($_POST["password"] ?? "");

// 3. Leemos el contenido del archivo como texto
$contenidoTexto = file_get_contents($rutaArchivo);

// 4. Lo convertimos de texto JSON a un array de PHP
$usuarios = json_decode($contenidoTexto, true);

$usuarioEncontrado = null;
foreach ($usuarios as $u) {
    // Comprobamos tanto 'user' como 'username' por compatibilidad
    $storedUser = $u['user'] ?? $u['username'] ?? '';
    if ($storedUser === $username) {
        $usuarioEncontrado = $u;
        break;
    }
}

if ($usuarioEncontrado === null) {
    echo json_encode(["success" => false, "message" => "Authentication failed. User not found."]);
    exit;
}

if (!password_verify($password, $usuarioEncontrado['password'])) {
    echo json_encode(["success" => false, "message" => "Password incorrect."]);
    exit;
}

echo json_encode(["success" => true, "message" => "Authentication successful."]);