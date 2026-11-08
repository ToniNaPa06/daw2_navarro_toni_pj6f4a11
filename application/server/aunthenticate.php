<?php
// Activem la sessió per gestionar l'accés de l'usuari
session_start();

// Comprovem que la petició s'hagi fet mitjançant el mètode POST (requisit de la rúbrica)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recollim les dades enviades des del formulari de client netejant espais
    $inputUser = trim($_POST['username'] ?? '');
    $inputPass = trim($_POST['password'] ?? '');

    // Ruta cap al fitxer d'usuaris predefinit
    $usersFile = '../../users/users.json';

    // Comprovem si el fitxer d'usuaris existeix
    if (file_exists($usersFile)) {
        // Llegim el contingut del fitxer JSON
        $jsonData = file_get_contents($usersFile);
        $users = json_decode($jsonData, true);

        $authenticated = false;

        // Recorrem l'array associatiu per validar les credencials
        foreach ($users as $userRecord) {
            // Comprovem el nom d'usuari i la contrasenya encriptada (password_verify)
            if ($userRecord['user'] === $inputUser && password_verify($inputPass, $userRecord['password'])) {
                $authenticated = true;
                break;
            }
        }

        if ($authenticated) {
            // Autenticació correcte: redirigim al menú d'operacions (que crearem després)
            $_SESSION['user'] = $inputUser;
            header("Location: ../client/operations.html"); // O la vista corresponent
            exit();
        } else {
            // Autenticació fallida: tornem a la pàgina de login amb error
            header("Location: ../client/login.html?error=invalid");
            exit();
        }
    } else {
        // Error si no es troba el fitxer d'usuaris
        http_response_code(404);
        echo "Error 404: Users file not found.";
    }
} else {
    // Si s'intenta accedir directament via GET, redirigim al login
    header("Location: ../client/login.html");
    exit();
}
?>