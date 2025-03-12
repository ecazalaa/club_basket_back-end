<?php
require_once '../config/config.php';
require_once '../modele/UtilisateurDAO.php';
class RechercheUtilisateur{
    private $utilisateurDAO;
    private $nom;
    private $prenom;
    private $mdp;
    public function __construct( $nom, $prenom, $mdp)
    {
        $pdo = connectionBD();
        $this->utilisateurDAO = new UtilisateurDAO($pdo);
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->mdp = $mdp;
    }

    public function executer(){
        return $this->utilisateurDAO->select($this->nom, $this->prenom, $this->mdp);
    }
}