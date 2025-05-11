<?php
// oauth-docker-practice/php-app/mitigated/oauth_callback.php
session_start();

echo "<h1>Respuesta del Callback OAuth (MITIGADO/SEGURO)</h1>";

// 1. Validar 'state' para prevenir CSRF
if (!isset($_GET['state']) || !isset($_SESSION['oauth_state_mitigated']) || $_GET['state'] !== $_SESSION['oauth_state_mitigated']) {
    die("<p style='color:red; font-weight:bold;'>Error Crítico: Fallo en la validación del parámetro 'state'. Posible ataque CSRF.</p>");
}
unset($_SESSION['oauth_state_mitigated']); // Limpiar 'state' después de usarlo

// 2. Verificar la recepción del código de autorización
if (!isset($_GET['code'])) {
    die("<p style='color:red;'>Error: No se recibió el código de autorización desde el servidor OAuth.</p>");
}
$code = $_GET['code'];

// 3. Recuperar 'code_verifier' para PKCE
if (!isset($_SESSION['code_verifier_mitigated'])) {
    die("<p style='color:red;'>Error Crítico: No se encontró 'code_verifier' en la sesión para la validación PKCE.</p>");
}
$code_verifier = $_SESSION['code_verifier_mitigated'];
unset($_SESSION['code_verifier_mitigated']); // Limpiar 'code_verifier'

// 4. Intercambiar el código por un token de acceso (usando POST y PKCE)
$clientId = "myapp";
$redirectUriMitigated = "http://localhost:8000/mitigated/oauth_callback.php"; // Debe coincidir
$tokenUrl = "http://keycloak:8080/realms/myrealm/protocol/openid-connect/token";

$postData = [
    'grant_type' => 'authorization_code',
    'client_id' => $clientId,
    'redirect_uri' => $redirectUriMitigated,
    'code' => $code,
    'code_verifier' => $code_verifier // Enviar 'code_verifier' para validación PKCE
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    echo "<p style='color:red;'>Error de cURL: " . htmlspecialchars($curl_error, ENT_QUOTES, 'UTF-8') . "</p>";
} elseif ($http_code === 200) {
    $token_data = json_decode($response, true);
    if (isset($token_data['access_token'])) {
        echo "<p style='color:green; font-weight:bold;'>¡Autenticación y obtención de token exitosas (con validación de state y PKCE)!</p>";
        echo "Token de acceso recibido:<br>";
        echo "<pre style='background-color:#f0f0f0; padding:10px; border:1px solid #ccc;'>" . htmlspecialchars(json_encode($token_data, JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8') . "</pre>";
    } else {
        echo "<p style='color:red;'>Error: No se encontró el token de acceso en la respuesta del servidor OAuth.</p>";
        echo "Respuesta del servidor: <pre>" . htmlspecialchars($response, ENT_QUOTES, 'UTF-8') . "</pre>";
    }
} else {
    echo "<p style='color:red;'>Error al obtener el token de acceso. Código HTTP: " . $http_code . "</p>";
    echo "Respuesta del servidor: <pre>" . htmlspecialchars($response, ENT_QUOTES, 'UTF-8') . "</pre>";
}
?>