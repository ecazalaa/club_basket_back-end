<?php

require_once '../config/config.php';
require_once '../modele/ParticiperDAO.php';

class RechercheParticipation2{
    private $participerDAO;
    private $licence;
    private $matchId;

    public function __construct($licence, $matchId)
    {
        $pdo = connectionBD();
        $this->participerDAO = new ParticiperDAO($pdo);
        $this->licence = $licence;
        $this->matchId = $matchId;
    }
    public function executer(){
        return $this->participerDAO->select2($this->licence, $this->matchId);
    }

}