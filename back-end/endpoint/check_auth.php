<?php
require_once 'jwt_utils.php';

function check_auth() {
    // Récupération du token
    $token = get_bearer_token();
    
    
    if (!$token) {
        http_response_code(401);
        deliver_response(401, "Unauthorized", ["error" => "Token manquant".$token]);
        exit();
    }

    // Validation du token avec votre secret
    $secret = "secret"; // Utilisez la même clé que dans authapi.php
    if (!is_jwt_valid($token, $secret)) {
        http_response_code(401);
        deliver_response(401, "Unauthorized", ["error" => "Token invalide ou expiré"]);
        exit();
    }

    return true;
}

?>