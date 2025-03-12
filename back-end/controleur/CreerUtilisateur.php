<?php
require_once '../config/config.php';
require_once '../modele/UtilisateurDAO.php';
class CreerUtilisateur{

    private $utilisateurDAO;
    private $utilisateur;

    public function __construct( Utilisateur $utilisateur){
        $pdo = connectionBD();
        $this->utilisateurDAO = new UtilisateurDAO($pdo);
        $this->utilisateur = $utilisateur;
    }
    public function executer(){
        return $this->utilisateurDAO->insert($this->utilisateur);
    }
}