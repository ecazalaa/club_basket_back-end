<?php

// Liste des origines autorisées
$allowed_origins = [
    'https://editor.swagger.io',
    'https://clubbasketbackend.alwaysdata.net',
    'https://apiauthclubbasket.alwaysdata.net',
    'https://clubbasketfrontend.alwaysdata.net'
];

// Récupérer l'origine de la requête
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// Vérifier si l'origine est autorisée
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    // Si l'origine n'est pas dans la liste, on autorise Swagger par défaut
    header("Access-Control-Allow-Origin: https://editor.swagger.io");
}

// Headers CORS essentiels
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Max-Age: 86400'); // 24 heures
header('Content-Type: application/json');

// Gérer les requêtes OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

