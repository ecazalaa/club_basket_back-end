<?php

function connectionBD(){

    $host = "mysql-clubbasketbackend.alwaysdata.net";
    $dbname = "clubbasketbackend_bd";
    $username = "403724";
    $password = "Agaboubou65$";

    /*
    $host = "localhost";
    $dbname = "clubbasket_auth";
    $username = "root";
    $password = "root";
*/


///Connexion au serveur MySQL
    try {
        $linkpdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    } catch (Exception $e) {
        die('Erreur  de connexion à la bd: ' . $e->getMessage());
    }
    return $linkpdo;

}
try {
    $pdo = connectionBD();
    echo "Connexion réussie à la base de données.";
} catch (Exception $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
}

function deliver_response($status_code, $status_message, $data = null) {
    // Paramétrage de l'entête HTTP
    http_response_code($status_code);

    // Indique au client le format de la réponse
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
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

