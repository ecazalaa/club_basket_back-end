<?php
function deliver_response($status_code, $status_message, $data = null) {
    // Paramétrage de l'entête HTTP
    http_response_code($status_code);

    // Indique au client le format de la réponse
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
    header("Content-Type:application/json; charset=utf-8");

    // Création du tableau de réponse
    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    $response['status'] = ($status_code >= 200 && $status_code < 300) ? 'success' : 'error';
    $response['data'] = $data;
    $response['title'] = "Bienvenue a l'API d'authentification de Romeo et Emile";

    // Encodage de la réponse au format JSON
    $json_response = json_encode($response);
    if ($json_response === false) {
        die('json encode ERROR: ' . json_last_error_msg());
    }

    // Affichage de la réponse (retournée au client)
    echo $json_response;
}
?>

