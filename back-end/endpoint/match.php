<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once '../controleur/CreerMatch.php';
require_once '../controleur/ModifierMatch.php';
require_once '../controleur/SupprimerMatch.php';
require_once '../controleur/RechercheMatch.php';
require_once '../controleur/ObtenirTousLesMatchsAVenir.php';
require_once '../controleur/ObtenirTousLesMatchsPasse.php';
require_once '../controleur/ModifierResultatMatch.php';
require_once 'response.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch($method) {
        case 'GET':
            if (isset($request[0])) {
                // Recherche d'un match spécifique
                $resultat = rechercherMatch($request[0]);
                deliver_response(200, "Match trouvé avec succès", $resultat);
            } else if (isset($_GET['type'])) {
                if ($_GET['type'] === 'avenir') {
                    // Obtenir tous les matchs à venir
                    $resultat = obtenirTousLesMatchsAVenir();
                    deliver_response(200, "Liste des matchs à venir récupérée avec succès", $resultat);
                } else if ($_GET['type'] === 'passe') {
                    // Obtenir tous les matchs passés
                    $resultat = obtenirTousLesMatchsPasse();
                    deliver_response(200, "Liste des matchs passés récupérée avec succès", $resultat);
                } else {
                    deliver_response(400, "Type de requête invalide");
                }
            } else {
                deliver_response(400, "Requête invalide");
            }
            break;

        case 'POST':
            if (!isset($input['date_match']) || !isset($input['heure_match']) || 
                !isset($input['lieu_match']) || !isset($input['equipe_adverse'])) {
                deliver_response(400, "Données manquantes");
                break;
            }
            $resultat = creerMatch(
                $input['date_match'],
                $input['heure_match'],
                $input['lieu_match'],
                $input['equipe_adverse']
            );
            deliver_response(201, "Match créé avec succès", $resultat);
            break;

        case 'PUT':
            if (!isset($input['id_match']) || !isset($input['date_match']) || 
                !isset($input['heure_match']) || !isset($input['lieu_match']) || 
                !isset($input['equipe_adverse'])) {
                deliver_response(400, "Données manquantes");
                break;
            }
            if (isset($input['resultat'])) {
                // Modification du résultat
                $resultat = modifierResultatMatch(
                    $input['id_match'],
                    $input['resultat']
                );
                deliver_response(200, "Résultat du match modifié avec succès", $resultat);
            } else {
                // Modification des informations du match
                $resultat = modifierMatch(
                    $input['id_match'],
                    $input['date_match'],
                    $input['heure_match'],
                    $input['lieu_match'],
                    $input['equipe_adverse']
                );
                deliver_response(200, "Match modifié avec succès", $resultat);
            }
            break;

        case 'DELETE':
            if (!isset($request[0])) {
                deliver_response(400, "ID du match manquant");
                break;
            }
            $resultat = supprimerMatch($request[0]);
            deliver_response(200, "Match supprimé avec succès", $resultat);
            break;

        default:
            deliver_response(405, "Méthode non supportée");
    }

} catch (Exception $e) {
    deliver_response(500, "Erreur serveur : " . $e->getMessage());
}
?>
