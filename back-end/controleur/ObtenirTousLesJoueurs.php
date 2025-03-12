<?php

require_once '../config/config.php';
require_once '../modele/JoueurDAO.php';

class ObtenirTousLesJoueurs
{
    private $joueurDAO;

    // Constructeur : Initialise la connexion PDO
    public function __construct()
    {
        $pdo = connectionBD();
        $this->joueurDAO = new JoueurDAO($pdo);
    }

    // Retourne tous les joueurs
    public function executer()
    {
        return $this->joueurDAO->selectAll();
    }
}