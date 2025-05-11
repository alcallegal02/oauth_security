<?php
// oauth-docker-practice/php-app/vulnerable/oauth_login.php
session_start();

$clientId = "myapp";
$redirectUriVulnerable = "http://localhost:8000/vulnerable/oauth_callback.php";

// --- CAMBIO AQUÍ ---
// Cuando el NAVEGADOR es redirigido a Keycloak, debe usar una URL accesible desde el HOST (tu máquina)
$keycloakAuthUrlForBrowser = "http://localhost:8080/realms/myrealm/protocol/openid-connect";
// La comunicación interna servidor-a-servidor (PHP a Keycloak) sí usaría http://keycloak:8080,
// pero este script solo hace la redirección inicial del navegador.

$params = [
    'response_type' => 'code',
    'client_id' => $clientId,
    'redirect_uri' => $redirectUriVulnerable,
    'scope' => 'openid profile email'
];

// Usar la URL para el navegador
$auth_url = $keycloakAuthUrlForBrowser . "/auth?" . http_build_query($params);

header("Location: " . $auth_url);
exit();
?>