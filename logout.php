<?php
// Détruire le cookie du wallet
setcookie(
    'feAutologin',
    '',
    time() - 3600,
    '/',
    '',
    isset($_SERVER['HTTPS']), // Secure si HTTPS
    true                      // HttpOnly
);

// Redirection vers la page de login
header('Location: Login.php');
exit();

