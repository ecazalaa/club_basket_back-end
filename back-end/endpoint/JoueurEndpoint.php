<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../endpoint/response.php';
require_once '../controleur/ObtenirTousLesJoueurs.php';
require_once '../controleur/RechercheJoueur.php';
require_once '../controleur/CreerJoueur.php';
require_once '../controleur/ModifierStatutJoueur.php';
require_once '../controleur/ModifieJoueur.php';
require_once '../controleur/SupprimerJoueur.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        $critere = null;
        $motcle = null;

        if (isset($_GET['cle']) && isset($_GET['critere'])) {
            $motcle = $_GET['cle'];
            $critere = $_GET['critere'];
            $recherche = new RechercheJoueur( $critere,$motcle);
            $joueur=$recherche->executer();
            deliver_response(200, "Joueur trouvé", $joueur);
        }

        else {
            // Utiliser ObtenirTousLesJoueurs.php
            $joueurs = new ObtenirTousLesJoueurs();
            $joueurs = $joueurs->executer();
            deliver_response(200, "Liste des joueurs récupérée", $joueurs);
        }
        break;

    case 'POST':
        // Utiliser CreerJoueur.php
        $data = json_decode(file_get_contents('php://input'), true);
        $creer = new CreerJoueur(
            $data['nom'],
            $data['prenom'],
            $data['date_naissance'],
            $data['taille'],
            $data['poids'],
            $data['licence']
        );
        $result = $creer->executer();
        deliver_response(201, "Joueur créé avec succès", $result);
        break;

    case 'PUT':
        if(isset($_GET['licence'])) {
            $data = json_decode(file_get_contents('php://input'), true);

            if(isset($data['statut'])) {
                // Utiliser ModifierStatutJoueur.php
                $modifierStatut = new ModifierStatutJoueur($_GET['licence'], $data['statut']);
                $result = $modifierStatut->executer();
                deliver_response(200, "Statut du joueur modifié", $result);
            } else {
                // Utiliser ModifieJoueur.php
                $modifier = new ModifieJoueur(
                    $_GET['licence'],
                    $data['nom'],
                    $data['prenom'],
                    $data['date_naissance'],
                    $data['taille'],
                    $data['poids']
                );
                $result = $modifier->executer();
                deliver_response(200, "Joueur modifié avec succès", $result);
            }
        }
        break;

    case 'DELETE':
        if(isset($_GET['licence'])) {
            $licence = $_GET['licence'];
            
            // Vérifier si le joueur existe
            $recherche = new RechercheJoueur('licence', $licence);
            $joueur = $recherche->executer();
            
            if(empty($joueur)) {
                deliver_response(404, "Joueur non trouvé", null);
            } else {
                // Supprimer le joueur
                $supprimer = new SupprimerJoueur();
                $result = $supprimer->executer($licence);
                deliver_response(200, "Joueur supprimé avec succès", $result);
            }
        }
        break;

    default:
        deliver_response(405, "Méthode non autorisée", null);
        break;
}
?>