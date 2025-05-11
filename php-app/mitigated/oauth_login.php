<?php
// oauth-docker-practice/php-app/mitigated/oauth_login.php
session_start();

$clientId = "myapp";
$redirectUriMitigated = "http://localhost:8000/mitigated/oauth_callback.php";

// --- CAMBIO AQUÍ ---
// Cuando el NAVEGADOR es redirigido a Keycloak, debe usar una URL accesible desde el HOST
$keycloakAuthUrlForBrowser = "http://localhost:8080/realms/myrealm/protocol/openid-connect";

$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state_mitigated'] = $state;

$code_verifier = bin2hex(random_bytes(32));
$_SESSION['code_verifier_mitigated'] = $code_verifier;

$code_challenge_hash = hash('sha256', $code_verifier, true);
$code_challenge = rtrim(strtr(base64_encode($code_challenge_hash), '+/', '-_'), '=');

$params = [
    'response_type' => 'code',
    'client_id' => $clientId,
    'redirect_uri' => $redirectUriMitigated,
    'state' => $state,
    'scope' => 'openid profile email',
    'code_challenge' => $code_challenge,
    'code_challenge_method' => 'S256'
];

// Usar la URL para el navegador
$auth_url = $keycloakAuthUrlForBrowser . "/auth?" . http_build_query($params);
header("Location: " . $auth_url);
exit();
?>