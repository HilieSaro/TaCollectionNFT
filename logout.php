<?php
setcookie('feAutologin', '', [
    'expires'  => time() - 3600,
    'path'     => '/',
    'domain'   => '',
    'secure'   => isset($_SERVER['HTTPS']), // Secure si HTTPS
    'httponly' => true,                     // HttpOnly
    'samesite' => 'Strict'                  // Protection CSRF maximale
]);

header('Location: Login.php');
exit();
