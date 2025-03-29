<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

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