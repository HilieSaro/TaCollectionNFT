<?php
header('Content-Type: application/json; charset=utf-8');

// Ce fichier renvoie les deux adresses de profil demandées.
// Il est utilisé par Catalogue.php via XMLHttpRequest().

$owners = [
  '0x72c2ae7b736e9cbc304e8c31a45fbfd82f04ab80',
];

echo json_encode(['addresses' => $owners]);
