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