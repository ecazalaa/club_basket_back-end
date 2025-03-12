<?php
require_once '../config/config.php';
require_once '../modele/JoueurDAO.php';
class CreerJoueur
{

    private $joueurDAO;
    private $joueur;

    // Constructeur : Initialise la connexion PDO et le joueur à ajouter
    public function __construct( Joueur $joueur)
    {
        $pdo = connectionBD();
        $this->joueurDAO = new JoueurDAO($pdo);
        $this->joueur = $joueur;
    }
    public function executer(){
        return $this->joueurDAO->insert($this->joueur);
    }

}


