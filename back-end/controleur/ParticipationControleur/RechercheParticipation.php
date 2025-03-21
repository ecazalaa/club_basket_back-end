<?php

require_once '../config/config.php';
require_once '../modele/ParticiperDAO.php';

class RechercheParticipation{
    private $participerDAO;
    private $critere;
    private $motcle;

    public function __construct($critere, $motcle)
    {
        $pdo = connectionBD();
        $this->participerDAO = new ParticiperDAO($pdo);
        $this->critere = $critere;
        $this->motcle = $motcle;
    }
    public function executer(){
        return $this->participerDAO->select($this->critere, $this->motcle);
    }

}