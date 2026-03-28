<?php
// Clear the wallet cookie and redirect to the login page.
setcookie('feAutologin', '', time() - 3600, '/', null, !!Kwf_Config::getValue('server.https'), true);
header('Location: Login.php');
exit();
