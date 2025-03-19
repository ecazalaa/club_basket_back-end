<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../modele/Joueur.php';
require_once '../modele/JoueurDAO.php';
require_once '../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = connectionBD();
$joueurDAO = new JoueurDAO($pdo);

switch($method) {
    case 'GET':
        if(isset($_GET['licence'])) {
            // Utiliser la méthode select pour obtenir un joueur spécifique
            $joueur = $joueurDAO->select('licence', $_GET['licence']);
            echo json_encode($joueur);
        } else {
            // Utiliser la méthode selectAll pour obtenir tous les joueurs
            $joueurs = $joueurDAO->selectAll();
            echo json_encode($joueurs);
        }
        break;

    case 'POST':
        // Créer un nouveau joueur
        $data = json_decode(file_get_contents('php://input'), true);
        $joueur = new Joueur(
            $data['nom'],
            $data['prenom'],
            $data['date_naissance'],
            $data['taille'],
            $data['poids'],
            $data['licence']
        );
        $result = $joueurDAO->insert($joueur);
        echo json_encode(['success' => $result]);
        break;

    case 'PUT':
        if(isset($_GET['licence'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            if(isset($data['statut'])) {
                // Mettre à jour le statut du joueur
                $result = $joueurDAO->udpateSatut($_GET['licence'], $data['statut']);
            } else {
                // Mettre à jour les informations du joueur
                $joueur = new Joueur(
                    $data['nom'],
                    $data['prenom'],
                    $data['date_naissance'],
                    $data['taille'],
                    $data['poids'],
                    $_GET['licence']
                );
                $result = $joueurDAO->update($joueur);
            }
            echo json_encode(['success' => $result]);
        }
        break;

    case 'DELETE':
        // Supprimer un joueur
        if(isset($_GET['licence'])) {
            $result = $joueurDAO->delete($_GET['licence']);
            echo json_encode(['success' => $result]);
        }
        break;
}
?>