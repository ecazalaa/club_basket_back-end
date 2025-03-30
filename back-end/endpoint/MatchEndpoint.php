<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'check_auth.php';
require_once 'response.php';
require_once '../controleur/MatchControleur/ObtenirTousLesMatchsAVenir.php';
require_once '../controleur/MatchControleur/ObtenirTousLesMatchsPasse.php';
require_once '../controleur/MatchControleur/RechercheMatch.php';
require_once '../controleur/MatchControleur/CreerMatch.php';
require_once '../controleur/MatchControleur/ModifierMatch.php';
require_once '../controleur/MatchControleur/ModifierResultatMatch.php';
require_once '../controleur/MatchControleur/SupprimerMatch.php';
require_once '../controleur/ParticipationControleur/SupprimerParticipationIdMatch.php';
require_once '../modele/MatchBasket.php';

$method = $_SERVER['REQUEST_METHOD'];

check_auth();

// Gestion des requêtes OPTIONS pour CORS
if ($method === 'OPTIONS') {
    http_response_code(204);
    deliver_response(204, "Requête OPTIONS autorisée - CORS", null);
    exit();
}

switch($method) {
    case 'GET':
        // Validation des paramètres
        if (isset($_GET['cle']) xor isset($_GET['critere'])) {
            deliver_response(400, "Erreur : Les paramètres 'cle' et 'critere' doivent être fournis ensemble", null);
            break;
        }

        if (isset($_GET['cle']) && isset($_GET['critere'])) {
            // Validation du critère
            $criteres_valides = [ 'nom_adversaire', 'lieu'];
            $critere = htmlspecialchars($_GET['critere']);
            
            if (!in_array($critere, $criteres_valides)) {
                deliver_response(400, "Erreur : Critère invalide. Les critères valides sont : " . implode(', ', $criteres_valides), null);
                break;
            }

            $motcle = htmlspecialchars($_GET['cle']);
            $recherche = new RechercheMatch($critere, $motcle);
            $match = $recherche->executer();
            
            if(empty($match)) {
                deliver_response(404, "Match non trouvé", null);
            } else {
                deliver_response(200, "Match trouvé", $match);
            }
        }
        // Si type=avenir est spécifié, on récupère les matchs à venir
        elseif (isset($_GET['type']) && $_GET['type'] === 'avenir') {
            $matchs = new ObtenirTousLesMatchsAVenir();
            $matchs = $matchs->executer();
            deliver_response(200, "Liste des matchs à venir récupérée", $matchs);
        }
        // Si type=passe est spécifié, on récupère les matchs passés
        elseif (isset($_GET['type']) && $_GET['type'] === 'passe') {
            $matchs = new ObtenirTousLesMatchsPasse();
            $matchs = $matchs->executer();
            deliver_response(200, "Liste des matchs passés récupérée", $matchs);
        }
        else {
            deliver_response(400, "Erreur : Le paramètre 'type' (avenir/passe) ou les paramètres 'cle' et 'critere' sont requis", null);
        }
        break;

        

    case 'POST':
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Vérification de la présence de tous les champs requis
        if(!isset($data['date']) || !isset($data['adversaire']) || !isset($data['lieu'])) {
            deliver_response(400, "Erreur : Les paramètres 'date', 'adversaire' et 'lieu' sont requis", null);
            break;
        }

        // Validation de la date
        if (!strtotime($data['date'])) {
            deliver_response(400, "Erreur : Format de date invalide", null);
            break;
        }

        // Création du match
        $match = new MatchBasket($data['date'], $data['adversaire'], $data['lieu']);
        $creer = new CreerMatch($match);
        $result = $creer->executer();
        deliver_response(201, "Match créé avec succès", $result);
        break;


    case 'PUT':
        if (!isset($_GET['id'])) {
            deliver_response(400, "Erreur : L'ID du match est requis", null);
            break;
        }

        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Vérifier si le match existe
        $recherche = new RechercheMatch('Id_Match', $_GET['id']);
        $matchExistant = $recherche->executer();
        if (empty($matchExistant)) {
            deliver_response(404, "Match non trouvé", null);
            break;
        }

        // Vérifier si le match est passé
        $dateMatch = $matchExistant[0]['M_date']; // Assurez-vous que la date est bien récupérée
        $isMatchPassed = strtotime($dateMatch) < time();

        // Modification du résultat uniquement si le match est passé
        if ($isMatchPassed && isset($data['resultat']) && !isset($data['date']) && !isset($data['adversaire']) && !isset($data['lieu'])) {
            $modifier = new ModifierResultatMatch($_GET['id'], $data['resultat']);
            $result = $modifier->executer();
            deliver_response(200, "Résultat du match modifié", $result);
        }
        // Modification des autres informations uniquement si le match n'est pas passé
        elseif (!$isMatchPassed && isset($data['date']) && isset($data['adversaire']) && isset($data['lieu'])) {
            $match = new MatchBasket($data['date'], $data['adversaire'], $data['lieu'],null,$_GET['id']);
            $modifier = new ModifierMatch($match);
            $result = $modifier->executer();
            deliver_response(200, "Match modifié avec succès", $result);
        }
        else {
            deliver_response(400, "Erreur : Paramètres invalides pour la modification", null);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            deliver_response(400, "Erreur : L'ID du match est requis", null);
            break;
        }

        // Vérifier si le match existe
        $recherche = new RechercheMatch('Id_Match', $_GET['id']);
        $match = $recherche->executer();
        
        if (empty($match)) {
            deliver_response(404, "Match non trouvé", null);
        } else {
            $suppressionParticipation = new SupprimerParticipationIdMatch($_GET['id']);
            $suppressionParticipation->executer();
            $supprimer = new SupprimerMatch();
            $result = $supprimer->executer($_GET['id']);
            deliver_response(200, "Match supprimé avec succès", $result);
        }
        break;

    default:
        deliver_response(405, "Méthode non autorisée", null);
        break;
}
?>
