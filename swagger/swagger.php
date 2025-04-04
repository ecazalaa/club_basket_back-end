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



