<?php

require_once '../config/config.php';
require_once '../modele/JoueurDAO.php';

class ModifierStatutJoueur{
    private $joueurDAO;
    private $licence;
    private $statut;

    public function __construct($licence, $statut)
    {
        $pdo = connectionBD();
        $this->joueurDAO = new JoueurDAO($pdo);
        $this->licence = $licence;
        $this->statut = $statut;
    }

    public function executer(){
        return $this->joueurDAO->udpateSatut($this->licence, $this->statut);
    }
}