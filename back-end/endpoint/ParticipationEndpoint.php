<?php
/**
 * @OA\Tag(
 *     name="Participations",
 *     description="Opérations concernant les participations des joueurs aux matchs"
 * )
 */

/**
 * @OA\Get(
 *     path="/endpoint/ParticipationEndpoint.php",
 *     summary="Rechercher des participations",
 *     tags={"Participations"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="critere",
 *         in="query",
 *         description="Critère de recherche",
 *         required=true,
 *         @OA\Schema(type="string", enum={"Id_Match", "Id_Joueur"})
 *     ),
 *     @OA\Parameter(
 *         name="cle",
 *         in="query",
 *         description="Valeur du critère",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Participations trouvées",
 *         @OA\JsonContent(
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="Participation trouvée"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="Id_Match", type="integer", example=1),
 *                     @OA\Property(property="Id_Joueur", type="integer", example=1),
 *                     @OA\Property(property="P_note", type="integer", example=8, nullable=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Participation non trouvée")
 * )
 * 
 * @OA\Post(
 *     path="/endpoint/ParticipationEndpoint.php",
 *     summary="Créer une nouvelle participation",
 *     tags={"Participations"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"Id_Match", "Id_Joueur"},
 *             @OA\Property(property="Id_Match", type="integer", example=1),
 *             @OA\Property(property="Id_Joueur", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Participation créée avec succès"
 *     ),
 *     @OA\Response(response=400, description="Données invalides")
 * )
 * 
 * @OA\Put(
 *     path="/endpoint/ParticipationEndpoint.php",
 *     summary="Modifier la note d'une participation",
 *     tags={"Participations"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id_match",
 *         in="query",
 *         required=true,
 *         description="ID du match",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="id_joueur",
 *         in="query",
 *         required=true,
 *         description="ID du joueur",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"note"},
 *             @OA\Property(property="note", type="integer", example=8)
 *         )
 *     ),
 *     @OA\Response(response=200, description="Note modifiée avec succès"),
 *     @OA\Response(response=404, description="Participation non trouvée")
 * )
 * 
 * @OA\Delete(
 *     path="/endpoint/ParticipationEndpoint.php",
 *     summary="Supprimer une participation",
 *     tags={"Participations"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id_match",
 *         in="query",
 *         required=true,
 *         description="ID du match",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="id_joueur",
 *         in="query",
 *         required=true,
 *         description="ID du joueur",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Participation supprimée avec succès"),
 *     @OA\Response(response=404, description="Participation non trouvée")
 * )
 */

require_once 'cors.php';
require_once 'check_auth.php';
require_once 'response.php';
require_once '../controleur/ParticipationControleur/CreerParticipation.php';
require_once '../controleur/ParticipationControleur/ModifierParticipation.php';
require_once '../controleur/ParticipationControleur/ModifierParticipationNote.php';
require_once '../controleur/ParticipationControleur/RechercheParticipation.php';
require_once '../controleur/ParticipationControleur/RechercheParticipation2.php';
require_once '../controleur/ParticipationControleur/SupprimerParticipation.php';
require_once '../controleur/ParticipationControleur/SupprimerParticipationIdMatch.php';
require_once '../modele/Participer.php';

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
            $criteres_valides = ['Id_Match', 'Id_Joueur'];
            $critere = htmlspecialchars($_GET['critere']);
            
            if (!in_array($critere, $criteres_valides)) {
                deliver_response(400, "Erreur : Critère invalide. Les critères valides sont : " . implode(', ', $criteres_valides), null);
                break;
            }

            $motcle = htmlspecialchars($_GET['cle']);
            $recherche = new RechercheParticipation($critere, $motcle);
            $participation = $recherche->executer();
            
            if(empty($participation)) {
                deliver_response(404, "Participation non trouvée", null);
            } else {
                deliver_response(200, "Participation trouvée", $participation);
            }
        }
        else {
            deliver_response(400, "Erreur : Les paramètres 'cle' et 'critere' sont requis", null);
        }
        break;

    case 'POST':
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Vérification de la présence de tous les champs requis
        if(!isset($data['Id_Match']) || !isset($data['Id_Joueur'])) {
            deliver_response(400, "Erreur : Les paramètres 'Id_Match' et 'Id_Joueur' sont requis", null);
            break;
        }

        // Création de la participation
        $participation = new Participer($data['Id_Match'], $data['Id_Joueur']);
        $creer = new CreerParticipation($participation);
        $result = $creer->executer();
        deliver_response(201, "Participation créée avec succès", $result);
        break;

    case 'PUT':
        if (!isset($_GET['id_match']) || !isset($_GET['id_joueur'])) {
            deliver_response(400, "Erreur : Les paramètres 'id_match' et 'id_joueur' sont requis", null);
            break;
        }

        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Vérifier si la participation existe
        $recherche = new RechercheParticipation2($_GET['id_match'], $_GET['id_joueur']);
        $participationExistant = $recherche->executer();
        if (empty($participationExistant)) {
            deliver_response(404, "Participation non trouvée", null);
            break;
        }

        // Modification de la note
        if (isset($data['note'])) {
            $modifier = new ModifierParticipationNote($_GET['id_match'], $_GET['id_joueur'], $data['note']);
            $result = $modifier->executer();
            deliver_response(200, "Note de la participation modifiée", $result);
        }
        else {
            deliver_response(400, "Erreur : Aucun paramètre à modifier", null);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id_match']) || !isset($_GET['id_joueur'])) {
            deliver_response(400, "Erreur : Les paramètres 'id_match' et 'id_joueur' sont requis", null);
            break;
        }

        // Vérifier si la participation existe
        $recherche = new RechercheParticipation2($_GET['id_match'], $_GET['id_joueur']);
        $participation = $recherche->executer();
        
        if (empty($participation)) {
            deliver_response(404, "Participation non trouvée", null);
        } else {
            $supprimer = new SupprimerParticipation();
            $result = $supprimer->executer($_GET['id_match'], $_GET['id_joueur']);
            deliver_response(200, "Participation supprimée avec succès", $result);
        }
        break;

    default:
        deliver_response(405, "Méthode non autorisée", null);
        break;
}
?> 