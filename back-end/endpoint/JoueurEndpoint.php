<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/endpoint/JoueurEndpoint.php",
 *     summary="Récupérer la liste des joueurs ou rechercher un joueur spécifique",
 *     tags={"Joueurs"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="critere",
 *         in="query",
 *         description="Critère de recherche (nom, prenom, licence, taille, poids)",
 *         required=false,
 *         @OA\Schema(type="string", enum={"nom", "prenom", "licence", "taille", "poids"})
 *     ),
 *     @OA\Parameter(
 *         name="cle",
 *         in="query",
 *         description="Valeur recherchée",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des joueurs ou joueur trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Joueur"))
 *         )
 *     ),
 *     @OA\Response(response=400, description="Paramètres invalides"),
 *     @OA\Response(response=401, description="Non authentifié")
 * )
 * 
 * @OA\Post(
 *     path="/endpoint/JoueurEndpoint.php",
 *     summary="Créer un nouveau joueur",
 *     tags={"Joueurs"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/Joueur")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Joueur créé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Joueur créé avec succès")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Données invalides"),
 *     @OA\Response(response=401, description="Non authentifié")
 * )
 * 
 * @OA\Put(
 *     path="/endpoint/JoueurEndpoint.php",
 *     summary="Modifier un joueur existant",
 *     tags={"Joueurs"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="licence",
 *         in="query",
 *         required=true,
 *         description="Numéro de licence du joueur à modifier",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/Joueur")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Joueur modifié avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Joueur modifié avec succès")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Données invalides"),
 *     @OA\Response(response=401, description="Non authentifié"),
 *     @OA\Response(response=404, description="Joueur non trouvé")
 * )
 * 
 * @OA\Delete(
 *     path="/endpoint/JoueurEndpoint.php",
 *     summary="Supprimer un joueur",
 *     tags={"Joueurs"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="licence",
 *         in="query",
 *         required=true,
 *         description="Numéro de licence du joueur à supprimer",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Joueur supprimé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="Joueur supprimé avec succès")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Données invalides"),
 *     @OA\Response(response=401, description="Non authentifié"),
 *     @OA\Response(response=404, description="Joueur non trouvé")
 * )
 */

require_once 'check_auth.php';
require_once 'response.php';
require_once '../controleur/JoueurControleur/ObtenirTousLesJoueurs.php';
require_once '../controleur/JoueurControleur/RechercheJoueur.php';
require_once '../controleur/JoueurControleur/CreerJoueur.php';
require_once '../controleur/JoueurControleur/ModifierStatutJoueur.php';
require_once '../controleur/JoueurControleur/ModifieJoueur.php';
require_once '../controleur/JoueurControleur/SupprimerJoueur.php';
require_once '../modele/Joueur.php';




// Vérification de l'authentification pour toutes les requêtes

check_auth();

$method = $_SERVER['REQUEST_METHOD'];
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
            $criteres_valides = ['nom', 'prenom', 'licence', 'taille', 'poids'];
            $critere = htmlspecialchars($_GET['critere']);
            
            if (!in_array($critere, $criteres_valides)) {
                deliver_response(400, "Erreur : Critère invalide. Les critères valides sont : " . implode(', ', $criteres_valides), null);
                break;
            }

            // Validation de la clé selon le critère
            $motcle = htmlspecialchars($_GET['cle']);
            $erreur = false;
            $message_erreur = "";

            switch($critere) {
                case 'taille':
                case 'poids':
                    if (!is_numeric($motcle)) {
                        $erreur = true;
                        $message_erreur = "La valeur pour le critère '$critere' doit être numérique";
                    }
                    break;
                case 'licence':
                    if (!preg_match('/^[0-9]{6}$/', $motcle)) {
                        $erreur = true;
                        $message_erreur = "Le numéro de licence doit contenir exactement 6 chiffres";
                    }
                    break;
                case 'nom':
                case 'prenom':
                    if (!preg_match('/^[a-zA-ZÀ-ÿ\s\'-]+$/', $motcle)) {
                        $erreur = true;
                        $message_erreur = "Le $critere ne doit contenir que des lettres, espaces, tirets ou apostrophes";
                    }
                    break;
            }

            if ($erreur) {
                deliver_response(400, $message_erreur, null);
                break;
            }

            $recherche = new RechercheJoueur($critere, $motcle);
            $joueur = $recherche->executer();
            
            if(empty($joueur)){
                deliver_response(404, "Joueur non trouvé", null);
            } else {
                deliver_response(200, "Joueur trouvé", $joueur);
            }
        }
        //si la cle et le critere ne sont pas renseignés, on recupere tous les joueurs
        else {
            $joueurs = new ObtenirTousLesJoueurs();
            $joueurs = $joueurs->executer();
            deliver_response(200, "Liste des joueurs récupérée", $joueurs);
        }
        break;

        

    case 'POST':
        
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Vérification de la présence de tous les champs requis
        if(!isset($data['nom']) || !isset($data['prenom']) || !isset($data['date_naissance']) || 
           !isset($data['taille']) || !isset($data['poids']) || !isset($data['licence'])) {
            deliver_response(400, "Erreur : Les paramètres 'nom', 'prenom', 'date_naissance', 'taille', 'poids' et 'licence' sont requis", null);
            break;
        }

        // Validation du format de la licence
        if (!preg_match('/^[0-9]{6}$/', $data['licence'])) {
            deliver_response(400, "Erreur : Le numéro de licence doit contenir exactement 6 chiffres", null);
            break;
        }

        // Vérification si le joueur existe déjà
        $recherche = new RechercheJoueur('licence', $data['licence']);
        $recherche = $recherche->executer();
        if(!empty($recherche)){
            deliver_response(400, "Erreur : Le joueur existe déjà, ou a le même numéro de licence", null);
            break;
        }

        // Création du joueur
        $joueur = new Joueur($data['nom'], $data['prenom'], $data['date_naissance'], $data['taille'], $data['poids'], $data['licence']);
        $creer = new CreerJoueur($joueur);
        $result = $creer->executer();
        deliver_response(201, "Joueur créé avec succès", $result);
        break;


    case 'PUT':
        $postedData = file_get_contents('php://input');
        $data = json_decode($postedData, true);

        // Vérification de la présence de la licence dans l'URL
        if (!isset($_GET['licence'])) {
            deliver_response(400, "Erreur : Le paramètre 'licence' est requis dans l'URL", null);
            break;
        }

        // Vérifier si le joueur existe
        $recherche = new RechercheJoueur('licence', $_GET['licence']);
        $joueurExistant = $recherche->executer();
        if (empty($joueurExistant)) {
            deliver_response(404, "Joueur non trouvé", null);
            break;
        }

        // Modification du statut
        if (isset($data['statut']) && !isset($data['nom']) && !isset($data['prenom']) && !isset($data['date_naissance']) && !isset($data['taille']) && !isset($data['poids'])) {
            $modifier = new ModifierStatutJoueur($_GET['licence'], $data['statut']);
            $result = $modifier->executer();
            deliver_response(200, "Statut du joueur modifié", $result);
            break;
        }

        // Modification des informations du joueur
        if (isset($data['nom']) && isset($data['prenom']) && isset($data['date_naissance']) && 
            isset($data['taille']) && isset($data['poids']) && !isset($data['statut'])) {
            $joueur = new Joueur($data['nom'], $data['prenom'], $data['date_naissance'], $data['taille'], $data['poids'],$_GET['licence']);
            $modifier = new ModifieJoueur($joueur);
            $result = $modifier->executer();
            deliver_response(200, "Joueur modifié avec succès", $result);
            break;
        }
        else{
            deliver_response(400, "Erreur : Aucun paramètre à modifier", null);
            break;
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
        else{
            deliver_response(400, "Erreur : Le paramètre 'licence' est requis dans l'URL", null);
            break;
        }
        break;

    default:
        deliver_response(405, "Méthode non autorisée", null);
        break;
}
?>