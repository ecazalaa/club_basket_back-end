<?php

require_once '../config/config.php';
require_once '../modele/JoueurDAO.php';

class ModifierResultatMatch{
    private $matchDAO;
    private $id;
    private $resultat;

    // Constructeur : Initialise la connexion PDO et le match à modifier
    public function __construct($id, $resultat)
    {
        $pdo = connectionBD();
        $this->matchDAO = new MatchDAO($pdo);
        $this->id = $id;
        $this->resultat = $resultat;
    }

    // Exécute la modification du résultat du match
    public function executer(){
        return $this->matchDAO->updateResultat($this->id, $this->resultat);
    }
}