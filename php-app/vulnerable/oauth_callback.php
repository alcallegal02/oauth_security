<?php
// oauth-docker-practice/php-app/vulnerable/oauth_callback.php

echo "<h1>Respuesta del Callback OAuth (VULNERABLE)</h1>";

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    // ¡PELIGRO! Mostrar el código directamente.
    echo "<p style='color:red; font-weight:bold;'>ATENCIÓN: Código OAuth recibido y EXPUESTO directamente: " . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . "</p>";
    echo "<p>Esta página es vulnerable porque:</p>";
    echo "<ul>";
    echo "<li>No valida un parámetro 'state', lo que permite ataques CSRF.</li>";
    echo "<li>Expone el código de autorización en la respuesta, que podría ser interceptado.</li>";
    echo "<li>Si se intentara intercambiar este código por un token, no se usaría PKCE.</li>";
    echo "</ul>";
    echo "<hr>";
    echo "<p>Para probar la vulnerabilidad CSRF, un atacante podría intentar redirigir a un usuario autenticado a una URL manipulada que use este endpoint de callback con un código de autorización robado o para iniciar un flujo malicioso.</p>";

} else {
    echo "<p style='color:orange;'>Advertencia: No se recibió ningún código OAuth en la URL.</p>";
}

// Mostrar cualquier error devuelto por el servidor OAuth
if (isset($_GET['error'])) {
    echo "<p style='color:red;'>Error de OAuth: " . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') . "</p>";
    if (isset($_GET['error_description'])) {
        echo "<p>Descripción del Error: " . htmlspecialchars($_GET['error_description'], ENT_QUOTES, 'UTF-8') . "</p>";
    }
}
?>