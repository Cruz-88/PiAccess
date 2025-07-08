<?php
// logout.php - Logout do sistema
require_once 'config.php';

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Se existe um cookie de sessão, deletá-lo
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir a sessão
session_destroy();

// Redirecionar para login
header('Location: login.php');
exit;
?>