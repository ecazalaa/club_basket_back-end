<?php
/**
 * @OA\Tag(
 *     name="Feuille de Match",
 *     description="Opérations concernant les feuilles de match et les participations détaillées des joueurs"
 * )
 */

/**
 * @OA\Get(
 *     path="/endpoint/FeuilleMatchEndpoint.php",
 *     summary="Récupérer les participations d'un joueur",
 *     tags={"Feuille de Match"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="licence",
 *         in="query",
 *         description="Numéro de licence du joueur",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="id_match",
 *         in="query",
 *         description="ID du match (optionnel, pour une participation spécifique)",
 *         required=false,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Participation(s) trouvée(s)",
 *         @OA\JsonContent(
 *             @OA\Property(property="status_code", type="integer", example=200),
 *             @OA\Property(property="status_message", type="string", example="Participation trouvée"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="licence", type="string", example="VT123456"),
 *                     @OA\Property(property="id_match", type="integer", example=1),
 *                     @OA\Property(property="tituRemp", type="string", example="T pour titulaire, R pour remplaçant"),
 *                     @OA\Property(property="poste", type="string", example="Meneur"),
 *                     @OA\Property(property="note", type="integer", example=8, nullable=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="Participation non trouvée"),
 *     @OA\Response(response=400, description="Paramètres manquants")
 * )
 * 
 * @OA\Post(
 *     path="/endpoint/FeuilleMatchEndpoint.php",
 *     summary="Créer une nouvelle participation à un match",
 *     tags={"Feuille de Match"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"licence", "id_match", "tituRemp", "poste"},
 *             @OA\Property(property="licence", type="string", example="VT123456"),
 *             @OA\Property(property="id_match", type="integer", example=1),
 *             @OA\Property(
 *                 property="tituRemp",
 *                 type="string",
 *                 example="T",
 *                 description="T pour titulaire, R pour remplaçant"
 *             ),
 *             @OA\Property(
 *                 property="poste",
 *                 type="string",
 *                 example="Meneur",
 *                 description="Poste du joueur dans le match"
 *             ),
 *             @OA\Property(property="note", type="integer", example=8, nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Participation créée avec succès"
 *     ),
 *     @OA\Response(response=400, description="Données invalides ou incomplètes"),
 *     @OA\Response(response=500, description="Erreur serveur lors de la création")
 * )
 * 
 * @OA\Put(
 *     path="/endpoint/FeuilleMatchEndpoint.php",
 *     summary="Modifier une participation",
 *     tags={"Feuille de Match"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="licence",
 *         in="query",
 *         required=true,
 *         description="Numéro de licence du joueur",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="id_match",
 *         in="query",
 *         required=true,
 *         description="ID du match",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     required={"tituRemp", "poste"},
 *                     @OA\Property(
 *                         property="tituRemp",
 *                         type="string",
 *                         example="T",
 *                         description="T pour titulaire, R pour remplaçant"
 *                     ),
 *                     @OA\Property(property="poste", type="string", example="Meneur"),
 *                     @OA\Property(property="note", type="integer", nullable=true)
 *                 ),
 *                 @OA\Schema(
 *                     required={"note"},
 *                     @OA\Property(property="note", type="integer", example=8)
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(response=200, description="Participation mise à jour avec succès"),
 *     @OA\Response(response=404, description="Participation non trouvée"),
 *     @OA\Response(response=400, description="Données invalides ou incomplètes")
 * )
 * 
 * @OA\Delete(
 *     path="/endpoint/FeuilleMatchEndpoint.php",
 *     summary="Supprimer une ou plusieurs participations",
 *     tags={"Feuille de Match"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id_match",
 *         in="query",
 *         required=true,
 *         description="ID du match",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="licence",
 *         in="query",
 *         required=false,
 *         description="Numéro de licence du joueur (optionnel, si non fourni, supprime toutes les participations du match)",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Participation(s) supprimée(s) avec succès"
 *     ),
 *     @OA\Response(response=404, description="Participation(s) non trouvée(s)"),
 *     @OA\Response(response=400, description="Paramètres manquants")
 * )
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../modele/Participer.php';
require_once '../config/config.php';
require_once 'response.php';
require_once 'check_auth.php';
require_once '../controleur/ParticipationControleur/CreerParticipation.php';
require_once '../controleur/ParticipationControleur/ModifierParticipation.php';
require_once '../controleur/ParticipationControleur/ModifierParticipationNote.php';
require_once '../controleur/ParticipationControleur/RechercheParticipation.php';
require_once '../controleur/ParticipationControleur/RechercheParticipation2.php';
require_once '../controleur/ParticipationControleur/SupprimerParticipation.php';
require_once '../controleur/ParticipationControleur/SupprimerParticipationIdMatch.php';

// Gestion des requêtes OPTIONS pour le CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérification de l'authentification
check_auth();

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        handleGet();
        break;
    case 'POST':
        handlePost();
        break;
    case 'PUT':
        handlePut();
        break;
    case 'DELETE':
        handleDelete();
        break;
    default:
        deliver_response(405, "Méthode non autorisée", ["error" => "Méthode $method non supportée"]);
        break;
}

function handleGet() {
    if(isset($_GET['licence']) && isset($_GET['id_match'])) {
        // Obtenir une participation spécifique
        $controller = new RechercheParticipation2($_GET['licence'], $_GET['id_match']);
        $participation = $controller->executer();
        
        if($participation) {
            deliver_response(200, "Participation trouvée", $participation);
        } else {
            deliver_response(404, "Participation non trouvée", null);
        }
    } elseif(isset($_GET['licence'])) {
        // Obtenir toutes les participations pour un joueur spécifique
        $controller = new RechercheParticipation('licence', $_GET['licence']);
        $participations = $controller->executer();
        
        if($participations && count($participations) > 0) {
            deliver_response(200, "Participations trouvées", $participations);
        } else {
            deliver_response(404, "Aucune participation trouvée pour cette licence", null);
        }
    } else {
        deliver_response(400, "Paramètres manquants", 
            ["error" => "Veuillez spécifier une licence ou une licence et un id_match"]);
    }
}

function handlePost() {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Vérification des données requises
    if(!isset($data['licence']) || !isset($data['id_match']) || 
       !isset($data['tituRemp']) || !isset($data['poste'])) {
        deliver_response(400, "Données incomplètes", 
            ["error" => "Les champs licence, id_match, tituRemp et poste sont obligatoires"]);
        return;
    }
    
    // Validation des données
    if(empty($data['licence']) || empty($data['id_match'])) {
        deliver_response(400, "Données invalides", 
            ["error" => "La licence et l'id_match ne peuvent pas être vides"]);
        return;
    }
    
    // Note est facultative, on l'initialise à null si non fournie
    $note = isset($data['note']) ? $data['note'] : null;
    
    try {
        $participation = new Participer(
            $data['licence'],
            $data['id_match'],
            $data['tituRemp'],
            $data['poste'],
            $note
        );
        
        $controller = new CreerParticipation($participation);
        $result = $controller->executer();
        
        if($result) {
            deliver_response(201, "Participation créée avec succès", ["success" => true]);
        } else {
            deliver_response(500, "Erreur lors de la création de la participation", 
                ["error" => "La participation n'a pas pu être créée"]);
        }
    } catch (Exception $e) {
        deliver_response(500, "Exception lors de la création", ["error" => $e->getMessage()]);
    }
}

function handlePut() {
    if(!isset($_GET['licence']) || !isset($_GET['id_match'])) {
        deliver_response(400, "Paramètres manquants", 
            ["error" => "Les paramètres licence et id_match sont requis"]);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if(empty($data)) {
        deliver_response(400, "Aucune donnée fournie", 
            ["error" => "Des données sont requises pour la mise à jour"]);
        return;
    }
    
    try {
        if(isset($data['note'])) {
            // Mettre à jour uniquement la note
            $controller = new ModifierParticipationNote(
                $_GET['licence'], 
                $_GET['id_match'], 
                $data['note']
            );
            $result = $controller->executer();
        } else {
            // Vérification des données requises pour une mise à jour complète
            if(!isset($data['tituRemp']) || !isset($data['poste'])) {
                deliver_response(400, "Données incomplètes", 
                    ["error" => "Les champs tituRemp et poste sont obligatoires"]);
                return;
            }
            
            // Mettre à jour toutes les informations de la participation
            $note = isset($data['note']) ? $data['note'] : null;
            $participation = new Participer(
                $_GET['licence'],
                $_GET['id_match'],
                $data['tituRemp'],
                $data['poste'],
                $note
            );
            
            $controller = new ModifierParticipation($participation);
            $result = $controller->executer();
        }
        
        if($result) {
            deliver_response(200, "Participation mise à jour avec succès", ["success" => true]);
        } else {
            deliver_response(404, "Participation non trouvée", 
                ["error" => "Aucune participation trouvée avec cette licence et cet id_match"]);
        }
    } catch (Exception $e) {
        deliver_response(500, "Exception lors de la mise à jour", ["error" => $e->getMessage()]);
    }
}

function handleDelete() {
    if(isset($_GET['licence']) && isset($_GET['id_match'])) {
        // Supprimer une participation spécifique
        try {
            $controller = new SupprimerParticipation();
            $result = $controller->executer($_GET['licence'], $_GET['id_match']);
            
            if($result) {
                deliver_response(200, "Participation supprimée avec succès", ["success" => true]);
            } else {
                deliver_response(404, "Participation non trouvée", 
                    ["error" => "Aucune participation trouvée avec cette licence et cet id_match"]);
            }
        } catch (Exception $e) {
            deliver_response(500, "Exception lors de la suppression", ["error" => $e->getMessage()]);
        }
    } elseif(isset($_GET['id_match'])) {
        // Supprimer toutes les participations pour un match spécifique
        try {
            $controller = new SupprimerParticipationIdMatch($_GET['id_match']);
            $result = $controller->executer();
            
            if($result) {
                deliver_response(200, "Toutes les participations du match supprimées avec succès", 
                    ["success" => true]);
            } else {
                deliver_response(404, "Aucune participation trouvée", 
                    ["error" => "Aucune participation trouvée pour ce match"]);
            }
        } catch (Exception $e) {
            deliver_response(500, "Exception lors de la suppression", ["error" => $e->getMessage()]);
        }
    } else {
        deliver_response(400, "Paramètres manquants", 
            ["error" => "Veuillez spécifier un id_match ou une licence et un id_match"]);
    }
}
?>