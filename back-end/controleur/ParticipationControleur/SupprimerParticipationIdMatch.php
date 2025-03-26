<?php

require_once '../config/config.php';
require_once '../modele/ParticiperDAO.php';

class SupprimerParticipationIdMatch{
    private $matchDAO;
    private $idMatch;

    public function __construct($idMatch)
    {
        $pdo = connectionBD();
        $this->participationDAO = new ParticiperDAO($pdo);
        $this->idMatch = $idMatch;
    }
    public function executer(){
        return $this->participationDAO->deleteByIdMatch($this->idMatch);
    }

}
