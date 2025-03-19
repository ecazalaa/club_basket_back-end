<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../modele/Participer.php';
require_once '../modele/ParticiperDAO.php';
require_once '../config/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = connectionBD();
$participerDAO = new ParticiperDAO($pdo);

switch($method) {
    case 'GET':
        if(isset($_GET['licence']) && isset($_GET['id_match'])) {
            // Obtenir une participation spécifique
            $participation = $participerDAO->select2($_GET['licence'], $_GET['id_match']);
            echo json_encode($participation);
        } elseif (isset($_GET['licence'])) {
            // Obtenir toutes les participations pour un joueur spécifique
            $participations = $participerDAO->select('licence', $_GET['licence']);
            echo json_encode($participations);
        } else {
            // Obtenir toutes les participations
            // Note: Vous devrez peut-être ajouter une méthode pour cela si nécessaire
            echo json_encode(['error' => 'Specify a licence or licence and id_match']);
        }
        break;

    case 'POST':
        // Créer une nouvelle participation
        $data = json_decode(file_get_contents('php://input'), true);
        $participation = new Participer(
            $data['licence'],
            $data['id_match'],
            $data['tituRemp'],
            $data['poste'],
            $data['note']
        );
        $result = $participerDAO->insert($participation);
        echo json_encode(['success' => $result]);
        break;

    case 'PUT':
        if(isset($_GET['licence']) && isset($_GET['id_match'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            if(isset($data['note'])) {
                // Mettre à jour la note de la participation
                $result = $participerDAO->updateNote($_GET['licence'], $_GET['id_match'], $data['note']);
            } else {
                // Mettre à jour les informations de la participation
                $participation = new Participer(
                    $_GET['licence'],
                    $_GET['id_match'],
                    $data['tituRemp'],
                    $data['poste'],
                    $data['note']
                );
                $result = $participerDAO->update($participation);
            }
            echo json_encode(['success' => $result]);
        }
        break;

    case 'DELETE':
        if(isset($_GET['licence']) && isset($_GET['id_match'])) {
            // Supprimer une participation spécifique
            $result = $participerDAO->delete($_GET['licence'], $_GET['id_match']);
            echo json_encode(['success' => $result]);
        } elseif (isset($_GET['id_match'])) {
            // Supprimer toutes les participations pour un match spécifique
            $result = $participerDAO->deleteByIdMatch($_GET['id_match']);
            echo json_encode(['success' => $result]);
        }
        break;
}
?>