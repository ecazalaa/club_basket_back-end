<?php
header('Content-Type: application/json');

$config = [
    'apache_modules' => function_exists('apache_get_modules') ? apache_get_modules() : 'Non disponible',
    'server_software' => $_SERVER['SERVER_SOFTWARE'],
    'document_root' => $_SERVER['DOCUMENT_ROOT'],
    'htaccess_working' => file_exists(__DIR__ . '/../.htaccess') ? 'Présent' : 'Absent',
    'mod_rewrite' => in_array('mod_rewrite', apache_get_modules()) ? 'Activé' : 'Désactivé',
    'mod_headers' => in_array('mod_headers', apache_get_modules()) ? 'Activé' : 'Désactivé',
    'authorization_header' => [
        'direct' => isset($_SERVER['HTTP_AUTHORIZATION']) ? 'Présent' : 'Absent',
        'redirect' => isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? 'Présent' : 'Absent',
        'raw' => isset(apache_request_headers()['Authorization']) ? 'Présent' : 'Absent'
    ]
];

echo json_encode($config, JSON_PRETTY_PRINT);
?> 