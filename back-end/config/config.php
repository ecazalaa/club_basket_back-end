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

?>