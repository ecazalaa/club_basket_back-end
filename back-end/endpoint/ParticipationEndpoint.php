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
require_once '../controleur/MatchControleur/RechercheMatch.php';
require_once '../controleur/JoueurControleur/RechercheJoueur.php';
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
            $criteres_valides = ['Id_Match', 'licence'];
            $critere = htmlspecialchars($_GET['critere']);
            
            if (!in_array($critere, $criteres_valides)) {
                deliver_response(400, "Erreur : Critère invalide. Les critères valides sont : " . implode(', ', $criteres_valides), null);
                break;
            }

            $motcle = htmlspecialchars($_GET['cle']);
            $recherche = new RechercheParticipation($critere, $motcle);
            $participation = $recherche->executer();
            
            if(empty($participation)) {
                deliver_response(404, "Participation(s) non trouvée(s)", null);
            } else {
                deliver_response(200, "Participation(s) trouvée(s)", $participation);
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
        if(!isset($data['Id_Match']) && !isset($data['licence']) && !isset($data['poste']) && !isset($data['Titu_remp'])) {
            deliver_response(400, "Erreur : Les paramètres 'Id_Match', 'licence', 'poste' et 'Titu_remp' sont requis", null);
            break;
        }
        //vérification si la participation existe déjà
        $recherche = new RechercheParticipation2( $data['licence'], $data['Id_Match']);
        $participationExistant = $recherche->executer();
        if ($participationExistant) {
            deliver_response(400, "Erreur : La participation existe déjà", null);
            break;
        }
        //vérification si le match existe
        $rechercheMatch = new RechercheMatch("Id_Match", $data['Id_Match']);
        $match = $rechercheMatch->executer();
        if (empty($match)) {
            deliver_response(404, "Erreur : Le match n'existe pas", null);
            break;
        }
        //vérification si le joueur existe
        $rechercheJoueur = new RechercheJoueur("licence", $data['licence']);
        $joueur = $rechercheJoueur->executer();
        if (empty($joueur)) {
            deliver_response(404, "Erreur : Le joueur n'existe pas", null);
            break;
        }
        
        // Création de la participation
        $participation = new Participer($data['licence'],$data['Id_Match'], $data['Titu_remp'],$data['poste']);
        $creer = new CreerParticipation($participation);
        $result = $creer->executer();
        deliver_response(201, "Participation créée avec succès", $result);
        break;

    case 'PUT':
        if (!isset($_GET['Id_Match']) && !isset($_GET['licence']) && !isset($data['note'])) {
            deliver_response(400, "Erreur : Les paramètres 'Id_Match' et 'licence' sont requis", null);
            break;
        }

        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Vérifier si la participation existe
        $recherche = new RechercheParticipation2($_GET['licence'], $_GET['Id_Match']);
        $participationExistant = $recherche->executer();
        if (empty($participationExistant)) {
            deliver_response(404, "Participation non trouvée", null);
            break;
        }

        // Modification de la note
        if (isset($data['note']) && empty($data['poste']) && empty($data['Titu_remp'])) {
            $modifier = new ModifierParticipationNote($_GET['licence'], $_GET['Id_Match'], $data['note']);
            $result = $modifier->executer();
            deliver_response(200, "Note de la participation modifiée", $result);
        }
        // Modification du poste
        if (isset($data['poste']) && isset($data['Titu_remp']) && empty($data['note']) ) {
            $participation = new Participer($_GET['licence'], $_GET['Id_Match'], $data['Titu_remp'], $data['poste']);
            $modifier = new ModifierParticipation($participation);
            $result = $modifier->executer();
            deliver_response(200, "Poste de la participation modifié", $result);
        }
        else {
            deliver_response(400, "Erreur : Aucun paramètre à modifier", null);
            break;
        }
        break;

    case 'DELETE':
        if (!isset($_GET['Id_Match']) || !isset($_GET['licence'])) {
            deliver_response(400, "Erreur : Les paramètres 'Id_Match' et 'licence' sont requis", null);
            break;
        }

        // Vérifier si la participation existe
        $recherche = new RechercheParticipation2($_GET['licence'], $_GET['Id_Match']);
        $participation = $recherche->executer();
        
        if (empty($participation)) {
            deliver_response(404, "Participation non trouvée", null);
        } else {
            $supprimer = new SupprimerParticipation();
            $result = $supprimer->executer($_GET['licence'], $_GET['Id_Match']);
            deliver_response(200, "Participation supprimée avec succès", $result);
        }
        break;

    default:
        deliver_response(405, "Méthode non autorisée", null);
        break;
}
?> 