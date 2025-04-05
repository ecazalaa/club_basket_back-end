<?php
use OpenApi\Attributes as OA;
/**
 * @OA\Info(
 *     version="0.1",
 *     title="Club Basket Labège API",
 *     description="API du Club de Basket de Labège",
 *     @OA\Contact(
 *         email="contact@clubbasketlabege.fr"
 *     )
 * )
 */

/**
 * @OA\Server(
 *     description="Serveur local",
 *     url="http://localhost/club_basket_back-end/back-end"
 * )
 */

 /**
 * @OA\Server(
 *     description="Serveur d'authentification",
 *     url="http://localhost/club_basket_apiAuth"
 * )
 */

 /**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

 /**
 * @OA\Schema(
 *     schema="LoginCredentials",
 *     required={"Nom", "Prenom", "Mot_de_passe"},
 *     @OA\Property(property="Nom", type="string", example="Smith"),
 *     @OA\Property(property="Prenom", type="string", example="Jane"),
 *     @OA\Property(property="Mot_de_passe", type="string", example="mypassword")
 * )
 */

/**
 * @OA\Schema(
 *     schema="LoginResponse",
 *     @OA\Property(property="status_code", type="integer", example=200),
 *     @OA\Property(property="status_message", type="string", example="OK"),
 *     @OA\Property(property="status", type="string", example="success"),
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(
 *             property="jwt",
 *             type="string",
 *             example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
 *         )
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         example="Bienvenue a l'API d'authentification de Romeo et Emile"
 *     )
 * )
 */

 /**
 * @OA\Post(
 *     path="/authapi.php",
 *     summary="Authentification utilisateur",
 *     description="Permet d'obtenir un token JWT pour accéder aux autres endpoints",
 *     tags={"Authentification"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Identifiants de connexion",
 *         @OA\JsonContent(ref="#/components/schemas/LoginCredentials")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Authentification réussie",
 *         @OA\JsonContent(ref="#/components/schemas/LoginResponse")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Authentification échouée",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Identifiants invalides")
 *         )
 *     )
 * )
 */

 /**
 * @OA\PathItem(
 *     path="/endpoint/JoueurEndpoint.php"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Joueur",
 *     required={"nom", "prenom", "date_naissance", "taille", "poids", "licence"},
 *     @OA\Property(property="nom", type="string", example="Dupont"),
 *     @OA\Property(property="prenom", type="string", example="Jean"),
 *     @OA\Property(property="date_naissance", type="string", format="date", example="2000-01-01"),
 *     @OA\Property(property="taille", type="integer", example=180),
 *     @OA\Property(property="poids", type="integer", example=75),
 *     @OA\Property(property="licence", type="string", example="123456")
 * )
 */



