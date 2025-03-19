<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../modele/Joueur.php';
require_once '../modele/JoueurDAO.php';

$method = $_SERVER['REQUEST_METHOD'];
$joueurDAO = new JoueurDAO();

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            // Équivalent à RechercheJoueur.php
            $joueur = $joueurDAO->rechercheJoueur($_GET['id']);
            echo json_encode($joueur);
        } else {
            // Équivalent à ObtenirTousLesJoueurs.php
            $joueurs = $joueurDAO->obtenirTousLesJoueurs();
            echo json_encode($joueurs);
        }
        break;

    case 'POST':
        // Équivalent à CreerJoueur.php
        $data = json_decode(file_get_contents('php://input'), true);
        $joueur = new Joueur(
            null,
            $data['nom'],
            $data['prenom'],
            $data['dateNaissance'],
            $data['taille'],
            $data['poids'],
            $data['poste'],
            $data['statut']
        );
        $result = $joueurDAO->creerJoueur($joueur);
        echo json_encode(['success' => $result]);
        break;

    case 'PUT':
        if(isset($_GET['id'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            if(isset($data['statut'])) {
                // Équivalent à ModifierStatutJoueur.php
                $result = $joueurDAO->modifierStatutJoueur($_GET['id'], $data['statut']);
            } else {
                // Équivalent à ModifieJoueur.php
                $joueur = new Joueur(
                    $_GET['id'],
                    $data['nom'],
                    $data['prenom'],
                    $data['dateNaissance'],
                    $data['taille'],
                    $data['poids'],
                    $data['poste'],
                    $data['statut']
                );
                $result = $joueurDAO->modifierJoueur($joueur);
            }
            echo json_encode(['success' => $result]);
        }
        break;

    case 'DELETE':
        // Équivalent à SupprimerJoueur.php
        if(isset($_GET['id'])) {
            $result = $joueurDAO->supprimerJoueur($_GET['id']);
            echo json_encode(['success' => $result]);
        }
        break;
}
?>