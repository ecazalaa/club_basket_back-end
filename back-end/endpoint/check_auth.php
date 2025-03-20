<?php
require_once 'jwt_utils.php';

function check_auth() {
    // Si c'est une requête GET, on autorise sans auth
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return true;
    }

    // Récupération du token
    $token = get_bearer_token();

    if (!$token) {
        http_response_code(401);
        deliver_response(401, "Unauthorized", ["error" => "Token manquant"]);
        exit();
    }

    // Validation du token avec votre secret
    $secret = "secret"; // Utilisez la même clé que dans authapi.php
    if (!is_jwt_valid($token, $secret)) {
        http_response_code(401);
        deliver_response(401, "Unauthorized", ["error" => "Token invalide ou expiré"]);
        exit();
    }

    // Décoder le payload pour récupérer les informations utilisateur
    $tokenParts = explode('.', $token);
    $payload = json_decode(base64_decode($tokenParts[1]), true);

    // Vérification des permissions selon le rôle
    $role = $payload['role'];
    $method = $_SERVER['REQUEST_METHOD'];

    if (!check_permissions($role, $method)) {
        http_response_code(403);
        deliver_response(403, "Forbidden", ["error" => "Permission refusée"]);
        exit();
    }

    return $payload;
}

function check_permissions($role, $method) {
    switch ($role) {
        case 'admin':
            return true; // L'admin peut tout faire
        case 'user':
            // Le user peut créer (POST) et mettre à jour ses propres phrases (PUT)
            return in_array($method, ['POST', 'PUT']);
        default:
            return false;
    }
}
?>