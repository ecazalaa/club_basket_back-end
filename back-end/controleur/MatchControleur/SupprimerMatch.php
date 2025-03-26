<?php

require_once '../config/config.php';
require_once '../modele/MatchDAO.php';

class SupprimerMatch{
    private $matchDAO;


    public function __construct()
    {
        $pdo = connectionBD();
        $this->matchDAO = new MatchDAO($pdo);
    }

    public function executer($idMatch){
        return $this->matchDAO->delete($idMatch);
    }
}