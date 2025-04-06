<?php
/**
 * @OA\Tag(
 *     name="Matchs",
 *     description="Opérations concernant les matchs de basket"
 * )
 */

/**
 * @OA\Get(
 *     path="/endpoint/MatchEndpoint.php",
 *     summary="Récupérer les matchs",
 *     tags={"Matchs"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="type",
 *         in="query",
 *         description="Type de matchs à récupérer (avenir/passe)",
 *         required=true,
 *         @OA\Schema(type="string", enum={"avenir", "passe"})
 *     ),
 *     @OA\Parameter(
 *         name="critere",
 *         in="query",
 *         description="Critère de recherche",
 *         required=true,
 *         @OA\Schema(type="string", enum={"nom_adversaire", "lieu"})
 *     ),
 *     @OA\Parameter(
 *         name="cle",
 *         in="query",
 *         description="Mot-clé pour la recherche",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Matchs trouvés avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="Liste des matchs récupérée"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="Id_Match", type="integer", example=1),
 *                     @OA\Property(property="M_date", type="string", format="date", example="2024-03-15"),
 *                     @OA\Property(property="M_adversaire", type="string", example="Toulouse Basket"),
 *                     @OA\Property(property="M_lieu", type="string", example="Labège"),
 *                     @OA\Property(property="M_resultat", type="string", example="85-80", nullable=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Aucun match trouvé")
 * )
 * 
 * @OA\Post(
 *     path="/endpoint/MatchEndpoint.php",
 *     summary="Créer un nouveau match",
 *     tags={"Matchs"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"date", "adversaire", "lieu"},
 *             @OA\Property(property="date", type="string", format="date", example="2024-03-15"),
 *             @OA\Property(property="adversaire", type="string", example="Toulouse Basket"),
 *             @OA\Property(property="lieu", type="string", example="Labège")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Match créé avec succès"
 *     ),
 *     @OA\Response(response=400, description="Données invalides")
 * )
 * 
 * @OA\Put(
 *     path="/endpoint/MatchEndpoint.php",
 *     summary="Modifier un match",
 *     tags={"Matchs"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         description="ID du match à modifier",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="date", type="string", format="date"),
 *                     @OA\Property(property="adversaire", type="string"),
 *                     @OA\Property(property="lieu", type="string")
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="resultat", type="string")
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(response=200, description="Match modifié avec succès"),
 *     @OA\Response(response=404, description="Match non trouvé")
 * )
 * 
 * @OA\Delete(
 *     path="/endpoint/MatchEndpoint.php",
 *     summary="Supprimer un match",
 *     tags={"Matchs"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="query",
 *         required=true,
 *         description="ID du match à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Match supprimé avec succès"),
 *     @OA\Response(response=404, description="Match non trouvé")
 * )
 */

require_once 'cors.php';
require_once 'check_auth.php';
require_once 'response.php';
require_once '../controleur/MatchControleur/ObtenirTousLesMatchsAVenir.php';
require_once '../controleur/MatchControleur/ObtenirTousLesMatchsPasse.php';
require_once '../controleur/MatchControleur/RechercheMatch.php';
require_once '../controleur/MatchControleur/CreerMatch.php';
require_once '../controleur/MatchControleur/ModifierMatch.php';
require_once '../controleur/MatchControleur/ModifierResultatMatch.php';
require_once '../controleur/MatchControleur/SupprimerMatch.php';
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
