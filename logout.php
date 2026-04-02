<?php
session_start();
unset($_SESSION['wallet_address']);
setcookie('wallet_address', '', time() - 3600, '/');
setcookie('feAutologin', '', [
    'expires'  => time() - 3600,
    'path'     => '/',
    'domain'   => '',
    'secure'   => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

header('Location: Login.php');
exit();
