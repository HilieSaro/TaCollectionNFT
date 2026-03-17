<?php
// Clear the wallet cookie and redirect to the login page.
setcookie('wallet_address', '', time() - 3600, '/');
header('Location: Login.php');
exit();
